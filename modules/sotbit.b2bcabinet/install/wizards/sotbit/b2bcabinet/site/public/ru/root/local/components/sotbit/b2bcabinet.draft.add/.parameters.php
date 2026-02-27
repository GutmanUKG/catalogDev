<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
$arComponentParameters = Array(
    "PARAMETERS" => Array(
        "DELETE_BASKET" => Array(
            "NAME" => Loc::getMessage( "DELETE_BASKET" ),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "PARENT" => "ADDITIONAL_SETTINGS"
        ),
    )
);
?>