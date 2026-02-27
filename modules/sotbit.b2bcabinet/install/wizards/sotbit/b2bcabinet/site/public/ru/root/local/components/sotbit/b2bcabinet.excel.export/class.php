<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sotbit.b2bcabinet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment, NumberFormat};
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\Error,
    Bitrix\Main\ErrorCollection,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Data\Cache,
    Bitrix\Main\Text\Encoding,
    Bitrix\Main\Web\Json,
    Bitrix\Main\IO\Path,
    Sotbit\B2bCabinet\Helper\Config;

Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');
Loader::includeModule('catalog');

class B2BExcelExport extends Bitrix\Iblock\Component\ElementList implements Controllerable, \Bitrix\Main\Errorable
{

    protected $alphabet = [
        0 => 'A',
        1 => 'B',
        2 => 'C',
        3 => 'D',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'H',
        8 => 'I',
        9 => 'J',
        10 => 'K',
        11 => 'L',
        12 => 'M',
        13 => 'N',
        14 => 'O',
        15 => 'P',
        16 => 'Q',
        17 => 'R',
        18 => 'S',
        19 => 'T',
        20 => 'U',
        21 => 'V',
        22 => 'W',
        23 => 'X',
        24 => 'Y',
        25 => 'Z',
    ];
    protected $errorCollection;
    private $arHeader = [];
    private $arFieldsTypeImg = [
        'DETAIL_PICTURE',
        'PREVIEW_PICTURE',
    ];
    private $arFieldsPriceCode;
    private $arCheckModules = [
        'sale',
        'catalog',
        'iblock'
    ];
    private $idProductsForFilter = [];
    private $selectProps = [];
    private $offersSelectProps = [];
    private $selectPrice = [];
    private $arProducts;
    private $order = [];

    public function configureActions()
    {
        return [
            'exportExcel' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }

    public function listKeysSignedParameters()
    {
        return [
            'IBLOCK_ID',
            'MODEL_OF_WORK',
            'PRICE_CODE',
            'HEADERS_COLUMN',
            'PROPERTY_CODE',
            'OFFERS_PROPERTY_CODE',
            'SORT_ORDER',
            'SORT_BY',
            'ONLY_AVAILABLE',
            'ONLY_ACTIVE',
            'FILTER_NAME',
            'SECTION_ID',
        ];
    }

    public function onPrepareComponentParams($params)
    {
        $params["CACHE_TIME"] = Config::get("CATALOG_FILE_STORAGE_TIME", SITE_ID) ?: 3600;
        $params["PRICE_CODE"] = array_diff($params["PRICE_CODE"], [""]);
        $params["HEADERS_COLUMN"] = array_diff($params["HEADERS_COLUMN"], [""]);
        $params["PROPERTY_CODE"] = array_diff($params["PROPERTY_CODE"], [""]);

        if (!empty($offersIblock = CCatalogSku::GetInfoByProductIBlock($params['IBLOCK_ID']))) {
            $params["OFFERS_PROPERTY_CODE"] = is_array($params["OFFERS_PROPERTY_CODE"])
                ? array_diff($params["OFFERS_PROPERTY_CODE"], [""])
                : null;
        } else {
            $params["OFFERS_PROPERTY_CODE"] = [];
        }

        if ($params["SORT_BY"] && $params["SORT_ORDER"]) {
            $this->order = [$params["SORT_BY"] => $params["SORT_ORDER"]];
        }
        $params["ONLY_ACTIVE"] = $params["ONLY_ACTIVE"] ?: "Y";
        $this->errorCollection = new ErrorCollection();
        $this->selectFields = [
            "ID",
            "NAME",
        ];
        return $params;
    }

    public function executeComponent()
    {
        if (!$this->checkModules()) {
            $this->arResult["ERROR_LIST"] = $this->errorCollection;
        }

        if (!$this->arParams["IBLOCK_ID"]) {
            $this->arResult["ERROR_LIST"][] = new Error(Loc::getMessage("B2B_EXCEL_EXPORT_ERROR_NO_IBLOCK_ID"));
        }

        if (empty($this->errorCollection->values)) {
            if ($this->arParams["MODEL_OF_WORK"] == "user_config") {
                $filterDataValues["iblockId"] = (int)$this->arParams["IBLOCK_ID"];
                $offers = CCatalogSku::GetInfoByProductIBlock((int)$this->arParams["IBLOCK_ID"]);
                if ($offers) {
                    $filterDataValues["offersIblockId"] = $offers["IBLOCK_ID"];
                }

                $this->arResult = [
                    "MODEL_OF_WORK" => "user_config",
                    "RIGHTS_DISPLAY_IBLOCK" => CIBlockRights::UserHasRightTo($this->arParams["IBLOCK_ID"],
                        $this->arParams["IBLOCK_ID"], "iblock_admin_display"),
                    "COND_TREE_DATA" => Json::encode($filterDataValues)
                ];
            }
        }

        $this->includeComponentTemplate();
    }

    protected function checkModules()
    {
        $result = true;
        $arrLib = get_loaded_extensions();
        if (!in_array('xmlwriter', $arrLib, true)) {
            $result = false;
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_EXPORT_ERROR_NO_XMLWRITER"));
        }

        foreach ($this->arCheckModules as $module) {
            if (!Loader::includeModule($module)) {
                $result = false;
                $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_EXPORT_ERROR_NO_MODULE",
                    ["#MODULE#" => $module]));
            }
        }

