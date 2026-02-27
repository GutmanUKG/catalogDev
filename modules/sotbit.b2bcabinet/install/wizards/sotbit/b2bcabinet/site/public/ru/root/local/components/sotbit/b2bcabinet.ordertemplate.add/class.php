<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sotbit.b2bcabinet/vendor/autoload.php';
use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Error,
    Bitrix\Main\ErrorCollection;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use \PhpOffice\PhpSpreadsheet\IOFactory;



Loc::loadMessages(__FILE__);

class OrderTemplateAdd extends CBitrixComponent implements Controllerable, \Bitrix\Main\Errorable
{
    const INPUT_NAME = 'B2B_ADD_ORDERTEMPLATE';

    protected $productsList = [];
    protected $errorCollection;
    private $filesIdList;
    private $filesList;
    private $resultsImport;
    private $arCheckModules = [
        'sale',
        'catalog',
        'iblock'
    ];

    private $excelTypes = [
        'Xls',
        'Xlsx',
    ];

    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'add' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ],
            'createOrderTemplate' => [
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
            "INPUT_NAME",
        ];
    }

    public function onPrepareComponentParams($params)
    {
        $this->errorCollection = new ErrorCollection();
        $params["INPUT_NAME"] = $params["INPUT_NAME"] ?: self::INPUT_NAME;

        return $params;
    }

    public function executeComponent()
    {
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

    public function createOrderTemplateAction($formData)
    {
        if (!$formData["data"][$this->arParams["INPUT_NAME"]]) {
            $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NO_FILES"));
            return null;
        }

        if (!$this->checkModules()) {
            return null;
        }

        $this->filesIdList[] = $formData["data"][$this->arParams["INPUT_NAME"]];
        $this->getFiles();

        if (!$this->checkFormatFiles()) {
            return null;
        }

        if (!$this->readFiles()) return null;
        $resultId = $this->addProductOrderTemplate();
        $this->deleteFiles();

        return  is_numeric($resultId) ? $resultId : null;
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

                    if ($cell->getValue() == 'ID') {
                        $idColl = $cell->getColumn();
                        continue;
                    }
                }

                if (empty($qntColl) || empty($idColl)) {
                    $this->setFileError($fileId, Loc::getMessage("B2B_EXCEL_IMPORT_FILE_WRONG_STRUCTURE"));
                }
                break;
            }

            if ($this->issetFileError($fileId)) {
                continue;
            }

            for ($i = 2; $i <= $higestRow; $i++) {
                $cellID = $aSheet->getCell($idColl . $i);
                $cellQNT = $aSheet->getCell($qntColl . $i);

                $idValue = $cellID->getValue();
                $qntValue = $cellQNT->getValue();

                if ($idValue && $qntValue) {
                    $this->productsList[$fileId][$idValue]['QUANTITY'] = $qntValue;
                    $this->productsList[$fileId][$idValue]['ID'] = $idValue;
                    $this->productsId[] = $idValue;
                }
            }

            if (!$this->productsList[$fileId]) {
                $this->errorCollection[] = new Error(Loc::getMessage("B2B_EXCEL_IMPORT_NOT_PRODUCTS_IN_FILES"));
                return false;
            }
        }

        return true;
    }


    public function addProductOrderTemplate ()
    {
        global $USER;
        $userId = $USER->GetID();
        foreach ($this->productsList as $fileId => $arProducts) {
            if(!empty($arProducts) && is_array($arProducts) && is_numeric($fileId)){
                $fileArray = CFile::GetFileArray($fileId);
                $orderTeplateFields = [
                    'NAME' => $fileArray["ORIGINAL_NAME"],
                    'USER_ID' => $userId,
                    'SITE_ID' => SITE_ID,
                    'COMPANY_ID' => $_SESSION["AUTH_COMPANY_CURRENT_ID"],
                    'SAVED' => 'N',
                ];

                $orderTemplate = new Sotbit\B2BCabinet\OrderTemplate\OrderTemplate(SITE_ID);
                return $orderTemplate->add($orderTeplateFields, $arProducts);
            }
            else{
                $this->errorCollection[] = new Error(Loc::getMessage('B2B_EXCEL_IMPORT_FILE_WRONG_STRUCTURE'));
                return false;
            }
        }
        $this->errorCollection[] = new Error(Loc::getMessage('B2B_EXCEL_IMPORT_FILE_WRONG_STRUCTURE'));
        return false;
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