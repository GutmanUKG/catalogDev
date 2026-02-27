<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$productTypes = [
    "SIMPLE" => 1,
    "SET" => 2,
    "PRODUCT_WITH_OFFERS" => 3,
    "OFFER" => 4,
    "OFFER_WITHOUT_PRODUCT" => 5
];

$item = &$arResult['ITEM'];

//Picture preparation
$productPicture = $item['PREVIEW_PICTURE'];

if (empty($productPicture['ID']) && !empty($item['DETAIL_PICTURE']['ID'])) {
    $productPicture = $item['DETAIL_PICTURE'];
} else {
    if (empty($productPicture['ID']) && !empty($item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
        $productPicture['ID'] = $item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
    }
}

if (!empty($productPicture['ID'])) {
    $productPictureOrigin = CFile::GetPath($productPicture['ID']);

    $productPicture = CFile::ResizeImageGet(
        $productPicture['ID'],
        array('width' => 45, 'height' => 45),
        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
        true
    );
}
$item['PICTURE'] = $productPicture['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';

//price modification
$priceTypeHelper = []; // need for offers
foreach ($arResult['TABLE_HEADER']['PRICES'] as $priceCode => $priceType) {
    $priceTypeHelper[$priceType['ID']] = $priceCode;
}

if (
    isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === $productTypes["SIMPLE"]
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
    $arResult['ITEM']['PRINT_PRICES'] = $printPrices;


    unset($printPrices, $cols, $matrix);
}

// share product properties to offers
if (
    isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === $productTypes["PRODUCT_WITH_OFFERS"]
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
    $baseCurrency = \CCurrency::GetBaseCurrency() != $arParams["CURRENCY_ID"] ? $arParams["CURRENCY_ID"] : \CCurrency::GetBaseCurrency();
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

        // $list[prod_id][price_code][price_range]
        if ($priceTypeHelper[$price['CATALOG_GROUP_ID']]) {
            $offersPrices[$price['PRODUCT_ID']][$priceTypeHelper[$price['CATALOG_GROUP_ID']]][($price['QUANTITY_FROM'] ?: 'ZERO') . '-' . ($price['QUANTITY_TO'] ?: 'INF')] = [
                "ID" => $price["ID"],
                "PRICE" => !empty($offersMesure[$price["PRODUCT_ID"]] ) ? $price["PRICE"] * $offersMesure[$price["PRODUCT_ID"]] : $price["PRICE"],
                "DISCOUNT_PRICE" => ($price["DISCOUNT_PRICE"] ?: $price["PRICE"]),
                "UNROUND_DISCOUNT_PRICE" => "",
                "CURRENCY" => $price["CURRENCY"],
                "VAT_RATE" => "",
                "PRINT" => CCurrencyLang::CurrencyFormat(($price["DISCOUNT_PRICE"] ?: $price['PRICE']), $price["CURRENCY"])
            ];
            if (empty($item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]]) ) {
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]] = $price['PRICE'];
            } elseif ($item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]] > $price['PRICE'] && $price['PRICE'] > 0) {
                $item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]] = $price['PRICE'];
            }

                $minPirce = &$item['MIN_PRICE'][$priceTypeHelper[$price['CATALOG_GROUP_ID']]];
                $minPirce = empty($minPirce)
                    ? "INF"
                    : ($minPirce > CCurrencyRates::ConvertCurrency(floatval($price["DISCOUNT_PRICE"]), $price["CURRENCY"], $baseCurrency)
                        ? $price["DISCOUNT_PRICE"]
                        : $minPirce);

        }
    }

    foreach ($item['OFFERS'] as $key => &$offer) {
        if (
            empty($offer["PREVIEW_PICTURE"])
            && empty($offer["DETAIL_PICTURE"])
            && empty($offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0])
        ) {
            $offer['PICTURE'] = $item['PICTURE'];
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
            if (empty($offerPicture["SRC"])) {
                if (!empty($offerPicture['ID'])) {

                    $offerPicture['src'] = CFile::GetPath($offerPicture['ID']);

                    $offerPicture['resize'] = CFile::ResizeImageGet(
                        $offerPicture['ID'],
                        array('width' => 45, 'height' => 45),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        true
                    );
                } else {
                    $offerPictire['src'] = $item['PICTURE'];
                }
                $offer['PICTURE'] = $offerPicture['resize']['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
            } else {
                $offer['PICTURE'] =  $offerPicture["SRC"];
            }

            unset($offerPicture);

        }
        // Price preparation
        $offer['PRINT_PRICES'] = $offersPrices[$offer['ID']];
        unset($printPrices, $cols, $matrix);
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

foreach ($arResult['ITEM']['OFFERS'] as $key => $val) {
    if (isset($arParams['ITEMS_PRIVAT_PRICES'][$val['ID']][$arParams['PRIVAT_PRICES_PARAMS']['PRICE_COLUMN']]) &&
        $val['MIN_PRICE']['VALUE'] > $arParams['ITEMS_PRIVAT_PRICES'][$val['ID']][$arParams['PRIVAT_PRICES_PARAMS']['PRICE_COLUMN']]) {
        $arResult['ITEM']['OFFERS'][$key]['MIN_PRICE']['MIN_PRICE_WITH_PRIVAT_PRICE'] = $arParams['ITEMS_PRIVAT_PRICES'][$val['ID']]["PRIVAT_PRICE_PRINT"];
    } else {
        $arResult['ITEM']['OFFERS'][$key]['MIN_PRICE']['MIN_PRICE_WITH_PRIVAT_PRICE'] = $val['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
    }
}

unset(
    $item,
    $productPicture,
    $arResult['ITEM']['PREVIEW_PICTURE'],
    $arResult['ITEM']['DETAIL_PICTURE'],
    $arResult['ITEM']['~PREVIEW_PICTURE'],
    $arResult['ITEM']['~DETAIL_PICTURE']
);