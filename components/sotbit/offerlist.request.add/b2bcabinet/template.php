<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
?>

<div id="modal-offerslist__request_add" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h5 class="modal-title">
                  <?=Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_MODAL_TITLE')?>
                </h5>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" id="offerslist__request_add" name="offerslist__request_add" method="post">
                <input type="hidden"  data-bs-target="#modal-offerslist__request_add" data-bs-toggle="modal">
                <div class="modal-body">
                    <? if ($arResult['FIELDS']) {
                        foreach ($arResult["FIELDS"] as $arField):
                            if (!$arField["NAME"]) {
                                continue;
                            }
                            ?>
                            <div class="form-group">
                                <label class="form-label">
                                    <?= $arField["NAME"] ?><span class="req">*</span>
                                </label>
                                <input type="text" class="form-control" name="<?= $arField["CODE"] ?>" placeholder="<?=Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_PLACEHOLDER', ["#FIELD#" => strtolower($arField["NAME"])])?>" required>
                            </div>
                        <?endforeach;
                    } ?>
                    <div class="form-group">
                        <label class="form-label">
                            <?= Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_COMMENT_FIELD') ?>
                        </label>
                        <textarea name="COMMENT" class="form-control" rows="2" placeholder="<?=Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_PLACEHOLDER_COMMENT')?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <?=Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_BTN_CANCEL')?>
                    </button>
                    <button type="submit" name="register_submit_button" class="btn btn-primary">
                        <?=Loc::getMessage('SOTBIT_OFFERLIST_REQUEST_BTN_SUBMIT')?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        new SORequestAdd({
            'btnGetReault': '<?=$arParams["BTN_GET_RESULT"]?>'
        })
    });
</script>