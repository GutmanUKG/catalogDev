<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($arResult['SECTIONS_COUNT'] > 0)
{
    $createSectionDetailUrl = function ($sectionId) use ($arParams) {
        return $arParams["USE_FILTER_SECTIONS"] === "Y" ?
            '?arrFilter_SECTION_ID_' . abs(crc32($sectionId)) . '=Y&set_filter=%CF%F0%E8%EC%E5%ED%E8%F2%FC' :
            '?SECTION_ID=' . $sectionId;
    };


    foreach ($arResult['SECTIONS'] as $key => $arSection) {
        $arMap[$arSection['ID']] = $key;
    }
    unset($key, $arSection);
    $rsSections = CIBlockSection::GetList(array(), array('ID' => array_keys($arMap)), false, $arSelect);
    while ($arSection = $rsSections->Fetch()) {
        if (!isset($arMap[$arSection['ID']]))
            continue;
        $key = $arMap[$arSection['ID']];
        $pictureId = (int)$arSection['PICTURE'];
        $arResult['SECTIONS'][$key]['PICTURE'] = ($pictureId > 0 ? CFile::GetFileArray($pictureId) : false);
        $arResult['SECTIONS'][$key]['~PICTURE'] = $arSection['PICTURE'];
    }

    unset($pictureId, $key, $arSection, $rsSections);
	unset($arMap, $arSelect);
}

