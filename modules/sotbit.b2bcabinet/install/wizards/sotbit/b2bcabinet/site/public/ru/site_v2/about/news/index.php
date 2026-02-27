<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if(!Loader::includeModule('sotbit.b2bcabinet'))
{
    header('Location: '.SITE_DIR);
}

$APPLICATION->SetTitle(Loc::getMessage('NEWS'));

$APPLICATION->IncludeComponent(
    "bitrix:news",
    "b2b_news",
    array(
        "ADD_ELEMENT_CHAIN" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "Y",
        "COLOR_NEW" => "3E74E6",
        "COLOR_OLD" => "C0C0C0",
        "COMPONENT_TEMPLATE" => "b2b_news",
        "DETAIL_ACTIVE_DATE_FORMAT" => "M j, Y",
        "DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
        "DETAIL_DISPLAY_TOP_PAGER" => "N",
        "DETAIL_FIELD_CODE" => array(
            0 => "TAGS",
            1 => "SHOW_COUNTER",
            2 => "CREATED_USER_NAME",
            3 => "",
        ),
        "DETAIL_PAGER_SHOW_ALL" => "Y",
        "DETAIL_PAGER_TEMPLATE" => "",
        "DETAIL_PAGER_TITLE" => "Страница",
        "DETAIL_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "DETAIL_SET_CANONICAL_URL" => "N",
        "DISPLAY_AS_RATING" => "rating",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "DISPLAY_TOP_PAGER" => "Y",
        "FILTER_FIELD_CODE" => array(
            0 => "NAME",
            1 => "DATE_ACTIVE_FROM",
            2 => "SHOW_COUNTER",
            3 => "CREATED_USER_NAME",
            4 => "",
        ),
        "FILTER_NAME" => "",
        "FILTER_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "FONT_MAX" => "50",
        "FONT_MIN" => "10",
        "GALERY_SOURCE" => "PICS_NEWS",
        "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
        "IBLOCK_ID" => "1",
        "IBLOCK_TYPE" => "news",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "LIST_ACTIVE_DATE_FORMAT" => "M j, Y",
        "LIST_FIELD_CODE" => array(
            0 => "TAGS",
            1 => "DATE_ACTIVE_FROM",
            2 => "SHOW_COUNTER",
            3 => "CREATED_USER_NAME",
            4 => "",
        ),
        "LIST_PROPERTY_CODE" => array(
            0 => "",
            1 => "",
        ),
        "MESSAGE_404" => "",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "NEWS_COUNT" => "10",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "Y",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "",
        "PAGER_TITLE" => "Новости",
        "PERIOD_NEW_TAGS" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "SEF_MODE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_TAG_CLOUD" => "Y",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "DESC",
        "SORT_ORDER2" => "ASC",
        "STRICT_SECTION_CHECK" => "N",
        "TAGS_CLOUD_ELEMENTS" => "150",
        "TAGS_CLOUD_WIDTH" => "100%",
        "USE_CATEGORIES" => "N",
        "USE_FILTER" => "Y",
        "USE_PERMISSIONS" => "N",
        "USE_RATING" => "N",
        "USE_REVIEW" => "N",
        "USE_RSS" => "N",
        "USE_SEARCH" => "Y",
        "USE_SHARE" => "N",
        "NEWS_LIST_TEMPLATE" => "grid",
        "VARIABLE_ALIASES" => array(
            "SECTION_ID" => "SECTION_ID",
            "ELEMENT_ID" => "ELEMENT_ID",
        )
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>