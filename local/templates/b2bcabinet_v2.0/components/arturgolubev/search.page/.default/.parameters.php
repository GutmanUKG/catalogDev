<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPreviewTypes = [
	"" => GetMessage("CP_BSP_PREVIEW_TEXT_DEFAULT"),
	"DETAIL_TEXT" => GetMessage("CP_BSP_PREVIEW_TEXT_DETAIL"),
	"PREVIEW_TEXT" => GetMessage("CP_BSP_PREVIEW_TEXT_PREVIEW"),
];

$arTemplateParameters["SHOW_PROPS"] = [
	"NAME" => GetMessage("TP_BSP_SHOW_PROPS"),
	"TYPE" => "STRING",
	"DEFAULT" => "",
	"MULTIPLE" => "Y",
	"PARENT" => "VISUAL",
];

$arTemplateParameters["INPUT_PLACEHOLDER"] = [
	"NAME" => GetMessage("TP_BSP_INPUT_PLACEHOLDER"),
	"TYPE" => "STRING",
	"DEFAULT" => "",
	"PARENT" => "VISUAL",
];

$arTemplateParameters["SHOW_HISTORY"] = [
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BSP_SHOW_HISTORY"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
];

$arTemplateParameters["PREVIEW_TEXT"] = [
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BSP_PREVIEW_TEXT"),
	"TYPE" => "LIST",
	"VALUES" => $arPreviewTypes,
	"MULTIPLE" => "N",
	"DEFAULT" => ""
];
?>
