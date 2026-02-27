<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Config\Option;

$arResult['COUNT_TABLE_HEADER'] = 1;
foreach ($arResult['TABLE_HEADER'] as $item) {
    $arResult['COUNT_TABLE_HEADER']++;
}

$item = &$arResult['ITEM'];

//Picture preparation
if ($arProduct = CIBlockElement::GetByID($item["ID"])->Fetch()) {
    $productPicture['ID'] = $arProduct['PREVIEW_PICTURE'];

    if (empty($productPicture['ID']) && !empty($arProduct['DETAIL_PICTURE'])) {
        $productPicture['ID'] = $arProduct['DETAIL_PICTURE'];
    }
    elseif (empty($productPicture['ID']) && !empty($item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
        $productPicture = $item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
    }
} else {
    $productPicture = $item['PREVIEW_PICTURE'];

    if (empty($productPicture['ID']) && !empty($item['DETAIL_PICTURE']['ID'])) {
        $productPicture = $item['DETAIL_PICTURE'];
    } else {
        if (empty($productPicture['ID']) && !empty($item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
            $productPicture['ID'] = $item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
        }
    }
}

if (!empty($productPicture['ID'])) {
    $productPicture['resize'] = CFile::ResizeImageGet(
        $productPicture['ID'],
        array('width' => 74, 'height' => 74),
        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
        true
    );

    $productPicture['resize_150'] = CFile::ResizeImageGet(
        $productPicture['ID'],
        array('width' => 150, 'height' => 150),
        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
        true
    );
}
$item['PICTURE'] = $productPicture['resize']['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
$item['PICTURE_150'] = $productPicture['resize_150']['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';

//price modification
$priceTypeHelper = []; // need for offers
foreach ($arResult['TABLE_HEADER']['PRICES'] as $priceCode => $priceType) {
    $priceTypeHelper[$priceType['ID']] = $priceCode;
}

if (
    isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_PRODUCT
) {
    $printPrices = [];
    $cols = $item['PRICE_MATRIX']['COLS'];
    $matrix = $item['PRICE_MATRIX']['MATRIX'];
    foreach ($matrix ?: [] as $key => $value) {
        $printPrices[$cols[$key]['NAME']] = $value;
        foreach ($value as $range => $priceDetails) {
            $printPrices[$cols[$key]['NAME']][$range]['PRINT'] = CCurrencyLang::CurrencyFormat(
                $arResult['ITEM']["CATALOG_MEASURE_RATIO"] ? $priceDetails['DISCOUNT_PRICE'] * $arResult['ITEM']["CATALOG_MEASURE_RATIO"] : $priceDetails['DISCOUNT_PRICE'],
                $priceDetails['CURRENCY']
            );

            if (round($priceDetails['PRICE'], 2) === round($item['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['VALUE'], 2)) {
                $item['DISPLAY_PROPERTIES']['MINIMUM_PRICE']['DISPLAY_VALUE'] = CCurrencyLang::CurrencyFormat(
                    $arResult['ITEM']["CATALOG_MEASURE_RATIO"] ? $priceDetails['DISCOUNT_PRICE'] * $arResult['ITEM']["CATALOG_MEASURE_RATIO"] : $priceDetails['DISCOUNT_PRICE'],
                    $priceDetails['CURRENCY']
                );
            }
            if (round($priceDetails['PRICE'], 2) === round($item['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']['VALUE'], 2)) {
                $item['DISPLAY_PROPERTIES']['MAXIMUM_PRICE']['DISPLAY_VALUE'] = CCurrencyLang::CurrencyFormat(
                    $arResult['ITEM']["CATALOG_MEASURE_RATIO"] ? $priceDetails['DISCOUNT_PRICE'] * $arResult['ITEM']["CATALOG_MEASURE_RATIO"] : $priceDetails['DISCOUNT_PRICE'],
                    $priceDetails['CURRENCY']
                );
            }
        }
    }
    
    // Private price
    if (
        \Bitrix\Main\Loader::includeModule("sotbit.privateprice") && 
        \Bitrix\Main\Config\Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && 
        $GLOBALS["USER"]->IsAuthorized()
    ) {
        if (!empty($arParams['ITEMS_PRIVAT_PRICES'][$item['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]])) {
            $printPrices['PRIVATE_PRICE'][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] = !empty($item['CATALOG_MEASURE_RATIO']) ? $arParams['ITEMS_PRIVAT_PRICES'][$item['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]] * $item['CATALOG_MEASURE_RATIO'] : $arParams['ITEMS_PRIVAT_PRICES'][$item['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]];
            $printPrices['PRIVATE_PRICE'][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] = CCurrencyLang::CurrencyFormat($printPrices['PRIVATE_PRICE'][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], $arParams['ITEMS_PRIVAT_PRICES'][$item['ID']][$arParams['PRIVAT_PRICES_PARAMS']['CURRENCY_FORMAT']]);
        } else {
            $printPrices['PRIVATE_PRICE'][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] = \SotbitPrivatePriceMain::setPlaceholder($item[$arParams["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '');
        }
    }

    $arResult['ITEM']['PRINT_PRICES'] = $printPrices;

    unset($printPrices, $cols, $matrix);
}

// share product properties to offers
if (
    isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU
    && isset($item['OFFERS'])
    && count($item['OFFERS']) > 0
) {
    // collect offers IDs
    $offersIds = [];
    foreach ($item['OFFERS'] as $offer) {
        $offersIds[] = $offer['ID'];
        $offersMesure[$offer['ID']] = $offer['CATALOG_MEASURE_RATIO'];
    }

    $offersPricesUnprepared = \Bitrix\Catalog\PriceTable::getList([
        "select" => ["*", "CATALOG_GROUP_ID"],
        "filter" => [
            "=PRODUCT_ID" => $offersIds,
        ],
        "order" => ["CATALOG_GROUP_ID" => "ASC", "PRODUCT_ID" => "ASC"]
    ])->fetchAll();

    $offersPrices = [];
    $baseCurrency = \CCurrency::GetBaseCurrency();
    foreach ($offersPricesUnprepared as $price) {
        $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
            $price["ID"],
            $USER->GetUserGroupArray(),
            "N",
            SITE_ID
        );
        $discountPrice = CCatalogProduct::CountPriceWithDiscount(
            $price["PRICE"],
            $price["CURRENCY"],
            $arDiscounts
        );
        $price["DISCOUNT_PRICE"] =!empty($offersMesure[$price["PRODUCT_ID"]] ) ?  $discountPrice * $offersMesure[$price["PRODUCT_ID"]] : $discountPrice;
        $price["PRICE"] = !empty($offersMesure[$price["PRODUCT_ID"]] ) ? $price["PRICE"] * $offersMesure[$price["PRODUCT_ID"]] : $price["PRICE"];

        // $list[prod_id][price_code][price_range]
        if ($priceTypeHelper[$price['CATALOG_GROUP_ID']]) {
            $offerPrice = [
                "ID" => $price["ID"],
                "PRICE" => $price["PRICE"],
                "DISCOUNT_PRICE" => ($price["DISCOUNT_PRICE"] ?: $price["PRICE"]),
                "CURRENCY" => $price["CURRENCY"],
                "VAT_RATE" => "",
                "PRINT" => CCurrencyLang::CurrencyFormat(($price["DISCOUNT_PRICE"] ?: $price["PRICE"]), $price["CURRENCY"])
            ];
            $offerPrice["PRINT_NOT_DISCOUNT_PRICE"] = CCurrencyLang::CurrencyFormat($offerPrice["PRICE"], $offerPrice["CURRENCY"]);
            $offersPrices[$price['PRODUCT_ID']][$priceTypeHelper[$price['CATALOG_GROUP_ID']]][($price['QUANTITY_FROM'] ?: 'ZERO') . '-' . ($price['QUANTITY_TO'] ?: 'INF')] = $offerPrice;
            if (empty($item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]) ) {
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]['PRICE'] = $price["PRICE"];
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]['PRINT'] = CCurrencyLang::CurrencyFormat($price["PRICE"], $price['CURRENCY']);
            } elseif ($item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]] > $price["PRICE"] && $price["PRICE"] > 0) {
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]['PRICE'] = $price["PRICE"];
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]['PRINT'] = CCurrencyLang::CurrencyFormat($price["PRICE"], $price['CURRENCY']);
            }
                $minPirce = &$item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]];
                $minPirce['PRICE'] = empty($minPirce)
                    ? "INF"
                    : ($minPirce > CCurrencyRates::ConvertCurrency(floatval($price["DISCOUNT_PRICE"]), $price["CURRENCY"], $baseCurrency)
                        ? $price["DISCOUNT_PRICE"]
                        : $minPirce);
                $minPirce['PRINT'] = CCurrencyLang::CurrencyFormat($minPirce['PRICE'], $baseCurrency);
        }
    }

    foreach ($item['OFFERS'] as $key => &$offer) {
        if (
            empty($offer["PREVIEW_PICTURE"])
            && empty($offer["DETAIL_PICTURE"])
            && empty($offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0])
        ) {
            $offer['PICTURE'] = $item['PICTURE'];
            $offer['PICTURE_150'] = $item['PICTURE_150'];
        } else {
            // Picture preparation

            $offerPicture = $offer['PREVIEW_PICTURE'];
            if (empty($offerPicture['ID']) && !empty($offer['DETAIL_PICTURE']['ID'])) {
                $offerPicture = $offer['DETAIL_PICTURE'];
            } else {
                if (empty($offerPicture['ID']) && !empty($offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0])) {
                    $offerPicture['ID'] = $offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0];
                }
            }

            if (!empty($offerPicture['ID'])) {
                $offerPicture['resize'] = CFile::ResizeImageGet(
                    $offerPicture['ID'],
                    array('width' => 74, 'height' => 74),
                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                    true
                );

                $offerPicture['resize_150'] = CFile::ResizeImageGet(
                    $offerPicture['ID'],
                    array('width' => 150, 'height' => 150),
                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                    true
                );
            } else {
                $offerPicture['resize']['src'] = $item['PICTURE'];
                $offerPicture['resize_150']['src'] = $item['PICTURE_150'];
            }
            
            $offer['PICTURE'] = $offerPicture['resize']['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
            $offer['PICTURE_150'] = $offerPicture['resize_150']['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
            unset($offerPicture);
        }

        // Quantity for product
        $item['CATALOG_QUANTITY'] += $offer['CATALOG_QUANTITY'];

        // Private price 
        if (!empty($arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]])) {
            $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] = !empty($offersMesure[$offer["ID"]]) ? $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]] * $offersMesure[$offer["ID"]] : $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]];
            $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] = CCurrencyLang::CurrencyFormat($offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']['CURRENCY_FORMAT']]);
        } else {
            $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] = $offer['MIN_PRICE']['DISCOUNT_VALUE'] * $offersMesure[$offer['ID']];
            $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] = CCurrencyLang::CurrencyFormat($offer['MIN_PRICE']['DISCOUNT_VALUE'] * $offersMesure[$offer['ID']], $offer['MIN_PRICE']['CURRENCY']);
        }

        if (
            empty($item['MIN_PRICE']['PRIVATE_PRICE']) || 
            $item['MIN_PRICE']['PRIVATE_PRICE']['PRICE'] > $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE']
            ) {
            $item['MIN_PRICE']['PRIVATE_PRICE'] = $offersPrices[$offer['ID']]['PRIVATE_PRICE'][$offer['ITEM_QUANTITY_RANGE_SELECTED']];
        }

        // Price preparation
        $offer['PRINT_PRICES'] = $offersPrices[$offer['ID']];
    }

    // convert single offer into item else sort offers
