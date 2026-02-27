<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Bitrix\Main\Web\Json;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/settings.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/search.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/utils.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/api.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/destination-selector.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/field-controller.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/main-ui-control-custom-entity.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/components/bitrix/main.ui.filter/b2bcabinet/js/presets.js");


Loader::includeModule("catalog");
Loader::includeModule("sale");

CJSCore::Init(array('clipboard', 'fx'));
$protocol = CMain::IsHTTPS() ? 'https://' : 'http://';
$methodIstall = Option::get('sotbit.b2bcabinet', 'method_install', '', SITE_ID) == 'AS_TEMPLATE' ?
    SITE_DIR . 'b2bcabinet/' :
    SITE_DIR;

$sotbitBillLink = $protocol . $_SERVER['SERVER_NAME'] . $methodIstall;

$order = \Bitrix\Sale\Order::load($arResult["ID"]);
$paymentCollection = $order->getPaymentCollection();
$signer = new Bitrix\Main\Security\Sign\Signer();

foreach ($paymentCollection as $i => $payment) {
    $id = $payment->getField('ID');

    foreach ($arResult['PAYMENT'] as $k => $pay) {
        if ($pay['ID'] == $id) {
            $key = $k;
            break;
        }
    }

    $paymentData[$arResult['PAYMENT'][$key]['ACCOUNT_NUMBER']] = array(
        "payment" => $arResult['PAYMENT'][$key]['ACCOUNT_NUMBER'],
        "order" => $arResult['ACCOUNT_NUMBER'],
        "allow_inner" => $arResult['PAYMENT'][$key]['ALLOW_INNER'],
        "only_inner_full" => $arParams['ONLY_INNER_FULL'],
        "path_to_payment" => $arParams['PATH_TO_PAYMENT'],
        "SITE_ID" => SITE_ID,
    );
}

if (CModule::IncludeModule('sotbit.complaints')) {
    $complaintsType = Option::get('sotbit.complaints', 'COMPLAINTS_WITH_ORDER', '', SITE_ID);
    $complaintsPath = Option::get('sotbit.complaints', 'COMPLAINTS_PATH', '', SITE_ID);
}

$APPLICATION->AddChainItem(Loc::getMessage('SPOD_ORDER') . " " . Loc::getMessage('SPOD_NUM_SIGN') . htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]));
$APPLICATION->SetTitle(Loc::getMessage('SPOD_LIST_MY_ORDER_TITLE'));

if (!empty($arResult['ERRORS']['FATAL'])) {
    foreach ($arResult['ERRORS']['FATAL'] as $error) {
        ShowError($error);
    }

    $component = $this->__component;

    if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
        $APPLICATION->AuthForm('', false, false, 'N', false);
    }
} else {
    if (!empty($arResult['ERRORS']['NONFATAL'])) {
        foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
            ShowError($error);
        }
    }
