<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);


$APPLICATION->IncludeComponent(
    "sotbit:complaints.list",
    ".default",
    array(
        "ADD_CHAIN" => $arParams["ADD_CHAIN"],
        "SORT_BY1" => $arParams["SORT_BY1"],
        "SORT_ORDER1" => $arParams["SORT_ORDER1"],
        "SORT_BY2" => $arParams["SORT_BY2"],
        "SORT_ORDER2" => $arParams["SORT_ORDER2"],
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "FILTER_NAME" => $arParams["FILTER_NAME"],
        "SEF_MODE" => $arParams["SEF_MODE"],
        "PER_PAGE" => $arParams["PER_PAGE"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
        "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
        "DISPLAY_PROPERTIES" => $arParams["DISPLAY_PROPERTIES"],
        "DISPLAY_FIELDS" => $arParams["DISPLAY_FIELDS"],
        "IBLOCK_URL" => $arResult["URL_TEMPLATES"]["detail"],
        "DETAIL_URL" => $arResult["URL_TEMPLATES"]["detail"],
        "ADD_URL" => $arResult["URL_TEMPLATES"]["add"],
        "DATE_FORMAT" => $arResult["DATE_FORMAT"],
    ),
    $component
);
?>