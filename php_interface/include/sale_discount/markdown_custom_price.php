<?php

use Bitrix\Main\Loader;

class MarkdownBasketCustomPrice
{
    protected const MARKDOWN_IBLOCK_ID = 37;

    public static function onBeforeBasketAdd(&$arFields)
    {
        if (!is_array($arFields)) {
            return true;
        }

        static::log('onBeforeBasketAdd.start', ['arFields' => $arFields]);
        static::applyForBasketFields($arFields, 0);
        static::log('onBeforeBasketAdd.done', ['arFields' => $arFields]);
        return true;
    }

    public static function onBeforeBasketUpdate($id, &$arFields)
    {
        if (!is_array($arFields)) {
            $arFields = [];
        }

        static::log('onBeforeBasketUpdate.start', ['basketId' => (int)$id, 'arFields' => $arFields]);
        static::applyForBasketFields($arFields, (int)$id);
        static::log('onBeforeBasketUpdate.done', ['basketId' => (int)$id, 'arFields' => $arFields]);
        return true;
    }

    public static function onSaleBasketItemBeforeSaved($basketItem, $isNew = false, $values = [])
    {
        if (!$basketItem instanceof \Bitrix\Sale\BasketItem) {
            static::log('onSaleBasketItemBeforeSaved.skip.notBasketItem', [
                'type' => is_object($basketItem) ? get_class($basketItem) : gettype($basketItem),
                'isNew' => $isNew,
                'values' => $values,
            ]);
            return;
        }

        Loader::includeModule('sale');
        Loader::includeModule('catalog');
        Loader::includeModule('iblock');

        $fields = $basketItem->getFieldValues();
        $fields['PROPS'] = [];
        $propertyCollection = $basketItem->getPropertyCollection();
        if ($propertyCollection) {
            $fields['PROPS'] = $propertyCollection->getPropertyValues();
        }

        static::log('onSaleBasketItemBeforeSaved.start', [
            'basketId' => (int)$basketItem->getId(),
            'productId' => (int)$basketItem->getProductId(),
            'fields' => $fields,
        ]);

        $productId = (int)$basketItem->getProductId();
        if ($productId <= 0) {
            static::log('onSaleBasketItemBeforeSaved.skip.noProductId', [
                'basketId' => (int)$basketItem->getId(),
            ]);
            return;
        }
        if (static::getProductIblockId($productId) !== static::MARKDOWN_IBLOCK_ID) {
            static::log('onSaleBasketItemBeforeSaved.skip.notMarkdownIblock', [
                'basketId' => (int)$basketItem->getId(),
                'productId' => $productId,
            ]);
            return;
        }

        static::applyForBasketFields($fields, (int)$basketItem->getId());

        if ((string)($fields['CUSTOM_PRICE'] ?? 'N') !== 'Y') {
            static::log('onSaleBasketItemBeforeSaved.skip.customPriceNotApplied', [
                'basketId' => (int)$basketItem->getId(),
                'productId' => $productId,
                'customPrice' => (string)($fields['CUSTOM_PRICE'] ?? 'N'),
            ]);
            return;
        }

        $basketItem->setField('PRICE', (float)($fields['PRICE'] ?? 0));
        $basketItem->setField('BASE_PRICE', (float)($fields['BASE_PRICE'] ?? 0));
        $basketItem->setField('DISCOUNT_PRICE', (float)($fields['DISCOUNT_PRICE'] ?? 0));
        $basketItem->setField('CURRENCY', (string)($fields['CURRENCY'] ?? 'KZT'));
        $basketItem->setField('CUSTOM_PRICE', (string)($fields['CUSTOM_PRICE'] ?? 'N'));
        if (array_key_exists('PRODUCT_PROVIDER_CLASS', $fields)) {
            $basketItem->setField('PRODUCT_PROVIDER_CLASS', (string)$fields['PRODUCT_PROVIDER_CLASS']);
        }

        static::log('onSaleBasketItemBeforeSaved.done', [
            'basketId' => (int)$basketItem->getId(),
            'productId' => (int)$basketItem->getProductId(),
            'finalPrice' => (float)$basketItem->getPrice(),
            'finalBasePrice' => (float)$basketItem->getBasePrice(),
            'finalDiscountPrice' => (float)$basketItem->getDiscountPrice(),
            'finalCustomPrice' => (string)$basketItem->getField('CUSTOM_PRICE'),
        ]);
    }

