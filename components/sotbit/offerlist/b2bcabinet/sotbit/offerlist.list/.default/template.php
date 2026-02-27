<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>

<div class="row">
    <? if ($arResult["OFFERS"]): ?>
        <? foreach ($arResult["OFFERS"] as $offerId => $offer): ?>
            <? if (!$offer["PRODUCTS"]) continue; ?>
            <div class="col-lg-6">
                <div id="offerlist__card_<?= $offerId ?>" class="card offerlist__card" data-offerlist-id="<?= $offerId ?>"
                    data-entity="card">
                    <div class="card-header gradient-modal text-white d-flex flex-wrap align-items-center">
                        <h5 class="card-title mb-0">
                            <?= $offer["NAME"] ?>
                        </h5>
                        <? if ($offer["DESCRIPTION"]): ?>
                            <div class="d-inline-flex ms-auto">
                                <span id="offerlist__condition-icon" 
                                      class="offerlist__condition-icon"
                                      role="button"
                                      data-bs-popup="tooltip"
                                      data-bs-placement="bottom"
                                      data-bs-original-title="<?= Loc::getMessage('SO_WARNING_TITLE') ?>">
                                        <i class="ph-warning-circle"></i>
                                </span>
                            </div>
                        <? endif; ?>
                    </div>
                    <div class="offerlist__card-wrap">
                        <table class="table table-mobile-grid">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <span class="table-header-name"><?= Loc::getMessage('SO_HEADER_PRODUCT_NAME') ?></span>
                                    </th>
                                    <th class="text-end">
                                        <span class="table-header-name"><?= Loc::getMessage('SO_HEADER_PRODUCT_QUANTITY') ?></span>
                                    </th>
                                    <th class="text-end">
                                        <span class="table-header-name"><?= Loc::getMessage('SO_HEADER_PRODUCT_PRICE') ?></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($offer["PRODUCTS"] as $product): ?>
                                    <?
                                    $productAvailable = ($product["QUANTITY"] !== null && isset($product["PRICE"]));
                                    ?>
                                    <tr class="<?= !$productAvailable ? 'offerlist__item__notavailable' : '' ?>">
                                        <td class="pe-0 w-0">
                                            <div class="table-img">
                                                <img class="rounded"
                                                     alt="<?= $product["NAME"] ?>"
                                                     src="<?= $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["IMG"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg' ?>"
                                                >
                                                <? if ($productAvailable && $product["BASE_PRICE"] && ($product["BASE_PRICE"] != $product["PRICE"])): ?>
                                                    <div class="offerlist__item_discount">
                                                        <span>-<?= ($product["BASE_PRICE"] - $product["PRICE"]) / $product["BASE_PRICE"] * 100 ?>%</span>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <? if ($product["DETAIL_PAGE_URL"]): ?>
                                                    <a class="table-name"
                                                       href="<?= $product["DETAIL_PAGE_URL"] ?>"
                                                       target="_blank"
                                                       title="<?= $product["NAME"] ?>"
                                                       >
                                                       <?= $product["NAME"] ?>
                                                    </a>
                                                <? else: ?>
                                                    <span class="table-name"
                                                          title="<?= $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["NAME"] ?>"
                                                          >
                                                          <?= $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["NAME"] ?>
                                                    </span>
                                                <? endif; ?>
                                                <? if (!empty($arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["ARTICLE"])): ?>
                                                    <span class="table-article">
                                                        <span><?= Loc::getMessage('SO_HEADER_PRODUCT_ARTICLE') ?>:</span> <?= $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["ARTICLE"] ?>
                                                    </span>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <? if (!$productAvailable): ?>
                                            <td colspan="2" class="text-start text-sm-end">
                                                <div class="text-nowrap">
                                                    <span><?= Loc::getMessage('SO_PRODUCT_NOT_AVAILABLE') ?></span>
                                                </div>
                                            </td>
                                        <? else: ?>
                                            <td class="text-start text-sm-end">
                                                <div class="text-nowrap" data-title="<?= Loc::getMessage('SO_HEADER_PRODUCT_QUANTITY') ?>">
                                                    <span><?= $product["QUANTITY"] ?></span>
                                                </div>
                                            </td>
                                            <td class="text-start text-sm-end">
                                                <div class="text-nowrap" data-title="<?= Loc::getMessage('SO_HEADER_PRODUCT_PRICE') ?>">
                                                    <span><?= CurrencyFormat($product["PRICE"], $product['CURRENCY']) ?></span>
                                                </div>
                                            </td>
                                        <? endif; ?>
                                    </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <footer class="offerlist__footer">
                        <div class="offerlist-footer__top">
                            <div class="offerlist__counter">
                                <span><?= Loc::getMessage('SO_FOOTER_COUNT', ["#COUNT#" => is_array($offer["PRODUCTS"]) ? count($offer["PRODUCTS"]) : 0]) ?></span>
                            </div>
                            <div class="offerlist__total-price">
                                <span class="offerlist__footer__total-price-key"><?= Loc::getMessage('SO_FOOTER_ITOG') ?></span>
                                <span class="offerlist__footer__total-price-value fw-semibold"><?= CurrencyFormat($offer['ORDER']['PRICE'], $offer['ORDER']['CURRENCY']) ?></span>
                            </div>
                        </div>
                        <div class="offerlist__footer__action-btn d-flex gap-3 flex-column flex-sm-row" data-entity="page-basket-action-button">
                            <button id="offerlist__actionbtn-addbasket" type="button" class="btn btn-primary">
                                <i class="ph-shopping-cart-simple me-2"></i>
                                <?= Loc::getMessage('SO_FOOTER_BTN_ACTION_ATB') ?>
                            </button>
                            <button id="offerlist__actionbtn-download" type="button" class="btn">
                                <?= Loc::getMessage('SO_FOOTER_BTN_ACTION_DOWNLOAD') ?>
                            </button>
                        </div>
                    </footer>
                </div>
            </div>
        <? endforeach; ?>
    <? else: ?>
        <div class="alert alert-info alert-styled-left alert-dismissible w-100">
            <?= Loc::getMessage('SO_NO_OFFERS') ?>
        </div>
    <? endif; ?>
