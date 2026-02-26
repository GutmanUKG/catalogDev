<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

CJSCore::Init('sidepanel');

include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/b2bcabinet/params_modifier.php';


$sort_field = (isset($_GET["SORT"]) && $_GET["SORT"]["CODE"]) ? $_GET["SORT"]["CODE"] : "NAME";
$sort_order = (isset($_GET["SORT"]) && $_GET["SORT"]["ORDER"]) ? $_GET["SORT"]["ORDER"] : "ASC";

if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y') {
    $basketAction = isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '';
} else {
    $basketAction = isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '';
}

$viewOffers = \Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'CATALOG_VIEW_OFFERS_VALUE', 'BLOCK', SITE_ID);
$showSearchCatalog = $APPLICATION->GetDirProperty('SHOW_CATALOG_SEARCH');

$isMarkdownOnly = (isset($_REQUEST['MARKDOWN_ONLY']) && $_REQUEST['MARKDOWN_ONLY'] === 'Y');
if ($isMarkdownOnly) {
    $filterName = (string)$arParams["FILTER_NAME"];
    if ($filterName !== '') {
        if (!isset($GLOBALS[$filterName]) || !is_array($GLOBALS[$filterName])) {
            $GLOBALS[$filterName] = [];
        }

        $getMarkdownLinkedProductIds = static function (int $catalogIblockId): array {
            static $cache = [];
            if (isset($cache[$catalogIblockId])) {
                return $cache[$catalogIblockId];
            }

            $resultIds = [];
            if (!\Bitrix\Main\Loader::includeModule('iblock') || $catalogIblockId <= 0) {
                $cache[$catalogIblockId] = [];
                return [];
            }

            $markdownArticles = [];
            $markdownRes = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => 37,
                    'ACTIVE' => 'Y',
                    '!PROPERTY_CML2_ARTICLE' => false
                ],
                false,
                false,
                ['ID', 'PROPERTY_CML2_ARTICLE']
            );

            while ($row = $markdownRes->Fetch()) {
                $article = trim((string)($row['PROPERTY_CML2_ARTICLE_VALUE'] ?? ''));
                if ($article !== '') {
                    $markdownArticles[$article] = $article;
                }
            }

            if (empty($markdownArticles)) {
                $cache[$catalogIblockId] = [];
                return [];
            }

            $catalogRes = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => $catalogIblockId,
                    'ACTIVE' => 'Y',
                    'PROPERTY_CML2_ARTICLE' => array_values($markdownArticles),
                ],
                false,
                false,
                ['ID']
            );

            while ($row = $catalogRes->Fetch()) {
                $id = (int)$row['ID'];
                if ($id > 0) {
                    $resultIds[$id] = $id;
                }
            }

            $cache[$catalogIblockId] = array_values($resultIds);
            return $cache[$catalogIblockId];
        };

        $markdownProductIds = $getMarkdownLinkedProductIds((int)$arParams["IBLOCK_ID"]);
        if (!empty($markdownProductIds)) {
            $GLOBALS[$filterName][] = ['ID' => $markdownProductIds];
        } else {
            $GLOBALS[$filterName][] = ['ID' => [0]];
        }
    }
}
?>
<?if ($isFilter):?>
    <!-- Right sidebar component 5-->
    <aside class="offcanvas-xxxl offcanvas-size-lg offcanvas-end
                catalog__filter
                smartfilter_wrapper"
           id="catalog__filter" aria-modal="true" role="dialog">
        <div class="sidebar-content bx_filter<?= (isset($arParams['FILTER_HIDE_ON_MOBILE']) && $arParams['FILTER_HIDE_ON_MOBILE'] === 'Y' ? ' hidden-xs' : '') ?>">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                // "onelab:catalog.smart.filter",
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
                    // "SEF_MODE" => $arParams["SEF_MODE"],
                    "SEF_MODE" => isset($_GET['q']) ? 'N' : $arParams["SEF_MODE"],
                    "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arCurSection['ID'] ? $arResult["VARIABLES"]["SMART_FILTER_PATH"] : $arResult["VARIABLES"]["SECTION_CODE_PATH"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    // "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                    // "AJAX_MODE" => isset($_GET['q']) ? 'Y' : 'N',
                    "AJAX_MODE" => 'N',
                    "INSTANT_RELOAD" => "N"
                ),
                $component,
                false
            );
            ?>
        <!-- Из-за этого дива после фильтрации иногда ломается страница -->
        <!-- </div> -->
    </aside>
