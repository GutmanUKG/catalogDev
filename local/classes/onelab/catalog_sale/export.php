<?php
namespace Onelab;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/function.php';
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class exportCatalog
{
    public function exportXML($arItems)
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //file_put_contents(__DIR__ . '/log.txt', print_r($arItems, true), FILE_APPEND);
        // Заголовки
        $headers = [
            'A1' => 'ID',
            'B1' => 'Наименование',
            'C1' => 'Бренд',
            'D1' => 'Артикул',
            'E1' => 'Серийный номер',
            'F1' => 'Категория уценки',
            'G1' => 'Цена уценки',
            'H1' => 'РРЦ',
            'I1' => 'В наличии',
            //'J1' => 'Статус',
            'K1' => 'Комментарий уценки'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Данные
        $row = 2; // Начинаем со второй строки
        foreach ($arItems as $arItem) {
            $sheet->setCellValue('A' . $row, $arItem['ID'] ?? '');
            $sheet->setCellValue('B' . $row, cleanHtmlEntities($arItem['NAME']) ?? '');
            $sheet->setCellValue('C' . $row, $arItem['PROPERTY_BRAND_VALUE'] ?? '');
            $sheet->setCellValue('D' . $row, $arItem['PROPERTY_ART_VALUE'] ?? '');
            $sheet->setCellValue('E' . $row, $arItem['PROPERTY_PART_VALUE'] ?? '');
            $sheet->setCellValue('F' . $row, $arItem['PROPERTY_KAT_CATEGORY_VALUE'] ?? '');
            $sheet->setCellValue('G' . $row, $arItem['PRICE'] ?? ''); // Цена
            $sheet->setCellValue('H' . $row, $arItem['PROPERTY_RRP_VALUE'] ?? '');
            $sheet->setCellValue('I' . $row, $arItem['PROPERTY_COUNT_VALUE'] ?? '');
           // $sheet->setCellValue('J' . $row, $arItem['PROPERTY_STATUS_VALUE'] ?? '');
            $sheet->setCellValue('K' . $row, $arItem['PROPERTY_DISTROY_TYPE_VALUE']['TEXT'] ?? '');
            $row++;
        }

        // Сохранение файла
        $tempDir = $_SERVER['DOCUMENT_ROOT'] . '/export_files/tmp/exports/'; // Укажите путь для сохранения файла
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true); // Создаём директорию с правами доступа
        }

        $uniqueId = uniqid(); // Генерация уникального идентификатора
        $fileName = $tempDir . 'export_' . $uniqueId . '.xlsx'; // Полный путь до файла
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        // Генерация публичного URL
        $publicPath = 'https://' .$_SERVER['SERVER_NAME']. '/export_files/tmp/exports/export_' . $uniqueId . '.xlsx';

        // Возврат ответа
        echo json_encode([
            "status" => "success",
            "file" => $publicPath, // Публичный путь для скачивания
            "filePth" => $fileName, // Полный путь на сервере
            "items" => $arItems
        ]);
    }
}
