<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arTypeIdentifierProduct = array('ID' =>  Loc::getMessage('INDENTIFIER_ID_NAME'), 'ART' => Loc::getMessage('INDENTIFIER_ART_NAME'));

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arComponentParameters = [
    "PARAMETERS" => [
        "MULTIPLE" => Array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_IMPORT_MULTIPLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ),
        "MAX_FILE_SIZE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::GetMessage("B2B_EXCEL_IMPORT_MAX_FILE_SIZE"),
            "TYPE" => "STRING",
        ),
        "IBLOCK_TYPE" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => Loc::GetMessage("B2B_EXCEL_IMPORT_LIST_TYPE_BLOCK"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
			"REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("B2B_EXCEL_IMPORT_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"REFRESH" => "Y",
		),
        "TYPE_IDENTIFIER_PRODUCT" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("B2B_EXCEL_IMPORT_TYPE_IDENTIFIER_PRODUCT"),
			"TYPE" => "LIST",
			"VALUES" => $arTypeIdentifierProduct,
			"DEFAULT" => 'ID',
			"REFRESH" => "Y",
		)
    ]
];

if ($arCurrentValues["TYPE_IDENTIFIER_PRODUCT"] == "ART") {
    $arComponentParameters["PARAMETERS"]["PROPERTY_ARTICUL"] = array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("B2B_EXCEL_IMPORT_PROPERTY_ARTICUL"),
        "TYPE" => "LIST",
        "VALUES" => $arProperty_LNS,
        "REFRESH" => "Y",
    );
}