<?php
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/events.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/basket.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/export.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/import.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/function.php");
$events =  new EventsCatalog; // Работа с элементами каталога
$events_basket =  new BasketEvent; // События корзины
$events_export =  new exportCatalog; // События экспорта
$events_import =  new importCatalog; // События экспорта
//Обновление товара
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "UPDATE"){
    $events-> RenderForm($_POST['ID'], $_POST['NAME'], $_POST);
}
//Копирование элемента
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "COPY_ITEM"){
    $events-> copyElement($_POST['ID']);
}

//Создание нового товара
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "SHOW_ADD_FROM"){
  $events-> RenderForm('', '',[]);
}
//Удаление фото
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "REMOVE_PHOTO"){
    $events-> removePhotoDestroy($_POST['ID'], $_POST['PHOTO_ID']);
}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "ADD_PHOTO"){
    $arrFileds = array_merge($_POST, $_FILES);
    $events-> addPhotoDestroy($arrFileds);
}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "ADD_ELEMENT"){
    $arrFileds = array_merge($_POST, normalizeFilesArray($_FILES));
    $events->CreateElement($arrFileds);

}

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "DELETE_ELEMENT"){
    $events->deleteElement($_POST['ID']);

}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "SAVE_UPDATE"){
    $arrFileds = array_merge($_POST, normalizeFilesArray($_FILES)); // Объединение $_POST и $_FILES для передачи файлов
    $events->UpdateElement($arrFileds);

}

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "UPDATE_ITEM"){
    $events-> UpdateElement($_POST['id_element'], $_POST);
}


if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "ADD_BASKET"){
    $events_basket-> addBasket($_POST['ID'], $_POST['NAME'], $_POST['QUANTITY']);
}


if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "SHOW_ITEM"){
    $events-> showItem($_POST['ID']);
}


if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "SHOW_ITEM_PHOTO"){
    $events-> ShowPhoto($_POST['ID']);
}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "DELETE_IMG"){
    $events-> DeleteImage($_POST);
}

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "UPDATE_QUANTITY"){
    $events_basket-> updateQuantity($_POST['ID'], $_POST['NUM']);
}

if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "REMOVE_BASKET"){
    $events_basket-> removeItemBasket($_POST['ID']);
}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "FORM_IMPORT"){

    $events_import-> showFormImport();
}
if(!empty($_POST['ACTION']) && $_POST['ACTION'] == "IMPORT_FILE"){
    $arrFileds = array_merge($_POST, normalizeFilesArray($_FILES));
    $events_import -> catalogImportExel($arrFileds);
}


if ($_POST['ACTION'] === 'EXPORT') {
    $filterParams = json_decode($_POST['FILTER'], true);
    if (!\Bitrix\Main\Loader::includeModule('iblock') || !\Bitrix\Main\Loader::includeModule('catalog')) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Не удалось подключить модули'
        ]);
        exit;
    }

    $arFilter = [
        'IBLOCK_ID' => 33, // Укажите ваш ID инфоблока
        'ACTIVE' => 'Y',
    ];
    //file_put_contents(__DIR__ . '/log.txt', print_r($filterParams, true), FILE_APPEND);
    $arFilter = array_merge($arFilter, $filterParams);

    $arSelect = [
        'ID',
        'NAME',
        'PROPERTY_BRAND',
        'PROPERTY_ART',
        'PROPERTY_PART',
        'PROPERTY_KAT_CATEGORY',
        'PROPERTY_RRP',
        'PROPERTY_STATUS',
        'PROPERTY_COUNT',
        'PROPERTY_DISTROY_TYPE'
    ];

    $arItems = [];
    $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($item = $res->GetNext()) {
        // Получаем цену товара
        $priceRes = \CPrice::GetList([], ['PRODUCT_ID' => $item['ID']]);
        if ($price = $priceRes->Fetch()) {
            $item['PRICE'] = $price['PRICE']; // Добавляем цену
            $item['CURRENCY'] = $price['CURRENCY']; // Добавляем валюту
        } else {
            $item['PRICE'] = null; // Если цена не найдена
            $item['CURRENCY'] = null;
        }

        $arItems[] = $item;
    }

    if (!empty($arItems)) {
        $events_export->exportXML($arItems);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Нет элементов для экспорта'
        ]);
    }

    exit;
}



