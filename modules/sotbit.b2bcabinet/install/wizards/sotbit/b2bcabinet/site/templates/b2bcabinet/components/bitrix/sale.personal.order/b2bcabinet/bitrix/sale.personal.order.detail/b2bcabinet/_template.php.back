<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
    SITE_DIR.'b2bcabinet/' :
    SITE_DIR;

$sotbitBillLink = $protocol . $_SERVER['SERVER_NAME'] . $methodIstall;

$order = \Bitrix\Sale\Order::load($arResult["ID"]);
$paymentCollection = $order->getPaymentCollection();

foreach ($paymentCollection as $i => $payment) {
    $id = $payment->getField('ID');

    foreach ($arResult['PAYMENT'] as $k => $pay)
    {
        if($pay['ID'] == $id)
        {
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

$APPLICATION->AddChainItem(Loc::getMessage('SPOD_ORDER') ." ". Loc::getMessage('SPOD_NUM_SIGN') . htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]));
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header header-elements-inline blank_detail-card_headers">
                        <h6 class="card-title">
                            <?= Loc::getMessage('SPOD_LIST_MY_ORDER_TITLE_SINGLE') ?>
                        </h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                                <a class="list-icons-item" data-action="reload"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="blank_detail-menu">
                            <ul class="nav nav-tabs nav-tabs-highlight">
                                <li class="nav-item">
                                    <a href="#basic-tab1" class="nav-link active show" data-toggle="tab" onclick="writeActiveTab('#basic-tab1')"><?=Loc::getMessage('SPOD_TAB_COMMON')?></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basic-tab2" class="nav-link" data-toggle="tab" onclick="writeActiveTab('#basic-tab2')"><?=Loc::getMessage('SPOD_TAB_GOODS')?></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basic-tab3" class="nav-link" data-toggle="tab" onclick="writeActiveTab('#basic-tab3')"><?=Loc::getMessage('SPOD_TAB_DOCS')?></a></li>
                                <li class="nav-item">
                                    <a href="#basic-tab4" class="nav-link" data-toggle="tab" onclick="writeActiveTab('#basic-tab4')"><?=Loc::getMessage('SPOD_TAB_PAYS')?></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#basic-tab5" class="nav-link" data-toggle="tab" onclick="writeActiveTab('#basic-tab5')"><?=Loc::getMessage('SPOD_TAB_SHIPMENTS')?></a></li>
                                <li class="nav-item">
                                    <a href="#basic-tab6" class="nav-link" data-toggle="tab" onclick="writeActiveTab('#basic-tab6')"><?=Loc::getMessage('SPOD_TAB_SUPPORT')?></a>
                                </li>
                                <?if ($complaintsType == "ORDER" && !empty($arResult['COMPLAINTS_ROW'])) {?>
                                    <li class="nav-item">
                                        <a href="#basic-tab7" class="nav-link" data-toggle="tab"
                                           onclick="writeActiveTab('#basic-tab7')"><?= Loc::getMessage('SPOD_TAB_COMPAINTS') ?></a>
                                    </li>
                                <?}?>
                            </ul>
                            <div class="btn-group blank_detail-dropdown_menu">
                                <button type="button" class="btn btn-primary b2b_detail_order__second__tab__btn" data-toggle="dropdown" aria-expanded="false">
                                    <?=Loc::getMessage('SPOD_ACTIONS')?>
                                </button>
                                <div class="dropdown-menu b2b_detail_order__second__tab__btn__block" x-placement="bottom-end">
                                    <?if($arResult['CAN_CANCEL'] !== "N"):?>
                                        <a href="<?=$arResult['URL_TO_CANCEL']?>" class="dropdown-item"><?=Loc::getMessage('SPOD_ORDER_CANCEL')?></a>
                                    <?endif;?>
                                    <a href="<?=$arResult['URL_TO_COPY']?>" class="dropdown-item"><?=Loc::getMessage('SPOD_ORDER_REPEAT')?></a>
                                    <? if ($complaintsType == "ORDER"  && !empty($complaintsPath)): ?>
                                        <a href="<?=$complaintsPath?>add/?orderId=<?= $arResult['ID'] ?>"
                                           class="dropdown-item"><?= Loc::getMessage('SPOD_ORDER_COMPLAINTS_ADD') ?></a>
                                    <?endif; ?>

                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <!--basic-tab1-->
                            <div class="tab-pane fade show active" id="basic-tab1">
                                <div class="row">
                                    <div class="flex-column">
                                        <div class="col-md-12 my-2">
                                            <div class="card">
                                                <div class="card-header header-elements-inline blank_detail-card_headers">
                                                    <h6 class="card-title"><?= Loc::getMessage('SPOD_SUB_ORDER_TITLE', array(
                                                            "#ACCOUNT_NUMBER#"=> htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
                                                            "#DATE_ORDER_CREATE#"=> $arResult["DATE_INSERT_FORMATED"]
                                                        ))?></h6>
                                                    <div class="header-elements">
                                                        <div class="list-icons">
                                                            <a class="list-icons-item" data-action="collapse"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body bg-light mb-0">
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-3">
                                                            <?= Loc::getMessage('SPOD_ORDER_STATUS', array(
                                                                '#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]
                                                            )) ?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?
                                                            if ($arResult['CANCELED'] !== 'Y') {
                                                                echo htmlspecialcharsbx($arResult["STATUS"]["NAME"] . " (".Loc::getMessage('SPOD_FROM')." " . $arResult["DATE_INSERT_FORMATED"] . ")");
                                                            } else {
                                                                echo Loc::getMessage('SPOD_ORDER_CANCELED');
                                                            }
                                                            ?>
                                                        </dd>
                                                        <dt class="col-sm-3">
                                                            <?=Loc::getMessage("SPOD_ORDER_PRICE")?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?= $arResult["PRICE_FORMATED"]?>
                                                        </dd>
                                                        <dt class="col-sm-3">
                                                            <?=Loc::getMessage('SPOD_ORDER_CANCELED');?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?=($arResult['CANCELED'] == "N" ? Loc::getMessage("SPOD_NO") : Loc::getMessage("SPOD_YES"))?>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <div class="card">
                                                <div class="card-header header-elements-inline blank_detail-card_headers">
                                                    <h6 class="card-title"><?= Loc::getMessage("SPOD_USER_BUYER")?></h6>
                                                    <div class="header-elements">
                                                        <div class="list-icons">
                                                            <a class="list-icons-item" data-action="collapse"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body bg-light mb-0">
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-3">
                                                            <?=Loc::getMessage("SPOD_ACCOUNT")?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?=$arResult['USER']['LOGIN'];?>
                                                        </dd>
                                                        <dt class="col-sm-3">
                                                            <?=Loc::getMessage("SPOD_PERSON_TYPE_NAME")?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?= $arResult["PERSON_TYPE"]["NAME"]?>
                                                        </dd>
                                                        <dt class="col-sm-3">
                                                            <?=Loc::getMessage('SPOD_EMAIL');?>
                                                        </dt>
                                                        <dd class="col-sm-9">
                                                            <?=$arResult['USER']["EMAIL"]?>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                        <?if ($arResult["PRINT_ORDER_PROPS"]):
                                            foreach ($arResult["PRINT_ORDER_PROPS"] as $orderGroup):?>
                                                <?if (!$orderGroup["PROPS"]) {continue;}?>
                                                <div class="col-md-12 my-2">
                                                    <div class="card">
                                                        <div class="card-header header-elements-inline blank_detail-card_headers">
                                                            <h6 class="card-title"><?= $orderGroup["NAME"]?></h6>
                                                            <div class="header-elements">
                                                                <div class="list-icons">
                                                                    <a class="list-icons-item" data-action="collapse"></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body bg-light mb-0">
                                                            <dl class="row mb-0">
                                                                <?foreach ($orderGroup["PROPS"] as $orderProp):?>
                                                                    <?if($orderProp['VALUE']):?>
                                                                        <dt class="col-sm-3">
                                                                            <?=$orderProp['NAME']?>
                                                                        </dt>
                                                                        <dd class="col-sm-9">
                                                                            <?
                                                                            if ($orderProp['MULTIPLE'] == "Y") {
                                                                                $orderProp['VALUE'] = is_array(unserialize($orderProp['VALUE'])) ? implode("<br>", unserialize($orderProp['VALUE'])) : unserialize($orderProp['VALUE']);
                                                                            }
                                                                            ?>
                                                                            <?=$orderProp['VALUE']?>
                                                                        </dd>
                                                                    <?endif;?>
                                                                <?endforeach;?>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?endforeach;
                                        endif;?>
                                    </div>
                                    <div class="flex-column">
                                        <div class="col-md-12 my-2">
                                            <div class="card">
                                                <div class="card-header header-elements-inline blank_detail-card_headers">
                                                    <h6 class="card-title"><?= Loc::getMessage("SPOD_ORDER_PAYMENT")?></h6>
                                                    <div class="header-elements">
                                                        <div class="list-icons">
                                                            <a class="list-icons-item" data-action="collapse"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?foreach ($arResult["PAYMENT"] as $k => $payment):?>
                                                    <?
                                                    $paymentSubTitle = Loc::getMessage('SOPC_TPL_BILL')." ".Loc::getMessage('SOPC_TPL_NUMBER_SIGN').$payment['ACCOUNT_NUMBER'];
                                                    if(isset($payment['DATE_BILL']))
                                                    {
                                                        $paymentSubTitle .= " ".Loc::getMessage('SOPC_TPL_FROM_DATE')." ".$payment['DATE_BILL']->format("d.m.Y");
                                                    }
                                                    $paymentSubTitle .= ", " . htmlspecialcharsbx($payment['PAY_SYSTEM_NAME']);
                                                    ?>
                                                    <div class="card-body bg-light mb-0">
                                                        <h6 class="payment-title font-size-sm font-weight-bold">
                                                            <span>
                                                                <?=$paymentSubTitle?>
                                                            </span>
                                                            <?if($payment['PAID'] === 'Y'):?>
                                                                <span class="badge badge-flat badge-pill border-success text-success-600">
                                                                   <?=Loc::getMessage('SOPC_TPL_PAID')?>
                                                                </span>
                                                            <?elseif ($arResult['IS_ALLOW_PAY'] == 'N'):?>
                                                                <span class="badge badge-flat badge-pill border-warning text-warning-600">
                                                                    <?=Loc::getMessage('SOPC_TPL_RESTRICTED_PAID')?>
                                                                </span>
                                                            <?else:?>
                                                                <span class="badge badge-flat badge-pill border-danger text-danger-600">
                                                                    <?=Loc::getMessage('SOPC_TPL_NOTPAID')?>
                                                                </span>
                                                            <?endif;?>
                                                        </h6>

                                                        <dl class="row mb-0 payment-wrapper" data-id="<?=$payment['ACCOUNT_NUMBER']?>">
                                                            <dt class="col-sm-3">
                                                                <?= Loc::getMessage("SPOD_PAY_SYSTEM")?>
                                                            </dt>
                                                            <dd class="col-sm-9">
                                                                <?=$payment['PAY_SYSTEM']['NAME']?>
                                                            </dd>
                                                            <dt class="col-sm-3">
                                                                <?=Loc::getMessage("SPOD_ORDER_PAYED")?>
                                                            </dt>
                                                            <dd class="col-sm-9">
                                                                <?=( $payment['PAID'] == 'Y' ? Loc::getMessage("SPOD_YES") : Loc::getMessage("SPOD_NO") )?>
                                                            </dd>

                                                            <?if((stripos($payment['PAY_SYSTEM']['ACTION_FILE'],'billsotbit') !== false || $payment['PAY_SYSTEM']['ACTION_FILE'] == 'orderdocument') && $arResult['IS_ALLOW_PAY'] !== 'N'):?>

                                                                <dt class="col-sm-3">
                                                                    <?=Loc::getMessage("SPOD_CHECK_BILL")?>
                                                                </dt>
                                                                <dd class="col-sm-9">
                                                                    <?=Loc::getMessage("SHOW_BILL", array(
                                                                        '#ORDER_ID#' => $arResult["ID"],
                                                                        '#PAYMENT_ID#' => $payment["ID"],
                                                                        '#DATE#' =>	$arResult["DATE_INSERT_FORMATED"],
                                                                        '#TYPE_TEMPLATE#' => $sotbitBillLink
                                                                    ))?>
                                                                </dd>
                                                                <dt class="col-sm-3">
                                                                    <?=Loc::getMessage("SPOD_DOWNLOAD_BILL")?>
                                                                </dt>
                                                                <dd class="col-sm-9">
                                                                    <?=Loc::getMessage("DOWNLOAD_BILL", array(
                                                                        '#ORDER_ID#' => $arResult["ID"],
                                                                        '#PAYMENT_ID#' => $payment["ID"],
                                                                        '#DATE#' =>	$arResult["DATE_INSERT_FORMATED"],
                                                                        '#TYPE_TEMPLATE#' => $sotbitBillLink
                                                                    ))?>
                                                                </dd>
                                                            <?endif;?>
                                                        </dl>
                                                        <?if ($arResult["PAYED"] != "Y"):?>
                                                            <dt class="sale-order-detail-payment-options-methods-info">
                                                                <button
                                                                        class="sale-order-detail-payment-options-methods-info-change-link btn btn-light"
                                                                        id="<?=$payment['ACCOUNT_NUMBER']?>"
                                                                    <?=$arResult["LOCK_CHANGE_PAYSYSTEM"] === "Y" ? 'title="'.Loc::getMessage("SPOD_LOCK_CHANGE_PAYSYSTEM_TITLE").'" disabled' : ''?>
                                                                >
                                                                    <?=Loc::getMessage("SPOD_CHANGE_PAYMENT_TYPE")?>
                                                                </button>
                                                            </dt>
                                                        <?endif;?>
                                                    </div>
                                                <?endforeach;?>
                                            </div>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <div class="card">
                                                <div class="card-header header-elements-inline blank_detail-card_headers">
                                                    <h6 class="card-title"><?= Loc::getMessage("SPOD_ORDER_SHIPMENT")?></h6>
                                                    <div class="header-elements">
                                                        <div class="list-icons">
                                                            <a class="list-icons-item" data-action="collapse"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?foreach ($arResult["SHIPMENT"] as $k => $shipment):?>
                                                    <?
                                                    $shipmentSubTitle = Loc::getMessage('SOPC_TPL_SHIPMENT')." ".Loc::getMessage('SOPC_TPL_NUMBER_SIGN').$shipment['ACCOUNT_NUMBER'];
                                                    if(isset($shipment['DATE_INSERT']))
                                                    {
                                                        $shipmentSubTitle .= " ".Loc::getMessage('SOPC_TPL_FROM_DATE')." ".$shipment['DATE_INSERT']->format("d.m.Y");
                                                    }
                                                    ?>
                                                    <div class="card-body bg-light mb-0">
                                                        <h6 class="payment-title font-size-sm font-weight-bold">
                                                            <span>
                                                                <?=$shipmentSubTitle?>
                                                            </span>
                                                            <?if($shipment['DEDUCTED'] === 'Y'):?>
                                                                <span class="badge badge-flat badge-pill border-success text-success-600">
                                                                   <?=Loc::getMessage('SPOD_SHIPMENTS_DEDUCTED_Y')?>
                                                                </span>
                                                            <?else:?>
                                                                <span class="badge badge-flat badge-pill border-danger text-danger-600">
                                                                    <?=Loc::getMessage('SPOD_SHIPMENTS_DEDUCTED_N')?>
                                                                </span>
                                                            <?endif;?>
                                                        </h6>
                                                        <dl class="row mb-0">
                                                            <dt class="col-sm-3">
                                                                <?=Loc::getMessage("SPOD_ORDER_DELIVERY")?>
                                                            </dt>
                                                            <dd class="col-sm-9">
                                                                <?=$shipment["DELIVERY"]['NAME']?>
                                                            </dd>
                                                            <dt class="col-sm-3">
                                                                <?=Loc::getMessage("SPOD_ORDER_SHIPMENT_STATUS")?>
                                                            </dt>
                                                            <dd class="col-sm-9">
                                                                <?=$shipment['STATUS_NAME']?>
                                                            </dd>
                                                            <dt class="col-sm-3">
                                                                <?=Loc::getMessage("SPOD_DELIVERY")?>
                                                            </dt>
                                                            <dd class="col-sm-9">
                                                                <?=$shipment['PRICE_DELIVERY_FORMATED']?>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                <?endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="blank_detail_table">
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.ui.filter",
                                        "b2bcabinet", [
                                        'FILTER_ID' => 'PRODUCT_LIST',
                                        'GRID_ID' => 'PRODUCT_LIST',
                                        'FILTER' => [
                                            [
                                                'id' => 'NAME',
                                                'name' =>Loc::getMessage('SPOD_NAME'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'ARTICLE',
                                                'name' =>Loc::getMessage('SPOD_ARTICLE'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'QUANTITY',
                                                'name' =>Loc::getMessage('SPOD_QUANTITY'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'PRICE',
                                                'name' =>Loc::getMessage('SPOD_PRICE'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'SUM',
                                                'name' =>Loc::getMessage('SPOD_ORDER_PRICE_WITHOUT_DOTS'),
                                                'type' => 'string'
                                            ],
                                        ],
                                        'ENABLE_LIVE_SEARCH' => true,
                                        'ENABLE_LABEL' => true
                                    ]);
                                    ?>
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.ui.grid',
                                        '',
                                        array(
                                            'GRID_ID' => 'PRODUCT_LIST',
                                            'HEADERS' => array(
                                                array(
                                                    "id" => "NAME",
                                                    "name" =>Loc::getMessage('SPOD_NAME'),
                                                    "sort" => "NAME",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "ARTICLE",
                                                    "name" =>Loc::getMessage('SPOD_ARTICLE'),
                                                    "sort" => "ARTICLE",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "QUANTITY",
                                                    "name" =>Loc::getMessage('SPOD_QUANTITY'),
                                                    "sort" => "QUANTITY",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "DISCOUNT",
                                                    "name" =>Loc::getMessage('SPOD_DISCOUNT'),
                                                    "sort" => "DISCOUNT",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "PRICE",
                                                    "name" =>Loc::getMessage('SPOD_PRICE'),
                                                    "sort" => "PRICE",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "SUM",
                                                    "name" =>Loc::getMessage('SPOD_ORDER_PRICE_WITHOUT_DOTS'),
                                                    "sort" => "SUM",
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
                                            "ALLOW_COLUMNS_RESIZE" => true,
                                            "ALLOW_HORIZONTAL_SCROLL" => true,
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
                                <div class="blank_detail-total">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <th rowspan="2" class="text-center"><h4><?=Loc::getMessage("SPOD_ORDER_BASKET")?></h4></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_QUANTITY")?></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_ORDER_PRICE_WITHOUT_DOTS")?></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_TAX")?></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_WEIGHT")?></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_DELIVERY")?></th>
                                                <th class="text-center"><?=Loc::getMessage("SPOD_SUMMARY")?></th>
                                            </tr>
                                            <tr>
                                                <td class="text-center"><?=$arResult['FULL_QUANTITY']?></td>
                                                <td class="text-center"><?=$arResult['PRODUCT_SUM_FORMATED']?></td>
                                                <td class="text-center"><?=$arResult['TAX_VALUE_FORMATED']?></td>
                                                <td class="text-center"><?=$arResult['ORDER_WEIGHT_FORMATED']?></td>
                                                <td class="text-center"><?=$arResult['PRICE_DELIVERY_FORMATED']?></td>
                                                <td class="text-center"><?=$arResult['PRICE_FORMATED']?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!--basic-tab2-->
                            <div class="tab-pane fade " id="basic-tab2">
                                <div class="blank_detail_table">
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.ui.filter",
                                        "b2bcabinet", [
                                        'FILTER_ID' => 'PRODUCT_LIST_2',
                                        'GRID_ID' => 'PRODUCT_LIST_2',
                                        'FILTER' => [
                                            [
                                                'id' => 'NAME',
                                                'name' =>Loc::getMessage('SPOD_NAME'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'ARTICLE',
                                                'name' =>Loc::getMessage('SPOD_ARTICLE'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'QUANTITY',
                                                'name' =>Loc::getMessage('SPOD_QUANTITY'),
                                                'type' => 'string'
                                            ],
                                            [
                                                'id' => 'SUM',
                                                'name' =>Loc::getMessage('SPOD_PRICE'),
                                                'type' => 'string'
                                            ],
                                        ],
                                        'ENABLE_LIVE_SEARCH' => true,
                                        'ENABLE_LABEL' => true
                                    ]);
                                    ?>
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.ui.grid',
                                        '',
                                        array(
                                            'GRID_ID' => 'PRODUCT_LIST_2',
                                            'HEADERS' => array(
                                                array(
                                                    "id" => "NAME",
                                                    "name" =>Loc::getMessage('SPOD_NAME'),
                                                    "sort" => "NAME",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "ARTICLE",
                                                    "name" =>Loc::getMessage('SPOD_ARTICLE'),
                                                    "sort" => "ARTICLE",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "QUANTITY",
                                                    "name" =>Loc::getMessage('SPOD_QUANTITY'),
                                                    "sort" => "QUANTITY",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "SUM",
                                                    "name" =>Loc::getMessage('SPOD_PRICE'),
                                                    "sort" => "SUM",
                                                    "default" => true
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
                                            "ALLOW_COLUMNS_RESIZE" => true,
                                            "ALLOW_HORIZONTAL_SCROLL" => true,
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
                                <div class="card">
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.ui.grid',
                                        '',
                                        array(
                                            'GRID_ID' => 'DOCUMENTS_LIST',
                                            'HEADERS' => array(
                                                array(
                                                    "id" => "NUMBER",
                                                    "name" =>Loc::getMessage('SPOD_NUMBER'),
                                                    "sort" => "NUMBER",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "DOC",
                                                    "name" =>Loc::getMessage('SPOD_DOC'),
                                                    "sort" => "DOC",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "DATE_CREATED",
                                                    "name" =>Loc::getMessage('SPOD_DATE_CREATED'),
                                                    "sort" => "DATE_CREATED",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "DATE_UPDATED",
                                                    "name" =>Loc::getMessage('SPOD_DATE_UPDATED'),
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
                                            "ALLOW_COLUMNS_RESIZE" => true,
                                            "ALLOW_HORIZONTAL_SCROLL" => true,
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
                            </div>
                            <!--basic-tab4-->
                            <div class="tab-pane fade" id="basic-tab4">
                                <div class="card">
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.ui.grid',
                                        '',
                                        array(
                                            'GRID_ID' => 'PAY_SYSTEMS_LIST',
                                            'HEADERS' => array(
                                                array(
                                                    "id" => "NUMBER",
                                                    "name" =>Loc::getMessage('SPOD_NUMBER'),
                                                    "sort" => "NUMBER",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "NAME",
                                                    "name" =>Loc::getMessage('SPOD_PRODUCT_NAME'),
                                                    "sort" => "NAME",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "DATE_CREATED",
                                                    "name" =>Loc::getMessage('SPOD_DATE_CREATED'),
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
                                                    "name" =>Loc::getMessage('SPOL_SUM'),
                                                    "sort" => "SUM",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "IS_PAID",
                                                    "name" =>Loc::getMessage('SPOL_PAYMENT_IS_PAID'),
                                                    "sort" => "IS_PAID",
                                                    "default" => true
                                                ),
                                                array(
                                                    "id" => "ORGANIZATION",
                                                    "name" =>Loc::getMessage('SPOD_ORGANIZATION'),
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
                                            "ALLOW_COLUMNS_RESIZE" => true,
                                            "ALLOW_HORIZONTAL_SCROLL" => true,
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
                            </div>
                            <!--basic-tab5-->
                            <div class="tab-pane fade" id="basic-tab5">
                                <?if (!empty($arResult['SHIPMENT'])):?>
                                    <?foreach ($arResult['SHIPMENT'] as $shipment):?>
                                    <div class="card card-shipment__wrap">
                                        <div class="card card-shipment">
                                            <div class="card-header header-elements-inline">
                                                <h6 class="card-title">
                                                    <?=Loc::getMessage(
                                                            'SPOD_SUB_ORDER_SHIPMENT_TITLE',
                                                            [
                                                                '#NUMBER#' => $shipment["ID"],
                                                                '#DATE#' => $shipment["DATE_INSERT_FORMATED"],
                                                            ]
                                                    )?>
                                                </h6>
                                            </div>
                                            <div class="table-responsive">
                                                    <table class="table text-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th><?=Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_NAME')?></th>
                                                                <th><?=Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_PRICE')?></th>
                                                                <th><?=Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_ALLOW_DELIVERY_Y')?></th>
                                                                <th><?=Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_DEDUCTED')?></th>
                                                                <th><?=Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT_STATUS')?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                   <?=$shipment["DELIVERY_NAME"]?>
                                                                </td>
                                                                <td>
                                                                    <?=$shipment["PRICE_DELIVERY_FORMATED"]?>
                                                                </td>
                                                                <td>
                                                                    <?=loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_ALLOW_DELIVERY_" . $shipment["ALLOW_DELIVERY"]);?>
                                                                </td>
                                                                <td>
                                                                    <?=loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_DEDUCTED_" . $shipment["DEDUCTED"]);?>
                                                                </td>
                                                                <td>
                                                                    <?=$shipment["STATUS_NAME"]?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
                                        <?if ($shipment["ITEMS"]):?>
                                            <div class="card card-shipment">
                                                <div class="card-header header-elements-inline">
                                                    <h6 class="card-title">
                                                        <?=Loc::getMessage(
                                                            'SPOD_SUB_ORDER_SHIPMENT_POSITIONS_TITLE',
                                                            [
                                                                '#NUMBER#' => $shipment["ID"],
                                                            ]
                                                        )?>
                                                    </h6>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table text-nowrap">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="2"><?=Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_NAME")?></th>
                                                            <th><?=Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_ARTICLE")?></th>
                                                            <th><?=Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_POS_QNT")?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?foreach ($shipment["ITEMS"] as $itemID => $item):?>
                                                                   <tr>
                                                                       <td class="pr-0" style="width: 45px;"><img src="<?=$arResult["BASKET"][$itemID]["PICTURE"]["SRC"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'?>" alt="<?=$item["NAME"]?>" width="<?=$arParams["PICTURE_WIDTH"]?>"></td>
                                                                       <td><a href="<?=$arResult["BASKET"][$itemID]["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></td>
                                                                       <td><?=$arResult["BASKET"][$itemID][$arResult["PROPERTY_ARTICLE"]] ?: ''?></td>
                                                                       <td><?=$item["QUANTITY"]."&nbsp;".$item["MEASURE_NAME"]?></td>
                                                                   </tr>
                                                            <?endforeach;?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?endif;?>
                                    </div>
                                    <?endforeach;?>
                                <?else:?>
                                    <div class="alert alert-info bg-white alert-styled-left alert-dismissible">
                                        <?=Loc::getMessage("SPOD_SUB_ORDER_SHIPMENT_EMPTY_TITLE")?>
                                    </div>
                                <?endif;?>
                            </div>
                            <!--basic-tab6-->
                            <div class="tab-pane fade" id="basic-tab6">
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
                            <?
                            if ($complaintsType == "ORDER" && !empty($arResult['COMPLAINTS_ROW'])) {?>
                                <!--basic-tab7-->
                                <div class="tab-pane fade" id="basic-tab7">
                                    <div class="card">
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
                                                "ALLOW_COLUMNS_RESIZE" => true,
                                                "ALLOW_HORIZONTAL_SCROLL" => true,
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
                                                "TOTAL_ROWS_COUNT" => count(is_array($arResult['COMPLAINTS_ROW']) ? $arResult['COMPLAINTS_ROW']: []),
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
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function ()
        {
            var b2bOrder = new B2bOrderDetail({
                'ajaxUrl': '<?= CUtil::JSEscape($this->__component->GetPath() . '/ajax.php');?>',
                'changePayment': '.sale-order-detail-payment-options-methods-info-change-link',
                'changePaymentWrapper': '.payment-wrapper',
                "paymentList": <?= CUtil::PhpToJSObject($paymentData);?>,
                "arParams":<?= Json::encode($arResult['PARAMS']); ?>,
                'filter':<?= Json::encode($arResult['FILTER_EXCEL']);?>,
                'qnts':<?= Json::encode($arResult['QNTS']);?>,
                "arResult":<?= CUtil::PhpToJSObject($arResult['BASKET'], false, true); ?>,
                "TemplateFolder": '<?= $templateFolder?>',
                "OrderId": "<?= $arResult["ID"] ?>",
                "Headers":<?= CUtil::PhpToJSObject($Headers, false, true); ?>,
                "HeadersSum":<?= CUtil::PhpToJSObject($HeadersSum, false, true); ?>,
                "TemplateName": 'b2bcabinet',
            });
        })

        $('.b2b_detail_order__second__tab__btn').on('click', function ()
        {
            $('.b2b_detail_order__second__tab__btn__block').toggle();
        });

        $('.b2b_detail_order__nav_ul__block a').click(function (e)
        {
            e.preventDefault();
            $(this).tab('show');
        })
        //BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?//=$javascriptParams?>//);
    </script>
    <?
}
?>
