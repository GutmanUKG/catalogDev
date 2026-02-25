<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$ticketExist = ( !empty($arResult["TICKET"]['ID']) && !empty($arResult['TICKET']['TITLE']) );

?>
<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<form name="support_edit" class="h-100" method="post" action="<?=$arResult["REAL_FILE_PATH"]?>" enctype="multipart/form-data">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="set_default" value="Y">
    <input type="hidden" value="Y" name="apply" />
    <?if (empty($ticketExist)): ?>
        <? 
            if ($_REQUEST["ajax"] === 'Y') {
                $APPLICATION->RestartBuffer();
            }
            
            require_once($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/ajax.php');
            if ($_REQUEST["ajax"] === 'Y') {
                die();
             } ?>
    <? else: ?>
    <div class="index_appeals h-100">
        <div class="<?=empty($arParams['COMPLAINT_ID']) ? 'row' : 'negative-mx-3'?> h-100">
            <div class="<?=empty($arParams['COMPLAINT_ID']) ? 'col-lg-7' : ''?>">
                <div class="card <?=!empty($arParams['COMPLAINT_ID']) ? 'shadow-none' : ''?> h-100">
                    <? if (!empty($arParams['COMPLAINT_ID'])):?>
                    <a class="d-flex align-items-center ms-3" data-card-action="collapse">
                        <?=Loc::getMessage('SUP_HIDDEN_MESSAGE')?>
                        <i class="ms-2 ph-caret-down fs-base"></i>
                    </a> 
                    <div class="collapse show">
                    <? endif; ?>

                        <div class="card-body index_appeal_form <?=!empty($arParams['COMPLAINT_ID']) ? 'h-auto' : ''?>">
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
                                                                onclick="SupQuoteMessage('quotetd<? echo $message["ID"]; ?>'); return false;"
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

                            <?if(strlen($arResult['TICKET']['DATE_CLOSE'] <= 0)):?>
                                <div class="form-group form-group-answe">
                                    <label class="form-label">
                                        <?=Loc::getMessage('SUP_MESSAGE')?>
                                    </label>
                                    <textarea name="MESSAGE" id="MESSAGE" rows="5" cols="5" class="form-control trumbowyg"></textarea>
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
                                <? if(empty($arParams['COMPLAINT_ID'])): ?>
                                    <div class="index_appeals-answer-footer">
                                        <input type="submit" class="btn btn-primary apply_support_message" name="apply" value="<?=GetMessage("SUP_APPLY")?>" />
                                    </div>
                                <? endif; ?>
                            <?endif;?>
                        </div>
                    <? if (!empty($arParams['COMPLAINT_ID'])):?>
                    </div>                
                    <? endif; ?>
                </div>
            </div>
            <div class="<?=empty($arParams['COMPLAINT_ID']) ? 'col-lg-5' : ''?>">
                <div class="<?=empty($arParams['COMPLAINT_ID']) ? 'card' : ''?>">
                    <? if (empty($arParams['COMPLAINT_ID'])): ?>
                        <div class="card-header d-flex flex-wrap">
                            <h6 class="card-title mb-0 fw-bold"><?= Loc::getMessage('SUP_TICKET') ?></h6>
                            <div class="d-inline-flex ms-auto">
                                <a class="text-body px-2" data-card-action="collapse">
                                    <i class="ph ph-caret-down"></i>
                                </a>
                            </div>
                        </div>
                    <? endif; ?>
                    <div class="collapse show">
                        <?if (empty($arParams['COMPLAINT_ID'])): ?>
                        <div class="card-body pt-0">
                            <dl class="card-content">
                                <div class="card-content__row">
                                    <dt><?= Loc::getMessage('SUP_SOURCE_FROM') ?></dt>
                                    <dd>
                                        <?
                                        if (strlen($arResult["TICKET"]["SOURCE_NAME"]) > 0):?>
                                            [<?= $arResult["TICKET"]["SOURCE_NAME"] ?>]
                                        <? else:?>
                                            [web]
                                        <?endif ?>

                                        <?
                                        if (strlen($arResult["TICKET"]["OWNER_SID"]) > 0):?>
                                            <?= $arResult["TICKET"]["OWNER_SID"] ?>
                                        <?endif ?>

                                        <?
                                        if (intval($arResult["TICKET"]["OWNER_USER_ID"]) > 0):?>
                                            [<?= $arResult["TICKET"]["OWNER_USER_ID"] ?>]
                                            (<?= $arResult["TICKET"]["OWNER_LOGIN"] ?>)
                                            <?= $arResult["TICKET"]["OWNER_NAME"] ?>
                                        <?endif ?>
                                    </dd>
                                </div>

                                <div class="card-content__row">
                                    <dt><?= Loc::getMessage('SUP_CREATE') ?></dt>
                                    <dd>
                                        <?= FormatDate($DB->DateFormatToPHP(CSite::GetDateFormat('FULL')), MakeTimeStamp($arResult["TICKET"]["DATE_CREATE"])) ?>

                                        <?
                                        if (strlen($arResult["TICKET"]["CREATED_MODULE_NAME"]) <= 0 || $arResult["TICKET"]["CREATED_MODULE_NAME"] == "support"):?>
                                            [<?= $arResult["TICKET"]["CREATED_USER_ID"] ?>]
                                            (<?= $arResult["TICKET"]["CREATED_LOGIN"] ?>)
                                            <?= $arResult["TICKET"]["CREATED_NAME"] ?>
                                        <? else:?>
                                            <?= $arResult["TICKET"]["CREATED_MODULE_NAME"] ?>
                                        <?endif ?>
                                    </dd>
                                </div>

                                <div class="card-content__row">
                                    <dt><?= Loc::getMessage('SUP_TIMESTAMP') ?></dt>
                                    <dd>
                                        <?
                                        if ($arResult["TICKET"]["DATE_CREATE"] != $arResult["TICKET"]["TIMESTAMP_X"]):?>
                                            <?= FormatDate($DB->DateFormatToPHP(CSite::GetDateFormat('FULL')), MakeTimeStamp($arResult["TICKET"]["TIMESTAMP_X"])) ?>
                                            <?
                                            if (strlen($arResult["TICKET"]["MODIFIED_MODULE_NAME"]) <= 0 || $arResult["TICKET"]["MODIFIED_MODULE_NAME"] == "support"):?>
                                                [<?= $arResult["TICKET"]["MODIFIED_USER_ID"] ?>]
                                                (<?= $arResult["TICKET"]["MODIFIED_BY_LOGIN"] ?>)
                                                <?= $arResult["TICKET"]["MODIFIED_BY_NAME"] ?>
                                            <? else:?>
                                                <?= $arResult["TICKET"]["MODIFIED_MODULE_NAME"] ?>
                                            <?endif ?>
                                        <?endif ?>
                                    </dd>
                                </div>

                                <div class="card-content__row">
                                    <dt><?= Loc::getMessage('SUP_SLA') ?></dt>
                                    <dd>
                                        <?
                                        if (strlen($arResult["TICKET"]["SLA_NAME"]) > 0) :?>
                                            <?= $arResult["TICKET"]["SLA_NAME"] ?>
                                        <?endif ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <? endif ?>

                        <div class="card-body mt-2 pt-0 <?=!empty($arParams["COMPLAINT_ID"]) ? 'pb-0' : ''?>">
                            <?if(!empty($arParams["ORDER_ID"])):?>
                            <input
                                    type="hidden"
                                    name="TITLE"
                                    value="<?=(
                                            !empty($arResult['TICKET']['TITLE']) ?
                                                $arResult['TICKET']['TITLE'] :
                                                Loc::getMessage('SPOD_ORDER') .Loc::getMessage('SUP_NUM'). htmlspecialcharsbx($arParams["ORDER_ID"])
                                    )?>"
                            >
                            <?endif;?>
                            <?if(!empty($arParams["COMPLAINT_ID"])):?>
                                <input
                                        type="hidden"
                                        name="TITLE"
                                        value="<?=(
                                        !empty($arResult['TICKET']['TITLE']) ?
                                            $arResult['TICKET']['TITLE'] :
                                            Loc::getMessage('SUP_COMPLAINT_TITLE') .Loc::getMessage('SUP_NUM'). htmlspecialcharsbx($arParams["COMPLAINT_ID"])
                                        )?>"
                                >
                                <input type="hidden" name="COMPLAINT_ID" value="<?=$arParams['COMPLAINT_ID']?>">
                            <?endif;?>
                            <input type="hidden" name="UF_ORDER" value="<?=$arParams['ORDER_ID']?>">
                            <input type="hidden" name="ID" value="<?=(empty($arResult["TICKET"]["ID"]) ? 0 : $arResult["TICKET"]["ID"])?>">
                            <input type="hidden" name="lang" value="<?=LANG?>">
                            <input type="hidden" name="edit" value="1">

    <!--                            --><?//if(strlen($arResult['TICKET']['DATE_CLOSE'] <= 0)):?>

                                <?if(empty($arParams["ORDER_ID"]) && empty($arParams["COMPLAINT_ID"])):?>
                                    <label class="form-label">
                                        <?=Loc::getMessage('SUP_TITLE')?> <span class="req">*</span>:
                                    </label>
                                    <?if (!empty($arResult['TICKET']['TITLE'])): ?>
                                        <input type="hidden" name="TITLE" value="<?= $arResult['TICKET']['TITLE'] ?>">
                                        <span><?=$arResult['TICKET']['TITLE']?></span>
                                    <? else: ?>
                                        <input
                                            name="TITLE"
                                            id="TITLE"
                                            value="<?=$arResult['TICKET']['TITLE']?>"
                                            class="form-control mb-3"
                                        >
                                    <? endif; ?>
                                <?endif;?>

                                <? if(empty($arParams['COMPLAINT_ID'])): ?>
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
                                <?endif;?>

                                <?if(strlen($arResult['TICKET']['DATE_CLOSE'] <= 0)):?>
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
                                <?endif;?>
                                <div class="form-group d-flex gap-3 flex-column flex-sm-row mt-4">
                                    <?if(!empty($arResult["TICKET"]['ID']) && strlen($arResult["TICKET"]["DATE_CLOSE"]) <= 0):?>
                                        <? if(!empty($arParams['COMPLAINT_ID'])): ?>
                                            <input type="submit" class="btn btn-primary apply_support_message" name="apply" value="<?=GetMessage("SUP_APPLY")?>" />
                                        <? else: ?>
                                            <button type="submit" name="CLOSE" class="btn" value="Y">
                                                <?=Loc::getMessage('SUP_CLOSE')?>
                                            </button>
                                        <? endif; ?>
                                    <?else:?>
                                        <input type="hidden" name="OPEN" value="Y">
                                        <input type="submit" class="btn apply_support_message" name="apply" value="<?=Loc::getMessage('SUP_OPEN')?>">
                                    <?endif;?>
                                </div>
    <!--                            --><?//endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? endif;?>
