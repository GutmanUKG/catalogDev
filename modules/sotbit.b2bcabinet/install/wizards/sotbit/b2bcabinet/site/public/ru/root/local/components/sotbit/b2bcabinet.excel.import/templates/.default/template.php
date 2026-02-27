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
?>

<? if ($arResult["ERROR_LIST"]):
    foreach ($arResult["ERROR_LIST"] as $obError):
        ShowError($obError->getMessage());
    endforeach;
else:?>

    <?if($arParams["USE_BUTTON"] !== 'N'):?>
        <button
                type="button"
                class="btn btn-light btn-ladda btn-ladda-spinner"
                data-spinner-color="#333"
                data-style="slide-right"
                id="blank-excel-import"
                data-toggle="modal"
                data-target="#modal_import_excel"
        >
            <span class="ladda-label export_excel_preloader">
                <i class="icon-download mr-2"></i>
               <?=Loc::getMessage("B2B_EXCEL_IMPORT_BTN_TITLE")?>
            </span>
        </button>
    <?else:?>
            <span
                id="blank-excel-import"
                class="dropdown-item"
                data-toggle="modal"
                data-target="#modal_import_excel">
                    <?=$arParams['USE_ICON'] === 'Y' ? '<i class="icon-download mr-2"></i>' : ''?>
                    <?= Loc::getMessage('B2B_EXCEL_IMPORT_BTN_TITLE') ?>
            </span>
    <?endif;?>
    <div id="modal_import_excel" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-main">
                    <h6 class="modal-title">
                        <?= Loc::getMessage("B2B_EXCEL_IMPORT_MODAL_TITLE") ?>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="btn btn-link btn-light" data-dismiss="modal">
                        <?=GetMessage("B2B_EXCEL_IMPORT_MODAL_BTN_CLOSE")?>
                    </button>
                    <button type="button" name="send_files_excel" class="btn btn_b2b" disabled>
                        <span class="ladda-label export_excel_preloader">
                            <?= Loc::getMessage("B2B_EXCEL_IMPORT_MODAL_BTN_SEND") ?>
                            <i></i>
                        </span>
                    </button>
                </div>
                <div class="modal-footer excel-import-success-footer" style="display: none">
                    <button type="button" name="send_files_excel" class="btn btn_b2b"  data-dismiss="modal">
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