    protected static function applyForBasketFields(array &$arFields, int $basketId): void
    {
        Loader::includeModule('sale');
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        static::enrichByBasketId($arFields, $basketId);

        $productId = (int)($arFields['PRODUCT_ID'] ?? 0);
        if ($productId <= 0) {
            static::log('applyForBasketFields.skip.noProductId', ['basketId' => $basketId, 'arFields' => $arFields]);
            return;
        }

        $iblockId = static::getProductIblockId($productId);
        if ($iblockId !== static::MARKDOWN_IBLOCK_ID) {
            static::log('applyForBasketFields.skip.notMarkdownIblock', [
                'basketId' => $basketId,
                'productId' => $productId,
                'iblockId' => $iblockId,
            ]);
            return;
        }

        $storeId = static::extractStoreId($arFields);
        if ($storeId <= 0) {
            static::log('applyForBasketFields.skip.noStoreId', [
                'basketId' => $basketId,
                'productId' => $productId,
                'arFields' => $arFields,
            ]);
            return;
        }

        $storeDiscount = static::getStoreDiscount($storeId);
        if (empty($storeDiscount['PERCENT']) || (float)$storeDiscount['PERCENT'] <= 0) {
            static::log('applyForBasketFields.skip.noStoreDiscount', [
                'basketId' => $basketId,
                'productId' => $productId,
                'storeId' => $storeId,
            ]);
            return;
        }

        $basePrice = static::extractBasePrice($productId, $arFields);
        if ($basePrice <= 0) {
            static::log('applyForBasketFields.skip.noBasePrice', [
                'basketId' => $basketId,
                'productId' => $productId,
                'storeId' => $storeId,
                'arFields' => $arFields,
            ]);
            return;
        }

        $currency = (string)($arFields['CURRENCY'] ?? '');
        if ($currency === '') {
            $currency = static::getProductCurrency($productId);
        }
        if ($currency === '') {
            $currency = 'KZT';
        }

        $discountPrice = round($basePrice - ($basePrice * ((float)$storeDiscount['PERCENT'] / 100)), 2);
        if ($discountPrice < 0) {
            $discountPrice = 0.0;
        }

        $arFields['PRICE'] = $discountPrice;
        $arFields['BASE_PRICE'] = $basePrice;
        $arFields['DISCOUNT_PRICE'] = max(0, round($basePrice - $discountPrice, 2));
        $arFields['CURRENCY'] = $currency;
        $arFields['CUSTOM_PRICE'] = 'Y';
        $arFields['PRODUCT_PROVIDER_CLASS'] = '';

        static::log('applyForBasketFields.applied', [
            'basketId' => $basketId,
            'productId' => $productId,
            'storeId' => $storeId,
            'discountId' => (int)($storeDiscount['ID'] ?? 0),
            'discountPercent' => (float)($storeDiscount['PERCENT'] ?? 0),
            'basePrice' => $basePrice,
            'discountPrice' => $discountPrice,
            'currency' => $currency,
        ]);
    }

