<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Catalog;


IncludeModuleLangFile(__FILE__);

class sotbit_b2bcabinet extends CModule
{
    const MODULE_ID = 'sotbit.b2bcabinet';
    const TEMPLATE_NAME = 'b2bcabinet';
    const TEMPLATE_NAME_V2 = 'b2bcabinet_v2.0';

    var $MODULE_ID = 'sotbit.b2bcabinet';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("SOTBIT_B2BCABINET_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("SOTBIT_B2BCABINET_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("SOTBIT_B2BCABINET_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("SOTBIT_B2BCABINET_PARTNER_URI");

        $this->MODULE_GROUP_RIGHTS = 'Y';
    }


    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule(self::MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("SOTBIT_B2BCABINET_INSTALL_TITLE"),
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/step.php");
    }

    function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler('main', 'OnBuildGlobalMenu', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'onBuildGlobalMenuHandler');
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'onPageStart');
        EventManager::getInstance()->registerEventHandler('main', 'OnBeforeProlog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'resourceAccessCheck');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSaleStatusOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleStatusOrder');
        EventManager::getInstance()->registerEventHandler('sale', 'OnOrderAdd', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnOrderAdd');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSaleCancelOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleCancelOrder');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSaleDeliveryOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleDeliveryOrder');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSalePayOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSalePayOrder');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSaleComponentOrderOneStepPersonType',
            self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setCurrentProfileValue');
        EventManager::getInstance()->registerEventHandler('sale', 'OnSaleComponentOrderOneStepOrderProps',
            self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setOrderPropertyValues');
        EventManager::getInstance()->registerEventHandler('catalog',
            sprintf('\%s::%s', Catalog\Price::class, DataManager::EVENT_ON_AFTER_ADD), self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'updateOrAddProductPrice');
        EventManager::getInstance()->registerEventHandler('catalog',
            sprintf('\%s::%s', Catalog\Price::class, DataManager::EVENT_ON_AFTER_UPDATE), self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'updateOrAddProductPrice');
        EventManager::getInstance()->registerEventHandler('catalog', 'OnBeforeProductPriceDelete', self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'deleteProductPrice');
        EventManager::getInstance()->registerEventHandler('main', 'OnEpilog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'check404Error');
        EventManager::getInstance()->registerEventHandler('main', 'OnProlog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'onProlog');
		EventManager::getInstance()->registerEventHandler('main', 'OnGetCurrentSiteTemplate', self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setTemplateSiteRequset');
        $arr = getdate();
        $ndate = mktime(3, 0, 0, $arr["mon"], $arr["mday"], $arr["year"]);
        CAgent::AddAgent("\Sotbit\B2bCabinet\EventHandlers::deleteExcelFiles();", self::MODULE_ID, "N", 86400, "", "Y",
            ConvertTimeStamp($ndate + CTimeZone::GetOffset(), "FULL"), 50);
        return true;
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/themes/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/", true, true);
        return true;
    }

    function InstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $DB->runSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/db/' . strtolower($DB->type) . '/install.sql');
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if (!$request->get('confirm_delete_module')) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage('SOTBIT_B2BCABINET_UNINSTALL_TITLE',
                ['#MODULE#' => $this->MODULE_NAME]),
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/uninstall.php");
        } else {
            try {
                $this->getSiteList();
                UnRegisterModule(self::MODULE_ID);
                $this->UnInstallEvents();
                $this->DeleteSiteTemplate();
                $this->UnInstallFiles();
                $this->DeleteWizard($_SERVER['DOCUMENT_ROOT'] . '/bitrix/wizards/sotbit');

                if ($request->get('del_iblock') === 'Y') {
                    $this->UnInstallIblock();
                }
                if ($request->get('del_database') === 'Y') {
                    $this->UnInstallDB();
                }
                if ($request->get('del_forms') === 'Y') {
                    $this->UnInstallForms();
                }
                if ($request->get('del_user_fields') === 'Y') {
                    $this->UnInstallUserFields();
                }
                if ($request->get('del_settings') === 'Y') {
                    $this->UnInstallSettings();
                }
                if ($request->get('del_user') === 'Y') {
                    $this->UnInstallUsers();
                }

            } catch (\Exception $e) {
                $APPLICATION->ThrowException($e->getMessage());
            }
        }

    }

    function UnInstallUsers()
    {
        $arUserEmail = [
            'b2b@sotbit.ru',
            'manager@mail.ru'
        ];

        $arUser = array_column(\Bitrix\Main\UserTable::query()
            ->addSelect('ID')
            ->whereIn('EMAIL', $arUserEmail)
            ->fetchAll() ?: [], 'ID');

        array_walk($arUser, fn($user) => CUser::delete($user));
    }

    function UnInstallUserFields()
    {
        $userFields = [
            'UF_P_MANAGER_ID',
            'UF_P_MANAGER_EMAIL',
        ];

        $userFieldsId = array_column(\Bitrix\Main\UserFieldTable::query()
            ->addSelect('ID')
            ->whereIn('FIELD_NAME', $userFields)
            ->fetchAll() ?: [], 'ID');

        if (!$userFieldsId) {
            return;
        }

        $obUserField = new CUserTypeEntity;

        foreach ($userFieldsId as $id) {
            $obUserField->Delete($id);
        }
    }

    function UnInstallSettings()
    {
        Option::delete(self::MODULE_ID);
        array_walk($this->siteList, fn($lid) => Option::delete(self::MODULE_ID, ['site_id' => $lid]));
    }

    function UnInstallForms()
    {
        Loader::IncludeModule('form');
        $arForm = CForm::GetList('s_sort', 'asc', ['SID'=> 'MANAGER_CALLBACK'])->fetch();
        if ($arForm) {
            CForm::Delete($arForm['ID']);
        }
    }

    function UnInstallIblock()
    {
        Loader::includeModule("iblock");
        $iblockTypes = [
            'sotbit_b2bcabinet_type_catalog',
            'sotbit_b2bcabinet_type_document',
            'sotbit_b2bcabinet_content',
        ];

        array_walk($iblockTypes, fn($iblockType) => CIBlockType::Delete($iblockType));
    }

    function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnBuildGlobalMenu', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'OnBuildGlobalMenuHandler');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnPageStart', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'onPageStart');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnBeforeProlog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'resourceAccessCheck');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleStatusOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleStatusOrder');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnOrderAdd', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnOrderAdd');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleCancelOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleCancelOrder');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleDeliveryOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSaleDeliveryOrder');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSalePayOrder', self::MODULE_ID,
            '\Sotbit\B2bCabinet\CalendarEvent\Eventhandlers', 'OnSalePayOrder');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleComponentOrderOneStepPersonType',
            self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setCurrentProfileValue');
        EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleComponentOrderOneStepOrderProps',
            self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setOrderPropertyValues');
        EventManager::getInstance()->unRegisterEventHandler('catalog',
            sprintf('\%s::%s', Catalog\Price::class, DataManager::EVENT_ON_AFTER_ADD), self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'updateOrAddProductPrice');
        EventManager::getInstance()->unRegisterEventHandler('catalog',
            sprintf('\%s::%s', Catalog\Price::class, DataManager::EVENT_ON_AFTER_UPDATE), self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'updateOrAddProductPrice');
        EventManager::getInstance()->unRegisterEventHandler('catalog', 'OnBeforeProductPriceDelete', self::MODULE_ID,
            \Sotbit\B2bCabinet\Listeners\ExtremupProductPrice::class, 'deleteProductPrice');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnEpilog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'check404Error');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnProlog', self::MODULE_ID,
            '\Sotbit\B2bCabinet\EventHandlers', 'onProlog');
		EventManager::getInstance()->unRegisterEventHandler('main', 'OnGetCurrentSiteTemplate', self::MODULE_ID, '\Sotbit\B2bCabinet\EventHandlers', 'setTemplateSiteRequset');
        return true;
    }

    function DeleteSiteTemplate()
    {
        CSiteTemplate::Delete(self::TEMPLATE_NAME);
        CSiteTemplate::Delete(self::TEMPLATE_NAME_V2);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/themes/.default/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/wizards/sotbit/b2bcabinet/site/templates/b2bcabinet",
            $_SERVER["DOCUMENT_ROOT"] . "/local/templates/b2bcabinet");

        $filePath = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/wizards/sotbit/b2bcabinet/site/public/ru/";

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/b2bcabinet/")) {
            $this->deleteAttachments($filePath . 'b2bcabinet/', '/b2bcabinet/');
        } else {
            $this->deleteAttachments($filePath . 'site/', '/');
        }

        $this->deleteAttachments($filePath . 'common/include/', '/include/');
        $this->deleteAttachments($filePath . 'root/local/components/sotbit/', '/local/components/sotbit/');
        $this->deleteAttachments($filePath . 'root/bitrix/components/sotbit/', '/bitrix/components/sotbit/');
        $this->deleteAttachments($filePath . 'root/local/js/sotbit/', '/local/js/sotbit/');
        $this->deleteAttachments($filePath . 'root/local/gadgets/sotbit/', '/local/gadgets/sotbit/');

        $uploadDir = COption::GetOptionString('main', 'upload_dir', 'upload');
        DeleteDirFilesEx("/{$uploadDir}/sotbit_b2bcabinet");
        DeleteDirFilesEx("/{$uploadDir}/sotbit.b2bcabinet");
        return true;
    }

    function DeleteWizard($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $this->DeleteWizard(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } else {
            if (is_file($path) === true) {
                return unlink($path);
            }
        }

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB;
        $DB->runSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/db/' . strtolower($DB->type) . '/uninstall.sql');
        return true;
    }

    function deleteAttachments($scanDir, $deleteDir)
    {
        if (is_dir($p = $scanDir)) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p . $item)) {
                        continue;
                    }
                    DeleteDirFilesEx($deleteDir . $item );
                }
                closedir($dir);
            }
        }
    }

    function getSiteList()
    {
        $this->siteList = array_column(\Bitrix\Main\SiteTable::query()
            ->addSelect('LID')
            ->fetchAll() ?: [], 'LID');
    }
}
?>