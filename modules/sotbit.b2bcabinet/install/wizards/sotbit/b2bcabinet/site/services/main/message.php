<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

\Bitrix\Main\Loader::includeModule('main');
use Bitrix\Main\Localization\Loc;

$templateId = 'b2bcabinet_mail';

$obEventType = new CEventType();
$obEventType->Add( array(
    "EVENT_NAME" => "SOTBIT_B2BCABINET_WHOLESALER_REGISTER",
    "NAME" => GetMessage( 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER_NAME' ),
    "LID" => LANGUAGE_ID,
    "DESCRIPTION" => GetMessage( 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER_DESCRIPTION' ),
) );

$oEventMessage = new CEventMessage();
$oEventMessage->Add( array(
    'ACTIVE' => 'Y',
    'EVENT_NAME' => 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER',
    'LID' => WIZARD_SITE_ID,
    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
    'EMAIL_TO' => '#EMAIL#',
    'SUBJECT' => GetMessage( 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER_SUBJECT' ),
    'MESSAGE' => GetMessage( 'SOTBIT_B2BCABINET_WHOLESALER_REGISTER_MESSAGE' ),
    'BODY_TYPE' => 'html',
    'SITE_TEMPLATE_ID' => $templateId
) );

$arEvents = [
    'NEW_USER'
];

$authMailEvents = array_column(\Bitrix\Main\Mail\Internal\EventMessageTable::query()
    ->addSelect('ID')
    ->whereIn('EVENT_NAME', $arEvents)
    ->fetchAll() ?: [], 'ID');

if ($authMailEvents) {
    array_walk($authMailEvents, fn($id) => \Bitrix\Main\Mail\Internal\EventMessageTable::update($id, ['SITE_TEMPLATE_ID' => $templateId]));
}