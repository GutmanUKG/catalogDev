<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Web\Json,
    BItrix\Main\Context;

$strRand = $this->randString();
$strObName = 'obExcelImport_' . $strRand;

$inputStatus = Context::getCurrent()->getRequest()->getPost('mfi_mode');
$UPLOAD_STATUS = "upload";
$DELETE_STATUS = "delete";
$allowApply = false;
if ($inputStatus === 'upload') {
    $allowApply = true;
} else {
    $allowApply = false;
}

$signer = new Bitrix\Main\Security\Sign\Signer();
$templateSigns = $signer->sign(SITE_TEMPLATE_ID, "template_preview".bitrix_sessid());
?>

<? if ($arResult["ERROR_LIST"]):
    foreach ($arResult["ERROR_LIST"] as $obError):
        ShowError($obError->getMessage());
    endforeach;
else:?>

    <?if($arParams["USE_BUTTON"] !== 'N'):?>
        <button
                type="button"
                class="btn w-100 w-sm-auto"
                id="blank-excel-import"
                aria-label="<?=Loc::getMessage("B2B_EXCEL_IMPORT_BTN_TITLE")?>"
                data-bs-toggle="modal"
                data-bs-target="#modal_import_excel"
        >
            <?=$arParams['USE_ICON'] === 'Y' ? '<i class="ph-arrow-line-down me-2"></i>' : ''?>
            <?=Loc::getMessage("B2B_EXCEL_IMPORT_BTN_TITLE")?>
        </button>
    <?else:?>
            <button
                type="button"
                id="blank-excel-import"
                class="dropdown-item text-primary"
                data-bs-toggle="modal"
                data-bs-target="#modal_import_excel">
                    <?=$arParams['USE_ICON'] === 'Y' ? '<i class="ph-arrow-line-down me-2"></i>' : ''?>
                    <?= Loc::getMessage('B2B_EXCEL_IMPORT_BTN_TITLE') ?>
            </button>
    <?endif;?>
    <div id="modal_import_excel" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title">
                        <?= Loc::getMessage("B2B_EXCEL_IMPORT_MODAL_TITLE") ?>
                    </h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger alert-styled-left alert-dismissible" style="display: none"></div>
                    <form action="/" name="excel_import" id="excel_import" method="post">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.file.input",
                            "b2bcabinet_no_popup",
                            [
                                "INPUT_NAME" => $arParams["INPUT_NAME"],
                                "MULTIPLE" => $arParams["MULTIPLE"] ?: "N",
                                "MODULE_ID" => "sotbit.b2bcabinet",
                                "MAX_FILE_SIZE" => $arParams["MAX_FILE_SIZE"] ?: "",
                                "ALLOW_UPLOAD" => "F",
                                "ALLOW_UPLOAD_EXT" => "",
                                "TAB_ID" => $strObName
                            ],
                            false
                        ); ?>

                    </form>
                    <div class="excel-import__block-result">
                    </div>
                </div>
                <div class="modal-footer excel-import-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <?=GetMessage("B2B_EXCEL_IMPORT_MODAL_BTN_CLOSE")?>
                    </button>
                    <button type="button" name="send_files_excel" class="btn btn-primary" disabled>
                        <i></i>
                        <?= Loc::getMessage("B2B_EXCEL_IMPORT_MODAL_BTN_SEND") ?>
                    </button>
                </div>
                <div class="modal-footer excel-import-success-footer" style="display: none">
                    <button type="button" name="send_files_excel" class="btn btn-primary"  data-bs-dismiss="modal">
                            <?= Loc::getMessage("B2B_EXCEL_IMPORT_MODAL_CANCEL") ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        BX.message(
            {
                "error_no_file": '<?=Loc::getMessage('B2B_EXCEL_IMPORT_MODAL_NO_FILES')?>'
            }
        );
        var <?=$strObName?> = new JCB2BExcelImport({
            siteId: '<?=$this->__component->getSiteId()?>',
            componentPath: '<?=$componentPath?>',
            parameters: '<?=$this->getComponent()->getSignedParameters()?>',
            templateSignsString: '<?=$templateSigns?>',
            btnSendSelector: '[name="send_files_excel"]',
            formSelector: '#excel_import',
            inputName: '<?=$arParams["INPUT_NAME"]?>',
            errorBlockSelector: '.alert-dismissible',
            resultBlockSelector: '.excel-import__block-result',
            modalSelector: '#modal_import_excel',
            modalFooter: '.excel-import-footer',
            modalSuccessFooter: '.excel-import-success-footer',
        });
    </script>

<? endif; ?>
