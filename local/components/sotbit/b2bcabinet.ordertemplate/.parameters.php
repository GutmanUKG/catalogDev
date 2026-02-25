<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
    Bitrix\Main\ModuleManager,
    Bitrix\Iblock,
    Bitrix\Catalog,
    Bitrix\Currency;

if (!Loader::includeModule('iblock'))
    return;
$catalogIncluded = Loader::includeModule('catalog');

$usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();


$offersIblock = array();
if ($catalogIncluded)
{
    $iterator = Catalog\CatalogIblockTable::getList(array(
        'select' => array('IBLOCK_ID'),
        'filter' => array('!=PRODUCT_IBLOCK_ID' => 0)
    ));
    while ($row = $iterator->fetch())
        $offersIblock[$row['IBLOCK_ID']] = true;
    unset($row, $iterator);
}

$arIBlock = array();
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
{
    $id = (int)$arr['ID'];
    if (isset($offersIblock[$id]))
        continue;
    $arIBlock[$id] = '['.$id.'] '.$arr['NAME'];
}


$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_F = array();
if ($iblockExists)
{
    $propertyIterator = Iblock\PropertyTable::getList(array(
        'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'SORT'),
        'filter' => array('=IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], '=ACTIVE' => 'Y'),
        'order' => array('SORT' => 'ASC', 'NAME' => 'ASC')
    ));
    while ($property = $propertyIterator->fetch())
    {
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '')
            $propertyCode = $property['ID'];
        $propertyName = '['.$propertyCode.'] '.$property['NAME'];

        if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
        {
            $arProperty[$propertyCode] = $propertyName;

            if ($property['MULTIPLE'] == 'Y')
                $arProperty_X[$propertyCode] = $propertyName;
            elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
                $arProperty_X[$propertyCode] = $propertyName;
            elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0)
                $arProperty_X[$propertyCode] = $propertyName;
        }
        else
        {
            if ($property['MULTIPLE'] == 'N')
                $arProperty_F[$propertyCode] = $propertyName;
        }

        if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER)
            $arProperty_N[$propertyCode] = $propertyName;
    }
    unset($propertyCode, $propertyName, $property, $propertyIterator);
}
$arProperty_LNS = $arProperty;

$priceCode = [];
$dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"), array(), false, false, ["ID", "NAME"]
);
while ($arPriceType = $dbPriceType->Fetch())
{
    $priceCode[$arPriceType["NAME"]] = "[".$arPriceType["ID"]."] ".$arPriceType["NAME"];
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "LIST_PROPERTY_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("LIST_PROPERTY_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNS,
        ),
        "PRICE_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("PRICE_CODE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $priceCode,
        ),
        "SEF_MODE" => Array(
            "list" => Array(
                "NAME" => GetMessage("SPP_LIST_DESC"),
                "DEFAULT" => "ordertemplate_list.php",
                "VARIABLES" => array()
            ),
            "detail" => Array(
                "NAME" => GetMessage("SPP_DETAIL_DESC"),
                "DEFAULT" => "ordertemplate_detail.php?ID=#ID#",
                "VARIABLES" => array("ID")
            ),
        ),
        "PRODUCTS_DETAIL_PATH" => Array(
            "NAME" => GetMessage("PRODUCTS_DETAIL_PATH"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),
        "PER_PAGE" => Array(
            "NAME" => GetMessage("SPP_PER_PAGE"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "20",
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),

        "ADD_CHAIN" => Array(
            "NAME" => GetMessage("SPP_ADD_CHAIN"),
            "TYPE" => "CHECKBOX",
            "MULTIPLE" => "N",
            "DEFAULT" => "Y",
            "PARENT" => "ADDITIONAL_SETTINGS",
        ),

        "SET_TITLE" => Array(),
    )
);
?>
