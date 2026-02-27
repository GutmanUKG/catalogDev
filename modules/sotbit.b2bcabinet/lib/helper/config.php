<?php
namespace Sotbit\B2bCabinet\Helper;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Web;

Loc::loadMessages(__FILE__);

/**
 * Class Config
 *
 * @package Sotbit\B2bCabinet\Helper
 * @author Andrey Sapronov <a.sapronov@sotbit.ru>
 * Date: 20.05.2020
 */
class Config
{
    const THEME_PATH = '/theme/';

    /**
     * @param $name
     * @param  string  $site
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function get($name, $site = '') {
        return Option::get(\SotbitB2bCabinet::MODULE_ID, $name, "", (!empty($site) ? $site : SITE_ID));
    }

    /**
     * @param $name
     * @param $value
     * @param  string  $site
     *
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function set($name, $value, $site = '') {
        return Option::set( \SotbitB2bCabinet::MODULE_ID, $name, $value, (!empty($site) ? $site : SITE_ID) );
    }

    /**
     * Root path
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function getPath() {
        $methodIstall = self::get('method_install');
        if($methodIstall == 'AS_TEMPLATE') {
            $path = self::get('PATH');
            $path = trim(trim(self::get('PATH'), "\\\/"));
            if(empty($path))
                $path = \SotbitB2bCabinet::PATH;
            return SITE_DIR.$path.'/';
        }

        return SITE_DIR;
    }

    /**
     * All iblock types
     *
     * @return array
     */
    public static function getIblockTypes()
    {
        $return = [];
        try {
            Loader::includeModule('iblock');
        } catch (LoaderException $e) {
            echo $e->getMessage();
        }

        $rs = \Bitrix\Iblock\TypeTable::getList(
            [
                'select' => [
                    'ID',
                    'LANG_MESSAGE.NAME',
                ],
                'filter' => [
                    'LANG_MESSAGE.LANGUAGE_ID' => LANGUAGE_ID,
                ],
            ]
        );
        while ($iType = $rs->fetch()) {
            $return[$iType['ID']] = '['.$iType['ID'].'] '.$iType['IBLOCK_TYPE_LANG_MESSAGE_NAME'];
        }

        return $return;
    }

    /**
     * All sites
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getSites()
    {
        $sites = [];
        
        $rs = \Bitrix\Main\SiteTable::getList([
            'select' => ['SITE_NAME', 'LID'],
            'filter' => ['ACTIVE' => 'Y'],
        ]);
        
        while ($site = $rs->fetch()) {
            $sites[$site['LID']] = $site['SITE_NAME'];
        }
        
        if (!is_array($sites) || count($sites) == 0) {
            echo "Cannot get sites";
        }
        
        return $sites;
    }

    /**
     * Checks the SITE parameter in URI
     * If empty, then redirect to the current site
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUriSite() {
        $request = Request::getInstance();
        $sites = array_keys(self::getSites());
        $uri = new Web\Uri($request->uri());
        $uri->addParams(array("site"=>$sites[0]));
        LocalRedirect($uri->getUri(), true);
    }

    /**
     * Method install modules (AS_SITE or AS_TEMPLATE)
     *
     * @return string AS_TEMPLATE || AS_SITE
     */
    public static function getMethodInstall($siteId = false) {
        return self::get('method_install', (!empty($siteId) ? $siteId : ''));
    }

    /**
     * Header path
     *
     * @return string path
     */
    public static function getHeaderPath($siteId)
    {
        return $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/' . (self::get('HEADER_TYPE', $siteId) ?: '1') . '/content_header.php';
    }

    /**
     * Footer path
     *
     * @return string path
     */
    public static function getFooterPath($siteId)
    {
        return $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/' . (self::get('HEADER_TYPE', $siteId) ?: '1') . '/content_footer.php';
    }

    public static function getHeaderStylePath($siteId)
    {
        $headerId = self::get('HEADER_TYPE', $siteId) ?: '1';

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/style.min.css')) {
            return SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/style.min.css';
        }

        if (file_exists($path = $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/style.css')) {
            return SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/style.css';
        }

        return false;
    }

    public static function getHeaderJSPath($siteId)
    {
        $headerId = self::get('HEADER_TYPE', $siteId) ?: '1';

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/script.min.js')) {
            return SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/script.min.js';
        }

        if (file_exists($path = $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/script.js')) {
            return SITE_TEMPLATE_PATH . self::THEME_PATH . 'layout/'. $headerId . '/script.js';
        }

        return false;
    }

    public static function getHeaderList() : array
    {
        $headerList = [];
        if(file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/templates/b2bcabinet/' . self::THEME_PATH . 'layout')) {
            $arDir = scandir($_SERVER["DOCUMENT_ROOT"] . '/local/templates/b2bcabinet/' . self::THEME_PATH . 'layout/');
            if(!empty($arDir)) {
                foreach ($arDir as $item) {
                    if($item == '.' || $item == '..') {
                        continue;
                    }
                    $headerList[$item]["NAME"] = $item;
                    $headerList[$item]["ID"] = $item;
                    $settings = [];
                    if(file_exists($_SERVER["DOCUMENT_ROOT"] .'/local/templates/b2bcabinet/' . self::THEME_PATH . 'layout/'. $item . '/settings.php')) {
                        include $_SERVER["DOCUMENT_ROOT"] .'/local/templates/b2bcabinet/' . self::THEME_PATH . 'layout/'. $item . '/settings.php';
                        $headerList[$item]["NAME"] = $settings["NAME"] ?: $item;
                    }
                }
            }
        }

        return $headerList;
    }
}