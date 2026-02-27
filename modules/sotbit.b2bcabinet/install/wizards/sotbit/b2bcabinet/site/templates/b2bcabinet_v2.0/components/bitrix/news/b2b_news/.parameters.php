<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))
    return;

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(
    Array("sort"=>"asc", "name"=>"asc"),
    Array("ACTIVE"=>"Y","PROPERTY_TYPE"=>"F","MULTIPLE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));

while ($arr=$rsProp->Fetch())
{
    $arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
}

$arTemplateParameters = array(
    "SHOW_TAG_CLOUD" => Array(
        "PARENT" => "FILTER_SETTINGS",
        "NAME" => GetMessage("T_IBLOCK_SHOW_TAG_CLOUD"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
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
	"DISPLAY_AS_RATING" => Array(
		"NAME" => GetMessage("TP_CBIV_DISPLAY_AS_RATING"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"rating" => GetMessage("TP_CBIV_RATING"),
			"vote_avg" => GetMessage("TP_CBIV_AVERAGE"),
		),
		"DEFAULT" => "rating",
    "HIDDEN" => 'Y',
	),
    "PAGER_DESC_NUMBERING" => array(
        "HIDDEN" => 'Y',
    ),
	"TAGS_CLOUD_ELEMENTS" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("SEARCH_PAGE_ELEMENTS"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"PERIOD_NEW_TAGS" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("SEARCH_PERIOD_NEW_TAGS"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => ""
	),

    "NEWS_LIST_TEMPLATE" => Array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("NEWS_LIST_TEMPLATE"),
        "TYPE" => "LIST",
        "VALUES" => array(
            "vertical" => GetMessage("NEWS_LIST_TEMPLATE_VERTICAL"),
            "horizontal" => GetMessage("NEWS_LIST_TEMPLATE_HORIZONTAL"),
            "grid"=>GetMessage("NEWS_LIST_TEMPLATE_GRID")
        ),
        "DEFAULT" => "vertical",
    ),
	"TAGS_CLOUD_WIDTH" => array(
		"NAME" => GetMessage("SEARCH_WIDTH"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "100%",
        "HIDDEN" => 'Y',
	),
    "GALERY_SOURCE"=>array(
        "NAME"=>GetMessage("GALERY_SOURCE"),
        "TYPE" => "LIST",
        "VALUES" => $arProperty_LNS,
    ),
    "DETAIL_DISPLAY_TOP_PAGER" => array(
        "HIDDEN" => 'Y',
    ),
    "DETAIL_DISPLAY_BOTTOM_PAGER" => array(
        "HIDDEN" => 'Y',
    ),
    "DETAIL_PAGER_TITLE" => array(
        "HIDDEN" => 'Y',
    ),
    "DETAIL_PAGER_TEMPLATE" => array(
        "HIDDEN" => 'Y',
    ),
    "DETAIL_PAGER_SHOW_ALL" => array(
        "HIDDEN" => 'Y',
    ),
    "USE_CATEGORIES" => array(
        "HIDDEN" => 'Y',
    ),
    "USE_RATING" => array(
        "HIDDEN" => 'Y',
    ),
    "USE_REVIEW" => array(
        "HIDDEN" => 'Y',
    ),
    "USE_RSS" => array(
        "HIDDEN" => 'Y',
    ),
);
?>