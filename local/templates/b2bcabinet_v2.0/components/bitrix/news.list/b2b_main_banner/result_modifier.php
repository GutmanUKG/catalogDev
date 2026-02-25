<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER;

if (isset($arResult["ITEMS"]) && !empty($arResult["ITEMS"])) {
    $arBanners = [];
    foreach ($arResult["ITEMS"] as $index => $item) {
        if ($item["PREVIEW_PICTURE"]["SRC"]) {
            $arBanners[$index]["PREVIEW_SRC"] = $item["PREVIEW_PICTURE"]["SRC"];
        }
        if ($item["DETAIL_PICTURE"]["SRC"]) {
            $arBanners[$index]["DETAIL_SRC"] = $item["DETAIL_PICTURE"]["SRC"];
        }
        if (!empty($item["PROPERTIES"]["LINK"]["VALUE"])) {
            $arBanners[$index]["LINK"] = $item["PROPERTIES"]["LINK"]["VALUE"];
        }
    }

    $arResult["BANNERS"] = $arBanners;
}