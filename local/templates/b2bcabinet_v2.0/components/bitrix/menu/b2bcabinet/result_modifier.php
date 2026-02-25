<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option;

if (is_object($USER) && $USER->IsAuthorized()) {
    $arResult['PERSONAL_MANAGER_ID'] = CUser::GetByID($USER->GetID())->fetch()['UF_P_MANAGER_ID'];
}

$methodInstall = Option::get("sotbit.b2bcabinet", "method_install", "", SITE_ID);
$templateDir = $methodInstall == 'AS_TEMPLATE' ? "/b2bcabinet/" : SITE_DIR;

//determine if child selected
$bWasSelected = false;
$issetCatalog = false;
$arParents = array();
$depth = 1;
foreach ($arResult as $i => $arMenu) {
    if (is_array($arMenu)) {
        $depth = $arMenu['DEPTH_LEVEL'];

        if ($arMenu['IS_PARENT'] == true || $arMenu['PARAM']['IS_PARENT'] == true) {
            $arParents[$arMenu['DEPTH_LEVEL'] - 1] = $i;
        } elseif ($arMenu['SELECTED'] == true) {
            $bWasSelected = true;
            break;
        }
    }
}

if ($bWasSelected) {
    for ($i = 0; $i < $depth - 1; $i++) {
        $arResult[$arParents[$i]]['CHILD_SELECTED'] = true;
    }
}

if (Loader::includeModule('iblock')) {
	global $APPLICATION;

	$iblockId = Option::get('sotbit.b2bcabinet', 'CATALOG_IBLOCK_ID', '', SITE_ID);

	if(defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$iblockId);

	$catalogMenuList = $APPLICATION->IncludeComponent("sotbit:b2bcabinet.menu.sections", "", array(
		"IS_SEF" => "N",
		"IBLOCK_ID" => $iblockId,
		"DEPTH_LEVEL" => $arParams["MAX_LEVEL"],
		"CACHE_TYPE" => "A",
	), false, Array('HIDE_ICONS' => 'Y'));

	if ($catalogMenuList) {

        $useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
        $replaceValue = null;
        if ($useReplace) {
            $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
            $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
            foreach ($arResult as $key => $val) {
                if (!empty($arResult[$key]['LINK']))
                    $arResult[$key]['LINK'] = str_replace($replaceableValue, $replaceValue, $arResult[$key]['LINK']);
            }

            foreach ($catalogMenuList as $key => $val) {
                if (!empty($catalogMenuList[$key][1]))
                    $catalogMenuList[$key][1] = str_replace($replaceableValue, $replaceValue, $val[1]);
            }
            $sectionPathList = array_column($catalogMenuList, 'SECTION_ID', "1");
            $searchPageWithTag = str_replace($replaceableValue, $replaceValue, $APPLICATION->GetCurPage());

            $arResult["CATALOG_MENU"] = array_combine($sectionPathList, $catalogMenuList);

            foreach ($arResult["CATALOG_MENU"] as $key => $val) {
                if (!empty($arResult["CATALOG_MENU"][$key][1]))
                    $arResult["CATALOG_MENU"][$key][1] = str_replace($replaceableValue, $replaceValue, $arResult["CATALOG_MENU"][$key][1]);
            }
        } else {
            $sectionPathList = array_column($catalogMenuList, 'SECTION_ID', "1");
            $arResult["CATALOG_MENU"] = array_combine($sectionPathList, $catalogMenuList);
        }


        $activeSection = null;

        if ($searchPageWithTag && isset($sectionPathList[$searchPageWithTag])) {
            $activeSection = $sectionPathList[$searchPageWithTag];
        } else if (isset($sectionPathList[$APPLICATION->GetCurPage()])) {
            $activeSection = $sectionPathList[$APPLICATION->GetCurPage()];
        }

        if ($activeSection) {
            $arResult["OPEN_CATALOG"] = "Y";
            $arOpenParentList = CIBlockSection::GetNavChain(false, $activeSection, ['ID', 'NAME'], true);
            foreach ($arOpenParentList as $openSections) {
                $arResult["CATALOG_MENU"][$openSections["ID"]]["PARAMS"]["OPEN"] = "Y";
            }
        }
    }
}


?>