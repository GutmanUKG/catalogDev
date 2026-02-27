<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader;


if (Loader::includeModule("sotbit.auth")
    && Loader::includeModule("sale")
    && Loader::includeModule("sotbit.b2bcabinet")) {

    $modeCompanies = Option::get("sotbit.auth", "EXTENDED_VERSION_COMPANIES", "N");
    $componentName = $modeCompanies == "N" ? "sotbit:sotbit.auth.wholesaler.register" : "sotbit:auth.company.register";
    $componentTemplate = $modeCompanies == "N" ? "b2bcabinet" : "";

    $APPLICATION->IncludeComponent(
        $componentName,
        $componentTemplate,
        [
            "AUTH" => "Y",
            'AUTH_URL' => $arResult["AUTH_AUTH_URL"],
            "REQUIRED_FIELDS" => [
                "EMAIL"
            ],
            "REQUIRED_WHOLESALER_FIELDS" => [
                "EMAIL"
            ],
            "SET_TITLE" => "Y",
            "SHOW_FIELDS" => [
                "NAME",
                'LAST_NAME'
            ],
            "SHOW_WHOLESALER_FIELDS" => unserialize(Option::get('sotbit.b2bcabinet', 'OPT_REGISTER_FIELDS', '')),
            "SHOW_WHOLESALER_ORDER_FIELDS" => unserialize(Option::get('sotbit.b2bcabinet', 'OPT_REGISTER_ORDER_FIELDS',
                '')),
            "SUCCESS_PAGE" => Option::get('sotbit.b2bcabinet', 'method_install', '',
                SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . 'b2bcabinet/' : SITE_DIR,
            "USER_PROPERTY" => [
                'UF_CONFIDENTIAL'
            ],
            "USER_PROPERTY_NAME" => "",
            "USE_BACKURL" => "Y",
            "VARIABLE_ALIASES" => []
        ]);
} else {
    $APPLICATION->IncludeComponent(
        'sotbit:b2bcabinet.wholesaler.register',
        'b2bcabinet',
        [
            "AUTH" => "Y",
            'AUTH_URL' => $arResult["AUTH_AUTH_URL"],
            "REQUIRED_FIELDS" => [
                "EMAIL"
            ],
            "REQUIRED_WHOLESALER_FIELDS" => [
                "EMAIL"
            ],
            "SET_TITLE" => "Y",
            "SHOW_FIELDS" => [
                "NAME",
                'LAST_NAME'
            ],
            "SHOW_WHOLESALER_FIELDS" => unserialize(Option::get('sotbit.b2bcabinet', 'OPT_REGISTER_FIELDS', '')),
            "SHOW_WHOLESALER_ORDER_FIELDS" => unserialize(Option::get('sotbit.b2bcabinet', 'OPT_REGISTER_ORDER_FIELDS',
                '')),
            "SUCCESS_PAGE" => Option::get('sotbit.b2bcabinet', 'method_install', '',
                SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . 'b2bcabinet/' : SITE_DIR,
            "USER_PROPERTY" => [
                'UF_CONFIDENTIAL'
            ],
            "USER_PROPERTY_NAME" => "",
            "USE_BACKURL" => "Y",
            "VARIABLE_ALIASES" => []
        ]);
}
?>