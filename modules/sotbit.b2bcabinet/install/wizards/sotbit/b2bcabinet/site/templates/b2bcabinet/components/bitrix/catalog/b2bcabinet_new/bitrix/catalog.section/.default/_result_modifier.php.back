<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;
/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

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
            $productProperties[$propertyCode] = $property['NAME'];
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

// Collect all visible prices
if(is_array($arResult['PRICES']) && !empty($arResult['PRICES']))
{
    foreach ($arResult['PRICES'] as $key => $PRICE)
    {
        if($PRICE['CAN_VIEW'])
        {
            $priceProperties['PRICES'][$key]['NAME'] = (empty($PRICE['TITLE']) ? $PRICE['CODE'] : $PRICE['TITLE']);
            $priceProperties['PRICES'][$key]['ID'] = $PRICE['ID'];
        }
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

    if ($settings['WORK_MODE']) {
        $params["ADDITIONAL_USER_FIELDS"] = array();
        $additionalUserSettings = unserialize($settings['USERS_PARAMS']);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS']) as $key => $value) {
            array_push($params['ADDITIONAL_USER_FIELDS'], [$value => $additionalUserSettings[$key]]);
        }
    } else {
        $params["ADDITIONAL_SESSIONS_FIELDS"] = array();
        $additionalSessionSettings = unserialize($settings['SESSION_KEY']);
        if (empty(unserialize($settings['ADDITIONAL_PARAMS'])))
            return [];
        foreach (unserialize($settings['ADDITIONAL_PARAMS']) as $key => $value) {
            array_push($params['ADDITIONAL_SESSIONS_FIELDS'], [$value => $_SESSION[$additionalSessionSettings[$key]]]);
        }
    }
    $arResult['ITEMS_PRIVAT_PRICES'] = SotbitPrivatePriceMain::makeMainCheckFields($productsKey, $params);
    foreach ($arResult['ITEMS_PRIVAT_PRICES'] as $key => $val) {
        $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRIVAT_PRICE_PRINT'] = CurrencyFormat(CCurrencyRates::ConvertCurrency(
                $arResult["ITEMS"][array_search($key, $arResult["ELEMENTS"])]['CATALOG_MEASURE_RATIO'] ?
                    $arResult['ITEMS_PRIVAT_PRICES'][$key][$params["PRICE_COLUMN"]] *  $arResult["ITEMS"][array_search($key, $arResult["ELEMENTS"])]['CATALOG_MEASURE_RATIO'] :
                    $arResult['ITEMS_PRIVAT_PRICES'][$key][$params["PRICE_COLUMN"]],
                $arResult['ITEMS_PRIVAT_PRICES'][$key][$params["CURRENCY_FORMAT"]],
                $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']),
            $arResult['ITEMS_PRIVAT_PRICES'][$key]['PRICE_PRIVATE_CURRENCY']);
    }
    $arResult['PRIVAT_PRICES_PARAMS'] = $params;
}

unset (
    $arTableHeader,
    $arProductParams,
    $skuProperties,
    $productProperties
);