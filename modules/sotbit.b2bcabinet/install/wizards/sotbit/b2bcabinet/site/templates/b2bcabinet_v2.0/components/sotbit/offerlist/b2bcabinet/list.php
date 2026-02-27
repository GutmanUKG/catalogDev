<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);

$APPLICATION->IncludeComponent(
    "sotbit:offerlist.list",
    ".default",
    [
        "URL_TEMPLATES" => $arResult["URL_TEMPLATES"],
        "PROPERTY_ARTICLE" => $arParams["PROPERTY_ARTICLE"],
        "EDITOR_INPUT" => $arParams["EDITOR_INPUT"],
        "SORT_BY" => $arParams["SORT_BY"],
        "SORT_ORDER" => $arParams["SORT_ORDER"],
    ],
    $component
);
?>