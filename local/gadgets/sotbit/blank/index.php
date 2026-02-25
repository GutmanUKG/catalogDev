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

        <div class="catalog-widget">
            <div class="catalog-banner">
                <a href="/orders/blank_zakaza/">
                    <img src="<?=SITE_TEMPLATE_PATH?>/assets/images/catalog-screenshot.png" alt="Каталог товаров">
                </a>
            </div>

            <div class="catalog-buttons">
                <?$APPLICATION->IncludeComponent(
                    "sotbit:b2bcabinet.excel.export",
                    ".default",
                    array(
                        "COMPONENT_TEMPLATE" => ".default",
                        "IBLOCK_TYPE" => Config::get('CATALOG_IBLOCK_TYPE'),
                        "IBLOCK_ID" => Config::get('CATALOG_IBLOCK_ID'),
                        "MODEL_OF_WORK" => "default",
                        "PRICE_CODE" => array(
                            0 => "Цена дилерского портала KZT",
                            1 => "RRP",
                            2 => "",
                        ),
                        "HEADERS_COLUMN" => array(
                            0 => "NAME",
                            1 => "PREVIEW_PICTURE",
                            2 => "",
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "KOD_ATTR_S",
                            1 => "CML2_ARTICLE",
                            2 => "BREND_ATTR_S",
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
                );?>
                <?$APPLICATION->IncludeComponent(
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
            </div>
        </div>
    </div>
<?endif;?>