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
        if (!static::checkReqiredModules()) {
            return false;
        }

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        $basketItemForUpdate = null;
        foreach ($basket as $basketItem) {
            if (static::isSameBasketPosition($basketItem, $arFields)) {
                $basketItemForUpdate = $basketItem;
                break;
            }
        }

        if ($basketItemForUpdate !== null) {
            if ($arFields['QUANTITY'] != 0) {
                $basketItemForUpdate->setField('QUANTITY', $arFields['QUANTITY']);
            } else {
                $basketItemForUpdate->delete();
            }

            $resultSave = $basket->save();

            if (!$resultSave->isSuccess()) {
                $this->addErrors($resultSave->getErrors());
                return null;
            }

            return $arFields['QUANTITY'];

        } else {
            $result = \Bitrix\Catalog\Product\Basket::addProduct($arFields);

            if (!$result->isSuccess()) {
                $this->addErrors($result->getErrors());
                return null;
            }

            return $arFields['QUANTITY'];
        }

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
}
