<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sotbit.b2bcabinet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \PhpOffice\PhpSpreadsheet\IOFactory;

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Loader,
    Bitrix\Main\Error,
    Bitrix\Main\ErrorCollection,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Text\Encoding;

Loc::loadMessages(__FILE__);


class B2BExcelImport extends \CBitrixComponent implements Controllerable, \Bitrix\Main\Errorable
{

    const INPUT_NAME = 'FILES_IMPORT_EXCEL';

    private $arCheckModules = [
        'sale',
        'catalog',
        'iblock'
    ];
    private $filesIdList = [];
    private $filesList = [];
    private $excelTypes = [
        'Xls',
        'Xlsx',
    ];
    private $productsList = [];
    private $productsInfoList = [];
    private $resultsImport = [];
    private $productsId = [];
    private $countAddedProducts = 0;
    protected $errorCollection;

    public function configureActions()
    {
        return [
            'importExcel' => [
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
            "MULTIPLE",
            "MAX_FILE_SIZE",
            "INPUT_NAME",
            "IBLOCK_ID",
            "IBLOCK_TYPE",
            "TYPE_IDENTIFIER_PRODUCT",
            "PROPERTY_ARTICUL"
        ];
    }

    public function onPrepareComponentParams($params)
    {
        $this->errorCollection = new ErrorCollection();
        $params["INPUT_NAME"] = $params["INPUT_NAME"] ?: self::INPUT_NAME;
        if (!empty($params["PROPERTY_ARTICUL"])){
            $params["PROPERTY_ARTICUL_CODE"] = $params["PROPERTY_ARTICUL"];
            $resPropArt = CIBlockProperty::GetByID($params["PROPERTY_ARTICUL"], $params["IBLOCK_ID"], $params["IBLOCK_TYPE"]);
            if($obPropArt = $resPropArt->Fetch()){
                $params["PROPERTY_ARTICUL_NAME"] = $obPropArt["NAME"];
            }
        }
        return $params;
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    public function importExcelAction($formData)
    {
        if (!$formData["data"][$this->arParams["INPUT_NAME"]]) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NO_FILES"));
            return null;
        }

        if ($this->arParams["MULTIPLE"] == "Y" && is_array($formData["data"][$this->arParams["INPUT_NAME"]])) {
            $this->filesIdList = $formData["data"][$this->arParams["INPUT_NAME"]];
        } else {
            $this->filesIdList[] = $formData["data"][$this->arParams["INPUT_NAME"]];
        }

        $this->getFiles();

        if (!$this->checkFormatFiles()) {
            return null;
        }

        $this->readFiles();
        $this->getProductsInfo();

        if ($this->addProductsToBasket()) {
            $arResultData["RESULT"] = [
                'DATA' => $this->resultsImport,
                'TOTAL_COUNT' => $this->countAddedProducts,
                'FILES' => $this->getFilesInfo(),
                'PRODUCTS' => $this->productsInfoList,
            ];

            $obComponentResult = new \Bitrix\Main\Engine\Response\Component('sotbit:b2bcabinet.excel.import.result', '',
                $arResultData);
        }

        $this->deleteFiles();
        return $obComponentResult ?: null;
    }

    private function addProductsToBasket()
    {
        if (!$this->productsList) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NOT_PRODUCTS_IN_FILES"));
            return false;
        }

        foreach ($this->productsList as $fileId => $products) {
            foreach ($products as $productId => $arProduct) {
                $controller = new Sotbit\B2bcabinet\Controller\Basket();
                if ($this->productsInfoList[$productId]["QUANTITY_TRACE"] === 'Y' &&
                    $this->productsInfoList[$productId]["CAN_BUY_ZERO"] !== 'Y' &&
                    $this->productsInfoList[$productId]["QUANTITY"] < $arProduct["QNT"]
                ) {

                    if ($controller->addProductToBasketAction([
                        "PRODUCT_ID" => $productId,
                        "QUANTITY" => $this->productsInfoList[$productId]["QUANTITY"]
                    ])) {
                        $this->resultsImport[$fileId]["PRODUCTS_NO_ADDED"][] = [
                            "ID" => $productId,
                            "QUANTITY" => $arProduct["QNT"],
                            "ERROR" => Loc::getMessage('B2B_EXCEL_IMPORT_PRODUCT_LESS_AVAILABLE', [
                                '#PRODUCT#' => $this->productsInfoList[$productId]["NAME"],
                                '#QUANTITY#' => $this->productsInfoList[$productId]["QUANTITY"]
                            ])
                        ];
                    } else {
                        $this->resultsImport[$fileId]["PRODUCTS_NO_ADDED"][] = [
                            "ID" => $productId,
                            "QUANTITY" => $arProduct["QNT"],
                            "ERROR" => $controller->getErrors()[0]->getMessage()
                        ];
                    }

                    continue;
                }

                if ($controller->addProductToBasketAction(["PRODUCT_ID" => $productId, "QUANTITY" => $arProduct["QNT"]])) {
                    $this->resultsImport[$fileId]["PRODUCTS_ADDED"][] = [
                        "ID" => $productId,
                        "QUANTITY" => $arProduct["QNT"]
                    ];
                    $this->countAddedProducts ++;
                } else {
                    $this->resultsImport[$fileId]["PRODUCTS_NO_ADDED"][] = [
                        "ID" => $productId,
                        "QUANTITY" => $arProduct["QNT"],
                        "ERROR" => $controller->getErrors()[0]->getMessage()
                    ];
                }
            }
        }

        return true;

    }

