<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/b2bcabinet_new/params_modifier.php';

if (\Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'CATALOG_SECTION_ROOT_TEMPLATE', 'SECTIONS_LIST', SITE_ID) === 'PRODUCTS_LIST') {
    require $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/b2bcabinet_new/section.php';
    return;
}

include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/b2bcabinet_new/params_modifier.php';

$sectionListParams = array(
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
    "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
    "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
);
if ($sectionListParams["COUNT_ELEMENTS"] === "Y") {
    $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_ACTIVE";
}
if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
    $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_AVAILABLE";
}

?>
<div class="catalog">
    <div class="catalog__wrapper">
        <div id="card__catalog__section-wrapper" class="catalog__section-wrapper">
            <section class="catalog__search">
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
            </section>
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "whiteblock",
                $sectionListParams,
                $component,
                ($arParams["SHOW_TOP_ELEMENTS"] !== "N" ? array("HIDE_ICONS" => "Y") : array())
            );
            unset($sectionListParams);
            ?>
        </div>
    </div>
</div>