<? endif ?>

<?$session = \Bitrix\Main\Application::getInstance()->getSession();?>

<div id="card__catalog__section-wrapper" class="catalog__section-wrapper <?if($session['FULLSCREEN'] == "Y"):?>card-fullscreen<?endif;?>">
    <div class="sticky-panel">

        <? if($showSearchCatalog !== "N"):?>
        <section class="catalog__search">
            <? $APPLICATION->IncludeComponent(
                "onelab:search.title",
                "b2b_catalog_search",
                array(
                    "COMPONENT_TEMPLATE" => "b2b_catalog_search",
                    "NUM_CATEGORIES" => "1",
                    "TOP_COUNT" => $arParams["SEARCH_PAGE_RESULT_COUNT"] ?: 5,
                    "ORDER" => "rank",
                    "USE_LANGUAGE_GUESS" => "N", // custom Y
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
                    "PREVIEW_WIDTH" => "74",
                    "PREVIEW_HEIGHT" => "74",
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
        <?endif;?>

        <? if ($isFilter): ?>
            <div class="flex-md-grow-1">
                <div 
                    data-bs-toggle="offcanvas" 
                    data-bs-target="#catalog__filter" 
                    class="catalog__filter-toggler btn btn-icon btn-primary <?=$positionSideBar !== 'LEFT' ? 'catalog__filter-toggler-position-left' : ''?>"
                >
                    <i class="ph-funnel catalog__filter-toggler-icon"></i>
                </div>
            </div>
        <? endif; ?>
       
        <?
            $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            // list($path, $query) = explode('?', $url);
            // parse_str($query, $q);
            // unset($q['bxajaxid']);
            // $query = http_build_query($q);

            // if(!$query) {
            //     $url = "{$path}";
            // }
            // else {
            //     $url = "{$path}?$query";
            // }

            $parts = parse_url($url);
            parse_str($parts['query'], $query);

            if($query['q'] || $query['SORT'] || $query['PAGEN_1'] || $query['SIZEN_1']) {
                $simbol = "&";
                $urlReplace = str_replace(array("?EL_COUNT=10&", "?EL_COUNT=20&", "?EL_COUNT=30&"), "?", $url);
                $urlReplace = str_replace($simbol."EL_COUNT=".$query['EL_COUNT'], "", $urlReplace);
            }
            else {
                $simbol = "?";
                $urlReplace = str_replace($simbol."EL_COUNT=".$query['EL_COUNT'], "", $url);
            }

            $rsUser = CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();

            if($query['EL_COUNT'] == 20) {
                $user = new CUser;
                $user->Update($arUser["ID"], array("UF_PROD_ON_PAGE_QUANTITY" => $query['EL_COUNT']));

                $elCount = $query['EL_COUNT'];
                $dDMenuContent = '
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=10">10</a>
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=30">30</a>
                ';
            }
            elseif($query['EL_COUNT'] == 30) {
                $user = new CUser;
                $user->Update($arUser["ID"], array("UF_PROD_ON_PAGE_QUANTITY" => $query['EL_COUNT']));

                $elCount = $query['EL_COUNT'];
                $dDMenuContent = '
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=10">10</a>
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=20">20</a>
                ';
            }
            elseif($query['EL_COUNT'] == 10) {
                $user = new CUser;
                $user->Update($arUser["ID"], array("UF_PROD_ON_PAGE_QUANTITY" => $query['EL_COUNT']));

                $elCount = $query['EL_COUNT'];
                $dDMenuContent = '
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=20">20</a>
                    <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=30">30</a>
                ';
            }
            elseif(!$query['EL_COUNT']) {
                if($arUser["UF_PROD_ON_PAGE_QUANTITY"] == 20) {
                    $elCount = $arUser["UF_PROD_ON_PAGE_QUANTITY"];
                    $dDMenuContent = '
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=10">10</a>
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=30">30</a>
                    ';
                }
                elseif($arUser["UF_PROD_ON_PAGE_QUANTITY"] == 30) {
                    $elCount = $arUser["UF_PROD_ON_PAGE_QUANTITY"];
                    $dDMenuContent = '
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=10">10</a>
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=20">20</a>
                    ';
                }
                elseif($arUser["UF_PROD_ON_PAGE_QUANTITY"] == 10 || $arUser["UF_PROD_ON_PAGE_QUANTITY"] == 0){
                    $elCount = 10;
                    $dDMenuContent = '
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=20">20</a>
                        <a class="dropdown-item" href="'.$urlReplace.$simbol.'EL_COUNT=30">30</a>
                    ';
                }
            }
            $APPLICATION->SetPageProperty("element_count", $elCount);

        ?>
        
        <div class="dropdown dropdown-el-count">
            <!-- <span>Количество <br>на странице:</span> -->
             <img src="/local/templates/b2bcabinet_v2.0/assets/icons/book.jpg" alt="Количество на странице">
            <button class="dropdown-toggle" type="button" id="dropdownMenuElCountButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=$elCount;?>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuElCountButton">
                <?=$dDMenuContent;?>
            </div>
        </div>
        <div class="count">
            <?$APPLICATION->ShowViewContent('els_count');?>
        </div>
    </div>
    
    <section class="catalog__section <?= $arParams["CATALOG_NOT_AVAILABLE"] === "Y" ? "catalog__section-not_available" : '' ?>">
        <?
        // Сортировка с поиском
        // if(empty($_GET["q"])) {
            $elem_sort_field = $sort_field ? : "NAME";
            $elem_sort_order = $sort_order ? : "ASC";
        // }
        // else {
        //     $elem_sort_field = $arParams["ELEMENT_SORT_FIELD"];
        //     $elem_sort_order = $arParams["ELEMENT_SORT_ORDER"];
        // }
        $sortFieldParam = $_GET["SORT"]["CODE"] ?? 'NAME';
        $sortOrderParam = $_GET["SORT"]["ORDER"] ?? 'asc';

        switch (mb_strtoupper($sortFieldParam)) {
            case 'PROPERTY_TRANSIT':
                $elem_sort_field = 'property_STORE_51';
                break;
            case 'QUANTITY':
                $elem_sort_field = 'quantity';
                break;
            case 'PRICE_7':
                $elem_sort_field = 'property_MINIMUM_PRICE';
                break;
            case 'PRICE_8':
                $elem_sort_field = 'catalog_PRICE_8';
                break;
            case 'NAME':
            default:
                $elem_sort_field = 'name';
                break;
        }

        $elem_sort_order = (mb_strtolower($sortOrderParam) === 'asc,nulls' || mb_strtolower($sortOrderParam) === 'asc') ? 'asc' : 'desc';
        $intSectionID = $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "",
            array(
                "BASKET_STATE" => Sotbit\B2BCabinet\Catalog\Basket::getBasketItemsQuantity(), //$arBasketItems,
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "AJAX_MODE" => $arParams["AJAX_MODE"],
                "AJAX_ID" => $arParams['AJAX_ID'],

                // "ELEMENT_SORT_FIELD" => $sort_field ? : "NAME",
                // "ELEMENT_SORT_ORDER" => $sort_order ? : "ASC",

                "ELEMENT_SORT_FIELD" => $elem_sort_field,
                "ELEMENT_SORT_ORDER" => $elem_sort_order,

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

                // "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],

                "PAGE_ELEMENT_COUNT" => $elCount,
                
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
               // "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                //"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),
                "OFFERS_VIEW" => $viewOffers,

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
    
    <section class="catalog__footer card-position-sticky">
        
        <div class="catalog__footer-wrapper">
        <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-05.png" class="info-ico-mob" >
            <div class="catalog__actions dropup <?= $USER->IsAuthorized() ? "" : "disabled" ?>">
                <button type="button" class="btn btn-actions" data-bs-toggle="dropdown" <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                    <i class="ph-dots-three-vertical"></i>
                    <?= Loc::getMessage("CT_BZ_ACTION_BUTTON") ?>
                </button>
                <div class="dropdown-menu">
                    <?/*$APPLICATION->IncludeComponent(
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
                    );*/?>
                    <?$APPLICATION->IncludeComponent(
	"sotbit:b2bcabinet.excel.export", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"MODEL_OF_WORK" => "default",
		"PRICE_CODE" => array(
			0 => "Цена дилерского портала KZT",
			1 => "RRP",
			2 => "",
		),
		"HEADERS_COLUMN" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "KOD_ATTR_S",
			2 => "CML2_ARTICLE",
			3 => "BREND_ATTR_S",
			4 => "",
		),
		"OFFERS_PROPERTY_CODE" => "",
		"SORT_BY" => "NAME",
		"SORT_ORDER" => "asc",
		"ONLY_AVAILABLE" => "Y",
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"SECTION_ID" => $arCurSection["ID"]?:$arResult["VARIABLES"]["SECTION_ID"]?:null,
		"USE_BTN" => "N",
		"ONLY_ACTIVE" => "Y"
	),
	false
);?>
                    <?$APPLICATION->IncludeComponent(
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
                    );?>
                </div>
            </div>

            
                <div class="catalog__footer-info catalog-left">
                    <div class="catalog__footer-info-row-top">
                        <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Вес">
                        <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-01.png" width="55px;">
                            <span id="catalog__footer-info-weight"></span>
                            <span class="catalog__footer-info-unit">кг</span>
                        </div>
                        <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Объём">
                            <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-03.png" width="55px">
                            <span id="catalog__footer-info-volume"></span>
                            <span class="catalog__footer-info-unit">м<sup>3</sup></span>
                        </div>
                        <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Общее количество коробок">
                            <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-04.png" width="55px">
                            <span id="catalog__footer-info-quantity"></span>
                            <span class="catalog__footer-info-unit">шт</span>
                        </div>
                        <div class="catalog__footer-info-item">
                            <div class="catalog__footer-info-ico-container">
                                <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-10.png" id="ico-boxman" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" >
                                <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-09.png" id="ico-auto-passenger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" >
                                <div class="truck-container truckb">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-06.png" class="truck-b" id="ico-auto-cargo-big" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="">
                                    <!-- <span class="truck-number" id="truck-big-number">0%</span> -->
                                </div>
                                <div class="truck-container truckm">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-07.png" class="truck-m" id="ico-auto-cargo-m" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="">
                                    <!-- <span class="truck-number" id="truck-medium-number">0%</span> -->
                                </div>
                                <div class="truck-container trucks">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-08.png" class="truck-s" id="ico-auto-cargo-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="">
                                    <!-- <span class="truck-number" id="truck-small-number">0%</span> -->
                                    
                                    
                                </div>
                                <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-05.png" id="info-ico-default" class="info-ico"  style="width: 50px; position: relative; margin-left: 43px; top: -1%" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" >
                                <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-05.png" id="info-ico-boxman" class="info-ico"  style="width: 50px; position: relative; margin-left: 43px; top: -1%" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Вес и объем заказа позволяют осуществить доставку заказа вручную.">
                                <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-05.png" id="info-ico-car" class="info-ico"  style="width: 50px; position: relative; margin-left: 43px; top: -1%" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Заказ поместится в багажник легкового автомобиля.">
                            </div>
                        </div>
                        
                    </div>
                    

                        <div class="catalog__footer-info-row-bottom"></div>
                </div>
                <script>document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(e=>{new bootstrap.Tooltip(e)});</script>
        

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
                    <a class="catalog__basket-link btn btn-primary disabled" href="javascript:void(0);">
                        <?= Loc::getMessage('CT_BZ_BASKET_BUTTON') ?>
                    </a>
                <? else: ?>
                    <a class="catalog__basket-link btn btn-primary" href="<?= $arParams["BASKET_URL"] ?>">
                        <i class="ph-shopping-cart-simple me-2"></i>
                        <?= Loc::getMessage('CT_BZ_BASKET_BUTTON') ?>
                    </a>
                <? endif; ?>
            </div>
        </div>
    </section>

    <? $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID; ?>
</div>

<script>
    $(document).ready(function(){
        $('.blank-zakaza__pagination--top').appendTo('.sticky-panel');
    });
</script>