    protected static function enrichByBasketId(array &$arFields, int $basketId): void
    {
        if ($basketId <= 0) {
            return;
        }

        $res = CSaleBasket::GetList(
            [],
            ['ID' => $basketId],
            false,
            false,
            ['ID', 'PRODUCT_ID', 'PRICE', 'BASE_PRICE', 'CURRENCY']
        );
        if ($row = $res->Fetch()) {
            if (empty($arFields['PRODUCT_ID']) && !empty($row['PRODUCT_ID'])) {
                $arFields['PRODUCT_ID'] = (int)$row['PRODUCT_ID'];
            }
            if (empty($arFields['PRICE']) && isset($row['PRICE'])) {
                $arFields['PRICE'] = (float)$row['PRICE'];
            }
            if (empty($arFields['BASE_PRICE']) && isset($row['BASE_PRICE'])) {
                $arFields['BASE_PRICE'] = (float)$row['BASE_PRICE'];
            }
            if (empty($arFields['CURRENCY']) && !empty($row['CURRENCY'])) {
                $arFields['CURRENCY'] = (string)$row['CURRENCY'];
            }
        }

        if (!empty($arFields['PROPS']) && is_array($arFields['PROPS'])) {
            return;
        }

        $arFields['PROPS'] = [];
        $propsRes = CSaleBasket::GetPropsList(
            [],
            ['BASKET_ID' => $basketId],
            false,
            false,
            ['NAME', 'CODE', 'VALUE']
        );
        while ($prop = $propsRes->Fetch()) {
            $arFields['PROPS'][] = $prop;
        }
    }

    protected static function extractStoreId(array $fields): int
    {
        foreach (['MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'] as $fieldName) {
            if (!empty($fields[$fieldName]) && (int)$fields[$fieldName] > 0) {
                return (int)$fields[$fieldName];
            }
        }

        if (!empty($fields['PROPS']) && is_array($fields['PROPS'])) {
            foreach ($fields['PROPS'] as $prop) {
                if (!is_array($prop)) {
                    continue;
                }
                $code = (string)($prop['CODE'] ?? $prop['NAME'] ?? '');
                if (in_array($code, ['MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'], true)) {
                    $value = (int)($prop['VALUE'] ?? 0);
                    if ($value > 0) {
                        return $value;
                    }
                }
            }
        }

        return 0;
    }

    protected static function extractBasePrice(int $productId, array $fields): float
    {
        $basePrice = (float)($fields['BASE_PRICE'] ?? 0);
        if ($basePrice > 0) {
            return $basePrice;
        }

        $price = (float)($fields['PRICE'] ?? 0);
        if ($price > 0) {
            return $price;
        }

        global $USER;
        $groups = is_object($USER) ? $USER->GetUserGroupArray() : [];
        $optimal = CCatalogProduct::GetOptimalPrice($productId, 1, $groups, 'N', [], SITE_ID);
        if (!empty($optimal['RESULT_PRICE']['BASE_PRICE'])) {
            return (float)$optimal['RESULT_PRICE']['BASE_PRICE'];
        }
        if (!empty($optimal['RESULT_PRICE']['DISCOUNT_PRICE'])) {
            return (float)$optimal['RESULT_PRICE']['DISCOUNT_PRICE'];
        }

        return 0.0;
    }

    protected static function getProductCurrency(int $productId): string
    {
        $price = \Bitrix\Catalog\PriceTable::getList([
            'select' => ['CURRENCY'],
            'filter' => ['=PRODUCT_ID' => $productId],
            'order' => ['CATALOG_GROUP_ID' => 'ASC'],
            'limit' => 1,
        ])->fetch();

        return is_array($price) && !empty($price['CURRENCY']) ? (string)$price['CURRENCY'] : '';
    }

