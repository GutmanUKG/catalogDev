<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Iblock,
    Bitrix\Catalog;

if (!Loader::includeModule('iblock')) {
    return;
}
global $APPLICATION;

$catalogIncluded = Loader::includeModule('catalog');

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$offersIblock = array();
if ($catalogIncluded) {
    $iterator = Catalog\CatalogIblockTable::getList(array(
        'select' => array('IBLOCK_ID'),
        'filter' => array('!=PRODUCT_IBLOCK_ID' => 0)
    ));
    while ($row = $iterator->fetch()) {
        $offersIblock[$row['IBLOCK_ID']] = true;
    }
    unset($row, $iterator);
}

$arIBlock = array();
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $id = (int)$arr['ID'];
    if (isset($offersIblock[$id])) {
        continue;
    }
    $arIBlock[$id] = '[' . $id . '] ' . $arr['NAME'];
}
unset($id, $arr, $rsIBlock, $iblockFilter);
unset($offersIblock);

$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_F = array();
if ($iblockExists) {
    $propertyIterator = Iblock\PropertyTable::getList(array(
        'select' => array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'CODE',
            'PROPERTY_TYPE',
            'MULTIPLE',
            'LINK_IBLOCK_ID',
            'USER_TYPE',
            'SORT'
        ),
        'filter' => array('=IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], '=ACTIVE' => 'Y'),
        'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
    ));
    while ($property = $propertyIterator->fetch()) {
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '') {
            $propertyCode = $property['ID'];
        }
        $propertyName = '[' . $propertyCode . '] ' . $property['NAME'];

        if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE) {
            $arProperty[$propertyCode] = $propertyName;

            if ($property['MULTIPLE'] == 'Y') {
                $arProperty_X[$propertyCode] = $propertyName;
            } elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
                $arProperty_X[$propertyCode] = $propertyName;
            } elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0) {
                $arProperty_X[$propertyCode] = $propertyName;
            }
        } else {
            if ($property['MULTIPLE'] == 'N') {
                $arProperty_F[$propertyCode] = $propertyName;
            }
        }

        if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER) {
            $arProperty_N[$propertyCode] = $propertyName;
        }
    }
    unset($propertyCode, $propertyName, $property, $propertyIterator);
}

$arProperty_LNS = $arProperty;

$arIBlock_LINK = array();
$iblockFilter = (
!empty($arCurrentValues['LINK_IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['LINK_IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y')
);
$rsIblock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIblock->Fetch()) {
    $arIBlock_LINK[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}
unset($iblockFilter);

$arProperty_LINK = array();
if (!empty($arCurrentValues['LINK_IBLOCK_ID']) && (int)$arCurrentValues['LINK_IBLOCK_ID'] > 0) {
    $propertyIterator = Iblock\PropertyTable::getList(array(
        'select' => array(
            'ID',
            'IBLOCK_ID',
            'NAME',
            'CODE',
            'PROPERTY_TYPE',
            'MULTIPLE',
            'LINK_IBLOCK_ID',
            'USER_TYPE',
            'SORT'
        ),
        'filter' => array(
            '=IBLOCK_ID' => $arCurrentValues['LINK_IBLOCK_ID'],
            '=PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_ELEMENT,
            '=ACTIVE' => 'Y'
        ),
        'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
    ));
    while ($property = $propertyIterator->fetch()) {
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '') {
            $propertyCode = $property['ID'];
        }
        $arProperty_LINK[$propertyCode] = '[' . $propertyCode . '] ' . $property['NAME'];
    }
    unset($propertyCode, $property, $propertyIterator);
}

$arUserFields_S = array("-" => " ");
$arUserFields_F = array("-" => " ");
if ($iblockExists) {
    global $USER_FIELD_MANAGER;
    $arUserFields = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_' . $arCurrentValues['IBLOCK_ID'] . '_SECTION', 0,
        LANGUAGE_ID);
    foreach ($arUserFields as $FIELD_NAME => $arUserField) {
        $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
        $arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '[' . $FIELD_NAME . ']' . $arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
        if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "string") {
            $arUserFields_S[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
        }
        if ($arUserField["USER_TYPE"]["BASE_TYPE"] == "file" && $arUserField['MULTIPLE'] == 'N') {
            $arUserFields_F[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
        }
    }
    unset($arUserFields);
}


$offers = false;
$arProperty_Offers = array();
$arProperty_OffersWithoutFile = array();
if ($catalogIncluded && $iblockExists) {
    $offers = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
    if (!empty($offers)) {
        $propertyIterator = Iblock\PropertyTable::getList(array(
            'select' => array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
                'PROPERTY_TYPE',
                'MULTIPLE',
                'LINK_IBLOCK_ID',
                'USER_TYPE',
                'SORT'
            ),
            'filter' => array(
                '=IBLOCK_ID' => $offers['IBLOCK_ID'],
                '=ACTIVE' => 'Y',
                '!=ID' => $offers['SKU_PROPERTY_ID']
            ),
            'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
        ));
        while ($property = $propertyIterator->fetch()) {
            $propertyCode = (string)$property['CODE'];
            if ($propertyCode == '') {
                $propertyCode = $property['ID'];
            }
            $propertyName = '[' . $propertyCode . '] ' . $property['NAME'];

            $arProperty_Offers[$propertyCode] = $propertyName;
            if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE) {
                $arProperty_OffersWithoutFile[$propertyCode] = $propertyName;
            }
        }
        unset($propertyCode, $propertyName, $property, $propertyIterator);
    }
}

