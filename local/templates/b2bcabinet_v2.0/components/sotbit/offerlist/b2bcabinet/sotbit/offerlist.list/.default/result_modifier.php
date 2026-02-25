<?php
use Bitrix\Main\Config\Option;

$useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
$replaceValue = null;
if ($useReplace) {
    $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
    $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
    foreach ($arResult["OFFERS"] as $key => $val) {
        if (!empty($arResult["OFFERS"][$key]['DETAIL_PAGE_URL']))
            $arResult["OFFERS"][$key]['DETAIL_PAGE_URL'] = str_replace($replaceableValue, $replaceValue, $arResult["OFFERS"][$key]['DETAIL_PAGE_URL']);
        if(!empty($arResult["OFFERS"][$key]['PRODUCTS'])){
            foreach ($val['PRODUCTS'] as $keyOff => $item) {
                if (!empty($arResult["OFFERS"][$key]['PRODUCTS'][$keyOff]['DETAIL_PAGE_URL'] ))
                    $arResult["OFFERS"][$key]['PRODUCTS'][$keyOff]['DETAIL_PAGE_URL'] = str_replace($replaceableValue, $replaceValue, $arResult["OFFERS"][$key]['PRODUCTS'][$keyOff]['DETAIL_PAGE_URL']);
            }
        }
    }
}