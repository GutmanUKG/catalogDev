<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Sotbit\B2bCabinet\Helper\Config;

if (!Loader::includeModule('sotbit.offerlist') || !SotbitOfferlist::getModuleEnable()) {
    header('Location: ' . SITE_DIR . 'orders/');
}

$APPLICATION->SetTitle(Loc::getMessage('TITLE'));

$APPLICATION->IncludeComponent(
    "sotbit:offerlist",
    "b2bcabinet",
    array(
        "COMPONENT_TEMPLATE" => "b2bcabinet",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/orders/offerlist/",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "SORT_BY" => "NAME",
        "SORT_ORDER" => "asc",
        "PROPERTY_ARTICLE" => "",
        "SEF_URL_TEMPLATES" => array(
            "list" => "index.php",
            "editor" => "editor/#OFFER_ID#/",
            "print" => "print/#OFFER_ID#/",
        )
    ),
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>