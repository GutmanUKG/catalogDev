<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$arParams["SEF_URL_TEMPLATES"] = $arResult["URL_TEMPLATES"];

$APPLICATION->IncludeComponent(
    "sotbit:complaints.add",
    ".default",
    $arParams,
    $component
);

$APPLICATION->IncludeComponent(
    "sotbit:sotbit.b2bcabinet.notifications",
    "b2bcabinet",
    [],
    false
);
?>

