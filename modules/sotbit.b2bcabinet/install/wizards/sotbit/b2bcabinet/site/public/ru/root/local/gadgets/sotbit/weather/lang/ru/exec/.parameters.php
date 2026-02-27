<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\IO;
use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;

// $regionsFile = new IO\File(Application::getDocumentRoot() . '/include/yandex_regions_use.csv');
// $content = Encoding::convertEncodingToCurrent($regionsFile->getContents());
// $arContnet = explode(PHP_EOL, $content);

// $arCity = [];

// foreach ($arContnet as $value) {
// 	$lineAsArray = explode(';', $value);
// 	$id = $lineAsArray[0];
// 	unset($lineAsArray[0]);
// 	$arCity[sprintf('c%s', $id)] = implode(' ', $lineAsArray);
// }

// $arCity["c213"] = "Москва (Россия)";
// $arCity["c2"] = "Санкт-Петербург (Россия)";
// $arCity["c54"] = "Екатеринбург (Россия)";
// $arCity["c143"] = "Киев (Украина)";
// $arCity['c56'] = "Челяба";
// $arCity['c155'] = "Гомель, (Беларусь)";


$arParameters = Array(
	"PARAMETERS"=> Array(
		"CACHE_TIME" => array(
			"NAME" => "Время кеширования, сек (0-не кешировать)",
			"TYPE" => "STRING",
			"DEFAULT" => "3600"
			),
	"SHOW_URL" => Array(
			"NAME" => "Показывать ссылку на подробную информацию",
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
		),
	),
	"USER_PARAMETERS"=> Array(
		"CITY"=>Array(
			"NAME" => "Город",
			"TYPE" => "STRING",
		),
	),
);
