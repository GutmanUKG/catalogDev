<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;

$this->addExternalJS('/bitrix/js/sotbit.offerlist/editors/ckeditor/ckeditor.js');

$APPLICATION->setTitle(Loc::getMessage('SO_PRICELIST_DETAIL_TITLE', ['#ID#' => $arParams['ID']]));
$APPLICATION->AddChainItem(Loc::getMessage('SO_PRICELIST_DETAIL_TITLE', ['#ID#' => $arParams['ID']]));
$request = Application::getInstance()->getContext()->getRequest();
$siteUrl = ($request->isHttps() ? "https://" : "http://") . $request->getServer()->get("HTTP_HOST");
?>

<div class="card">
    <div class="card-body offer-pricelist_fields">
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <label class="col-lg-3 col-form-label"><?= Loc::getMessage('SO_PRICELIST_DETAIL_FIELD_NAME') ?></label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control" name="NAME"
                               value="<?= $arResult['PRICELIST']['NAME'] ?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-end align-items-center h-100">
                    <a href="javascript: void(0)" class="breadcrumb-elements-item me-3" id="action-save">
                        <i class="ph-floppy-disk me-1"></i>
                        <?= Loc::getMessage('SO_PRICELIST_DETAIL_ACTION_SAVE') ?>
                    </a>
                    <a href="<?=preg_replace('/\#\S+\#/', $arParams["ID"], $arParams["PATH_TO_PRINT"])?>" class="breadcrumb-elements-item" id="action-print">
                        <i class="ph-printer me-1"></i>
                        <?= Loc::getMessage('SO_PRICELIST_DETAIL_ACTION_PRINT') ?>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="offerlist__downloadeditor">
            <? if ($arResult['PRICELIST']['HTML']):
                $html = base64_decode($arResult['PRICELIST']['HTML']);
                if (SITE_CHARSET === 'windows-1251') {
                    $html = Encoding::convertEncoding($html, "UTF-8", "WINDOWS-1251");
                }
                echo $html;
            else:?>
                <div class="editor">
                    <div class="header">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <div class="header__img">
                                        <img src="<?=$siteUrl . $this->__folder . '/img/logo.png' ?>" alt="" width="150">
                                    </div>
                                </td>
                                <td style="text-align: right">
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
                            <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_TITLE', [
                                '#ID#' => $arResult['PRICELIST']['ID'],
                                '#DATE#' => $arResult['PRICELIST']['DATE_CREATE'] ? $arResult['PRICELIST']['DATE_CREATE']->toString() : ''
                            ]) ?>
                        </h1>
                        <p class="info-text">
                            <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_INFO') ?>
                        </p>
                        <table class="product-table">
                            <thead>
                            <tr>
                                <td colspan="2"><?=Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_PRODUCT_NAME')?></td>
                                <td><?=Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_PRODUCT_ARTICLE')?></td>
                                <td><?=Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_PRODUCT_QNT')?></td>
                                <td><?=Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_PRODUCT_PRICE')?></td>
                                <td><?=Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_PRODUCT_TOTAL')?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach (array_keys($arResult['PRICELIST']['PRODUCT_SET']['PRICE']) as $productID): ?>
                                <tr>
                                    <td style="width: 50px; padding: 5px">
                                        <img class="rounded" src="<?=$arResult["PRODUCT_INFO"][$productID]["IMG"] ?: SITE_TEMPLATE_PATH  . '/assets/images/no_photo.svg' ?>">
                                    </td>
                                    <td class="product_name"><?= $arResult["PRODUCT_INFO"][$productID]["NAME"] ?></td>
                                    <td><?= $arResult["PRODUCT_INFO"][$productID]['ARTICLE'] ?></td>
                                    <td><?= $arResult['PRICELIST']['PRODUCT_SET']['QUANTITY'][$productID] ?></td>
                                    <td class="product_price"><?= $arResult['PRICELIST']['PRODUCT_SET']['PRICE'][$productID] ?></td>
                                    <td class="product_price"><?= number_format($arResult["PRODUCT_INFO"][$productID]['TOTAL_PRICE'],
                                            2, '.', ' ') ?></td>
                                </tr>
                            <? endforeach; ?>
                            </tbody>
                        </table>
                        <p class="total">
                            <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_TOTAL') ?>
                            <?= $arResult['PRICELIST']['PRODUCT_SET']['ORDER']['CURRENCY'] === 'RUB' ? number_format($arResult['PRODUCT_TOTAL_SUM'],
                                    2, '.',
                                    ' ') . Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_RUB') : CurrencyFormat($arResult['PRODUCT_TOTAL_SUM'],
                                $arResult['PRICELIST']['PRODUCT_SET']['ORDER']['CURRENCY']); ?>
                        </p>
                        <div class="total-row">
                            <?= Loc::getMessage('SO_PRICELIST_DETAIL_EDITOR_TOTAL_ROW', [
                                '#COUNT#' => count($arResult['PRICELIST']['PRODUCT_SET']['PRICE']),
                                '#SUM#' => Number2Word_Rus($arResult["PRODUCT_TOTAL_SUM"]),
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

                    .header table td{
                        border: 1px solid white !important;
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
            <? endif; ?>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        BX.message({
            'SUCCESS_TITLE': '<?=Loc::getMessage('SO_PRICELIST_ACTION_SAVE_SUCCESS_TITLE')?>'
        });

        const detailPriceList = new OfferlistPriceListDetail({
            editorInput: 'offerlist__downloadeditor',
            id: '<?=$arParams["ID"] ?: $arResult['PRICELIST']['ID']?>',
            actions: {
                'save': 'action-save',
                'print': 'action-print'
            }
        });
    });
</script>