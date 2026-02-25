<?php
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

require_once __DIR__ . '/function.php';
class EventsCatalog
{
    private $iblockId = 33; // ID инфоблока

    public function UpdateElement($arrFileds)
    {
        if (!isset($arrFileds['id_element']) || !\CModule::IncludeModule("iblock")) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Невозможно обновить элемент. Отсутствует ID или не загружен модуль инфоблоков.',
                'elementID' => $arrFileds
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $elementId = (int)$arrFileds['id_element'];
        $result = $this->saveElement($elementId, $arrFileds);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function CreateElement($arrFileds = [])
    {
        if (!\CModule::IncludeModule("iblock")) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка: Не удалось загрузить модуль инфоблоков.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = $this->saveElement(null, $arrFileds);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    /*
    private function saveElement($elementId = null, $arrFileds)
    {
        $el = new \CIBlockElement;

        // Подготовка данных для сохранения
        $elementFields['IBLOCK_ID'] = $this->iblockId;
        $elementFields['NAME'] = $arrFileds['element_name'];
        $elementFields['PREVIEW_TEXT'] = trim($arrFileds['PREVIEW_TEXT']);
        $elementFields['PROPERTY_VALUES'] = [
            'ART' => $arrFileds['art'],
            'BRAND' => $arrFileds['brand'],
            'RRP' => $arrFileds['price'],
            'DISTROY_TYPE' => $arrFileds['DISTROY_TYPE'],
            'COUNT' => $arrFileds['COUNT'],
            'ONE_KAT' => $arrFileds['ONE_KAT'],
            'TWO_KAT' => $arrFileds['TWO_KAT'],
            'THREE_KAT' => $arrFileds['THREE_KAT'],
            'FOUR_KAT' => $arrFileds['FOUR_KAT'],
            'STATUS' => $arrFileds['STATUS'],
            'SINGLE' => $arrFileds['SINGLE'],
            'PART' => $arrFileds['PART'],
            'KAT_CATEGORY' => $arrFileds['KAT_CATEGORY'],
            'CATEGORY'=> $arrFileds['CATEGORY']
        ];

        // Сохранение элемента (обновление или добавление)
        if ($elementId) {


            if (!$el->Update($elementId, $elementFields)) {
                return [
                    'status' => 'error',
                    'message' => 'Ошибка при обновлении элемента: ' . $el->LAST_ERROR
                ];
            }

        } else {
            $elementId = $el->Add($elementFields, false, true, true);
            if (!empty($arrFileds['PREW_IMG'])) {
                $prevImg = \CFile::MakeFileArray($arrFileds['PREW_IMG'][0]['tmp_name']);
                \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $prevImg);
            }else{
                // Поиск элемента по ART в инфоблоке с ID 7
                \Bitrix\Main\Loader::includeModule('iblock');
                $res = \CIBlockElement::GetList([], [
                    'IBLOCK_ID' => 7,
                    'ACTIVE' => 'Y',
                    'PROPERTY_CML2_ARTICLE' => $arrFileds['art']
                ], false, false, ['ID', 'PREVIEW_PICTURE']);

                if ($arSimilar = $res->Fetch()) {
                    if ($arSimilar['PREVIEW_PICTURE']) {
                        // Устанавливаем PREVIEW_PICTURE
                        $picArray = \CFile::MakeFileArray($arSimilar['PREVIEW_PICTURE']);
                        $el->Update($elementId, ['PREVIEW_PICTURE' => $picArray]);

                        // Также устанавливаем в свойство PHOTO
                        \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $picArray);
                    }
                }
            }


            foreach ($arrFileds['file_name'] as $file) {
                $f = \CFile::MakeFileArray($file['tmp_name']);
                \CIBlockElement::SetPropertyValueCode($elementId, "CARUSEL", $f);
            }

            if (!$elementId) {
                return [
                    'status' => 'error',
                    'message' => 'Ошибка при создании элемента: ' . $el->LAST_ERROR
                ];
            }
        }

        // Устанавливаем цену товара из первого заполненного поля КАТ
        \Bitrix\Main\Loader::includeModule('catalog');
        if (!empty($arrFileds['PREW_IMG'])) {
            $prevImg = \CFile::MakeFileArray($arrFileds['PREW_IMG'][0]['tmp_name']);

            \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $prevImg);
        }
        $price = $arrFileds['PRICE_KAT'] ?? null; // Получаем цену для записи
        if ($price !== null) {
            // Определяем ID группы цен для "Розничной цены"
            $priceGroupId = 1; // Предполагается, что 2 — это ID "Розничной цены". Уточните в админке.

            $priceFields = [
                "PRODUCT_ID" => $elementId,
                "CATALOG_GROUP_ID" => $priceGroupId,
                "PRICE" => $price,
                "CURRENCY" => "KZT" // Укажите валюту
            ];

            $existingPrice = \CPrice::GetList(
                [],
                ["PRODUCT_ID" => $elementId, "CATALOG_GROUP_ID" => $priceGroupId]
            )->Fetch();

            if ($existingPrice) {
                \CPrice::Update($existingPrice['ID'], $priceFields); // Обновляем, если цена существует
            } else {
                \CPrice::Add($priceFields); // Добавляем новую цену, если её нет
            }
        }

        return [
            'status' => 'success',
            'message' => $elementId ? 'Элемент успешно обновлен' : 'Элемент успешно создан',
            'element_id' => $elementId
        ];
    }

    */
    private function saveElement($elementId = null, $arrFileds)
    {

        $el = new \CIBlockElement;

        $elementFields['IBLOCK_ID'] = $this->iblockId;
        $elementFields['NAME'] = $arrFileds['element_name'];
        $elementFields['PREVIEW_TEXT'] = trim($arrFileds['PREVIEW_TEXT']);
        $elementFields['PROPERTY_VALUES'] = [
            'ART' => $arrFileds['art'],
            'BRAND' => $arrFileds['brand'],
            'RRP' => $arrFileds['price'],
            'DISTROY_TYPE' => $arrFileds['DISTROY_TYPE'],
            'COUNT' => $arrFileds['COUNT'],
            'ONE_KAT' => $arrFileds['ONE_KAT'],
            'TWO_KAT' => $arrFileds['TWO_KAT'],
            'THREE_KAT' => $arrFileds['THREE_KAT'],
            'FOUR_KAT' => $arrFileds['FOUR_KAT'],
            'STATUS' => $arrFileds['STATUS'],
            'SINGLE' => $arrFileds['SINGLE'],
            'PART' => $arrFileds['PART'],
            'KAT_CATEGORY' => $arrFileds['KAT_CATEGORY'],
            'CATEGORY' => $arrFileds['CATEGORY']
        ];

        if ($elementId) {

            // Устанавливаем PREVIEW_PICTURE, если оно отсутствует и найден аналог в ИБ 7
            if (empty($arrFileds['PREW_IMG'][0]['tmp_name'])) {
                $res = \CIBlockElement::GetList([], [
                    'IBLOCK_ID' => 7,

                    'PROPERTY_CML2_ARTICLE' => $arrFileds['art']
                ], false, false, ['ID', 'PREVIEW_PICTURE']);

                if ($arSimilar = $res->Fetch()) {

                    if ($arSimilar['PREVIEW_PICTURE']) {
                        $picArray = \CFile::MakeFileArray($arSimilar['PREVIEW_PICTURE']);
                        $elementFields['PREVIEW_PICTURE'] = $picArray;

                        \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $picArray);
                    }
                }
//                print_r($arSimilar);
            } else {
                $prevImg = \CFile::MakeFileArray($arrFileds['PREW_IMG'][0]['tmp_name']);
                $elementFields['PREVIEW_PICTURE'] = $prevImg;
                \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $prevImg);
            }

            if (!$el->Update($elementId, $elementFields)) {
                return [
                    'status' => 'error',
                    'message' => 'Ошибка при обновлении элемента: ' . $el->LAST_ERROR
                ];
            }
        } else {
            $elementId = $el->Add($elementFields, false, true, true);

            if (!$elementId) {
                return [
                    'status' => 'error',
                    'message' => 'Ошибка при создании элемента: ' . $el->LAST_ERROR
                ];
            }

            if (!empty($arrFileds['PREW_IMG'][0]['tmp_name'])) {
                $prevImg = \CFile::MakeFileArray($arrFileds['PREW_IMG'][0]['tmp_name']);
                \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $prevImg);
                $el->Update($elementId, ['PREVIEW_PICTURE' => $prevImg]);
            } else {
                $res = \CIBlockElement::GetList([], [
                    'IBLOCK_ID' => 7,
                    'PROPERTY_CML2_ARTICLE' => $arrFileds['art']
                ], false, false, ['ID', 'PREVIEW_PICTURE']);

                if ($arSimilar = $res->Fetch()) {
                    if ($arSimilar['PREVIEW_PICTURE']) {
                        $picArray = \CFile::MakeFileArray($arSimilar['PREVIEW_PICTURE']);

                     
                        $el->Update($elementId, ['PREVIEW_PICTURE' => $picArray]);
                        \CIBlockElement::SetPropertyValueCode($elementId, "PHOTO", $picArray);
                    }
                }
            }

            foreach ($arrFileds['file_name'] as $file) {
                $f = \CFile::MakeFileArray($file['tmp_name']);
                \CIBlockElement::SetPropertyValueCode($elementId, "CARUSEL", $f);
            }
        }

