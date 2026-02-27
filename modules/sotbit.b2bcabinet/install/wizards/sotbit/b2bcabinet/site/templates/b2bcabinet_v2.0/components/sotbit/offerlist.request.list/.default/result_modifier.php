<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arResult['HEADERS'] = [
    [
        "id" => 'ID',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_ID'),
        "sort" => 'ID',
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
        "id" => 'STATUS',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_STATUS'),
        "sort" => 'STATUS',
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'FIELDS',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_FILEDS'),
        "default" => true,
        "editable" => false
    ],
    [
        "id" => 'COMMENT',
        "name" => Loc::getMessage('SO_REQUEST_HEADER_COMMENT'),
        "default" => true,
        "editable" => false
    ],
];

$arResult['FILTER_HEADER'] = [
    [
        'id' => 'ID',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_ID'),
        'type' => 'text',
    ],
    [
        'id' => 'DATE_CREATE',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_DATE_CREATE'),
        'type' => 'date',
    ],
    [
        'id' => 'STATUS',
        'name' => Loc::getMessage('SO_REQUEST_HEADER_STATUS'),
        'type' => 'list',
        'items' => $arResult['REQUEST_STATUS_LIST']
    ]
];

if ($arResult['REQUEST']) {
    foreach ($arResult['REQUEST'] as $request) {
        $request['DETAIL_PAGE'] = preg_replace('/#.+?#/', $request["ID"], $arParams["DETAIL_URL"]);
        $arResult["ROWS"][$request["ID"]] = [
            'data' => [
                'ID' => Loc::getMessage('SO_REQUEST_HEADER_ID') . $request['ID'],
                'DATE_CREATE' => $request['DATE_CREATE']->toString(),
                'COMMENT' => $request['COMMENT'],
                'FIELDS' => implode("<br>",
                    array_map(fn($item, $key) => '<span class="text-muted">' . $arResult["REQUEST_FIELDS_ALIAS"][$key] . ':</span> ' . $item,
                        $request["FIELDS"],
                        array_keys($request["FIELDS"]))),
                'STATUS' => $arResult['REQUEST_STATUS_LIST'][$request['STATUS']]
            ],
            'actions' => [
                0 => [
                    "TEXT" => Loc::getMessage("SO_REQUEST_LIST_ACTION_DETAIL"),
                    "ONCLICK" => "location.assign('" . $request['DETAIL_PAGE'] . "')",
                    "DEFAULT" => true
                ],
                1 => [
                    "TEXT" => Loc::getMessage("SO_REQUEST_LIST_ACTION_DELETE"),
                    "HIDETEXT"=>"Y",
                    "ICON" => "ph-trash",
                    "HIDDEN"=>"Y",
                    "ONCLICK" => "javascript:if(confirm('" . Loc::getMessage("SO_REQUEST_LIST_ACTION_CONFIRM_DELETE") . "')) deleteRequestOffer('" . $request['ID'] . "')",
                ]
            ]
        ];
    }
}