<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
CJSCore::Init(['currency']);
?>


<? if ($arResult["ORDER"] && !empty($arResult["ORDER"]["BASKET_ITEMS"])): ?>
    <input type="hidden" data-bs-toggle="modal" data-bs-target="#offerlist__price_list_add">
    <div id="offerlist__price_list_add" class="modal fade">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title"><?=Loc::getMessage('SO_PRICELIST_TITLE')?></h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>
                <form id="offerlist__price_list_products" action="" method="post">
                    <input type="hidden" name="PRICELIST[ORDER][CURRENCY]" value="<?=$arResult["ORDER"]["ORDER_FIELDS"]["CURRENCY"]?>">
                    <div class="modal-body">
                        <div class="offerlist_pricelist__productstable">
                            <div class="table-responsive table-rounded table-border-wrapper">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th colspan="2" class="productstable__col-name w-100"><?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_NAME')?></th>
                                        <th class="productstable__col-price text-end"><?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_PRICE')?></th>
                                        <th class="productstable__col-margin text-end"><?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_MARGIN')?></th>
                                        <th class="productstable__col-quantity text-end"><?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_QUANTITY')?></th>
                                        <th class="productstable__col-total text-end"><?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_TOTAL')?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <? foreach ($arResult["ORDER"]["BASKET_ITEMS"] as $item): ?>
                                            <tr data-entity="product" data-id="<?=$item['PRODUCT_ID']?>">
                                                <td class="pe-0 productstable__col-img">
                                                    <div class="product_img_block rounded">
                                                        <img class="rounded" src="<?= $arResult["BASKET_ITEMS_FIELDS"][$item['PRODUCT_ID']]["IMG"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg' ?>">
                                                    </div>
                                                </td>
                                                <td class="productstable__col-name">
                                                    <span class="fw-semibold">
                                                        <?= $item["NAME"] ?>
                                                    </span>
                                                    <?if($arResult["BASKET_ITEMS_FIELDS"][$item['PRODUCT_ID']]["ARTICLE"]):?>
                                                        <div class="fs-xs">
                                                            <?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_ARTICLE', ['#ARTICLE#' => $arResult["BASKET_ITEMS_FIELDS"][$item['PRODUCT_ID']]["ARTICLE"]])?>
                                                        </div>
                                                    <?endif;?>
                                                </td>
                                                <td class="productstable__col" data-title="<?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_PRICE')?>">
                                                    <div class="input-group flex-nowrap">
                                                        <input type="text"
                                                            class="form-control"
                                                            name="PRICELIST[PRICE][<?=$item['PRODUCT_ID']?>]"
                                                            data-entity="price"
                                                            value="<?= strpos($item["PRICE"],'.')!==false ? rtrim(rtrim($item["PRICE"],'0'),'.') : $item["PRICE"] ?>">
                                                        <span class="input-group-text"><?= $item["CURRENCY_FORMAT"] ?></span>
                                                    </div>
                                                </td>
                                                <td class="productstable__col" data-title="<?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_MARGIN')?>">
                                                    <div class="input-group flex-nowrap">
                                                        <button class="btn btn-icon"
                                                                data-entity="toggle-margin"
                                                                data-number-sign="plus"
                                                                type="button"
                                                                >
                                                                <i class="ph-plus"></i>
                                                        </button>
                                                        <input type="number"
                                                               step="0.01"
                                                               class="form-control"
                                                               data-entity="margin"
                                                               name="PRICELIST[MARGIN][<?=$item['PRODUCT_ID']?>]"
                                                               value="0">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td class="productstable__col" data-title="<?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_QUANTITY')?>">
                                                    <div class="input-group flex-nowrap">
                                                        <input type="number"
                                                               min="0.1"
                                                               step="any"
                                                               class="form-control"
                                                               data-entity="quantity"
                                                               name="PRICELIST[QUANTITY][<?=$item['PRODUCT_ID']?>]"
                                                               value="<?= $item["QUANTITY"] ?>">
                                                        <span class="input-group-text"><?= $item["MEASURE_NAME"] ?></span>
                                                    </div>
                                                </td>
                                                <td class="productstable__col productstable__col-total text-end position-relative" data-title="<?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_TOTAL')?>">
                                                    <div class="input-group w-lg-100">
                                                        <span class="fw-semibold text-nowrap" data-entity="total">
                                                            <?= CurrencyFormat($item["TOTAL"], $item["CURRENCY"])?>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <? endforeach; ?>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                                                            <label class="text-center">
                                                                <span class="d-block text-nowrap">
                                                                    <?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_MARGIN')?>
                                                                </span>
                                                                <span class="fs-xs">
                                                                    <?=Loc::getMessage('SO_PRICELIST_SUBTITLE_PRODUCT_MARGIN')?>
                                                                </span>
                                                            </label>
                                                            <div class="input-group flex-nowrap">
                                                                <button class="btn btn-icon"
                                                                        data-entity="toggle-margin-all"
                                                                        data-number-sign="plus"
                                                                        type="button"
                                                                        >
                                                                        <i class="ph-plus"></i>
                                                                </button>
                                                                <input type="number"
                                                                        step="0.01"
                                                                        class="form-control"
                                                                        data-prev-value="0"
                                                                        data-entity="margin-all"
                                                                        name="PRICELIST[MARGIN_ALL]"
                                                                        value="0">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-end">
                                                            <div class="total-qnt">
                                                                <span>
                                                                    <?=Loc::getMessage('SO_PRICELIST_TITLE_PRODUCT_COUNT', ['#COUNT#' => count($arResult["ORDER"]["BASKET_ITEMS"])])?>
                                                                </span>
                                                            </div>
                                                            <div class="total-sum text-nowrap">
                                                                <span><?=Loc::getMessage('SO_PRICELIST_TITLE_TOTAL')?></span>
                                                                <span class="fw-semibold" data-entity="total-sum"><?=CurrencyFormat($arResult["ORDER"]["ORDER_FIELDS"]["PRICE"], $arResult["ORDER"]["ORDER_FIELDS"]["CURRENCY"])?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn order-1 order-md-0" data-bs-dismiss="modal" type="reset">
                            <?=Loc::getMessage('SO_PRICELIST_BTN_CANCEL')?>
                        </button>
                        <input type="submit" class="btn btn-primary" name="web_form_submit" value="<?=Loc::getMessage('SO_PRICELIST_BTN_SUBMIT')?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<? endif; ?>

<?
$signer = new Bitrix\Main\Security\Sign\Signer();
$arResult['JS_PARAMS'] = [
    'AJAX_CALL' => $arParams['AJAX_CALL'] === 'Y',
    'BUTTON_GET_COMPONENT' => $arParams['BTN_GET_RESULT'],
    'RESULT_BLOCK' => $arParams['RESULT_BLOCK'],
    'signedParameters' => $this->__component->getSignedParameters(),
    'templateSigns' => $signer->sign(SITE_TEMPLATE_ID, "template_preview".bitrix_sessid()),
    'MODAL' => 'offerlist__price_list_add',
    'FORM_ADD' => 'offerlist__price_list_products',
];

if ($arResult["ORDER"]) {
    $arResult['JS_PARAMS']['ORDER'] = $arResult['ORDER'];
}
?>

<script>
    BX.ready(() => {
        new OfferlistPriceListAdd(
            <?=CUtil::PhpToJSObject($arResult['JS_PARAMS'])?>
        );
    });
</script>



