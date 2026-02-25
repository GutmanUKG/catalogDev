<?php
namespace Onelab;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/function.php';
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class importCatalog
{
    public function showFormImport()
    {
        if (!check_user()) {
            echo json_encode([
                "success" => 'error',
                "message" => 'У вас нет прав на импорт файлов'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => "<div class='import_popup_form'>
                <div class='close_btn'></div>
                <h4>Загрузите exel файл</h4>
                <form method='POST' action='import.php' enctype='multipart/form-data' id='import_catalog_form'>
                    <label for='file'>
                        <input type='file' name='exel_file'>
                    </label>
                    <button type='submit'>Импорт</button>
                </form>
                </div>"
        ]);
    }

    public function catalogImportExel($arrFileds)
    {
        if (empty($arrFileds)) {
            echo json_encode([
                "success" => 'error',
                "message" => 'Отсутствует или поврежден файл'
            ]);
            return;
        }

        if (!\CModule::IncludeModule("iblock") || !\CModule::IncludeModule("catalog")) {
            echo json_encode([
                "success" => 'error',
                "message" => 'Ошибка подключения модулей'
            ]);
            return;
        }

        if (!empty($arrFileds['exel_file'][0]['tmp_name'])) {
            $filePath = $arrFileds['exel_file'][0]['tmp_name'];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $fieldMap = [
                'A' => 'ID',
                'B' => 'NAME',
                'D' => 'ART',
                'F' => 'KAT_CATEGORY',
                'G' => 'PRICE',
                'I' => 'COUNT',
                'K' => 'DISTROY_TYPE',
                'J' => 'STATUS',
                'C' => 'BRAND',
                'E' => 'PART',
                'H' => 'RRP',
                //'L'=> 'PREVIEW_TEXT'
            ];

            $requiredFields = ['NAME', 'ART', 'KAT_CATEGORY', 'PRICE', 'COUNT'];
            $iblockId = 33;
            $errors = [];
            $updated = 0;
            $created = 0;

            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex == 1) continue;

                $data = [];
                foreach ($fieldMap as $column => $field) {
                    $data[$field] = trim($row[$column]);
                }

                $hasError = false;
                foreach ($requiredFields as $field) {
                    if (empty($data[$field])) {
                        $errors[] = "Ошибка на строке {$rowIndex}: отсутствует поле '{$field}'";
                        file_put_contents(__DIR__ . '/log.txt', print_r($errors, true)."\n",FILE_APPEND);
                        $hasError = true;
                    }
                }
                if ($hasError) continue;

                $el = new \CIBlockElement;

                if (!empty($data['ID'])) {
                    $updateFields = [
                        "NAME" => $data['NAME'],
                        "PROPERTY_VALUES" => $this->prepareProperties($data)
                    ];

                    if ($el->Update($data['ID'], $updateFields)) {
                        $this->setRetailPrice($data['ID'], $data['PRICE']);
                        $this->setPreviewPictureByArt($data['ART'], $data['ID']);
                        $this->setPreviewTextByArt($data['ART'], $data['ID']);
                        $updated++;
                    } else {
                        $errors[] = "Ошибка обновления элемента с ID {$data['ID']}: " . $el->LAST_ERROR;
                        file_put_contents(__DIR__ . '/log.txt', print_r($errors, true)."\n",FILE_APPEND);
                    }
                } else {
                    $newElementFields = [
                        "IBLOCK_ID" => $iblockId,
                        "NAME" => cleanHtmlEntities($data['NAME']),
                        "PROPERTY_VALUES" => $this->prepareProperties($data)
                    ];

                    $newElementID = $el->Add($newElementFields);
                    if ($newElementID) {
                        $this->setRetailPrice($newElementID, $data['PRICE']);
                        $this->setPreviewPictureByArt($data['ART'], $newElementID);
                        //$this->setPreviewTextByArt($data['ART'], $newElementID);
                        $created++;
                    } else {
                        $errors[] = "Ошибка создания элемента на строке {$rowIndex}: " . $el->LAST_ERROR;
                        file_put_contents(__DIR__ . '/log.txt', print_r($errors, true)."\n",FILE_APPEND);
                    }
                }
            }

            echo json_encode([
                "status" => 'success',
                "message" => "Обновлено: {$updated}, Создано: {$created}",
                "errors" => $errors
            ]);
        } else {
            echo json_encode([
                "status" => 'error',
                "message" => 'Файл не загружен'
            ]);
        }
    }

    private function setPreviewPictureByArt($art, $elementId)
    {
        $arSelect = ["ID", "PREVIEW_PICTURE", "PREVIEW_TEXT"];
        $arFilter = ["IBLOCK_ID" => 7, "PROPERTY_CML2_ARTICLE" => $art ];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        if ($element = $res->Fetch()) {
            $el = new \CIBlockElement();
            if ($element['PREVIEW_PICTURE']) {
                $el->Update($elementId, ["PREVIEW_PICTURE" => \CFile::MakeFileArray($element['PREVIEW_PICTURE'])]);
                //\CIBlockElement::Update($elementId, ["PREVIEW_PICTURE" => \CFile::MakeFileArray($element['PREVIEW_PICTURE'])]);
            }

            //$el->Update($elementId, ["PREVIEW_TEXT" => $element['PREVIEW_TEXT']]);
        }
    }
    private function setPreviewTextByArt($art, $elementId)
    {

        $arSelect = ["ID", "DETAIL_TEXT"];
        $arFilter = ["IBLOCK_ID" => 7, "PROPERTY_CML2_ARTICLE" => $art ];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        if ($element = $res->Fetch()) {
            if (!empty($element['PREVIEW_TEXT'])) {
                $el = new \CIBlockElement();
                $el->Update($elementId, ["PREVIEW_TEXT" => $element['DETAIL_TEXT']]);
            }
        }
    }
    //Установка превью текста из excel
    private function setPreviewText($elementId, $PREVIEW_TEXT)
    {
        $arSelect = ["ID"];
        $arFilter = ["IBLOCK_ID" => 33];
    }
    private function prepareProperties($data)
    {
        $category = $data['CATEGORY'];
        // Если категория пуста, берем из инфоблока 7
        if (empty($category) && !empty($data['ART'])) {
            $category = $this->getCategoryFromIblock7($data['ART']);
        }
        return [
            'ART' => $data['ART'],
            'KAT_CATEGORY' => $this->mapKatCategory($data['KAT_CATEGORY']),
            'COUNT' => $data['COUNT'],
            'DISTROY_TYPE' => $data['DISTROY_TYPE'],
            'STATUS' => $data['STATUS'],
            'BRAND' =>trim($data['BRAND']),
            'PART' => (empty($data['PART']) && $data['COUNT'] < 1) ? $data['ART'] . $data['KAT_CATEGORY'] : $data['PART'],
            'RRP' => $data['RRP'],
            'CATEGORY' => $category
        ];
    }
    private function getCategoryFromIblock7($art)
    {
        $arSelect = ["ID", "PROPERTY_PODKATEGORIYA_ATTR_S"];
        $arFilter = ["IBLOCK_ID" => 7, "PROPERTY_CML2_ARTICLE" => $art];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        if ($element = $res->Fetch()) {
            return $element['PROPERTY_PODKATEGORIYA_ATTR_S_VALUE'] ?? null;
        }

       return null;
    }
    private function mapKatCategory($category)
    {
        $map = [
            'КАТ-1' => 19684,
            'КАТ-2' => 19685,
            'КАТ-3' => 19686,
            'КАТ-4' => 19687
        ];
        return $map[$category] ?? null;
    }

    private function setRetailPrice($elementId, $price)
    {
        $priceFields = [
            "PRODUCT_ID" => $elementId,
            "CATALOG_GROUP_ID" => 1,
            "PRICE" => $price,
            "CURRENCY" => "KZT"
        ];

        $existingPrice = \CPrice::GetList(
            [],
            ["PRODUCT_ID" => $elementId, "CATALOG_GROUP_ID" => 1]
        )->Fetch();

        if ($existingPrice) {
            \CPrice::Update($existingPrice['ID'], $priceFields);
        } else {
            \CPrice::Add($priceFields);
        }
    }
}

?>