    protected static function getStoreDiscount(int $storeId): array
    {
        static $discountRows = null;

        if ($discountRows === null) {
            $discountRows = [];
            $res = CSaleDiscount::GetList(
                ['PRIORITY' => 'DESC', 'SORT' => 'ASC', 'ID' => 'ASC'],
                ['LID' => SITE_ID, 'ACTIVE' => 'Y'],
                false,
                false,
                ['ID', 'CONDITIONS_LIST', 'ACTIONS_LIST', 'CONDITIONS', 'ACTIONS', 'ACTIVE_FROM', 'ACTIVE_TO']
            );
            while ($row = $res->Fetch()) {
                $discountRows[] = $row;
            }
            static::log('getStoreDiscount.source', [
                'source' => 'CSaleDiscount',
                'loaded_count' => count($discountRows),
            ]);
        }

        $nowTs = (new \Bitrix\Main\Type\DateTime())->getTimestamp();
        $debugRows = [];
        foreach ($discountRows as $row) {
            $debug = [
                'id' => (int)$row['ID'],
                'active_from' => (string)($row['ACTIVE_FROM'] ?? ''),
                'active_to' => (string)($row['ACTIVE_TO'] ?? ''),
                'skip_reason' => '',
                'has_store_condition' => false,
                'percent' => 0.0,
            ];

            if (!empty($row['ACTIVE_FROM']) && $row['ACTIVE_FROM'] instanceof \Bitrix\Main\Type\DateTime) {
                if ($row['ACTIVE_FROM']->getTimestamp() > $nowTs) {
                    $debug['skip_reason'] = 'not_started';
                    $debugRows[] = $debug;
                    continue;
                }
            }
            if (!empty($row['ACTIVE_TO']) && $row['ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                if ($row['ACTIVE_TO']->getTimestamp() < $nowTs) {
                    $debug['skip_reason'] = 'expired';
                    $debugRows[] = $debug;
                    continue;
                }
            }

            $conditionsRaw = $row['CONDITIONS_LIST'] ?? ($row['CONDITIONS'] ?? []);
            $actionsRaw = $row['ACTIONS_LIST'] ?? ($row['ACTIONS'] ?? []);
            $conditions = static::decodeRuleTree($conditionsRaw);
            $actions = static::decodeRuleTree($actionsRaw);
            $debug['conditions_shape'] = static::detectShape($conditions);
            $debug['actions_shape'] = static::detectShape($actions);
            $debug['conditions_class_ids'] = static::collectClassIds($conditions);
            $debug['actions_class_ids'] = static::collectClassIds($actions);
            $debug['conditions_store_candidates'] = static::collectStoreCandidates($conditions);
            $debug['actions_store_candidates'] = static::collectStoreCandidates($actions);
            $debug['raw_lengths'] = [
                'conditions_list' => is_string($row['CONDITIONS_LIST'] ?? null) ? mb_strlen((string)$row['CONDITIONS_LIST']) : 0,
                'actions_list' => is_string($row['ACTIONS_LIST'] ?? null) ? mb_strlen((string)$row['ACTIONS_LIST']) : 0,
                'conditions' => is_string($row['CONDITIONS'] ?? null) ? mb_strlen((string)$row['CONDITIONS']) : 0,
                'actions' => is_string($row['ACTIONS'] ?? null) ? mb_strlen((string)$row['ACTIONS']) : 0,
            ];

            $hasStoreInConditions = static::hasStoreCondition($conditions, $storeId);
            $hasStoreInActions = static::hasStoreCondition($actions, $storeId);
            $hasStoreCondition = ($hasStoreInConditions || $hasStoreInActions);
            $debug['has_store_condition'] = $hasStoreCondition;
            $debug['has_store_in_conditions'] = $hasStoreInConditions;
            $debug['has_store_in_actions'] = $hasStoreInActions;
            if (!$hasStoreCondition) {
                $debug['skip_reason'] = 'store_not_matched';
                $debugRows[] = $debug;
                continue;
            }

            $percent = static::extractPercent($actions);
            $debug['percent'] = $percent;
            if ($percent <= 0) {
                $debug['skip_reason'] = 'no_percent_action';
                $debugRows[] = $debug;
                continue;
            }

            static::log('getStoreDiscount.matched', [
                'storeId' => $storeId,
                'discount' => $debug,
            ]);
            return [
                'ID' => (int)$row['ID'],
                'PERCENT' => $percent,
            ];
        }

        static::log('getStoreDiscount.no_match', [
            'storeId' => $storeId,
            'checked_count' => count($discountRows),
            'rows' => array_slice($debugRows, 0, 25),
        ]);
        return [];
    }

