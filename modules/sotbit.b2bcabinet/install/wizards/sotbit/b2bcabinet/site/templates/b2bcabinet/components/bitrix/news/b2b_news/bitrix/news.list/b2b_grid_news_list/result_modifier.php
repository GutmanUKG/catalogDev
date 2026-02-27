<?php
CModule::IncludeModule("iblock");

if ($arResult['ITEMS']) {
    $arSelect = $arParams["FIELDS"];
    $arSelect[] = "ID";


    foreach ($arResult['ITEMS'] as $arItem) {
        $arItemId[] = $arItem['ITEM_ID'];
    }

    $obItem = CIBlockElement::GetList(array(), array("ID" => $arItemId), false, false, $arSelect);
    while ($ob = $obItem->Fetch()) {
        $arElementsFields[$ob["ID"]] = $ob;
    }

    foreach ($arResult['ITEMS'] as &$arItem) {

        if ($arItem['FIELDS']['TAGS'] && $arItem['FIELDS']['TAGS'] != ' ') {
            $arItem["TAGS"] = explode(",", $arItem['FIELDS']['TAGS']);

        }
        unset($arItem['FIELDS']['TAGS']);

        if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]) {
            $arItem['FIELDS']['DATE_ACTIVE_FROM'] = $arItem["DISPLAY_ACTIVE_FROM"];
        }
    }

    $chunkArray = array_chunk($arResult["ITEMS"], ceil(count($arResult["ITEMS"])/3));

    if (count($chunkArray) == 2 && count($arResult["ITEMS"]) >= 4) {
        $chunkArray[][] = $chunkArray[1][1];
        unset($chunkArray[1][1]);
    }
    $arResult["ITEMS"] = $chunkArray;
}