<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arResult['HEADERS'] = [
    [
        "id" => 'NAME',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_NAME'),
        "sort" => 'NAME',
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'ARTICLE',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_ARTICLE'),
        "sort" => 'ARTICLE',
        "default" => true,
        "editable" => false,
        "align" => "right",
    ],
    [
        "id" => 'QUANTITY',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_QNT'),
        "sort" => 'QUANTITY',
        "default" => true,
        "editable" => false,
        "align" => "right",
    ],
    [
        "id" => 'PRICE_FORMAT',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_PRICE'),
        "sort" => 'PRICE',
        "default" => true,
        "editable" => false,
        "align" => "right",
    ],
    [
        "id" => 'TOTAL_FORMAT',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_TOTAL'),
        "sort" => 'TOTAL',
        "default" => true,
        "editable" => false,
        "align" => "right",
    ],
];

$arResult['FILTER_HEADER'] = [
    [
        'id' => 'NAME',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_NAME'),
        'type' => 'text',
    ],
    [
        'id' => 'ARTICLE',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_ARTICLE'),
        'type' => 'text',
    ],
];


if ($arResult['PRODUCTS']) {

    if (\Bitrix\Main\Loader::includeModule('sotbit.b2bcabinet')) {
        $useReplace = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
        if ($useReplace) {
            $replaceableValue = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
            $replaceValue = \Bitrix\Main\Config\Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
        }
    }

    foreach ($arResult['PRODUCTS'] as $products) {
        $products['DETAIL_PAGE_URL'] = $useReplace ?  str_replace($replaceableValue, $replaceValue, $products['DETAIL_PAGE_URL']) : $products['DETAIL_PAGE_URL'];

        $products['NAME'] = '<div class="requestoffer__products-item">
                                <div class="requestoffer__products-item__img">
                                    <img class="rounded" src="'.($products["IMG"] ?: SITE_TEMPLATE_PATH.'/assets/images/no_photo.svg').'" alt="'.$products['NAME'].'">
                                </div>
                                <div>
                                    <a href="'.$products['DETAIL_PAGE_URL'].'" target="_blank">'.$products['NAME'].'</a>
                                </div>
                            </div>';
        $products['PRICE'] =
        $arResult['PRODUCTS_ROW'][$products["ID"]] = [
            'data' => $products
        ];
    }
}