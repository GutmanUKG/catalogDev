<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
?>

<? if ($arResult): ?>
    <div class="alert alert-info bg-white alert-styled-left alert-dismissible"
         data-toggle="collapse" data-target="#excel_import__result" aria-expanded="false">
        <?=Loc::getMessage("SUCCESS_TITLE", ["#COUNT#" => $arResult["TOTAL_COUNT"]])?>
    </div>
    <div class="collapse show" id="excel_import__result" style="">
        <div class="card">
            <table class="table">
                <tbody>
                    <?foreach ($arResult["DATA"] as $fileID => $data):?>
                        <tr class="table-active table-border-double">
                            <td colspan="3">
                                <div class="text-body font-weight-semibold ecxel-import__file-name">
                                    <?=$arResult["FILES"][$fileID]["ORIGINAL_NAME"] ?: $fileID?>
                                </div>
                                <?if($data["ERROR_LIST"]):?>
                                    <?foreach ($data["ERROR_LIST"] as $error):?>
                                        <div class="ecxel-import__error-file">
                                            <span class="badge badge-mark border-danger mr-1"></span>
                                            <span class="ecxel-import__error-text"><?=$error?></span>
                                        </div>
                                    <?endforeach;?>
                                <?endif;?>
                            </td>
                            <td class="text-right result__count-products">
                                <span class="badge badge-success badge-pill"><?=$data["PRODUCTS_ADDED"] ? count($data["PRODUCTS_ADDED"]) : 0?></span>
                                <span class="badge badge-danger badge-pill"><?=$data["PRODUCTS_NO_ADDED"] ? count($data["PRODUCTS_NO_ADDED"]) : 0?></span>
                            </td>
                        </tr>
                        <?if($data["PRODUCTS_ADDED"]):?>
                            <?foreach ($data["PRODUCTS_ADDED"] as $prodAdd):?>
                                    <tr>
                                        <td class="text-center">
                                            <i class="icon-checkmark3 text-success"></i>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <span class="text-body font-weight-semibold letter-icon-title">
                                                        <?=$arResult["PRODUCTS"][$prodAdd["ID"]]["NAME"] ?: Loc::getMessage("POSITION_NOT_FOUND")?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <a href="#" class="text-body">
                                                <div><?=$prodAdd["QUANTITY"]?>&nbsp;<?=$arResult["PRODUCTS"][$prodAdd["ID"]]["MEASURE"]?></div>
                                            </a>
                                        </td>
                                    </tr>
                            <?endforeach;?>
                        <?endif;?>
                        <?if($data["PRODUCTS_NO_ADDED"]):?>
                            <?foreach ($data["PRODUCTS_NO_ADDED"] as $prodNoAdd):?>
                                <tr>
                                    <td class="text-center">
                                        <i class="icon-cross2 text-danger"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="text-body font-weight-semibold letter-icon-title">
                                                        <?=$arResult["PRODUCTS"][$prodNoAdd["ID"]]["NAME"] ?: Loc::getMessage("POSITION_NOT_FOUND")?>
                                                    </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-body">
                                            <div class="text-muted font-size-sm ">
                                                <span class="badge badge-mark border-danger mr-1"></span>
                                                <span class="product__error-added"><?=$prodNoAdd["ERROR"]?></span>
                                            </div>
                                        </span>
                                    </td>
                                    <td>
                                        <span  class="text-body">
                                            <div><?=$prodNoAdd["QUANTITY"]?>&nbsp;<?=$arResult["PRODUCTS"][$prodNoAdd["ID"]]["MEASURE"]?></div>
                                        </span>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        <?endif;?>
                    <?endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
<? endif; ?>

