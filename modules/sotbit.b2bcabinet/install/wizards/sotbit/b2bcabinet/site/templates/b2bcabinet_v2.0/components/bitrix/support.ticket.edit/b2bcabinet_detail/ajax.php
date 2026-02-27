<? 
define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if ($_REQUEST['ajax'] === 'Y'): ?>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH.'/assets/js/plugins/editor/trumbowyg/trumbowyg.min.js'?>"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH.'/assets/js/plugins/editor/trumbowyg/lang/ru.min.js'?>"></script>
    <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH.'/assets/js/pages/editor_trumbowyg.js'?>"></script>
    <script type="text/javascript" src="<?=$templateFolder.'/script.js?v=1'?>"></script>
    <form name="support_edit" method="post" action="<?=$arResult["REAL_FILE_PATH"]?>" enctype="multipart/form-data">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="set_default" value="Y">
<? endif; ?>

    <input type="hidden" name="ID" value="<?=(empty($arResult["TICKET"]["ID"]) ? 0 : $arResult["TICKET"]["ID"])?>">
    <input type="hidden" name="lang" value="<?=LANG?>">
    <input type="hidden" name="edit" value="1">
    
    <? if (!empty($arParams["ORDER_ID"])): ?>
        <input type="hidden" name="UF_ORDER" value="<?=$arParams['ORDER_ID']?>">
    <? elseif(!empty($arParams["COMPLAINT_ID"])): ?>
        <input type="hidden" name="COMPLAINT_ID" value="<?=$arParams['COMPLAINT_ID']?>">
    <? endif; ?>

    <div class="form-group">
        <?
            $titleValue = '';

            if (!empty($arResult['TICKET']['TITLE'])) {
                $titleValue = $arResult['TICKET']['TITLE'];
            } else if (!empty($arParams["ORDER_ID"])) {
                $titleValue = Loc::getMessage('SUP_ORDER', ['#ORDER_ID#' => htmlspecialcharsbx($arParams["ORDER_ID"])]);
            } else if (!empty($arParams['TITLE_SUPPORT'])) {
                $titleValue = $arParams['TITLE_SUPPORT'];
            }
        ?>
        <label class="form-label">
            <?=Loc::getMessage('SUP_TITLE')?> <span class="req">*</span>
        </label>
        <input
            name="TITLE"
            id="TITLE"
            value="<?=$titleValue?>"
            class="form-control mb-3"
            required
        >
    </div>
    <div class="form-group">
        <label class="form-label">
            <?=Loc::getMessage('SUP_CATEGORY')?>
        </label>
        <select name="CATEGORY_ID"
                id="CATEGORY_ID"
                data-placeholder="<?=Loc::getMessage('SUP_CHOOSE_OPTION')?>"
            <?=( isset($arResult['TICKET']['CATEGORY_ID']) && !empty($arResult['TICKET']['CATEGORY_ID']) && $ticketExist ? 'disabled' : '' )?>
                class="form-control select"
                data-minimum-results-for-search="Infinity"
        >
            <option value=""></option>
            <?foreach ($arResult["DICTIONARY"]["CATEGORY"] as $value => $category):?>
                <option value="<?=$value?>" <?= ($value == $arResult['TICKET']['CATEGORY_ID']) ? 'selected="selected"' : '' ?>>
                    <?=$category?>
                </option>
            <?endforeach?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">
            <?=Loc::getMessage('SUP_MESSAGE')?> <span class="req">*</span>
        </label>
        <textarea name="MESSAGE" id="MESSAGE" rows="5" cols="5" class="form-control trumbowyg" required></textarea>
    </div>
    <div class="form-group form-group-answe">
        <label class="form-label">
            <?=Loc::getMessage('SUP_ATTACH')?>
        </label>
        <div class="add_more_files">
            <div class="media-body">
                <div class="upload-file">
                    <img id="files_preview_0">
                    <input type="file" name="FILE_0" size="30" class="input-file" data-fouc onchange="App.showPreviewPicture(0)">
                    <span class="filename"><?=Loc::getMessage('SUP_CHOOSE_NO')?></span>
                </div>
            </div>
            <label class="btn-add-more-files" title="<?=Loc::getMessage('SUP_MORE')?>" OnClick="App.addFile()">
                <i class="ph-plus"></i>
            </label>
        </div>

        <input type="hidden" name="files_counter" id="files_counter" value="1" />
        <input type="hidden"
                name="MAX_FILE_SIZE"
                value="<?= ($arResult["OPTIONS"]["MAX_FILESIZE"] * 1024) ?>"
        >
    </div>
    <div class="form-group">
        <label class="form-label">
            <?=Loc::getMessage('SUP_CRITICALITY')?>
        </label>
        <?if (empty($arResult["TICKET"]) || strlen($arResult["ERROR_MESSAGE"]) > 0 )
        {
            if (strlen($arResult["DICTIONARY"]["CRITICALITY_DEFAULT"]) > 0 && strlen($arResult["ERROR_MESSAGE"]) <= 0)
                $criticality = $arResult["DICTIONARY"]["CRITICALITY_DEFAULT"];
            else
                $criticality = htmlspecialcharsbx($_REQUEST["CRITICALITY_ID"]);
        }
        else
            $criticality = $arResult["TICKET"]["CRITICALITY_ID"];
        ?>
        <select data-placeholder="<?=Loc::getMessage('SUP_CHOOSE_OPTION')?>"
                name="CRITICALITY_ID"
                id="CRITICALITY_ID"
                class="form-control select"
                data-minimum-results-for-search="Infinity"
        >
            <option value=""></option>
            <?foreach ($arResult["DICTIONARY"]["CRITICALITY"] as $value => $option):?>
                <option value="<?=$value?>" <?if($criticality == $value):?>selected="selected"<?endif?>><?=$option?></option>
            <?endforeach?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">
            <?=Loc::getMessage('SUP_RATE_ANSWER')?>
        </label>
        <?
        $mark = (strlen($arResult["ERROR_MESSAGE"]) > 0 ?
            htmlspecialcharsbx($_REQUEST["MARK_ID"]) :
            $arResult["TICKET"]["MARK_ID"]);
        ?>
        <select name="MARK_ID"
                id="MARK_ID"
                data-placeholder="<?=Loc::getMessage('SUP_CHOOSE_OPTION')?>"
                class="form-control select"
                data-minimum-results-for-search="Infinity"
        >
            <option value=""></option>
            <?foreach ($arResult["DICTIONARY"]["MARK"] as $value => $option):?>
                <option value="<?=$value?>" <?if($mark == $value):?>selected="selected"<?endif?>><?=$option?></option>
            <?endforeach?>
        </select>
    </div>
    <div class="d-flex justify-content-end gap-4">
        <input type="submit" class="btn apply_support_message" name="apply" value="<?=Loc::getMessage('SUP_APPLY')?>">
        <input type="submit" class="btn btn-primary" name="save" value="<?=Loc::getMessage('SUP_SAVE')?>">
    </div>

<? if ($_REQUEST['ajax'] === 'Y'): ?>
    </form>
<script>
    BX.message({
        SITE_TEMPLATE_PATH: '<?= SITE_TEMPLATE_PATH ?>',
        FILE_NOT_SELECTED_TEXT: '<?=Loc::getMessage("SUP_CHOOSE_NO")?>'
    });

    const inputs = document.querySelectorAll('.input-file');
    Array.prototype.forEach.call(inputs, function (input)
    {
        App.initFile(input);
    });
    Trumbowyg.init();
    App.initSelect2();
</script>
<? endif; ?>