    private function readFiles()
    {
        foreach ($this->filesList as $fileId => $filePath) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $objPHPExcel = $reader->load($filePath);
            $objPHPExcel->setActiveSheetIndex(0);
            $aSheet = $objPHPExcel->getActiveSheet();
            $higestRow = $aSheet->getHighestRow();
            $rowiterator = $aSheet->getRowIterator();
            foreach ($rowiterator as $row) {
                $cellIterator = $row->getCellIterator();

                foreach ($cellIterator as $j => $cell) {
                    if (mb_convert_encoding($cell->getValue(), LANG_CHARSET,
                            mb_detect_encoding($cell->getValue())) == Loc::getMessage("B2B_EXCEL_IMPORT_COL_QUANTITY")) {
                        $qntColl = $cell->getColumn();
                        continue;
                    }

                    if ($this->arParams['TYPE_IDENTIFIER_PRODUCT'] == "ART" && 
                        mb_convert_encoding($cell->getValue(), LANG_CHARSET,
                            mb_detect_encoding($cell->getValue())) == $this->arParams["PROPERTY_ARTICUL_NAME"]) {
                        $artColl = $cell->getColumn();
                        continue;
                    }
                    else if($cell->getValue() == 'ID') {
                        $idColl = $cell->getColumn();
                        continue;
                    }
                }

                if ($this->arParams['TYPE_IDENTIFIER_PRODUCT'] == "ART"){
                    if (empty($qntColl) || empty($artColl)) 
                        $this->setFileError($fileId, Loc::getMessage("B2B_EXCEL_IMPORT_FILE_WRONG_STRUCTURE"));
                } else {
                    if (empty($qntColl) || empty($idColl)) 
                        $this->setFileError($fileId, Loc::getMessage("B2B_EXCEL_IMPORT_FILE_WRONG_STRUCTURE"));
                }
                break;
            }

            if ($this->issetFileError($fileId)) {
                continue;
            }

            for ($i = 2; $i <= $higestRow; $i++) {
                if ($this->arParams['TYPE_IDENTIFIER_PRODUCT'] == "ART"){
                    $cellArt = $aSheet->getCell($artColl . $i)->getValue();
                    $idValue=$this->getProductIdByArticle($cellArt);
                } else {
                    $cellID = $aSheet->getCell($idColl . $i)->getValue();
                    $idValue=$cellID;
                }
                $cellQNT = $aSheet->getCell($qntColl . $i);

                $qntValue = $cellQNT->getValue();

                if ($idValue && $qntValue) {
                    $this->productsList[$fileId][$idValue]['QNT'] = $qntValue+$this->productsList[$fileId][$idValue]['QNT'];
                    $this->productsId[] = $idValue;
                }elseif(empty($idValue)){
                    if($this->arParams['TYPE_IDENTIFIER_PRODUCT'] == "ART"){
                        $this->setFileError($fileId, Loc::getMessage("B2B_EXCEL_IMPORT_NO_SUCH_PRODUCT_FROM_ARTICLE",['#ARTICLE#'=>$cellArt]));
                    }
                }
            }

            if (!$this->productsList[$fileId]) {
                $this->setFileError($fileId, Loc::getMessage("B2B_EXCEL_IMPORT_NOT_PRODUCTS_IN_FILES"));
            }
        }
    }

    private function getProductsInfo()
    {
        if (!$this->productsId) {
            return;
        }

        if(!Loader::includeModule('catalog')) {
            return;
        }

        $arProducts = [];
        $arMeasure = \Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($this->productsId);
        $arSelect = [
            "ID",
            "NAME",
            "PREVIEW_PICTURE",
            "DETAIL_PICTURE",
            "QUANTITY",
            "QUANTITY_TRACE",
            "CAN_BUY_ZERO",
        ];
        $arFilter = [
            "ID" => $this->productsId
        ];
        $dbResult = CIblockElement::GetList([], $arFilter, false, false, $arSelect);
        while($result = $dbResult->Fetch()) {
            $arProducts[$result["ID"]] = [
                'ID' => $result["ID"],
                'NAME' => trim($result["NAME"]),
                'MEASURE' => $arMeasure[$result["ID"]]["MEASURE"]["SYMBOL"],
                "QUANTITY" => $result["QUANTITY"],
                "QUANTITY_TRACE" => $result["QUANTITY_TRACE"],
                "CAN_BUY_ZERO" => $result["CAN_BUY_ZERO"],
                //'PICTURE' => ($result["PREVIEW_PICTURE"] || $result["DETAIL_PICTURE"]) ? CFile::ResizeImageGet($result["PREVIEW_PICTURE"] ?: $result["DETAIL_PICTURE"], array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true) : []
//                'RATIO' => $arMeasure[$result["ID"]]["RATIO"],
            ];
        }

        $this->productsInfoList = $arProducts;
    }

    private function getProductIdByArticle($artValue)
    {
        $arSelect = ["ID"];
        $arFilter = ["PROPERTY_".$this->arParams["PROPERTY_ARTICUL_CODE"] => $artValue];
        $dbResult = CIblockElement::GetList([], $arFilter, false, false, $arSelect);
        while($result = $dbResult->Fetch()) {
            return $result["ID"];
        }
    }

    private function getFilesInfo()
    {
        $arFiles = [];
        $bdFiles = CFile::GetList(
            [],
            [
                "MODULE_ID"=>"sotbit.b2bcabinet",
                "@ID" => implode(',', $this->filesIdList)
            ]
        );
        while($arFile = $bdFiles->Fetch()) {
            $arFiles[$arFile["ID"]] = $arFile;
        }

        return $arFiles;
    }

    private function deleteFiles()
    {
        foreach ($this->filesIdList as $id) {
            CFile::Delete($id);
        }
    }

    private function checkFormatFiles()
    {
        if (!$this->filesList) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NO_FILES"));
            return false;
        }

        foreach ($this->filesList as $id => $filePath) {
            $canRead = false;
            foreach ($this->excelTypes as $type) {
                $reader = IOFactory::createReader($type);
                if ($reader->canRead($filePath)) {
                    $canRead = true;
                    break;
                }
            }

            if ($canRead === false) {
                $this->setFileError($id, Loc::getMessage("B2B_EXCEL_IMPORT_FILE_IS_NOT_EXCEL_FORMAT"));
                unset($this->filesList[$id]);
            }
        }

        if (!$this->filesList) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NO_FILES_EXCEL"));
            return false;
        }

        return true;

    }

    private function getFiles()
    {
        foreach ($this->filesIdList as $fileId) {
            $filePath = $_SERVER["DOCUMENT_ROOT"] . CFile::GetPath($fileId);
            if (file_exists($filePath)) {
                $this->filesList[$fileId] = $filePath;
            }
        }
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

    private function validEncoding($str)
    {
        if (!Encoding::detectUtf8($str)) {
            return Encoding::convertEncoding($str, 'Windows-1251', 'UTF-8');
        }

        return $str;
    }

    private function issetFileError($fileID)
    {
        return isset($this->resultsImport[$fileID]["ERROR_LIST"]);
    }

    private function setFileError($fileID, $errorMsg)
    {
        $this->resultsImport[$fileID]["ERROR_LIST"][] = $errorMsg;
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}