<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Config\Option;

\Bitrix\Main\Loader::includeModule('sale');

$useFilterSections = (\Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'CATALOG_SHOW_SECTIONS', 'FILTER',
        SITE_ID) === 'FILTER') ? true : false;

$PREVIEW_WIDTH = intval($arParams["PREVIEW_WIDTH"]);
if ($PREVIEW_WIDTH <= 0)
    $PREVIEW_WIDTH = 48;

$PREVIEW_HEIGHT = intval($arParams["PREVIEW_HEIGHT"]);
if ($PREVIEW_HEIGHT <= 0)
    $PREVIEW_HEIGHT = 48;

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

$arCatalogs = false;

$arResult["ELEMENTS"] = array();
$arResult["SEARCH"] = array();
foreach($arResult["CATEGORIES"] as $category_id => &$arCategory)
{
    foreach($arCategory["ITEMS"] as $i => $arItem)
    {
        if ($arItem["TYPE"] === 'all') {
            unset($arCategory["ITEMS"][$i]);
            continue;
        }
        if(isset($arItem["ITEM_ID"]))
        {
            $arResult["SEARCH"][] = &$arResult["CATEGORIES"][$category_id]["ITEMS"][$i];
            if (
                $arItem["MODULE_ID"] == "iblock"
                && mb_substr($arItem["ITEM_ID"], 0, 1) !== "S"
            )
            {
                if ($arCatalogs === false)
                {
                    $arCatalogs = array();
                    if (CModule::IncludeModule("catalog"))
                    {
                        $rsCatalog = CCatalog::GetList(array(
                            "sort" => "asc",
                        ));
                        while ($ar = $rsCatalog->Fetch())
                        {
                            if ($ar["PRODUCT_IBLOCK_ID"])
                                $arCatalogs[$ar["PRODUCT_IBLOCK_ID"]] = 1;
                            else
                                $arCatalogs[$ar["IBLOCK_ID"]] = 1;
                        }
                    }
                }

                if (array_key_exists($arItem["PARAM2"], $arCatalogs))
                {
                    $arResult["ELEMENTS"][$arItem["ITEM_ID"]] = $arItem["ITEM_ID"];
                }
            }
        }
    }
}

