<?php
if (!CModule::IncludeModule("iblock"))
    return;

$dbProperty = CIBlockElement::getProperty(
    $arResult["IBLOCK_ID"],
    $arResult["ID"],
    array("sort" => "asc"),
    array("CODE" => $arParams["GALERY_SOURCE"])
);

while ($arProperty = $dbProperty->Fetch()) {
    if ($arProperty["VALUE"]) {
        $arResult["GALERY_PROPERTY"][] = CFile::GetFileArray($arProperty["VALUE"]);
    }
}
if ($arParams["DISPLAY_DATE"] != "N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
    $arResult['FIELDS']['DISPLAY_ACTIVE_FROM'] = $arResult["DISPLAY_ACTIVE_FROM"];
}