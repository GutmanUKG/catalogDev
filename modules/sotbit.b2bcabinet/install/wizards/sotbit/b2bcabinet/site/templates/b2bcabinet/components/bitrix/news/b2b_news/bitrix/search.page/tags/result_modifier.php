<?php
CModule::IncludeModule("iblock");
$arSelect = $arParams["DISPLAY_FIELDS"];
$arSelect[] = "PREVIEW_PICTURE";
$arSelect[] = "ID";

foreach ($arResult['SEARCH'] as $arItem) {
    $arItemId[] = $arItem['ITEM_ID'];
}
$obItem = CIBlockElement::GetList(array(), array("ID" => $arItemId), false, false, $arSelect);
while ($ob = $obItem->Fetch()) {
    $arElementsFields[$ob["ID"]] = $ob;
}

foreach ($arResult['SEARCH'] as &$arItem) {
    if ($arElementsFields[$arItem["ITEM_ID"]]) {

        $arItem["DISPLAY_FIELDS"] = $arElementsFields[$arItem["ITEM_ID"]];

        if($arParams['ACTIVE_DATE_FORMAT']) {
            $arItem["DISPLAY_FIELDS"]["DATE_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem['DATE_FROM'], CSite::GetDateFormat()));
        }

        if ($arItem["DISPLAY_FIELDS"]["PREVIEW_PICTURE"]) {
            $arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["DISPLAY_FIELDS"]["PREVIEW_PICTURE"]);
            unset($arItem["DISPLAY_FIELDS"]["PREVIEW_PICTURE"]);
        }

        if($arItem["DISPLAY_FIELDS"]["TAGS"]&&$arItem["DISPLAY_FIELDS"]["TAGS"]!=''){
            $arItem["TAGS"]=explode(",", $arItem['DISPLAY_FIELDS']['TAGS']);
        }

        unset($arItem['DISPLAY_FIELDS']['TAGS']);
        unset($arItem["DISPLAY_FIELDS"]["ID"]);
    }
}
