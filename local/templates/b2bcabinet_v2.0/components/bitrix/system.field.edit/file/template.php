<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if(!is_array($arParams['ATTR']))
    $arParams['ATTR'] = array();
?>
<div class="form-group row">
    <?if(!empty($arParams['LABEL'])):?>
        <label class="col-lg-4 col-form-label">
            <?=$arParams['LABEL']?>
            <?if(in_array('required', $arParams['ATTR'])) {
                echo '<span class="req">*</span>';
            }?>
        </label>
    <?endif;?>
    <div class="col-lg-8">
        <div class="media d-flex align-items-center gap-2">
            <div class="media-body add_more_files">
                <div class="upload-file">
                    <?if(!empty($arParams['VALUE'])):?>
                        <?=CFile::ShowImage(
                           CFile::ResizeImageGet(
                                $arParams['VALUE'],
                                [
                                    "width" => "42",
                                    "height" => "42"
                                ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            )['src'],
                            42,
                            42,
                            'class="rounded-round" id="files_preview_0" loading="lazy"'
                        )?>
                    <?else:?>
                        <img src="<?=SITE_TEMPLATE_PATH . '/assets/images/placeholders/user.png'?>"
                                width="42"
                                height="42"
                                loading="lazy"
                                id="files_preview_0"
                                class="rounded-round"
                                alt="">
                    <?endif;?>
                    <input type="file" <?=(!empty($arParams['NAME']) ? "name='".$arParams['NAME']."'" : "")?> value="<?=CFile::GetPath($arParams['VALUE'])?>" size="30" class="input-file" data-fouc onchange="App.showPreviewPicture(0)">
                    <span class="filename"><?=Loc::getMessage('SUP_CHOOSE_NO')?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    App.initFile(document.querySelector('.input-file'))
</script>
