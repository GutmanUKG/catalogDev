<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/local/templates/b2bcabinet_mail/helper.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/local/templates/b2bcabinet_mail/params.php';

$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
$serverName = $protocol . "://" . $arParams["SERVER_NAME"];
$logoSRC = getLogoPath(getSiteLogoPath($arParams['SITE_ID'], $serverName) ?: ($serverName . '/local/templates/b2bcabinet_mail/img/logo.png'));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
</head>
<body>

<? if (\Bitrix\Main\Loader::includeModule('mail')) : ?>
    <?= \Bitrix\Mail\Message::getQuoteStartMarker(true); ?>
<? endif; ?>

<table width="600" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:auto; padding: 0 32px;"
       bgcolor="#ffffff">
    <tr>
        <td>
            <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
                <tr>
                    <td align="center" colspan="2" style="padding: 12px;">
                        <a href="<?= $serverName ?>">
                            <img
                                    src="<?= $logoSRC ?>"
                                    alt="logo"
                                    border="0"
                                    width="100"
                                    height="70"
                                    style="display:block;text-decoration:none;outline:none;"
                            >
                        </a>
                    </td>
                </tr>
                <tr>
                    <td align="right" style="width: 50%; padding: 8px 0 16px 0;">
                        <a href="mailto:<?= $MAIL_CONSTANTS['EMAIL'] ?>"
                           style="margin-right: 18px; color: #202122; font-family: 'Open Sans', sans-serif; font-size: 14px; font-weight: 400; text-decoration: none;">
                            <img src="/local/templates/b2bcabinet_mail/img/mail_icon.png"
                                 alt=""
                                 border="0"
                                 width="20"
                                 height="20"
                                 style="border:0; outline:none; text-decoration:none; display:inline-block; vertical-align: middle; margin-right: 8px;"
                            >
                            <?= $MAIL_CONSTANTS['EMAIL'] ?>
                        </a>
                    </td>
                    <td align="left" style="width: 50%; padding: 8px 0 16px 0;">
                        <a href="tel:<?= $MAIL_CONSTANTS['PHONE_NUMBER'] ?>"
                           style="margin-left: 18px; color: #202122; font-family: 'Open Sans', sans-serif; font-size: 14px; font-weight: 400; text-decoration: none;">
                            <img
                                    src="/local/templates/b2bcabinet_mail/img/tel_icon.png"
                                    alt=""
                                    border="0"
                                    width="20"
                                    height="20"
                                    style="border:0; outline:none; text-decoration:none; display:inline-block; vertical-align: middle; margin-right: 8px;"
                            >
                            <?= $MAIL_CONSTANTS['PHONE_NUMBER'] ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 2px; background: linear-gradient(90.06deg, #fff2f2 0%, #e5e0ff 23.42%, #8ea7e9 62.47%, #7286d3 99.95%);"></td>
                </tr>
            </table>
            <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                   style="padding:24px 0 24px 0">
                <tr>
                    <td>