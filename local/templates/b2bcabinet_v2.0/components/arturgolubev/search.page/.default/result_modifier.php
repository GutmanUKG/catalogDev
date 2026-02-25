<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

use \Arturgolubev\Smartsearch\Encoding;
use \Arturgolubev\Smartsearch\SearchComponent;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$PREVIEW_WIDTH = intval($arParams["PREVIEW_WIDTH"]);
if ($PREVIEW_WIDTH <= 0)
	$PREVIEW_WIDTH = 120;

$PREVIEW_HEIGHT = intval($arParams["PREVIEW_HEIGHT"]);
if ($PREVIEW_HEIGHT <= 0)
	$PREVIEW_HEIGHT = 100;

$textLengh = 500;

foreach($arResult["SEARCH"] as $arItem){
	if($arItem["MODULE_ID"] == "iblock" && substr($arItem["ITEM_ID"], 0, 1) !== "S"){
		$arResult["ELEMENTS"][$arItem["ITEM_ID"]] = $arItem["ITEM_ID"];
	}elseif($arItem["MODULE_ID"] == "iblock" && substr($arItem["ITEM_ID"], 0, 1) == "S"){
		$arResult["SECTIONS"][$arItem["ITEM_ID"]] = str_replace('S', '', $arItem["ITEM_ID"]);
	}
}

if (!empty($arResult["ELEMENTS"]) && Loader::includeModule("iblock"))
{
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
	);
	
	if($arParams['PREVIEW_TEXT']){
		$arSelect[] = $arParams['PREVIEW_TEXT'];
	}
	
	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"MIN_PERMISSION" => "R",
	);
	$arFilter["=ID"] = $arResult["ELEMENTS"];
	$arResult["ELEMENTS"] = array();
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($arElement = $rsElements->Fetch())
	{
		$arElement["PROPS"] = array();
		if(!empty($arParams["SHOW_PROPS"])){
			foreach($arParams["SHOW_PROPS"] as $prop){
				$prop = IntVal(trim($prop));
				if(!$prop) continue;
				
				$tmp = array();
				$vals = array();
				
				$db_props = CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"], array("sort" => "asc"), Array("ID"=>$prop));
				while($ar_props = $db_props->Fetch())
				{
					$tmp = $ar_props;
					
					if($ar_props["VALUE"])
						$vals[] = $ar_props["VALUE"];
				}
				$tmp["VALUE"] = $vals;
				
				$arElement["PROPS"][] = $tmp;
			}
		}
		
		$arResult["ELEMENTS"][$arElement["ID"]] = $arElement;
	}
}


if (!empty($arResult["SECTIONS"]) && Loader::includeModule("iblock"))
{
	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"MIN_PERMISSION" => "R",
	);
	$arFilter["=ID"] = $arResult["SECTIONS"];

	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"PICTURE",
	);
	
	if($arParams['PREVIEW_TEXT']){
		$arSelect[] = "DESCRIPTION";
	}

	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, $arSelect);
	while($ar_result = $db_list->GetNext()){
		$arResult["SECTIONS"]["S".$ar_result["ID"]] = $ar_result;
	}
}

foreach($arResult["SEARCH"] as $i=>&$arItem)
{
	if($arResult["sphrase_id"])
	{
		$arItem["URL"] = $arItem["URL"] . (strstr($arItem["URL"], '?') === false ? '?' : '&') . 'sphrase_id='.$arResult["sphrase_id"];
	}
	
	if($arItem["MODULE_ID"] == "iblock" && !empty($arResult["ELEMENTS"][$arItem["ITEM_ID"]]))
	{
		$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
		
		if ($arElement["PREVIEW_PICTURE"] > 0)
			$arItem["PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		elseif ($arElement["DETAIL_PICTURE"] > 0)
			$arItem["PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		/* else{			
			// get picture from property file
			
			$propertyPictureCode = 'MORE_PHOTO';
			$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arItem["PARAM2"], "ID"=>$arItem["ITEM_ID"]), false, array("nPageSize"=>1), array("ID", "PROPERTY_".$propertyPictureCode));
			while($arFields = $res->Fetch()){
				if($arFields['PROPERTY_'.$propertyPictureCode.'_VALUE']){
					$arItem["PICTURE"] = CFile::ResizeImageGet($arFields['PROPERTY_'.$propertyPictureCode.'_VALUE'], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}
			}
		} */
		
		if($arParams['PREVIEW_TEXT']){
			$arItem["BODY_FORMATED"] = SearchComponent::reformatDescription($arItem["BODY_FORMATED"], $arElement[$arParams['PREVIEW_TEXT']], $textLengh);
		}
	}
	elseif($arItem["MODULE_ID"] == "iblock" && !empty($arResult["SECTIONS"][$arItem["ITEM_ID"]]))
	{
		$arElement = $arResult["SECTIONS"][$arItem["ITEM_ID"]];
		
		if ($arElement["PICTURE"] > 0)
			$arItem["PICTURE"] = CFile::ResizeImageGet($arElement["PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		
		if($arParams['PREVIEW_TEXT']){
			$arItem["BODY_FORMATED"] = SearchComponent::reformatDescription($arItem["BODY_FORMATED"], $arElement['DESCRIPTION'], $textLengh);
		}
	}
}
unset($arItem);



$arResult["SEARCH_HISTORY"] = array();
if($arParams["SHOW_HISTORY"] == 'Y'){
	$arResult["SEARCH_HISTORY"] = \Arturgolubev\Smartsearch\Tools::getSearchHistory(10);
}