<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);
Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');
$idUser = intval($USER->GetID());
if(Loader::includeModule('sotbit.b2bcabinet') && Loader::includeModule('sale') && $idUser > 0) {
    $limit = 2;
    if($arParams['GU_ORDERS_LIMIT'] > 0) {
        $limit = $arParams['GU_ORDERS_LIMIT'];
    }
    if($arGadgetParams['LIMIT'] > 0) {
        $limit = $arGadgetParams['LIMIT'];
    }
    $listOrders = new \Sotbit\B2BCabinet\Shop\OrderCollection();
    $listOrders->setLimit($limit);
    if(defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES == "Y"){
        $company = new Sotbit\Auth\Company\Company(SITE_ID);
        $filter["ID"] = $company->getCompanyOrders();
        $filter["LID"] = SITE_ID;
    }
    else{
        $filter = ["USER_ID" => $idUser, "LID" => SITE_ID];
    }
    if($arParams['STATUS'] && $arParams['STATUS'] != 'ALL') {
        $filter['STATUS_ID'] = $arParams['STATUS'];
    }
    if($arGadgetParams['STATUS'] && $arGadgetParams['STATUS'] != 'ALL') {
        $filter['STATUS_ID'] = $arGadgetParams['STATUS'];
    }
    $orders = $listOrders->getOrders($filter);
    ?>
    <div class="idget_content_list">
    <?foreach($orders as $order) {
        ?>
        <div class="widget_content widget_links orders">
            <div class="widget_order_content">
                <div class="widget_order_header">
                    <a href="<?=$order->getUrl($arParams['G_ORDERS_PATH_TO_ORDER_DETAIL'])?>" title="<?=Loc::getMessage('GD_SOTBIT_CABINET_ORDER_ORDER')?> <?=$order->getId()?>">
                        <h6><?=Loc::getMessage('GD_SOTBIT_CABINET_ORDER_ORDER')?> <?=$order->getDisplayId()?></h6> <i class="icon-arrow-right13 mr-2"></i>
                    </a>
                </div>
                <div class="widget_order_information">
                    <span><?=Loc::getMessage('GD_SOTBIT_CABINET_ORDER_FROM')?> <?=$order->getDate()->format("d.m.Y")?></span>
                    <span><span class="text-muted"><?=Loc::getMessage('GD_SOTBIT_CABINET_ORDER_SUM')?>:</span>  <?=$order->getPrice()?></span>
                </div>
            </div>
            <span class="text-muted">
                <?= reset($order->getStatus()) ?>
            </span>
        </div>
        <?
    }?>
    </div>
    <?
    if($arParams['G_ORDERS_PATH_TO_ORDERS']) {
        ?>
        <div class="b2b-gadget b2b-gadget-orders">
            <div class="b2b-gadget-profile__link">
                <a href="<?php echo $arParams['G_ORDERS_PATH_TO_ORDERS']; ?>" class="main-link b2b-main-link">
                    <?=Loc::getMessage('GD_SOTBIT_CABINET_ORDER_HISTORY')?>
                </a>
            </div>
        </div>
        <?php
    }
}
?>
