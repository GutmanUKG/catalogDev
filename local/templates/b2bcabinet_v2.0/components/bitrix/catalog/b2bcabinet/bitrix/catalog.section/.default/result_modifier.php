<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application,
    Bitrix\Main\Loader;
use Bitrix\Catalog\StoreProductTable;
/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */





$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arTableHeader = [
    'NAME' => Loc::getMessage('HEAD_NAME')
];

$arProductParams = [];
$skuProperties = [];
$productProperties = [];
$priceProperties = [];

$arBasketItems = $arParams['BASKET_STATE'];

// Collect all display props include SKU
foreach ($arResult['ITEMS'] as &$product) {
    if (is_array($product['DISPLAY_PROPERTIES'])) {
        foreach ($product['DISPLAY_PROPERTIES'] as $propertyCode => $property) {
            if ($propertyCode !== 'CML2_MANUFACTURER') {
                $productProperties[$propertyCode] = $property['NAME'];
            }
        }
    }

    if ($product['PRODUCT']['TYPE'] === 3) {
        foreach ($product['OFFERS'] as &$offer) {
            if (is_array($offer['DISPLAY_PROPERTIES'])) {
                foreach ($offer['DISPLAY_PROPERTIES'] as $propertyCode => $property) {
                    $skuProperties[$propertyCode] = $property['NAME'];
                }
            }
            $offer['ACTUAL_QUANTITY'] = !empty($arBasketItems[$offer['ID']]) ? $arBasketItems[$offer['ID']] : 0;

            if ($arParams['OFFERS_VIEW'] !== 'LIST') {
                $arTableHeader['OFFERS'] = Loc::getMessage('HEAD_OFFERS');
            }

            foreach ($offer['ITEM_QUANTITY_RANGES'] as $rangeName => $rangeProps ) {
                if ( $offer['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM']
                    && $offer['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
                ) {
                    $offer['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
                }
            }
        }
    } else {
        $product['ACTUAL_QUANTITY'] = !empty($arBasketItems[$product['ID']]) ? $arBasketItems[$product['ID']] : 0;

        foreach ($product['ITEM_QUANTITY_RANGES'] ?: [] as $rangeName => $rangeProps ) {
            if ( $product['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM']
                && $product['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
            ) {
                $product['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
            }
        }
    }
}






//Добавление к основному каталогу каталога уценки
global $USER;
if($USER->isAdmin()){
    $SALE_IBLOCK_ID = 37;

    $ARTICLES = [];
    foreach ($arResult['ITEMS'] as $ITEM) {
        $ARTICLES[] = $ITEM['PROPERTIES']['CML2_ARTICLE']['VALUE'];
    }

    $SALE_ITEMS = [];
    $res = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $SALE_IBLOCK_ID,
            'PROPERTY_CML2_ARTICLE' => $ARTICLES,
            'ACTIVE' => 'Y'
        ],
        false,
        false,
        [
            'ID',
            'IBLOCK_ID',
            'NAME',
            'PROPERTY_CML2_ARTICLE'
        ]
    );

    while($row = $res -> Fetch()){

        $SALE_ITEMS[$row['PROPERTY_CML2_ARTICLE_VALUE']] = $row;
    }

    foreach ($arResult['ITEMS'] as &$ITEM) {
        $article = $ITEM['PROPERTIES']['CML2_ARTICLE']['VALUE'];

        if(isset($SALE_ITEMS[$article])){

            $ITEM['HAS_SECOND'] = true;
            $ITEM['SECOND_ITEM'] = $SALE_ITEMS[$article];
        }else{
            $ITEM['HAS_SECOND'] = false;
        }
        if( $ITEM['HAS_SECOND']) {

        }
    }
    unset($ITEM);


}




if ($arParams["SHOW_MAX_QUANTITY"] !== "N") {
    $arTableHeader["AVALIABLE"] = Loc::getMessage('HEAD_AVAILABLE');
}

// Collect all visible prices
if(is_array($arResult['PRICES']) && !empty($arResult['PRICES']))
{
    foreach ($arResult['PRICES'] as $key => $PRICE)
    {
        // if($PRICE['CAN_VIEW'])
        // {
        $priceProperties['PRICES'][$key]['NAME'] = (empty($PRICE['TITLE']) ? $PRICE['CODE'] : $PRICE['TITLE']);
        $priceProperties['PRICES'][$key]['ID'] = $PRICE['ID'];
        // }
    }
}

// Add private price to prices
if (\Bitrix\Main\Loader::includeModule("sotbit.privateprice") && \Bitrix\Main\Config\Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && $GLOBALS["USER"]->IsAuthorized()) {
    $priceProperties['PRICES']['PRIVATE_PRICE']['NAME'] = Loc::getMessage('CT_BCS_CATALOG_MESS_PRIVATE_PRICE_TITLE');
    $priceProperties['PRICES']['PRIVATE_PRICE']['ID'] = '';
    $arResult["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"] = str_replace("PRODUCT_", "", \Bitrix\Main\Config\Option::get("sotbit.privateprice", "PRODUCT_UNIQUE_KEY", "ID"));
}

$arProductParams = array_merge($priceProperties, $skuProperties, $productProperties);

// Add quantity as a last child of header
if(is_array($arResult['PRICES']) && !empty($arResult['PRICES']))
{
    $arProductParams['QUANTITY'] = Loc::getMessage('HEAD_QUANTITY');
}

$arParams['TABLE_HEADER'] = array_merge($arTableHeader, $arProductParams);

if (Loader::includeModule("sotbit.privateprice")) {
    foreach($arResult['ITEMS'] as $val) {
        foreach ($val['OFFERS'] as $v) {
            $productsKey[] = $v['ID'];
        }
        $productsKey[] = $val['ID'];
    }

    $settings = SotbitPrivatePriceSettings::getInstance()->getOptions();
    $params = [
        "PRODUCT_COLUMN" => $settings["PRODUCT_COLUMN"],
        "PRICE_COLUMN" => $settings["PRICE_COLUMN"],
        "CURRENCY_FORMAT" => $settings["CURRENCY_FORMAT"],
        "PRODUCT_UNIQUE_KEY" => $settings["PRODUCT_UNIQUE_KEY"],
    ];



        // Собираем ID товаров/офферов
        $productIds = [];
        foreach ($arResult['ITEMS'] as $item) {
            if (!empty($item['OFFERS'])) {
                foreach ($item['OFFERS'] as $offer) {
                    $productIds[] = $offer['ID'];
                }
            } else {
                $productIds[] = $item['ID'];
            }
        }

        if ($productIds) {
            $res = StoreProductTable::getList([
                'filter' => ['=PRODUCT_ID' => $productIds],
                'select' => ['PRODUCT_ID', 'STORE_ID', 'AMOUNT']
            ]);
            $storeAmounts = [];
            while ($row = $res->fetch()) {
                $storeAmounts[$row['PRODUCT_ID']][$row['STORE_ID']] = (int)$row['AMOUNT'];
            }

            foreach ($arResult['ITEMS'] as &$item) {
                if (!empty($item['OFFERS'])) {
                    foreach ($item['OFFERS'] as &$offer) {
                        $offer['STORE_AMOUNT'] = $storeAmounts[$offer['ID']] ?? [];
                    }
                    unset($offer);
                } else {
                    $item['STORE_AMOUNT'] = $storeAmounts[$item['ID']] ?? [];
                }
            }
            unset($item);
        }
        /*
        // Сортировка именно по складу 51
        if ($_GET['SORT']['CODE'] === 'PROPERTY_TRANSIT') {
            $storeId = 51;

            uasort($arResult['ITEMS'], function ($a, $b) use ($storeId) {
                // считаем сумму остатков всех офферов на складе 51
                $aQty = 0;
                if (!empty($a['OFFERS'])) {
                    foreach ($a['OFFERS'] as $offer) {
                        $aQty += (int)($offer['STORE_AMOUNT'][$storeId] ?? 0);
                    }
                } else {
                    $aQty = (int)($a['STORE_AMOUNT'][$storeId] ?? 0);
                }

                $bQty = 0;
                if (!empty($b['OFFERS'])) {
                    foreach ($b['OFFERS'] as $offer) {
                        $bQty += (int)($offer['STORE_AMOUNT'][$storeId] ?? 0);
                    }
                } else {
                    $bQty = (int)($b['STORE_AMOUNT'][$storeId] ?? 0);
                }

                if ($_GET['SORT']['ORDER'] === 'asc,nulls') {
                    return $aQty <=> $bQty; // меньшее вперед
                } else {
                    return $bQty <=> $aQty; // большее вперед
                }
            });
        }

*/

    $sortOrder = strtolower($_GET['SORT']['ORDER'] ?? 'desc');

//    usort($arResult['ITEMS'], function($a, $b) use ($sortOrder) {
//        $valA = (int)$a['PROPERTIES']['STORE_51']['VALUE'];
//        $valB = (int)$b['PROPERTIES']['STORE_51']['VALUE'];
//
//        if ($sortOrder === 'asc' || $sortOrder === 'asc,nulls') {
//            return $valA <=> $valB;
//        } else {
//            return $valB <=> $valA;
//        }
//    });
    if ($settings['WORK_MODE']) {
        $params["ADDITIONAL_USER_FIELDS"] = array();
        $additionalUserSettings = unserialize($settings['USERS_PARAMS'], ['allowed_classes' => false]);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]) as $key => $value) {
            array_push($params['ADDITIONAL_USER_FIELDS'], [$value => $additionalUserSettings[$key]]);
        }
    } else {
        $params["ADDITIONAL_SESSIONS_FIELDS"] = array();
        $additionalSessionSettings = unserialize($settings['SESSION_KEY'], ['allowed_classes' => false]);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]) as $key => $value) {
            array_push($params['ADDITIONAL_SESSIONS_FIELDS'], [$value => $_SESSION[$additionalSessionSettings[$key]]]);
        }
    }
    $arResult['ITEMS_PRIVAT_PRICES'] = SotbitPrivatePriceMain::makeMainCheckFields($productsKey, $params);
    foreach ($arResult['ITEMS_PRIVAT_PRICES'] as $key => $val) {
        $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRIVAT_PRICE_PRINT'] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arResult['ITEMS_PRIVAT_PRICES'][$key][$params["PRICE_COLUMN"]], $arResult['ITEMS_PRIVAT_PRICES'][$key][$params["CURRENCY_FORMAT"]], $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']), $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']);
    }
    $arResult['PRIVAT_PRICES_PARAMS'] = $params;
}





unset (
    $arTableHeader,
    $arProductParams,
    $skuProperties,
    $productProperties
);

