<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Sotbit\B2bCabinet\Helper\Config;

if (!Loader::includeModule('sotbit.offerlist') || !SotbitOfferlist::getModuleEnable()) {
    header('Location: ' . SITE_DIR);
}

$APPLICATION->SetTitle(Loc::getMessage('TITLE'));

$APPLICATION->IncludeComponent(
	"sotbit:offerlist", 
	"b2bcabinet", 
	array(
		"COMPONENT_TEMPLATE" => "b2bcabinet",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => "/offers/offerlist/",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SORT_BY" => "NAME",
		"SORT_ORDER" => "asc",
		"PROPERTY_ARTICLE" => "ARTNUMBER",
		"VARIABLE_ALIASES" => array(
			"OFFER_ID" => "OFFER_ID",
			"OFFER_PRINT" => "OFFER_PRINT",
		)
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>