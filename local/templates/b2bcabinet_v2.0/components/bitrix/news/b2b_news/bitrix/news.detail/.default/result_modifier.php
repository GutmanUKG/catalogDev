<?php
use Bitrix\Main\Page\Asset;

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

if( !empty($arResult['GALERY_PROPERTY']) && is_array($arResult['GALERY_PROPERTY'])) {
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/lightbox/lightbox.min.js');

    foreach($arResult['GALERY_PROPERTY'] as &$image) {
        $image += [
            'SMALL_IMAGE' => CFile::ResizeImageGet(
                $image['ID'],
                array("width" => 585, "height" => 357),
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                true
            ),
            'BIG_IMAGE' => CFile::ResizeImageGet(
                $image['ID'],
                array("width" => 1200, "height" => 768),
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                true
            ),
        ];  
    }
    unset($obFiles, $file, $uploadDir);
}

if ($arParams["DISPLAY_DATE"] != "N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
    array_unshift($arResult['FIELDS'],$arResult["DISPLAY_ACTIVE_FROM"]);
}