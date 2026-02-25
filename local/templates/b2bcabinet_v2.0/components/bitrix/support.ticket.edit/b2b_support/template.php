<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/assets/js/plugins/editor/trumbowyg/trumbowyg.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/assets/js/plugins/editor/trumbowyg/lang/ru.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/assets/js/pages/editor_trumbowyg.js");

$ticketExist = ( !empty($arResult["TICKET"]['ID']) && !empty($arResult['TICKET']['TITLE']) );
?>
<form name="support_edit" method="post" action="<?=$arResult["REAL_FILE_PATH"]?>" enctype="multipart/form-data">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="set_default" value="Y">
    <input type="hidden" name="ID" value="<?=(empty($arResult["TICKET"]["ID"]) ? 0 : $arResult["TICKET"]["ID"])?>">
    <input type="hidden" name="lang" value="<?=LANG?>">
    <input type="hidden" name="edit" value="1">
    <?
    if($ticketExist) {
        ?>
        <div class="index_appeals-answer-form mb-2">
            <?= $arResult["NAV_STRING"] ?>

            <?foreach ($arResult["MESSAGES"] as $key => $message):
                $arUserGroups = CUser::GetUserGroup($message["OWNER_USER_ID"]);
                $rsGroups = Bitrix\Main\GroupTable::GetList(
                    array(
                        'filter' => array(
                            "LOGIC" => "OR",
                            array("STRING_ID" => "SUPPORT"),
                            array("STRING_ID" => "SUPPORT_ADMIN")
                        ),

                    )
                )->fetchAll();
                $isSupport = false;
                if(is_array($rsGroups)) {
                    foreach ($rsGroups as $group) {
                        if (in_array($group['ID'], $arUserGroups)) {
                            $isSupport = true;
                            break;
                        }
                    }
                }
                if ($key == 0) {
                    ?>
                    <div class="index_appeals-answer-day-time">
                        <?=FormatDate('d f Y', MakeTimeStamp($arResult["MESSAGES"][$key]["DATE_CREATE"]))?>
                    </div>
                    <?
                }
                elseif ($key > 0) {
                    if (MakeTimeStamp($arResult["MESSAGES"][$key]["DATE_CREATE"]) - MakeTimeStamp($arResult["MESSAGES"][$key - 1]["DATE_CREATE"]) > 86400) {
                        ?>
                            <div class="index_appeals-answer-day-time">
                                <?=FormatDate('d f Y', MakeTimeStamp($arResult["MESSAGES"][$key]["DATE_CREATE"]))?>
                            </div>
                        <?
                    }
                }
                ?>

                <div class="index_appeals-answer-row-messages <?echo ($isSupport) ? "support-answer":""?>">
                    <div class="col-sm-8 col-9 <?echo ($isSupport) ? "offset-sm-4 offset-3":""?>">
                        <div class="card card-bitrix-cabinet">
                            <div class="card-header d-flex">
                                <h6 class="card-title mb-0">
                                    <?if (intval($message["OWNER_USER_ID"])>0):?>
                                        [<?=$message["OWNER_USER_ID"]?>]
                                        (<?=$message["OWNER_LOGIN"]?>)
                                        <?=$message["OWNER_NAME"]?>
                                    <?endif?>
                                </h6>
                                <span class="text-muted ms-3">
                                    <?=FormatDate("H:m", MakeTimeStamp($message["DATE_CREATE"]))?>
                                </span>
                                <div class="d-inline-flex ms-auto">
                                    <a class="text-primary px-2"
                                        href="#postform"
                                        OnMouseDown="javascript:SupQuoteMessage('quotetd<? echo $message["ID"]; ?>')"
                                    >
                                        <i class="ph-quotes"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body">
                                    <div id="quotetd<? echo $message["ID"]; ?>">
                                        <?=$message["MESSAGE"]?>
                                    </div>
                                    <div class="quatetd-images">
                                        <?$aImg = array("gif", "png", "jpg", "jpeg", "bmp");
                                        foreach ($message["FILES"] as $arFile):?>
                                            <?if(in_array(strtolower(GetFileExtension($arFile["NAME"])), $aImg)):?>
                                                <a target="_blank" title="<?=GetMessage("SUP_VIEW_ALT")?>" href="<?=$componentPath?>/ticket_show_file.php?hash=<?echo $arFile["HASH"]?>&amp;lang=<?=LANG?>"><?=$arFile["NAME"]?></a> 
                                            <?else:?>
                                                <?=$arFile["NAME"]?>
                                            <?endif?>
                                            (<? echo CFile::FormatSize($arFile["FILE_SIZE"]); ?>)
                                            [ <a title="<?=str_replace("#FILE_NAME#", $arFile["NAME"], GetMessage("SUP_DOWNLOAD_ALT"))?>" href="<?=$componentPath?>/ticket_show_file.php?hash=<?=$arFile["HASH"]?>&amp;lang=<?=LANG?>&amp;action=download"><?=GetMessage("SUP_DOWNLOAD")?></a> ]
                                            <br class="clear" />
                                        <?endforeach?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?endforeach;?>

            <?= $arResult["NAV_STRING"] ?>
        </div>
        <?
    }
    ?>
    <div class="form-group">
        <label class="form-label">
            <?=Loc::getMessage('SUP_TITLE')?> <span class="req">*</span>
        </label>
        <input
            name="TITLE"
            id="TITLE"
            value="<?=$arResult['TICKET']['TITLE']?>"
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
            <?foreach ($arResult["DICTIONARY"]["MARK"] as $value => $option):?>
                <option value="<?=$value?>" <?if($mark == $value):?>selected="selected"<?endif?>><?=$option?></option>
            <?endforeach?>
        </select>
    </div>
    <div class="d-flex justify-content-end gap-4">
        <input type="submit" class="btn" name="apply" value="<?=Loc::getMessage('SUP_APPLY')?>">
        <input type="submit" class="btn btn-primary" name="save" value="<?=Loc::getMessage('SUP_SAVE')?>">
    </div>
</form>
<script>
    BX.message({
        SITE_TEMPLATE_PATH: '<?= SITE_TEMPLATE_PATH ?>',
        FILE_NOT_SELECTED_TEXT: '<?=Loc::getMessage("SUP_CHOOSE_NO")?>'
    });

    var inputs = document.querySelectorAll('.input-file');
    Array.prototype.forEach.call(inputs, function (input)
    {
        const label = input.nextElementSibling;

        input.addEventListener('change', function (e)
        {
            var fileName = '';
            if (this.files && this.files.length > 1)
                fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
            else
                fileName = e.target.value.split('\\').pop();

            if (fileName)
            {
                label.innerHTML = fileName;
            }
        });
    });
</script>