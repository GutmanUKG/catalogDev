<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$APPLICATION->IncludeComponent(
    "sotbit:complaints.detail",
    ".default",
    array(
        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "SEF_MODE" => $arParams["SEF_MODE"],
        "PER_PAGE" => $arParams["PER_PAGE"],
        "ADD_CHAIN" => $arParams["ADD_CHAIN"],
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
        "DISPLAY_DETAIL_FIELDS" => $arParams["DISPLAY_DETAIL_FIELDS"],
        "DISPLAY_DETAIL_PROPERTIES" => $arParams["DISPLAY_DETAIL_PROPERTIES"],
        "DISPLAY_POSITIONS_PROPERTIES" => $arParams["DISPLAY_POSITIONS_PROPERTIES"],
        "PATH_TO_LIST" => $arResult["URL_TEMPLATES"]["list"],
        "DETAIL_URL" => $arResult["URL_TEMPLATES"]["detail"],
        "ID" => $arResult["VARIABLES"]["ID"],
        "DATE_FORMAT" => $arParams["DATE_FORMAT"],
    ),
    $component
);
?>

