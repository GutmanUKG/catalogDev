<?
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/main/utils.js');

global $AUTH_COMPANY_LIST_FILTER;
$filterOption = new Bitrix\Main\UI\Filter\Options( 'PERSONAL_PROFILE_LIST' );
$filterData = $filterOption->getFilter( [] );

$defaultFilter = [
	'ID',
	'NAME',
	'DATE_UPDATE',
	'DATE_UPDATE_from',
	'DATE_UPDATE_to',
	'BUYER_TYPE',
	'ACTIVE',
	'STATUS',
	'FIND'
];

if ($filterData) {
	foreach ($filterData as $key => $value) {
		if (in_array($key, $defaultFilter) && $value) {
			switch ($key) {
				case "FIND": {
					$AUTH_COMPANY_LIST_FILTER["%COMPANY.NAME"] = $value;
					break;
				}
				case "NAME": {
					$AUTH_COMPANY_LIST_FILTER["%COMPANY.NAME"] = $value;
					break;
				}
				case "DATE_UPDATE": {
					$AUTH_COMPANY_LIST_FILTER["COMPANY.ID"] = $value;
					break;
				}
				case 'DATE_UPDATE_from':
				{
					$AUTH_COMPANY_LIST_FILTER['>=COMPANY.DATE_UPDATE'] = $value;
					break;
				}
				case 'DATE_UPDATE_to':
				{
					$AUTH_COMPANY_LIST_FILTER['<=COMPANY.DATE_UPDATE'] = $value;
					break;
				}
				default: {
					$AUTH_COMPANY_LIST_FILTER["COMPANY." . $key] = $value;
					break;
				}
			}
		}
	}
}

$APPLICATION->IncludeComponent(
	"sotbit:auth.company.list",
	"b2bcabinet",
	array(
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"PER_PAGE" => $arParams["PER_PAGE"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"GRID_HEADER" => array(
			array("id"=>"ID", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_ID'), "sort"=>"ID", "default"=>false, "editable"=>false),
			array("id"=>"NAME", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_NAME'), "sort"=>"NAME", "default"=>true, "editable"=>false),
			array("id"=>"BUYER_TYPE_NAME", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_PERSON_TYPE_NAME'), "sort"=>"BUYER_TYPE", "default"=>true, "editable"=>false),
			array("id"=>"DATE_UPDATE", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_DATE_UPDATE'), "sort"=>"DATE_UPDATE", "default"=>true, "editable"=>false),
			array("id"=>"ACTIVE", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_ACTIVE'), "sort"=>"ACTIVE", "default"=>true, "editable"=>false),
			array("id"=>"STATUS", "name"=>Loc::getMessage('SOTBIT_B2BCABINET_ORGANIZATIONS_STATUS'), "sort"=>"STATUS", "default"=>true, "editable"=>false),
		),
		"BUYER_PERSONAL_TYPE" => $arParams['BUYER_PERSONAL_TYPE'],
		"FILTER_NAME" => 'AUTH_COMPANY_LIST_FILTER',
	),
	$component
);
?>
