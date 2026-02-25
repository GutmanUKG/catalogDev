<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
    "NAME" => Loc::getMessage("SOTBIT_B2BCABINET_WHOLESALER_REGISTER_NAME"),
    "DESCRIPTION" => Loc::getMessage("SOTBIT_B2BCABINET_WHOLESALER_REGISTER_DESCRIPTION"),
    "PATH" => array(
        "ID" => "sotbit",
        "NAME" => Loc::getMessage("SOTBIT_COMPONENTS_TITLE"),
    ),
);
?>