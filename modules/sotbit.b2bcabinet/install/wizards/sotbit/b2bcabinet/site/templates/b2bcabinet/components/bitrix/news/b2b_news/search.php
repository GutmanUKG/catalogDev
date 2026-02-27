<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:search.page",
    "tags",
    array(
        "CHECK_DATES" => $arParams["CHECK_DATES"] !== "N" ? "Y" : "N",
        "arrWHERE" => array("iblock_" . $arParams["IBLOCK_TYPE"]),
        "arrFILTER" => array("iblock_" . $arParams["IBLOCK_TYPE"]),
        "arrFILTER_iblock_" . $arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
        "SHOW_WHERE" => "N",
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "SET_TITLE" => $arParams["SET_TITLE"],
        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGE_RESULT_COUNT" => $arParams["NEWS_COUNT"],
        "PAGE_ELEMENTS" => $arParams["TAGS_CLOUD_ELEMENTS"],
        "PERIOD_NEW_TAGS" => $arParams["PERIOD_NEW_TAGS"],
        "WIDTH" => $arParams["TAGS_CLOUD_WIDTH"],
        "ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
        "DISPLAY_FIELDS" => $arParams["LIST_FIELD_CODE"],
        'DISPLAY_ACTIVE_FROM'=>$arParams["DISPLAY_FIELDS"]
    ),
    $component
); ?>
<div class="d-flex justify-content-between align-items-center">
    <a class="btn btn-light"
       href="<?= $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"] ?>"><?= GetMessage("T_NEWS_DETAIL_BACK") ?>
        <i class="icon-reload-alt ml-2"></i>
    </a>
</div>