//    if (count( $item['OFFERS'] ) === 1) {
//        $arResult['ITEM'] = $item['OFFERS'][0];
//    } else {
    $sortCode = $arParams["ELEMENT_SORT_FIELD"];
    $sortOrder = $arParams["ELEMENT_SORT_ORDER"];
    usort($item['OFFERS'], function ($a, $b) use ($sortCode, $sortOrder) {
        if (isset($a[$sortCode])) {
            return ($sortOrder == "DESC")
                ? strcmp($a[$sortCode], $b[$sortCode])
                : strcmp($a[$sortCode], $b[$sortCode]) * -1;
        }
        if (isset($a['PROPERTIES'][$sortCode])) {
            return ($sortOrder == "DESC")
                ? strcmp($a['PROPERTIES'][$sortCode]['VALUE'], $b['PROPERTIES'][$sortCode]['VALUE'])
                : strcmp($a['PROPERTIES'][$sortCode]['VALUE'], $b['PROPERTIES'][$sortCode]['VALUE']) * -1;
        }
        return strcmp($a["ID"], $b["ID"]);
    });
    unset($sortCode, $sortOrder);
//    }
}

foreach ($arResult['TABLE_HEADER']['PRICES'] as $priceCode => $priceValue) {
    if ($priceCode == "PRIVATE_PRICE" && !empty($arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]])) {
        if (empty($arResult['MIN_MOBILE_PRICE'])) {
            $arResult['MIN_MOBILE_PRICE'] = $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]];
            $arResult['MIN_MOBILE_PRICE_PRINT'] = $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"];
        } elseif ($arResult['MIN_MOBILE_PRICE'] > $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]]) {
            $arResult['MIN_MOBILE_PRICE'] = $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]];
            $arResult['MIN_MOBILE_PRICE_PRINT'] = $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"];
        }
        continue;
    }
    if (empty($arResult['MIN_MOBILE_PRICE']) && $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] > 0) {
        $arResult['MIN_MOBILE_PRICE'] = $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'];
        $arResult['MIN_MOBILE_PRICE_PRINT'] = $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
    } elseif ($arResult['MIN_MOBILE_PRICE'] > $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] &&
        $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] > 0) {
        $arResult['MIN_MOBILE_PRICE'] = $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'];
        $arResult['MIN_MOBILE_PRICE_PRINT'] = $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
    }
}

