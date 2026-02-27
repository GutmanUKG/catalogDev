<?php
define("NEED_AUTH", true);

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if (!$USER->IsAuthorized()) {
	$APPLICATION->AuthForm('');
}

$methodIstall = Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR.'b2bcabinet/' : SITE_DIR;
if (isset($methodIstall) && strlen($methodIstall)>0)
	LocalRedirect($methodIstall);

$APPLICATION->SetTitle("Вход на сайт");
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>