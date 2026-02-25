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

        $cacheKey = $productId . '_' . $storeId;
        if (isset(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        // 1. Проверяем что товар из ИБ 37
        $iblockId = static::getProductIblockId($productId);
        if ($iblockId != 37) {
            static::$cache[$cacheKey] = false;
            return false;
        }

        // 2. Проверяем привязку к складу
        $res = \CCatalogStoreProduct::GetList(
            array(),
            array('PRODUCT_ID' => $productId, 'STORE_ID' => $storeId),
            false,
            false,
            array('ID')
        );

        $result = ($res && $res->Fetch() !== false);
        static::$cache[$cacheKey] = $result;
        return $result;
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
