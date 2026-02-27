<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
?>

    <button
        class="btn btn-primary white-space-nowrap w-100 w-sm-auto"
        id="add-ordertemplate"
        data-bs-toggle="modal"
        data-bs-target="#modal-add-ordertemplate"
    >
        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATE_ADD_CREATE")?>
    </button>

<div id="modal-add-ordertemplate" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h6 class="modal-title">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATE_ADD_CREATE")?>
                </h6>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger alert-styled-left alert-dismissible" style="display: none"></div>
                <form action="/" name="add_ordertemplate" id="add_ordertemplate" method="post">
                    <?
                    $strRand = $this->randString();
                    $strObName = 'obOrderTemplateAdd_' . $strRand;
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.file.input",
                        "b2bcabinet_no_popup",
                        [
                            "INPUT_NAME" => $arParams["INPUT_NAME"],
                            "MULTIPLE" => "N",
                            "MODULE_ID" => "sotbit.b2bcabinet",
                            "MAX_FILE_SIZE" => $arParams["MAX_FILE_SIZE"] ?: "",
                            "ALLOW_UPLOAD" => "F",
                            "ALLOW_UPLOAD_EXT" => "",
                            "TAB_ID" => $strObName
                        ],
                        false
                    ); ?>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <?=GetMessage("SOTBIT_ORDERTEMPLATE_BTN_RESET")?>
                </button>
                <button type="button" name="send_file" class="btn btn-primary" disabled>
                    <i></i>
                    <?=GetMessage("SOTBIT_ORDERTEMPLATE_BTN_SUBMIT")?>
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    BX.message(
        {
            "error_no_file": '<?=Loc::getMessage('ORDER_TEMPLATE_ADD_ERROR_NOT_FILE')?>'
        }
    );
    var <?=$strObName?> = new JCB2BOrderTemplateAdd({
        siteId: '<?=$this->__component->getSiteId()?>',
        componentPath: '<?=$componentPath?>',
        parameters: '<?=$this->getComponent()->getSignedParameters()?>',
        btnSendSelector: '[name="send_file"]',
        formSelector: '#add_ordertemplate',
        inputName: '<?=$arParams["INPUT_NAME"]?>',
        errorBlockSelector: '.alert-dismissible',
        modalSelector: '#modal-add-ordertemplate',
        path_to_detail: '<?=$arParams["SEF_URL_TEMPLATES"]["detail"]?>',
    });
</script>
