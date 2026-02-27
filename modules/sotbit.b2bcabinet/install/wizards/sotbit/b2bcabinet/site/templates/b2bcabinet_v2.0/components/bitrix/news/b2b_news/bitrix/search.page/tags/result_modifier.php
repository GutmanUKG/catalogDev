<?php
CModule::IncludeModule("iblock");
$arSelect = $arParams["DISPLAY_FIELDS"];
$arSelect[] = "PREVIEW_PICTURE";
$arSelect[] = "ID";

$WIDTH_PREVIEW_PICTURE = 323;
$HEIGHT_PREVIEW_PICTURE = 270;

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
            $arItem["RESIZE_PICTURE"] = CFile::ResizeImageGet(
                $arItem["DISPLAY_FIELDS"]["PREVIEW_PICTURE"], array (
                'width' => $WIDTH_PREVIEW_PICTURE,
                'height' => $HEIGHT_PREVIEW_PICTURE
            ), BX_RESIZE_IMAGE_EXACT , true);
            unset($arItem["DISPLAY_FIELDS"]["PREVIEW_PICTURE"]);
        } else {
            $arItem["RESIZE_PICTURE"]['src'] = $this->GetFolder().'/image/no_image.jpg';
            $arItem["RESIZE_PICTURE"]['height'] = $WIDTH_PREVIEW_PICTURE;
            $arItem["RESIZE_PICTURE"]['width'] = $HEIGHT_PREVIEW_PICTURE;
        }

        if($arItem["DISPLAY_FIELDS"]["TAGS"]&&$arItem["DISPLAY_FIELDS"]["TAGS"]!=''){
            $arItem["TAGS"]=explode(",", $arItem['DISPLAY_FIELDS']['TAGS']);
        }

        unset($arItem['DISPLAY_FIELDS']['TAGS']);
        unset($arItem["DISPLAY_FIELDS"]["ID"]);
    }
}
