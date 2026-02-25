<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
?>

<?if($arResult["ITEMS"]):?>
    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal_company_join">
        <?= Loc::getMessage('SOTBIT_COMPANY_JOINL_ADD_JOIN_PROFILE') ?>
    </button>

    <div id="modal_company_join" class="modal fade" tabindex="-1">
        <div class="modal-dialog" id="modal_company_join-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title">
                        <?=GetMessage("SOTBIT_COMPANY_JOIN_FORM_TITLE")?>
                    </h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>
                <form name="joinCompany" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="joinCompany__error-block"></div>
                        <input type="text" class="form-control join__search-company" placeholder="<?=GetMessage("SOTBIT_COMPANY_JOIN_FORM_INPUT_PLACEHOLDER")?>"/>
                        <div class="company-join__select-block"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" name="company-join-reset"class="btn" data-bs-dismiss="modal"><?=GetMessage("SOTBIT_COMPANY_JOIN_FORM_BTN_RESET")?></button>
                        <button type="button" name="company-join-send" class="btn btn-primary"><?=GetMessage("SOTBIT_COMPANY_JOIN_FORM_BTN_SUBMIT")?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalDialogJoinCompany = document.getElementById('modal_company_join');
        const bodyJoinCompany = document.querySelector('body');
        bodyJoinCompany.append(modalDialogJoinCompany);

        var companyJoin_companyList = <?=CUtil::PhpToJSObject($arResult["SELECT_ITEMS"])?>;
        BX.message({
            "SUCCESS_TITLE": '<?=Loc::getMessage("SOTBIT_COMPANY_JOIN_FORM_TITLE_SUCCESS_TITLE")?>',
            "SUCCESS_TEXT": '<?=Loc::getMessage("SOTBIT_COMPANY_JOIN_FORM_TITLE_SUCCESS_TEXT")?>',
            "INFO_TITLE": '<?=Loc::getMessage("SOTBIT_COMPANY_JOIN_FORM_TITLE_NO_COMPANIES")?>',
        });
    </script>
<?endif;?>
