<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}
CAdminMessage::ShowNote(Loc::getMessage("SOTBIT_B2BCABINET_INSTALL_OK"));
?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <a href="/bitrix/admin/wizard_install.php?lang=<?= LANGUAGE_ID ?>'&wizardName=sotbit.b2bcabinet:sotbit:b2bcabinet&<?= bitrix_sessid_get() ?>"
       class="adm-btn">
        <?= Loc::getMessage('SOTBIT_B2BCABINET_INSTALL_MASTER_BTN') ?>
    </a>
<form>
