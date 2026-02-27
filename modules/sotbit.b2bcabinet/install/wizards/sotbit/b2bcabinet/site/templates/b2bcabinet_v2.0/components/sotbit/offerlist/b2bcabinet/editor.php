<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);

$arParams["ID"] = $arResult["VARIABLES"]["OFFER_ID"] ?: $arResult["VARIABLES"]["ID"] ?: null;
$arParams["URL_TEMPLATES"] = $arResult["URL_TEMPLATES"];
$arParams["URL_TEMPLATES"]["print"] = htmlspecialcharsBack($arParams["URL_TEMPLATES"]["print"]);

$APPLICATION->IncludeComponent(
    "bitrix:ui.sidepanel.wrapper",
    ".default",
    array(
        'POPUP_COMPONENT_NAME' => "sotbit:offerlist.editor",
        'POPUP_COMPONENT_TEMPLATE_NAME' => "",
        'POPUP_COMPONENT_PARAMS' => $arParams,
        'USE_PADDING' => true,
        'POPUP_COMPONENT_PARENT' => $component,
    )
);