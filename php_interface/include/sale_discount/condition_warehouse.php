<?php

use Bitrix\Main\Loader;
use Bitrix\Catalog\StoreTable;

/**
 * Кастомное условие для правил работы с корзиной:
 * "Товар из ИБ 37 привязан к определённому складу"
 *
 * Паттерн по https://o2k.ru/blog/usloviya-dlya-pravil-raboty-s-korzinoj
 */
class CCondCtrlProductOnWarehouse extends \CSaleActionCtrlAction
{
    protected static $cache = [];

    public static function GetClassName()
    {
        return __CLASS__;
    }

    public static function GetControlID()
    {
        return 'CondCtrlProductOnWarehouse';
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetAtomsEx();

        return array(
            'controlgroup' => true,
            'group' => false,
            'label' => 'Склад (ИБ 37)',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array(
                array(
                    'controlId' => static::GetControlID(),
                    'group' => false,
                    'label' => 'Товар из ИБ 37 привязан к складу',
                    'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                    'control' => array(
                        'Товар из ИБ 37 привязан к складу',
                        $arControls['STORE_ID'],
                    ),
                ),
            ),
        );
    }

    public static function GetAtomsEx($strControlID = false, $boolEx = false)
    {
        $boolEx = (true === $boolEx ? true : false);

        $storeList = array();
        if (Loader::includeModule('catalog')) {
            $res = StoreTable::getList(array(
                'filter' => array('ACTIVE' => 'Y'),
                'select' => array('ID', 'TITLE', 'ADDRESS'),
                'order' => array('SORT' => 'ASC', 'TITLE' => 'ASC'),
            ));
            while ($store = $res->fetch()) {
                $name = $store['TITLE'] ?: $store['ADDRESS'] ?: 'Склад #' . $store['ID'];
                $storeList[$store['ID']] = '[' . $store['ID'] . '] ' . $name;
            }
        }

        $arAtomList = array(
            'STORE_ID' => array(
                'JS' => array(
                    'id' => 'STORE_ID',
                    'name' => 'extra',
                    'type' => 'select',
                    'values' => $storeList,
                    'defaultText' => '-- Выберите склад --',
                    'defaultValue' => '',
                    'first_option' => '...',
                ),
                'ATOM' => array(
                    'ID' => 'STORE_ID',
                    'FIELD_TYPE' => 'string',
                    'FIELD_LENGTH' => 255,
                    'MULTIPLE' => 'N',
                    'VALIDATE' => 'list',
                ),
            ),
        );

        if (!$boolEx) {
            foreach ($arAtomList as &$arOneAtom) {
                $arOneAtom = $arOneAtom['JS'];
            }
        }

        return $arAtomList;
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $storeId = isset($arOneCondition['STORE_ID']) ? (int)$arOneCondition['STORE_ID'] : 0;
        if ($storeId <= 0) {
            return 'false';
        }

        return __CLASS__ . '::checkCondition($row, ' . $storeId . ')';
    }

    /**
     * Проверка условия в рантайме: товар из ИБ 37 и привязан к складу
     */
    public static function checkCondition($row, $storeId)
    {
        $productId = isset($row['PRODUCT_ID']) ? (int)$row['PRODUCT_ID'] : 0;
        if ($productId <= 0 || $storeId <= 0) {
            return false;
        }

        $basketId = static::extractBasketId($row);
        $rowKey = static::extractRowKey($row);
        $rowStoreId = static::extractRowStoreId($row);
        if ($rowStoreId <= 0 && $rowKey !== '') {
            $rowStoreId = static::extractStoreIdFromRowKey($rowKey);
        }
        if ($rowStoreId <= 0) {
            $rowStoreId = static::extractStoreIdFromProductXmlId($row);
        }
        if ($rowStoreId <= 0) {
            $rowStoreId = static::extractStoreIdByProductAndQuantity($row);
        }
        $cacheKey = $productId . '_' . $storeId . '_' . $basketId . '_' . $rowStoreId . '_' . $rowKey;
        if (isset(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        Loader::includeModule('iblock');
        Loader::includeModule('catalog');
        Loader::includeModule('sale');

        // 1. Проверяем что товар из ИБ 37
        $iblockId = static::getProductIblockId($productId);
        if ($iblockId != 37) {
            static::$cache[$cacheKey] = false;
            return false;
        }

        // 1.1. Если у строки корзины явно указан склад (через поле/props),
        // сверяем условие именно с ним. Это исключает ситуацию, когда
        // один и тот же PRODUCT_ID в нескольких складах попадает под первое
        // попавшееся правило скидки.
        if ($rowStoreId > 0) {
            $result = ($rowStoreId === (int)$storeId);
            static::$cache[$cacheKey] = $result;
            return $result;
        }

        // 2. Если склад строки так и не найден, условие для ИБ37 считаем несопоставимым.
        // Иначе срабатывает "первая подходящая" акция для товара, лежащего на двух складах.
        static::$cache[$cacheKey] = false;
        return false;
    }

    protected static function extractRowStoreId($row)
    {
        if (!is_array($row)) {
            return 0;
        }

        // Прямые поля строки корзины
        $directFields = array('MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID');
        foreach ($directFields as $field) {
            if (isset($row[$field]) && (int)$row[$field] > 0) {
                return (int)$row[$field];
            }
        }

        // Свойства корзины: разные форматы в зависимости от контекста выполнения
        if (!empty($row['PROPS']) && is_array($row['PROPS'])) {
            foreach ($row['PROPS'] as $prop) {
                if (!is_array($prop)) {
                    continue;
                }

                $code = '';
                if (!empty($prop['CODE'])) {
                    $code = (string)$prop['CODE'];
                } elseif (!empty($prop['NAME'])) {
                    $code = (string)$prop['NAME'];
                }

                if (in_array($code, array('MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'), true)) {
                    if (isset($prop['VALUE']) && (int)$prop['VALUE'] > 0) {
                        return (int)$prop['VALUE'];
                    }
                }
            }
        }

        $basketId = static::extractBasketId($row);
        if ($basketId > 0) {
            return static::extractStoreIdByBasketId($basketId);
        }

        return 0;
    }

    protected static function extractRowKey($row)
    {
        if (!is_array($row)) {
            return '';
        }

        if (!empty($row['MARKDOWN_ROW_KEY'])) {
            return (string)$row['MARKDOWN_ROW_KEY'];
        }

        if (!empty($row['PROPS']) && is_array($row['PROPS'])) {
            foreach ($row['PROPS'] as $prop) {
                if (!is_array($prop)) {
                    continue;
                }

                $code = '';
                if (!empty($prop['CODE'])) {
                    $code = (string)$prop['CODE'];
                } elseif (!empty($prop['NAME'])) {
                    $code = (string)$prop['NAME'];
                }

                if ($code === 'MARKDOWN_ROW_KEY' && isset($prop['VALUE']) && $prop['VALUE'] !== '') {
                    return (string)$prop['VALUE'];
                }
            }
        }

        $basketId = static::extractBasketId($row);
        if ($basketId > 0) {
            return static::extractRowKeyByBasketId($basketId);
        }

        return '';
    }

    protected static function extractBasketId($row)
    {
        if (!is_array($row)) {
            return 0;
        }

        foreach (array('BASKET_ID', 'ID', 'BASKET_CODE') as $field) {
            if (!empty($row[$field]) && (int)$row[$field] > 0) {
                return (int)$row[$field];
            }
        }

        return 0;
    }

    protected static function extractStoreIdByBasketId($basketId)
    {
        if ($basketId <= 0 || !Loader::includeModule('sale')) {
            return 0;
        }

        $res = \Bitrix\Sale\Internals\BasketPropertyTable::getList(array(
            'filter' => array('=BASKET_ID' => $basketId, '@CODE' => array('MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID')),
            'select' => array('CODE', 'VALUE'),
            'order' => array('ID' => 'ASC'),
        ));

        while ($prop = $res->fetch()) {
            if ((int)$prop['VALUE'] > 0) {
                return (int)$prop['VALUE'];
            }
        }

        return 0;
    }

    protected static function extractRowKeyByBasketId($basketId)
    {
        if ($basketId <= 0 || !Loader::includeModule('sale')) {
            return '';
        }

        $res = \Bitrix\Sale\Internals\BasketPropertyTable::getList(array(
            'filter' => array('=BASKET_ID' => $basketId, '=CODE' => 'MARKDOWN_ROW_KEY'),
            'select' => array('VALUE'),
            'order' => array('ID' => 'ASC'),
            'limit' => 1,
        ));
        if ($prop = $res->fetch()) {
            return (string)$prop['VALUE'];
        }

        return '';
    }

    protected static function extractStoreIdFromRowKey($rowKey)
    {
        if (!is_string($rowKey) || $rowKey === '') {
            return 0;
        }

        if (preg_match('/_(\d+)$/', $rowKey, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }

    protected static function extractStoreIdFromProductXmlId($row)
    {
        if (!is_array($row)) {
            return 0;
        }

        $xmlId = '';
        if (!empty($row['PRODUCT_XML_ID'])) {
            $xmlId = (string)$row['PRODUCT_XML_ID'];
        } elseif (!empty($row['XML_ID'])) {
            $xmlId = (string)$row['XML_ID'];
        }

        if ($xmlId !== '' && preg_match('/_(\d+)$/', $xmlId, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }

    protected static function extractStoreIdByProductAndQuantity($row)
    {
        if (!is_array($row) || !Loader::includeModule('sale')) {
            return 0;
        }

        $productId = isset($row['PRODUCT_ID']) ? (int)$row['PRODUCT_ID'] : 0;
        $quantity = isset($row['QUANTITY']) ? (float)$row['QUANTITY'] : 0.0;
        if ($productId <= 0 || $quantity <= 0) {
            return 0;
        }

        static $storeByProductQty = null;
        if ($storeByProductQty === null) {
            $storeByProductQty = [];
            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                \Bitrix\Sale\Fuser::getId(),
                \Bitrix\Main\Context::getCurrent()->getSite()
            );

            foreach ($basket as $basketItem) {
                $basketProductId = (int)$basketItem->getProductId();
                if ($basketProductId <= 0) {
                    continue;
                }

                $basketQuantity = (float)$basketItem->getQuantity();
                $basketStoreId = static::extractStoreIdFromBasketItem($basketItem);
                if ($basketStoreId <= 0) {
                    continue;
                }

                $key = $basketProductId . '|' . static::normalizeQuantityKey($basketQuantity);
                if (!isset($storeByProductQty[$key])) {
                    $storeByProductQty[$key] = [];
                }
                $storeByProductQty[$key][$basketStoreId] = $basketStoreId;
            }
        }

        $lookupKey = $productId . '|' . static::normalizeQuantityKey($quantity);
        if (!isset($storeByProductQty[$lookupKey])) {
            return 0;
        }

        if (count($storeByProductQty[$lookupKey]) === 1) {
            return (int)reset($storeByProductQty[$lookupKey]);
        }

        return 0;
    }

    protected static function normalizeQuantityKey($quantity)
    {
        return number_format((float)$quantity, 4, '.', '');
    }

    protected static function extractStoreIdFromBasketItem(\Bitrix\Sale\BasketItem $basketItem)
    {
        $directFields = array('MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID');
        foreach ($directFields as $field) {
            $value = $basketItem->getField($field);
            if ((int)$value > 0) {
                return (int)$value;
            }
        }

        $propertyCollection = $basketItem->getPropertyCollection();
        if (!$propertyCollection) {
            return 0;
        }
        $properties = $propertyCollection->getPropertyValues();
        if (!is_array($properties)) {
            return 0;
        }

        foreach ($properties as $prop) {
            if (!is_array($prop)) {
                continue;
            }

            $code = '';
            if (!empty($prop['CODE'])) {
                $code = (string)$prop['CODE'];
            } elseif (!empty($prop['NAME'])) {
                $code = (string)$prop['NAME'];
            }

            if (in_array($code, array('MARKDOWN_STORE_ID', 'STORE_ID', 'CATALOG_STORE_ID'), true)) {
                if (isset($prop['VALUE']) && (int)$prop['VALUE'] > 0) {
                    return (int)$prop['VALUE'];
                }
            }
        }

        return 0;
    }

    protected static function getProductIblockId($productId)
    {
        $product = \CCatalogSku::GetProductInfo($productId);
        if (!empty($product)) {
            $res = \CIBlockElement::GetList(
                array(),
                array('ID' => $product['ID'], 'ACTIVE' => 'Y'),
                false,
                false,
                array('IBLOCK_ID')
            );
        } else {
            $res = \CIBlockElement::GetList(
                array(),
                array('ID' => $productId, 'ACTIVE' => 'Y'),
                false,
                false,
                array('IBLOCK_ID')
            );
        }

        if ($item = $res->Fetch()) {
            return (int)$item['IBLOCK_ID'];
        }
        return 0;
    }
}
