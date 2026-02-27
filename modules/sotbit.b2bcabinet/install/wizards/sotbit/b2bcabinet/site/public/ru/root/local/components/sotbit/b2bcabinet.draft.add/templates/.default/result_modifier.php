<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Config\Option;
global $APPLICATION;
$methodIstall = Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR.'b2bcabinet/' : SITE_DIR;

$arResult["LINK_TO_DRAFTS_LIST"] =  $methodIstall . "orders/?tab=draft";
