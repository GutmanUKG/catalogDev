<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("SBD_NAME"),
	"DESCRIPTION" => GetMessage("SBD_DESCRIPTION"),
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "draft_add",
			"NAME" => GetMessage("SBD_NAME")
		)
	),
);
?>