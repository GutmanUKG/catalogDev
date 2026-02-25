<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Catalog;

Loader::includeModule("catalog");

if (!Loader::includeModule('catalog')) {
    return;
}

class StoreQuantityComponent extends CBitrixComponent implements Controllerable
{
    private $productId;

    protected function listKeysSignedParameters()
    {
        return [
            'CACHE_TIME',
            'ELEMENT_ID',
            'MESS_SHOW_MAX_QUANTITY',
            'SHOW_MAX_QUANTITY',
            'USE_STORE',
            'COMPONENT_TEMPLATE',
            'STORES',
            'STORE_FIELDS',
            'STORE_PROPERTIES'
        ];
    }

    public function configureActions()
    {
        return [
            'updateQuantity' => [
                'prefilters' => [],
                'postfilters' => []
            ],
            'updateStorData' => [
                'prefilters' => [],
                'postfilters' => []
            ],
            'updateStoreInfo' => [
                'prefilters' => [],
                'postfilters' => []
            ]
        ];
    }

    public function updateQuantityAction()
    {
        return ['stores' => $this->getResultFields()];
    }

    public function updateStoreDataAction()
    {
        return $this->getStoresQuantity();
    }

    public function updateStoreInfoAction()
    {
        return ['stores' => $this->getStoresInfo()];
    }

    public function onPrepareComponentParams($arParams)
    {
        if (!isset($arParams['CACHE_TIME']))
            $arParams['CACHE_TIME'] = 360000;

        $arParams["CACHE_TIME"] = IntVal($arParams["CACHE_TIME"]);

        if (!empty($arParams['ELEMENT_ID']) && is_numeric($arParams['ELEMENT_ID'])) {
            $this->productId = $arParams['ELEMENT_ID'];
        }

        if (!is_array($arParams["STORES"])) {
            $arParams["STORES"] = array();
        }

        if (!is_array($arParams["STORE_FIELDS"])) {
            $arParams["STORE_FIELDS"] = array();
        }

        if (!is_array($arParams["STORE_PROPERTIES"])) {
            $arParams["STORE_PROPERTIES"] = array();
        }
        if (in_array('COORDINATES', $arParams['STORE_FIELDS'])) {
            unset($arParams['STORE_FIELDS']['COORDINATES']);
            $arParams['STORE_FIELDS'] = array_merge($arParams['STORE_FIELDS'], array('GPS_N', 'GPS_S'));
        }

        $arParams["SHOW_EMPTY_STORE"] = $arParams["SHOW_EMPTY_STORE"] ?: "N";

        return $arParams;
    }

    public function onPrepareComponentFields()
    {
        if ($this->arParams['QUANTITY_TRACE']) {
            $this->arResult['QUANTITY_TRACE'] = $this->arParams['QUANTITY_TRACE'];
        } else {
            $arSelectElement[] = 'QUANTITY_TRACE';
        }
        if ($this->arParams['CAN_BUY_ZERO']) {
            $this->arResult['CAN_BUY_ZERO'] = $this->arParams['CAN_BUY_ZERO'];
        } else {
            $arSelectElement[] = 'CAN_BUY_ZERO';
        }
        if ($this->arParams['SUBSCRIBE']) {
            $this->arResult['SUBSCRIBE'] = $this->arParams['SUBSCRIBE'];
        } else {
            $arSelectElement[] = 'SUBSCRIBE';
        }
        if ($this->arParams['TYPE']) {
            $this->arResult['TYPE'] = $this->arParams['TYPE'];
        } else {
            $arSelectElement[] = 'TYPE';
        }
        if ($this->arParams['USE_STORE'] != 'Y') {
            if ($this->arParams['QUANTITY']) {
                $this->arResult['QUANTITY'] = $this->arParams['QUANTITY'];
            } else {
                $arSelectElement[] = 'QUANTITY';
            }
        }
        return $arSelectElement;
    }

    private function getResultFields()
    {
        $arSelectElement = $this->onPrepareComponentFields();
        $arResultFields = array();
        if (count($arSelectElement) > 0) {
            $arProd = CCatalogProduct::GetList(
                array(),
                array('ID' => $this->arParams['ELEMENT_ID']),
                false,
                false,
                $arSelectElement
            );
            if ($obProd = $arProd->Fetch()) {
                $arResultFields = $obProd;
            }
        }
        return $arResultFields;
    }