$arSort = CIBlockParameters::GetElementSortFields(
    array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
    array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if ($catalogIncluded) {
    $arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
    if (isset($arSort['CATALOG_AVAILABLE'])) {
        unset($arSort['CATALOG_AVAILABLE']);
    }
    $arPrice = CCatalogIBlockParameters::getPriceTypesList();
} else {
    $arPrice = $arProperty_N;
}

$arSortFields = Array(
    "ID"=>GetMessage("T_IBLOCK_DESC_FID"),
    "NAME"=>GetMessage("T_IBLOCK_DESC_FNAME"),
    "ACTIVE_FROM"=>GetMessage("T_IBLOCK_DESC_FACT"),
    "SORT"=>GetMessage("T_IBLOCK_DESC_FSORT"),
    "TIMESTAMP_X"=>GetMessage("T_IBLOCK_DESC_FTSAMP")
);

$arSorts = array(
    "asc" => Loc::GetMessage("IBLOCK_SORT_ASC"),
    "desc" => Loc::GetMessage("IBLOCK_SORT_DESC"),
);

$arComponentParameters = [
    "GROUPS" => [
        "SHOW_FIELDS_SETTINGS" => array(
            "NAME" => GetMessage("B2B_EXCEL_EXPORT_GROUP_SHOW_FIELDS_SETTINGS"),
        ),
    ],
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "SORT_BY" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_SORT_BY"),
            "TYPE" => "LIST",
            "DEFAULT" => "NAME",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("B2B_EXCEL_EXPORT_SORT_ORDER"),
            "TYPE" => "LIST",
            "DEFAULT" => "asc",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "ONLY_AVAILABLE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_ONLY_AVAILABLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ONLY_ACTIVE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_ONLY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "FILTER_NAME" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_FILTER_NAME"),
            "TYPE" => "STRING",
        ),
        "PRICE_CODE" => array(
            "PARENT" => "SHOW_FIELDS_SETTINGS",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arPrice,
        ),
        "HEADERS_COLUMN" => CIBlockParameters::GetFieldCode(Loc::getMessage("B2B_EXCEL_EXPORT_HEADERS_COLUMN"),
            "SHOW_FIELDS_SETTINGS"),
        "PROPERTY_CODE" => array(
            "PARENT" => "SHOW_FIELDS_SETTINGS",
            "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arProperty_LNS,
        ),

    )
];

if (!empty($offers)) {
    $arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"] = array(
        "PARENT" => "SHOW_FIELDS_SETTINGS",
        "NAME" => Loc::GetMessage("B2B_EXCEL_EXPORT_OFFERS_PROPERTY_CODE"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arProperty_Offers,
        "ADDITIONAL_VALUES" => "Y",
    );
}