<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/tables/datatables.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/jquery/jquery.bootstrap-touchspin.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/tables/datatables/extensions/natural-sort.js");

$propsType = [
    "S" => "TEXT",
    "L" => "SELECT",
    "HTML" => "TEXTAREA",
    "F" => "FILE",
];

$dateFields = [
    "DATE_CREATE",
    "TIMESTAMP_X",
    "ACTIVE_FROM",
    "DATE_ACTIVE_FROM",
    "ACTIVE_TO",
    "DATE_ACTIVE_TO",
];

$fileFields = [
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
];

$htmlFields = [
    "PREVIEW_TEXT",
    "DETAIL_TEXT",
];

$arResult["FORM_GROUPS"] = [];

$arResult["FORM_SETTINGS"] = [
    "ATTRIBUTES" => [
        "NAME" => 'COMPLAIT_ADD',
    ],
    "PARAMS" => [],
    "BUTTONS" => [],
];

if ($arResult["ADD_COMPLAINTS_FIELDS"]) {
    foreach ($arResult["ADD_COMPLAINTS_FIELDS"] as $field) {
        $attr = [];
        if (in_array($field["FIELD_ID"], $dateFields)) {
            $type = 'DATE';
            $attr = [
                "NAME" => $field["FIELD_ID"],
            ];
        } elseif (in_array($field["FIELD_ID"], $fileFields)) {
            $type = 'FILE';
            $attr = [
                "NAME" => $field["FIELD_ID"],
                "CLASS" => 'complaint__input-type-file',
                "ACCEPT" => "image/*",
            ];
        } elseif (in_array($field["FIELD_ID"], $htmlFields)) {
            $type = 'TEXTAREA';
        } else {
            $type = 'TEXT';
        }

        $rows[] = [
            "LABEL_STYLE" => "BASIC",
            "LABEL_BP_SIZE" => "3",
            "LABEL" => $field["NAME"],
            "IS_REQUIRED" => $field["IS_REQUIRED"],
            "MULTIPLE" => $field["MULTIPLE"],
            "ITEMS" => [
                [
                    "INPUT_TYPE" => $type,
                    "ATTRIBUTES" => $attr ?: [
                        "NAME" => "COMPLAINTS[FIELDS][".$field["FIELD_ID"]."]",
                    ]
                ]
            ]
        ];
    }

    $arResult["FORM_GROUPS"][] = [
        [
            "NAME" => Loc::getMessage("SOTBIT_COMPLAINTS_FORM_GROUP_1"),
            "ID" => 'COMPLAINTS_FIELDS_ADD',
            "COLLAPSE" => 'Y',
            "ITEM_ROWS" => $rows
        ]
    ];
}

if ($arResult['ADD_COMPLAINTS_PROPERTIES']) {
    $rows = [];

    foreach ($arResult['ADD_COMPLAINTS_PROPERTIES'] as $props) {
        $attr = $item = [];
        if (is_array($props["DEFAULT_VALUE"]) && isset($props["DEFAULT_VALUE"]["TEXT"])) {
            $type = "HTML";
            $value = $props["DEFAULT_VALUE"]["TEXT"];
        } else {
            $type = $props["PROPERTY_TYPE"];
            $value = $props["DEFAULT_VALUE"];
        }

        if ($propsType[$type] == "FILE") {
            $attr = [
                "NAME" => $props["CODE"],
                "CLASS" => 'complaint__input-type-file',
                "MULTIPLE" => $props["MULTIPLE"],
                "FILE_TYPE" => $props["FILE_TYPE"]
            ];
        }

        $item = [
            "INPUT_TYPE" => $propsType[$type],
            "HELPER_TEXT" => $propsType[$type] == "FILE" ? Loc::getMessage("SOTBIT_COMPLAINTS_FORM_FILE_HELPER_TEXT") : "",
            "ATTRIBUTES" => $attr ?: [
                "NAME" => "COMPLAINTS[PROPERTIES][".$props["CODE"]."]",
                "VALUE" => $value,
                "MULTIPLE" => "",
            ],
            "OPTIONS" => $props["OPTIONS"]
        ];

        if ($props["MULTIPLE"] != "Y") {
            unset($item["ATTRIBUTES"]["MULTIPLE"]);
        } elseif ($props["MULTIPLE"] == "Y") {
            $item["ATTRIBUTES"]["NAME"] = $item["ATTRIBUTES"]["NAME"] . '[]';
        }

        $rows[] = [
            "LABEL_STYLE" => "BASIC",
            "LABEL_BP_SIZE" => "3",
            "LABEL" => $props["NAME"],
            "ITEMS" => [
                $item
            ]
        ];
    }

    $arResult["FORM_GROUPS"][] = [
        [
            "NAME" => Loc::getMessage("SOTBIT_COMPLAINTS_FORM_GROUP_2"),
            "ID" => 'COMPLAINTS_PROPS_ADD',
            "COLLAPSE" => 'Y',
            "ITEM_ROWS" => $rows
        ]
    ];
}

