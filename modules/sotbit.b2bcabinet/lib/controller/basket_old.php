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

        foreach ($basket as $basketItem) {
            $arBasketItems[$basketItem->getProductId()] = $basketItem;
        }

        if (isset($arBasketItems[$arFields['PRODUCT_ID']])) {
            if ($arFields['QUANTITY'] != 0) {
                $arBasketItems[$arFields['PRODUCT_ID']]->setField('QUANTITY', $arFields['QUANTITY']);
            } else {
                $arBasketItems[$arFields['PRODUCT_ID']]->delete();
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
}