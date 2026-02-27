<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle(Loc::getMessage('COMPLAINTS'));

if(!Loader::includeModule('sotbit.b2bcabinet') || !Loader::includeModule('iblock') || Option::get('sotbit.complaints', 'INCLUDE_COMPLAINTS', 'N', SITE_ID) == "N")
{
    LocalRedirect(is_dir($_SERVER["DOCUMENT_ROOT"].'/b2bcabinet/') ? SITE_DIR.'b2bcabinet/' : SITE_DIR);
}

$APPLICATION->IncludeComponent(
	"sotbit:complaints", 
	"b2bcabinet", 
	array(
		"COMPONENT_TEMPLATE" => "b2bcabinet",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => Option::get("sotbit.complaints","IBLOCK_COMPLAINTS_ID","",SITE_ID),
		"SEF_MODE" => "Y",
		"PER_PAGE" => "10",
		"ADD_CHAIN" => "Y",
		"SET_TITLE" => "Y",
		"DISPLAY_PROPERTIES" => array(
			0 => "",
			1 => "COMPLAINT_STATUS",
			2 => "COMPLAINT_TYPE",
			3 => "",
		),
		"DISPLAY_FIELDS" => array(
			0 => "ID",
			1 => "NAME",
			2 => "DATE_CREATE",
			3 => "",
		),
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => Loc::getMessage('B2B_CABINET_COMPLAINTS'),
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"FILTER_NAME" => "filterComplaints",
		"SORT_BY1" => "ID",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "NAME",
		"SORT_ORDER2" => "ASC",
		"SEF_FOLDER" => "/complaints/",
		"IBLOCK_POSITION_TYPE" => "catalog",
		"IBLOCK_POSITION_ID" => Option::get("sotbit.complaints","IBLOCK_COMPLAINTS_POSITIONS_ID","",SITE_ID),
		"DISPLAY_DETAIL_PROPERTIES" => array(
			0 => "",
			1 => "COMPLAINT_STATUS",
			2 => "COMPLAINT_TYPE",
			3 => "COMPLAINT_FILE",
			4 => "",
		),
		"DISPLAY_DETAIL_FIELDS" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "DETAIL_TEXT",
			3 => "DATE_CREATE",
			4 => "CREATED_USER_NAME",
			5 => "",
		),
		"DISPLAY_POSITIONS_PROPERTIES" => array(
			0 => "",
			1 => "QUANTITY",
			2 => "DEGREE_DEFECT",
			3 => "DEFECT_OCCURRENCE",
			4 => "",
		),
		"DATE_FORMAT" => "d.M.Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"ADD_COMPLAINTS_FIELDS" => array(
			0 => "NAME",
			1 => "DETAIL_TEXT",
			2 => "",
		),
		"ADD_COMPLAINTS_PROPERTIES" => array(
			0 => "",
			1 => "COMPLAINT_TYPE",
			2 => "COMPLAINT_FILE",
			3 => "",
		),
		"ADD_POSITIONS_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ADD_POSITIONS_PROPERTIES" => array(
			0 => "",
			1 => "QUANTITY",
			2 => "DEGREE_DEFECT",
			3 => "DEFECT_OCCURRENCE",
			4 => "",
		),
		"ADD_PRODUCTS_IBLOCKS" => array(
			0 => "4",
			1 => "17",
			2 => "3",
			3 => "2",
		),
		"SEARCH_PRODUCTS_FIELDS" => array(
			0 => "CODE",
			1 => "NAME",
			2 => "PREVIEW_PICTURE",
			3 => "",
		),
		"SEARCH_PRODUCTS_PROPERTIES" => array(
			0 => "CML2_ARTICLE",
			1 => "CML2_MANUFACTURER",
			2 => "",
		),
		"SEF_URL_TEMPLATES" => array(
			"list" => "index.php",
			"detail" => "detail/#ID#/",
			"add" => "add/",
			"product_search" => "search/",
		)
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>