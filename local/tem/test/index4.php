<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock")) {
    die("Не удалось подключить модуль инфоблоков");
}

$iblockId = 33;
$elementId = 48187; // <-- Укажи нужный ID

// Получаем список всех свойств инфоблока
$propertyList = [];
$resProps = CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId]);
while ($prop = $resProps->Fetch()) {
    $propertyList[$prop["CODE"]] = $prop["NAME"];
}

// Выводим список всех доступных свойств
echo "<h2>Список свойств инфоблока №{$iblockId}:</h2>";
echo "<ul>";
foreach ($propertyList as $code => $name) {
    echo "<li><strong>{$code}</strong>: {$name}</li>";
}
echo "</ul><hr>";

// Параметры выборки
$arSelect = [
    "ID",
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "DETAIL_PAGE_URL",
    "IBLOCK_ID"
];

$arFilter = [
    "IBLOCK_ID" => $iblockId,
    "ACTIVE" => "Y",
    "ID" => $elementId
];

$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

if ($arElement = $res->GetNext()) {
    // Получаем свойства элемента
    $props = CIBlockElement::GetProperty($iblockId, $arElement["ID"], [], []);
    $properties = [];
    while ($prop = $props->Fetch()) {
        if ($prop["MULTIPLE"] == "Y") {
            $properties[$prop["CODE"]][] = $prop["VALUE"];
        } else {
            $properties[$prop["CODE"]] = $prop["VALUE"];
        }
    }

    // Картинка
    $previewImg = '';
    if (!empty($arElement["PREVIEW_PICTURE"])) {
        $imgPath = CFile::GetPath($arElement["PREVIEW_PICTURE"]);
        $previewImg = "<img src='{$imgPath}' alt='{$arElement["NAME"]}' width='100'>";
    }

    // Вывод
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px'>";
    echo "<h3>{$arElement['NAME']}</h3>";
    echo $previewImg;
    echo "<p><strong>Описание:</strong> {$arElement['PREVIEW_TEXT']}</p>";
    echo "<ul>";
    foreach ($propertyList as $code => $name) {
        $value = isset($properties[$code]) ? $properties[$code] : "<em>не задано</em>";
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        echo "<li><strong>{$name} ({$code}):</strong> {$value}</li>";
    }
    echo "</ul>";
    echo "</div>";
} else {
    echo "<p style='color:red;'>Элемент с ID {$elementId} не найден или неактивен.</p>";
}
?>
