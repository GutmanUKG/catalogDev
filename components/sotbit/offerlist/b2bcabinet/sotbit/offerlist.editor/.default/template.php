<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

$this->addExternalJS('/bitrix/js/sotbit.offerlist/editors/ckeditor/ckeditor.js');

if (!$arResult["OFFER"]) {
    return;
}
$request = Application::getInstance()->getContext()->getRequest();
$APPLICATION->SetTitle(Loc::getMessage('SOTBIT_OFFERLIST_EDITOR_TITLE'));
$siteUrl = ($request->isHttps() ? "https://" : "http://") . $request->getServer()->get("HTTP_HOST");

if ($request["IFRAME"] !== 'Y' && $request["IFRAME_TYPE"] !== 'SIDE_SLIDER') {
    $APPLICATION->AddChainItem(Loc::getMessage('SOTBIT_OFFERLIST_EDITOR_TEMPLATE_NAME', ["#OFFER_ID#" => $arResult["OFFER"]["ID"], "#DATE_FROM#" => $arResult["OFFER"]["ACTIVE_FROM"]]));
}
?>
<form action="<?=$arParams["URL_TEMPLATES"]["print"]?>" method="post" id="offerlist__editor-form">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="OFFER_NAME" value="<?= $arResult["OFFER"]["NAME"] ?>">
    <input type="hidden" name="OFFER_ID" value="<?= $arResult["OFFER"]["ID"] ?>">
    <div class="mb-3 offerlist__editor_wraper" style="overflow-y: auto">
        <!-- Offerlist document -->
        <div name="<?= $arParams["EDITOR_INPUT"] ?>" id="offerlist__downloadeditor">
            <div class="editor">
                <div class="header">
                    <table>
                        <tr>
                            <td style="border: none">
                                <div class="header__img">
                                    <img src="<?=$siteUrl . $this->__folder . '/lib/img/logo.png' ?>" alt="" width="150">
                                </div>
                            </td>
                            <td style="text-align: right; border: none">
                                <div class="header__sidebar">
                                    <?= Loc::getMessage('SO_PRICELIST_DETAIL_HEADER') ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <div class="body">

                    <h1>
                        <?= Loc::getMessage('SOTBIT_OFFERLIST_EDITOR_TEMPLATE_NAME', ["#OFFER_ID#" => $arResult["OFFER"]["ID"], "#DATE_FROM#" => $arResult["OFFER"]["ACTIVE_FROM"]]) ?>
                    </h1>

                    <div class="offerlist__editor_producttable">
                        <table style="text-align: center;table-layout: fixed; width: 100%; border-collapse: collapse; border: 1px solid rgba(0,0,0,.125); margin:  25px 0 25px 0;">
                            <thead>
                            <tr style="height: 50px; border: 1px solid rgba(0,0,0,.125);">
                                <td width="70px"></td>
                                <td style="border: 1px solid rgba(0,0,0,.125);"><?= Loc::getMessage('SO_HEADER_PRODUCT_NAME') ?></td>
                                <td style="border: 1px solid rgba(0,0,0,.125);"><?= Loc::getMessage('SO_HEADER_PRODUCT_ARTICLE') ?></td>
                                <td style="border: 1px solid rgba(0,0,0,.125);"><?= Loc::getMessage('SO_HEADER_PRODUCT_QUANTITY') ?></td>
                                <td style="border: 1px solid rgba(0,0,0,.125);"><?= Loc::getMessage('SO_HEADER_PRODUCT_PRICE') ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($arResult['PRODUCTS'] as $product): ?>
                                <tr style="height: 50px; border: 1px solid rgba(0,0,0,.125);">
                                    <td width="70px">
                                        <img
                                                src="<?= $siteUrl . ($arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["IMG"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'); ?>"
                                                alt="<?= $product["NAME"] ?: $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["NAME"] ?>"  width="50" heigth="50"
                                        >
                                    </td>
                                    <td style="border: 1px solid rgba(0,0,0,.125);">
                                        <?= $product["NAME"] ?: $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["NAME"] ?>
                                    </td>
                                    <td style="border: 1px solid rgba(0,0,0,.125);">
                                        <?= $arResult["ALL_PRODUCTS"][$product['PRODUCT_ID']]["ARTICLE"] ?>
                                    </td>
                                    <td style="border: 1px solid rgba(0,0,0,.125);">
                                        <?= $product["QUANTITY"] ?>
                                    </td>
                                    <td style="border: 1px solid rgba(0,0,0,.125);">
                                        <?= CurrencyFormat($product["PRICE"], $product['CURRENCY']) ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <p class="total">
                        <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_TOTAL') ?>
                        <?= CurrencyFormat($arResult['ORDER']['PRICE'],
                            $arResult['ORDER']['CURRENCY']);?>
                    </p>
                    <div class="total-row">
                        <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_TOTAL_ROW', [
                            '#COUNT#' => count($arResult['PRODUCTS']),
                            '#SUM#' => Number2Word_Rus($arResult['ORDER']['PRICE']),
                        ]) ?>
                    </div>
                </div>
                <hr>
                <div class="footer">
                    <div class="footer-info-manager">
                        <p><?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_FOOTER_1') ?></p>
                        <p><?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_FOOTER_2') ?></p>
                    </div>
                </div>
            </div>
            <style>
                .editor {
                    max-width: 885px;
                    margin-left: auto;
                    margin-right: auto;
                }

                .header {
                    margin-bottom: 30px;
                }

                .header table {
                    width: 100%;
                    border: 1px solid white !important;
                }

                .header table td {
                    border: none !important;
                }

                .header__img {
                    margin-top: auto;
                    margin-bottom: auto;
                }

                .header__sidebar p {
                    margin: 5px 0 5px 0;
                    font-size: 15px;
                }

                .body {
                    padding: 25px 0 25px 0;
                }

                .total {
                    text-align: right;
                    font-size: 16px;
                    line-height: 19px;
                    font-weight: bold;
                }

                .product-table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-top: 25px;
                }

                .product-table tr {
                    height: 60px;
                }

                .product-table tr td {
                    border: 1px solid rgba(0, 0, 0, .125) !important;
                    text-align: center;
                }

                .product-table td.product_name {
                    text-align: left;
                    padding-left: 10px;
                }

                .product-table td.product_price {
                    min-width: 50px;
                    text-align: right;
                    padding-right: 5px;
                }

                .total-row {
                    margin-top: 60px;
                    font-size: 16px;
                }

                .footer-info-manager {
                    text-align: right;
                }

                .footer-info-manager p {
                    margin: 5px 0 5px 0;
                    font-size: 15px;
                }

                .info-text {
                    font-size: 15px;
                }

                .footer {
                    margin-top: 30px;
                }
            </style>
        </div>
        <!-- /offerlist document -->
    </div>
    <button type="submit" class="btn btn-primary float-end"><i class="ph-arrow-line-down me-2"></i><?=Loc::getMessage('SO_BTN_ACTION_EDITOR_SAVE')?></button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editorSOB2bcabinet = new EditorSotbitOferlist({
            formselector: '#offerlist__editor-form',
            editorInput: 'offerlist__downloadeditor',
            printPageAction: '<?=$arResult['OFFER']['PRINT_PAGE_URL']?>',
            offerId: '<?= $arResult["OFFER"]["ID"] ?>',
        });
    });
</script>