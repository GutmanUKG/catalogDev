<?php

namespace Sotbit\B2bCabinet;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Seo\SitemapTable;
use Sotbit\B2bCabinet\Helper\Menu;

class EventHandlers
{

    const B2B_CATALOG_FOLDER = 'orders/blank_zakaza';

    public static function onBuildGlobalMenuHandler(&$arGlobalMenu, &$arModuleMenu)
    {
        Menu::getAdminMenu($arGlobalMenu, $arModuleMenu);
    }

    public static function onPageStart()
    {
//        define("NEED_AUTH", true);
        global $APPLICATION;

        if (strpos($APPLICATION->GetCurPage(false), '/bitrix') !== false) {
            return;
        }

        if (!\Bitrix\Main\Loader::includeModule('sotbit.b2bcabinet')) {
            return;
        }

        if (defined("SITE_TEMPLATE_ID")) {
            if (SITE_TEMPLATE_ID !== "b2bcabinet" && SITE_TEMPLATE_ID !== "b2bcabinet_v".\SotbitB2bCabinet::VERSION)
                return;
        }

        $access_mode = \COption::GetOptionString(\SotbitB2bCabinet::MODULE_ID, 'OPT_ACCESS_GROUPS', false, SITE_ID);

        if ($access_mode == "S") {
            define("NEED_AUTH", true);
        }
    }

    public static function deleteExcelFiles()
    {
        $storageTime = \COption::GetOptionString(\SotbitB2bCabinet::MODULE_ID, 'CATALOG_FILE_STORAGE_TIME', false, SITE_ID);

        if (!$storageTime) {
            return "\Sotbit\B2bCabinet\EventHandlers::deleteExcelFiles();";
        }

        $currentDate = new \Bitrix\Main\Type\DateTime();
        $stmp = MakeTimeStamp($currentDate->toString(), "DD.MM.YYYY HH:MI:SS");
        $filterDate = date("d.m.Y H:i:s", AddToTimeStamp(array("SS" => -$storageTime), $stmp));

        $dbFiles = \Bitrix\Main\FileTable::getList(
            [
                'filter' => [
                    "MODULE_ID" => "sotbit.b2bcabinet",
                    "<=TIMESTAMP_X" => $filterDate,
                    "DESCRIPTION" => "blank excel export"
                ],
                'select' => ["ID", "EXTERNAL_ID"]
            ]);

        while ($arFile = $dbFiles->fetch()) {
            \CFile::Delete($arFile["ID"]);
        }

        return "\Sotbit\B2bCabinet\EventHandlers::deleteExcelFiles();";
    }

    public static function check404Error()
    {
        if ((defined("ADMIN_SECTION") && ADMIN_SECTION == true)) {
            return;
        }

        if (defined("SITE_TEMPLATE_ID")) {
            if (SITE_TEMPLATE_ID !== "b2bcabinet" && SITE_TEMPLATE_ID !== "b2bcabinet_v".\SotbitB2bCabinet::VERSION)
                return;
        }

        if (defined('ERROR_404') && ERROR_404 == 'Y' || \CHTTP::GetLastStatus() == "404 Not Found") {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            require $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/header.php';
            $filePath = (\COption::GetOptionString(\SotbitB2bCabinet::MODULE_ID, 'VERSION_TEMPLATE', 'v1', SITE_ID) === 'v2') ? 
                SITE_TEMPLATE_PATH .'/404.php' :  
                '/include/b2b/template/404.php';
            require $_SERVER['DOCUMENT_ROOT'] . $filePath;
            require $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/footer.php';
        }
    }

    public static function onProlog()
    {
        if (Loader::includeModule("sotbit.auth") && Option::get("sotbit.auth", "EXTENDED_VERSION_COMPANIES", "N") === "Y") {
            define('EXTENDED_VERSION_COMPANIES', 'Y');
        } else{
            define('EXTENDED_VERSION_COMPANIES', 'N');
        }
    }

