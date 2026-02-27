<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arResult["TABLE_HEADER"]["NAME"] = GetMessage("B2B_ORDERTEMPLATE_ADD_NAME");
$arResult["TABLE_HEADER"]["ID"] = GetMessage("B2B_ORDERTEMPLATE_ADD_ID");
$arResult["TABLE_HEADER"]["QUANTITY"] = GetMessage("B2B_ORDERTEMPLATE_ADD_QUANTITY");

if($arParams["IBLOCK_ID"]){
    $arResult["FILTER"]["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
}

if($arParams["LIST_PROPERTY_CODE"]){
    $arResult["FILTER"] = array_merge($arResult["FILTER"], $arParams["LIST_PROPERTY_CODE"]);
}
?>