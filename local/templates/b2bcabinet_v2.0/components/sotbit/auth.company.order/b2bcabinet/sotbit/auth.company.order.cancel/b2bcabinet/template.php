<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if(!Loader::includeModule('sotbit.b2bcabinet'))
{
	return false;
}
?>
<div class="index_order_cancel">
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0 fw-bold">
                <?=Loc::getMessage('SALE_CANCEL_ORDER_TITLE')?>
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="mb-3">
                <span><?=GetMessage("SALE_CANCEL_ORDER1") ?></span>
                <a href="<?=$arResult["URL_TO_DETAIL"]?>">
                    <?=GetMessage("SALE_CANCEL_ORDER2")?><?=$arResult["ACCOUNT_NUMBER"]?>
                </a>
                <span> ? <b>
                        <?= GetMessage("SALE_CANCEL_ORDER3") ?>
                    </b>
                </span>
            </div>
            <?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
                <form method="post" action="<?=POST_FORM_ACTION_URI?>">
                    <input type="hidden" name="CANCEL" value="Y">
                    <?=bitrix_sessid_post()?>
                    <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
                    <div class="form-group row">
                        <label class="col-lg-3 form-label"><?=GetMessage("SALE_CANCEL_ORDER4")?>:</label>
                        <div class="col-lg-9">
                            <textarea rows="5" cols="5" class="form-control" name="REASON_CANCELED" maxlength="250"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="action" value="<?=GetMessage("SALE_CANCEL_ORDER_BTN")?>"><?=GetMessage("SALE_CANCEL_ORDER_BTN")?></button>
                </form>
            <?else:?>
                <?=ShowError($arResult["ERROR_MESSAGE"]);?>
            <?endif;?>
        </div>
    </div>
</div>