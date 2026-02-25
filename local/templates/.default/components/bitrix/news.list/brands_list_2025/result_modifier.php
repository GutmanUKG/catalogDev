<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Ensure $arResult is an array and not empty
if (is_array($arResult['ITEMS']) && !empty($arResult['ITEMS'])) {
    // Filter the items where IS_BRAND property is 'Y'
    $arResult['ITEMS'] = array_filter($arResult['ITEMS'], function($item) {
        return isset($item['PROPERTIES']['IS_BRAND']['VALUE']) && $item['PROPERTIES']['IS_BRAND']['VALUE'] === 'Y';
    });
}
if(LANGUAGE_ID == 'kz'){
    foreach ($arResult['ITEMS'] as &$arItem) {
        if (isset($arItem['PROPERTIES']['DESCR_KZ']['VALUE']['TEXT'])) {
            $arItem['PREVIEW_TEXT'] = $arItem['PROPERTIES']['DESCR_KZ']['VALUE']['TEXT'];
        }
    }
    unset($arItem); // Unset reference after the loop
}

if(LANGUAGE_ID == 'en'){
    foreach ($arResult['ITEMS'] as &$arItem) {
        if (isset($arItem['PROPERTIES']['DESCR_EN']['VALUE']['TEXT'])) {
            $arItem['PREVIEW_TEXT'] = $arItem['PROPERTIES']['DESCR_EN']['VALUE']['TEXT'];
        }
    }
    unset($arItem); // Unset reference after the loop
}

// Return the modified $arResult
return $arResult;
?>