    protected static function decodeRuleTree($raw): array
    {
        if (is_array($raw)) {
            return static::normalizeTree($raw);
        }

        if (is_object($raw)) {
            return static::normalizeTree((array)$raw);
        }

        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = @unserialize($raw);
        if (is_array($decoded)) {
            return static::normalizeTree($decoded);
        }
        if (is_object($decoded)) {
            return static::normalizeTree((array)$decoded);
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? static::normalizeTree($decoded) : [];
    }

    protected static function normalizeTree($node)
    {
        if (is_object($node)) {
            $node = (array)$node;
        }
        if (!is_array($node)) {
            return $node;
        }

        $normalized = [];
        foreach ($node as $key => $value) {
            $normalizedKey = $key;
            if (is_string($normalizedKey) && mb_strpos($normalizedKey, "\0") !== false) {
                $parts = explode("\0", $normalizedKey);
                $normalizedKey = end($parts);
            }
            $normalized[$normalizedKey] = static::normalizeTree($value);
        }

        return $normalized;
    }

    protected static function hasStoreCondition($node, int $storeId): bool
    {
        if (!is_array($node)) {
            return false;
        }

        $classId = (string)($node['CLASS_ID'] ?? $node['ClassName'] ?? $node['class_id'] ?? '');
        if (
            $classId !== ''
            && (
                mb_strpos($classId, 'CondCtrlProductOnWarehouse') !== false
                || mb_strpos($classId, 'ProductOnWarehouse') !== false
            )
        ) {
            $storeIds = static::extractStoreIdsFromNode($node);
            static::log('hasStoreCondition.node', [
                'storeIdNeedle' => $storeId,
                'classId' => $classId,
                'storeIdsExtracted' => $storeIds,
                'node' => [
                    'CLASS_ID' => $node['CLASS_ID'] ?? null,
                    'ClassName' => $node['ClassName'] ?? null,
                    'class_id' => $node['class_id'] ?? null,
                    'DATA' => $node['DATA'] ?? null,
                    'CHILDREN' => $node['CHILDREN'] ?? null,
                    'children' => $node['children'] ?? null,
                ],
            ]);
            return in_array($storeId, $storeIds, true);
        }

        foreach ($node as $child) {
            if ((is_array($child) || is_object($child)) && static::hasStoreCondition($child, $storeId)) {
                return true;
            }
        }

        return false;
    }

    protected static function detectShape(array $node): array
    {
        $keys = array_keys($node);
        $sample = [];
        foreach ($keys as $k) {
            $sample[] = is_scalar($k) ? (string)$k : gettype($k);
            if (count($sample) >= 10) {
                break;
            }
        }

        return [
            'keys' => $sample,
            'class_id' => (string)($node['CLASS_ID'] ?? ''),
        ];
    }

    protected static function collectClassIds($node): array
    {
        $classIds = [];
        $walk = static function ($n) use (&$walk, &$classIds): void {
            if (is_object($n)) {
                $n = (array)$n;
            }
            if (!is_array($n)) {
                return;
            }

            $classId = (string)($n['CLASS_ID'] ?? $n['ClassName'] ?? $n['class_id'] ?? '');
            if ($classId !== '') {
                $classIds[$classId] = $classId;
            }
            foreach ($n as $child) {
                $walk($child);
            }
        };
        $walk($node);

        return array_values($classIds);
    }

    protected static function collectStoreCandidates($node): array
    {
        $candidates = [];
        $walk = static function ($n) use (&$walk, &$candidates): void {
            if (is_object($n)) {
                $n = (array)$n;
            }
            if (!is_array($n)) {
                return;
            }
            if (array_key_exists('STORE_ID', $n)) {
                $candidates[] = ['STORE_ID' => $n['STORE_ID']];
            }
            if (array_key_exists('DATA', $n) && is_array($n['DATA'])) {
                $data = $n['DATA'];
                foreach (['value', 'Value', 'VALUE'] as $k) {
                    if (array_key_exists($k, $data)) {
                        $candidates[] = ['DATA.' . $k => $data[$k]];
                    }
                }
            }
            foreach ($n as $child) {
                $walk($child);
            }
        };
        $walk($node);

        return array_slice($candidates, 0, 20);
    }

    protected static function extractStoreIdsFromNode(array $node): array
    {
        $values = [];
        if (isset($node['STORE_ID'])) {
            $values[] = $node['STORE_ID'];
        }
        if (isset($node['DATA']) && is_array($node['DATA'])) {
            foreach (['STORE_ID', 'store_id', 'storeId', 'value', 'Value', 'VALUE'] as $k) {
                if (array_key_exists($k, $node['DATA'])) {
                    $values[] = $node['DATA'][$k];
                }
            }
        }
        if (isset($node['value'])) {
            $values[] = $node['value'];
        }

        $result = [];
        $collect = static function ($value) use (&$collect, &$result): void {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $collect($item);
                }
                return;
            }
            if (is_scalar($value)) {
                preg_match_all('/\d+/', (string)$value, $m);
                foreach (($m[0] ?? []) as $id) {
                    $id = (int)$id;
                    if ($id > 0) {
                        $result[$id] = $id;
                    }
                }
            }
        };
        foreach ($values as $value) {
            $collect($value);
        }

