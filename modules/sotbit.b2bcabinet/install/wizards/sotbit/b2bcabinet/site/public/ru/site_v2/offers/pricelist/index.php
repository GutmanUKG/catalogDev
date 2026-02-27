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
	"sotbit:offerlist.pricelist", 
	"b2bcabinet",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SEF_MODE" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"PROPERTY_ARTICLE" => "",
		"VARIABLE_ALIASES" => array(
			"ELEMENT_ID" => "ID",
			"print" => "print",
		)
	),
	false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>