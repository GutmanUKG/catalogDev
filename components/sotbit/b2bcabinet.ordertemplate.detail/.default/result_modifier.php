<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option;

if ($arResult["PRODUCTS"]) {
    $totalPrice = 0;
    $totalQnt = 0;
    $unavailableCount = 0;
    $availableCount = 0;

    $productAvailableQnt = [];

    $dbProductInfo = \CCatalogProduct::GetList(
        [],
        ["ID" => $arResult["PRODUCTS_ID"]],
        false,
        false,
        ["ID", "QUANTITY"]
    );

    while ($arProduct = $dbProductInfo->fetch()) {
        $productAvailableQnt[$arProduct["ID"]] = $arProduct["QUANTITY"];
    }

    foreach ($arResult["PRODUCTS"] as $key => $product) {
        if (!$product["NAME"]) {
            continue;
        }
        if ($product["PICTURE"]["SRC"]) {
            $img = $product["PICTURE"]["SRC"];
        } else {
            $img = SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
        }
        if ((!$product["PRICE"] && !$product["PRICE_AVAILABLE"]) || $product["ACTIVE"] == "N" ) {
            $unavailableCount++;
            $unavailable[$unavailableCount]["data"]["ID"] = $product["PRODUCT_ID"];
            $unavailable[$unavailableCount]["data"]["NAME"] = '<div class="d-flex align-items-center"><div class="orders-templates__image-container rounded overflow-hidden"><img src="' . $img . '"></div><div class="orders-templates__product-link"><a href="' . $product["DETAIL_PAGE_URL"] . '" target="_blank">' . $product["NAME"] . '</a></div></div>';
            $unavailable[$unavailableCount]["data"]["QNT"] = '<span class="unavailable-product">' . $product["QUANTITY"] . '';
            $unavailable[$unavailableCount]["data"]["PRICE"] = '<span class="unavailable-product">' . GetMessage("PRODUCT_UNABLE") . '</span>';
            $unavailable[$unavailableCount]["editable"] = true;
        } elseif ($productAvailableQnt[$product["PRODUCT_ID"]] < $product["QUANTITY"]) {
            $availableCount++;
            $available[$availableCount]["data"]["ID"] = $product["PRODUCT_ID"];
            $available[$availableCount]["data"]["NAME"] = '<div class="d-flex align-items-center"><div class="orders-templates__image-container rounded overflow-hidden"><img src="' . $img . '"></div><div class="orders-templates__product-link"><a href="' . $product["DETAIL_PAGE_URL"] . '" target="_blank">' . $product["NAME"] . '</a></div></div>';
            $available[$availableCount]["data"]["QNT"] = $productAvailableQnt[$product["PRODUCT_ID"]] . '&nbsp;<span class="unavailable-product"> (' . $product["QUANTITY"] . ')</span>';
            $available[$availableCount]["data"]["PRICE"] = CurrencyFormat($product["PRICE"], $product["CURRENCY"]);
            $available[$availableCount]["editable"] = true;

            $totalPrice += $product["PRICE_AVAILABLE"];
            $totalQnt += $productAvailableQnt[$product["PRODUCT_ID"]];
        } else {
            $availableCount++;
            $available[$availableCount]["data"]["ID"] = $product["PRODUCT_ID"];
            $available[$availableCount]["data"]["NAME"] = '<div class="d-flex align-items-center"><div class="orders-templates__image-container rounded overflow-hidden"><img src="' . $img . '"></div><div class="orders-templates__product-link"><a href="' . $product["DETAIL_PAGE_URL"] . '" target="_blank">' . $product["NAME"] . '</a></div></div>';
            $available[$availableCount]["data"]["QNT"] = $product["QUANTITY"];
            $available[$availableCount]["data"]["PRICE"] = CurrencyFormat($product["PRICE"], $product["CURRENCY"]);
            $available[$availableCount]["editable"] = true;

            $totalQnt += $product["QUANTITY"];
            $totalPrice += $product["PRICE"];
        }
    }
    if (!empty($unavailable) && !empty($available)) {
        $arResult["ROWS"] = array_merge($available, $unavailable);
    } else {
        $arResult["ROWS"] = $available;
    }
    $arResult["TOTAL_PRICE"] = CurrencyFormat($totalPrice, CSaleLang::GetLangCurrency(SITE_ID));
    $arResult["TOTAL_QUANTITY"] = $totalQnt;
}

$arResult["TABLE_HEADER"] = [
    "NAME" => GetMessage("TABLE_HEADER_NAME"),
    "ID" => "ID",
    "QUANTITY" => GetMessage("TABLE_HEADER_QUANTITY"),
];


if ($arParams["IBLOCK_ID"]) {
    $arResult["FILTER_DOCUMENT"] = ["ID" => $arResult["PRODUCTS_ID"]];

    if ($arParams["LIST_PROPERTY_CODE"]) {
        foreach ($arParams["LIST_PROPERTY_CODE"] as $code) {
            $rsProperty = CIBlockProperty::GetList(array(), ["CODE" => $code, "IBLOCK_ID" => $arParams["IBLOCK_ID"]]);

            if ($property = $rsProperty->Fetch()) {
                $arResult["TABLE_HEADER"][$property["CODE"]] = $property["NAME"];
            }
        }

    }
}
if ($arResult["PRODUCTS_ID"]) {
    foreach ($arResult["PRODUCTS_ID"] as $proId) {
        $arResult["QUANTITY_PRODUCTS"][$proId] = $arResult["PRODUCTS"][$proId]["QUANTITY"];
    }
}

$useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
$replaceValue = null;
if ($useReplace) {
    $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
    $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);

    foreach ($arResult["ROWS"] as $key => $val) {
        if (!empty($arResult["ROWS"][$key]["data"]["NAME"]))
            $arResult["ROWS"][$key]["data"]["NAME"] = str_replace($replaceableValue, $replaceValue, $arResult["ROWS"][$key]["data"]["NAME"]);
    }
}