        // Устанавливаем цену
        \Bitrix\Main\Loader::includeModule('catalog');

        $price = $arrFileds['PRICE_KAT'] ?? null;
        if ($price !== null) {
            $priceGroupId = 1;

            $priceFields = [
                "PRODUCT_ID" => $elementId,
                "CATALOG_GROUP_ID" => $priceGroupId,
                "PRICE" => $price,
                "CURRENCY" => "KZT"
            ];

            $existingPrice = \CPrice::GetList(
                [],
                ["PRODUCT_ID" => $elementId, "CATALOG_GROUP_ID" => $priceGroupId]
            )->Fetch();

            if ($existingPrice) {
                \CPrice::Update($existingPrice['ID'], $priceFields);
            } else {
                \CPrice::Add($priceFields);
            }
        }

        return [
            'status' => 'success',
            'message' => $elementId ? 'Элемент успешно обновлен' : 'Элемент успешно создан',
            'element_id' => $elementId
        ];
    }

    public function deleteElement($elementID)
    {
        if (empty($elementID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Не передан , либо неверный ID',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $el = new \CIBlockElement;
        $updateFields = [
            'ACTIVE' => 'N',
        ];

        if ($el->Update($elementID, $updateFields)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Элемент деактивирован ID ' . $elementID,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка при деактивации элемента: ' . $el->LAST_ERROR,
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    public function showItem($itemId)
    {
        $TEMPLATE_PATH = SITE_TEMPLATE_PATH;
        if ($itemId && \CModule::IncludeModule("iblock") && \CModule::IncludeModule("catalog")) {
            $element = \CIBlockElement::GetByID($itemId);
            if ($elementData = $element->GetNext()) {
                $arrFileds = [];
                $existingImages = [];

                // Получаем значения свойств
                $properties = \CIBlockElement::GetProperty($elementData['IBLOCK_ID'], $itemId, [], []);
                while ($property = $properties->GetNext()) {
                    $arrFileds[$property['CODE']] = $property['VALUE'];

                    if ($property['CODE'] === 'CARUSEL' && !empty($property['VALUE'])) {
                        $existingImages[] = [
                            'ID' => $property['VALUE'],
                            'SRC' => \CFile::GetPath($property['VALUE'])
                        ];
                    }
                    if ($property['CODE'] === 'PHOTO' && !empty($property['VALUE'])) {
                        $prevImage = [
                            'ID' => $property['VALUE'],
                            'SRC' => \CFile::GetPath($property['VALUE'])
                        ];
                    }
                }
                if (!empty($arrFileds['KAT_CATEGORY'])) {
                    $katCategoryId = $arrFileds['KAT_CATEGORY'];
                    $katCategoryEnum = \CIBlockPropertyEnum::GetByID($katCategoryId);
                    $arrFileds['KAT_CATEGORY'] = $katCategoryEnum['VALUE'] ?? 'Категория уценки не указана';
                } else {
                    $arrFileds['KAT_CATEGORY'] = 'Категория уценки не указана';
                }
                // Получаем цену
                $price = null;
                $resPrice = \CPrice::GetList([], ["PRODUCT_ID" => $itemId]);
                if ($priceData = $resPrice->Fetch()) {
                    $price = formatPrice($priceData['PRICE']);
                }
                if(!empty($arrFileds['RRP'])){
                    $arrFileds['RRP'] = formatPrice($arrFileds['RRP']);
                }
                // Обрабатываем изображения
                $images = [];
                $prevImgHtml = '';

                $prevImg = \CFile::GetPath($elementData['PREVIEW_PICTURE'] ) ?? $prevImage['SRC'];
                if (!empty($prevImg)) {
//                    $images[] = "<img src='{$prevImg}' alt='{$elementData['NAME']}'>";
                    $prevImgHtml = "<img src='{$prevImg}' alt='{$elementData['NAME']}'>";
                }else{
                    $prevImgHtml = "<img src='{$TEMPLATE_PATH}/assets/img/no_photo.svg' alt='{$elementData['NAME']}'>";
                }

                foreach ($existingImages as $image) {
                    if(!empty($image['SRC'])){
                        $images[] = "<a href='{$image['SRC']}' class='item' data-caption='{$arrFileds['DISTROY_TYPE']['TEXT']}' data-fancybox='gallery'><img src='{$image['SRC']}' alt='{$elementData['NAME']}'></a>";
                    }

                }
                if(empty($images)){
                    $images[] = "<img src='{$TEMPLATE_PATH}/assets/img/sale_photo.jpg' alt='{$elementData['NAME']}' style='object-fit: contain'>";
                }
                // Значения по умолчанию
                $defaults = [
                    'CATEGORY' => 'не указанна',
                    'PART' => 'не указан',
                    'BRAND' => 'не указан',
                    'ART' => 'не указан',
                    'RRP' => 'нет цены',
                    'KAT_CATEGORY' => 'не указанна',

                ];

                foreach ($defaults as $key => $default) {
                    $arrFileds[$key] = $arrFileds[$key] ?? $default;
                }

                $price = $price ?? 'нет цены';
                if($arrFileds['KAT_CATEGORY'] == "КАТ-1"){
                    $infoHtml = '
                      <b>1 категория уценки</b> - Любые повреждения упаковки
                     (помятости, срыв стикера и т.д.), при этом товар
                      не имеет внешних повреждений, исправен,
                      укомплектован на 100% и выполняет все заявленные производителем функции';
                }else if($arrFileds['KAT_CATEGORY'] == "КАТ-2"){
                    $infoHtml = '
                    <b>2 категория уценки</b> - Имеются видимые следы повреждения товара (царапины, потертости, незначительные сколы и вмятины и т.д.), при этом товар исправен, укомплектован на 100% и выполняет все заявленные производителем функции 
                    ';
                }else if($arrFileds['KAT_CATEGORY'] == "КАТ-3"){
                    $infoHtml = '
                    <b>3 категория уценки</b> - Бывший в употреблении товар, имеются следы использования, некомплект, при этом товар исправен и частично или полностью выполняет все заявленные производителем функции 
                    ';
                }else if($arrFileds['KAT_CATEGORY'] == "КАТ-4"){
                    $infoHtml = '
                    <b>4 категория уценки</b> - Товар после сервиса, производилось внутреннее, программное или иное вмешательство 
                    ';
                }else{

                }

                $prevText = !empty($elementData['PREVIEW_TEXT']) ? $elementData['PREVIEW_TEXT'] : 'Нет описания товара';
                $elementData['PREV_IMG'] = $prevImgHtml;
                $elementData['PREV_TEXT'] = html_entity_decode( $prevText);
                $elementData['BRAND'] = $arrFileds['BRAND'];
                $elementData['ART'] = $arrFileds['ART'];
                $elementData['PART'] = $arrFileds['PART'];
                $elementData['CATEGORY'] = $arrFileds['CATEGORY'];
                $elementData['PRICE'] = $price;
                $elementData['PRICE'] = $price;
                $elementData['RRP'] = $arrFileds['RRP'];
                $elementData['INFO_HTML'] = check_user_group() ? $infoHtml : '';
                $elementData['CARUSEL'] = $images;
                $elementData['DISTROY_TEXT'] = $arrFileds['DISTROY_TYPE']['TEXT'];
                include_once ( __DIR__ . '/view_item.php');?>
                <?php
            }
        }
    }


    //Вывод фото
    public function ShowPhoto($itemId)
    {
        $TEMPLATE_PATH = SITE_TEMPLATE_PATH;
        if ($itemId && \CModule::IncludeModule("iblock") && \CModule::IncludeModule("catalog")) {
            $element = \CIBlockElement::GetByID($itemId);
            if ($elementData = $element->GetNext()) {
                $arrFileds = [];
                $existingImages = [];

                // Получаем значения свойств
                $properties = \CIBlockElement::GetProperty($elementData['IBLOCK_ID'], $itemId, [], []);
                while ($property = $properties->GetNext()) {
                    $arrFileds[$property['CODE']] = $property['VALUE'];

                    if ($property['CODE'] === 'CARUSEL' && !empty($property['VALUE'])) {
                        $existingImages[] = [
                            'ID' => $property['VALUE'],
                            'SRC' => \CFile::GetPath($property['VALUE'])
                        ];
                    }
                    if ($property['CODE'] === 'PHOTO' && !empty($property['VALUE'])) {
                        $prevImage = [
                            'ID' => $property['VALUE'],
                            'SRC' => \CFile::GetPath($property['VALUE'])
                        ];
                    }
                }

                // Обрабатываем изображения
                $images = [];
                $prevImgHtml = '';

                $prevImg = \CFile::GetPath($elementData['PREVIEW_PICTURE'] ) ?? $prevImage['SRC'];
                if (!empty($prevImg)) {
//                    $images[] = "<img src='{$prevImg}' alt='{$elementData['NAME']}'>";
                    $prevImgHtml = "<img src='{$prevImg}' alt='{$elementData['NAME']}'>";
                }else{
                    $prevImgHtml = "<img src='{$TEMPLATE_PATH}/assets/img/no_photo.svg' alt='{$elementData['NAME']}'>";
                }

                foreach ($existingImages as $image) {
                    if(!empty($image['SRC'])){
                        $images[] = "<a href='{$image['SRC']}' class='item' data-caption='{$arrFileds['DISTROY_TYPE']['TEXT']}'  data-fancybox='gallery'><img src='{$image['SRC']}' alt='{$elementData['NAME']}'></a>";
                    }

                }
                if(empty($images)){
                    $images[] = "<img src='{$TEMPLATE_PATH}/assets/img/sale_photo.jpg' alt='{$elementData['NAME']}' style='object-fit: contain'>";
                }


                $prevText = !empty($elementData['PREVIEW_TEXT']) ? $elementData['PREVIEW_TEXT'] : 'Нет описания товара';
                $elementData['PREV_IMG'] = $prevImgHtml;
                $elementData['PREV_TEXT'] = html_entity_decode( $prevText);
                $elementData['CATEGORY'] = $arrFileds['CATEGORY'];

                $elementData['CARUSEL'] = $images;
                $elementData['DISTROY_TEXT'] = $arrFileds['DISTROY_TYPE']['TEXT'];
                include_once ( __DIR__ . '/view_item_photo.php');?>
                <?php
            }
        }
    }
    //Отрисовка формы
    public function RenderForm($id = '', $name = '', $arrFileds = []) {
        if (!check_user()) {
            return false;
        }
        $arrFiledsForm = [];

        if (!empty($id)){
            $arSelect = Array(
                "ID",
                "NAME",
                "CATALOG_GROUP_1",
                "IBLOCK_ID",
                "PREVIEW_PICTURE",
                "PREVIEW_TEXT",
                "PROPERTY_ART",
                "PROPERTY_BRAND",
                "PROPERTY_RRP",
                "PROPERTY_DISTROY_TYPE",
                "PROPERTY_COUNT",
                "PROPERTY_STATUS",
                "PROPERTY_PART",
                "PROPERTY_SINGLE",
                "PROPERTY_KAT_CATEGORY",
                "PROPERTY_CARUSEL",
                "PROPERTY_PHOTO",
                "PROPERTY_CATEGORY",

            );
            $arFilter = Array(
                "IBLOCK_ID"=> 33,
                "ACTIVE"=>"Y",
                'ID'=> $id,
            );
            $res = \CIBlockElement::GetList(
                Array(),
                $arFilter,
                false,
                false,
                $arSelect
            );
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();

                $name = $arFields['NAME'];
                $arrFiledsForm['element_name'] = $arFields['NAME'];
                $arrFiledsForm['PREVIEW_TEXT'] = $arFields['PREVIEW_TEXT'];
                $arrFiledsForm['art'] = $arFields['PROPERTY_ART_VALUE'] ?? '';  // Артикул
                $arrFiledsForm['PRICE_KAT'] = $arFields['CATALOG_PRICE_1'] ?? '';  // Цена уценки
                $arrFiledsForm['brand'] = $arFields['PROPERTY_BRAND_VALUE'] ?? '';  // Бренд
                $arrFiledsForm['price'] = $arFields['PROPERTY_RRP_VALUE'] ?? '';  // Цена РРЦ
                $arrFiledsForm['distroy'] = trim($arFields['PROPERTY_DISTROY_TYPE_VALUE']['TEXT']) ?? '';
                $arrFiledsForm['COUNT'] = trim($arFields['PROPERTY_COUNT_VALUE']) ?? '';
                $arrFiledsForm['STATUS'] = trim($arFields['PROPERTY_STATUS_VALUE']) ?? '';
                $arrFiledsForm['PART'] = trim($arFields['PROPERTY_PART_VALUE']) ?? '';
                $arrFiledsForm['SINGLE'] = trim($arFields['PROPERTY_SINGLE_VALUE']) ?? '';
                $arrFiledsForm['PHOTO'] = \CFile::GetPath($arFields['PROPERTY_PHOTO_VALUE']) ?? \CFile::GetPath($arFields['PREVIEW_PICTURE']);
                $arrFiledsForm['KAT_CATEGORY'] = trim($arFields['PROPERTY_KAT_CATEGORY_VALUE']) ?? '';
                $arrFiledsForm['CATEGORY'] = trim($arFields['PROPERTY_CATEGORY_VALUE']) ?? '';
                $arrFiledsForm['PROPERTY_KAT_CATEGORY_ENUM_ID'] = trim($arFields['PROPERTY_KAT_CATEGORY_ENUM_ID']) ?? '';
            }


        }
        if(!empty($arrFiledsForm['PHOTO'])){
            $prevImg = "<img src='{$arrFiledsForm['PHOTO']}' alt=''>";
        }else{
            $prevImg = '';
        }

        $singleActive = '';
        if($arrFiledsForm['SINGLE'] == 'Y'){
            $singleActive = ' <option value=\'19682\' selected>Разделить</option> <option value=\'19683\'>Не делить</option>';
        }elseif ($arrFiledsForm['SINGLE'] == 'N'){
            $singleActive = ' <option value=\'19682\' >Разделить</option> <option value=\'19683\' selected>Не делить</option>';
        }else{
            $singleActive = ' <option value=\'19682\' >Разделить</option> <option value=\'19683\'>Не делить</option>';
        }

        $katHtml = '';
        switch ($arrFiledsForm['PROPERTY_KAT_CATEGORY_ENUM_ID']){
            case 19684:
                $katHtml = "
                    <option value='19684' selected>1 категория</option>
                    <option value='19685'>2 категория</option>
                    <option value='19686'>3 категория</option>
                    <option value='19687'>4 категория</option>
                ";
                break;
            case 19685:
                $katHtml = "
                    <option value='19684' >1 категория</option>
                    <option value='19685' selected>2 категория</option>
                    <option value='19686'>3 категория</option>
                    <option value='19687'>4 категория</option>
                ";
                break;
            case 19686:
                $katHtml = "
                    <option value='19684' >1 категория</option>
                    <option value='19685' >2 категория</option>
                    <option value='19686' selected>3 категория</option>
                    <option value='19687'>4 категория</option>
                ";
                break;
            case 19687:
                $katHtml = "
                    <option value='19684' >1 категория</option>
                    <option value='19685' >2 категория</option>
                    <option value='19686' >3 категория</option>
                    <option value='19687' selected>4 категория</option>
                ";
                break;
            default:
                $katHtml = "
                    <option value='19684' >1 категория</option>
                    <option value='19685' >2 категория</option>
                    <option value='19686' >3 категория</option>
                    <option value='19687' >4 категория</option>
                ";

        }
        if(!empty($id)){
            $inputUpload = "<input class=\"form-upload__input\" id=\"uploadForm_File\" type=\"file\"   name=\"file_name[]\" accept=\"image/*\" multiple>";
        }else{
            $inputUpload = "<input class=\"\" id=\"uploadForm_File\" type=\"file\"  name=\"file_name[]\" accept=\"image/*\"  multiple>";
        }
        $prevText = trim($arrFiledsForm['PREVIEW_TEXT']);

        echo json_encode([
            'status' => 'success',
            'message' => "
            <div class='ajax_form_update'>
                <h3> {$name}</h3>
                <div class='close' id='close_ajax_form'></div>
                <form method='POST' action='events.php' enctype='multipart/form-data' id='ajax_form_update'>
                    <div class='form_group'>
                        <label>
                            Наименование товара по базе 1с
                            <input type='text' name='element_name' placeholder='Название элемента' value='{$arrFiledsForm['element_name']}'>
                        </label>
                         <div class='row-3'>
                           <label>
                            Артикул
                            <input type='text' name='art' required placeholder='Артикул' value='{$arrFiledsForm['art']}'>
                            </label>
                            <label>
                            Бренд
                            <input type='text' name='brand' placeholder='Брэнд' value='{$arrFiledsForm['brand']}'>
                            </label>
                             <label for=''>
                               Серийный номер
                                <input type='text' name='PART'  value='{$arrFiledsForm['PART']}'>
                            </label>
                            <label for='' style='display: none'>
                               Подгатегория товара
                                <input type='text' name='CATEGORY'  value='{$arrFiledsForm['CATEGORY']}'>
                            </label>
                            
                            
                        </div>
                         <div class='row-3'>
                            <label for=''>
                             Категория уценки
                             <select name='KAT_CATEGORY' id=''>
                              {$katHtml}
                             </select>
                            </label>
                            <label for=''>
                            Цена уценки
                             <input name='PRICE_KAT' type='number' placeholder='' value='{$arrFiledsForm['PRICE_KAT']}'>
                            </label>
                            <label>
                            Цена РРЦ
                            <input name='price' type='number' placeholder='Цена РРЦ' value='{$arrFiledsForm['price']}'>
                        </label>
                         </div>
                        <div class='row-flex '>
                        <div class='row-2'>
                         <label>
                          Количество
                            <input name='COUNT' type='text' placeholder='' value='{$arrFiledsForm['COUNT']}'>
                        </label>


                        </div>
                        
                        </div>
                        <label>
                            Комментарий уценки
                            <textarea name=\"DISTROY_TYPE\" id=\"DISTROY_TYPE\" value='{$arrFiledsForm['distroy']}' class=\"form-control\"> {$arrFiledsForm['distroy']}</textarea>
                        </label>
                        <label for=''>
                            Описание анонса
                            <textarea name=\"PREVIEW_TEXT\" value='{$prevText}' class=\"form-control\">{$prevText}</textarea>
                        </label>
                        <label for=''>
                        Изображение анонса (подгружает автоматически если есть в основном каталоге)
                          <div class='prev_img'>
                            {$prevImg}
                           </div>
                        <input type='file' name='PREW_IMG'>
                        </label>
                     <label id='photo_destroy'>
                        Фото товара уценки
                        <div class='loader'>
                        </div>
                        <div class='image_list_destroy'>

                        "

                        . $this->GetPhotoList($id)
                        ."
                        </div>
                        <div id=\"uploadFile_Loader\" class=\"upload-zone\">

                          <div class=\"form-upload\" id=\"uploadForm\">
                            <div class=\"upload-zone_dragover\">
                              <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" stroke=\"currentColor\"
                               stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1\" viewBox=\"0 0 24 2\4\" class=\"upload-loader__image\">
                                <path d=\"M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242M12 12v9\"/>
                                <path d=\"m16 16-4-4-4 4\"/>
                              </svg>
                              <p>Перетащи файл сюда</p>
                              <span class=\"form-upload__hint\" id=\"hint\">Можно загружать только картинки</span>
                            </div>
                            <label class=\"form-upload__label\" for=\"uploadForm_file\">
                              <span class=\"form-upload__title\"></span>
                              {$inputUpload}
                            </label>
                            <div class=\"form-upload__container\">
                              <span class=\"form-upload__hint\" id=\"uploadForm_Hint\"></span>
                            </div>
                          </div>
                        </div>
                    </label>
                    </div>
                    <button class='btn btn-save btn-default'>Сохранить</button>
                    <input type='hidden' name='id_element' value='{$id}'>
                </form>
            </div>
        ",
            'arr' => $arrFileds
        ], JSON_UNESCAPED_UNICODE);
    }

    //Получение  фото
    function GetPhotoList($element_id)
    {
        if(empty($element_id)){
            return ;
        }
        $arSelect = [
            "ID",
            "NAME",
            "IBLOCK_ID",
        ];
        $arFilter = [
            "IBLOCK_ID" => 33,
            "ACTIVE" => "Y",
            "ID" => $element_id,
        ];
        $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        $existingImages = [];
        while ($ob = $res->GetNextElement()) {
            $arProperties = $ob->GetProperties();
            if (!empty($arProperties['CARUSEL']['VALUE'])) {
                foreach ($arProperties['CARUSEL']['VALUE'] as $fileId) {
                    $file = \CFile::GetFileArray($fileId);
                    if ($file) {
                        $existingImages[] = [
                            'ID' => $fileId,
                            'SRC' => $file['SRC'],
                            'NAME' => $file['ORIGINAL_NAME'],
                        ];
                    }
                }
            }
        }
        $imageBlock = '';
        foreach ($existingImages as $img){
        $imageBlock .=
            "<div class='image_item' id='{$img["ID"]}'>
                <img src='{$img["SRC"]}' alt='{$img["NAME"]}'>
                <div class='remove_img'></div>
            </div>"
       ;

        }
        return $imageBlock;
    }

    //Добавление фото
    public function addPhotoDestroy($arr)
    {
        if (!\CModule::IncludeModule("iblock")) {
            echo json_encode([
                'status' => 'error',
                'message' => "Модуль инфоблоков не подключен."
            ]);
            return;
        }

        $iblockId = 33; // ID инфоблока
        $elementId = $arr['ID'] ?? null; // ID элемента
        $propertyCode = "PROPERTY_CARUSEL"; // Код свойства

        // Проверяем входные данные
        if (!$elementId || empty($_FILES['file'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Некорректные данные. Проверьте ID элемента и файлы.'
            ]);
            return;
        }
        $file = \CFile::MakeFileArray($arr['file']['tmp_name']);
        if( \CIBlockElement::SetPropertyValueCode($elementId, "CARUSEL", $file)){
            echo json_encode([
                'status' => 'success',
                'message' => "Файл успешно добавлен.",
                "response" => $this->GetPhotoList($elementId)
            ]);
        }

    }

    //Удаление фото
   /* public function removePhotoDestroy($element_id, $file_id)
    {
        if (!\CModule::IncludeModule("iblock")) {
            echo json_encode([
                'status' => 'error',
                'message' => "Модуль инфоблоков не подключен."]);
            return ;
        }

        $iblockId = 33;

        // Получаем текущее значение свойства CARUSEL
        $currentFiles = [];
        $properties = \CIBlockElement::GetProperty($iblockId, $element_id, [], ['CODE' => 'CARUSEL']);
        while ($property = $properties->GetNext()) {
            if ((int)$property['VALUE'] !== (int)$file_id) {
                // Добавляем только ID оставшихся файлов
                if(!empty($property['VALUE'])){
                    $currentFiles[] = $property['VALUE'];
                }

            }
        }

        // Если больше нет файлов, передаем null для очистки свойства
        $newPropertyValue = !empty($currentFiles) ? $currentFiles : false;

        // Обновляем свойство CARUSEL
        \CIBlockElement::SetPropertyValuesEx(
            $element_id,
            $iblockId,
            ['CARUSEL' => $newPropertyValue]
        );

        // Удаляем сам файл из файловой системы
        \CFile::Delete($file_id);

        // Удаляем сам файл из файловой системы
        \CFile::Delete($file_id);
        echo json_encode([
            'status' => 'success',
            'message' => "Файл успешно удален.",
            "response" => $this->GetPhotoList($element_id)
        ]);
    }

*/

    public function removePhotoDestroy($element_id, $file_id)
    {
        if (!\CModule::IncludeModule("iblock")) {
            echo json_encode([
                'status' => 'error',
                'message' => "Модуль инфоблоков не подключен."
            ]);
            return;
        }

        $iblockId = 33;

        // Получаем текущее значение свойства CARUSEL
        $currentFiles = [];
        $properties = \CIBlockElement::GetProperty($iblockId, $element_id, [], ['CODE' => 'CARUSEL']);
        while ($property = $properties->GetNext()) {
            if (!empty($property['VALUE']) && (int)$property['VALUE'] !== (int)$file_id) {
                $currentFiles[] = (int)$property['VALUE'];
            }
        }

        // Если в массиве есть "пустые" или старые значения, фильтруем
        $currentFiles = array_filter($currentFiles, function ($file) {
            return !empty($file) && is_numeric($file);
        });

        // Если больше нет файлов, полностью удаляем свойство
        if (empty($currentFiles)) {
            \CIBlockElement::SetPropertyValuesEx($element_id, $iblockId, ["CARUSEL" => ["del" => "Y"]]);
        } else {
            \CIBlockElement::SetPropertyValuesEx($element_id, $iblockId, ["CARUSEL" => $currentFiles]);
        }

        // Принудительное обновление элемента для сброса кеша
        $el = new \CIBlockElement;
        $el->Update($element_id, ["TIMESTAMP_X" => date("d.m.Y H:i:s")]);

        // Удаляем сам файл из файловой системы
        \CFile::Delete($file_id);

        echo json_encode([
            'status' => 'success',
            'message' => "Файл успешно удален.",
            "response" => $this->GetPhotoList($element_id)
        ]);
    }


    public function copyElement($elementID)
    {
        if (empty($elementID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Не передан или неверный ID элемента',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!\CModule::IncludeModule("iblock") || !\CModule::IncludeModule("catalog")) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Не подключен модуль инфоблоков или каталогов.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Получение данных оригинального элемента
        $elementData = \CIBlockElement::GetByID($elementID)->GetNextElement();
        if (!$elementData) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Элемент с таким ID не найден',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $fields = $elementData->GetFields();
        $properties = $elementData->GetProperties();

        // Подготовка полей для нового элемента
        $newElementFields = [
            'IBLOCK_ID' => $fields['IBLOCK_ID'],
            'NAME' => $fields['NAME'],
            'ACTIVE' => $fields['ACTIVE'],
            'SORT' => $fields['SORT'],
            'PREVIEW_PICTURE' => \CFile::MakeFileArray($fields['PREVIEW_PICTURE']),
            'DETAIL_PICTURE' => \CFile::MakeFileArray($fields['DETAIL_PICTURE']),
        ];

        // Подготовка свойств для нового элемента
        $newProperties = [];
        foreach ($properties as $code => $property) {
            if ($property['PROPERTY_TYPE'] === 'L' && !empty($property['VALUE_ENUM_ID'])) {
                // Устанавливаем значение для свойств типа "список"
                $newProperties[$code] = ["VALUE" => $property['VALUE_ENUM_ID']];
            } else {
                $newProperties[$code] = $property['VALUE'];
            }
        }

        // Добавление нового элемента
        $el = new \CIBlockElement;
        $newElementFields['PROPERTY_VALUES'] = $newProperties;
        $newElementID = $el->Add($newElementFields);

        if ($newElementID) {
            // Копирование цены
            $priceData = \CPrice::GetList([], ['PRODUCT_ID' => $elementID])->Fetch();
            if ($priceData) {
                $priceFields = [
                    'PRODUCT_ID' => $newElementID,
                    'CATALOG_GROUP_ID' => $priceData['CATALOG_GROUP_ID'],
                    'PRICE' => $priceData['PRICE'],
                    'CURRENCY' => $priceData['CURRENCY']
                ];

                // Добавление цены для нового элемента
                $priceResult = \CPrice::Add($priceFields);
                if (!$priceResult) {
                    echo json_encode([
                        'status' => 'warning',
                        'message' => 'Элемент скопирован, но цена не была добавлена.',
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Элемент успешно скопирован',
                'newElementID' => $newElementID,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Ошибка при копировании элемента: ' . $el->LAST_ERROR,
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}


//<label for=''>
//Делить ?
//                            <select name='SINGLE' id=''>
//                              {$singleActive}
//                            </select>
//                        </label>