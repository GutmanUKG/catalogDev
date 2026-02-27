<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Text\Encoding,
    Sotbit\B2BCabinet\Controller\FileController;

$propertyBindPropuct = $arParams["COMPLAINTS_OPTIONS"]["PROPERTY_BINDING_PRODUCTS"];
$arResult["FORM_GROUPS"] = [];

$arResult["FORM_SETTINGS"] = [
    "ATTRIBUTES" => [
        "NAME" => 'COMPLAIT_DETAIL',
    ],
    "PARAMS" => [],
    "BUTTONS" => [
        0 => [
            "TYPE" => 'button',
            "VALUE" => Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_BUTTON_TO_LIST"),
            "CLASS" => 'btn btn-primary',
            "ONCLICK" => "location.assign('". $arParams["PATH_TO_LIST"] ."')",
        ],
    ],
];

$fieldsNoRender = [
    "DETAIL_TEXT_TYPE",
    "PREVIEW_TEXT_TYPE",
    "ID",
];

if (!function_exists("createInputItem")) {
    function createInputItem($type, $value, $description)
    {
        if ($type == "F") {
            $file = CFile::GetFileArray($value);
            if (!Encoding::detectUtf8($file["ORIGINAL_NAME"]) && SITE_CHARSET == "UTF-8") {
                $file["ORIGINAL_NAME"] = Encoding::convertEncoding($file["ORIGINAL_NAME"], "WINDOWS-1251", "UTF-8");
            } elseif (Encoding::detectUtf8($file["ORIGINAL_NAME"]) && !SITE_CHARSET == "UTF-8") {
                $file["ORIGINAL_NAME"] = Encoding::convertEncoding($file["ORIGINAL_NAME"], "UTF-8", "WINDOWS-1251");
            }

            $item = [
                "INPUT_TYPE" => "MEDIA",
                "ATTRIBUTES" => [
                    "NAME" => $file["ORIGINAL_NAME"],
                    "DESCRIPTION" => $description,
                    "SRC" => FileController::urlGenerate(
                        'fileDownload',
                        ['fileId' => $file['ID'], 'fileName' => addslashes(substr( $file["ORIGINAL_NAME"], 0, strrpos( $file["ORIGINAL_NAME"],'.')))],
                    ),
                ]
            ];
        } elseif (is_array($value) && $value["TEXT"]) {
            $item = [
                "INPUT_TYPE" => "TEXTAREA",
                "ATTRIBUTES" => [
                    "VALUE" => $value["TEXT"],
                    "READONLY" => 1,
                ]
            ];
        } elseif ($type == "HTML") {
            $item = [
                "INPUT_TYPE" => "TEXTAREA",
                "ATTRIBUTES" => [
                    "VALUE" => $value,
                    "READONLY" => 1,
                ]
            ];
        } else {
            $item = [
                "INPUT_TYPE" => "TEXT",
                "ATTRIBUTES" => [
                    "VALUE" => $value,
                    "READONLY" => 1,
                ]
            ];
        }

        return $item;
    }
}

if ($arResult["COMPLAINT"]["FIELDS"] && CModule::IncludeModule("lists")) {
    $iblock = new CListFieldList($arParams["COMPLAINTS_OPTIONS"]["IBLOCK_COMPLAINTS_ID"]);
    foreach ($arResult["COMPLAINT"]["FIELDS"] as $fieldCode => $field) {
        if (in_array($fieldCode, $fieldsNoRender) || !$field) {
            continue;
        }
        $type = "TEXT";
        if ($fieldCode == "PREVIEW_PICTURE" || $fieldCode == "DETAIL_PICTURE") {
            $type = "F";
        } elseif ($fieldCode == "PREVIEW_TEXT" || $fieldCode == "DETAIL_TEXT") {
            $type = "HTML";
        }
        $item = [createInputItem($type, $field, "")];
        $rows[] = [
            "LABEL_STYLE" => "BASIC",
            "LABEL_BP_SIZE" => "3",
            "LABEL" => $iblock->GetArrayByID($fieldCode)["NAME"] ?: Loc::getMessage("IBLOCK_FIELD_" . $fieldCode),
            "ITEMS" => $item
        ];
    }

    $arResult["FORM_GROUPS"][] = [
        [
            "NAME" => Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_CARD_FIELDS"),
            "ID" => 'COMPLAINTS_FIELDS',
            "COLLAPSE" => 'Y',
            "ITEM_ROWS" => $rows
        ]
    ];
}


if ($arResult["COMPLAINT"]["PROPERTIES"]) {
    $rows = [];
    foreach ($arResult["COMPLAINT"]["PROPERTIES"] as $propertry) {
        if (!$propertry["VALUE"]) {
            continue;
        }
        $item = [];
        if ($propertry["MULTIPLE"] == "Y") {
            foreach ($propertry["VALUE"] as $key => $value) {
                $item[] = createInputItem($propertry["PROPERTY_TYPE"], $value, $propertry["DESCRIPTION"][$key]);
            }
        } else {
            $item[] = createInputItem($propertry["PROPERTY_TYPE"], $propertry["VALUE"], $propertry["DESCRIPTION"]);
        }

        $rows[] = [
            "LABEL_STYLE" => "BASIC",
            "LABEL_BP_SIZE" => "3",
            "LABEL" => $propertry["NAME"],
            "ITEMS" => $item
        ];
    }

    $arResult["FORM_GROUPS"][] = [
        [
            "NAME" => Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_CARD_PROPS"),
            "ID" => 'COMPLAINTS_PROPS',
            "COLLAPSE" => 'Y',
            "ITEM_ROWS" => $rows
        ]
    ];
}

$header = [
    [
        'id' => "ID",
        'name' => "ID",
        "default" => false,
        "editable" => false
    ],
    [
        'id' => "NAME",
        'name' => Loc::getMessage("SOTBIT_COMPLAINTS_POSITIONS_COL_NAME"),
        "default" => true,
        "editable" => false
    ],
    [
        'id' => "PRODUCT",
        'name' => Loc::getMessage("SOTBIT_COMPLAINTS_POSITIONS_COL_PRODUCT"),
        "default" => true,
        "editable" => false
    ],
];

if ($arResult["POSITIONS"]["ITEMS"]) {
    foreach ($arResult["POSITIONS"]["ITEMS"] as $id => $position) {
        $arResult["GRID_POSITION"][$id]["data"]["ID"] = $id;
        $arResult["GRID_POSITION"][$id]["data"]["NAME"] = $position["FIELDS"]["NAME"];
        if ($position["PRODUCT"]) {
            $arResult["GRID_POSITION"][$id]["data"]["PRODUCT"] = $position["PRODUCT"]["NAME"];
        }

        foreach ($position["PROPERTIES"] as $code => $prop) {
            if ($code == $propertyBindPropuct) {
                continue;
            }
            $header[] = [
                'id' => "PROPERTY_" . $code,
                'name' => $prop["NAME"],
                "default" => true,
                "editable" => false
            ];
            $arResult["GRID_POSITION"][$id]["data"]["PROPERTY_" . $code] = $prop["VALUE"];
        }

    }
}

$arResult["HEADERS"] = $header;

if(Loader::includeModule('support'))
{
    $res = CIBlockElement::GetProperty($arParams["COMPLAINTS_OPTIONS"]["IBLOCK_COMPLAINTS_ID"], $arResult["ID"], "sort", "asc", array("CODE" => "TICKET_ID"));
    if ($ob = $res->Fetch())
    {
        $arResult['TICKET_ID'] = $ob['VALUE'];
    }
}