    private function GetStoreFields($arFilter = array(), $arSelect = array())
    {
        $arStoreFields = array();
        $storeSelect = array_merge(array('ID'), $arSelect);
        $rsStores = CCatalogStore::GetList(
            array(),
            $arFilter,
            false,
            false,
            $storeSelect
        );

        while ($obStore = $rsStores->Fetch()) {
            foreach ($obStore as $storeKey => $itemStore) {
                $arStoreFields[$obStore['ID']][$storeKey] = unserialize($itemStore) ?: $itemStore;
                if ($storeKey == 'IMAGE_ID') {
                    $arStoreFields[$obStore['ID']]['IMAGE'] = CFile::GetPath($itemStore);
                }
            }
        }
        getMessage('STORE_ENTITY_GPS_S_FIELD');
        return $arStoreFields;
    }

    private function getStoresQuantity()
    {
        $storeQuantity = array();
        $filter = ["PRODUCT_ID" => $this->arParams['ELEMENT_ID'], "ID" => $this->arParams['STORES']];
        if ($this->arParams["SHOW_EMPTY_STORE"] == "N") {
            $filter["!PRODUCT_AMOUNT"] = false;
        }

        $storeFields = $this->GetStoreFields(
            $filter,
            array('PRODUCT_AMOUNT')
        );
        foreach ($storeFields as $storeKey => $itemStore) {
                $storeQuantity['STORE_AMOUNT'][$storeKey] = !empty($itemStore['PRODUCT_AMOUNT'])?$itemStore['PRODUCT_AMOUNT']:0;
                $storeQuantity['QUANTITY'] += !empty($itemStore['PRODUCT_AMOUNT'])?$itemStore['PRODUCT_AMOUNT']:0;
        }
        if (empty($storeQuantity)) {
            $storeQuantity['STORE_AMOUNT'] = [];
            $storeQuantity['QUANTITY'] = 0;
        }
        
        return $storeQuantity;
    }

    private function getStoresInfo()
    {
        $cache = new CPHPCache;
        $cacheId = md5(serialize($this->arParams['STORES']));
        if ($cache->InitCache($this->arParams['CACHE_TIME'], $cacheId, "bitrix/components" . $this->GetRelativePath())) {
            $vars = $cache->GetVars();
            $arStores['STORES'] = $vars['STORES'];
        } elseif ($cache->StartDataCache()) {

            $storeTitleFields = array();
            $storeMap = catalog\storetable::getmap();
            foreach ($storeMap as $storeMapKey => $obStoreField) {
                $storeTitleFields[$storeMapKey] = $obStoreField->getParameter('title');

            }

            $arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields('CAT_STORE', $this->arParams['STORES'], LANGUAGE_ID);

            $arSelectStoreFields = array_merge(
                $this->arParams["STORE_FIELDS"],
                $this->arParams["STORE_PROPERTIES"]
            );

            $arStoresFields = $this->GetStoreFields(
                array('ID' => $this->arParams['STORES']),
                $arSelectStoreFields
            );
            foreach ($arStoresFields as $storeKey => $storeItem) {
                foreach ($storeItem as $storeFieldKey => $storeField) {
                    if ($storeField) {
                        if ($storeTitleFields[$storeFieldKey]) {
                            $arStores['STORES'][$storeKey][$storeFieldKey]['TITLE'] = $storeTitleFields[$storeFieldKey];
                        } elseif ($arUserFields[$storeFieldKey]) {
                            $arStores['STORES'][$storeKey][$storeFieldKey]['TITLE'] = $arUserFields[$storeFieldKey]['EDIT_FORM_LABEL'];
                        } else {
                            $arStores['STORES'][$storeKey][$storeFieldKey]['TITLE'] = $storeFieldKey;
                        }
                        $arStores['STORES'][$storeKey][$storeFieldKey]['VALUE'] = $storeField;
                    }
                }
            }
            $cache->EndDataCache(array('STORES' => $arStores['STORES']));
        }
        return $arStores;
    }

    public function executeComponent()
    {
        if ($this->productId) {
            $this->arResult += $this->getResultFields();
            if ($this->arResult['TYPE'] !== 2 || $this->arResult['TYPE'] !== 3) {
                if ($this->arParams['USE_STORE'] == 'Y' && !empty($this->arParams["STORES"])) {
                    $this->arResult += $this->getStoresQuantity();
                    $this->arResult += $this->getStoresInfo()?:[];
                }
            }
            $this->includeComponentTemplate();
        }
        return $this->arResult['QUANTITY'];
    }
}
