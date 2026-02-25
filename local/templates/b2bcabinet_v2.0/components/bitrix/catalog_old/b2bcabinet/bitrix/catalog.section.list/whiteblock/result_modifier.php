<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


if ($arResult['SECTIONS_COUNT'] > 0)
{
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

use Bitrix\Iblock\SectionTable;

foreach ($arResult['SECTIONS'] as &$section) {
    $subSections = [];

    $res = CIBlockSection::GetList(
        ['SORT' => 'ASC'],
        [
            'IBLOCK_ID' => $section['IBLOCK_ID'],
            'SECTION_ID' => $section['ID'],
            'ACTIVE' => 'Y'
        ],
        false,
        ['ID', 'NAME', 'CODE', 'SECTION_PAGE_URL', 'PICTURE']
    );

    while ($sub = $res->GetNext()) {
        // Получение SRC картинки, если нужно
        if ($sub['PICTURE']) {
            $sub['PICTURE_SRC'] = CFile::GetPath($sub['PICTURE']);
        }

        $subSections[] = $sub;
    }

    $section['SUBSECTIONS'] = $subSections;
}
unset($section);
