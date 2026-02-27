<?php
use Bitrix\Main\Loader;
use Sotbit\B2bCabinet\Helper\Config;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Шаблоны заказов");

if(!Loader::includeModule('sotbit.b2bcabinet')){
    header('Location: '.SITE_DIR.'b2bcabinet/');
}


$APPLICATION->IncludeComponent(
"sotbit:b2bcabinet.ordertemplate",
".default",
array(
    "COMPONENT_TEMPLATE" => ".default",
    "IBLOCK_TYPE" => Config::get('CATALOG_IBLOCK_TYPE'),
    "IBLOCK_ID" => Config::get('CATALOG_IBLOCK_ID'),
    "LIST_PROPERTY_CODE" => array(
    ),
    "PRICE_CODE" => array(
        0 => "BASE",
    ),
    "SEF_MODE" => "N",
    "PER_PAGE" => "20",
    "SET_TITLE" => "Y",
    "ADD_CHAIN" => "Y",
    "SEF_FOLDER" => "/orders/templates/",
    "PRODUCTS_DETAIL_PATH" => SITE_DIR . "orders/blank_zakaza/?SECTION_ID=#SECTION_ID#&ELEMENT_ID=#ELEMENT_ID#"
),
false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>