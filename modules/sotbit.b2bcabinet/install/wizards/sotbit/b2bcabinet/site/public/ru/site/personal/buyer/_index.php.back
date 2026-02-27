<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if(!Loader::includeModule('sotbit.b2bcabinet'))
{
    header('Location: '.SITE_DIR);
}

if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES == "Y"){
    if (!defined("ERROR_404"))
        define("ERROR_404", "Y");

    \CHTTP::setStatus("404 Not Found");

    if ($APPLICATION->RestartWorkarea()) {
        require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
        die();
    }
}
else
{
    $APPLICATION->IncludeComponent(
        "bitrix:sale.personal.profile",
        "b2bcabinet",
        array(
            "PER_PAGE" => "20",
            "SEF_MODE" => "Y",
            "SET_TITLE" => "N",
            "USE_AJAX_LOCATIONS" => "N",
            "COMPONENT_TEMPLATE" => "b2bcabinet",
            "SEF_FOLDER" => "/personal/buyer/",
            "BUYER_PERSONAL_TYPE" => unserialize(COption::GetOptionString("sotbit.b2bcabinet","BUYER_PERSONAL_TYPE","a:0:{}",SITE_ID)),
            "SEF_URL_TEMPLATES" => array(
                "list" => "profile_list.php",
                "detail" => "profile_detail.php?ID=#ID#",
            ),
            "VARIABLE_ALIASES" => array(
                "detail" => array(
                    "ID" => "ID",
                ),
            )
        ),
        false
    );
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>