</form>

<script type="text/javascript">
    var inputs = document.querySelectorAll('.input-file');
    Array.prototype.forEach.call(inputs, function (input)
    {
        App.initFile(input);
    });
    BX.ready(function ()
    {
        var buttons = BX.findChildren(document.forms['support_edit'], {attr: {type: 'submit'}});
        for (i in buttons)
        {
            BX.bind(buttons[i], "click", function (e)
            {
                setTimeout(function ()
                {
                    var _buttons = BX.findChildren(document.forms['support_edit'], {attr: {type: 'submit'}});
                    for (j in _buttons)
                    {
                        _buttons[j].disabled = true;
                    }

                }, 30);
            });
        }

        let applyBtn = document.querySelector(".apply_support_message");

        function setCookie(name, value, options = {}) {

            options = {
                path: '/',
            };

            if (options.expires !== undefined && options.expires.toUTCString) {
                options.expires = options.expires.toUTCString();
            }

            let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

            for (let optionKey in options) {
                updatedCookie += "; " + optionKey;
                let optionValue = options[optionKey];
                if (optionValue !== true) {
                    updatedCookie += "=" + optionValue;
                }
            }

            document.cookie = updatedCookie;
        }

        applyBtn.addEventListener("click", function () {
            setCookie("sended", "Y");
        })
    });

    BX.message({
        SITE_TEMPLATE_PATH: '<?= SITE_TEMPLATE_PATH ?>',
        FILE_NOT_SELECTED_TEXT: '<?=Loc::getMessage("SUP_CHOOSE_NO")?>'
    });

    BX.addCustomEvent('onAjaxSuccess', function(result) {
        if (!!result) return;
        
        App.initSelect2();
        App.initCardActions();
        Trumbowyg.init();
    })
</script>