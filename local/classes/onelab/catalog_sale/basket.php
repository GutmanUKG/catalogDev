<?php
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/function.php';

class BasketEvent
{
    public function addBasket($id, $name)
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
        \Bitrix\Main\Loader::includeModule("catalog");

        // Получаем базовую цену товара
        $ar_res = \CPrice::GetList([], ["PRODUCT_ID" => $id]);
        $myPricesa = null;
        while ($arPrices = $ar_res->Fetch()) {
            $myPricesa = $arPrices["PRICE"];
            $myPricesa = substr($myPricesa, 0, -3);
            $myPricesa = number_format($myPricesa, 0, '.', ' ');
        }

        // Получаем свойство COUNT (максимальное количество на складе)
        $arFilter = ["IBLOCK_ID" => 33, "ID" => $id, "ACTIVE" => "Y"];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, ["ID", "NAME", "PROPERTY_COUNT"]);
        $maxCount = null;
        if ($ob = $res->Fetch()) {
            $maxCount = (int)$ob['PROPERTY_COUNT_VALUE'];
        }

        // Если свойство COUNT не установлено, задаём его как бесконечное
        if ($maxCount === null || $maxCount <= 0) {
            $maxCount = PHP_INT_MAX;
        }

        // Инициализируем корзину как массив, если она не существует или не является массивом
        if (!isset($_SESSION['USER_BASKET'][$userId]) || !is_array($_SESSION['USER_BASKET'][$userId])) {
            $_SESSION['USER_BASKET'][$userId] = [];
        }

        // Поиск товара в корзине
        $itemFound = false;
        foreach ($_SESSION['USER_BASKET'][$userId] as &$basketItem) {
            if ($basketItem['ID'] == $id) {
                $itemFound = true;

                // Увеличиваем количество
                $currentQuantity = (int)$basketItem['QUANTITY'];
                $newQuantity = $currentQuantity + 1; // Прибавляем 1 к текущему количеству

                if ($newQuantity <= $maxCount) {
                    $basketItem['QUANTITY'] = $newQuantity;
                    $status = 'success';
                    $message = 'Количество товара увеличено';
                } else {
                    $basketItem['QUANTITY'] = $maxCount;
                    $status = 'error';
                    $message = 'Нельзя добавить больше товара, чем доступно на складе';
                }
                break;
            }
        }

        // Если товар не найден, добавляем его в корзину
        if (!$itemFound) {
            $_SESSION['USER_BASKET'][$userId][] = [
                'ID' => $id,
                'NAME' => $name,
                'PRICE' => $myPricesa,
                "QUANTITY" => 1 // Первоначальное количество для нового товара
            ];
            $status = 'success';
            $message = 'Товар успешно добавлен в корзину';
        }

        // Обновляем массив в сессии
        $_SESSION['USER_BASKET'][$userId] = array_values($_SESSION['USER_BASKET'][$userId]);

        echo json_encode([
            'status' => $status,
            'message' => $message,
            'basket' => $_SESSION['USER_BASKET'][$userId],
            'user' => $userId
        ]);
    }

    public function removeItemBasket($id)
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
            echo json_encode([
                'status' => 'error',
                'message' => 'Корзина пуста'
            ]);
            return;
        }

        // Перебираем корзину и удаляем товар с переданным ID
        $found = false;
        foreach ($_SESSION['USER_BASKET'][$userId] as $key => $basketItem) {
            if ($basketItem['ID'] == $id) {
                unset($_SESSION['USER_BASKET'][$userId][$key]); // Удаляем элемент
                $found = true;
                break;
            }
        }

        // Обновляем массив в сессии (пересоздаём индексированный массив)
        $_SESSION['USER_BASKET'][$userId] = array_values($_SESSION['USER_BASKET'][$userId]);

        // Возвращаем результат операции
        if ($found) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Товар удалён из корзины',
                'basket' => $_SESSION['USER_BASKET'][$userId]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Товар не найден в корзине'
            ]);
        }
    }

    public function updateQuantity($id, $num)
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
            echo json_encode([
                'status' => 'error',
                'message' => 'Корзина пуста'
            ]);
            return;
        }

        // Получаем максимальное количество товара из свойства COUNT
        \Bitrix\Main\Loader::includeModule("iblock");
        $arFilter = ["IBLOCK_ID" => 33, "ID" => $id, "ACTIVE" => "Y"];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, ["ID", "PROPERTY_COUNT"]);
        $maxCount = null;
        if ($element = $res->Fetch()) {
            $maxCount = (int)$element['PROPERTY_COUNT_VALUE'];
        }

        // Если свойство COUNT не установлено, задаём его как бесконечное
        if ($maxCount === null || $maxCount <= 0) {
            $maxCount = PHP_INT_MAX;
        }

        // Обновляем количество
        foreach ($_SESSION['USER_BASKET'][$userId] as &$basketItem) {
            if ($basketItem['ID'] == $id) {
                if ($num > $maxCount) {
                    $basketItem['QUANTITY'] = $maxCount;
                    $status = 'error';
                    $message = 'Количество товара превышает доступное на складе. Установлено максимально доступное количество.';
                } elseif ($num <= 0) {
//                    $basketItem['QUANTITY'] = 0;
//                    $status = 'error';
//                    $message = 'Количество товара не может быть меньше или равно нулю. Установлено 0.';
                    $this->removeItemBasket($id);
                    $status = 'success';
                    $message = 'Товар удалён из корзины.';
                } else {
                    $basketItem['QUANTITY'] = $num;
                    $status = 'success';
                    $message = 'Количество товара обновлено.';
                }

                // Завершаем цикл, так как товар найден
                echo json_encode([
                    'status' => $status,
                    'message' => $message,
                    'basket' => $_SESSION['USER_BASKET'][$userId]
                ]);
                return;
            }
        }

        // Если товар не найден
        echo json_encode([
            'status' => 'error',
            'message' => 'Товар не найден в корзине'
        ]);
    }

    public function createInfoOrder($userId, $basketItems)
    {
        if (!$userId || empty($basketItems)) {
            return [
                'status' => 'error',
                'message' => 'Не указан пользователь или корзина пуста'
            ];
        }

        \Bitrix\Main\Loader::includeModule("iblock");

        $el = new \CIBlockElement;

        // Получаем имя пользователя
        $user = \CUser::GetByID($userId)->Fetch();
        $userName = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
        $orderName = "Заказ от {$userName} / " . date('d.m.Y H:i');

        // Подготавливаем привязку элементов (товаров)
        $itemIds = [];
        foreach ($basketItems as $item) {
            $itemIds[] = $item['ID'];
        }

        // Подготавливаем данные для нового элемента
        $fields = [
            'IBLOCK_ID' => 35,
            'NAME' => $orderName,
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                'USER' => $userId, // Привязка к пользователю
                'ITEM' => $itemIds  // Множественная привязка к элементам
            ]
        ];

        // Добавляем элемент
        $elementId = $el->Add($fields);

        if ($elementId) {
            return [
                'status' => 'success',
                'message' => 'Информация о заказе успешно сохранена',
                'elementId' => $elementId
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Ошибка создания элемента: ' . $el->LAST_ERROR
            ];
        }
    }



    /*
    public function orderBasket()
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

        if (!isset($_SESSION['USER_BASKET'][$userId]) || empty($_SESSION['USER_BASKET'][$userId])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ваша корзина пуста'
            ]);
            return;
        }

        \Bitrix\Main\Loader::includeModule("iblock");
        \Bitrix\Main\Loader::includeModule("main");

        $basket = $_SESSION['USER_BASKET'][$userId];
        $orderItems = [];
        $totalSum = 0;

        foreach ($basket as $item) {
            $price = (float)str_replace(' ', '', $item['PRICE']);
            $quantity = (int)$item['QUANTITY'];
            $itemTotal = $price * $quantity;
            //Обновление остатка на складе
            $this->calcQuantityItem($item['ID'], $quantity);
            $orderItems[] = [
                'NAME' => $item['NAME'],
                'PRICE' => $price,
                'QUANTITY' => $quantity,
                'TOTAL' => $itemTotal,
                'ID' => $item['ID']
            ];
            $totalSum += $itemTotal;
        }

        // Получаем данные пользователя, включая UF_MANAGER
        $filter = ["ID" => $userId];
        $selectFields = ["ID", "NAME", "LAST_NAME", "EMAIL", "PERSONAL_PHONE", "UF_MANAGER"];
        $rsUsers = \CUser::GetList($by = "id", $order = "asc", $filter, ["SELECT" => $selectFields]);

        $userData = [];
        $managerEmail = null; // Для хранения email менеджера
        if ($arUser = $rsUsers->Fetch()) {
            $userData['FULL_NAME'] = trim($arUser['NAME'] . ' ' . $arUser['LAST_NAME']);
            $userData['EMAIL'] = $arUser['EMAIL'];

            // Проверяем PERSONAL_PHONE, если пусто — берём из UserPhoneAuthTable
            if (empty($arUser['PERSONAL_PHONE'])) {
                $phoneData = \Bitrix\Main\UserPhoneAuthTable::getList([
                    'filter' => ['=USER_ID' => $userId],
                    'select' => ['PHONE_NUMBER']
                ])->fetch();
                $userData['PHONE'] = $phoneData['PHONE_NUMBER'] ?? 'Не указан';
            } else {
                $userData['PHONE'] = $arUser['PERSONAL_PHONE'];
            }

            // Получаем email менеджера, если указан UF_MANAGER
            if (!empty($arUser['UF_MANAGER'])) {
                $managerId = $arUser['UF_MANAGER']; // ID привязанного элемента

                // Запрос в инфоблок 16 для получения email менеджера
                $manager = \CIBlockElement::GetList(
                    [],
                    ["IBLOCK_ID" => 16, "ID" => $managerId],
                    false,
                    false,
                    ["ID", "PROPERTY_EMAIL"]
                )->Fetch();

                if (!empty($manager['PROPERTY_EMAIL_VALUE'])) {
                    $managerEmail = $manager['PROPERTY_EMAIL_VALUE'];
                }
            }
        }

        // Подготовка строки с деталями заказа
        $orderDetails = "";
        foreach ($orderItems as $orderItem) {
            $orderDetails .= "Название: {$orderItem['NAME']}\n";
            $orderDetails .= "Цена: " . number_format($orderItem['PRICE'], 0, '.', ' ') . " KZT\n";
            $orderDetails .= "Количество: {$orderItem['QUANTITY']}\n";
            $orderDetails .= "Общая цена: " . number_format($orderItem['TOTAL'], 0, '.', ' ') . " KZT\n\n";
        }
        $orderDetails .= "Общая стоимость заказа: " . number_format($totalSum, 0, '.', ' ') . " KZT\n\n";

        // Добавляем информацию о пользователе
        $orderDetails .= "Информация о пользователе:\n";
        $orderDetails .= "Полное имя: {$userData['FULL_NAME']}\n";
        $orderDetails .= "Email: {$userData['EMAIL']}\n";
        $orderDetails .= "Телефон: {$userData['PHONE']}\n";

        // Формируем массив полей для почтового события
        $eventFields = [
            "ORDER_DETAILS" => $orderDetails,
            "TOTAL_SUM" => number_format($totalSum, 0, '.', ' ') . " KZT",
            "MANAGER_EMAIL" => $managerEmail,
            "USER_NAME" => $userData['FULL_NAME'],
            "USER_PHONE" => $userData['PHONE'],
            "USER_EMAIL" => $userData['EMAIL']
        ];

        // Логируем данные для отладки
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order_debug.log', print_r($eventFields, true), FILE_APPEND);

        // Отправляем почтовое событие
        \CEvent::Send("ORDER_SALE_BASKET", SITE_ID, $eventFields);

        // Создаём запись в инфоблоке
        $orderInfoResult = $this->createInfoOrder($userId, $orderItems);

        if ($orderInfoResult['status'] === 'success') {
            // Очищаем корзину
           unset($_SESSION['USER_BASKET'][$userId]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Ваш заказ успешно оформлен и отправлен на обработку',
                'user' => [
                    'FULL_NAME' => $userData['FULL_NAME'],
                    'EMAIL' => $userData['EMAIL'],
                    'PHONE' => $userData['PHONE']
                ],
                'elementId' => $orderInfoResult['elementId'],
                "MANAGER_EMAIL" => $managerEmail
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка создания информации о заказе: ' . $orderInfoResult['message']
            ]);
        }
    }
    */

    public function orderBasket()
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

        if (!isset($_SESSION['USER_BASKET'][$userId]) || empty($_SESSION['USER_BASKET'][$userId])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ваша корзина пуста'
            ]);
            return;
        }

        \Bitrix\Main\Loader::includeModule("iblock");
        \Bitrix\Main\Loader::includeModule("main");

        $basket = $_SESSION['USER_BASKET'][$userId];
        $orderItems = [];
        $totalSum = 0;

        foreach ($basket as $item) {
            $price = (float)str_replace(' ', '', $item['PRICE']);
            $quantity = (int)$item['QUANTITY'];
            $itemTotal = $price * $quantity;

            // Получение свойств элемента через ID инфоблока 33
            $res = \CIBlockElement::GetList(
                [],
                ["IBLOCK_ID" => 33, "ID" => $item['ID']],
                false,
                false,
                ["ID", "PROPERTY_ART", "PROPERTY_PART", "PROPERTY_KAT_CATEGORY"]
            );

            if ($element = $res->Fetch()) {
                $article = $element['PROPERTY_ART_VALUE'] ?? 'Не указан';
                $serial = $element['PROPERTY_PART_VALUE'] ?? 'не указан';
                $category = is_array($element['PROPERTY_KAT_CATEGORY_VALUE'])
                    ? implode(', ', $element['PROPERTY_KAT_CATEGORY_VALUE'])
                    : ($element['PROPERTY_KAT_CATEGORY_VALUE'] ?? 'Не указана');
            } else {
                $article = 'Не указан';
                $serial = 'не указан';
                $category = 'Не указана';
            }

            // Обновление остатка на складе
            $this->calcQuantityItem($item['ID'], $quantity);

            $orderItems[] = [
                'NAME' => $item['NAME'],
                'ARTICLE' => $article,
                'SERIAL' => $serial,
                'CATEGORY' => $category,
                'PRICE' => $price,
                'QUANTITY' => $quantity,
                'TOTAL' => $itemTotal,
                'ID' => $item['ID']
            ];
            $totalSum += $itemTotal;
        }

        // Получаем данные пользователя
        $filter = ["ID" => $userId];
        $selectFields = ["ID", "NAME", "LAST_NAME", "EMAIL", "PERSONAL_PHONE", "UF_MANAGER"];
        $rsUsers = \CUser::GetList($by = "id", $order = "asc", $filter, ["SELECT" => $selectFields]);

        $userData = [];
        $managerEmail = null;
        if ($arUser = $rsUsers->Fetch()) {
            $userData['COMPANY'] = 'ТОО «Администратор Главный»'; // Фиксированное значение
            $userData['FULL_NAME'] = trim($arUser['NAME'] . ' ' . $arUser['LAST_NAME']);
            $userData['EMAIL'] = $arUser['EMAIL'];
            $userData['PHONE'] = !empty($arUser['PERSONAL_PHONE']) ? $arUser['PERSONAL_PHONE'] : 'Не указан';

            if (!empty($arUser['UF_MANAGER'])) {
                $managerId = $arUser['UF_MANAGER'];
                $manager = \CIBlockElement::GetList(
                    [],
                    ["IBLOCK_ID" => 16, "ID" => $managerId],
                    false,
                    false,
                    ["ID", "PROPERTY_EMAIL"]
                )->Fetch();

                if (!empty($manager['PROPERTY_EMAIL_VALUE'])) {
                    $managerEmail = $manager['PROPERTY_EMAIL_VALUE'];
                }
            }
        }

        // Подготовка строки с деталями заказа по шаблону письма
        $orderDetails = "Информация о покупателе:\n";
        $orderDetails .= "Компания: {$userData['COMPANY']}\n";
        $orderDetails .= "ФИО: {$userData['FULL_NAME']}\n";
        $orderDetails .= "Email: {$userData['EMAIL']}\n";
        $orderDetails .= "Телефон: {$userData['PHONE']}\n\n";

        foreach ($orderItems as $orderItem) {
            $orderDetails .= "{$orderItem['NAME']}\n";
            $orderDetails .= "Артикул: {$orderItem['ARTICLE']}\n";
            $orderDetails .= "Серийный номер: {$orderItem['SERIAL']}\n";
            $orderDetails .= "Категория уценки: {$orderItem['CATEGORY']}\n";
            $orderDetails .= "Цена уценки: " . number_format($orderItem['PRICE'], 0, '.', ' ') . " KZT\n";
            $orderDetails .= "Количество: {$orderItem['QUANTITY']}\n";
            $orderDetails .= "Общая сумма: " . number_format($orderItem['TOTAL'], 0, '.', ' ') . " KZT\n\n";
        }
        $orderDetails .= "Итого: " . number_format($totalSum, 0, '.', ' ') . " KZT\n\n";

        $eventFields = [
            "ORDER_DETAILS" => $orderDetails,
            "TOTAL_SUM" => number_format($totalSum, 0, '.', ' ') . " KZT",
            "MANAGER_EMAIL" => $managerEmail,
            "USER_NAME" => $userData['FULL_NAME'],
            "USER_PHONE" => $userData['PHONE'],
            "USER_EMAIL" => $userData['EMAIL']
        ];

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/order_debug.log', print_r($eventFields, true), FILE_APPEND);

        \CEvent::Send("ORDER_SALE_BASKET", SITE_ID, $eventFields);

        $orderInfoResult = $this->createInfoOrder($userId, $orderItems);

        if ($orderInfoResult['status'] === 'success') {
            unset($_SESSION['USER_BASKET'][$userId]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Ваш заказ успешно оформлен и отправлен на обработку',
                'elementId' => $orderInfoResult['elementId'],
                "MANAGER_EMAIL" => $managerEmail
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка создания информации о заказе: ' . $orderInfoResult['message']
            ]);
        }
    }




    function calcQuantityItem($itemID, $quantity)
    {
        $IBLOCK_ID = 33;
        $arSelect = ["ID", "NAME", "IBLOCK_ID", "PROPERTY_COUNT"];
        $arFilter = ["IBLOCK_ID" => $IBLOCK_ID, "ID" => $itemID];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        if ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $currentCount = (int)$arFields['PROPERTY_COUNT_VALUE']; // Текущее количество

            // Проверяем, достаточно ли количества для вычитания
            if ($currentCount < $quantity) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Недостаточное количество на складе'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Вычисляем новое количество
            $newCount = $currentCount - $quantity;

            // Сохраняем новое значение в свойство элемента
            \CIBlockElement::SetPropertyValuesEx($itemID, $IBLOCK_ID, ["COUNT" => $newCount]);

            return true;
        } else {
           return false;
        }
    }



}



