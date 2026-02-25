<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

function cleanHtmlEntities($text) {
    // Декодируем сначала стандартные HTML-сущности
    $decodedText = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

    // Удаляем возможные остатки кодировки типа &amp;quot;
    $cleanedText = preg_replace('/&(?:amp;)+([^;]+);/', '&\1;', $decodedText);

    // Повторно декодируем, если остались сущности
    return html_entity_decode($cleanedText, ENT_QUOTES | ENT_HTML5);
}
function normalizeFilesArray($files)
{
    $normalized = [];
    foreach ($files as $fieldName => $fileInfo) {
        if (is_array($fileInfo['name'])) {
            foreach ($fileInfo['name'] as $index => $name) {
                $normalized[$fieldName][$index] = [
                    'name' => $fileInfo['name'][$index],
                    'type' => $fileInfo['type'][$index],
                    'tmp_name' => $fileInfo['tmp_name'][$index],
                    'error' => $fileInfo['error'][$index],
                    'size' => $fileInfo['size'][$index],
                ];
            }
        } else {
            $normalized[$fieldName] = [$fileInfo];
        }
    }
    return $normalized;
}

function itemQuantityInBasket($id)
{
    global $USER;

    // Проверка авторизации пользователя
    if (!$USER->IsAuthorized()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Пользователь не авторизован'
        ]);
        return 0;
    }

    $userId = $USER->GetID();

    // Проверяем, существует ли корзина и является ли она массивом
    if (!isset($_SESSION['USER_BASKET'][$userId]) || !is_array($_SESSION['USER_BASKET'][$userId])) {
        return 0; // Если корзина пуста или не существует, возвращаем 0
    }

    // Ищем товар в корзине
    foreach ($_SESSION['USER_BASKET'][$userId] as $basketItem) {
        if ($basketItem['ID'] == $id) {
            return (int)$basketItem['QUANTITY']; // Возвращаем количество товара
        }
    }

    return 0; // Если товар не найден, возвращаем 0
}


function itemQuantity($id)
{
    $arFilter = ["IBLOCK_ID" => 33, "ID" => $id, "ACTIVE" => "Y"];
    $res = \CIBlockElement::GetList([], $arFilter, false, false, ["ID", "NAME", "PROPERTY_COUNT"]);

    if ($element = $res->Fetch()) {
        return (int)$element['PROPERTY_COUNT_VALUE']; // Возвращаем значение как целое число
    }

    return 0; // Если элемент не найден, возвращаем 0
}

function counterBasketItems()
{
    global $USER;

    if (!$USER->IsAuthorized()) {
        return 0; // Если пользователь не авторизован, возвращаем 0
    }

    $userId = $USER->GetID();

    // Проверяем, существует ли корзина и является ли она массивом
    if (!isset($_SESSION['USER_BASKET'][$userId]) || !is_array($_SESSION['USER_BASKET'][$userId])) {
        return 0; // Если корзина пуста, возвращаем 0
    }

    // Подсчёт общего количества товаров
    $totalItems = 0;
    foreach ($_SESSION['USER_BASKET'][$userId] as $basketItem) {
        $totalItems += (int)$basketItem['QUANTITY']; // Суммируем количество каждого товара
    }

    return $totalItems;
}

function totalSumInBasket()
{
    global $USER;

    if (!$USER->IsAuthorized()) {
        return 0; // Если пользователь не авторизован, возвращаем 0
    }

    $userId = $USER->GetID();

    // Проверяем, существует ли корзина и является ли она массивом
    if (!isset($_SESSION['USER_BASKET'][$userId]) || !is_array($_SESSION['USER_BASKET'][$userId])) {
        return 0; // Если корзина пуста, возвращаем 0
    }

    // Подсчёт общей стоимости
    $totalSum = 0;
    foreach ($_SESSION['USER_BASKET'][$userId] as $basketItem) {
        $quantity = (int)$basketItem['QUANTITY'];
        $price = (float)str_replace(' ', '', $basketItem['PRICE']); // Убираем пробелы и приводим к числу
        $totalSum += $quantity * $price; // Умножаем количество на цену
    }

    return formatPrice($totalSum);
}

function checkItemInBasket($id)
{
    global $USER;

    if (!$USER->IsAuthorized()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Пользователь не авторизован'
        ]);
        return;
    }

    $userId = $USER->GetID();

    // Проверяем, существует ли корзина и является ли она массивом
    if (!isset($_SESSION['USER_BASKET'][$userId]) || !is_array($_SESSION['USER_BASKET'][$userId])) {
        return false;
    }

    // Ищем массив с переданным ID
    foreach ($_SESSION['USER_BASKET'][$userId] as $basketItem) {
        if ($basketItem['ID'] == $id) {
            return true; // Возвращаем найденный массив
        }
    }

    return false; // Если элемент не найден
}

//Пользователь с правами на покупку
function check_user_sale()
{
    global $USER;
    if($USER->IsAuthorized()){
        $arUserGroups = $USER->GetUserGroupArray();
        if (in_array(10, $arUserGroups) && $_SERVER['SERVER_NAME'] != 'ak-cent.kz') { // где 19 — это ID группы Менеджер уценки
            return true;
        }
    }
}


function check_user()
{
    global $USER;
    if (auth_user() &&  check_user_group()){
        return true;
    }
}

function auth_user()
{
    global $USER;
    if($USER->IsAuthorized()){
        return true;
    }
}

function check_user_group()
{
    global $USER;
    if(auth_user()){
        $arUserGroups = $USER->GetUserGroupArray();
        if (in_array(19, $arUserGroups)) { // где 19 — это ID группы Менеджер уценки
           return true;
        }
        return false;
    }
}

function formatPrice($price, $currency = '₸')
{
    // Убедимся, что цена — это число
    $price = preg_replace("/\s+/", "" ,$price);
    $price = (float)$price;

    // Форматируем цену с разделителями тысяч
    $formattedPrice = number_format($price, 0, '.', ' ');

    // Добавляем валюту
    return $formattedPrice . ' ' . $currency;
}

function d($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}