<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$eventManager = \Bitrix\Main\EventManager::getInstance();

// $eventManager->addEventHandler('sale', 'OnSaleComponentOrderJsData', function(&$arResult, &$arParams) {
//     foreach ($arResult['LOCATIONS'] as &$location) {
//         if (!$location['showAlt']) {
//             $location['showAlt'] = true;
//         }
//     }
//     unset($location);
// });

// $eventManager->addEventHandler(
//     'sale',
//     'OnSaleOrderSaved',
//     function(\Bitrix\Main\Event $event) {
//         static $disallowHandler = false;

// 		if ($disallowHandler === false) {
// 			$disallowHandler = true;
	
// 			$order = $event->getParameter("ENTITY");
// 			$isNew = $event->getParameter("IS_NEW");
	
// 			\Onelab\Sale\Order\Main::onSavedHandler($order, $isNew);
	
// 			$disallowHandler = false;
	
// 			// $event->addResult(
// 			// 	new \Bitrix\Main\EventResult(
// 			// 		\Bitrix\Main\EventResult::SUCCESS, $order
// 			// 	)
// 			// );
// 		}
//     }
// );

//AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserRegisterHandler");
//AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");



use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'main',
    'OnAfterUserRegister',
    function (&$arFields) {

     
        $eventFields = [
            "USER_ID"    => $arFields["USER_ID"],
            "LOGIN"      => $arFields["LOGIN"],
            "EMAIL"      => $arFields["EMAIL"],
            "NAME"       => $arFields["NAME"],
            "LAST_NAME"  => $arFields["LAST_NAME"],
            "PASSWORD"   => $arFields["PASSWORD"],
        ];

        // Отправка почтового события NEW_USER
        CEvent::SendImmediate("NEW_USER", SITE_ID, $eventFields);


//        file_put_contents(
//            $_SERVER['DOCUMENT_ROOT'].'/local/logs/event.log',
//            date("Y-m-d H:i:s") . " NEW_USER отправлено для USER_ID=".$arFields['USER_ID']."\n",
//            FILE_APPEND
//        );
    }
);


