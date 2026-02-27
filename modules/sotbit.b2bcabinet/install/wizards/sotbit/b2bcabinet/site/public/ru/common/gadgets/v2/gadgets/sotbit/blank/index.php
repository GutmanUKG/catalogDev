<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;
use Sotbit\B2bCabinet\Helper\Config;

$methodIstall = Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR.'b2bcabinet/' : SITE_DIR;
Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');

$idUser = intval($USER->GetID());

if(Loader::includeModule('sotbit.b2bcabinet') && $idUser > 0):?>
    <div class="widget_blank-buttons gap-2">

        <?php
        $APPLICATION->IncludeComponent(
            "sotbit:b2bcabinet.excel.import",
            ".default",
            array(
                "COMPONENT_TEMPLATE" => ".default",
                "MULTIPLE" => "Y",
                "MAX_FILE_SIZE" => "",
                "USE_ICON" => "Y"
            ),
            false
        );?>

        <?
        $APPLICATION->IncludeComponent(
            "sotbit:b2bcabinet.excel.export",
            ".default",
            array(
                "COMPONENT_TEMPLATE" => ".default",
                "IBLOCK_TYPE" => Config::get('CATALOG_IBLOCK_TYPE'),
                "IBLOCK_ID" => Config::get('CATALOG_IBLOCK_ID'),
                "MODEL_OF_WORK" => "default",
                "PRICE_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "HEADERS_COLUMN" => array(
                    0 => "NAME",
                    1 => "PREVIEW_PICTURE",
                    2 => "DETAIL_PICTURE",
                    3 => "",
                ),
                "PROPERTY_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_PROPERTY_CODE" => "",
                "SORT_BY" => "NAME",
                "SORT_ORDER" => "asc",
                "ONLY_AVAILABLE" => "Y",
                "FILTER_NAME" => "",
                "ONLY_ACTIVE" => "Y",
                "COMPOSITE_FRAME_MODE" => "A",
                "COMPOSITE_FRAME_TYPE" => "AUTO"
            ),
            false
        );
        ?>
    </div>
<?endif;?>
