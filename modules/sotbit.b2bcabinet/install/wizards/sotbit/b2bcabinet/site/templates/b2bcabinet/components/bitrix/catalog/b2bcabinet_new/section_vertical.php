<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

CJSCore::Init('sidepanel');

include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/b2bcabinet_new/params_modifier.php';

$sort_field = (isset($_GET["SORT"]) && $_GET["SORT"]["CODE"]) ? $_GET["SORT"]["CODE"] : "NAME";
$sort_order = (isset($_GET["SORT"]) && $_GET["SORT"]["ORDER"]) ? $_GET["SORT"]["ORDER"] : "ASC";

if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y') {
    $basketAction = isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '';
} else {
    $basketAction = isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '';
}

$positionSideBar = \Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'MENU_POSITION', 'LEFT', SITE_ID);

if ($isFilter):
    ?>
    <!-- Right sidebar component 5-->
    <aside class="catalog__filter sidebar sidebar-light bg-transparent sidebar-component
                sidebar-component-right border-0 shadow-0 sidebar-expand-md smartfilter_wrapper <?= $positionSideBar !== 'LEFT' ? 'catalog__filter-position-left' : '' ?>"
           id="catalog__filter">
        <button class="catalog__filter-toggler--close"
                onclick="BX.toggleClass('catalog__filter', ['','catalog__filter--open'])"><?= Loc::getMessage("CT_BZ_ACTION_BUTTON_CLOSE_FILTER") ?></button>
        <div class="sidebar-content bx_filter<?= (isset($arParams['FILTER_HIDE_ON_MOBILE']) && $arParams['FILTER_HIDE_ON_MOBILE'] === 'Y' ? ' hidden-xs' : '') ?>">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                "b2b_smart_filter",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arCurSection['ID'],
                    "PREFILTER_NAME" => $arParams["FILTER_NAME"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SAVE_IN_SESSION" => "N",
                    "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                    "XML_EXPORT" => "N",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    "SEF_MODE" => $arParams["SEF_MODE"],
                    "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arCurSection['ID'] ? $arResult["VARIABLES"]["SMART_FILTER_PATH"] : $arResult["VARIABLES"]["SECTION_CODE_PATH"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );
            ?>
        </div>
    </aside>
<? endif ?>

<div id="card__catalog__section-wrapper" class="catalog__section-wrapper">
    <section class="catalog__search">
        <? if ($positionSideBar !== 'LEFT'): ?>
            <div class="catalog__filter-toggler catalog__filter-toggler-position-left"
                 onclick="BX.toggleClass('catalog__filter', ['','catalog__filter--open'])"><i
                        class="icon-filter3 catalog__filter-toggler-icon"></i></div>
        <? endif; ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:search.title",
            "b2b_catalog_search",
            array(
                "COMPONENT_TEMPLATE" => "b2b_catalog_search",
                "NUM_CATEGORIES" => "1",
                "TOP_COUNT" => $arParams["SEARCH_PAGE_RESULT_COUNT"] ?: 5,
                "ORDER" => "rank",
                "USE_LANGUAGE_GUESS" => "Y",
                "CHECK_DATES" => "N",
                "SHOW_OTHERS" => "N",
                "PAGE" => $methodIstall . "orders/blank_zakaza/",
                "SHOW_INPUT" => "Y",
                "INPUT_ID" => "title-search-input",
                "CONTAINER_ID" => "title-search",
                "CATEGORY_0_TITLE" => "",
                "CATEGORY_0" => array(
                    0 => "iblock_" . $arParams["IBLOCK_TYPE"],
                ),
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "PRICE_VAT_INCLUDE" => "Y",
                "PREVIEW_TRUNCATE_LEN" => "",
                "SHOW_PREVIEW" => "Y",
                "CONVERT_CURRENCY" => "N",
                "PREVIEW_WIDTH" => "48",
                "PREVIEW_HEIGHT" => "48",
                "TEMPLATE_THEME" => "blue",
                "PROPERTY_ARTICLE" => $arParams["PROPERTY_ARTICLE"] ?? "CML2_ARTICLE",
                "CATEGORY_0_iblock_" . $arParams["IBLOCK_TYPE"] => array(
                    0 => $arParams["IBLOCK_ID"],
                ),
                "CATALOG_NOT_AVAILABLE" => $arParams["CATALOG_NOT_AVAILABLE"]
            ),
            false,
            ["HIDE_ICONS" => "Y"]
        ); ?>
        <? if ($positionSideBar === 'LEFT'): ?>
            <div class="catalog__filter-toggler"
                 onclick="BX.toggleClass('catalog__filter', ['','catalog__filter--open'])"><i
                        class="icon-filter3 catalog__filter-toggler-icon"></i></div>
        <? endif; ?>
    </section>

    <div class="card">
        <section
                class="catalog__section <?= $arParams["CATALOG_NOT_AVAILABLE"] === "Y" ? "catalog__section-not_available" : '' ?>">
            <?

            $intSectionID = $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "",
                array(
                    "BASKET_STATE" => Sotbit\B2BCabinet\Catalog\Basket::getBasketItemsQuantity(),//$arBasketItems,
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "AJAX_MODE" => $arParams["AJAX_MODE"],
                    "ELEMENT_SORT_FIELD" => $sort_field ?: "NAME",
                    "ELEMENT_SORT_ORDER" => $sort_order ?: "ASC",
                    // "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    // "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "PROPERTY_CODE" => (isset($arParams["LIST_PROPERTY_CODE"]) ? $arParams["LIST_PROPERTY_CODE"] : []),
                    "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
                    "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                    "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    "ELEMENT_ID_VARIABLE" => $arParams["VARIABLE_ALIASES"]["ELEMENT_ID"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "CATALOG_NOT_AVAILABLE" => $arParams["CATALOG_NOT_AVAILABLE"],
                    "SET_TITLE" => $arParams["SET_TITLE"],
                    "MESSAGE_404" => $arParams["~MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["~PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                    "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

                    "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                    "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                    "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                    "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "LAZY_LOAD" => $arParams["LAZY_LOAD"],
                    "MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
                    "LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

                    "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

                    'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
                    'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                    'HIDE_NOT_AVAILABLE_OFFERS' =>  $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

                    'LABEL_PROP' => $arParams['LABEL_PROP'],
                    'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
                    'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
                    'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                    'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
                    'PRODUCT_ROW_VARIANTS' => "[{'VARIANT':'0','BIG_DATA':false}]",
                    'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
                    'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
                    'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
                    'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
                    'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

                    'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                    'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                    'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
                    'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
                    'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
                    'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
                    'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
                    'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
                    'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
                    'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
                    'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
                    'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
                    'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

                    'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
                    'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
                    'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

                    'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                    "ARTICLE_PROPERTY" => $arParams["ARTICLE_PROPERTY"],
                    "ARTICLE_PROPERTY_OFFERS" => $arParams["ARTICLE_PROPERTY_OFFERS"],
                    "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
                    'ADD_TO_BASKET_ACTION' => $basketAction,
                    'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                    'USE_COMPARE_LIST' => 'Y',
                    'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
                    'SHOW_ALL_WO_SECTION' => 'Y',
                    "BY_LINK" => "N",
                    'LIST_SHOW_MEASURE_RATIO' => (isset($arParams['LIST_SHOW_MEASURE_RATIO'])
                        ? $arParams['LIST_SHOW_MEASURE_RATIO'] : ''),
                    'STORE_PATH' => $arParams['STORE_PATH'],
                    'MAIN_TITLE' => $arParams['MAIN_TITLE'],
                    'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
                    'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
                    'STORES' => $arParams['STORES'],
                    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
                    'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                    'USER_FIELDS' => $arParams['USER_FIELDS'],
                    'FIELDS' => $arParams['FIELDS'],
                    'USE_STORE' => $arParams['USE_STORE'],
                ),
                $component
            );
            ?>
        </section>
        <? global $USER; ?>
        <section class="b2b_table__footer">
            <div class="b2b_table__footer-wrapper">
                <div class="catalog__actions <?= $USER->IsAuthorized() ? "" : "disabled" ?>">
                    <button type="button" class="btn btn-light btn-actions" data-toggle="dropdown" <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                        <span class="ladda-label ">
                            <i class="icon-more2"></i>
                            <?= Loc::getMessage("CT_BZ_ACTION_BUTTON") ?>
                        </span>
                    </button>
                    <div class="dropdown-menu">
                        <?php
                        $APPLICATION->IncludeComponent(
                            "sotbit:b2bcabinet.excel.import",
                            ".default",
                            array(
                                "COMPONENT_TEMPLATE" => ".default",
                                "MULTIPLE" => "Y",
                                "MAX_FILE_SIZE" => "",
                                "USE_BUTTON" => 'N',
                                "USE_ICON" => 'Y',
                            ),
                            false
                        ); ?>
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:b2bcabinet.excel.export",
                            ".default",
                            array(
                                "COMPONENT_TEMPLATE" => ".default",
                                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "MODEL_OF_WORK" => "default",
                                "PRICE_CODE" => $arParams["PRICE_CODE"],
                                "HEADERS_COLUMN" => array(
                                    0 => "NAME",
                                    1 => "DETAIL_PICTURE",
                                    2 => "DATE_CREATE",
                                    3 => "",
                                ),
                                "PROPERTY_CODE" => array(
                                    0 => "",
                                    1 => "BRAND_REF",
                                    2 => "MATERIAL",
                                    3 => "COLOR",
                                    4 => "",
                                ),
                                "OFFERS_PROPERTY_CODE" => array(
                                    0 => "ARTNUMBER",
                                    1 => "COLOR_REF",
                                    2 => "SIZES_CLOTHES",
                                    3 => "",
                                ),
                                "SORT_BY" => "NAME",
                                "SORT_ORDER" => "asc",
                                "ONLY_AVAILABLE" => "Y",
                                "FILTER_NAME" => $arParams["FILTER_NAME"],
                                "SECTION_ID" => $arCurSection['ID'] ?: $arResult["VARIABLES"]["SECTION_ID"] ?: null,
                                "USE_BTN" => 'N'
                            ),
                            false
                        );
                        ?>
                    </div>
                </div>
                <div class="catalog__basket">
                    <div class="catalog__basket-wrapper">
                        <div class="catalog__basket-quantity">
                            <span class="catalog__basket-quantity-title"><?= Loc::getMessage('CT_BZ_BASKET_POSITIONS') ?></span>
                            <span class="catalog__basket-quantity-value" id="catalog__basket-quantity-value"></span>
                        </div>
                        <div class="catalog__basket-price">
                            <span class="catalog__basket-price-title"><?= Loc::getMessage('CT_BZ_BASKET_PRICE') ?></span>
                            <span class="catalog__basket-price-value" id="catalog__basket-price-value"></span>
                        </div>
                    </div>
                    <? if ($arParams["CATALOG_NOT_AVAILABLE"] === "Y"): ?>
                        <a class="catalog__basket-link disabled" href="javascript:void(0);">
                            <?= Loc::getMessage('CT_BZ_BASKET_BUTTON') ?>
                        </a>
                    <? else: ?>
                        <a class="catalog__basket-link" href="<?= $arParams["BASKET_URL"] ?>">
                            <?= Loc::getMessage('CT_BZ_BASKET_BUTTON') ?>
                        </a>
                    <? endif; ?>
                </div>

            </div>
        </section>

        <? $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID; ?>
    </div>
</div>