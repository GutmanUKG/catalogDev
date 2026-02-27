<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Application;

define("BX_SECURITY_AV_STARTED", false);
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.offerlist/classes/mpdf/vendor/autoload.php");

global $APPLICATION;
$APPLICATION->RestartBuffer();

$request = Application::getInstance()->getContext()->getRequest();
$siteUrl = ($request->isHttps() ? "https://" : "http://") . $request->getServer()->get("HTTP_HOST");


if ($arResult['PRICELIST']['HTML']) {
    $html = base64_decode($arResult['PRICELIST']['HTML']);
} else {
    ob_start();
?>

    <div id="offerlist__downloadeditor">
        <? if ($arResult['PRICELIST']['HTML']):
            $html = base64_decode($arResult['PRICELIST']['HTML']);
        else:?>

            <div class="editor">
                <div class="header">
                    <table>
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
                            '#DATE#' => $arResult['PRICELIST']['DATE_CREATE']->toString()
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
                                    <img src="<?= $siteUrl . $arResult["PRODUCT_INFO"][$productID]["IMG"] ?: $this->__folder . '/img/no_photo.svg' ?>">
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
                    <p class="total" style="text-align: right">
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

<?php
    $html = ob_get_contents();
    ob_end_clean();
}

if (!Encoding::detectUtf8($html)) {
    $html = Encoding::convertEncoding($html,"WINDOWS-1251", "UTF-8");
}

$title = $arResult['PRICELIST']['NAME'];
if (!Encoding::detectUtf8($title)) {
$title = Encoding::convertEncoding($title, "WINDOWS-1251", "UTF-8");
}
$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'default_font' => 'DejaVu Serif',
    'mode' => 'utf-8',
]);
$mpdf->charset_in = 'utf-8';

$mpdf->SetTitle($title);
$mpdf->WriteHTML($html);
$mpdf->Output('price-list' . $arParams["ID"] . '.pdf', 'I');
die();