foreach ($arResult['ITEM']['OFFERS'] as $key => &$offer) {
    if (isset($arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']['PRICE_COLUMN']]) &&
        $offer['MIN_PRICE']['VALUE'] > $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']['PRICE_COLUMN']]) {
        $offer['MIN_PRICE']['VALUE'] = !empty($offersMesure[$offer["ID"]] ) ? $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]] * $offersMesure[$offer["ID"]] : $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']["PRICE_COLUMN"]];
        $offer['MIN_PRICE']['PRINT'] = CCurrencyLang::CurrencyFormat($offer['MIN_PRICE']['VALUE'], $arParams['ITEMS_PRIVAT_PRICES'][$offer['ID']][$arParams['PRIVAT_PRICES_PARAMS']['CURRENCY_FORMAT']]);
    } else {
        $offer['MIN_PRICE']['VALUE'] = !empty($offersMesure[$offer['ID']]) ? $offersMesure[$offer['ID']] * $offer['MIN_PRICE']['DISCOUNT_VALUE'] : $offer['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
        $offer['MIN_PRICE']['PRINT'] = CCurrencyLang::CurrencyFormat($offer['MIN_PRICE']['VALUE'], $offer["MIN_PRICE"]["CURRENCY"]);
    }
}

unset(
    $item,
    $productPicture,
    $offersMesure,
    $arResult['ITEM']['PREVIEW_PICTURE'],
    $arResult['ITEM']['DETAIL_PICTURE'],
    $arResult['ITEM']['~PREVIEW_PICTURE'],
    $arResult['ITEM']['~DETAIL_PICTURE']
);