if (!empty($arResult["ELEMENTS"]) && CModule::IncludeModule("iblock"))
{
    $arConvertParams = array();
    if ('Y' == $arParams['CONVERT_CURRENCY'])
    {
        if (!CModule::IncludeModule('currency'))
        {
            $arParams['CONVERT_CURRENCY'] = 'N';
            $arParams['CURRENCY_ID'] = '';
        }
        else
        {
            $arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
            if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo)))
            {
                $arParams['CONVERT_CURRENCY'] = 'N';
                $arParams['CURRENCY_ID'] = '';
            }
            else
            {
                $arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
                $arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
            }
        }
    }

    $obParser = new CTextParser;

    if (is_array($arParams["PRICE_CODE"]))
        $arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices(0, $arParams["PRICE_CODE"]);
    else
        $arResult["PRICES"] = array();

    $arSelect = array(
        "ID",
        "IBLOCK_ID",
        "PREVIEW_TEXT",
        "PREVIEW_PICTURE",
        "DETAIL_PICTURE",
        "IBLOCK_SECTION_ID",
        "MEASURE",
        "NAME",
    );
    if ($arParams["PROPERTY_ARTICLE"]) {
        array_push($arSelect, "PROPERTY_" . $arParams["PROPERTY_ARTICLE"]);
    }
    $arFilter = array(
        "IBLOCK_LID" => SITE_ID,
        "IBLOCK_ACTIVE" => "Y",
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
        "CHECK_PERMISSIONS" => "Y",
        "MIN_PERMISSION" => "R",
    );
    foreach($arResult["PRICES"] as $value)
    {
        $arSelect[] = $value["SELECT"];
        $arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
    }
    $arFilter["=ID"] = $arResult["ELEMENTS"];
    $arResult["ELEMENTS"] = [];
    $arMeasureId = [];
    $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while($arElement = $rsElements->Fetch())
    {
        if ($arElement["MEASURE"]) {
            $arMeasureId[] = $arElement["MEASURE"];
        }
        $arElement["PRICES"] = CIBlockPriceTools::GetItemPrices($arElement["IBLOCK_ID"], $arResult["PRICES"], $arElement, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
        if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
            $arElement["PREVIEW_TEXT"] = $obParser->html_cut($arElement["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

        $arResult["ELEMENTS"][$arElement["ID"]] = $arElement;
    }

    $dbRatio = \Bitrix\Catalog\MeasureRatioTable::getList([
        'filter' => ['PRODUCT_ID' => array_keys($arResult["ELEMENTS"])],
    ]);

    while($arRation = $dbRatio->fetch()) {
        $arResult["RATIO"][$arRation["PRODUCT_ID"]] = $arRation;
    }

    $dbMeasure = CCatalogMeasure::getList([], ["ID" => $arMeasureId], false, false, ["SYMBOL_RUS", "ID", "SYMBOL"]);

    while($arMeasure = $dbMeasure->fetch()) {
        $arResult["MEASURE"][$arMeasure["ID"]] = $arMeasure;
    }

    $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    foreach ($basket as $basketItem) {
        $arResult["PRODUCT_IN_BASKET"][] = $basketItem->getField("PRODUCT_ID");
    }
} else {
    unset($arResult["CATEGORIES"]["all"]);
}

foreach($arResult["SEARCH"] as $i=>$arItem)
{
    switch($arItem["MODULE_ID"])
    {
        case "iblock":
            if(array_key_exists($arItem["ITEM_ID"], $arResult["ELEMENTS"]))
            {
                $arElement = &$arResult["ELEMENTS"][$arItem["ITEM_ID"]];

                if ($arParams["SHOW_PREVIEW"] == "Y")
                {
                    if ($arElement["PREVIEW_PICTURE"] > 0)
                        $arElement["PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    elseif ($arElement["DETAIL_PICTURE"] > 0)
                        $arElement["PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    else {
                        $arElement["PICTURE"] = [
                            'src' => SITE_TEMPLATE_PATH . "/assets/images/no_photo.svg",
                            'width' => $PREVIEW_WIDTH,
                            'height' => $PREVIEW_HEIGHT,

                        ];
                    }
                }
            }
            break;
    }

    $arResult["SEARCH"][$i]["ICON"] = true;
}

if ($arResult["ELEMENTS"] && \Bitrix\Main\Loader::includeModule('sotbit.privateprice') && Option::get('sotbit.privateprice', 'MODULE_STATUS', 0)) {
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

    $productsHl = SotbitPrivatePriceMain::makeMainCheckFields(array_keys($arResult["ELEMENTS"]), $params);

    if ($productsHl) {
        $arResult["PRODUCT_PRIVATE_PRICE"] = [];
        foreach ($productsHl as $id => $item) {
            $arResult["PRODUCT_PRIVATE_PRICE"][$id] = CurrencyFormat(CCurrencyRates::ConvertCurrency($item[$params['PRICE_COLUMN']], $item[$params['CURRENCY_FORMAT']] ?: $item['PRICE_PRIVATE_CURRENCY'], $item['PRICE_PRIVATE_CURRENCY']), $item['PRICE_PRIVATE_CURRENCY']);
        }
    }
}

$useReplace = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS', 'N', SITE_ID) === 'Y';
$replaceValue = null;
if ($useReplace) {
    $replaceableValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACEABLE_LINKS_VALUE', 'catalog', SITE_ID);
    $replaceValue = Option::get(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_REPLACE_LINKS_VALUE', '/b2bcabinet/orders/blank_zakaza/', SITE_ID);
    foreach ($arResult['SEARCH'] as $key => $val) {
        if (!empty($arResult['SEARCH'][$key]['URL']))
            $arResult['SEARCH'][$key]['URL'] = str_replace($replaceableValue, $replaceValue, $arResult['SEARCH'][$key]['URL']);
    }
}

?>