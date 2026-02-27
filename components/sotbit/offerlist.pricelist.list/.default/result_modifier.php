<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arResult['HEADERS'] = [
    [
        "id" => 'ID',
        "name" => Loc::getMessage('SO_PRICELIST_LIST_ID'),
        "sort" => 'ID',
        "default" => false,
        "editable" => false
    ],
    [
        "id" => 'ID_STR',
        "name" => Loc::getMessage('SO_PRICELIST_LIST_ID'),
        "sort" => 'ID_STR',
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'NAME',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_NAME'),
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'DATE_CREATE',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_DATE_CREATE'),
        "sort" => 'DATE_CREATE',
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'DATE_UPDATE',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_DATE_UPDATE'),
        "sort" => 'DATE_UPDATE',
        "default" => true,
        "editable" => false
    ],
];

$arResult['FILTER_HEADER'] = [
    [
        'id' => 'ID',
        'name' => Loc::getMessage('SO_PRICELIST_LIST_ID'),
        'type' => 'text',
    ],
    [
        'id' => 'NAME',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_NAME'),
        'type' => 'text',
    ],
    [
        'id' => 'DATE_CREATE',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_DATE_CREATE'),
        'type' => 'date',
    ],
    [
        'id' => 'DATE_UPDATE',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_DATE_UPDATE'),
        'type' => 'date',
    ],
];

if ($arResult['PRICELIST']) {
    foreach ($arResult['PRICELIST'] as $item) {
        $item['DETAIL_PAGE'] = preg_replace('/#.+?#/', $item["ID"], $arParams["DETAIL_URL"]);
        $arResult["ROWS"][$item["ID"]] = [
            'data' => [
                'ID' => $item['ID'],
                'ID_STR' => Loc::getMessage('SO_PRICELIST_LIST_ID') . $item['ID'],
                'NAME' => $item['NAME'] ?: 'Pricelist' . ' ' . $item['ID'],
                'DATE_CREATE' => $item['DATE_CREATE']->toString(),
                'DATE_UPDATE' => $item['DATE_UPDATE'] ? $item['DATE_UPDATE']->toString() : '',
            ],
            'actions' => [
                0 => [
                    "TEXT" => Loc::getMessage("SO_REQUEST_LIST_ACTION_DETAIL"),
                    "ONCLICK" => "location.assign('" . $item['DETAIL_PAGE'] . "')",
                    "DEFAULT" => true
                ],
                1 => [
                    "TEXT" => Loc::getMessage("SO_REQUEST_LIST_ACTION_DELETE"),
                    "ICON" => 'ph-trash',
                    "HIDDEN" => 'Y',
                    "HIDETEXT" => 'Y',
                    "ONCLICK" => "javascript:event.stopPropagation();if(confirm('" . Loc::getMessage("SO_REQUEST_LIST_ACTION_CONFIRM_DELETE") . "')) deletePriceList('" . $item['ID'] . "')",
                ]
            ]
        ];
    }
}