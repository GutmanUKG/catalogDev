<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Catalog\ProductTable,
    Bitrix\Main\Page\Asset;
Loc::loadMessages(__FILE__);

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

if (intval($item['CATALOG_TYPE']) === ProductTable::TYPE_SKU) {
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
    && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_PRODUCT
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
    && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU
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
            "PRICE" =>$price["PRICE"],
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
                    array('width' => 74, 'height' => 74),
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

if ($addOfferPictProp && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU && count($arResult['OFFERS'])) {
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
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/lightbox/lightbox.min.js');

    foreach($arResult['GALLERY'] as &$image) {
        $image += [
            'SMALL_IMAGE' => CFile::ResizeImageGet(
                $image['ID'],
                array("width" => 300, "height" => 300),
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                true
            ),
            'BIG_IMAGE' => CFile::ResizeImageGet(
                $image['ID'],
                array("width" => 1200, "height" => 768),
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                true
            ),
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

        if(!is_array($arFiles) && isset($arFiles['VALUE']))
        {
            $arFiles['VALUE'] = [$arFiles['VALUE']];
        }

        foreach($arFiles as $index => $fileId) {
            $arResult['DOCUMENTS'][$fileId] = null;
        }
    }
    if ($arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU && count($arResult['OFFERS']))
    {
        foreach($arResult['OFFERS'] as $offer) {
            $arFiles = $offer['PROPERTIES'][$propDoc]['VALUE'];

            if (empty($arFiles)) continue;

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

$SALE_IBLOCK_ID = 37;
$storeCategories = [
    56 => Loc::getMessage('CT_BZD_MARKDOWN_CAT_1'),
    55 => Loc::getMessage('CT_BZD_MARKDOWN_CAT_2'),
    58 => Loc::getMessage('CT_BZD_MARKDOWN_CAT_3'),
    57 => Loc::getMessage('CT_BZD_MARKDOWN_CAT_4')
];

// Detect if this item is an orphan markdown (loaded from IB 37)
$arResult['IS_ORPHAN_MARKDOWN'] = ((int)($arResult['IBLOCK_ID'] ?? $arParams['IBLOCK_ID']) === $SALE_IBLOCK_ID);

$article = $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'];
$arResult['HAS_SECOND'] = false;
$arResult['SECOND_ITEMS'] = [];

if ($article) {
    $secondProducts = [];
    $secondProductIds = [];

    $res = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $SALE_IBLOCK_ID,
            'PROPERTY_CML2_ARTICLE' => $article,
            'ACTIVE' => 'Y'
        ],
        false,
        false,
        ['ID', 'NAME', 'PROPERTY_CML2_ARTICLE']
    );

    while ($row = $res->Fetch()) {
        $secondId = (int)$row['ID'];
        $secondProductIds[] = $secondId;
        $secondProducts[$secondId] = [
            'ID' => $secondId,
            'NAME' => $row['NAME'],
            'CML2_ARTICLE' => $row['PROPERTY_CML2_ARTICLE_VALUE'],
        ];
    }

    if (!empty($secondProductIds)) {
        $storeAmountsByProduct = [];
        $resStores = CCatalogStoreProduct::GetList(
            [],
            ['PRODUCT_ID' => $secondProductIds, 'STORE_ID' => array_keys($storeCategories)],
            false,
            false,
            ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']
        );
        while ($store = $resStores->Fetch()) {
            $amount = (float)$store['AMOUNT'];
            if ($amount > 0) {
                $productId = (int)$store['PRODUCT_ID'];
                $storeId = (int)$store['STORE_ID'];
                $storeAmountsByProduct[$productId][$storeId] = $amount;
            }
        }

        if (!empty($storeAmountsByProduct)) {
            $secondPricesRaw = \Bitrix\Catalog\PriceTable::getList([
                'select' => ['*', 'CATALOG_GROUP_ID'],
                'filter' => [
                    '=PRODUCT_ID' => $secondProductIds,
                    'CATALOG_GROUP_ID' => $arResult['PRICES_ALLOW'],
                ],
                'order' => ['CATALOG_GROUP_ID' => 'ASC', 'PRODUCT_ID' => 'ASC'],
            ])->fetchAll();

            $secondPrices = [];
            foreach ($secondPricesRaw as $price) {
                $productId = (int)$price['PRODUCT_ID'];
                $priceCode = $priceTypeHelper[$price['CATALOG_GROUP_ID']] ?? $price['CATALOG_GROUP_ID'];
                $rangeKey = ($price['QUANTITY_FROM'] ?: 'ZERO') . '-' . ($price['QUANTITY_TO'] ?: 'INF');
                $secondPrices[$productId][$priceCode][$rangeKey] = [
                    'ID' => $price['ID'],
                    'PRICE' => $price['PRICE'],
                    'DISCOUNT_PRICE' => $price['PRICE'],
                    'CURRENCY' => $price['CURRENCY'],
                    'DISCOUNT_IDS' => [],
                    'PRINT' => CCurrencyLang::CurrencyFormat($price['PRICE'], $price['CURRENCY']),
                    'PRINT_OLD' => '',
                ];
            }

            $secondProductsData = [];
            $resProductsData = \Bitrix\Catalog\ProductTable::getList([
                'select' => ['ID', 'QUANTITY_TRACE', 'CAN_BUY_ZERO'],
                'filter' => ['=ID' => $secondProductIds],
            ]);
            while ($productData = $resProductsData->fetch()) {
                $secondProductsData[(int)$productData['ID']] = $productData;
            }

            $secondMeasureRatios = [];
            $resMeasure = \Bitrix\Catalog\MeasureRatioTable::getList([
                'select' => ['PRODUCT_ID', 'RATIO'],
                'filter' => ['=PRODUCT_ID' => $secondProductIds, '=IS_DEFAULT' => 'Y'],
            ]);
            while ($measure = $resMeasure->fetch()) {
                $secondMeasureRatios[(int)$measure['PRODUCT_ID']] = (float)$measure['RATIO'];
            }

            $getSelectedPriceByCategory = static function (array $prices, string $category): array {
                if (empty($prices)) {
                    return [];
                }

                preg_match('/(\d+)/u', $category, $categoryMatch);
                $categoryNumber = $categoryMatch[1] ?? '';

                if ($categoryNumber !== '') {
                    foreach ($prices as $priceCode => $ranges) {
                        if ($priceCode === 'PRIVATE_PRICE') {
                            continue;
                        }

                        $normalizedCode = mb_strtolower((string)$priceCode);
                        $normalizedCode = preg_replace('/[^a-z0-9]/i', '', $normalizedCode);
                        preg_match('/(\d+)/', (string)$priceCode, $priceCodeNumberMatch);
                        $priceCodeNumber = $priceCodeNumberMatch[1] ?? '';

                        if (
                            $priceCodeNumber === $categoryNumber
                            || mb_strpos($normalizedCode, 'k' . $categoryNumber) !== false
                            || mb_strpos($normalizedCode, 'kat' . $categoryNumber) !== false
                            || mb_strpos($normalizedCode, 'cat' . $categoryNumber) !== false
                        ) {
                            return reset($ranges) ?: [];
                        }
                    }
                }

                foreach ($prices as $priceCode => $ranges) {
                    if ($priceCode === 'PRIVATE_PRICE') {
                        continue;
                    }

                    return reset($ranges) ?: [];
                }

                return [];
            };

            $decodeDiscountRuleTree = static function ($rawValue): array {
                if (is_array($rawValue)) {
                    return $rawValue;
                }

                if (!is_string($rawValue) || $rawValue === '') {
                    return [];
                }

                $decoded = @unserialize($rawValue);
                if (is_array($decoded)) {
                    return $decoded;
                }

                $decoded = json_decode($rawValue, true);
                if (is_array($decoded)) {
                    return $decoded;
                }

                return [];
            };

            $normalizeDiscountTree = static function ($node) use (&$normalizeDiscountTree) {
                if (is_object($node)) {
                    $node = (array)$node;
                }

                if (!is_array($node)) {
                    return $node;
                }

                $normalized = [];
                foreach ($node as $key => $value) {
                    $normalizedKey = $key;
                    if (is_string($normalizedKey)) {
                        if (mb_strpos($normalizedKey, "\0") !== false) {
                            $parts = explode("\0", $normalizedKey);
                            $normalizedKey = end($parts);
                        }
                        $normalizedKey = trim($normalizedKey);
                    }

                    $normalized[$normalizedKey] = $normalizeDiscountTree($value);
                }

                return $normalized;
            };

            $extractStoreIdsFromConditionNode = static function ($conditionNode): array {
                if (!is_array($conditionNode)) {
                    return [];
                }

                $rawValues = [];
                if (isset($conditionNode['STORE_ID'])) {
                    $rawValues[] = $conditionNode['STORE_ID'];
                }
                if (isset($conditionNode['STORE'])) {
                    $rawValues[] = $conditionNode['STORE'];
                }

                if (isset($conditionNode['DATA']) && is_array($conditionNode['DATA'])) {
                    if (array_key_exists('STORE_ID', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['STORE_ID'];
                    }
                    if (array_key_exists('store_id', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['store_id'];
                    }
                    if (array_key_exists('storeId', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['storeId'];
                    }
                    if (array_key_exists('value', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['value'];
                    }
                    if (array_key_exists('Value', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['Value'];
                    }
                    if (array_key_exists('VALUE', $conditionNode['DATA'])) {
                        $rawValues[] = $conditionNode['DATA']['VALUE'];
                    }
                }

                $storeIds = [];
                $collect = static function ($value) use (&$collect, &$storeIds): void {
                    if (is_array($value)) {
                        foreach ($value as $innerValue) {
                            $collect($innerValue);
                        }
                        return;
                    }

                    if (is_scalar($value)) {
                        preg_match_all('/\d+/', (string)$value, $matches);
                        if (!empty($matches[0])) {
                            foreach ($matches[0] as $id) {
                                $intId = (int)$id;
                                if ($intId > 0) {
                                    $storeIds[$intId] = $intId;
                                }
                            }
                        }
                    }
                };

                foreach ($rawValues as $rawValue) {
                    $collect($rawValue);
                }

                return array_values($storeIds);
            };

            $hasStoreCondition = static function ($conditionNode, int $storeId) use (&$hasStoreCondition, $extractStoreIdsFromConditionNode): bool {
                if (!is_array($conditionNode)) {
                    return false;
                }
                $classId = (string)($conditionNode['CLASS_ID'] ?? $conditionNode['ClassName'] ?? $conditionNode['class_id'] ?? '');

                if (
                    $classId !== ''
                    && mb_strpos($classId, 'CondCtrlProductOnWarehouse') !== false
                ) {
                    $nodeStoreIds = $extractStoreIdsFromConditionNode($conditionNode);
                    return in_array($storeId, $nodeStoreIds, true);
                }

                foreach ($conditionNode as $childNode) {
                    if (is_array($childNode) && $hasStoreCondition($childNode, $storeId)) {
                        return true;
                    }
                    if (is_object($childNode) && $hasStoreCondition((array)$childNode, $storeId)) {
                        return true;
                    }
                    if (is_array($childNode)) {
                        foreach ($childNode as $deepNode) {
                            if (is_array($deepNode) && $hasStoreCondition($deepNode, $storeId)) {
                                return true;
                            }
                        }
                    }
                }

                return false;
            };

            $hasAnyStoreCondition = static function ($conditionNode) use (&$hasAnyStoreCondition): bool {
                if (!is_array($conditionNode)) {
                    return false;
                }
                $classId = (string)($conditionNode['CLASS_ID'] ?? $conditionNode['ClassName'] ?? $conditionNode['class_id'] ?? '');

                if (
                    $classId !== ''
                    && mb_strpos($classId, 'CondCtrlProductOnWarehouse') !== false
                ) {
                    return true;
                }

                foreach ($conditionNode as $childNode) {
                    if (is_array($childNode) && $hasAnyStoreCondition($childNode)) {
                        return true;
                    }
                    if (is_object($childNode) && $hasAnyStoreCondition((array)$childNode)) {
                        return true;
                    }
                    if (is_array($childNode)) {
                        foreach ($childNode as $deepNode) {
                            if (is_array($deepNode) && $hasAnyStoreCondition($deepNode)) {
                                return true;
                            }
                        }
                    }
                }

                return false;
            };

            $extractPercentFromActions = static function (
                $actionNode,
                int $storeId,
                bool $allowWithoutStoreCondition,
                callable $hasStoreCondition,
                callable $hasAnyStoreCondition
            ) use (&$extractPercentFromActions): ?float {
                if (!is_array($actionNode)) {
                    return null;
                }

                $classId = (string)($actionNode['CLASS_ID'] ?? $actionNode['ClassName'] ?? $actionNode['class_id'] ?? '');
                if (
                    $classId !== ''
                    && mb_strpos($classId, 'ActSaleBsktGrp') !== false
                ) {
                    $value = 0.0;
                    foreach (['VALUE', 'value'] as $valueKey) {
                        if (isset($actionNode[$valueKey]) && (float)$actionNode[$valueKey] > 0) {
                            $value = (float)$actionNode[$valueKey];
                            break;
                        }
                    }
                    if ($value <= 0 && isset($actionNode['DATA']) && is_array($actionNode['DATA'])) {
                        foreach (['VALUE', 'Value', 'value'] as $valueKey) {
                            if (isset($actionNode['DATA'][$valueKey]) && (float)$actionNode['DATA'][$valueKey] > 0) {
                                $value = (float)$actionNode['DATA'][$valueKey];
                                break;
                            }
                        }
                    }

                    $unit = '';
                    foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                        if (!empty($actionNode[$unitKey])) {
                            $unit = (string)$actionNode[$unitKey];
                            break;
                        }
                    }
                    if ($unit === '' && isset($actionNode['DATA']) && is_array($actionNode['DATA'])) {
                        foreach (['UNIT', 'Unit', 'unit'] as $unitKey) {
                            if (!empty($actionNode['DATA'][$unitKey])) {
                                $unit = (string)$actionNode['DATA'][$unitKey];
                                break;
                            }
                        }
                    }
                    $hasStoreInAction = $hasAnyStoreCondition($actionNode);
                    $isStoreMatchedInAction = $hasStoreCondition($actionNode, $storeId);
                    if (
                        $value > 0
                        && (mb_strtolower($unit) === 'perc' || mb_strtolower($unit) === 'percent' || $unit === '')
                        && (
                            ($hasStoreInAction && $isStoreMatchedInAction)
                            || (!$hasStoreInAction && $allowWithoutStoreCondition)
                        )
                    ) {
                        return $value;
                    }
                }

                foreach ($actionNode as $childAction) {
                    if (is_array($childAction)) {
                        $foundPercent = $extractPercentFromActions(
                            $childAction,
                            $storeId,
                            $allowWithoutStoreCondition,
                            $hasStoreCondition,
                            $hasAnyStoreCondition
                        );
                        if ($foundPercent !== null) {
                            return $foundPercent;
                        }
                    }
                }

                return null;
            };

            $getStoreMarketingDiscount = static function (int $storeId) use (
                $hasStoreCondition,
                $hasAnyStoreCondition,
                $extractPercentFromActions,
                $normalizeDiscountTree,
                $decodeDiscountRuleTree
            ): array {
                static $discountRows = null;

                if ($discountRows === null) {
                    $discountRows = [];
                    $discountRes = CSaleDiscount::GetList(
                        ['PRIORITY' => 'DESC', 'SORT' => 'ASC', 'ID' => 'ASC'],
                        ['LID' => SITE_ID, 'ACTIVE' => 'Y'],
                        false,
                        false,
                        ['ID', 'CONDITIONS_LIST', 'ACTIONS_LIST', 'CONDITIONS', 'ACTIONS', 'PRIORITY', 'SORT', 'ACTIVE_FROM', 'ACTIVE_TO']
                    );

                    while ($discountRow = $discountRes->Fetch()) {
                        $discountRows[] = $discountRow;
                    }
                }

                $now = new \Bitrix\Main\Type\DateTime();

                foreach ($discountRows as $discountRow) {
                    if (!empty($discountRow['ACTIVE_FROM']) && $discountRow['ACTIVE_FROM'] instanceof \Bitrix\Main\Type\DateTime) {
                        if ($discountRow['ACTIVE_FROM']->getTimestamp() > $now->getTimestamp()) {
                            continue;
                        }
                    }
                    if (!empty($discountRow['ACTIVE_TO']) && $discountRow['ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                        if ($discountRow['ACTIVE_TO']->getTimestamp() < $now->getTimestamp()) {
                            continue;
                        }
                    }

                    $conditionsRaw = $discountRow['CONDITIONS_LIST'] ?? ($discountRow['CONDITIONS'] ?? []);
                    $actionsRaw = $discountRow['ACTIONS_LIST'] ?? ($discountRow['ACTIONS'] ?? []);
                    $conditions = $normalizeDiscountTree($decodeDiscountRuleTree($conditionsRaw));
                    $actions = $normalizeDiscountTree($decodeDiscountRuleTree($actionsRaw));

                    $hasStoreInConditions = $hasAnyStoreCondition($conditions);
                    $hasStoreInActions = $hasAnyStoreCondition($actions);
                    $isStoreMatchedInConditions = $hasStoreCondition($conditions, $storeId);
                    $isStoreMatchedInActions = $hasStoreCondition($actions, $storeId);

                    // Для markdown-позиций учитываем только акции, где явно есть складское условие.
                    // Иначе в модалке может подтягиваться "общая" акция с одинаковым ID для разных складов.
                    if (!$hasStoreInConditions && !$hasStoreInActions) {
                        continue;
                    }

                    if ($hasStoreInConditions && !$isStoreMatchedInConditions) {
                        continue;
                    }
                    if ($hasStoreInActions && !$isStoreMatchedInActions && !$isStoreMatchedInConditions) {
                        continue;
                    }

                    $allowWithoutStoreCondition = $isStoreMatchedInConditions || $isStoreMatchedInActions;
                    $percent = $extractPercentFromActions(
                        $actions,
                        $storeId,
                        $allowWithoutStoreCondition,
                        $hasStoreCondition,
                        $hasAnyStoreCondition
                    );
                    if ($percent !== null && $percent > 0) {
                        return [
                            'ID' => (int)$discountRow['ID'],
                            'PERCENT' => (float)$percent,
                        ];
                    }
                }

                return [];
            };

            $basketQuantitiesByMarkdownRow = [];
            if (\Bitrix\Main\Loader::includeModule('sale')) {
                $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), SITE_ID);
                $secondProductIdLookup = array_fill_keys($secondProductIds, true);
                foreach ($basket as $basketItem) {
                    $basketProductId = (int)$basketItem->getProductId();
                    if (empty($secondProductIdLookup[$basketProductId])) {
                        continue;
                    }

                    $basketStoreId = 0;
                    $basketRowKey = '';
                    $propertyCollection = $basketItem->getPropertyCollection();
                    if ($propertyCollection) {
                        $propertyValues = $propertyCollection->getPropertyValues();
                        if (is_array($propertyValues)) {
                            foreach ($propertyValues as $propertyValue) {
                                if (!is_array($propertyValue)) {
                                    continue;
                                }
                                $propertyCode = (string)($propertyValue['CODE'] ?? '');
                                $propertyDataValue = (string)($propertyValue['VALUE'] ?? '');
                                if ($propertyCode === 'MARKDOWN_ROW_KEY' && $propertyDataValue !== '') {
                                    $basketRowKey = $propertyDataValue;
                                } elseif ($propertyCode === 'MARKDOWN_STORE_ID' && (int)$propertyDataValue > 0) {
                                    $basketStoreId = (int)$propertyDataValue;
                                }
                            }
                        }
                    }

                    if ($basketRowKey === '' && $basketStoreId > 0) {
                        $basketRowKey = $basketProductId . '_' . $basketStoreId;
                    }
                    if ($basketRowKey !== '') {
                        $basketQuantitiesByMarkdownRow[$basketRowKey] = (float)$basketItem->getQuantity();
                    }
                }
            }

            $maxMarkdownPercent = 0.0;
            $minMarkdownPrice = null;
            $defaultMarkdownStoreId = 0;
            $defaultMarkdownPercent = null;
            foreach ($storeAmountsByProduct as $productId => $storeAmounts) {
                foreach ($storeAmounts as $storeId => $amount) {
                    if (empty($secondProducts[$productId])) {
                        continue;
                    }

                    $category = $storeCategories[$storeId] ?? '';
                    $pricesByProduct = $secondPrices[$productId] ?? [];
                    $measureRatio = (float)($secondMeasureRatios[$productId] ?? 1);
                    $selectedPrice = $getSelectedPriceByCategory($pricesByProduct, $category);
                    if (!empty($selectedPrice)) {
                        $selectedPrice['DISCOUNT_IDS'] = [];
                        $basePriceRounded = round((float)$selectedPrice['PRICE']);
                        $selectedPrice['PRICE'] = $basePriceRounded;
                        $selectedPrice['DISCOUNT_PRICE'] = $basePriceRounded;
                        $selectedPrice['PRINT'] = CCurrencyLang::CurrencyFormat($basePriceRounded, (string)$selectedPrice['CURRENCY']);
                        $selectedPrice['PRINT_OLD'] = '';
                    }
                    $storeDiscount = $getStoreMarketingDiscount((int)$storeId);
                    if (!empty($storeDiscount) && !empty($storeDiscount['PERCENT'])) {
                        $maxMarkdownPercent = max($maxMarkdownPercent, (float)$storeDiscount['PERCENT']);
                    }
                    if (
                        $arResult['IS_ORPHAN_MARKDOWN']
                        && (int)$productId === (int)$arResult['ID']
                    ) {
                        $storePercent = !empty($storeDiscount['PERCENT']) ? (float)$storeDiscount['PERCENT'] : 0.0;
                        if ($defaultMarkdownPercent === null || $storePercent > $defaultMarkdownPercent) {
                            $defaultMarkdownPercent = $storePercent;
                            $defaultMarkdownStoreId = (int)$storeId;
                        }
                    }
                    if (!empty($selectedPrice) && !empty($storeDiscount)) {
                        $basePrice = round((float)$selectedPrice['PRICE']);
                        $discountPrice = round($basePrice - ($basePrice * ((float)$storeDiscount['PERCENT'] / 100)));
                        $currency = (string)$selectedPrice['CURRENCY'];
                        $selectedPrice['DISCOUNT_PRICE'] = $discountPrice;
                        $selectedPrice['PRINT'] = CCurrencyLang::CurrencyFormat($discountPrice, $currency);
                        $selectedPrice['PRINT_OLD'] = ($basePrice !== $discountPrice)
                            ? CCurrencyLang::CurrencyFormat($basePrice, $currency)
                            : '';
                        $selectedPrice['DISCOUNT_IDS'] = [(int)$storeDiscount['ID']];
                    }
                    if (!empty($selectedPrice)) {
                        $candidate = $selectedPrice['DISCOUNT_PRICE'] ?? $selectedPrice['PRICE'] ?? null;
                        if ($candidate !== null) {
                            $candidate = round((float)$candidate);
                            $minMarkdownPrice = $minMarkdownPrice === null
                                ? $candidate
                                : min($minMarkdownPrice, $candidate);
                        }
                    }
                    $rowKey = $productId . '_' . $storeId;
                    $actualQuantity = isset($basketQuantitiesByMarkdownRow[$rowKey])
                        ? (float)$basketQuantitiesByMarkdownRow[$rowKey]
                        : 0;

                    $arResult['SECOND_ITEMS'][] = [
                        'ROW_KEY' => $rowKey,
                        'ID' => $productId,
                        'STORE_ID' => $storeId,
                        'CATEGORY' => $category,
                        'AMOUNT' => $amount,
                        'NAME' => $secondProducts[$productId]['NAME'],
                        'CML2_ARTICLE' => $secondProducts[$productId]['CML2_ARTICLE'],
                        'PRICES' => $pricesByProduct,
                        'SELECTED_PRICE' => $selectedPrice,
                        'MEASURE_RATIO' => $measureRatio,
                        'QUANTITY_TRACE' => $secondProductsData[$productId]['QUANTITY_TRACE'] ?? 'N',
                        'CAN_BUY_ZERO' => $secondProductsData[$productId]['CAN_BUY_ZERO'] ?? 'N',
                        'TOTAL_QUANTITY' => $amount,
                        'ACTUAL_QUANTITY' => $actualQuantity,
                    ];
                }
            }

            if ($arResult['IS_ORPHAN_MARKDOWN']) {
                if ($defaultMarkdownStoreId <= 0 && !empty($storeAmountsByProduct[$arResult['ID']])) {
                    $storesForItem = $storeAmountsByProduct[$arResult['ID']];
                    reset($storesForItem);
                    $defaultMarkdownStoreId = (int)key($storesForItem);
                }
                $arResult['MARKDOWN_DEFAULT_STORE_ID'] = $defaultMarkdownStoreId;
                $arResult['MARKDOWN_DEFAULT_ROW_KEY'] = $defaultMarkdownStoreId > 0
                    ? ($arResult['ID'] . '_' . $defaultMarkdownStoreId)
                    : '';
            }

            if (!empty($arResult['SECOND_ITEMS'])) {
                $arResult['HAS_SECOND'] = true;
            }

            if (
                $arResult['IS_ORPHAN_MARKDOWN']
                && $minMarkdownPrice !== null
                && !empty($arResult['PRICE_MATRIX']['MATRIX'])
            ) {
                foreach ($arResult['PRICE_MATRIX']['MATRIX'] as $priceId => $ranges) {
                    foreach ($ranges as $rangeKey => $priceDetails) {
                        if (!isset($priceDetails['PRICE'])) {
                            continue;
                        }
                        $basePrice = round((float)$priceDetails['PRICE']);
                        $discountPrice = $minMarkdownPrice;
                        $arResult['PRICE_MATRIX']['MATRIX'][$priceId][$rangeKey]['DISCOUNT_PRICE'] = $discountPrice;
                        $arResult['PRICE_MATRIX']['MATRIX'][$priceId][$rangeKey]['BASE_PRICE'] = $basePrice;
                    }
                }

                $printPrices = [];
                $cols = $arResult['PRICE_MATRIX']['COLS'] ?? [];
                $matrix = $arResult['PRICE_MATRIX']['MATRIX'];
                foreach ($matrix as $key => $value) {
                    $colName = $cols[$key]['NAME'] ?? $key;
                    $printPrices[$colName] = $value;
                    foreach ($value as $range => $priceDetails) {
                        $printPrices[$colName][$range]['PRINT'] = CCurrencyLang::CurrencyFormat(
                            $arResult["CATALOG_MEASURE_RATIO"] ? $priceDetails['DISCOUNT_PRICE'] * $arResult["CATALOG_MEASURE_RATIO"] : $priceDetails['DISCOUNT_PRICE'],
                            $priceDetails['CURRENCY']
                        );
                        if (round($priceDetails['DISCOUNT_PRICE'], 2) !== round($priceDetails['PRICE'], 2)) {
                            $printPrices[$colName][$range]['PRINT_WHITHOUT_DISCONT'] = CCurrencyLang::CurrencyFormat(
                                $arResult["CATALOG_MEASURE_RATIO"] ? $priceDetails['PRICE'] * $arResult["CATALOG_MEASURE_RATIO"] : $priceDetails['PRICE'],
                                $priceDetails['CURRENCY']
                            );
                        } else {
                            $printPrices[$colName][$range]['PRINT_WHITHOUT_DISCONT'] = '';
                        }
                    }
                }
                $arResult['PRINT_PRICES'] = $printPrices;
            }
        }
    }
}