        return array_values($result);
    }

    protected static function extractPercent($node): float
    {
        if (!is_array($node)) {
            return 0.0;
        }

        $classId = (string)($node['CLASS_ID'] ?? $node['ClassName'] ?? $node['class_id'] ?? '');
        if ($classId !== '' && mb_strpos($classId, 'ActSaleBsktGrp') !== false) {
            $value = 0.0;
            foreach (['VALUE', 'value'] as $valueKey) {
                if (isset($node[$valueKey]) && (float)$node[$valueKey] > 0) {
                    $value = (float)$node[$valueKey];
                    break;
                }
            }
            if (
                $value <= 0
                && isset($node['DATA'])
                && is_array($node['DATA'])
            ) {
                foreach (['VALUE', 'Value', 'value'] as $valueKey) {
                    if (isset($node['DATA'][$valueKey]) && (float)$node['DATA'][$valueKey] > 0) {
                        $value = (float)$node['DATA'][$valueKey];
                        break;
                    }
                }
            }

            $unit = '';
            foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                if (!empty($node[$unitKey])) {
                    $unit = mb_strtolower((string)$node[$unitKey]);
                    break;
                }
            }
            if (
                $unit === ''
                && isset($node['DATA'])
                && is_array($node['DATA'])
            ) {
                foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                    if (!empty($node['DATA'][$unitKey])) {
                        $unit = mb_strtolower((string)$node['DATA'][$unitKey]);
                        break;
                    }
                }
            }

            if ($value > 0 && ($unit === 'perc' || $unit === 'percent' || $unit === '')) {
                return $value;
            }
        }

        foreach ($node as $child) {
            if (!is_array($child)) {
                continue;
            }
            $found = static::extractPercent($child);
            if ($found > 0) {
                return $found;
            }
        }

        return 0.0;
    }

    protected static function getProductIblockId(int $productId): int
    {
        $product = \CCatalogSku::GetProductInfo($productId);
        if (!empty($product['ID'])) {
            $res = \CIBlockElement::GetList([], ['ID' => (int)$product['ID']], false, false, ['IBLOCK_ID']);
        } else {
            $res = \CIBlockElement::GetList([], ['ID' => $productId], false, false, ['IBLOCK_ID']);
        }

        $row = $res ? $res->Fetch() : false;
        return is_array($row) && !empty($row['IBLOCK_ID']) ? (int)$row['IBLOCK_ID'] : 0;
    }

    protected static function log(string $tag, array $data = []): void
    {
        $line = print_r([
            'MARKDOWN_BASKET_DEBUG' => $tag,
            'TS' => date('Y-m-d H:i:s'),
            'DATA' => $data,
        ], true);

        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? (string)$_SERVER['DOCUMENT_ROOT'] : '';
        if ($docRoot !== '') {
            $targets = [
                rtrim($docRoot, '/\\') . '/local/php_interface/markdown_debug.log',
                rtrim($docRoot, '/\\') . '/upload/markdown_debug.log',
            ];
            foreach ($targets as $filePath) {
                @file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND);
            }
        } else {
            @file_put_contents(sys_get_temp_dir() . '/markdown_debug.log', $line . PHP_EOL, FILE_APPEND);
        }

        if (function_exists('debugFile')) {
            debugFile($line);
        }
    }

    public static function ping(): void
    {
        static::log('bootstrap.ping', ['uri' => (string)($_SERVER['REQUEST_URI'] ?? '')]);
    }
}
