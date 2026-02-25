<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_NAME" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_NAME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
    "IBLOCK_NEWS_ID_RU"=> Array(
        "NAME" => GetMessage('IBLOCK_NEWS_ID_RU'),
        "TYPE"=> "STRING",
        "DEFAULT" => ''
    ),
    "IBLOCK_NEWS_ID_EN"=> Array(
        "NAME" => GetMessage('IBLOCK_NEWS_ID_EN'),
        "TYPE"=> "STRING",
        "DEFAULT" => ''
    ),
    "IBLOCK_NEWS_ID_KZ"=> Array(
        "NAME" => GetMessage('IBLOCK_NEWS_ID_KZ'),
        "TYPE"=> "STRING",
        "DEFAULT" => ''
    )
);