?>
    <div class="blank_detail">
        <div class="detail-menu d-flex justify-content-between align-items-center overflow-auto">
            <ul class="nav nav-tabs nav-mainpage-tabs">
                <li class="nav-item">
                    <a href="#basic-tab1" class="nav-link active show" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab1')"><?= Loc::getMessage('SPOD_TAB_COMMON') ?></a>
                </li>
                <li class="nav-item">
                    <a href="#basic-tab2" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab2')"><?= Loc::getMessage('SPOD_TAB_GOODS') ?></a>
                </li>
                <li class="nav-item">
                    <a href="#basic-tab3" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab3')"><?= Loc::getMessage('SPOD_TAB_DOCS') ?></a>
                </li>
                <li class="nav-item">
                    <a href="#basic-tab4" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab4')"><?= Loc::getMessage('SPOD_TAB_PAYS') ?></a>
                </li>
                <li class="nav-item">
                    <a href="#basic-tab5" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab5')"><?= Loc::getMessage('SPOD_TAB_SHIPMENTS') ?></a>
                </li>
                <li class="nav-item">
                    <a href="#basic-tab6" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab6')"><?= Loc::getMessage('SPOD_TAB_SUPPORT') ?></a>
                </li>
                <? if ($complaintsType == "ORDER" && !empty($arResult['COMPLAINTS_ROW'])) { ?>
                    <li class="nav-item">
                        <a href="#basic-tab7" class="nav-link" data-bs-toggle="tab" onclick="writeActiveTab('#basic-tab7')"><?= Loc::getMessage('SPOD_TAB_COMPAINTS') ?></a>
                    </li>
                <? } ?>
            </ul>
        </div>
        <div class="tab-content mt-4">
            <!--basic-tab1-->
            <div class="tab-pane fade show active" id="basic-tab1">
                <div class="card-columns card-columns-3">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                                                        "#ACCOUNT_NUMBER#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                                                        "#DATE_ORDER_CREATE#" => $arResult["DATE_INSERT_FORMATED"]
                                                    )) ?></h6>
                            <div class="d-inline-flex ms-auto">
                                <a class="text-body px-2" data-card-action="collapse">
                                    <i class="ph ph-caret-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="collapse show">
                            <div class="card-body pt-0">
                                <dl class="card-content">
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage('SPOD_ORDER_STATUS', array(
                                                '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                                            )) ?>
                                        </dt>
                                        <dd>
                                            <?
                                            if ($arResult['CANCELED'] !== 'Y') {
                                                echo htmlspecialcharsbx($arResult["STATUS"]["NAME"] . " (" . Loc::getMessage('SPOD_FROM') . " " . $arResult["DATE_INSERT_FORMATED"] . ")");
                                            } else {
                                                echo Loc::getMessage('SPOD_ORDER_CANCELED');
                                            }
                                            ?>
                                        </dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage("SPOD_ORDER_PRICE_WITHOUT_DOTS") ?>
                                        </dt>
                                        <dd>
                                            <?= $arResult["PRICE_FORMATED"] ?>
                                        </dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage('SPOD_ORDER_CANCELED'); ?>
                                        </dt>
                                        <dd>
                                            <?= ($arResult['CANCELED'] == "N" ? Loc::getMessage("SPOD_NO") : Loc::getMessage("SPOD_YES")) ?>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage("SPOD_USER_BUYER") ?></h6>
                            <div class="d-inline-flex ms-auto">
                                <a class="text-body px-2" data-card-action="collapse">
                                    <i class="ph ph-caret-down"></i>
                                </a>
                            </div>
                        </div>
                        <div class="collapse show">
                            <div class="card-body pt-0">
                                <dl class="card-content">
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage("SPOD_ACCOUNT") ?>
                                        </dt>
                                        <dd>
                                            <?= $arResult['USER']['LOGIN']; ?>
                                        </dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage("SPOD_PERSON_TYPE_NAME") ?>
                                        </dt>
                                        <dd>
                                            <?= $arResult["PERSON_TYPE"]["NAME"] ?>
                                        </dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt>
                                            <?= Loc::getMessage('SPOD_EMAIL'); ?>
                                        </dt>
                                        <dd>
                                            <?= $arResult['USER']["EMAIL"] ?>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <? if ($arResult["PRINT_ORDER_PROPS"]) :
                        foreach ($arResult["PRINT_ORDER_PROPS"] as $orderGroup) : ?>
                            <? if (!$orderGroup["PROPS"]) {
                                continue;
                            } ?>
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <h6 class="card-title mb-0 fw-bold"><?= $orderGroup["PROPS"][0]["GROUP_NAME"] ?></h6>
                                    <div class="d-inline-flex ms-auto">
                                        <a class="text-body px-2" data-card-action="collapse">
                                            <i class="ph ph-caret-down"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="collapse show">
                                    <div class="card-body pt-0">
                                        <dl class="card-content">
                                            <? foreach ($orderGroup["PROPS"] as $orderProp) : ?>
                                                <? if ($orderProp['VALUE']) : ?>
                                                    <div class="card-content__row">
                                                        <dt>
                                                            <?= $orderProp['NAME'] ?>
                                                        </dt>
                                                        <dd>
                                                            <?
                                                            if ($orderProp['MULTIPLE'] == "Y") {
                                                                $orderProp['VALUE'] = is_array(unserialize($orderProp['VALUE'])) ? implode("<br>", unserialize($orderProp['VALUE'])) : unserialize($orderProp['VALUE']);
                                                            }
                                                            ?>
                                                            <?= $orderProp['VALUE'] ?>
                                                        </dd>
                                                    </div>
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                    <? endforeach;
                    endif; ?>

                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage("SPOD_ORDER_PAYMENT") ?></h6>
                            <div class="d-inline-flex ms-auto">
                                <a class="text-body px-2" data-card-action="collapse">
                                    <i class="ph ph-caret-down"></i>
                                </a>
                            </div>
                        </div>
                        <? foreach ($arResult["PAYMENT"] as $k => $payment) : ?>
                            <?
                            $paymentSubTitle = Loc::getMessage('SOPC_TPL_BILL') . " " . Loc::getMessage('SOPC_TPL_NUMBER_SIGN') . $payment['ACCOUNT_NUMBER'];
                            if (isset($payment['DATE_BILL'])) {
                                $paymentSubTitle .= " " . Loc::getMessage('SOPC_TPL_FROM_DATE') . " " . $payment['DATE_BILL']->format("d.m.Y");
                            }
                            $paymentSubTitle .= ", " . htmlspecialcharsbx($payment['PAY_SYSTEM_NAME']);
                            ?>
                            <div class="collapse show">
                                <div class="card-body pt-0">
                                    <dl class="card-content" data-id="<?= $payment['ACCOUNT_NUMBER'] ?>">
                                        <div class="card-content__row">
                                            <dt>
                                                <?= $paymentSubTitle ?>
                                            </dt>
                                            <dd>
                                                <? if ($payment['PAID'] === 'Y') : ?>
                                                    <span class="badge bg-success bg-opacity-20 text-success rounded-pill small-text">
                                                        <?= Loc::getMessage('SOPC_TPL_PAID') ?>
                                                    </span>
                                                <? elseif ($arResult['IS_ALLOW_PAY'] == 'N') : ?>
                                                    <span class="badge bg-warning bg-opacity-20 text-warning rounded-pill small-text">
                                                        <?= Loc::getMessage('SOPC_TPL_RESTRICTED_PAID') ?>
                                                    </span>
                                                <? else : ?>
                                                    <span class="badge bg-danger bg-opacity-20 text-danger rounded-pill small-text">
                                                        <?= Loc::getMessage('SOPC_TPL_NOTPAID') ?>
                                                    </span>
                                                <? endif; ?>
                                            </dd>
                                        </div>

                                        <div class="payment-wrapper" data-id="<?=$payment['ACCOUNT_NUMBER']?>">
                                            <div class="card-content__row">
                                                <dt>
                                                    <?= Loc::getMessage("SPOD_PAY_SYSTEM") ?>
                                                </dt>
                                                <dd>
                                                    <?= $payment['PAY_SYSTEM']['NAME'] ?>
                                                </dd>
                                            </div>
                                            <div class="card-content__row">
                                                <dt>
                                                <?= Loc::getMessage("SPOD_ORDER_PAYED") ?>
                                                </dt>
                                                <dd>
                                                    <?= ($payment['PAID'] == 'Y' ? Loc::getMessage("SPOD_YES") : Loc::getMessage("SPOD_NO")) ?>
                                                </dd>
                                            </div>

                                            <? if ((stripos($payment['PAY_SYSTEM']['ACTION_FILE'], 'billsotbit') !== false || $payment['PAY_SYSTEM']['ACTION_FILE'] == 'orderdocument') && $arResult['IS_ALLOW_PAY'] !== 'N') : ?>
                                                
                                                <div class="card-content__row">
                                                    <dt>
                                                        <?= Loc::getMessage("SPOD_CHECK_BILL") ?>
                                                    </dt>
                                                    <dd>
                                                        <?= Loc::getMessage("SHOW_BILL", array(
                                                            '#ORDER_ID#' => $arResult["ID"],
                                                            '#PAYMENT_ID#' => $payment["ID"],
                                                            '#DATE#' =>    $arResult["DATE_INSERT_FORMATED"],
                                                            '#TYPE_TEMPLATE#' => $sotbitBillLink
                                                        )) ?>
                                                    </dd>
                                                </div>
                                                <div class="card-content__row">
                                                    <dt>
                                                        <?= Loc::getMessage("SPOD_DOWNLOAD_BILL") ?>
                                                    </dt>
                                                    <dd>
                                                        <?= Loc::getMessage("DOWNLOAD_BILL", array(
                                                            '#ORDER_ID#' => $arResult["ID"],
                                                            '#PAYMENT_ID#' => $payment["ID"],
                                                            '#DATE#' =>    $arResult["DATE_INSERT_FORMATED"],
                                                            '#TYPE_TEMPLATE#' => $sotbitBillLink
                                                        )) ?>
                                                    </dd>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                    </dl>
                                    <? if ($arResult["PAYED"] != "Y") : ?>
                                        <dt class="sale-order-detail-payment-options-methods-info">
                                            <button class="sale-order-detail-payment-options-methods-info-change-link btn" id="<?= $payment['ACCOUNT_NUMBER'] ?>" <?= $arResult["LOCK_CHANGE_PAYSYSTEM"] === "Y" ? 'title="' . Loc::getMessage("SPOD_LOCK_CHANGE_PAYSYSTEM_TITLE") . '" disabled' : '' ?>>
                                                <?= Loc::getMessage("SPOD_CHANGE_PAYMENT_TYPE") ?>
                                            </button>
                                        </dt>
                                    <? endif; ?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage("SPOD_ORDER_SHIPMENT") ?></h6>
                            <div class="d-inline-flex ms-auto">
                                <a class="text-body px-2" data-card-action="collapse">
                                    <i class="ph ph-caret-down"></i>
                                </a>
                            </div>
                        </div>
                        <? foreach ($arResult["SHIPMENT"] as $k => $shipment) : ?>
                            <?
                            $shipmentSubTitle = Loc::getMessage('SOPC_TPL_SHIPMENT') . " " . Loc::getMessage('SOPC_TPL_NUMBER_SIGN') . $shipment['ACCOUNT_NUMBER'];
                            if (isset($shipment['DATE_INSERT'])) {
                                $shipmentSubTitle .= " " . Loc::getMessage('SOPC_TPL_FROM_DATE') . " " . $shipment['DATE_INSERT']->format("d.m.Y");
                            }
                            ?>
                            <div class="collapse show">
                                <div class="card-body pt-0">
                                    <dl class="card-content">
                                        <div class="card-content__row">
                                            <dt>
                                                <?= $shipmentSubTitle ?>
                                            </dt>
                                            <dd>
                                                <? if ($shipment['DEDUCTED'] === 'Y') : ?>
                                                    <span class="badge bg-success bg-opacity-20 text-success rounded-pill small-text">
                                                        <?= Loc::getMessage('SPOD_SHIPMENTS_DEDUCTED_Y') ?>
                                                    </span>
                                                <? else : ?>
                                                    <span class="badge bg-danger bg-opacity-20 text-danger rounded-pill small-text">
                                                        <?= Loc::getMessage('SPOD_SHIPMENTS_DEDUCTED_N') ?>
                                                    </span>
                                                <? endif; ?>
                                            </dd>
                                        </div>
                                        <div class="card-content__row">
                                            <dt>
                                                <?= Loc::getMessage("SPOD_ORDER_DELIVERY") ?>
                                            </dt>
                                            <dd>
                                                <?= $shipment["DELIVERY"]['NAME'] ?>
                                            </dd>
                                        </div>
                                        <div class="card-content__row">
                                            <dt>
                                                <?= Loc::getMessage("SPOD_ORDER_SHIPMENT_STATUS") ?>
                                            </dt>
                                            <dd>
                                                <?= $shipment['STATUS_NAME'] ?>
                                            </dd>
                                        </div>
                                        <div class="card-content__row">
                                            <dt>
                                                <?= Loc::getMessage("SPOD_DELIVERY") ?>
                                            </dt>
                                            <dd>
                                                <?= $shipment['PRICE_DELIVERY_FORMATED'] ?>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
                <div class="blank_detail_products row">
                    <div class="blank_detail_table col-md-8">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.ui.filter",
                            "b2bcabinet",
                            [
                                'FILTER_ID' => 'PRODUCT_LIST',
                                'GRID_ID' => 'PRODUCT_LIST',
                                'FILTER' => [
                                    [
                                        'id' => 'NAME',
                                        'name' => Loc::getMessage('SPOD_NAME'),
                                        'type' => 'string'
                                    ],
                                    [
                                        'id' => 'ARTICLE',
                                        'name' => Loc::getMessage('SPOD_ARTICLE'),
                                        'type' => 'string'
                                    ],
                                    [
                                        'id' => 'QUANTITY',
                                        'name' => Loc::getMessage('SPOD_QUANTITY'),
                                        'type' => 'string'
                                    ],
                                    [
                                        'id' => 'PRICE',
                                        'name' => Loc::getMessage('SPOD_PRICE'),
                                        'type' => 'string'
                                    ],
                                    [
                                        'id' => 'SUM',
                                        'name' => Loc::getMessage('SPOD_ORDER_PRICE'),
                                        'type' => 'string'
                                    ],
                                ],
                                'ENABLE_LIVE_SEARCH' => true,
                                'ENABLE_LABEL' => true
                            ]
                        );
                        ?>
                        <?
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.ui.grid',
                            'simple',
                            array(
                                'GRID_ID' => 'PRODUCT_LIST',
                                'HEADERS' => array(
                                    array(
                                        "id" => "NAME",
                                        "name" => Loc::getMessage('SPOD_NAME'),
                                        "sort" => "NAME",
                                        "default" => true
                                    ),
                                    array(
                                        "id" => "ARTICLE",
                                        "name" => Loc::getMessage('SPOD_ARTICLE'),
                                        "sort" => "ARTICLE",
                                        "align" => "right",
                                        "default" => true,
                                    ),
                                    array(
                                        "id" => "QUANTITY",
                                        "name" => Loc::getMessage('SPOD_QUANTITY'),
                                        "sort" => "QUANTITY",
                                        "align" => "right",
                                        "default" => true
                                    ),
                                    array(
                                        "id" => "DISCOUNT",
                                        "name" => Loc::getMessage('SPOD_DISCOUNT'),
                                        "sort" => "DISCOUNT",
                                        "align" => "right",
                                        "default" => true
                                    ),
                                    array(
                                        "id" => "PRICE",
                                        "name" => Loc::getMessage('SPOD_PRICE'),
                                        "sort" => "PRICE",
                                        "align" => "right",
                                        "default" => true
                                    ),
                                    array(
                                        "id" => "SUM",
                                        "name" => Loc::getMessage('SPOD_ORDER_PRICE'),
                                        "sort" => "SUM",
                                        "align" => "right",
                                        "default" => true
                                    ),
                                ),
                                'ROWS' => $arResult['PRODUCT_ROWS'],
                                'FILTER_STATUS_NAME' => '',
                                'AJAX_MODE' => 'Y',
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "N",
                                "AJAX_OPTION_HISTORY" => "N",

                                "ALLOW_COLUMNS_SORT" => true,
                                "ALLOW_ROWS_SORT" => array(),
                                "ALLOW_COLUMNS_RESIZE" => false,
                                "ALLOW_HORIZONTAL_SCROLL" => false,
                                "ALLOW_SORT" => false,
                                "ALLOW_PIN_HEADER" => true,
                                "ACTION_PANEL" => array(),

                                "SHOW_CHECK_ALL_CHECKBOXES" => false,
                                "SHOW_ROW_CHECKBOXES" => false,
                                "SHOW_ROW_ACTIONS_MENU" => true,
                                "SHOW_GRID_SETTINGS_MENU" => true,
                                "SHOW_NAVIGATION_PANEL" => true,
                                "SHOW_PAGINATION" => true,
                                "SHOW_SELECTED_COUNTER" => false,
                                "SHOW_TOTAL_COUNTER" => true,
                                "SHOW_PAGESIZE" => true,
                                "SHOW_ACTION_PANEL" => true,

                                "ENABLE_COLLAPSIBLE_ROWS" => true,
                                'ALLOW_SAVE_ROWS_STATE' => true,

                                "SHOW_MORE_BUTTON" => false,
                                '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                                'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                                'NAV_STRING' => $arResult['NAV_STRING'],
                                "TOTAL_ROWS_COUNT" => is_array($arResult['PRODUCT_ROWS']) ? count($arResult['PRODUCT_ROWS']) : 0,
                                "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                                "PAGE_SIZES" => 20,
                                "DEFAULT_PAGE_SIZE" => 50
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    </div>
                    <div class="blank_detail-total col-md-4">
                        <div class="card">
                            <div class="card-header card-p-1">
                                <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage("SPOD_ORDER_BASKET") ?></h6>
                            </div>
                            <div class="card-body pt-0">
                                <dl class="card-content">
                                    <div class="card-content__row">
                                        <dt><?= Loc::getMessage("SPOD_QUANTITY") ?></dt>
                                        <dd><?= $arResult['FULL_QUANTITY'] ?></dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt><?= Loc::getMessage("SPOD_ORDER_PRICE_WITHOUT_DOTS") ?></dt>
                                        <dd><?= $arResult['PRODUCT_SUM_FORMATED'] ?></dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt><?= Loc::getMessage("SPOD_TAX") ?></dt>
                                        <dd><?= $arResult['TAX_VALUE_FORMATED'] ?></dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt><?= Loc::getMessage("SPOD_WEIGHT") ?></dt>
                                        <dd><?= $arResult['ORDER_WEIGHT_FORMATED'] ?></dd>
                                    </div>
                                    <div class="card-content__row">
                                        <dt><?= Loc::getMessage("SPOD_DELIVERY") ?></dt>
                                        <dd><?= $arResult['PRICE_DELIVERY_FORMATED'] ?></dd>
                                    </div>
                                    <div class="card-content__row card-content__bold">
                                        <dt><?= Loc::getMessage("SPOD_SUMMARY") ?></dt>
                                        <dd><?= $arResult['PRICE_FORMATED'] ?></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--basic-tab2-->
            <div class="tab-pane fade " id="basic-tab2">
                <div class="blank_detail_table">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.ui.filter",
                        "b2bcabinet",
                        [
                            'FILTER_ID' => 'PRODUCT_LIST_2',
                            'GRID_ID' => 'PRODUCT_LIST_2',
                            'FILTER' => [
                                [
                                    'id' => 'NAME',
                                    'name' => Loc::getMessage('SPOD_NAME'),
                                    'type' => 'string'
                                ],
                                [
                                    'id' => 'ARTICLE',
                                    'name' => Loc::getMessage('SPOD_ARTICLE'),
                                    'type' => 'string'
                                ],
                                [
                                    'id' => 'QUANTITY',
                                    'name' => Loc::getMessage('SPOD_QUANTITY'),
                                    'type' => 'string'
                                ],
                                [
                                    'id' => 'SUM',
                                    'name' => Loc::getMessage('SPOD_PRICE'),
                                    'type' => 'string'
                                ],
                            ],
                            'ENABLE_LIVE_SEARCH' => true,
                            'ENABLE_LABEL' => true
                        ]
                    );
                    ?>
                    <?
                    $APPLICATION->IncludeComponent(
                        'bitrix:main.ui.grid',
                        'simple',
                        array(
                            'GRID_ID' => 'PRODUCT_LIST_2',
                            'HEADERS' => array(
                                array(
                                    "id" => "NAME",
                                    "name" => Loc::getMessage('SPOD_NAME'),
                                    "sort" => "NAME",
                                    "default" => true
                                ),
                                array(
                                    "id" => "ARTICLE",
                                    "name" => Loc::getMessage('SPOD_ARTICLE'),
                                    "sort" => "ARTICLE",
                                    "default" => true,
                                    "align" => "right"
                                ),
                                array(
                                    "id" => "QUANTITY",
                                    "name" => Loc::getMessage('SPOD_QUANTITY'),
                                    "sort" => "QUANTITY",
                                    "default" => true,
                                    "align" => "right"
                                ),
                                array(
                                    "id" => "SUM",
                                    "name" => Loc::getMessage('SPOD_PRICE'),
                                    "sort" => "SUM",
                                    "default" => true,
                                    "align" => "right"
                                ),
                            ),
                            'ROWS' => $arResult['PRODUCT_2_ROWS'],
                            'FILTER_STATUS_NAME' => '',
                            'AJAX_MODE' => 'Y',
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "N",
                            "AJAX_OPTION_HISTORY" => "N",

                            "ALLOW_COLUMNS_SORT" => true,
                            "ALLOW_ROWS_SORT" => array(),
                            "ALLOW_COLUMNS_RESIZE" => false,
                            "ALLOW_HORIZONTAL_SCROLL" => false,
                            "ALLOW_SORT" => false,
                            "ALLOW_PIN_HEADER" => true,
                            "ACTION_PANEL" => array(),

                            "SHOW_CHECK_ALL_CHECKBOXES" => false,
                            "SHOW_ROW_CHECKBOXES" => false,
                            "SHOW_ROW_ACTIONS_MENU" => false,
                            "SHOW_GRID_SETTINGS_MENU" => true,
                            "SHOW_NAVIGATION_PANEL" => true,
                            "SHOW_PAGINATION" => true,
                            "SHOW_SELECTED_COUNTER" => false,
                            "SHOW_TOTAL_COUNTER" => true,
                            "SHOW_PAGESIZE" => true,
                            "SHOW_ACTION_PANEL" => true,

                            "ENABLE_COLLAPSIBLE_ROWS" => true,
                            'ALLOW_SAVE_ROWS_STATE' => true,

                            "SHOW_MORE_BUTTON" => false,
                            '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                            'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                            'NAV_STRING' => $arResult['NAV_STRING'],
                            "TOTAL_ROWS_COUNT" => count($arResult['PRODUCT_2_ROWS']),
                            "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                            "PAGE_SIZES" => 20,
                            "DEFAULT_PAGE_SIZE" => 50
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </div>
            </div>
            <!--basic-tab3-->
            <div class="tab-pane fade" id="basic-tab3">
                <?
                $APPLICATION->IncludeComponent(
                    'bitrix:main.ui.grid',
                    '',
                    array(
                        'GRID_ID' => 'DOCUMENTS_LIST',
                        'HEADERS' => array(
                            array(
                                "id" => "NUMBER",
                                "name" => Loc::getMessage('SPOD_NUMBER'),
                                "sort" => "NUMBER",
                                "default" => true
                            ),
                            array(
                                "id" => "DOC",
                                "name" => Loc::getMessage('SPOD_DOC'),
                                "sort" => "DOC",
                                "default" => true
                            ),
                            array(
                                "id" => "DATE_CREATED",
                                "name" => Loc::getMessage('SPOD_DATE_CREATED'),
                                "sort" => "DATE_CREATED",
                                "default" => true
                            ),
                            array(
                                "id" => "DATE_UPDATED",
                                "name" => Loc::getMessage('SPOD_DATE_UPDATED'),
                                "sort" => "DATE_UPDATED",
                                "default" => true
                            ),

                        ),
                        'ROWS' => $arResult['DOCS_ROWS'],
                        'FILTER_STATUS_NAME' => '',
                        'AJAX_MODE' => 'Y',
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",

                        "ALLOW_COLUMNS_SORT" => true,
                        "ALLOW_ROWS_SORT" => array(),
                        "ALLOW_COLUMNS_RESIZE" => false,
                        "ALLOW_HORIZONTAL_SCROLL" => false,
                        "ALLOW_SORT" => false,
                        "ALLOW_PIN_HEADER" => true,
                        "ACTION_PANEL" => array(),

                        "SHOW_CHECK_ALL_CHECKBOXES" => false,
                        "SHOW_ROW_CHECKBOXES" => false,
                        "SHOW_ROW_ACTIONS_MENU" => true,
                        "SHOW_GRID_SETTINGS_MENU" => true,
                        "SHOW_NAVIGATION_PANEL" => true,
                        "SHOW_PAGINATION" => true,
                        "SHOW_SELECTED_COUNTER" => false,
                        "SHOW_TOTAL_COUNTER" => true,
                        "SHOW_PAGESIZE" => true,
                        "SHOW_ACTION_PANEL" => true,

                        "ENABLE_COLLAPSIBLE_ROWS" => true,
                        'ALLOW_SAVE_ROWS_STATE' => true,

                        "SHOW_MORE_BUTTON" => false,
                        '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                        'NAV_STRING' => $arResult['NAV_STRING'],
                        "TOTAL_ROWS_COUNT" => count(is_array($arResult['DOCS_ROWS']) ? $arResult['DOCS_ROWS'] : []),
                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                        "PAGE_SIZES" => 20,
                        "DEFAULT_PAGE_SIZE" => 50
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
            </div>
            <!--basic-tab4-->
            <div class="tab-pane fade" id="basic-tab4">
                <?
                $APPLICATION->IncludeComponent(
                    'bitrix:main.ui.grid',
                    '',
                    array(
                        'GRID_ID' => 'PAY_SYSTEMS_LIST',
                        'HEADERS' => array(
                            array(
                                "id" => "NUMBER",
                                "name" => Loc::getMessage('SPOD_NUMBER'),
                                "sort" => "NUMBER",
                                "default" => true
                            ),
                            array(
                                "id" => "NAME",
                                "name" => Loc::getMessage('SPOD_PRODUCT_NAME'),
                                "sort" => "NAME",
                                "default" => true
                            ),
                            array(
                                "id" => "DATE_CREATED",
                                "name" => Loc::getMessage('SPOD_DATE_CREATED'),
                                "sort" => "DATE_CREATED",
                                "default" => true
                            ),
                            /*array(
                                                "id" => "DATE_UPDATED",
                                                "name" =>Loc::getMessage('SPOD_DATE_UPDATED'),
                                                "sort" => "DATE_UPDATED",
                                                "default" => true
                                            ),*/
                            array(
                                "id" => "SUM",
                                "name" => Loc::getMessage('SPOL_SUM'),
                                "sort" => "SUM",
                                "default" => true
                            ),
                            array(
                                "id" => "IS_PAID",
                                "name" => Loc::getMessage('SPOL_PAYMENT_IS_PAID'),
                                "sort" => "IS_PAID",
                                "default" => true
                            ),
                            array(
                                "id" => "ORGANIZATION",
                                "name" => Loc::getMessage('SPOD_ORGANIZATION'),
                                "sort" => "ORGANIZATION",
                                "default" => true
                            ),

                        ),
                        'ROWS' => $arResult['PAY_SYSTEM_ROWS'],
                        'FILTER_STATUS_NAME' => '',
                        'AJAX_MODE' => 'Y',
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",

                        "ALLOW_COLUMNS_SORT" => true,
                        "ALLOW_ROWS_SORT" => array(),
                        "ALLOW_COLUMNS_RESIZE" => false,
                        "ALLOW_HORIZONTAL_SCROLL" => false,
                        "ALLOW_SORT" => false,
                        "ALLOW_PIN_HEADER" => true,
                        "ACTION_PANEL" => array(),

                        "SHOW_CHECK_ALL_CHECKBOXES" => false,
                        "SHOW_ROW_CHECKBOXES" => false,
                        "SHOW_ROW_ACTIONS_MENU" => true,
                        "SHOW_GRID_SETTINGS_MENU" => true,
                        "SHOW_NAVIGATION_PANEL" => true,
                        "SHOW_PAGINATION" => true,
                        "SHOW_SELECTED_COUNTER" => false,
                        "SHOW_TOTAL_COUNTER" => true,
                        "SHOW_PAGESIZE" => true,
                        "SHOW_ACTION_PANEL" => true,

                        "ENABLE_COLLAPSIBLE_ROWS" => true,
                        'ALLOW_SAVE_ROWS_STATE' => true,

                        "SHOW_MORE_BUTTON" => false,
                        '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                        'NAV_STRING' => $arResult['NAV_STRING'],
                        "TOTAL_ROWS_COUNT" => count($arResult['PAY_SYSTEM_ROWS']),
                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                        "PAGE_SIZES" => 20,
                        "DEFAULT_PAGE_SIZE" => 50
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
            </div>
            <!--basic-tab5-->
            <div class="tab-pane fade" id="basic-tab5">
                <? if (!empty($arResult['SHIPMENT'])) : ?>
                    <div class="row">
                        <? foreach ($arResult['SHIPMENT'] as $shipment) : ?>
                            <div class="col-md-8">
                            <div class="card card-shipment">
                                    <div class="table-responsive">
                                        <table class="table text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th colspan="2"><?= Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_NAME") ?></th>
                                                    <th class="text-end"><?= Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_ARTICLE") ?></th>
                                                    <th class="text-end"><?= Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_QNT") ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <? foreach ($shipment["ITEMS"] as $itemID => $item) : ?>
                                                    <tr>
                                                        <td class="pe-0" style="width: 1%"><img class="rounded object-fit-contain" src="<?= $arResult["BASKET"][$itemID]["PICTURE"]["SRC"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg' ?>" alt="<?= $item["NAME"] ?>" width="<?= $arParams["PICTURE_WIDTH"] ?>" height="<?= $arParams["PICTURE_HEIGHT"] ?>"></td>
                                                        <td><a href="<?= $arResult["BASKET"][$itemID]["DETAIL_PAGE_URL"] ?>"><?= $item["NAME"] ?></a></td>
                                                        <td class="text-end"><?= $arResult["BASKET"][$itemID][$arResult["PROPERTY_ARTICLE"]] ?: '' ?></td>
                                                        <td class="text-end"><?= $item["QUANTITY"] . "&nbsp;" . $item["MEASURE_NAME"] ?></td>
                                                    </tr>
                                                <? endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <? if ($shipment["ITEMS"]) : ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header card-p-1">
                                        <h6 class="card-title mb-0 fw-bold">
                                        <?= Loc::getMessage(
                                                                'SPOD_SUB_ORDER_SHIPMENT_TITLE',
                                                                [
                                                                    '#NUMBER#' => $shipment["ID"],
                                                                    '#DATE#' => $shipment["DATE_INSERT_FORMATED"],
                                                                ]
                                                            ) ?>  
                                        </h6>
                                    </div>
                                    <div class="card-body pt-0">
                                        <dl class="card-content">
                                            <div class="card-content__row">
                                                <dt><?= Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_NAME') ?></dt>
                                                <dd><?= $shipment["DELIVERY_NAME"] ?></dd>
                                            </div>
                                            <div class="card-content__row">
                                                <dt><?= Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_PRICE') ?></dt>
                                                <dd><?= $shipment["PRICE_DELIVERY_FORMATED"] ?></dd>
                                            </div>
                                            <div class="card-content__row">
                                                <dt><?= Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_ALLOW_DELIVERY_Y') ?></dt>
                                                <dd><?= loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_ALLOW_DELIVERY_" . $shipment["ALLOW_DELIVERY"]); ?></dd>
                                            </div>
                                            <div class="card-content__row">
                                                <dt><?= Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_DEDUCTED') ?></dt>
                                                <dd><?= loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_DEDUCTED_" . $shipment["DEDUCTED"]); ?></dd>
                                            </div>
                                            <div class="card-content__row">
                                                <dt><?= Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_STATUS') ?></dt>
                                                <dd><?= $shipment["STATUS_NAME"] ?></dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <? endif; ?>
                        <? endforeach; ?>
                </div>
                <? else : ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="ph-info me-2"></i>
                        <?= Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_EMPTY_TITLE") ?>
                    </div>
                <? endif; ?>
            </div>
            <!--basic-tab6-->
            <div class="tab-pane fade" id="basic-tab6">
                <div class="card">
                    <div class="card-body">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:support.ticket.edit",
                            "b2bcabinet_detail",
                            array(
                                "ID" => $arResult["TICKET"]["ID"],
                                "AJAX_MODE" => "Y",
                                "MESSAGES_PER_PAGE" => "20",
                                "MESSAGE_MAX_LENGTH" => "70",
                                "MESSAGE_SORT_ORDER" => "asc",
                                "SET_PAGE_TITLE" => "N",
                                "SHOW_COUPON_FIELD" => "N",
                                "TICKET_EDIT_TEMPLATE" => "#",
                                "TICKET_LIST_URL" => $methodIstall . 'orders/',
                                "ORDER_ID" => $arResult["ID"],
                                "COMPONENT_TEMPLATE" => "b2bcabinet_detail"
                            ),
                            false
                        );
                        ?>
                    </div>
                </div>
            </div>
            <?
            if ($complaintsType == "ORDER" && !empty($arResult['COMPLAINTS_ROW'])) { ?>
                <!--basic-tab7-->
                <div class="tab-pane fade" id="basic-tab7">
                    <?
                    $APPLICATION->IncludeComponent(
                        'bitrix:main.ui.grid',
                        '',
                        array(
                            'GRID_ID' => 'COMPLAINTS_LIST',
                            'HEADERS' => array(
                                array(
                                    "id" => "ID",
                                    "name" => "ID",
                                    "sort" => "ID",
                                    "default" => true
                                ),
                                array(
                                    "id" => "NAME",
                                    "name" => Loc::getMessage('SOPC_COMPLAINTS_NAME'),
                                    "sort" => "NAME",
                                    "default" => true
                                ),
                                array(
                                    "id" => "STATUS",
                                    "name" => Loc::getMessage('SOPC_COMPLAINTS_STATUS'),
                                    "sort" => "STATUS",
                                    "default" => true
                                ),
                            ),
                            'ROWS' => $arResult['COMPLAINTS_ROW'],
                            'FILTER_STATUS_NAME' => '',
                            'AJAX_MODE' => 'Y',
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "N",
                            "AJAX_OPTION_HISTORY" => "N",

                            "ALLOW_COLUMNS_SORT" => true,
                            "ALLOW_ROWS_SORT" => array(),
                            "ALLOW_COLUMNS_RESIZE" => false,
                            "ALLOW_HORIZONTAL_SCROLL" => false,
                            "ALLOW_SORT" => false,
                            "ALLOW_PIN_HEADER" => true,
                            "ACTION_PANEL" => array(),

                            "SHOW_CHECK_ALL_CHECKBOXES" => false,
                            "SHOW_ROW_CHECKBOXES" => false,
                            "SHOW_ROW_ACTIONS_MENU" => true,
                            "SHOW_GRID_SETTINGS_MENU" => true,
                            "SHOW_NAVIGATION_PANEL" => true,
                            "SHOW_PAGINATION" => true,
                            "SHOW_SELECTED_COUNTER" => false,
                            "SHOW_TOTAL_COUNTER" => true,
                            "SHOW_PAGESIZE" => true,
                            "SHOW_ACTION_PANEL" => true,

                            "ENABLE_COLLAPSIBLE_ROWS" => true,
                            'ALLOW_SAVE_ROWS_STATE' => true,

                            "SHOW_MORE_BUTTON" => false,
                            '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                            'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                            'NAV_STRING' => $arResult['NAV_STRING'],
                            "TOTAL_ROWS_COUNT" => count($arResult['COMPLAINTS_ROW']),
                            "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                            "PAGE_SIZES" => 20,
                            "DEFAULT_PAGE_SIZE" => 50
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </div>
            <? } ?>
        </div>
    </div>

    <div class="card card-position-sticky blank_detail_actions">
        <div class="card-body d-flex gap-3">
            <a href="<?= $arResult['URL_TO_COPY'] ?>" class="btn btn-primary"><?= Loc::getMessage('SPOD_ORDER_REPEAT') ?></a>
            <? if ($complaintsType == "ORDER"  && !empty($complaintsPath)) : ?>
                <a href="<?= $complaintsPath ?>add/?orderId=<?= $arResult['ID'] ?>" class="btn btn-primary"><?= Loc::getMessage('SPOD_ORDER_COMPLAINTS_ADD') ?></a>
            <? endif; ?>
            <? if ($arResult['CAN_CANCEL'] !== "N") : ?>
                <a href="<?= $arResult['URL_TO_CANCEL'] ?>" class="btn"><?= Loc::getMessage('SPOD_ORDER_CANCEL') ?></a>
            <? endif; ?>
        </div>
    </div>
    <script>
        $(function() {
            var b2bOrder = new B2bOrderDetail({
                'ajaxUrl': '<?= CUtil::JSEscape($this->__component->GetPath() . '/ajax.php'); ?>',
                'changePayment': '.sale-order-detail-payment-options-methods-info-change-link',
                'changePaymentWrapper': '.payment-wrapper',
                "paymentList": <?= CUtil::PhpToJSObject($paymentData); ?>,
                "arParams": <?= Json::encode($arResult['PARAMS']); ?>,
                'filter': <?= Json::encode($arResult['FILTER_EXCEL']); ?>,
                'qnts': <?= Json::encode($arResult['QNTS']); ?>,
                "arResult": <?= CUtil::PhpToJSObject($arResult['BASKET'], false, true); ?>,
                "TemplateFolder": '<?= $templateFolder ?>',
                "OrderId": "<?= $arResult["ID"] ?>",
                "Headers": <?= CUtil::PhpToJSObject($Headers, false, true); ?>,
                "HeadersSum": <?= CUtil::PhpToJSObject($HeadersSum, false, true); ?>,
                "TemplateName": 'b2bcabinet',
                "templateSigns": "<?= $signer->sign(SITE_TEMPLATE_ID, "template_preview".bitrix_sessid()) ?>"
            });
        })

        $('.b2b_detail_order__second__tab__btn').on('click', function() {
            $('.b2b_detail_order__second__tab__btn__block').toggle();
        });

        $('.b2b_detail_order__nav_ul__block a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        })
        //BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<? //=$javascriptParams?>//);
    </script>
    <?
}
?>