<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;
use Sotbit\B2bCabinet\Helper\Config;
use Sotbit\Multibasket\Helpers;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle("");
$APPLICATION->SetTitle(Loc::getMessage('ORDERS_MAKE_ORDER'));
$APPLICATION->SetPageProperty('title_prefix',
    '<span class="font-weight-semibold">' . Loc::getMessage("ORDERS_ORDERS") . '</span> - ');
$multibasketOn = Loader::includeModule('sotbit.multibasket') && Helpers\Config::moduleIsEnabled(SITE_ID);
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/basket-upselling.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/pages/basket-upselling.js');
?><?php
if (!Loader::includeModule('sotbit.b2bcabinet')) {
    header('Location: ' . SITE_DIR . 'b2bcabinet/');
    exit;
}
$request = Application::getInstance()->getContext()->getRequest();
?>
<main class="basket-page">
    <div class="basket-page__content">
        <div class="basket-upselling__basket <?= $multibasketOn ? "multibasket_working" : "" ?>">
            <? $APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket",
                "b2bcabinet",
                array(
                    "ACTION_VARIABLE" => "action",
                    "ADDITIONAL_PICT_PROP_13" => "-",
                    "ADDITIONAL_PICT_PROP_2" => "-",
                    "ADDITIONAL_PICT_PROP_3" => "-",
                    "ADDITIONAL_PICT_PROP_4" => "-",
                    "AJAX_MODE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AUTO_CALCULATION" => "Y",
                    "BASKET_IMAGES_SCALING" => "adaptive",
                    "COLUMNS_LIST" => array(
                        0 => "NAME",
                        1 => "DISCOUNT",
                        2 => "WEIGHT",
                        3 => "DELETE",
                        4 => "DELAY",
                        5 => "TYPE",
                        6 => "PRICE",
                        7 => "QUANTITY",
                        8 => "SUM",
                    ),
                    "COLUMNS_LIST_EXT" => array(
                        0 => "PREVIEW_PICTURE",
                        1 => "DISCOUNT",
                        2 => "PROPS",
                        3 => "DELETE",
                        4 => "DELAY",
                        5 => "TYPE",
                        6 => "SUM",
                    ),
                    "COLUMNS_LIST_MOBILE" => array(
                        0 => "PREVIEW_PICTURE",
                        1 => "DISCOUNT",
                        2 => "DELETE",
                        3 => "DELAY",
                        4 => "TYPE",
                        5 => "SUM",
                    ),
                    "COMPATIBLE_MODE" => "Y",
                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                    "CORRECT_RATIO" => "Y",
                    "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
                    "DEFERRED_REFRESH" => "Y",
                    "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                    "DISPLAY_MODE" => "extended",
                    "EMPTY_BASKET_HINT_PATH" => SITE_DIR . "b2bcabinet/orders/blank_zakaza/index.php",
                    "GIFTS_BLOCK_TITLE" => "???????? ???? ?? ????????",
                    "GIFTS_CONVERT_CURRENCY" => "N",
                    "GIFTS_HIDE_BLOCK_TITLE" => "N",
                    "GIFTS_HIDE_NOT_AVAILABLE" => "N",
                    "GIFTS_MESS_BTN_BUY" => "???????",
                    "GIFTS_MESS_BTN_DETAIL" => "?????????",
                    "GIFTS_PAGE_ELEMENT_COUNT" => "4",
                    "GIFTS_PLACE" => "BOTTOM",
                    "GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
                    "GIFTS_PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
                    "GIFTS_SHOW_OLD_PRICE" => "N",
                    "GIFTS_TEXT_LABEL_GIFT" => "???????",
                    "HIDE_COUPON" => "N",
                    "IBLOCK_ID" => "",
                    "IBLOCK_TYPE" => "",
                    "IMAGE_SIZE_PREVIEW" => "23",
                    "IMG_HEIGHT" => "",
                    "IMG_WIDTH" => "",
                    "LABEL_PROP" => array(),
                    "MANUFACTURER_ELEMENT_PROPS" => "",
                    "MANUFACTURER_LIST_PROPS" => "",
                    "MORE_PHOTO_OFFER_PROPS" => "",
                    "MORE_PHOTO_PRODUCT_PROPS" => "",
                    "OFFERS_PROPS" => array(),
                    "OFFER_COLOR_PROP" => "",
                    "OFFER_TREE_PROPS" => "",
                    "PATH_TO_BASKET" => SITE_DIR . "b2bcabinet/orders/make/index.php",
                    "PATH_TO_ORDER" => SITE_DIR . "b2bcabinet/orders/make/make.php",
                    "PICTURE_FROM_OFFER" => "",
                    "PRICE_DISPLAY_MODE" => "Y",
                    "PRICE_VAT_SHOW_VALUE" => "Y",
                    "PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
                    "QUANTITY_FLOAT" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_DISCOUNT_PERCENT" => "Y",
                    "SHOW_FILTER" => "Y",
                    "SHOW_RESTORE" => "Y",
                    "SHOW_VAT_PRICE" => "Y",
                    "TEMPLATE_THEME" => "blue",
                    "TOTAL_BLOCK_DISPLAY" => array(
                        0 => "top",
                    ),
                    "USE_DYNAMIC_SCROLL" => "Y",
                    "USE_ENHANCED_ECOMMERCE" => "N",
                    "USE_GIFTS" => "Y",
                    "USE_PREPAYMENT" => "N",
                    "USE_PRICE_ANIMATION" => "Y",
                    "ADDITIONAL_PICT_PROP_5" => "-",
                    "ADDITIONAL_PICT_PROP_9" => "-"
                ),
                false
            ); ?>
        </div>
        <? $APPLICATION->IncludeComponent(
	"sotbit:basket.upselling", 
	"b2bcabinet", 
	array(
		"COMPONENT_TEMPLATE" => "b2bcabinet",
		"IBLOCK_TYPE" => Config::get("CATALOG_IBLOCK_TYPE"),
		"IBLOCK_ID" => Config::get("CATALOG_IBLOCK_ID"),
		"PAGE_ELEMENT_COUNT" => "5",
		"HIDE_NOT_AVAILABLE" => "N",
		"SHOW_ALL_WO_SECTION" => "Y",
		"ELEMENT_SORT_FIELD" => "name",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "by_price",
		"ELEMENT_SORT_ORDER2" => "asc",
		"COMPARE_OFFERS_PROPERTY_CODE" => array(
			0 => "0",
			1 => "1",
			2 => "2",
		),
		"DISPLAYED_PROPERTIES" => "",
		"DISPLAYED_PROPERTIES_IN_BUSKET" => "",
		"ARTICLE" => array(
			0 => "9",
			1 => "20",
			2 => "62",
		),
		"OFFER_TREE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000",
		"CACHE_GROUPS" => "N",
		"CACHE_FILTER" => "N",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/b2bcabinet/orders/make/",
		"TYPE_PRICE" => array(
			0 => "1",
			1 => "2",
			2 => "3",
		),
		"PRIVATE_PRICE" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SEF_URL_TEMPLATES" => array(
			"NAME" => "",
			"TYPE" => "C",
			"DEFAULT" => "N",
			"smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
		)
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
); ?>

    </div>
    <footer class="card card-position-sticky" id="basket-page__footer">
        <div class="card-body basket-page__footer">
            <div class="d-sm-none basket basket-footer">
                <div class="basket-checkbox__wrapper"
                    data-entity="basket-gruope-item-checkbox">
                    <?= Loc::getMessage('BASKET_CHECKED_ALL_ITEMS') ?>
                    <label class="basket__checkbox"
                        >
                        <span class="basket__checkbox_content"></span>
                    </label>
                </div>
            </div>
            <div class="basket-page__action-btn dropup" data-entity="page-basket-action-button">
                <button type="button" class="btn btn-actions" data-bs-toggle="dropdown">
                        <span class="ladda-label">
                            <i class="ph-dots-three-vertical"></i>
                            <?= Loc::getMessage('BASKET_BTN_ACTIONS') ?>
                        </span>
                </button>
                <div class="dropdown-menu">
                    <?php
                    $APPLICATION->IncludeComponent(
                        "sotbit:b2bcabinet.excel.import",
                        ".default",
                        array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "MULTIPLE" => "Y",
                            "MAX_FILE_SIZE" => "",
                            "USE_BUTTON" => "N",
                        ),
                        false
                    ); ?>
                    <? if (Loader::includeModule('sotbit.offerlist') && SotbitOfferlist::getModuleEnable()): ?>
                        <button type="button" id="offerlist_pricelist_open" class="dropdown-item text-primary">
                            <?= Loc::getMessage('BASKET_BTN_ADD_PRICELIST') ?>
                        </button>
                        <button type="button" id="offerlist_requestadd_open" class="dropdown-item text-primary">
                            <?= Loc::getMessage('BASKET_BTN_ADD_REQUEST') ?>
                        </button>
                    <? endif; ?>
                </div>
            </div>
            <div class="basket-page__total-price">
                <span class="basket-page__total-price-key"><?= Loc::getMessage('BASKET_TOTAL') ?></span>
                <span class="basket-page__total-price-value"
                    id="page-basket-total-block"><?= Loc::getMessage('START_BASKET_TOTAL_PRICE') ?></span>
            </div>
            <div class="basket-page__checkout-btn" data-entity="page-basket-checkout-button">
                <button class="btn btn-primary"><?= Loc::getMessage('CHECKOUT_ORDER') ?></button>
            </div>
        </div>
    </footer>
</main>

<? if (Loader::includeModule('sotbit.offerlist') && SotbitOfferlist::getModuleEnable()):
    $APPLICATION->IncludeComponent(
        "sotbit:offerlist.pricelist.add",
        "b2bcabinet",
        array(
            'AJAX_CALL' => 'Y',
            'RESULT_BLOCK' => 'offerlist_pricelist_component',
            'BTN_GET_RESULT' => 'offerlist_pricelist_open',
            'DETAIL_PAGE_URL' => SITE_DIR . 'b2bcabinet/offers/pricelist/?ID=#ID#',
        ), false
    );

    $APPLICATION->IncludeComponent(
        "sotbit:offerlist.request.add",
        "b2bcabinet",
        array(
            'BTN_GET_RESULT' => 'offerlist_requestadd_open',
        ),
        false,
        ['HIDE_ICONS' => 'Y']
    );

    ?>
    <div id="offerlist_pricelist_component"></div>
<? endif; ?>

<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
