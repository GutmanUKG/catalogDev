<meta charset="UTF-8">
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Подключаем PhpSpreadsheet
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Подключаем модуль инфоблоков
CModule::IncludeModule("iblock");

header('Content-Type: text/html; charset=utf-8');

$inputFileName = 'data/data.xlsx';

// Загружаем Excel-файл
$spreadsheet = IOFactory::load($inputFileName);
$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow(); // например: 100
$sum = 0;
echo '<div class="row">';
for ($row = 2; $row <= $highestRow; $row++) {
    $colA = $sheet->getCell("A$row")->getValue(); // ← ID элемента инфоблока
    $colC = $sheet->getCell("C$row")->getValue();
    $colB = $sheet->getCell("B$row")->getValue();
    $colH = $sheet->getCell("H$row")->getValue();
    $colG = $sheet->getCell("G$row")->getValue();
    $colI = $sheet->getCell("I$row")->getValue();
    $colK = $sheet->getCell("K$row")->getValue();
    
    echo '<div class="mb-4 col-sm-3 style="background-color:white;">';
    
    // Пытаемся получить картинку по ID из инфоблока
    echo "$colB";
    if (!empty($colA)) {
        $res = CIBlockElement::GetList([], [
            "ID" => (int)$colA,
            "IBLOCK_ID" => 33, // Укажи нужный инфоблок
            "ACTIVE" => "Y"
        ], false, false, ["PREVIEW_PICTURE"]);
	
        if ($el = $res->GetNext()) {
            if (!empty($el["PREVIEW_PICTURE"])) {
                $imgSrc = CFile::GetPath($el["PREVIEW_PICTURE"]);
                echo "<div class='text-center'><img src='{$imgSrc}' alt='Картинка элемента {$colA}' width='100'></div><br>";
            }
        }
    }

    // Вывод данных из Excel
    
    
    echo "Цена со скидкой: <b>$colG</b><br>РРЦ: <i>$colH</i><br><i>$colK</i> <br>$colI шт.<br>";
    echo "</div>";
    
    
    $sum = $sum + 1;
}
echo "</div>";
echo "col". $sum;
?>
