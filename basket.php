<?php

namespace Sotbit\B2bcabinet\Controller;

use \Bitrix\Main\Loader,
    Bitrix\Main\Engine\ActionFilter;

class Basket extends \Bitrix\Main\Engine\Controller
{
    public function configureActions()
    {
        return [
            'getBasketSmallState' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        [
                            ActionFilter\HttpMethod::METHOD_POST,
                        ]
                    ),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    public function addProductToBasketAction($arFields)
    {
        if (!is_array($arFields)) {
            $arFields = [];
        }
        $arFields['PRODUCT_ID'] = (int)($arFields['PRODUCT_ID'] ?? 0);
        $arFields['QUANTITY'] = isset($arFields['QUANTITY']) ? (float)$arFields['QUANTITY'] : 0.0;
        if (!isset($arFields['PROPS']) || !is_array($arFields['PROPS'])) {
            $arFields['PROPS'] = [];
        }
        static::trace('addProductToBasketAction.start', ['AR_FIELDS' => $arFields]);

        if (!static::checkReqiredModules()) {
            static::trace('addProductToBasketAction.skip.modules');
            return false;
        }

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        $isMarkdownPosition = static::isMarkdownFields($arFields);
        $basketItemForUpdate = null;
        foreach ($basket as $basketItem) {
            if ($isMarkdownPosition) {
                if (static::isSameBasketPosition($basketItem, $arFields)) {
                    $basketItemForUpdate = $basketItem;
                    break;
                }
            } elseif (
                !static::isBasketItemMarkdown($basketItem)
                && (int)$basketItem->getProductId() === (int)$arFields['PRODUCT_ID']
            ) {
                $basketItemForUpdate = $basketItem;
                break;
            }
        }

        if ($basketItemForUpdate !== null) {
            static::trace('addProductToBasketAction.update', [
                'BASKET_ITEM_ID' => (int)$basketItemForUpdate->getId(),
                'PRODUCT_ID' => (int)$basketItemForUpdate->getProductId(),
                'PRICE_BEFORE' => (float)$basketItemForUpdate->getPrice(),
                'BASE_PRICE_BEFORE' => (float)$basketItemForUpdate->getBasePrice(),
                'DISCOUNT_PRICE_BEFORE' => (float)$basketItemForUpdate->getDiscountPrice(),
                'CUSTOM_PRICE_BEFORE' => (string)$basketItemForUpdate->getField('CUSTOM_PRICE'),
            ]);

            if ((float)$arFields['QUANTITY'] != 0.0) {
                $basketItemForUpdate->setField('QUANTITY', $arFields['QUANTITY']);
            } else {
                $basketItemForUpdate->delete();
            }

            $resultSave = $basket->save();

            if (!$resultSave->isSuccess()) {
                static::trace('addProductToBasketAction.update.error', ['ERRORS' => $resultSave->getErrorMessages()]);
                $this->addErrors($resultSave->getErrors());
                return null;
            }

            static::trace('addProductToBasketAction.update.saved', [
                'BASKET_ITEM_ID' => (int)$basketItemForUpdate->getId(),
                'PRODUCT_ID' => (int)$basketItemForUpdate->getProductId(),
                'PRICE_AFTER' => (float)$basketItemForUpdate->getPrice(),
                'BASE_PRICE_AFTER' => (float)$basketItemForUpdate->getBasePrice(),
                'DISCOUNT_PRICE_AFTER' => (float)$basketItemForUpdate->getDiscountPrice(),
                'CUSTOM_PRICE_AFTER' => (string)$basketItemForUpdate->getField('CUSTOM_PRICE'),
            ]);

            return $arFields['QUANTITY'];
            
        } else {
            $result = \Bitrix\Catalog\Product\Basket::addProduct($arFields);

            if (!$result->isSuccess()) {
                static::trace('addProductToBasketAction.add.error', ['ERRORS' => $result->getErrorMessages()]);
                $this->addErrors($result->getErrors());
                return null;
            }

            static::trace('addProductToBasketAction.added', [
                'PRODUCT_ID' => (int)($arFields['PRODUCT_ID'] ?? 0),
                'QUANTITY' => (float)($arFields['QUANTITY'] ?? 0),
                'RESULT_DATA' => $result->getData(),
            ]);

            return $arFields['QUANTITY'];
        }

    }

    private static function isMarkdownFields(array $arFields): bool
    {
        if (!empty($arFields['MARKDOWN_ROW_KEY']) || !empty($arFields['MARKDOWN_STORE_ID'])) {
            return true;
        }

        $props = isset($arFields['PROPS']) && is_array($arFields['PROPS']) ? $arFields['PROPS'] : [];
        foreach ($props as $prop) {
            if (!is_array($prop)) {
                continue;
            }
            $code = (string)($prop['CODE'] ?? $prop['NAME'] ?? '');
            if ($code === 'MARKDOWN_ROW_KEY' || $code === 'MARKDOWN_STORE_ID') {
                return true;
            }
        }

        return false;
    }

    public function getBasketSmallStateAction() {
        if (!static::checkReqiredModules()) {
            return false;
        }

        global $USER;

        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            $siteId
        );
        $order = \Bitrix\Sale\Order::create(
            $siteId,
            $USER->GetID()
        );
        $order->setBasket($basket);
        $discounts = $order->getDiscount();
        $discounts->getApplyResult();

        return [
            'price' => $order->getPrice(),
            'print_price' =>  \CCurrencyLang::CurrencyFormat(
                $order->getPrice(),
                \Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency($siteId),
                true
            ),
            'currency' => \CCurrency::GetBaseCurrency(),
            'quantity' => $basket->count()
        ];
    }

