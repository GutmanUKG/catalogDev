<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if ($arResult["ITEMS"]){
    foreach ($arResult["ITEMS"] as $key => $item){
        $arResult["ROWS"][$item["ID"]]["data"]["ID"] = $item["ID"];
        $arResult["ROWS"][$item["ID"]]["data"]["NAME"] = $item["NAME"];
        $arResult["ROWS"][$item["ID"]]["data"]["DATE_CREATE"] = $item["DATE_CREATE"]->toString();
        $arResult["ROWS"][$item["ID"]]["data"]["PRICE"] = $item["TOTAL_PRICE"];
        $arResult["ROWS"][$item["ID"]]["editable"] = true;
        $arResult["ROWS"][$item["ID"]]['actions'][0]["TEXT"] = GetMessage("B2BCABINET_DRAFT_LIST_ACTION_CREATE_ORDER");
        $arResult["ROWS"][$item["ID"]]['actions'][0]["ONCLICK"] = "showPopupCreateOrderDraft(".$item["ID"].", '".$item["NAME"]."')";
        $arResult["ROWS"][$item["ID"]]['actions'][0]["DEFAULT"] = "1";
        $arResult["ROWS"][$item["ID"]]['actions'][1]["TEXT"] = GetMessage("B2BCABINET_DRAFT_LIST_ACTION_CREATE_ORDERTEMPLATE");
        $arResult["ROWS"][$item["ID"]]['actions'][1]["ONCLICK"] = "showPopupCreateOrdertemplate(".$item["ID"].", '".$item["NAME"]."')";
        $arResult["ROWS"][$item["ID"]]['actions'][1]["DEFAULT"] = "1";
        $arResult["ROWS"][$item["ID"]]['actions'][2]["TEXT"] = GetMessage("B2BCABINET_DRAFT_LIST_ACTION_REMOVE");
        $arResult["ROWS"][$item["ID"]]['actions'][2]["ONCLICK"] = "showPopupRemoveDraft(".$item["ID"].", '".$item["NAME"]."')";
        $arResult["ROWS"][$item["ID"]]['actions'][2]["DEFAULT"] = "1";
    }
}
?>