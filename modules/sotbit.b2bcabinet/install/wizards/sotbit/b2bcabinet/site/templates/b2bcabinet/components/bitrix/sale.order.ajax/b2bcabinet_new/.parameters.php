<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
    "LOCATION_OF_PROPERTIES" => array(
        "NAME" => GetMessage("T_LOCATION_OF_PROPERTIES"),
        "TYPE" => "LIST",
        "VALUES" => array(
            "AFTER" => GetMessage("T_LOCATION_OF_PROPERTIES_AFTER"),
            "BEFORE" => GetMessage("T_LOCATION_OF_PROPERTIES_BEFORE")),
        "DEFAULT" => "AFTER",
        "PARENT" => "BASE",
    ),
    "HIDDEN_BASKET" => array(
        "NAME" => GetMessage("T_HIDDEN_BASKET"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" =>"N",
        "PARENT" => "BASE",
    ),
);