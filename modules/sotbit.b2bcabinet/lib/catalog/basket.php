<?php
namespace Sotbit\B2BCabinet\Catalog;

use Bitrix\Main\Loader;

class Basket extends \SotbitB2bCabinet
{
    public static function getBasketItemsQuantity() {
        if(!Loader::includeModule('sale')) {
            return [];
        }
        
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );
        $arBasketItems = [];
        foreach ($basket as $basketItem) {
            $arBasketItems[$basketItem->getProductId()] = $basketItem->getQuantity();
        }
        return $arBasketItems;
    }
}