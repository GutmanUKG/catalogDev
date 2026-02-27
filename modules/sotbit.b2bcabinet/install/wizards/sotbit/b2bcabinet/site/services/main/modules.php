<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!defined("WIZARD_SITE_ID") || !defined("WIZARD_SITE_DIR")) {
    return;
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$moduleName = 'sotbit.b2bcabinet';

if (!IsModuleInstalled("sotbit.b2bcabinet") && file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sotbit.b2bcabinet/")) {
    $installFile = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sotbit.b2bcabinet/install/index.php";
    if (!file_exists($installFile)) {
        return false;
    }

    include_once($installFile);

    $moduleIdTmp = str_replace(".", "_", "sotbit.b2bcabinet");
    if (!class_exists($moduleIdTmp)) {
        return false;
    }

    $module = new $moduleIdTmp;

    $module->InstallEvents();
    $module->InstallFiles();
    $module->InstallDB();
    RegisterModule("sotbit.b2bcabinet");
}

$modulesThear = array(
    'sotbit.bill',
    'sotbit.checkcompany',
    'sotbit.multibasket',
    'sotbit.offerlist'
);

if (IsModuleInstalled('sotbit.b2bshop') && (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sotbit.b2bshop/") || file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards/sotbit/b2bshop/"))) {
    $modulesB2bShop = array(
        'shs.parser',
        'sotbit.seometa',
        'sotbit.reviews',
        'sotbit.regions',
        'sotbit.opengraph',
        'sotbit.crosssell',
        'sotbit.schemaorg',
        'sotbit.htmleditoraddition',
        'sotbit.orderphone',
        'sotbit.seosearch'
    );

    $modulesThear = array_merge($modulesThear, $modulesB2bShop);
}

if (IsModuleInstalled('sotbit.b2bplus') && (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sotbit.b2bplus/") || file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards/sotbit/b2bplus/"))) {
    $modulesB2bPlus = array(
        'sotbit.auth',
        'sns.tools1c'
    );

    $modulesThear = array_merge($modulesThear, $modulesB2bPlus);
}

if (IsModuleInstalled('sotbit.b2bportal') && (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sotbit.b2bportal/") || file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards/sotbit/b2bportal/"))) {
    $modulesB2bPortal = array(
        'sotbit.regions',
        'sotbit.privateprice',
        'sotbit.complaints',
        'sotbit.notification',
        'sotbit.auth',
        'sotbit.exchange1c',
        'sns.tools1c',
    );

    $modulesThear = array_merge($modulesThear, $modulesB2bPortal);
}

if (!function_exists("installModuleHands")) {

    function installModuleHands($module, $modulesThear)
    {

        $obModule = CModule::CreateModuleObject($module);
        if (!is_object($obModule)) {
            return false;
        }

        if (!$obModule->IsInstalled()) {
            if (in_array($module, array('sotbit.auth'))) {
                $obModule->InstallFiles();
                $obModule->InstallEvents();
                $obModule->InstallDB();
                $obModule->InstallDefaultRoles();
                $obModule->InstallCompanyManagerGroup();
                $obModule->addDefaultData();
                if (!$obModule->IsInstalled()) {
                    RegisterModule($module);
                }
                return true;
            }

            if (in_array($module, array('sotbit.exchange1c'))) {
                $obModule->InstallFiles();
                $obModule->InstallProps();
                $obModule->InstallDB();
                $obModule->InstallEvents();
                RegisterModule($module);
                return true;
            }

            if (in_array($module, array('sotbit.notification'))) {
                $obModule->InstallFiles();
                $obModule->InstallDB();
                $obModule->InstallEvents();
                if (!$obModule->IsInstalled()) {
                    RegisterModule($module);
                }
                $obModule->InstallAgents();
                return true;
            }

            if (in_array($module, array('sotbit.multibasket'))) {
                global $APPLICATION;
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/entity/mbasket.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/entity/mbasketitem.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/entity/mbasketitemprops.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/listeners/deletbuyerlistener.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/listeners/savebasketlistener.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $module . '/lib/helpers/config.php';

                $obModule->InstallDB();
                $obModule->InstallEvents();
                $obModule->InstallFiles();
                Sotbit\Multibasket\Helpers\Config::setDefault();

                RegisterModule($module);
                return true;
            }

            if (in_array($module, array('sotbit.offerlist'))) {
                if (!$obModule->IsInstalled()) {
                    RegisterModule($module);
                }
                $obModule->installDB();
                $obModule->installEvents();
                $obModule->installFiles();
                return true;
            }

            if (in_array($module, array('sotbit.complaints'))) {
                $obModule->InstallEvents();
                $obModule->InstallIblock();
                $obModule->InstallFiles();
                $obModule->InstallDB();

                if (!$obModule->IsInstalled()) {
                    RegisterModule($module);
                }
                return true;
            }

            if (in_array($module, $modulesThear)) {
                $obModule->InstallFiles();
                $obModule->InstallDB();
                $obModule->InstallEvents();

                if (!$obModule->IsInstalled()) {
                    RegisterModule($module);
                }
                return true;
            }
        }
    }
}

$modulesNeed = $modulesThear;
foreach ($modulesNeed as $module) {
    $modulesPathDir = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $module . "/";
    if (!file_exists($modulesPathDir)) {
        $strError = '';
        CUpdateClientPartner::LoadModuleNoDemand($module, $strError, 'Y', false);
    }

    $module_status = CModule::IncludeModuleEx($module);
    if ($module_status == 2 || $module_status == 0 || $module_status == 3) {
        installModuleHands($module, $modulesThear);
    }
}
?>