<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserFieldTable;


$arPersonType = $this->arCurOptionValues['BUYER_PERSONAL_TYPE'];

if (!$arPersonType) {
    ?>
    <tr>
        <td valign="top" colspan="2">
            <div class="adm-info-message">
                <span class="required"><?= Loc::getMessage('SB_SETTINGS_ERROR_NOT_PERSON_TYPE'); ?></span>
            </div>
        </td>
    </tr>
    <?
    return;
}

$arCurPerson = self::getPersonType($arPersonType);
$arPersonTab = [];
$arRegisterOptions = [];
foreach ($arCurPerson as $personId => $personName) {
    $arPersonTab[] = [
        'DIV' => $personId,
        'TAB' => $personName,
        'TITLE' => $personName,
    ];

    $arGroupReqFields[$personId] = [];
    $curGroupFields = $this->arCurOptionValues['GROUP_FIELDS_' . $personId];

    foreach ($curGroupFields as $fieldCode) {
        $arGroupReqFields[$personId]['REFERENCE_ID'][] = $fieldCode;
        $arGroupReqFields[$personId]['REFERENCE'][] = "[{$fieldCode}] " . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_' . $fieldCode);
    }
}


$personTabControl = new CAdminViewTabControl("OPTION_REGISTER_TAB", $arPersonTab);
$personTabControl->Begin();

$userFieldsRegister = array(
    'REFERENCE_ID' => array(
        'EMAIL',
        'TITLE',
        'NAME',
        'SECOND_NAME',
        'LAST_NAME',
        'PERSONAL_PROFESSION',
        'PERSONAL_WWW',
        'PERSONAL_ICQ',
        'PERSONAL_GENDER',
        'PERSONAL_BIRTHDAY',
        'PERSONAL_PHOTO',
        'PERSONAL_PHONE',
        'PERSONAL_FAX',
        'PERSONAL_MOBILE',
        'PERSONAL_PAGER',
        'PERSONAL_STREET',
        'PERSONAL_MAILBOX',
        'PERSONAL_CITY',
        'PERSONAL_STATE',
        'PERSONAL_ZIP',
        'PERSONAL_COUNTRY',
        'PERSONAL_NOTES',
        'WORK_COMPANY',
        'WORK_DEPARTMENT',
        'WORK_POSITION',
        'WORK_WWW',
        'WORK_PHONE',
        'WORK_FAX',
        'WORK_PAGER',
        'WORK_STREET',
        'WORK_MAILBOX',
        'WORK_CITY',
        'WORK_STATE',
        'WORK_ZIP',
        'WORK_COUNTRY',
        'WORK_PROFILE',
        'WORK_LOGO',
        'WORK_NOTES',
    ),
    'REFERENCE' => array(
        '[EMAIL] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_EMAIL'),
        '[TITLE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_TITLE'),
        '[NAME] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_NAME'),
        '[SECOND_NAME] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_SECOND_NAME'),
        '[LAST_NAME] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_LAST_NAME'),
        '[PERSONAL_PROFESSION] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_PROFESSION'),
        '[PERSONAL_WWW] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_WWW'),
        '[PERSONAL_ICQ] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_ICQ'),
        '[PERSONAL_GENDER] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_GENDER'),
        '[PERSONAL_BIRTHDAY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_BIRTHDAY'),
        '[PERSONAL_PHOTO] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_PHOTO'),
        '[PERSONAL_PHONE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_PHONE'),
        '[PERSONAL_FAX] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_FAX'),
        '[PERSONAL_MOBILE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_MOBILE'),
        '[PERSONAL_PAGER] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_PAGER'),
        '[PERSONAL_STREET] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_STREET'),
        '[PERSONAL_MAILBOX] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_MAILBOX'),
        '[PERSONAL_CITY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_CITY'),
        '[PERSONAL_STATE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_STATE'),
        '[PERSONAL_ZIP] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_ZIP'),
        '[PERSONAL_COUNTRY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_COUNTRY'),
        '[PERSONAL_NOTES] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_PERSONAL_NOTES'),
        '[WORK_COMPANY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_COMPANY'),
        '[WORK_DEPARTMENT] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_DEPARTMENT'),
        '[WORK_POSITION] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_POSITION'),
        '[WORK_WWW] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_WWW'),
        '[WORK_PHONE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_PHONE'),
        '[WORK_FAX] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_FAX'),
        '[WORK_PAGER] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_PAGER'),
        '[WORK_STREET] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_STREET'),
        '[WORK_MAILBOX] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_MAILBOX'),
        '[WORK_CITY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_CITY'),
        '[WORK_STATE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_STATE'),
        '[WORK_ZIP] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_ZIP'),
        '[WORK_COUNTRY] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_COUNTRY'),
        '[WORK_PROFILE] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_PROFILE'),
        '[WORK_LOGO] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_LOGO'),
        '[WORK_NOTES] ' . Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_WORK_NOTES')
    )
);

