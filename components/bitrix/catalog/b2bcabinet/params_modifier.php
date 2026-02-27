<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Sotbit\B2bCabinet\Helper\Config,
    Sotbit\Regions\Internals\OptionsTable;

if (empty($arParams["FILTER_NAME"])) {
    $arParams["FILTER_NAME"] = "arrFilter";
}

global $searchFilter, ${$arParams["FILTER_NAME"]};

$methodIstall = Config::getMethodInstall(SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . \SotbitB2bCabinet::PATH . '/' : SITE_DIR;

if (!empty($searchFilter) && $_REQUEST["q"]) {
    ${$arParams["FILTER_NAME"]} = $searchFilter;
}

if (!$GLOBALS["USER"]->IsAuthorized()) {
    $arParams["CATALOG_NOT_AVAILABLE"] = 'Y';
}

$modifyArPrice = function () use (&$arParams) {
    $context = \Bitrix\Main\Application::getInstance()->getContext();

    if ($arParams["CATALOG_NOT_AVAILABLE"] === 'Y') {
        $priceNotAuthUser = Option::get('sotbit.b2bcabinet', 'PRICE_FOR_NOT_AUTHORIZED_USER', [], SITE_ID);
        $arParams["PRICE_CODE"] = $arParams["~PRICE_CODE"] = [$priceNotAuthUser];
        return;
    }
    if (Loader::includeModule("sotbit.regions") && isset($_SESSION["SOTBIT_REGIONS"]) && isset($_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"])) {
        $valEnableRegions = OptionsTable::GetList(["select" => ["VALUE"], "filter" => ["SITE_ID" => $context->getsite(), "CODE" => "ENABLE"]])->Fetch();
        if ($valEnableRegions['VALUE'] == "Y") {
            $arParams["PRICE_CODE"] = $arParams["~PRICE_CODE"] = array_intersect ($_SESSION["SOTBIT_REGIONS"]["PRICE_CODE"], $arParams["PRICE_CODE"]);
        }
    }
};

$modifyArStores = function () use (&$arParams) {
    $context = \Bitrix\Main\Application::getInstance()->getContext();

    if ($arParams["CATALOG_NOT_AVAILABLE"] === 'Y') {
        $arParams["SHOW_MAX_QUANTITY"] = Option::get('sotbit.b2bcabinet', 'SHOW_MAX_QUANTITY_FOR_NOT_AUTHORIZED_USER', 'N', SITE_ID);
    }

    if (Loader::includeModule("sotbit.regions") && isset($_SESSION["SOTBIT_REGIONS"]) && isset($_SESSION["SOTBIT_REGIONS"]["STORE"])) {
        $valEnableRegions = OptionsTable::GetList(["select" => ["VALUE"], "filter" => ["SITE_ID" => $context->getsite(), "CODE" => "ENABLE"]])->Fetch();
        if ($valEnableRegions['VALUE'] == "Y") {
            $arParams["STORES"] = $arParams["~STORES"] = array_intersect ($_SESSION["SOTBIT_REGIONS"]["STORE"], $arParams["STORES"]);
        }
    }
};

$modifyArPrice();
$modifyArStores();