<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER;

if (isset($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) {
    $arBanners = [];
    foreach ($arResult["ITEMS"] as $item) {
        if ($item["DETAIL_PICTURE"]["SRC"]) {
            $arBanners[$item['ID']]["SRC"] = $item["DETAIL_PICTURE"]["SRC"];
        }
        if (!empty($item["PROPERTIES"]["LINK"]["VALUE"])) {
            $arBanners[$item['ID']]["LINK"] = $item["PROPERTIES"]["LINK"]["VALUE"];
        }
    }

    $arResult["BANNERS"] = $arBanners;
}

