<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arDetParams = array(
	"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
	"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
	"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
	"PATH_TO_CATALOG" => $arParams["PATH_TO_CATALOG"],
	"SET_TITLE" =>$arParams["SET_TITLE"],
	"ID" => $arResult["VARIABLES"]["ID"],
	"ACTIVE_DATE_FORMAT" => $arParams["ACTIVE_DATE_FORMAT"],
	"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	"CUSTOM_SELECT_PROPS" => $arParams["CUSTOM_SELECT_PROPS"],
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"PICTURE_WIDTH" => $arParams["PICTURE_WIDTH"],
	"PICTURE_HEIGHT" => $arParams["PICTURE_HEIGHT"],
	"RESTRICT_CHANGE_PAYSYSTEM" => $arParams["RESTRICT_CHANGE_PAYSYSTEM"],
	"DISALLOW_CANCEL" => $arParams["DISALLOW_CANCEL"],
);
foreach($arParams as $key => $val)
{
	if(strpos($key, "PROP_") !== false)
		$arDetParams[$key] = $val;
}

$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.detail",
	"b2bcabinet",
	$arDetParams,
	$component
);
?>