$ufUserFields = array_column(UserFieldTable::query()
    ->addSelect('FIELD_NAME')
    ->addOrder('SORT')
    ->where('ENTITY_ID', 'USER')
    ->fetchAll() ?: [], 'FIELD_NAME');

$printUfUserFields = [];

foreach ($ufUserFields as $userField) {
    $printUfUserFields["REFERENCE_ID"][] = $userField;
    $printUfUserFields["REFERENCE"][] = $userField;
}

$orderProps = \Bitrix\Sale\Internals\OrderPropsTable::query()
    ->setSelect(['CODE', 'NAME', 'PERSON_TYPE_ID'])
    ->where('ACTIVE', 'Y')
    ->whereIn('PERSON_TYPE_ID', $arPersonType)
    ->fetchAll();

$arOrderProps = [];
foreach ($orderProps as $arProperty) {
    $arOrderProps[$arProperty['PERSON_TYPE_ID']]['REFERENCE_ID'][] = $arProperty['CODE'];
    $arOrderProps[$arProperty['PERSON_TYPE_ID']]['REFERENCE'][] = "[{$arProperty['CODE']}] {$arProperty['NAME']}";
}

?>
<tr>
    <td valign="top" colspan="2">
        <?
        foreach ($arPersonTab as $personTab) {
            $personTabControl->BeginNextTab();
            if (!$arOrderProps[$personTab["DIV"]]) {
                $arOrderProps[$personTab["DIV"]] = [];
                echo ' <div align="center" class="adm-info-message-wrap">
                           <div class="adm-info-message">
                               <span class="required">
                                    '.Loc::getMessage('SB_SETTINGS_REGISTER_NOT_ORDER_PROPS_HINT').'
                               </span>
                           </div>
                        </div>';
            }
            ?>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="logistics_stores">
                <tr>
                    <td width="50%"><?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_GROUP_FIELDS') ?></td>
                    <td width="50%"><?= SelectBoxMFromArray("GROUP_FIELDS_{$personTab["DIV"]}[]", $userFieldsRegister, $this->arCurOptionValues["GROUP_FIELDS_{$personTab["DIV"]}"], "", false, 10) ?></td>
                </tr>
                <tr>
                    <td width="50%"><?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_USER_DOP_FIELDS') ?></td>
                    <td width="50%"><?= SelectBoxMFromArray("USER_DOP_FIELDS_{$personTab["DIV"]}[]", $printUfUserFields, $this->arCurOptionValues["USER_DOP_FIELDS_{$personTab["DIV"]}"], "", false, 5) ?></td>
                </tr>
                <tr>
                    <td width="50%"><?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_GROUP_REQUIRED_FIELDS'); ?></td>
                    <td width="50%"><?= SelectBoxMFromArray("GROUP_REQUIRED_FIELDS_{$personTab["DIV"]}[]", $arGroupReqFields[$personTab["DIV"]], $this->arCurOptionValues["GROUP_REQUIRED_FIELDS_{$personTab["DIV"]}"], "", false, 5) ?></td>
                </tr>
                <tr>
                    <td width="50%"><?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_GROUP_ORDER_FIELDS'); ?></td>
                    <td width="50%"><?= SelectBoxMFromArray("GROUP_ORDER_FIELDS_{$personTab["DIV"]}[]", $arOrderProps[$personTab["DIV"]], $this->arCurOptionValues["GROUP_ORDER_FIELDS_{$personTab["DIV"]}"], "", false, 10) ?></td>
                </tr>
                <tr>
                    <td width="50%"><?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_GROUP_UNIQUE_FIELDS'); ?></td>
                    <td width="50%"><?= SelectBoxFromArray("GROUP_UNIQUE_FIELDS_{$personTab["DIV"]}", $arOrderProps[$personTab["DIV"]], $this->arCurOptionValues["GROUP_UNIQUE_FIELDS_{$personTab["DIV"]}"], Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_NOT_SELECTED'), false, 10) ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div align="center" class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?= Loc::getMessage('SB_SETTINGS_REGISTER_USER_FIELD_UNIQUE_NOTE') ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <?
        }
        $personTabControl->End();
        ?>
    </td>
</tr>