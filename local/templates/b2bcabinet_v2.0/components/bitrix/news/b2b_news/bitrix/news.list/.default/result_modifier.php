<?php
CModule::IncludeModule("iblock");
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