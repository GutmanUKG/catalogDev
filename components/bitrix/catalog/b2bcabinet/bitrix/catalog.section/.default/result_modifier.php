<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application,
    Bitrix\Main\Loader;
use Bitrix\Catalog\StoreProductTable;
/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */





$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arTableHeader = [
    'NAME' => Loc::getMessage('HEAD_NAME')
];

$arProductParams = [];
$skuProperties = [];
$productProperties = [];
$priceProperties = [];

$arBasketItems = $arParams['BASKET_STATE'];

// Collect all display props include SKU
foreach ($arResult['ITEMS'] as &$product) {
    if (is_array($product['DISPLAY_PROPERTIES'])) {
        foreach ($product['DISPLAY_PROPERTIES'] as $propertyCode => $property) {
            if ($propertyCode !== 'CML2_MANUFACTURER') {
                $productProperties[$propertyCode] = $property['NAME'];
            }
        }
    }

    if ($product['PRODUCT']['TYPE'] === 3) {
        foreach ($product['OFFERS'] as &$offer) {
            if (is_array($offer['DISPLAY_PROPERTIES'])) {
                foreach ($offer['DISPLAY_PROPERTIES'] as $propertyCode => $property) {
                    $skuProperties[$propertyCode] = $property['NAME'];
                }
            }
            $offer['ACTUAL_QUANTITY'] = !empty($arBasketItems[$offer['ID']]) ? $arBasketItems[$offer['ID']] : 0;

            if ($arParams['OFFERS_VIEW'] !== 'LIST') {
                $arTableHeader['OFFERS'] = Loc::getMessage('HEAD_OFFERS');
            }

            foreach ($offer['ITEM_QUANTITY_RANGES'] as $rangeName => $rangeProps ) {
                if ( $offer['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM']
                    && $offer['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
                ) {
                    $offer['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
                }
            }
        }
    } else {
        $product['ACTUAL_QUANTITY'] = !empty($arBasketItems[$product['ID']]) ? $arBasketItems[$product['ID']] : 0;

        foreach ($product['ITEM_QUANTITY_RANGES'] ?: [] as $rangeName => $rangeProps ) {
            if ( $product['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM']
                && $product['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
            ) {
                $product['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
            }
        }
    }
}






//���������� � ��������� �������� �������� ������
//global $USER;
//if($USER->isAdmin()){
$SALE_IBLOCK_ID = 37;

$ARTICLES = [];
foreach ($arResult['ITEMS'] as $ITEM) {
    $ARTICLES[] = $ITEM['PROPERTIES']['CML2_ARTICLE']['VALUE'];
}

$SALE_ITEMS = [];
$res = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => $SALE_IBLOCK_ID,
        'PROPERTY_CML2_ARTICLE' => $ARTICLES,
        'ACTIVE' => 'Y'
    ],
    false,
    false,
    [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'PROPERTY_CML2_ARTICLE'
    ]
);

while($row = $res -> Fetch()){

    $SALE_ITEMS[$row['PROPERTY_CML2_ARTICLE_VALUE']] = $row;
}

foreach ($arResult['ITEMS'] as &$ITEM) {
    $article = $ITEM['PROPERTIES']['CML2_ARTICLE']['VALUE'];

    if(isset($SALE_ITEMS[$article])){

        $ITEM['HAS_SECOND'] = true;
        $ITEM['SECOND_ITEM'] = $SALE_ITEMS[$article];
    }else{
        $ITEM['HAS_SECOND'] = false;
    }
    if( $ITEM['HAS_SECOND']) {

    }
}
unset($ITEM);


// ============================================================
// ORPHAN MARKDOWN ITEMS: IB 37 items without matching IB 7 article
// ============================================================
$_MARKDOWN_IB_ID = 37;
$_MAIN_IB_ID = (int)$arParams['IBLOCK_ID'];
$_currentSectionId = (int)($arResult['ORIGINAL_PARAMETERS']['SECTION_ID'] ?? 0);

if ($_currentSectionId > 0 && Loader::includeModule('iblock') && Loader::includeModule('catalog')) {
    // Find the matching section in IB 37 by CODE
    $_mainSection = CIBlockSection::GetByID($_currentSectionId)->Fetch();
    $_sectionCode = $_mainSection ? $_mainSection['CODE'] : '';

    $_markdownSectionId = 0;
    if ($_sectionCode !== '') {
        $_mdSectionRes = CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => $_MARKDOWN_IB_ID, 'CODE' => $_sectionCode, 'ACTIVE' => 'Y'],
            false,
            false,
            ['ID']
        );
        if ($_mdSection = $_mdSectionRes->Fetch()) {
            $_markdownSectionId = (int)$_mdSection['ID'];
        }
    }

    if ($_markdownSectionId > 0) {
        // Step 1: Get all IB 37 items in this section (with subsections)
        $_mdItemsByArticle = [];
        $_mdRes = CIBlockElement::GetList(
            ['NAME' => 'ASC'],
            [
                'IBLOCK_ID' => $_MARKDOWN_IB_ID,
                'SECTION_ID' => $_markdownSectionId,
                'INCLUDE_SUBSECTIONS' => 'Y',
                'ACTIVE' => 'Y',
                '!PROPERTY_CML2_ARTICLE' => false,
            ],
            false,
            false,
            ['ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'PROPERTY_CML2_ARTICLE']
        );
        while ($_mdRow = $_mdRes->GetNext()) {
            $_art = trim($_mdRow['PROPERTY_CML2_ARTICLE_VALUE'] ?? '');
            if ($_art !== '' && !isset($_mdItemsByArticle[$_art])) {
                $_mdItemsByArticle[$_art] = $_mdRow;
            }
        }

        if (!empty($_mdItemsByArticle)) {
            // Step 2: Check which articles exist in IB 7
            $_matchedArticles = [];
            $_matchRes = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $_MAIN_IB_ID,
                    'ACTIVE' => 'Y',
                    'PROPERTY_CML2_ARTICLE' => array_keys($_mdItemsByArticle),
                ],
                false,
                false,
                ['ID', 'PROPERTY_CML2_ARTICLE']
            );
            while ($_mRow = $_matchRes->Fetch()) {
                $_mArt = trim($_mRow['PROPERTY_CML2_ARTICLE_VALUE'] ?? '');
                if ($_mArt !== '') {
                    $_matchedArticles[$_mArt] = true;
                }
            }

            // Step 3: Orphans = IB 37 articles NOT in IB 7
            $_orphanItems = array_diff_key($_mdItemsByArticle, $_matchedArticles);

            if (!empty($_orphanItems)) {
                $_orphanIds = array_map(function ($el) { return (int)$el['ID']; }, $_orphanItems);

                // Fetch markdown store amounts (used for discount selection)
                $_markdownStoreIds = [56, 55, 58, 57];
                $_orphanStoreAmounts = [];
                if (!empty($_orphanIds)) {
                    $_storeRes = CCatalogStoreProduct::GetList(
                        [],
                        ['PRODUCT_ID' => $_orphanIds, 'STORE_ID' => $_markdownStoreIds],
                        false,
                        false,
                        ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']
                    );
                    while ($_storeRow = $_storeRes->Fetch()) {
                        $_amount = (float)$_storeRow['AMOUNT'];
                        if ($_amount <= 0) {
                            continue;
                        }
                        $_pId = (int)$_storeRow['PRODUCT_ID'];
                        $_sId = (int)$_storeRow['STORE_ID'];
                        $_orphanStoreAmounts[$_pId][$_sId] = $_amount;
                    }
                }

                // Prepare store discount lookup (markdown discounts are store-based)
                $_storeDiscounts = [];
                if (!empty($_markdownStoreIds) && Loader::includeModule('sale')) {
                    $decodeDiscountRuleTree = static function ($rawValue): array {
                        if (is_array($rawValue)) {
                            return $rawValue;
                        }

                        if (!is_string($rawValue) || $rawValue === '') {
                            return [];
                        }

                        $decoded = @unserialize($rawValue);
                        if (is_array($decoded)) {
                            return $decoded;
                        }

                        $decoded = json_decode($rawValue, true);
                        if (is_array($decoded)) {
                            return $decoded;
                        }

                        return [];
                    };

                    $normalizeDiscountTree = static function ($node) use (&$normalizeDiscountTree) {
                        if (is_object($node)) {
                            $node = (array)$node;
                        }

                        if (!is_array($node)) {
                            return $node;
                        }

                        $normalized = [];
                        foreach ($node as $key => $value) {
                            $normalizedKey = $key;
                            if (is_string($normalizedKey)) {
                                if (mb_strpos($normalizedKey, "\0") !== false) {
                                    $parts = explode("\0", $normalizedKey);
                                    $normalizedKey = end($parts);
                                }
                                $normalizedKey = trim($normalizedKey);
                            }

                            $normalized[$normalizedKey] = $normalizeDiscountTree($value);
                        }

                        return $normalized;
                    };

                    $extractStoreIdsFromConditionNode = static function ($conditionNode): array {
                        if (!is_array($conditionNode)) {
                            return [];
                        }

                        $rawValues = [];
                        if (isset($conditionNode['STORE_ID'])) {
                            $rawValues[] = $conditionNode['STORE_ID'];
                        }
                        if (isset($conditionNode['STORE'])) {
                            $rawValues[] = $conditionNode['STORE'];
                        }

                        if (isset($conditionNode['DATA']) && is_array($conditionNode['DATA'])) {
                            if (array_key_exists('STORE_ID', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['STORE_ID'];
                            }
                            if (array_key_exists('store_id', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['store_id'];
                            }
                            if (array_key_exists('storeId', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['storeId'];
                            }
                            if (array_key_exists('value', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['value'];
                            }
                            if (array_key_exists('Value', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['Value'];
                            }
                            if (array_key_exists('VALUE', $conditionNode['DATA'])) {
                                $rawValues[] = $conditionNode['DATA']['VALUE'];
                            }
                        }

                        $storeIds = [];
                        $collect = static function ($value) use (&$collect, &$storeIds): void {
                            if (is_array($value)) {
                                foreach ($value as $innerValue) {
                                    $collect($innerValue);
                                }
                                return;
                            }

                            if (is_scalar($value)) {
                                preg_match_all('/\d+/', (string)$value, $matches);
                                if (!empty($matches[0])) {
                                    foreach ($matches[0] as $id) {
                                        $intId = (int)$id;
                                        if ($intId > 0) {
                                            $storeIds[$intId] = $intId;
                                        }
                                    }
                                }
                            }
                        };

                        foreach ($rawValues as $rawValue) {
                            $collect($rawValue);
                        }

                        return array_values($storeIds);
                    };

                    $hasStoreCondition = static function ($conditionNode, int $storeId) use (&$hasStoreCondition, $extractStoreIdsFromConditionNode): bool {
                        if (!is_array($conditionNode)) {
                            return false;
                        }
                        $classId = (string)($conditionNode['CLASS_ID'] ?? $conditionNode['ClassName'] ?? $conditionNode['class_id'] ?? '');

                        if (
                            $classId !== ''
                            && mb_strpos($classId, 'CondCtrlProductOnWarehouse') !== false
                        ) {
                            $nodeStoreIds = $extractStoreIdsFromConditionNode($conditionNode);
                            return in_array($storeId, $nodeStoreIds, true);
                        }

                        foreach ($conditionNode as $childNode) {
                            if (is_array($childNode) && $hasStoreCondition($childNode, $storeId)) {
                                return true;
                            }
                            if (is_object($childNode) && $hasStoreCondition((array)$childNode, $storeId)) {
                                return true;
                            }
                            if (is_array($childNode)) {
                                foreach ($childNode as $deepNode) {
                                    if (is_array($deepNode) && $hasStoreCondition($deepNode, $storeId)) {
                                        return true;
                                    }
                                }
                            }
                        }

                        return false;
                    };

                    $hasAnyStoreCondition = static function ($conditionNode) use (&$hasAnyStoreCondition): bool {
                        if (!is_array($conditionNode)) {
                            return false;
                        }
                        $classId = (string)($conditionNode['CLASS_ID'] ?? $conditionNode['ClassName'] ?? $conditionNode['class_id'] ?? '');

                        if (
                            $classId !== ''
                            && mb_strpos($classId, 'CondCtrlProductOnWarehouse') !== false
                        ) {
                            return true;
                        }

                        foreach ($conditionNode as $childNode) {
                            if (is_array($childNode) && $hasAnyStoreCondition($childNode)) {
                                return true;
                            }
                            if (is_object($childNode) && $hasAnyStoreCondition((array)$childNode)) {
                                return true;
                            }
                            if (is_array($childNode)) {
                                foreach ($childNode as $deepNode) {
                                    if (is_array($deepNode) && $hasAnyStoreCondition($deepNode)) {
                                        return true;
                                    }
                                }
                            }
                        }

                        return false;
                    };

                    $extractPercentFromActions = static function (
                        $actionNode,
                        int $storeId,
                        bool $allowWithoutStoreCondition,
                        callable $hasStoreCondition,
                        callable $hasAnyStoreCondition
                    ) use (&$extractPercentFromActions): ?float {
                        if (!is_array($actionNode)) {
                            return null;
                        }

                        $classId = (string)($actionNode['CLASS_ID'] ?? $actionNode['ClassName'] ?? $actionNode['class_id'] ?? '');
                        if (
                            $classId !== ''
                            && mb_strpos($classId, 'ActSaleBsktGrp') !== false
                        ) {
                            $value = 0.0;
                            foreach (['VALUE', 'value'] as $valueKey) {
                                if (isset($actionNode[$valueKey]) && (float)$actionNode[$valueKey] > 0) {
                                    $value = (float)$actionNode[$valueKey];
                                    break;
                                }
                            }
                            if ($value <= 0 && isset($actionNode['DATA']) && is_array($actionNode['DATA'])) {
                                foreach (['VALUE', 'Value', 'value'] as $valueKey) {
                                    if (isset($actionNode['DATA'][$valueKey]) && (float)$actionNode['DATA'][$valueKey] > 0) {
                                        $value = (float)$actionNode['DATA'][$valueKey];
                                        break;
                                    }
                                }
                            }

                            $unit = '';
                            foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                                if (!empty($actionNode[$unitKey])) {
                                    $unit = (string)$actionNode[$unitKey];
                                    break;
                                }
                            }
                            if ($unit === '' && isset($actionNode['DATA']) && is_array($actionNode['DATA'])) {
                                foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                                    if (!empty($actionNode['DATA'][$unitKey])) {
                                        $unit = (string)$actionNode['DATA'][$unitKey];
                                        break;
                                    }
                                }
                            }
                            $hasStoreInAction = $hasAnyStoreCondition($actionNode);
                            $isStoreMatchedInAction = $hasStoreCondition($actionNode, $storeId);
                            if (
                                $value > 0
                                && (mb_strtolower($unit) === 'perc' || mb_strtolower($unit) === 'percent' || $unit === '')
                                && (
                                    ($hasStoreInAction && $isStoreMatchedInAction)
                                    || (!$hasStoreInAction && $allowWithoutStoreCondition)
                                )
                            ) {
                                return $value;
                            }
                        }

                        foreach ($actionNode as $childAction) {
                            if (is_array($childAction)) {
                                $foundPercent = $extractPercentFromActions(
                                    $childAction,
                                    $storeId,
                                    $allowWithoutStoreCondition,
                                    $hasStoreCondition,
                                    $hasAnyStoreCondition
                                );
                                if ($foundPercent !== null) {
                                    return $foundPercent;
                                }
                            }
                        }

                        return null;
                    };

                    $getStoreMarketingDiscount = static function (int $storeId) use (
                        $hasStoreCondition,
                        $hasAnyStoreCondition,
                        $extractPercentFromActions,
                        $normalizeDiscountTree,
                        $decodeDiscountRuleTree
                    ): array {
                        static $discountRows = null;

                        if ($discountRows === null) {
                            $discountRows = [];
                            $discountRes = CSaleDiscount::GetList(
                                ['PRIORITY' => 'DESC', 'SORT' => 'ASC', 'ID' => 'ASC'],
                                ['LID' => SITE_ID, 'ACTIVE' => 'Y'],
                                false,
                                false,
                                ['ID', 'CONDITIONS_LIST', 'ACTIONS_LIST', 'CONDITIONS', 'ACTIONS', 'PRIORITY', 'SORT', 'ACTIVE_FROM', 'ACTIVE_TO']
                            );

                            while ($discountRow = $discountRes->Fetch()) {
                                $discountRows[] = $discountRow;
                            }
                        }

                        $now = new \Bitrix\Main\Type\DateTime();

                        foreach ($discountRows as $discountRow) {
                            if (!empty($discountRow['ACTIVE_FROM']) && $discountRow['ACTIVE_FROM'] instanceof \Bitrix\Main\Type\DateTime) {
                                if ($discountRow['ACTIVE_FROM']->getTimestamp() > $now->getTimestamp()) {
                                    continue;
                                }
                            }
                            if (!empty($discountRow['ACTIVE_TO']) && $discountRow['ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                                if ($discountRow['ACTIVE_TO']->getTimestamp() < $now->getTimestamp()) {
                                    continue;
                                }
                            }

                            $conditionsRaw = $discountRow['CONDITIONS_LIST'] ?? ($discountRow['CONDITIONS'] ?? []);
                            $actionsRaw = $discountRow['ACTIONS_LIST'] ?? ($discountRow['ACTIONS'] ?? []);
                            $conditions = $normalizeDiscountTree($decodeDiscountRuleTree($conditionsRaw));
                            $actions = $normalizeDiscountTree($decodeDiscountRuleTree($actionsRaw));

                            $hasStoreInConditions = $hasAnyStoreCondition($conditions);
                            $hasStoreInActions = $hasAnyStoreCondition($actions);
                            $isStoreMatchedInConditions = $hasStoreCondition($conditions, $storeId);
                            $isStoreMatchedInActions = $hasStoreCondition($actions, $storeId);

                            if (!$hasStoreInConditions && !$hasStoreInActions) {
                                continue;
                            }

                            if ($hasStoreInConditions && !$isStoreMatchedInConditions) {
                                continue;
                            }
                            if ($hasStoreInActions && !$isStoreMatchedInActions && !$isStoreMatchedInConditions) {
                                continue;
                            }

                            $allowWithoutStoreCondition = $isStoreMatchedInConditions || $isStoreMatchedInActions;
                            $percent = $extractPercentFromActions(
                                $actions,
                                $storeId,
                                $allowWithoutStoreCondition,
                                $hasStoreCondition,
                                $hasAnyStoreCondition
                            );
                            if ($percent !== null && $percent > 0) {
                                return [
                                    'ID' => (int)$discountRow['ID'],
                                    'PERCENT' => (float)$percent,
                                ];
                            }
                        }

                        return [];
                    };

                    foreach ($_markdownStoreIds as $_storeId) {
                        $_discount = $getStoreMarketingDiscount((int)$_storeId);
                        $_storeDiscounts[(int)$_storeId] = !empty($_discount)
                            ? (float)$_discount['PERCENT']
                            : 0.0;
                    }
                }

                // Fetch prices
                $_orphanPrices = [];
                $_priceRes = \Bitrix\Catalog\PriceTable::getList([
                    'select' => ['ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'QUANTITY_FROM', 'QUANTITY_TO'],
                    'filter' => ['=PRODUCT_ID' => $_orphanIds],
                    'order' => ['CATALOG_GROUP_ID' => 'ASC'],
                ]);
                while ($_pr = $_priceRes->fetch()) {
                    $_orphanPrices[(int)$_pr['PRODUCT_ID']][(int)$_pr['CATALOG_GROUP_ID']][] = $_pr;
                }

                // Fetch catalog product data
                $_orphanProdData = [];
                $_pdRes = \Bitrix\Catalog\ProductTable::getList([
                    'select' => ['ID', 'QUANTITY', 'TYPE', 'AVAILABLE', 'CAN_BUY_ZERO', 'QUANTITY_TRACE', 'MEASURE', 'WEIGHT'],
                    'filter' => ['=ID' => $_orphanIds],
                ]);
                while ($_pd = $_pdRes->fetch()) {
                    $_orphanProdData[(int)$_pd['ID']] = $_pd;
                }

                // Fetch measure ratios
                $_orphanMeasureRatios = [];
                $_mrRes = \Bitrix\Catalog\MeasureRatioTable::getList([
                    'select' => ['PRODUCT_ID', 'RATIO'],
                    'filter' => ['=PRODUCT_ID' => $_orphanIds, '=IS_DEFAULT' => 'Y'],
                ]);
                while ($_mr = $_mrRes->fetch()) {
                    $_orphanMeasureRatios[(int)$_mr['PRODUCT_ID']] = (float)$_mr['RATIO'];
                }

                // Fetch properties for each orphan
                $_orphanProps = [];
                foreach ($_orphanIds as $_oId) {
                    $_propRes = CIBlockElement::GetByID($_oId);
                    if ($_fullElem = $_propRes->GetNextElement()) {
                        $_orphanProps[$_oId] = $_fullElem->GetProperties();
                    }
                }

                // Build price columns from $arResult['PRICES']
                $_priceCols = [];
                $_priceCodesById = [];
                if (!empty($arResult['PRICES'])) {
                    foreach ($arResult['PRICES'] as $_pk => $_pInfo) {
                        $_priceCols[$_pInfo['ID']] = [
                            'ID' => $_pInfo['ID'],
                            'NAME' => !empty($_pInfo['TITLE']) ? $_pInfo['TITLE'] : $_pInfo['CODE'],
                        ];
                        $_priceCodesById[$_pInfo['ID']] = $_pInfo['CODE'] ?? (string)$_pInfo['ID'];
                    }
                }

                // Build section path for DETAIL_PAGE_URL
                $_sectionPath = '';
                $_navChain = CIBlockSection::GetNavChain(
                    $_MAIN_IB_ID,
                    $_currentSectionId,
                    ['CODE'],
                    true
                );
                $_pathParts = [];
                foreach ($_navChain as $_chainItem) {
                    if (!empty($_chainItem['CODE'])) {
                        $_pathParts[] = $_chainItem['CODE'];
                    }
                }
                $_sectionPath = implode('/', $_pathParts);

                // URL template (prefer component param; fallback to arResult if present)
                $_detailUrlTpl = $arParams['DETAIL_URL'] ?? '';
                if ($_detailUrlTpl === '') {
                    $_detailUrlTpl = ($arResult['FOLDER'] ?? '') . ($arResult['URL_TEMPLATES']['element'] ?? '');
                }

                // Basket quantities for orphan items
                $_orphanBasketQty = [];
                if (!empty($arBasketItems)) {
                    foreach ($_orphanIds as $_oId) {
                        if (isset($arBasketItems[$_oId])) {
                            $_orphanBasketQty[$_oId] = (int)$arBasketItems[$_oId];
                        }
                    }
                }

                foreach ($_orphanItems as $_article => $_elem) {
                    $_id = (int)$_elem['ID'];
                    $_pd = $_orphanProdData[$_id] ?? [];
                    $_ratio = $_orphanMeasureRatios[$_id] ?? 1;
                    $_props = $_orphanProps[$_id] ?? [];
                    $_markdownStores = $_orphanStoreAmounts[$_id] ?? [];
                    $_markdownStoreCount = count($_markdownStores);
                    $_storePercents = [];
                    if (!empty($_markdownStores)) {
                        foreach ($_markdownStores as $_storeId => $_amount) {
                            $_storePercents[(int)$_storeId] = (float)($_storeDiscounts[(int)$_storeId] ?? 0);
                        }
                    }
                    $_markdownDefaultStoreId = 0;
                    $_markdownDefaultPercent = null;
                    if (!empty($_storePercents)) {
                        foreach ($_storePercents as $_storeId => $_percent) {
                            if ($_markdownDefaultPercent === null || $_percent > $_markdownDefaultPercent) {
                                $_markdownDefaultPercent = $_percent;
                                $_markdownDefaultStoreId = (int)$_storeId;
                            }
                        }
                    }
                    if ($_markdownDefaultStoreId <= 0 && !empty($_markdownStores)) {
                        reset($_markdownStores);
                        $_markdownDefaultStoreId = (int)key($_markdownStores);
                    }
                    $_markdownDefaultRowKey = ($_markdownDefaultStoreId > 0)
                        ? ($_id . '_' . $_markdownDefaultStoreId)
                        : '';

                    // Build PRICE_MATRIX
                    $_matrix = [];
                    $_printPrices = [];
                    $_pricesByCode = [];
                    $_minBasePrice = null;
                    $_minDiscountPrice = null;
                    $_priceCurrency = '';
                    $_pricesForItem = $_orphanPrices[$_id] ?? [];
                    foreach ($_pricesForItem as $_pgId => $_priceRows) {
                        if (!isset($_priceCols[$_pgId])) continue;
                        $_priceCode = $_priceCodesById[$_pgId] ?? (string)$_pgId;
                        foreach ($_priceRows as $_pRow) {
                            $_rKey = ($_pRow['QUANTITY_FROM'] ?: 'ZERO') . '-' . ($_pRow['QUANTITY_TO'] ?: 'INF');
                            $_basePrice = round((float)$_pRow['PRICE']);
                            $_discountPrice = $_basePrice;
                            if (!empty($_storePercents)) {
                                $_bestPrice = null;
                                foreach ($_storePercents as $_percent) {
                                    $_candidate = $_basePrice;
                                    if ($_percent > 0) {
                                        $_candidate = round($_basePrice - ($_basePrice * ($_percent / 100)));
                                    }
                                    if ($_bestPrice === null || $_candidate < $_bestPrice) {
                                        $_bestPrice = $_candidate;
                                    }
                                }
                                if ($_bestPrice !== null) {
                                    $_discountPrice = $_bestPrice;
                                }
                            }
                            $_matrix[$_pgId][$_rKey] = [
                                'PRICE' => $_basePrice,
                                'DISCOUNT_PRICE' => $_discountPrice,
                                'CURRENCY' => $_pRow['CURRENCY'],
                                'BASE_PRICE' => $_basePrice,
                            ];
                            $_printPrices[$_priceCode][$_rKey] = [
                                'PRICE' => $_basePrice,
                                'DISCOUNT_PRICE' => $_discountPrice,
                                'CURRENCY' => $_pRow['CURRENCY'],
                                'PRINT' => CCurrencyLang::CurrencyFormat($_discountPrice, $_pRow['CURRENCY']),
                                'PRINT_OLD' => ($_discountPrice < $_basePrice)
                                    ? CCurrencyLang::CurrencyFormat($_basePrice, $_pRow['CURRENCY'])
                                    : '',
                            ];
                            if (!isset($_pricesByCode[$_priceCode])) {
                                $_discountDiff = $_basePrice - $_discountPrice;
                                $_discountPercent = $_basePrice > 0
                                    ? round(($_discountDiff / $_basePrice) * 100, 2)
                                    : 0;
                                $_pricesByCode[$_priceCode] = [
                                    'VALUE' => $_basePrice,
                                    'DISCOUNT_VALUE' => $_discountPrice,
                                    'CURRENCY' => $_pRow['CURRENCY'],
                                    'PRINT_VALUE' => CCurrencyLang::CurrencyFormat($_basePrice, $_pRow['CURRENCY']),
                                    'PRINT_DISCOUNT_VALUE' => CCurrencyLang::CurrencyFormat($_discountPrice, $_pRow['CURRENCY']),
                                    'DISCOUNT_DIFF' => $_discountDiff,
                                    'DISCOUNT_DIFF_PERCENT' => $_discountPercent,
                                ];
                            }
                            $_minBasePrice = $_minBasePrice === null ? $_basePrice : min($_minBasePrice, $_basePrice);
                            $_minDiscountPrice = $_minDiscountPrice === null ? $_discountPrice : min($_minDiscountPrice, $_discountPrice);
                            if ($_priceCurrency === '') {
                                $_priceCurrency = (string)$_pRow['CURRENCY'];
                            }
                        }
                    }

                    $_minPrice = [];
                    if ($_minBasePrice !== null) {
                        $currency = $_priceCurrency !== '' ? $_priceCurrency : \CCurrency::GetBaseCurrency();
                        $discountValue = $_minDiscountPrice ?? $_minBasePrice;
                        $_minPrice = [
                            'VALUE' => $_minBasePrice,
                            'DISCOUNT_VALUE' => $discountValue,
                            'CURRENCY' => $currency,
                            'PRINT_VALUE' => CCurrencyLang::CurrencyFormat($_minBasePrice, $currency),
                            'PRINT_DISCOUNT_VALUE' => CCurrencyLang::CurrencyFormat($discountValue, $currency),
                        ];
                    }

                    // Build DISPLAY_PROPERTIES
                    $_displayProps = [];
                    foreach ($_props as $_code => $_prop) {
                        if (!empty($_prop['VALUE'])) {
                            $_displayProps[$_code] = [
                                'NAME' => $_prop['NAME'],
                                'VALUE' => $_prop['VALUE'],
                                'DISPLAY_VALUE' => is_array($_prop['VALUE'])
                                    ? implode(', ', $_prop['VALUE'])
                                    : $_prop['VALUE'],
                                'PROPERTY_TYPE' => $_prop['PROPERTY_TYPE'] ?? 'S',
                            ];
                            if (!empty($_prop['LINK_IBLOCK_ID']) && !empty($_prop['VALUE'])) {
                                $_linkVals = [];
                                $_linkIdsList = is_array($_prop['VALUE']) ? $_prop['VALUE'] : [$_prop['VALUE']];
                                foreach ($_linkIdsList as $_linkId) {
                                    $_linkRes = CIBlockElement::GetByID($_linkId);
                                    if ($_linkElem = $_linkRes->Fetch()) {
                                        $_linkVals[$_linkId] = ['NAME' => $_linkElem['NAME']];
                                    }
                                }
                                if (!empty($_linkVals)) {
                                    $_displayProps[$_code]['LINK_ELEMENT_VALUE'] = $_linkVals;
                                }
                            }
                        }
                    }

                    // Build DETAIL_PAGE_URL
                    if ($_detailUrlTpl !== '') {
                        $_detailUrl = \CComponentEngine::MakePathFromTemplate(
                            $_detailUrlTpl,
                            [
                                'SECTION_CODE_PATH' => $_sectionPath,
                                'SECTION_CODE' => $_mainSection['CODE'] ?? '',
                                'SECTION_ID' => $_currentSectionId,
                                'ELEMENT_CODE' => $_elem['CODE'],
                                'ELEMENT_ID' => $_id,
                            ]
                        );
                    } else {
                        $_detailUrl = '';
                    }

                    // Default quantity ranges
                    $_quantityRanges = [
                        'ZERO-INF' => [
                            'HASH' => 'ZERO-INF',
                            'QUANTITY_FROM' => null,
                            'QUANTITY_TO' => null,
                            'SORT_FROM' => 0,
                            'SORT_TO' => PHP_INT_MAX,
                        ],
                    ];

                    $_actualQty = $_orphanBasketQty[$_id] ?? 0;

                    // Build SECOND_ITEMS for multi-store orphan markdown items
                    $_secondItems = [];
                    $_isMultiStoreMarkdown = false;
                    if ($_markdownStoreCount > 1) {
                        $_isMultiStoreMarkdown = true;
                        $_categoryMap = [56 => 'КАТ-1', 55 => 'КАТ-2', 58 => 'КАТ-3', 57 => 'КАТ-4'];
                        foreach ($_markdownStores as $_sId => $_sAmount) {
                            $_sPercent = $_storePercents[(int)$_sId] ?? 0;
                            $_rowKey = $_id . '_' . $_sId;
                            // Build per-store price from first available price type
                            $_selectedPrice = [];
                            foreach ($_pricesForItem as $_pgId => $_priceRows) {
                                if (!isset($_priceCols[$_pgId])) continue;
                                foreach ($_priceRows as $_pRow) {
                                    $_sBasePrice = round((float)$_pRow['PRICE']);
                                    $_sDiscountPrice = $_sBasePrice;
                                    if ($_sPercent > 0) {
                                        $_sDiscountPrice = round($_sBasePrice - ($_sBasePrice * ($_sPercent / 100)));
                                    }
                                    $_selectedPrice = [
                                        'PRICE' => $_sBasePrice,
                                        'DISCOUNT_PRICE' => $_sDiscountPrice,
                                        'CURRENCY' => $_pRow['CURRENCY'],
                                        'PRINT' => CCurrencyLang::CurrencyFormat($_sDiscountPrice, $_pRow['CURRENCY']),
                                        'PRINT_OLD' => ($_sDiscountPrice < $_sBasePrice)
                                            ? CCurrencyLang::CurrencyFormat($_sBasePrice, $_pRow['CURRENCY'])
                                            : '',
                                    ];
                                    break 2; // use first available price
                                }
                            }
                            $_secondItems[] = [
                                'ROW_KEY' => $_rowKey,
                                'ID' => $_id,
                                'STORE_ID' => (int)$_sId,
                                'CATEGORY' => $_categoryMap[(int)$_sId] ?? '',
                                'AMOUNT' => $_sAmount,
                                'NAME' => $_elem['NAME'],
                                'CML2_ARTICLE' => $_article,
                                'SELECTED_PRICE' => $_selectedPrice,
                                'MEASURE_RATIO' => $_ratio,
                                'QUANTITY_TRACE' => $_pd['QUANTITY_TRACE'] ?? 'N',
                                'CAN_BUY_ZERO' => $_pd['CAN_BUY_ZERO'] ?? 'N',
                                'TOTAL_QUANTITY' => $_sAmount,
                                'ACTUAL_QUANTITY' => 0,
                            ];
                        }
                    }

                    $arResult['ITEMS'][] = [
                        'ID' => $_id,
                        'IBLOCK_ID' => $_MARKDOWN_IB_ID,
                        'CODE' => $_elem['CODE'],
                        'NAME' => $_elem['NAME'],
                        'DETAIL_PAGE_URL' => $_detailUrl,
                        'PREVIEW_PICTURE' => !empty($_elem['PREVIEW_PICTURE']) ? CFile::GetFileArray($_elem['PREVIEW_PICTURE']) : null,
                        'DETAIL_PICTURE' => !empty($_elem['DETAIL_PICTURE']) ? CFile::GetFileArray($_elem['DETAIL_PICTURE']) : null,
                        '~PREVIEW_PICTURE' => !empty($_elem['PREVIEW_PICTURE']) ? CFile::GetFileArray($_elem['PREVIEW_PICTURE']) : null,
                        '~DETAIL_PICTURE' => !empty($_elem['DETAIL_PICTURE']) ? CFile::GetFileArray($_elem['DETAIL_PICTURE']) : null,
                        'PROPERTIES' => $_props,
                        'DISPLAY_PROPERTIES' => $_displayProps,
                        'IPROPERTY_VALUES' => [],
                        'PRODUCT' => [
                            'TYPE' => (int)($_pd['TYPE'] ?? 1),
                            'AVAILABLE' => $_pd['AVAILABLE'] ?? 'Y',
                            'QUANTITY' => (float)($_pd['QUANTITY'] ?? 0),
                            'QUANTITY_TRACE' => $_pd['QUANTITY_TRACE'] ?? 'N',
                            'CAN_BUY_ZERO' => $_pd['CAN_BUY_ZERO'] ?? 'N',
                            'MEASURE' => $_pd['MEASURE'] ?? null,
                            'WEIGHT' => (float)($_pd['WEIGHT'] ?? 0),
                        ],
                        'CATALOG_QUANTITY' => (float)($_pd['QUANTITY'] ?? 0),
                        'CATALOG_MEASURE_RATIO' => $_ratio,
                        'CATALOG_MEASURE_NAME' => 'шт',
                        'PRICE_MATRIX' => [
                            'COLS' => $_priceCols,
                            'MATRIX' => $_matrix,
                        ],
                        'PRINT_PRICES' => $_printPrices,
                        'MIN_PRICE' => $_minPrice,
                        'PRICES' => $_pricesByCode,
                        'ITEM_QUANTITY_RANGES' => $_quantityRanges,
                        'ITEM_QUANTITY_RANGE_SELECTED' => 'ZERO-INF',
                        'ITEM_PRICE_MODE' => 'S',
                        'OFFERS' => [],
                        'OFFERS_SELECTED' => 0,
                        'OFFERS_PROP' => [],
                        'SKU_TREE_VALUES' => [],
                        'JS_OFFERS' => [],
                        'MORE_PHOTO' => [],
                        'MORE_PHOTO_COUNT' => 0,
                        'BIG_DATA' => false,
                        'EDIT_LINK' => '',
                        'DELETE_LINK' => '',
                        'HAS_SECOND' => true,
                        'IS_ORPHAN_MARKDOWN' => true,
                        'IS_MARKDOWN_ORPHAN' => true,
                        'ITEM_SALE' => true,
                        'MARKDOWN_DEFAULT_STORE_ID' => $_markdownDefaultStoreId,
                        'MARKDOWN_DEFAULT_ROW_KEY' => $_markdownDefaultRowKey,
                        'MARKDOWN_STORES' => $_markdownStores,
                        'MARKDOWN_STORES_COUNT' => $_markdownStoreCount,
                        'SECOND_ITEMS' => $_secondItems,
                        'IS_MULTI_STORE_MARKDOWN' => $_isMultiStoreMarkdown,
                        'ACTUAL_QUANTITY' => $_actualQty,
                    ];
                }
            }
        }
    }

    unset(
        $_MARKDOWN_IB_ID, $_MAIN_IB_ID, $_currentSectionId,
        $_mainSection, $_sectionCode, $_markdownSectionId,
        $_mdItemsByArticle, $_matchedArticles, $_orphanItems,
        $_orphanIds, $_orphanPrices, $_orphanProdData,
        $_orphanMeasureRatios, $_orphanProps, $_priceCols,
        $_sectionPath, $_navChain, $_pathParts,
        $_detailUrlTpl, $_orphanBasketQty
    );
}





//}




if ($arParams["SHOW_MAX_QUANTITY"] !== "N") {
    $arTableHeader["AVALIABLE"] = Loc::getMessage('HEAD_AVAILABLE');
}

// Collect all visible prices
if(is_array($arResult['PRICES']) && !empty($arResult['PRICES']))
{
    foreach ($arResult['PRICES'] as $key => $PRICE)
    {
        // if($PRICE['CAN_VIEW'])
        // {
        $priceProperties['PRICES'][$key]['NAME'] = (empty($PRICE['TITLE']) ? $PRICE['CODE'] : $PRICE['TITLE']);
        $priceProperties['PRICES'][$key]['ID'] = $PRICE['ID'];
        // }
    }
}

// Add private price to prices
if (\Bitrix\Main\Loader::includeModule("sotbit.privateprice") && \Bitrix\Main\Config\Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && $GLOBALS["USER"]->IsAuthorized()) {
    $priceProperties['PRICES']['PRIVATE_PRICE']['NAME'] = Loc::getMessage('CT_BCS_CATALOG_MESS_PRIVATE_PRICE_TITLE');
    $priceProperties['PRICES']['PRIVATE_PRICE']['ID'] = '';
    $arResult["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"] = str_replace("PRODUCT_", "", \Bitrix\Main\Config\Option::get("sotbit.privateprice", "PRODUCT_UNIQUE_KEY", "ID"));
}

$arProductParams = array_merge($priceProperties, $skuProperties, $productProperties);

// Add quantity as a last child of header
if(is_array($arResult['PRICES']) && !empty($arResult['PRICES']))
{
    $arProductParams['QUANTITY'] = Loc::getMessage('HEAD_QUANTITY');
}

$arParams['TABLE_HEADER'] = array_merge($arTableHeader, $arProductParams);

if (Loader::includeModule("sotbit.privateprice")) {
    foreach($arResult['ITEMS'] as $val) {
        foreach ($val['OFFERS'] as $v) {
            $productsKey[] = $v['ID'];
        }
        $productsKey[] = $val['ID'];
    }

    $settings = SotbitPrivatePriceSettings::getInstance()->getOptions();
    $params = [
        "PRODUCT_COLUMN" => $settings["PRODUCT_COLUMN"],
        "PRICE_COLUMN" => $settings["PRICE_COLUMN"],
        "CURRENCY_FORMAT" => $settings["CURRENCY_FORMAT"],
        "PRODUCT_UNIQUE_KEY" => $settings["PRODUCT_UNIQUE_KEY"],
    ];



    // �������� ID �������/�������
    $productIds = [];
    foreach ($arResult['ITEMS'] as $item) {
        if (!empty($item['OFFERS'])) {
            foreach ($item['OFFERS'] as $offer) {
                $productIds[] = $offer['ID'];
            }
        } else {
            $productIds[] = $item['ID'];
        }
    }

    if ($productIds) {
        $res = StoreProductTable::getList([
            'filter' => ['=PRODUCT_ID' => $productIds],
            'select' => ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']
        ]);
        $storeAmounts = [];
        while ($row = $res->fetch()) {
            $storeAmounts[$row['PRODUCT_ID']][$row['STORE_ID']] = (int)$row['AMOUNT'];
        }

        foreach ($arResult['ITEMS'] as &$item) {
            if (!empty($item['OFFERS'])) {
                foreach ($item['OFFERS'] as &$offer) {
                    $offer['STORE_AMOUNT'] = $storeAmounts[$offer['ID']] ?? [];
                }
                unset($offer);
            } else {
                $item['STORE_AMOUNT'] = $storeAmounts[$item['ID']] ?? [];
            }
        }
        unset($item);
    }
    /*
    // ���������� ������ �� ������ 51
    if ($_GET['SORT']['CODE'] === 'PROPERTY_TRANSIT') {
        $storeId = 51;

        uasort($arResult['ITEMS'], function ($a, $b) use ($storeId) {
            // ������� ����� �������� ���� ������� �� ������ 51
            $aQty = 0;
            if (!empty($a['OFFERS'])) {
                foreach ($a['OFFERS'] as $offer) {
                    $aQty += (int)($offer['STORE_AMOUNT'][$storeId] ?? 0);
                }
            } else {
                $aQty = (int)($a['STORE_AMOUNT'][$storeId] ?? 0);
            }

            $bQty = 0;
            if (!empty($b['OFFERS'])) {
                foreach ($b['OFFERS'] as $offer) {
                    $bQty += (int)($offer['STORE_AMOUNT'][$storeId] ?? 0);
                }
            } else {
                $bQty = (int)($b['STORE_AMOUNT'][$storeId] ?? 0);
            }

            if ($_GET['SORT']['ORDER'] === 'asc,nulls') {
                return $aQty <=> $bQty; // ������� ������
            } else {
                return $bQty <=> $aQty; // ������� ������
            }
        });
    }

*/

    $sortOrder = strtolower($_GET['SORT']['ORDER'] ?? 'desc');

//    usort($arResult['ITEMS'], function($a, $b) use ($sortOrder) {
//        $valA = (int)$a['PROPERTIES']['STORE_51']['VALUE'];
//        $valB = (int)$b['PROPERTIES']['STORE_51']['VALUE'];
//
//        if ($sortOrder === 'asc' || $sortOrder === 'asc,nulls') {
//            return $valA <=> $valB;
//        } else {
//            return $valB <=> $valA;
//        }
//    });
    if ($settings['WORK_MODE']) {
        $params["ADDITIONAL_USER_FIELDS"] = array();
        $additionalUserSettings = unserialize($settings['USERS_PARAMS'], ['allowed_classes' => false]);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]) as $key => $value) {
            array_push($params['ADDITIONAL_USER_FIELDS'], [$value => $additionalUserSettings[$key]]);
        }
    } else {
        $params["ADDITIONAL_SESSIONS_FIELDS"] = array();
        $additionalSessionSettings = unserialize($settings['SESSION_KEY'], ['allowed_classes' => false]);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]) as $key => $value) {
            array_push($params['ADDITIONAL_SESSIONS_FIELDS'], [$value => $_SESSION[$additionalSessionSettings[$key]]]);
        }
    }
    $arResult['ITEMS_PRIVAT_PRICES'] = SotbitPrivatePriceMain::makeMainCheckFields($productsKey, $params);
    foreach ($arResult['ITEMS_PRIVAT_PRICES'] as $key => $val) {
        $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRIVAT_PRICE_PRINT'] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arResult['ITEMS_PRIVAT_PRICES'][$key][$params["PRICE_COLUMN"]], $arResult['ITEMS_PRIVAT_PRICES'][$key][$params["CURRENCY_FORMAT"]], $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']), $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']);
    }
    $arResult['PRIVAT_PRICES_PARAMS'] = $params;
}





unset (
    $arTableHeader,
    $arProductParams,
    $skuProperties,
    $productProperties
);

