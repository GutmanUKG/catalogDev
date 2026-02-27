<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Come from GetTemplateProps()
 * @param string $templateName
 * @param string $siteTemplate
 * @param array $arCurrentValues
 */

    $path2Handlers = __DIR__."/handlers/";
    CheckDirPath($path2Handlers);
    $arHandlers = array();
    if ($handle = opendir($path2Handlers))
    {
        while (($file = readdir($handle)) !== false)
        {
            if ($file == "." || $file == "..")
                continue;
            if (is_file($path2Handlers.$file) && mb_strtoupper(mb_substr($file, mb_strlen($file) - 4)) == ".PHP")
            {
                $name = $title = $icon_url_template = "";
                $sort = 0;
                include($path2Handlers.$file);
                if ($name <> '')
                {
                    $arItemsHandler[$name] = array(
                        "TITLE" => $title,
                        "ICON" => $icon_url_template,
                        "SORT" => intval($sort)
                    );
                }
            }
        }
    }

    foreach($arItemsHandler as $name=>$arSystem){
        if ($arSystem["TITLE"] <> '')
            $arBookmarkHandlerDropdown[$name] = $arSystem["TITLE"];
    }

    $arBookmarkHandlerDropdownTmp = $arBookmarkHandlerDropdown;
    if (LANGUAGE_ID != 'ru')
    {
        if (array_key_exists("vk", $arBookmarkHandlerDropdownTmp))
            unset($arBookmarkHandlerDropdownTmp["vk"]);
        if (array_key_exists("mailru", $arBookmarkHandlerDropdownTmp))
            unset($arBookmarkHandlerDropdownTmp["mailru"]);
    }
    $arBookmarkHandlerDropdownDefault = array_keys($arBookmarkHandlerDropdownTmp);

$arHandlers=array(
    "HANDLERS" => $arBookmarkHandlerDropdown,
    "HANDLERS_DEFAULT" => $arBookmarkHandlerDropdownDefault
);

$arTemplateParameters= array(
	"HIDE" => array(
		"NAME" => GetMessage("BOOKMARK_HIDE"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
		"DEFAULT" => "N",
	),
	"HANDLERS" => array(
		"NAME" => GetMessage("BOOKMARK_SYSTEM"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arHandlers["HANDLERS"],
		"DEFAULT" => $arHandlers["HANDLERS_DEFAULT"],
		"REFRESH"=> "Y",
	),
	"PAGE_URL" => array(
		"NAME" => GetMessage("BOOKMARK_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"PAGE_TITLE" => array(
		"NAME" => GetMessage("BOOKMARK_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
);

if (
	(
		is_array($arCurrentValues["HANDLERS"])
		&& in_array("twitter", $arCurrentValues["HANDLERS"])
	)
	|| (
		empty($arCurrentValues["HANDLERS"])
		&& is_array($arHandlers["HANDLERS_DEFAULT"])
		&& in_array("twitter", $arHandlers["HANDLERS_DEFAULT"])
	)
)
{
	$arTemplateParameters["SHORTEN_URL_LOGIN"] = array(
		"NAME" => GetMessage("BOOKMARK_SHORTEN_URL_LOGIN"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);

	$arTemplateParameters["SHORTEN_URL_KEY"] = array(
		"NAME" => GetMessage("BOOKMARK_SHORTEN_URL_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}
foreach($arCurrentValues['HANDLERS'] as $itemHandler)
{
    $arTemplateParameters["IMAGE_".mb_strtoupper($itemHandler)."_SRC"]=array(

        "NAME"=>GetMessage("IMAGE_SHARE_URL").$arTemplateParameters['HANDLERS']['VALUES'][$itemHandler],
        "TYPE"=>"FILE",
        "FD_TARGET" => "F",
        "FD_EXT" => "jpg,gif,bmp,png,jpeg,webp",
        "FD_UPLOAD" => true,
        "FD_USE_MEDIALIB" => true,
    );
}