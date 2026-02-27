<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
include $_SERVER['DOCUMENT_ROOT'] . '/local/templates/b2bcabinet_mail/params.php';

?>

</td>
</tr>
</table>
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
    <tr>
        <td colspan="2" style="height: 2px; background: linear-gradient(90.06deg, #fff2f2 0%, #e5e0ff 23.42%, #8ea7e9 62.47%, #7286d3 99.95%)"></td>
    </tr>
    <tr>
        <td colspan="2" align="center" style="padding: 24px 0 12px 0;">
            <div style="font-family: 'Open Sans', sans-serif; font-weight: 600;font-size: 16px;line-height: 22px;color: #202122;">
                <?= Loc::getMessage('CONTACT_INFO'); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td align="right" style="width: 50%; padding-bottom: 12px;">
            <a href="mailto:<?= $MAIL_CONSTANTS['EMAIL'] ?>"
               style="margin-right: 18px; color: #202122; font-family: 'Open Sans', sans-serif; font-size: 14px; font-weight: 400; text-decoration: none;">
                <img src="/local/templates/b2bcabinet_mail/img/mail_icon.png" alt="" border="0" width="20" height="20"
                     style="border:0; outline:none; text-decoration:none; display:inline-block; vertical-align: middle; margin-right: 8px;">
                <?= $MAIL_CONSTANTS['EMAIL'] ?>
            </a>
        </td>
        <td align="left" style="width: 50%; padding-bottom: 12px;">
            <a href="tel:<?=$MAIL_CONSTANTS['PHONE_NUMBER']?>"
               style="margin-left: 18px; color: #202122; font-family: 'Open Sans', sans-serif; font-size: 14px; font-weight: 400; text-decoration: none;">
                <img src="/local/templates/b2bcabinet_mail/img/tel_icon.png" alt="" border="0" width="20" height="20"
                     style="border:0; outline:none; text-decoration:none; display:inline-block; vertical-align: middle; margin-right: 8px;">
                <?=$MAIL_CONSTANTS['PHONE_NUMBER']?>
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <p style="text-align: center; font-family: 'Open Sans', sans-serif; font-weight: 400;font-size: 15px;line-height: 140%;text-align: center;color: #202122;">
                <?= Loc::getMessage('INFO_MESSAGE', ['#SITE_NAME#' => $arParams['SITE_NAME']]); ?>
            </p>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
</body>
</html>