        return $result;
    }

    public function exportExcelAction($arFilter = [], $condTreeParams = [])
    {
        if (!$this->arParams["IBLOCK_ID"]) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_EXPORT_ERROR_NO_IBLOCK_ID"));
            return null;
        }

        if (!$this->checkModules()) {
            return null;
        }

        global $USER;

        if ($this->arParams["MODEL_OF_WORK"] == "user_config") {
            $condTree = new CCatalogCondTree();
            $success = $condTree->Init(
                BT_COND_MODE_DEFAULT,
                BT_COND_BUILD_CATALOG,
                $condTreeParams
            );
            if ($success) {
                $resultParse = $condTree->Parse($arFilter['data']["rule"]);
                $this->arParams["FILTER"] = $this->parseCondition($resultParse, $condTreeParams);
            } else {
                $this->arParams["FILTER"] = [];
            }
        } else {
            $this->arParams["FILTER"] = $arFilter;
        }

        $cache = Cache::createInstance();
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $cachePath = 'sotbit_b2b_cabinet';
        if ($cache->initCache($this->arParams["CACHE_TIME"],
            md5(serialize(array_merge($this->arParams, ["USER_GROUPS" => $USER->GetGroups()]))), $cachePath)) {
            $arResult = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $taggedCache->startTagCache($cachePath);
            $taggedCache->registerTag('iblock_id_' . $this->arParams["IBLOCK_ID"]);
            $offerIblock = CCatalogSku::GetInfoByProductIBlock((int)$this->arParams["IBLOCK_ID"]);
            if ($offerIblock) {
                $taggedCache->registerTag('iblock_id_' . $offerIblock["IBLOCK_ID"]);
            }
            $this->getProducts();
            $this->getOffers();
            $this->renderFile();
            $arResult = $this->saveFile();
            $taggedCache->endTagCache();
            $cache->endDataCache($arResult);
        }

        return $arResult;
    }

    private function getProducts()
    {
        $select = $this->getSelectFields();
        $filter = $this->getProductsFilter();

        $arPropsHL = $this->getPropertiesHL($this->arParams["PROPERTY_CODE"]);

        $dbProducts = CIblockElement::GetList(
            $this->order,
            $filter,
            false,
            false,
            $select
        );
        while ($arProduct = $dbProducts->Fetch()) {
            if ($arProduct["TYPE"] == 3) { //checking for the availability of offers
                $this->idProductsForFilter[] = $arProduct["ID"];
            }
            if ($arPropsHL) {
                foreach ($arPropsHL as $code => $hlProperty) {
                    if (isset($arProduct["PROPERTY_" . $code . "_VALUE"]) && !empty($arProduct["PROPERTY_" . $code . "_VALUE"])) {
                        $arProduct["PROPERTY_" . $code . "_VALUE"] = $this->getPropertyValueFromHL($arProduct["PROPERTY_" . $code . "_VALUE"],
                            $hlProperty);
                    }
                }
            }
            if ($arProduct["PREVIEW_PICTURE"]) {
                $arProduct["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arProduct["PREVIEW_PICTURE"],
                    array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
            }
            if ($arProduct["DETAIL_PICTURE"]) {
                $arProduct["DETAIL_PICTURE"] = CFile::ResizeImageGet($arProduct["DETAIL_PICTURE"],
                    array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
            }

            $this->arProducts[$arProduct["ID"]] = $this->prepareFields($select, $arProduct);
            //TODO: quantity for user basket
            $this->arProducts[$arProduct["ID"]]["QUANTITY"] = '0';
        }
    }

    private function getSelectFields()
    {
        $select = [];
        $this->selectFields = $this->getArFields(array_merge($this->selectFields, $this->arParams["HEADERS_COLUMN"]));
        $select = array_merge($select, array_keys($this->selectFields));


        if (isset($this->arParams["PROPERTY_CODE"]) && !empty($this->arParams["PROPERTY_CODE"])) {
            $this->selectProps = $this->setSelectProps($this->arParams["PROPERTY_CODE"]);
            $select = array_merge($select, array_keys($this->selectProps));
        }

        if (isset($this->arParams["PRICE_CODE"]) && !empty($this->arParams["PRICE_CODE"])) {
            $this->selectPrice = $this->getPrice($this->arParams["PRICE_CODE"]);
            foreach ($this->selectPrice as $priceItem) {
                $select[] = "CATALOG_CURRENCY_" . $priceItem['ID'];
            }
            $this->arFieldsPriceCode = array_keys($this->selectPrice);
            $select = array_merge($select, $this->arFieldsPriceCode);
        }

        if (isset($this->arParams["OFFERS_PROPERTY_CODE"]) && !empty($this->arParams["OFFERS_PROPERTY_CODE"])) {
            $this->offersSelectProps = $this->setSelectProps($this->arParams["OFFERS_PROPERTY_CODE"]);
        }

        $select[] = "TYPE";
        $select[] = "CURRENCY";
        return array_diff($select, [""]);
    }

    private function getArFields($arFieldsCode)
    {
        $arFieldsLang = [];
        if (Loader::IncludeModule("lists")) {
            $iblock = new CListFieldList($this->arParams["IBLOCK_ID"]);
            foreach ($arFieldsCode as $code) {
                $arField = $iblock->GetArrayByID($code);
                if ($arField["NAME"]) {
                    $arFieldsLang[$code] = $arField;
                } else {
                    $arFieldsLang[$code]["NAME"] = $code;
                }
            }
        } else {
            foreach ($arFieldsCode as $code) {
                $arFieldsLang[$code]["NAME"] = $code;
            }
        }

        return $arFieldsLang;
    }

    private function setSelectProps($arPropsCode)
    {
        $result = [];

        $dbProps = \Bitrix\Iblock\PropertyTable::getList([
            "filter" => [
                "CODE" => $arPropsCode,
            ],
            "select" => ["CODE", "NAME", "ID"]
        ]);

        while ($arProp = $dbProps->fetch()) {
            $result["PROPERTY_" . $arProp["CODE"]] = $arProp;
        }

        return $result;
    }

    private function getPrice($priceCode)
    {
        global $USER;

        $arAccessPrice = Bitrix\Catalog\GroupAccessTable::getList([
            'filter' => [
                "GROUP_ID" => CUser::GetUserGroup($USER->getId())
            ],
            'select' => ["CATALOG_GROUP_ID"]
        ])->fetchAll();

        $dbPriceType = CCatalogGroup::GetList(
            ["SORT" => "ASC"],
            [
                "NAME" => $priceCode,
                "ID" => array_unique(array_column($arAccessPrice, 'CATALOG_GROUP_ID'))
            ],
            false,
            false,
            ["ID", "NAME", "NAME_LANG"]
        );
        while ($arPriceType = $dbPriceType->Fetch()) {
            $arPrice["PRICE_" . $arPriceType["ID"]] = [
                "ID" => $arPriceType["ID"],
                "NAME" => $arPriceType["NAME_LANG"] ?: $arPriceType["NAME"],
            ];
        }

        return $arPrice ?: [];
    }

    private function getProductsFilter()
    {
        if ($this->arParams["MODEL_OF_WORK"] == "user_config" && $this->arParams["FILTER"]) {
            $filter = $this->arParams["FILTER"];
            return $filter;
        }

        $filter = [
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "INCLUDE_SUBSECTIONS" => "Y"
        ];

        if ($this->arParams["ONLY_AVAILABLE"] == "Y") {
            $filter["AVAILABLE"] = "Y";
        }

        if ($this->arParams["ONLY_ACTIVE"] == "Y") {
            $filter["ACTIVE"] = "Y";
        }

        if ($this->arParams["SECTION_ID"]) {
            $filter["SECTION_ID"] = $this->arParams["SECTION_ID"];
        }

        if (!empty($this->arParams["FILTER"])) {
            $filter = array_merge($filter, $this->arParams["FILTER"]);
        }

        return $filter;
    }

    private function getPropertiesHL($arPropsCode)
    {
        if (Loader::IncludeModule('highloadblock')) {
            $dbProps = \Bitrix\Iblock\PropertyTable::getList([
                "filter" => [
                    "CODE" => $arPropsCode,
                    "PROPERTY_TYPE" => "S",
                    "USER_TYPE" => "directory",
                    "%USER_TYPE_SETTINGS" => "TABLE_NAME",
                ],
                "select" => ["CODE", "NAME", "IBLOCK_ID", "ID", "USER_TYPE_SETTINGS_LIST"]
            ]);

            while ($arProp = $dbProps->fetch()) {
                $arProps[$arProp["CODE"]] = $arProp;
            }
        }

        return $arProps ?: false;
    }

    private function getPropertyValueFromHL($value, $hlProperty)
    {
        $hlblock = HighloadBlockTable::getList(array(
            'filter' => array('TABLE_NAME' => $hlProperty['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])
        ))->fetch();

        if ($hlblock) {
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $entityClass = $entity->getDataClass();

            $res = $entityClass::getList([
                'filter' => [
                    'UF_XML_ID' => $value
                ],
                'select' => ["UF_NAME", "UF_XML_ID"]
            ])->fetch();

            return $res["UF_NAME"] ?: '';
        }
    }

    private function prepareFields($arFields, $arResult)
    {
        $modifyArFields = [];
        foreach ($arFields as $field) {
            if (isset($arResult[$field])) {
                $modifyArFields[$field] = $arResult[$field];
            } elseif (isset($arResult[$field . "_VALUE"])) {
                $modifyArFields[$field] = $arResult[$field . "_VALUE"];
            } else {
                $modifyArFields[$field] = '';
            }
        }

        return $modifyArFields;
    }

    private function getOffers()
    {
        if (!$this->idProductsForFilter) {
            return;
        }

        $arIblockOffers = CCatalogSku::GetInfoByProductIBlock($this->arParams['IBLOCK_ID']);

        if ($arIblockOffers) {
            $filter = [
                "IBLOCK_ID" => $arIblockOffers["IBLOCK_ID"],
                "PROPERTY_" . $arIblockOffers["SKU_PROPERTY_ID"] => $this->idProductsForFilter
            ];

            if ($this->arParams["ONLY_AVAILABLE"] == "Y") {
                $filter["AVAILABLE"] = "Y";
            }

            if ($this->arParams["ONLY_ACTIVE"] == "Y") {
                $filter["ACTIVE"] = "Y";
            }

            if ($this->arParams["MODEL_OF_WORK"] == "user_config" && !empty($this->arParams["FILTER"])) {
                $filter = $this->getOffersPropFilter($this->arParams["FILTER"]);
            } elseif (!empty($this->arParams["FILTER"])) {
                // $filter = array_merge($filter, $this->arParams["FILTER"]);
            }

            $arPropsHL = $this->getPropertiesHL($this->arParams["OFFERS_PROPERTY_CODE"]);
            $select = array_merge(array_keys($this->selectFields), array_keys($this->offersSelectProps),
                array_keys($this->selectPrice));
            $select[] = "PROPERTY_" . $arIblockOffers["SKU_PROPERTY_ID"];
            $select[] = "CURRENCY";
            $dbOffers = CIblockElement::GetList(
                $this->order,
                $filter,
                false,
                false,
                $select
            );
            while ($arOffer = $dbOffers->Fetch()) {
                $mainProductsId = $arOffer["PROPERTY_" . $arIblockOffers["SKU_PROPERTY_ID"] . "_VALUE"];
                if (!$this->arProducts[$mainProductsId]) {
                    continue;
                }

                if ($arPropsHL) {
                    foreach ($arPropsHL as $code => $hlProperty) {
                        if (isset($arOffer["PROPERTY_" . $code . "_VALUE"]) && !empty($arOffer["PROPERTY_" . $code . "_VALUE"])) {
                            $arOffer["PROPERTY_" . $code . "_VALUE"] = $this->getPropertyValueFromHL($arOffer["PROPERTY_" . $code . "_VALUE"],
                                $hlProperty);
                        }
                    }
                }
                $this->arProducts[$mainProductsId]["OFFERS"][] = $this->prepareFields($select,
                    $arOffer);;
            }
        }

        unset($this->idProductsForFilter);
    }

    private function renderFile()
    {
        $this->objSpreadsheet = new Spreadsheet();
        $this->objSheet = $this->objSpreadsheet->getActiveSheet();
        $this->arHeader = array_merge($this->selectFields, $this->selectProps, $this->offersSelectProps,
            $this->selectPrice);
        $this->arHeader["QUANTITY"] = [
            "NAME" => Loc::getMessage("B2B_EXCEL_EXPORT_HEADER_QUANTITY")
        ];
        $this->setHeaderStyle();


        $j = 0;
        $i = -1;

        foreach ($this->arHeader as $code => $header) {
            $rowcount = 2;
            if ($j > 25) {
                $j = 0;
                $i++;
            }

            $letter = $i === -1 ? $this->alphabet[$j] : ($this->alphabet[$i] . $this->alphabet[$j]);

            if (!empty($header['WIDTH'])) {
                $this->objSheet->getColumnDimension($letter)->setWidth($header['WIDTH']);
            } else {
                $this->objSheet->getColumnDimension($letter)->setAutoSize(true);
            }
            $this->objSheet->getStyle($letter . '1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $this->objSheet->getStyle($letter . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if (!empty($this->headerStyle)) {
                $this->objSheet->getStyle($letter . '1')->applyFromArray($this->headerStyle);
            }

            $this->objSheet->getStyle($letter . '1')->getFont()->setBold(true);
            $this->objSheet->setCellValue($letter . '1', $this->validEncoding($header["NAME"]));
            if ($code == "ID") {
                $this->objSheet->getColumnDimension($letter)->setVisible(false);
            }

            foreach ($this->arProducts as $product) {
                if (isset($product["OFFERS"])) {
                    foreach ($product["OFFERS"] as $offer) {
                        if ($offer[$code] || $offer[$code] === "0") {
                            $this->renderExcelRow($code, $offer, $letter, $rowcount);
                        } elseif ($product[$code] || $product[$code] === "0") {
                            $this->renderExcelRow($code, $product, $letter, $rowcount);
                        } else {
                            $this->renderExcelRow($code, "", $letter, $rowcount);
                        }
                        $rowcount++;
                    }
                } else {
                    if ($product[$code]) {
                        $this->renderExcelRow($code, $product, $letter, $rowcount);
                    } else {
                        $this->renderExcelRow($code, "", $letter, $rowcount);
                    }
                    $rowcount++;
                }
            }
            $j++;
        }
    }

    private function setHeaderStyle()
    {
        $borderHeader = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => array('rgb' => '000000')
                ),
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );

        $this->objSheet->getStyle("A1:" . $this->alphabet[count($this->arHeader) - 1] . "1")->applyFromArray($borderHeader);
        $this->objSheet->getRowDimension("1")->setRowHeight(30);
    }

    private function validEncoding($str)
    {
        //TODO: take out the received encodings
        if (!Encoding::detectUtf8($str)) {
            return Encoding::convertEncoding($str, 'Windows-1251', 'UTF-8');
        }

        return $str;
    }

    private function renderExcelRow($code, $arFields, $letter, $rowcount)
    {
        if (in_array($code, $this->arFieldsTypeImg) && !empty($arFields[$code])) {

            $objDrawing = new Drawing();
            if (($code === 'DETAIL_PICTURE' || $code === 'PREVIEW_PICTURE') && is_numeric($arFields[$code])) {
                $path = CFile::GetPath($arFields[$code]);
                $name = Path::getName($path);
                $newPath = $_SERVER['DOCUMENT_ROOT'] . Path::getDirectory($path) . "/low_{$name}";
                if (file_exists($newPath)) {
                    $objDrawing->setPath($newPath);
                } else {
                    CFile::ResizeImageFile($_SERVER['DOCUMENT_ROOT'] . $path, $newPath,
                        ['width' => 80, 'height' => 80]);
                    $objDrawing->setPath($newPath);
                }
                $this->objSheet->getRowDimension($rowcount)->setRowHeight(50);
            } else {
                $objDrawing->setPath($_SERVER['DOCUMENT_ROOT'] . $arFields[$code]);
            }
            $objDrawing->setCoordinates($letter . $rowcount);
            $objDrawing->setOffsetX(10);
            $objDrawing->setOffsetY(10);
            $objDrawing->setWorksheet($this->objSheet);
            $this->objSheet->getRowDimension($rowcount)->setRowHeight(50);
        } elseif (in_array($code,
                is_array($this->arFieldsPriceCode) ? $this->arFieldsPriceCode : []) && !empty($arFields[$code])) {
            $currency = $arFields['CATALOG_CURRENCY_' . $this->selectPrice[$code]["ID"]] ?: CCurrency::GetBaseCurrency();
            $this->objSheet->setCellValue($letter . $rowcount,
                $arFields[$code] . " " . $currency);
        } else {
            if (!empty($arFields[$code])) {
                $this->objSheet->setCellValue($letter . $rowcount,
                    $this->validEncoding($arFields[$code]));
                if (!is_int($arFields[$code]) ? (ctype_digit($arFields[$code])) : true) {
                    $this->objSheet->getStyle($letter . $rowcount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                }
            }

        }
    }

    protected function saveFile()
    {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/upload/tmp/sotbit_b2bcabinet_blank') &&
            !mkdir($concurrentDirectory = $_SERVER['DOCUMENT_ROOT'] . '/upload/tmp/sotbit_b2bcabinet_blank', 0777,
                true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }


        $fileName = mt_rand() . '.xlsx';
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/tmp/sotbit_b2bcabinet_blank/' . $fileName;
        $objWriter = new Xlsx($this->objSpreadsheet);
        $objWriter->save($filePath);

        $http = 'http://';
        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $http = 'https://';
        }

        $filePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
        $filePath = $http . $_SERVER['SERVER_NAME'] . $filePath;

        CFile::SaveFile(
            [
                "name" => $fileName,
                "tmp_name" => $filePath,
                "MODULE_ID" => "sotbit.b2bcabinet",
                "description" => "blank excel export",
                "external_id" => md5(serialize($this->arParams)),
            ],
            "/tmp/sotbit_b2bcabinet_blank/"
        );

        return ['filePath' => $filePath];
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    protected function chooseOffer($offers, $iblockId)
    {
        // TODO: Implement chooseOffer() method.
    }

    protected function getIblockElements($elementIterator)
    {
        // TODO: Implement getIblockElements() method.
    }

    protected function getAdditionalCacheId()
    {
        // TODO: Implement getAdditionalCacheId() method.
    }

    protected function getComponentCachePath()
    {
        // TODO: Implement getComponentCachePath() method.
    }
}