<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arParameters = Array(
    "PARAMETERS" => Array(
        "PERSONAL_MANAGER_NAME_TEMPLATE" => array(
            "NAME" => GetMessage("GD_SOTBIT_CABINET_PERSONAL_MANAGER_NAME_TEMPLATE"),
            "TYPE" => "STRING",
            "DEFAULT" => "#NOBR##NAME# #LAST_NAME##/NOBR#"
        ),
    )
);