    public function getBasketItemsQuantityAction() {
        if (Loader::includeModule("sale")) {
            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                \Bitrix\Sale\Fuser::getId(),
                \Bitrix\Main\Context::getCurrent()->getSite()
            );
            $arBasketItems = [];
            foreach ($basket as $basketItem) {
                $arBasketItems[$basketItem->getProductId()] = $basketItem->getQuantity();
            }
        }

        return $arBasketItems ?: [];
    }

    public function getBasketItemsAction($arFields) {
        $Items = new \Sotbit\B2BCabinet\Shop\BasketItems($arFields['PRODUCTS_FILTER'], $arFields['IMG_PROP']);
        $result = [];
        $result["quantity"] = $Items->getQnt();
        $result["print_price"] = $Items->getSum();

        foreach ($Items->getItems() as $item) {
            $result["products"][] = [
                'img' => $item->getElement()->getImg()
            ];
        }
        return $result;
    }

    private static function checkReqiredModules()
    {
        return !Loader::includeModule('sale') || !Loader::includeModule('catalog') || !Loader::includeModule('currency') ? false : true;
    }

    private static function isSameBasketPosition(\Bitrix\Sale\BasketItem $basketItem, array $arFields)
    {
        $incomingProductId = isset($arFields['PRODUCT_ID']) ? (int)$arFields['PRODUCT_ID'] : 0;
        if ($incomingProductId <= 0 || (int)$basketItem->getProductId() !== $incomingProductId) {
            return false;
        }

        $incomingStoreId = static::extractStoreIdFromFields($arFields);
        $incomingRowKey = static::extractMarkdownRowKeyFromFields($arFields);

        $itemStoreId = static::extractStoreIdFromBasketItem($basketItem);
        $itemRowKey = static::extractMarkdownRowKeyFromBasketItem($basketItem);

        return $incomingStoreId === $itemStoreId && $incomingRowKey === $itemRowKey;
    }

    private static function isBasketItemMarkdown(\Bitrix\Sale\BasketItem $basketItem): bool
    {
        if ((int)static::extractStoreIdFromBasketItem($basketItem) > 0) {
            return true;
        }
        return static::extractMarkdownRowKeyFromBasketItem($basketItem) !== '';
    }

    private static function extractStoreIdFromFields(array $arFields)
    {
        $fieldPriority = ['MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'];
        foreach ($fieldPriority as $fieldName) {
            if (isset($arFields[$fieldName]) && (int)$arFields[$fieldName] > 0) {
                return (int)$arFields[$fieldName];
            }
        }

        return static::extractValueByCodeFromProps(
            isset($arFields['PROPS']) && is_array($arFields['PROPS']) ? $arFields['PROPS'] : [],
            $fieldPriority
        );
    }

    private static function extractMarkdownRowKeyFromFields(array $arFields)
    {
        if (isset($arFields['MARKDOWN_ROW_KEY']) && $arFields['MARKDOWN_ROW_KEY'] !== '') {
            return (string)$arFields['MARKDOWN_ROW_KEY'];
        }

        return static::extractValueByCodeFromProps(
            isset($arFields['PROPS']) && is_array($arFields['PROPS']) ? $arFields['PROPS'] : [],
            ['MARKDOWN_ROW_KEY'],
            ''
        );
    }

    private static function extractStoreIdFromBasketItem(\Bitrix\Sale\BasketItem $basketItem)
    {
        $fieldPriority = ['MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'];
        foreach ($fieldPriority as $fieldName) {
            $value = $basketItem->getField($fieldName);
            if ((int)$value > 0) {
                return (int)$value;
            }
        }

        return static::extractValueByCodeFromProps(
            static::getBasketItemProps($basketItem),
            $fieldPriority
        );
    }

    private static function extractMarkdownRowKeyFromBasketItem(\Bitrix\Sale\BasketItem $basketItem)
    {
        $fieldValue = $basketItem->getField('MARKDOWN_ROW_KEY');
        if ($fieldValue !== null && $fieldValue !== '') {
            return (string)$fieldValue;
        }

        return static::extractValueByCodeFromProps(
            static::getBasketItemProps($basketItem),
            ['MARKDOWN_ROW_KEY'],
            ''
        );
    }

    private static function getBasketItemProps(\Bitrix\Sale\BasketItem $basketItem)
    {
        $propertyCollection = $basketItem->getPropertyCollection();
        if (!$propertyCollection) {
            return [];
        }

        return $propertyCollection->getPropertyValues();
    }

    private static function extractValueByCodeFromProps(array $props, array $codes, $default = 0)
    {
        foreach ($props as $prop) {
            if (!is_array($prop)) {
                continue;
            }

            $code = isset($prop['CODE']) ? (string)$prop['CODE'] : '';
            if ($code === '' && isset($prop['NAME'])) {
                $code = (string)$prop['NAME'];
            }

            if (!in_array($code, $codes, true)) {
                continue;
            }

            if ($default === '') {
                return isset($prop['VALUE']) ? (string)$prop['VALUE'] : '';
            }

            if (isset($prop['VALUE']) && (int)$prop['VALUE'] > 0) {
                return (int)$prop['VALUE'];
            }
        }

        return $default;
    }

    private static function trace(string $tag, array $data = []): void
    {
        $line = print_r([
            'BASKET_CONTROLLER' => $tag,
            'TS' => date('Y-m-d H:i:s'),
            'FILE' => __FILE__,
            'DATA' => $data,
        ], true);
        $docRoot = (string)($_SERVER['DOCUMENT_ROOT'] ?? '');
        $targets = [];

        if ($docRoot !== '') {
            $docRoot = rtrim($docRoot, '/\\');
            $targets[] = $docRoot . '/upload/basket_controller_debug.log';
            $targets[] = $docRoot . '/local/php_interface/basket_controller_debug.log';
            $targets[] = $docRoot . '/local/php_interface/log.txt';
        }

        $targets[] = __DIR__ . '/basket_controller_debug.log';
        $targets[] = sys_get_temp_dir() . '/basket_controller_debug.log';

        foreach (array_unique($targets) as $path) {
            @file_put_contents($path, $line . PHP_EOL, FILE_APPEND);
        }
    }
}