</div>


<div id="offerlist__condition-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white ">
                <h5 class="modal-title"><?= Loc::getMessage('SO_MODAL_CONDITION') ?></h5>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="btn" data-bs-toggle="modal" data-bs-target="#offerlist__condition-modal">


<script>
    BX.ready(function () {
        BX.message({
            'SO_ACTION_NOTAVAILABLE_1': '<?=Loc::getMessage('SO_ACTION_NOTAVAILABLE_1');?>',
            'SO_ACTION_NOTAVAILABLE_2': '<?=Loc::getMessage('SO_ACTION_NOTAVAILABLE_2');?>',
        });
        var sotbitOffersList = <?=CUtil::PhpToJSObject($arResult["OFFERS"])?>;
        if (sotbitOffersList) {
            for (let i in sotbitOffersList) {
                new SotbitOfferlist(
                    sotbitOffersList[i],
                    {
                        'condition': '#offerlist__condition-icon',
                        'addBasket': '#offerlist__actionbtn-addbasket',
                        'download': '#offerlist__actionbtn-download',
                        'actionbtn': '#offerlist__actionbtn',
                        'actionContainer': '.offerlist__footer__action-btn',
                        'conditionTextModal': '#offerlist__condition-modal',
                        'downloadModal': '#offerlist__download-modal__' + sotbitOffersList[i].ID,
                        'downloadEditorInput': '#offerlist__downloadeditor__' + sotbitOffersList[i].ID,
                    },
                    {
                        'PATH_TO_EDITOR': '<?=$arParams["URL_TEMPLATES"]["editor"]?>'
                    }
                );
            }
        }
    });
</script>
