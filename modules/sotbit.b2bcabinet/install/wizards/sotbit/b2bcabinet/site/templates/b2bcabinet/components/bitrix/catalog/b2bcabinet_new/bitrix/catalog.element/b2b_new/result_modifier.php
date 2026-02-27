<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$productTypes = [
    "SIMPLE"                    => 1,
    "SET"                       => 2,
    "PRODUCT_WITH_OFFERS"       => 3,
    "OFFER"                     => 4,
    "OFFER_WITHOUT_PRODUCT"     => 5
];

$item = &$arResult;

//Picture preparation
$productPicture = $item['PREVIEW_PICTURE'];

if (empty($productPicture['ID']) && !empty($item['DETAIL_PICTURE']['ID'])) {
    $productPicture = $item['DETAIL_PICTURE'];
} else {
    if (empty($productPicture['ID']) && !empty($item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
        $productPicture = $item['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
    }
}

if (!empty($productPicture['ID'])) 
{
    $productPictureOrigin = CFile::GetPath($productPicture['ID']);

    $productPicture = CFile::ResizeImageGet(
        $productPicture['ID'],
        array('width' => 320, 'height' => 320),
        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
        true
    );
}
$item['PICTURE'] = $productPicture['src']?:SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';

$arBasketItems = $arParams['BASKET_STATE'];

if (intval($item['CATALOG_TYPE']) === 3) {
    foreach ($arResult['OFFERS'] as $offerId => &$offer) {
        $offer['ACTUAL_QUANTITY'] = !empty($arBasketItems[$offer['ID']]) ? $arBasketItems[$offer['ID']] : 0;
        foreach ($offer['ITEM_QUANTITY_RANGES'] as $rangeName => $rangeProps ) {
            if ( $offer['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM'] 
                 && $offer['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
            ) {
                $offer['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
            }
        }
    }
    unset($offerId, $offer);
} else {
    $item['ACTUAL_QUANTITY'] = !empty($arBasketItems[$item['ID']]) ? $arBasketItems[$item['ID']] : 0;
    foreach ($item['ITEM_QUANTITY_RANGES'] as $rangeName => $rangeProps) {
        if ( $item['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM'] 
             && $item['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
        ) {
            $item['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
        }
    }
    unset($rangeName, $rangeProps);
}

//price modification
$priceTypeHelper = []; // need for offers
foreach($arResult['CAT_PRICES'] as $priceCode => $priceType) {
    $priceTypeHelper[$priceType['ID']] = $priceCode;
}
unset($priceCode, $priceType);

if (
    isset( $item['PRODUCT']['TYPE'] ) 
    && $item['PRODUCT']['TYPE'] === $productTypes["SIMPLE"]  
) {
    $printPrices = [];
    $cols = $item['PRICE_MATRIX']['COLS'];
    $matrix = $item['PRICE_MATRIX']['MATRIX'];
    foreach ($matrix as $key => $value) {
        $printPrices[$cols[$key]['NAME']] = $value;
        foreach($value as $range => $priceDetails) {
            $printPrices[$cols[$key]['NAME']][$range]['PRINT'] = CCurrencyLang::CurrencyFormat(
                $arResult["CATALOG_MEASURE_RATIO"] ? $priceDetails['DISCOUNT_PRICE'] * $arResult["CATALOG_MEASURE_RATIO"] : $priceDetails['DISCOUNT_PRICE'],
                $priceDetails['CURRENCY']
            );
            if (round($priceDetails['DISCOUNT_PRICE'], 2) !== round($priceDetails['PRICE'], 2)) {
                $printPrices[$cols[$key]['NAME']][$range]['PRINT_WHITHOUT_DISCONT'] = CCurrencyLang::CurrencyFormat(
                    $arResult["CATALOG_MEASURE_RATIO"] ? $priceDetails['PRICE'] * $arResult["CATALOG_MEASURE_RATIO"] : $priceDetails['PRICE'],
                    $priceDetails['CURRENCY']
                );
            }
        }
    }
    $arResult['PRINT_PRICES'] = $printPrices;

    unset($value, $range, $priceDetails, $printPrices, $cols, $matrix);
}

// Add private price to prices
if (\Bitrix\Main\Loader::includeModule("sotbit.privateprice") && \Bitrix\Main\Config\Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && $GLOBALS["USER"]->IsAuthorized()) {
    $sotbitPrivatePrice = true;
    $arResult['CAT_PRICES']['PRIVATE_PRICE']['TITLE'] = Loc::getMessage('CT_BCS_CATALOG_MESS_PRIVATE_PRICE_TITLE');
    $arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"] = str_replace("PRODUCT_", "", \Bitrix\Main\Config\Option::get("sotbit.privateprice", "PRODUCT_UNIQUE_KEY", "ID"));
}
// share product properties to offers
if (
    isset( $item['PRODUCT']['TYPE'] ) 
    && $item['PRODUCT']['TYPE'] === $productTypes["PRODUCT_WITH_OFFERS"]
    && isset( $item['OFFERS'] )
    && count( $item['OFFERS'] ) > 0 
) {
    // collect offers IDs
    $offersIds = [];
    foreach ($item['OFFERS'] as $offer) {
        $offersIds[] = $offer['ID'];
        $offersMesure[$offer['ID']] = $offer['CATALOG_MEASURE_RATIO'];
    }
    unset($offer);

    $offersPricesUnprepared = \Bitrix\Catalog\PriceTable::getList([
        "select" => ["*", "CATALOG_GROUP_ID"],
        "filter" => [
            "=PRODUCT_ID" => $offersIds,
            "CATALOG_GROUP_ID" => $arResult['PRICES_ALLOW']
        ],
        "order" => ["CATALOG_GROUP_ID" => "ASC", "PRODUCT_ID" => "ASC"]
    ])->fetchAll();

    $offersPrices = [];
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
        $price["DISCOUNT_PRICE"] = !empty($offersMesure[$price["PRODUCT_ID"]] ) ?  $discountPrice * $offersMesure[$price["PRODUCT_ID"]] : $discountPrice;
        $price["PRICE"] = !empty($offersMesure[$price["PRODUCT_ID"]] ) ? $price["PRICE"] * $offersMesure[$price["PRODUCT_ID"]] : $price["PRICE"];
        // $list[prod_id][price_code][price_range]
        $offersPrices[$price['PRODUCT_ID']][$priceTypeHelper[$price['CATALOG_GROUP_ID']]][($price['QUANTITY_FROM']?:'ZERO') . '-' . ($price['QUANTITY_TO']?:'INF')] = [
            "ID" => $price["ID"],
            "PRICE" => $price["PRICE"],
            "DISCOUNT_PRICE" => ($price["DISCOUNT_PRICE"]?:$price["PRICE"]),
            "UNROUND_DISCOUNT_PRICE" => "",
            "CURRENCY" => $price["CURRENCY"],
            "VAT_RATE" => "",
            "PRINT" => CCurrencyLang::CurrencyFormat(($price["DISCOUNT_PRICE"]?:$price['PRICE']), $price["CURRENCY"])
        ];
    }
    unset($price);

    foreach ($item['OFFERS'] as $key => &$offer) {
        if (
            empty($offer["PREVIEW_PICTURE"]) 
            && empty($offer["DETAIL_PICTURE"])
            && empty($offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0])
        )
        {
            $offer['PICTURE'] = $item['PICTURE'];
        } else {

            // Picture preparation
            $offerPicture = $offer['PREVIEW_PICTURE'];
            if (empty($offerPicture['ID']) && !empty($offer['DETAIL_PICTURE']['ID'])) {
                $offerPicture = $offer['DETAIL_PICTURE'];
            } else {
                if (empty($offerPicture['ID']) && !empty($offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0])) {
                    $offerPicture = $offer['PROPERTIES'][$arParams["OFFER_ADD_PICT_PROP"]]['VALUE'][0];
                }
            }

            if (!empty($offerPicture)) {
                $offerPictureOrigin = CFile::GetPath($offerPicture['ID']);
            
                $offerPicture = CFile::ResizeImageGet(
                    $offerPicture,
                    array('width' => 45, 'height' => 45),
                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                    true
                );
            }

            $offer['PICTURE'] = $offerPicture['src'];
            unset($offerPicture);

        }    
        // Price preparation
        $offer['PRINT_PRICES'] = $offersPrices[$offer['ID']];
        if ($sotbitPrivatePrice) {
            $offer['PRINT_PRICES']['PRIVATE_PRICE'] = [];
        }
        unset($printPrices, $cols, $matrix);
        
    }
    
    unset($key, $offer);
}

// Gallery
$arResult['GALLERY'] = [];

$addPictProp = $arParams['ADD_PICT_PROP'];
$addOfferPictProp = $arParams['OFFER_ADD_PICT_PROP'];

if ($addPictProp && is_set($arResult['PROPERTIES'][$addPictProp]['VALUE'])) {
    foreach($arResult['PROPERTIES'][$addPictProp]['VALUE'] as $index => $imageId) {
        $arResult['GALLERY'][$imageId] = [
            'ID' => $imageId,
            'DESCRIPTION' => $arResult['PROPERTIES'][$addPictProp]['DESCRIPTION'][$index]
        ];
    }
    unset($index,$imageId);
}

if ($addOfferPictProp && $arResult['PRODUCT']['TYPE'] === 3 && count($arResult['OFFERS'])) {
    foreach($arResult['OFFERS'] as $offer) {
        foreach($offer['PROPERTIES'][$addPictProp]['VALUE'] ?: [] as $index => $imageId) {
            $arResult['GALLERY'][$imageId] = [
                'ID' => $imageId,
                'DESCRIPTION' => $offer['PROPERTIES'][$addPictProp]['DESCRIPTION'][$index]
            ];
        }
        unset($index,$imageId);
    }
    unset($offer);
}

if( !empty($arResult['GALLERY']) && is_array($arResult['GALLERY'])) {
    $obFiles = CFile::GetList(
        '',
        ["@ID" => implode(',', array_keys($arResult['GALLERY']))]
    );
    $uploadDir = COption::GetOptionString("main", "upload_dir", "upload");
    while($file = $obFiles->GetNext()) {
        $arResult['GALLERY'][$file['ID']] += [
            'ORIGIN' => $file,
            'LINK' => '/' . implode("/", [
                $uploadDir,
                $file["SUBDIR"],
                $file["FILE_NAME"]
            ]),
            'NAME' => $file["ORIGINAL_NAME"],
            'CONTENT_TYPE' => $file["CONTENT_TYPE"],
            'TYPE' => end(explode('.', $file["FILE_NAME"]))
        ];  
    }
    unset($obFiles, $file, $uploadDir);
}

// Documents
$arResult['DOCUMENTS'] = [];
$propDoc = $arParams['DETAIL_MAIN_FILES_PROPERTY'];
if ($propDoc) {
    if(!empty($arResult['PROPERTIES'][$propDoc]['VALUE'])) 
    {
        $arFiles = $arResult['PROPERTIES'][$propDoc]['VALUE'];
        
        if(!is_array($arFiles)) 
        {
            $arFiles['VALUE'] = [$arFiles['VALUE']];
        }
            
        foreach($arFiles as $index => $fileId) {
            $arResult['DOCUMENTS'][$fileId] = null;
        }
    }
    if ($arResult['PRODUCT']['TYPE'] === 3 && count($arResult['OFFERS']))
    {
        foreach($arResult['OFFERS'] as $offer) {
            $arFiles = $offer['PROPERTIES'][$propDoc]['VALUE'];
        
            if(!is_array($arFiles)) 
            {
                $arFiles[] = [$arFiles];
            }
                
            foreach($arFiles as $index => $fileId) {
                $arResult['DOCUMENTS'][$fileId] = null;
            } 
        }
    }
    $obFiles = CFile::GetList(
        '',
        ["@ID" => implode(',', array_keys($arResult['DOCUMENTS']))]
    );
    $uploadDir = COption::GetOptionString("main", "upload_dir", "upload");
    while($file = $obFiles->GetNext()) {
        $arResult['DOCUMENTS'][$file['ID']] = [
            'ORIGIN' => $file,
            'LINK' => '/' . implode("/", [
                $uploadDir,
                $file["SUBDIR"],
                $file["FILE_NAME"]
            ]),
            'NAME' => $file["ORIGINAL_NAME"],
            'CONTENT_TYPE' => $file["CONTENT_TYPE"],
            'TYPE' => end(explode('.', $file["FILE_NAME"]))
        ];  
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