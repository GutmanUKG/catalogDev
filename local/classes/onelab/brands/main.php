<?
namespace Onelab;
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//$IBLOCK_ID = 20; //Инфоблок с брендами
//DESCR_KZ - Описание на каз
//DESCR_EN - Описание на англ
//PARENT_SECTION - Родительская категория
class BrandsEvent
{

    public function main($NEWS_IBLOCK_ID, $SECTION_NEWS, $ITEM_ID){
        $newsList = $this->GetNewsList($NEWS_IBLOCK_ID, $SECTION_NEWS);
        $categoriesList = $this->GetCategoryList($ITEM_ID);
        echo json_encode([
            'status' => 'ok',
            'news' => $newsList,
            'categories' => $categoriesList,
        ], JSON_UNESCAPED_UNICODE);
    }


    private function GetCategoryList($ITEM_ID)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            echo json_encode(['error' => 'Module iblock not found'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $arSelect = ['ID', 'IBLOCK_ID', 'PROPERTY_CATEGOTY_LIST'];
        $arFilter = ['IBLOCK_ID' => 20, 'ID' => $ITEM_ID];

        $res = \CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);

        $sectionIds = [];
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            if (!empty($arFields['PROPERTY_CATEGOTY_LIST_VALUE'])) {
                $sectionIds[] = $arFields['PROPERTY_CATEGOTY_LIST_VALUE'];
            }
        }

        // Убираем дубликаты ID секций
        $sectionIds = array_unique($sectionIds);

        // 1️⃣ Получаем иерархию секций и строим вложенную структуру (максимум 3 уровня)
        $sections = [];
        foreach ($sectionIds as $sectionId) {
            $hierarchy = $this->GetSectionHierarchy($sectionId, 3);
            if (!empty($hierarchy)) {
                $this->InsertSection($sections, $hierarchy);
            }
        }

        return [
            'categories' => array_values($sections)
        ];
    }
// 2️⃣ Функция для получения иерархии секций (ограничение в 3 уровня)
    function getSectionHierarchy($sectionId) {
        $hierarchy = [];
        $existingNames = [];
        $depth = 0; // Счётчик глубины
        while ($sectionId) {
            $sectionRes = \CIBlockSection::GetList([], ['ID' => $sectionId], false, ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'PICTURE']);
            if ($section = $sectionRes->Fetch()) {
                if (!in_array($section['NAME'], $existingNames)) {
                    $hierarchy[] = [
                        'ID' => $section['ID'],
                        'NAME' => $section['NAME'],
                        'PARENT_ID' => $section['IBLOCK_SECTION_ID'],
                        'PICTURE' => $section['PICTURE'] ? \CFile::GetPath($section['PICTURE']) : null, // Проверяем наличие изображения
                    ];
                    $existingNames[] = $section['NAME'];
                }
                $sectionId = $section['IBLOCK_SECTION_ID'];
            } else {

                break;
            }
            $depth++; // Увеличиваем глубину вложенности (уже не нужно )
        }

        return array_reverse($hierarchy); // Reverse the hierarchy to show from top-level to bottom
    }



// 3️⃣ Функция для вложенного построения дерева (с правильным `CHILD` как массив)
    private function InsertSection(&$tree, $hierarchy)
    {
        $current = &$tree;

        foreach ($hierarchy as $section) {
            $sectionId = $section['ID'];

            // Проверяем, существует ли уже эта секция
            $found = false;
            foreach ($current as &$child) {
                if ($child['ID'] == $sectionId) {
                    $current = &$child['CHILD'];
                    $found = true;
                    break;
                }
            }

            // Если секции ещё нет, добавляем её
            if (!$found) {
                $newSection = [
                    'ID' => $sectionId,
                    'NAME' => $section['NAME'],
                    'CHILD' => [],
                    'img' => $section['PICTURE'] // Используем уже полученный путь, а не повторно вызываем CFile::GetPath()
                ];
                $current[] = $newSection;
                $current = &$current[array_key_last($current)]['CHILD'];
            }
        }

        // Убираем пустые `CHILD`
        foreach ($tree as &$node) {
            if (empty($node['CHILD'])) {
                unset($node['CHILD']);
            }
        }
    }



    //Получение новостей брэнда
    private function GetNewsList($NEWS_IBLOCK_ID, $SECTION_NEWS)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return json_encode(['error' => 'Module iblock not found'], JSON_UNESCAPED_UNICODE);
        }

        $arSelect = [
            'ID',
            'NAME',
            'DISPLAY_ACTIVE_FROM',
            'PREVIEW_TEXT',
            'ACTIVE_FROM',
            'DETAIL_PAGE_URL',
        ];
        $arFilter = [
            'IBLOCK_ID' => $NEWS_IBLOCK_ID,
            'SECTION_ID' => $SECTION_NEWS,
            'ACTIVE' => 'Y',
        ];

        $newsList = [];
        $res = \CIBlockElement::GetList(
            ['ACTIVE_FROM' => 'DESC'],  // Сортировка по дате (сначала свежие)
            $arFilter,
            false,
            ['nTopCount' => 10],  // Ограничение количества (можно изменить)
            $arSelect
        );

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arFields['DISPLAY_ACTIVE_FROM'] = \CIBlockFormatProperties::DateFormat('d.m.Y', MakeTimeStamp($arFields['ACTIVE_FROM']));
            $newsList[] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
                'PREVIEW_TEXT' => $arFields['PREVIEW_TEXT'],
                'ACTIVE_FROM' => $arFields['ACTIVE_FROM'],
                'DISPLAY_ACTIVE_FROM' => $arFields['DISPLAY_ACTIVE_FROM'],
                'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
            ];
        }

        return [
            'news' => $newsList,
        ];
    }


}



