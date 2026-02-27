<?php
use Bitrix\Main\Localization\Loc;
CAdminMessage::ShowMessage(Loc::getMessage('SB_UNINSTALL_STEP_MESSAGE'));
?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
    <input type="hidden" name="id" value="sotbit.b2bcabinet">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <p>
        <input type="checkbox" name="del_iblock" id="del_iblock" value="Y" checked>
        <label for="del_iblock"><?=Loc::getMessage('SB_UNINSTALL_STEP_IBLOCK');?></label>
    </p>
    <p>
        <input type="checkbox" name="del_database" id="del_database" value="Y" checked>
        <label for="del_database"><?=Loc::getMessage('SB_UNINSTALL_STEP_DB');?></label>
    </p>
    <p>
        <input type="checkbox" name="del_settings" id="del_settings" value="Y" checked>
        <label for="del_settings"><?=Loc::getMessage('SB_UNINSTALL_STEP_SETTINGS');?></label>
    </p>
    <p>
        <input type="checkbox" name="del_forms" id="del_forms" value="Y" checked>
        <label for="del_forms"><?=Loc::getMessage('SB_UNINSTALL_STEP_FORMS');?></label>
    </p>
    <p>
        <input type="checkbox" name="del_user_fields" id="del_user_fields" value="Y" checked>
        <label for="del_user_fields"><?=Loc::getMessage('SB_UNINSTALL_STEP_USER_FIELDS');?></label>
    </p>
    <p>
        <input type="checkbox" name="del_user" id="del_user" value="Y" checked>
        <label for="del_user"><?=Loc::getMessage('SB_UNINSTALL_STEP_USERS');?></label>
    </p>
    <input type="submit" name="confirm_delete_module" value="<?=Loc::getMessage('SB_UNINSTALL_STEP_BTN');?>">
</form>