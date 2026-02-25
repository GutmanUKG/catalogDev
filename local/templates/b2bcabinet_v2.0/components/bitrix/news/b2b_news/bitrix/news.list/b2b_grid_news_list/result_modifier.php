<?php
CModule::IncludeModule("iblock");

if ($arResult['ITEMS']) {
    $WIDTH_PREVIEW_PICTURE = 323;
    $HEIGHT_PREVIEW_PICTURE = 270;

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

        if (!empty($arItem["PREVIEW_PICTURE"])) {
            $arItem["RESIZE_PICTURE"] = CFile::ResizeImageGet(
                $arItem["PREVIEW_PICTURE"], array (
                'width' => $WIDTH_PREVIEW_PICTURE,
                'height' => $HEIGHT_PREVIEW_PICTURE
            ), BX_RESIZE_IMAGE_EXACT , true);
        } elseif (!empty($arItem["DETAIL_PICTURE"])) {
            $arItem["RESIZE_PICTURE"] = CFile::ResizeImageGet(
                $arItem["DETAIL_PICTURE"], array (
                'width' => $WIDTH_PREVIEW_PICTURE,
                'height' => $HEIGHT_PREVIEW_PICTURE
            ), BX_RESIZE_IMAGE_EXACT , true);
        } else {
            $arItem["RESIZE_PICTURE"]['src'] = $this->GetFolder().'/image/no_image.jpg';
            $arItem["RESIZE_PICTURE"]['height'] = $WIDTH_PREVIEW_PICTURE;
            $arItem["RESIZE_PICTURE"]['width'] = $HEIGHT_PREVIEW_PICTURE;
        }
    }
}