$arResult["COLUMN_TITLE"] = [];
$arResult["COLUMN_MODEL"] = [];

$arResult["COLUMN_MODEL"][0] = '${product.NAME}';
$arResult["COLUMN_TITLE"][0] = Loc::getMessage("SOTBIT_COMPLAINTS_POSITIONS_PRODUCT_TITLE");

if ($arResult["ADD_POSITIONS_FIELDS"]) {
    foreach ($arResult["ADD_POSITIONS_FIELDS"] as $field) {
        if (in_array($field["FIELD_ID"], $dateFields)) {
            $type = 'date';
        } elseif (in_array($field["FIELD_ID"], $fileFields)) {
            $type = 'file';
        } else {
            $type = 'text';
        }

        $arResult["COLUMN_TITLE"][] = $field["NAME"];
        $arResult["COLUMN_MODEL"][] = '<input type="' .$type. '" name="POSITIONS[${rowCount}][FIELDS][' .$field["FIELD_ID"]. ']" class="form-control" data-item="position">';
    }
}

if ($arResult["ADD_POSITIONS_PROPERTIES"]) {
    foreach ($arResult["ADD_POSITIONS_PROPERTIES"] as $props) {
        $inputName = 'name="POSITIONS[${rowCount}][PROPERTIES][' .$props["CODE"]. ']"';
        if ($props["PROPERTY_TYPE"] == "N") {
            $arResult["COLUMN_MODEL"][] = '<input type="text" '.$inputName.' class="touchspin-basic" data-item="position" data-max="${product.MAXQUANTITY}" data-ratio="${product.RATIO}">';
        } elseif ($props["PROPERTY_TYPE"] == "L") {
            $select = '';
            $select = '<select class="form-control" '.$inputName.' data-item="position" data-minimum-results-for-search="Infinity">';
            foreach ($props["OPTIONS"] as $enum) {
                $select .= '<option value="'.$enum["VALUE"].'">'.$enum["OPTION_NAME"].'</option>';
            }
            $select .= '</select>';
            $arResult["COLUMN_MODEL"][] = $select;
        } else {
            $arResult["COLUMN_MODEL"][] = '<input data-item="position" data-max="${product.MAXQUANTITY}" data-ratio="${product.RATIO}" type="text" '.$inputName.' class="form-control">';
        }
        $arResult["COLUMN_TITLE"][] = $props["NAME"];
    }
}

$arResult["COLUMN_MODEL"][] = '<a role="button" class="delete-position" data-name="${product.NAME}">'.Loc::getMessage('SOTBIT_COMPLAINTS_BTN_REMOVE').'</a>
                               <input type="hidden" name="POSITIONS[${rowCount}][PRODUCT_ID]" value="${product.ID}" data-item="position">';