<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
    die();
}

use Bitrix\Catalog\PriceTable;
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/function.php");
function saveDataToFile($arResult, $componentPath) {
    // Указываем путь к файлу в папке с компонентом
    $filePath = $componentPath . '/saved_data.json';

    // Преобразуем массив $arResult в JSON-формат
    $jsonData = json_encode($arResult, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Пишем данные в файл
    if (file_put_contents($filePath, $jsonData)) {
        return "Данные успешно сохранены в $filePath";
    } else {
        return "Ошибка при сохранении данных";
    }
}

// Пример вызова функции
$componentPath = __DIR__; // Путь к текущей директории компонента
//echo saveDataToFile($arResult, $componentPath);
$count = 0;
function getRetailPrice($productId) {
    $retailPriceGroupId = 1; // ID группы цен для розничной цены
    $priceData = CPrice::GetListEx(
        [],
        [
            "PRODUCT_ID" => $productId,
            "CATALOG_GROUP_ID" => $retailPriceGroupId
        ]
    )->Fetch();

    if ($priceData && $priceData["PRICE"] > 0) {
        return [
            "PRICE" => $priceData["PRICE"],
            "CURRENCY" => $priceData["CURRENCY"]
        ];
    }

    return null; // Если цена не найдена
}

function filterAndGroupItemsByArt(&$arResult,$query) {
    $groupedItems = [];

    foreach ($arResult['ITEMS'] as &$item) { // Используем ссылку, чтобы сразу обновить элементы
        //Скрытие элементов для покупателей с 0 остатком
        if(!check_user() && ($item['PROPERTIES']['COUNT']['VALUE'] < 1 || empty($item['PROPERTIES']['COUNT']['VALUE'])))
        {
            continue;
        }
        // Добавляем розничную цену к элементу
        $item['PRICE'] = getRetailPrice($item['ID']);

        // Проверяем, существует ли свойство ART и не пустое ли оно
        if (!empty($item['PROPERTIES']['ART']['VALUE'])) {
            $artValue = preg_replace("/\s+/","",$item['PROPERTIES']['ART']['VALUE']);
            $artSingle = $item['PROPERTIES']['SINGLE']['VALUE'];

            // Если элемент с SINGLE == "Y", он остается отдельным элементом
            if ($artSingle == 'Y') {
                $groupedItems[] = $item;
                continue;
            }

            // Если элемент с таким ART уже существует, добавляем его в CHILD_ITEMS
            if (isset($groupedItems[$artValue])) {
                $groupedItems[$artValue]['CHILD_ITEMS'][] = $item;
            } else {
                // Создаем новый элемент с полем CHILD_ITEMS
                $groupedItems[$artValue] = $item;
                $groupedItems[$artValue]['CHILD_ITEMS'] = [];
            }
        } else {
            // Если ART пустой, добавляем элемент как отдельный, чтобы не потерять данные
            $groupedItems[] = $item;
        }
    }

    // Преобразуем массив в индексированный, чтобы использовать `usort`
    $groupedItems = array_values($groupedItems);




    if ($query) {
        $groupedItems = array_filter($groupedItems, function ($item) use ($query) {
            $nameMatches = stripos(mb_strtolower($item['NAME']), mb_strtolower($query)) !== false;
            $artMatches = !empty(mb_strtolower($item['PROPERTIES']['ART']['VALUE'])) &&
                stripos(mb_strtolower($item['PROPERTIES']['ART']['VALUE']), mb_strtolower($query)) !== false;
            $arMatchesSN = !empty(mb_strtolower($item['PROPERTIES']['PART']['VALUE'])) &&
                stripos(mb_strtolower($item['PROPERTIES']['PART']['VALUE']), mb_strtolower($query)) !== false;


            return $nameMatches || $artMatches || $arMatchesSN;
        });

        // После фильтрации приводим массив к индексированному виду
        $groupedItems = array_values($groupedItems);
    }

    // Заменяем исходный массив $arResult на отфильтрованный и отсортированный массив
    $arResult['ITEMS'] = $groupedItems;

    // Сортировка массива в алфавитном порядке по `NAME`
//    usort($groupedItems, function ($a, $b) {
//        return strcmp($a['NAME'], $b['NAME']);
//    });
    $arResult['ITEMS'] = $groupedItems;
        usort($arResult['ITEMS'], function ($a, $b) {
        return strcmp($a['NAME'], $b['NAME']);
    });
}

if(!empty($_GET['q'])){
    $query = trim($_GET['q']);
}

// Вызов функции для обработки данных в $arResult
filterAndGroupItemsByArt($arResult, $query);