    public static function resourceAccessCheck()
    {
        global $USER;

        if (defined("SITE_TEMPLATE_ID")) {
            if (SITE_TEMPLATE_ID !== "b2bcabinet" && SITE_TEMPLATE_ID !== "b2bcabinet_v".\SotbitB2bCabinet::VERSION)
                return;
        }

        if ($USER->IsAuthorized() || (defined("ADMIN_SECTION") && ADMIN_SECTION == true)) {
            return;
        }

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if (!$USER->CanDoFileOperation('fm_view_file', array(SITE_ID, $request->getScriptFile()))) {
            \CMain::PrologActions();
            include($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/prolog_after.php");
            $filePath = (\COption::GetOptionString(\SotbitB2bCabinet::MODULE_ID, 'VERSION_TEMPLATE', 'v1', SITE_ID) === 'v2') ? 
                SITE_TEMPLATE_PATH .'/403.php' :  
                '/include/b2b/template/403.php';
            require $_SERVER['DOCUMENT_ROOT'] . $filePath;
            include($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog.php");
        }
    }

    public static function setCurrentProfileValue(&$arResult, &$arUserResult, $arParams)
    {
        if ((Loader::includeModule('sotbit.auth') && self::getVersionMode() == "Y") || !self::isB2BCabinet()) {
            return true;
        }

        $dbProfiles = \CSaleOrderUserProps::GetList(
            array("DATE_UPDATE" => "DESC"),
            array(
                "USER_ID" => $GLOBALS['USER']->GetID(),
                "PERSON_TYPE_ID" => $arParams['BUYER_PERSONAL_TYPE']
            )
        );

        while ($arrProfile = $dbProfiles->Fetch()) {
            $arProfiles[$arrProfile['ID']] = $arrProfile;
        }

        if (!$arProfiles) {
            return true;
        }

        $currentProfile = current($arProfiles);
        $arPersonTypeProfile = array_unique(array_column($arProfiles, 'PERSON_TYPE_ID'));

        $issetCheck = false;
        foreach ($arResult['PERSON_TYPE'] as $id => $person) {
            if (!in_array($id, $arParams['BUYER_PERSONAL_TYPE'])) {
                unset($arResult['PERSON_TYPE'][$id]);
                continue;
            }

            if ($person["CHECKED"] === "Y" && !in_array($id, $arPersonTypeProfile)) {
                unset($arResult['PERSON_TYPE'][$id]["CHECKED"]);
                continue;
            }

            if ($person["CHECKED"] === "Y" && in_array($id, $arPersonTypeProfile)) {
                $issetCheck = true;
            }
        }

        if (!$issetCheck) {
            $arResult['PERSON_TYPE'][$currentProfile["PERSON_TYPE_ID"]]['CHECKED'] = 'Y';
            $arUserResult['PERSON_TYPE_ID'] = $currentProfile["PERSON_TYPE_ID"];
        }
    }

    public static function setOrderPropertyValues(&$arResult, &$arUserResult, $arParams)
    {
        if ((Loader::includeModule('sotbit.auth') && self::getVersionMode() == "Y") || !self::isB2BCabinet()) {
            return true;
        }

        foreach ($arResult["ORDER_PROP"]["USER_PROPS_Y"] as $key => &$prop) {

            if ($prop["IS_ZIP"] === "Y" && $arUserResult["ZIP_PROPERTY_CHANGED"] === "N" && $arUserResult["DELIVERY_LOCATION_ZIP"]) {
                $arResult["ORDER_PROP"]["PRINT"][$key]["VALUE"] = $arUserResult["DELIVERY_LOCATION_ZIP"];
                $arUserResult["ORDER_PROP"][$key] = $arUserResult["DELIVERY_LOCATION_ZIP"];
                continue;
            }


            if ($prop["TYPE"] == "LOCATION") {
                if (Loader::includeModule('sotbit.regions') && isset($_SESSION["SOTBIT_REGIONS"]) && \Sotbit\Regions\Config\Option::get('INSERT_SALE_LOCATION', SITE_ID) === 'Y') {
                    $currentRegion = \Sotbit\Regions\Location\Domain::getCurrentLocation();
                    $locationId = $currentRegion["LOCATION"]["ID"];
                }
                if ($locationId) {
                    $arLocation = \CSaleLocation::GetByID($locationId);
                    $prop["VALUE"] = $locationId;
                    $arResult["ORDER_PROP"]["PRINT"][$key]["VALUE"] = $arLocation["COUNTRY_NAME"];
                    $arUserResult["ORDER_PROP"][$key] = $arLocation["CODE"];
                }
            }
        }
    }

    public static function isB2BCabinet()
    {
        $arTmpPath = explode("/", SITE_TEMPLATE_PATH);
        return in_array(end($arTmpPath), ["b2bcabinet", "b2bcabinet_v".\SotbitB2bCabinet::VERSION]);
    }

    public static function getVersionMode()
    {
        return Option::get(\SotbitAuth::idModule, "EXTENDED_VERSION_COMPANIES", "N");
    }

    public static function setTemplateSiteRequset() {
        if (isset($_REQUEST["sotbit_set_site_template"]) && is_string($_REQUEST["sotbit_set_site_template"]) && $_REQUEST["sotbit_set_site_template"] <> "") {
            $signer = new \Bitrix\Main\Security\Sign\Signer();
            try {
                $requestTemplate = $signer->unsign($_REQUEST["sotbit_set_site_template"], "template_preview".bitrix_sessid());
                $aTemplates = \CSiteTemplate::GetByID($requestTemplate);
                if($template = $aTemplates->Fetch())
                {
                    return $template["ID"];
                }
            } catch(\Bitrix\Main\Security\Sign\BadSignatureException $e) {
                
            }
        }
    }
}

?>