<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}


function isChildChecked($arSection)
{
    if ($arSection["CHILDS"]) {
        foreach ($arSection["CHILDS"] as $arChild) {
            if ($arChild["CHECKED"]) {
                return true;
            }
        }
    }

    return false;
}

function fillArrayRecursive(&$array, &$arResult) {
    foreach ($array as $key => &$item) {
        if (isset($item['CHILDS'])) {
            $childs = $item['CHILDS'];
            $array[$item['ID']] = array_merge($arResult['ITEMS']['SECTION_ID']['VALUES'][$item['ID']], array('CHILDS' => $childs));
            fillArrayRecursive($item['CHILDS'], $arResult);
        } else {
            $array[$item['ID']] = $arResult['ITEMS']['SECTION_ID']['VALUES'][$item['ID']];
        }
    }
}

if(
    (!empty($arParams['ARR_SECTIONS']) && is_array($arParams['ARR_SECTIONS'])) &&
    (!empty($arResult['ITEMS']['SECTION_ID']['VALUES']) && is_array($arResult['ITEMS']['SECTION_ID']['VALUES']))
)
{
    $sectionKeys = array_keys($arResult['ITEMS']['SECTION_ID']['VALUES']);

    $max = 0;
    foreach ($arParams['ARR_SECTIONS'] as $item) {
        if ((int)$item['DEPTH_LEVEL'] > $max)
            $max = $item['DEPTH_LEVEL'];
    }


    $sections = $arParams['ARR_SECTIONS'];
    for ($i = $max; $i >= 1; $i--) {
        foreach ($sections as $key => &$item) {
            if ((int)$item['DEPTH_LEVEL'] == $i) {
                if ($i != 1) {
                    $sections[$item['IBLOCK_SECTION_ID']]['CHILDS'][$item['ID']] = $item;
                    unset($sections[$key]);
                }
            }

        }
    }

    fillArrayRecursive($sections, $arResult);
    $arResult['ITEMS']['SECTION_ID']['FILTRED_FIELDS'] = $sections;

}


global $sotbitFilterResult;
$sotbitFilterResult = $arResult;


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

foreach ($arResult["ITEMS"] as &$arItem) {
    if (!empty($arItem["VALUES"]) && is_array($arItem["VALUES"])) {
        foreach ($arItem["VALUES"] as &$arValue) {
            if (isset($arValue["VALUE"])) {
              
                $arValue["VALUE"] = html_entity_decode($arValue["VALUE"], ENT_QUOTES | ENT_HTML5, SITE_CHARSET);
            }
            if (isset($arValue["HTML_VALUE"])) {
                $arValue["HTML_VALUE"] = html_entity_decode($arValue["HTML_VALUE"], ENT_QUOTES | ENT_HTML5, SITE_CHARSET);
            }
        }
        unset($arValue);
    }
}
unset($arItem);

