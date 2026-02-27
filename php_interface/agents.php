<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\StoreProductTable;

function AgentUpdateStoreQuantity()
{
   // file_put_contents(__DIR__ . '/log.txt', "Агент стартовал \n", FILE_APPEND);
    if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
        return __FUNCTION__ . '();';
    }

    $iblockId = 7;
    $storeId = 51;
    $propertyCode = 'VPUTI_ATTR_S';

    // Получаем все элементы ИБ
    $res = \CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_' . $propertyCode]
    );

    while ($item = $res->Fetch()) {
        $productId = (int)$item['ID'];
        $quantity = (float)$item['PROPERTY_' . $propertyCode . '_VALUE'];

        // проверяем, есть ли запись по складу
        $storeRow = StoreProductTable::getRow([
            'filter' => [
                '=PRODUCT_ID' => $productId,
                '=STORE_ID' => $storeId
            ]
        ]);

        if ($storeRow) {
            // обновляем
            StoreProductTable::update($storeRow['ID'], [
                'AMOUNT' => $quantity
            ]);
           // file_put_contents(__DIR__ . '/log.txt', "Обновление \n", FILE_APPEND);
        } else {
            // создаём новую запись
            StoreProductTable::add([
                'PRODUCT_ID' => $productId,
                'STORE_ID'   => $storeId,
                'AMOUNT'     => $quantity
            ]);
           // file_put_contents(__DIR__ . '/log.txt', "Создание \n", FILE_APPEND);
        }
    }

    // вернуть саму функцию, чтобы агент выполнялся снова
    return __FUNCTION__ . '();';
}
