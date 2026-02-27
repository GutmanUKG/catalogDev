<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle( Loc::getMessage('ORDERS_MAKE_ORDER') );
?>

<?php
if(!Loader::includeModule('sotbit.b2bcabinet')) {
    header('Location: '.SITE_DIR);
    exit;
}
?>

    <div class="blank_resizer">
        <div class="blank_resizer_tool blank_resizer_tool_open"></div>
    </div>

<?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax",
    "b2bcabinet",
    Array(
        "IMAGE_SIZE_PREVIEW" => "23",
        "FIELDS_USER_INFO" => ['LAST_NAME','NAME','SECOND_NAME','PHONE','EMAIL'],
        "DETAIL_PAGE_URL" => "N",
        "IMAGE_SIZE_DELIVERY_PAYSYSTEM" => [23,23],

        "PRODUCT_COLUMNS_VISIBLE" => array(
            1 => "PROPERTY_CML2_ARTICLE",
            2 => "DISCOUNT_PRICE_PERCENT_FORMATED"
        ),
        "ALLOW_AUTO_REGISTER" => "Y",
		"ALLOW_NEW_PROFILE" => "N",
		"ALLOW_USER_PROFILES" => "Y",
        "BUYER_PERSONAL_TYPE" => unserialize(COption::GetOptionString("sotbit.b2bcabinet","BUYER_PERSONAL_TYPE","a:0:{}",
            SITE_ID)),
        "COMPONENT_TEMPLATE" => "b2bcabinet",
        "DELIVERY_NO_AJAX" => "Y",
        "DELIVERY_NO_SESSION" => "N",
        "DELIVERY_TO_PAYSYSTEM" => "d2p",
        "DISABLE_BASKET_REDIRECT" => "N",
        "ONLY_FULL_PAY_FROM_ACCOUNT" => "Y",
        "PATH_TO_AUTH" => "/auth/",
        "PATH_TO_BASKET" => SITE_DIR."orders/make/index.php",
        "PATH_TO_ORDER" => SITE_DIR."orders/make/make.php",
        "PATH_TO_PAYMENT" => SITE_DIR."orders/payment/index.php",
        "PATH_TO_PERSONAL" => SITE_DIR."orders/index.php",
        "PAY_FROM_ACCOUNT" => "Y",
        "SEND_NEW_USER_NOTIFY" => "Y",
        "SET_TITLE" => "Y",
        "SHOW_PAYMENT_SERVICES_NAMES" => "Y",
        "SHOW_STORES_IMAGES" => "N",
        "TEMPLATE_LOCATION" => "popup",
        "USE_PREPAYMENT" => "N",
		"ALLOW_APPEND_ORDER" => "Y",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "L",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"SHOW_VAT_PRICE" => "Y",
		"COMPATIBLE_MODE" => "Y",
		"USE_PRELOAD" => "Y",
		"LOCATION_OF_PROPERTIES" => "AFTER",
		"HIDDEN_BASKET" => "N",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"ACTION_VARIABLE" => "soa-action",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"USE_PHONE_NORMALIZATION" => "Y",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"ADDITIONAL_PICT_PROP_3" => "-",
		"ADDITIONAL_PICT_PROP_4" => "-",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"ADDITIONAL_PICT_PROP_24" => "-",
		"TEMPLATE_THEME" => "site",
		"SHOW_ORDER_BUTTON" => "final_step",
		"SHOW_TOTAL_ORDER_BUTTON" => "N",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
		"SHOW_DELIVERY_LIST_NAMES" => "Y",
		"SHOW_DELIVERY_INFO_NAME" => "Y",
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",
		"SKIP_USELESS_BLOCK" => "Y",
		"BASKET_POSITION" => "after",
		"SHOW_BASKET_HEADERS" => "N",
		"DELIVERY_FADE_EXTRA_SERVICES" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "N",
		"SHOW_COUPONS_PAY_SYSTEM" => "N",
		"SHOW_NEAREST_PICKUP" => "N",
		"DELIVERIES_PER_PAGE" => "9",
		"PAY_SYSTEMS_PER_PAGE" => "9",
		"PICKUPS_PER_PAGE" => "5",
		"SHOW_PICKUP_MAP" => "Y",
		"SHOW_MAP_IN_PROPS" => "N",
		"PICKUP_MAP_TYPE" => "yandex",
		"PROPS_FADE_LIST_3" => "",
		"PROPS_FADE_LIST_1" => "",
		"PROPS_FADE_LIST_2" => "",
		"SERVICES_IMAGES_SCALING" => "adaptive",
		"PRODUCT_COLUMNS_HIDDEN" => array(
		    0=> ""
		),
		"HIDE_ORDER_DESCRIPTION" => "N",
		"USE_YM_GOALS" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_CUSTOM_MAIN_MESSAGES" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
		"USE_CUSTOM_ERROR_MESSAGES" => "N"
    ),
    false
);
?>

<?require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>