<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/components/onelab/user.table/lib/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// Подключение модуля инфоблоков, пользователей и интернет-магазина
if (CModule::IncludeModule("iblock") && CModule::IncludeModule("main") && CModule::IncludeModule("sale")) {

    $iblockId = 32; // ID инфоблока

    // Массив для хранения пользователей и их элементов
    $usersWithElements = [];

    // Получение всех пользователей
    $rsUsers = CUser::GetList(
        ($by = "ID"), // Сортировка по ID
        ($order = "ASC"), // Порядок сортировки
        [
            "ACTIVE" => "Y",
            "BLOCKED"=> "Y"
        ], // Фильтрация по пользователям
        [
            'FIELDS' => ['ID', 'NAME', 'LAST_NAME', 'DATE_REGISTER', 'WORK_COMPANY', 'BLOCKED'], // Поля, которые необходимо получить
            'SELECT' => ['UF_MANAGER' , 'UF_COMMENT']
        ] // Поля, которые необходимо получить
    );

    while ($arUser = $rsUsers->Fetch()) {
        $userId = $arUser['ID'];

        // Получаем дату последнего заказа пользователя
        $lastOrderDate = getLastOrderDate($userId);
        $userResult = CUser::GetByID($userId);
        if ($userData = $userResult->Fetch()) {
            // Проверяем значение поля BLOCKED
            if ($userData['BLOCKED'] === 'Y') {
                continue; // Пропускаем заблокированных пользователей
            }
        }
        // Вычисляем разницу в днях
        if ($lastOrderDate) {
            // Если есть заказы, считаем количество дней с момента последнего заказа
            $daysSinceLastOrder = (new DateTime())->diff(new DateTime($lastOrderDate))->days;
        } else {
            // Если заказов нет, считаем количество дней с момента регистрации
            $registrationDate = new DateTime($arUser['DATE_REGISTER']);
            $currentDate = new DateTime();
            $daysSinceLastOrder = $currentDate->diff($registrationDate)->days;
        }
        $managerName = '';
        if (!empty($arUser['UF_MANAGER'])) {
            $managerId = $arUser['UF_MANAGER'];

            // Делаем запрос к инфоблоку для получения названия менеджера по ID
            $res = CIBlockElement::GetByID($managerId);
            if ($arManager = $res->GetNext()) {
                $managerName = $arManager['NAME']; // Название привязанного элемента
            }
        }

        // Инициализация пользователя с пустыми секциями
        $usersWithElements[$userId] = [
            "USER_ID" => $userId,
            'USER_NAME' => $arUser['NAME'] . ' ' . $arUser['LAST_NAME'], // Имя и фамилия пользователя
            'WORK_COMPANY' => $arUser['WORK_COMPANY'],// Название компании и фамилия пользователя
            'UF_MANAGER' => $managerName,
            'UF_COMMENT'=> $arUser['UF_COMMENT'],
            'DATE_REGISTER' => $arUser['DATE_REGISTER'], // Дата регистрации пользователя
            'DAYS_SINCE_LAST_ORDER' => $daysSinceLastOrder, // Дней с последнего заказа или регистрации
            'SECTIONS' => [], // Секции будут заполняться ниже
            'BLOCKED' => $arUser['BLOCKED'], // Секции будут заполняться ниже
        ];
    }
//    echo '<pre>';
//    print_r($usersWithElements);
//    echo '</pre>';
    // Получение всех секций инфоблока
    $sectionRes = CIBlockSection::GetList(
        ['SORT' => 'ASC'], // Сортировка по порядку
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'], // Фильтрация по активным секциям инфоблока
        false, // Не считаем количество элементов
        ['ID', 'NAME'] // Поля, которые необходимо получить
    );

    // Массив всех секций для будущего использования
    $allSections = [];
    while ($section = $sectionRes->GetNext()) {
        $allSections[$section['ID']] = $section['NAME'];
    }

    // Инициализация всех секций для каждого пользователя
    foreach ($usersWithElements as &$user) {
        foreach ($allSections as $sectionId => $sectionName) {
            $user['SECTIONS'][$sectionId] = [
                'SECTION_NAME' => $sectionName,
                'ELEMENTS' => [] // По умолчанию элементы пустые
            ];
        }
    }

    // Получение всех элементов инфоблока с привязкой к пользователям
    $elementRes = CIBlockElement::GetList(
        ['SORT' => 'ASC'], // Сортировка по порядку
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'], // Фильтрация по инфоблоку
        false, // Не используем пагинацию
        false, // Не считаем количество элементов
        ['ID', 'NAME', 'PROPERTY_USER', 'PROPERTY_DOCUMENT', 'IBLOCK_SECTION_ID'] // Поля, которые необходимо получить
    );

    // Привязка элементов к пользователям
    while ($element = $elementRes->GetNext()) {
        $userId = $element['PROPERTY_USER_VALUE']; // Получаем ID пользователя
        $sectionId = $element['IBLOCK_SECTION_ID']; // ID секции, к которой относится элемент
        $document = $element['PROPERTY_DOCUMENT_VALUE']; // Получаем значение свойства DOCUMENT (ID файла)

        if (isset($usersWithElements[$userId])) {
            // Если у пользователя есть привязанные элементы
            if ($document) {
                if (is_array($document)) {
                    // Множественные файлы
                    foreach ($document as $fileId) {
                        $filePath = CFile::GetPath($fileId);
                        $usersWithElements[$userId]['SECTIONS'][$sectionId]['ELEMENTS'][] = $filePath;
                    }
                } else {
                    // Одиночный файл
                    $filePath = CFile::GetPath($document);
                    $usersWithElements[$userId]['SECTIONS'][$sectionId]['ELEMENTS'][] = $filePath;
                }
            }
        }
    }

    // Сортировка по GET-параметру ?sort=asc или ?sort=desc
    if (isset($_GET['sort_date']) && ($_GET['sort_date'] == 'asc' || $_GET['sort_date'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $dateA = new DateTime($a['DATE_REGISTER']);
            $dateB = new DateTime($b['DATE_REGISTER']);

            if ($_GET['sort_date'] == 'asc') {
                return $dateA <=> $dateB; // Сортировка от новой к старой
            } else {
                return $dateB <=> $dateA; // Сортировка от старой к новой
            }
        });
    }

    //Сортировка по имени пользователя sort_name=asc - в алфавитом порядке sort_name=desc в обратном порядке
    if (isset($_GET['sort_name']) && ($_GET['sort_name'] == 'asc' || $_GET['sort_name'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $nameA = $a['USER_NAME'];
            $nameB = $b['USER_NAME'];

            if ($_GET['sort_name'] == 'asc') {
                return strcasecmp($nameA, $nameB); // Сортировка по алфавиту
            } else {
                return strcasecmp($nameB, $nameA); // Сортировка в обратном порядке
            }
        });
    }
    //Сортировка по имени менеджера sort_manager=asc - в алфавитом порядке sort_manager=desc в обратном порядке
    if (isset($_GET['sort_manager']) && ($_GET['sort_manager'] == 'asc' || $_GET['sort_manager'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $nameA = $a['UF_MANAGER'];
            $nameB = $b['UF_MANAGER'];

            if ($_GET['sort_manager'] == 'asc') {
                return strcasecmp($nameA, $nameB); // Сортировка по алфавиту
            } else {
                return strcasecmp($nameB, $nameA); // Сортировка в обратном порядке
            }
        });
    }
    //Сортировка по наличию коментария
    if (isset($_GET['sort_comment']) && ($_GET['sort_comment'] == 'asc' || $_GET['sort_comment'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $nameA = !empty($a['UF_COMMENT']);
            $nameB = !empty($b['UF_COMMENT']);

            if ($_GET['sort_comment'] == 'asc') {
               return $nameB <=> $nameA;
            } else {
                return $nameA <=> $nameB;
            }
        });
    }

    //Сортировка по количеству дней с последней покупки sort_days=asc от 0 и выше (так же false) а sort_days=desc наобрарот
    if (isset($_GET['sort_days']) && ($_GET['sort_days'] == 'asc' || $_GET['sort_days'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $daysA = $a['DAYS_SINCE_LAST_ORDER'];
            $daysB = $b['DAYS_SINCE_LAST_ORDER'];

            // Если один из пользователей не совершал покупок (false), ставим его в конец списка
            if ($daysA === false) {
                return ($_GET['sort_days'] == 'asc') ? 1 : -1;
            }
            if ($daysB === false) {
                return ($_GET['sort_days'] == 'asc') ? -1 : 1;
            }

            if ($_GET['sort_days'] == 'asc') {
                return $daysA <=> $daysB; // Сортировка по возрастанию (от 0 и выше)
            } else {
                return $daysB <=> $daysA; // Сортировка по убыванию (от больших значений к меньшим)
            }
        });
    }
    //Сортировка по наличию НДС sort_nds=asc - поля заполнены sort_nds=desc поля пусты
    if (isset($_GET['sort_nds']) && ($_GET['sort_nds'] == 'asc' || $_GET['sort_nds'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $ndsA = !empty($a['SECTIONS'][1699]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"
            $ndsB = !empty($b['SECTIONS'][1699]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"

            if ($_GET['sort_nds'] == 'asc') {
                // Сортировка: сначала те, у кого есть элементы НДС
                return $ndsB <=> $ndsA;
            } else {
                // Сортировка: сначала те, у кого нет элементов НДС
                return $ndsA <=> $ndsB;
            }
        });
    }
    //Сортировка по наличию документов регистрации sort_reg=asc - поля заполнены sort_reg=desc поля пусты
    if (isset($_GET['sort_reg']) && ($_GET['sort_reg'] == 'asc' || $_GET['sort_reg'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $ndsA = !empty($a['SECTIONS'][1700]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"
            $ndsB = !empty($b['SECTIONS'][1700]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"

            if ($_GET['sort_reg'] == 'asc') {
                // Сортировка: сначала те, у кого есть элементы НДС
                return $ndsB <=> $ndsA;
            } else {
                // Сортировка: сначала те, у кого нет элементов НДС
                return $ndsA <=> $ndsB;
            }
        });
    }
    //Сортировка по наличию документов договор sort_doc=asc - поля заполнены sort_doc=desc поля пусты
    if (isset($_GET['sort_doc']) && ($_GET['sort_doc'] == 'asc' || $_GET['sort_doc'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $ndsA = !empty($a['SECTIONS'][1702]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"
            $ndsB = !empty($b['SECTIONS'][1702]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"

            if ($_GET['sort_doc'] == 'asc') {
                // Сортировка: сначала те, у кого есть элементы НДС
                return $ndsB <=> $ndsA;
            } else {
                // Сортировка: сначала те, у кого нет элементов НДС
                return $ndsA <=> $ndsB;
            }
        });
    }

    //Сортировка по наличию документов Соглашение sort_doc=asc - поля заполнены sort_doc=desc поля пусты
    if (isset($_GET['sort_so']) && ($_GET['sort_so'] == 'asc' || $_GET['sort_so'] == 'desc')) {
        usort($usersWithElements, function ($a, $b) {
            $ndsA = !empty($a['SECTIONS'][1703]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"
            $ndsB = !empty($b['SECTIONS'][1703]['ELEMENTS']); // Проверка наличия элементов в секции "НДС"

            if ($_GET['sort_so'] == 'asc') {
                // Сортировка: сначала те, у кого есть элементы НДС
                return $ndsB <=> $ndsA;
            } else {
                // Сортировка: сначала те, у кого нет элементов НДС
                return $ndsA <=> $ndsB;
            }
        });
    }


    $arResult['USERS'] = $usersWithElements;
    // Массив для хранения названий секций
    $sections = [
        'Дата регистрации',
        'Наименование клиента',
        'Менеджер',
        'Без покупок'
    ];

    // Получение всех секций инфоблока
    $sectionRes = CIBlockSection::GetList(
        ['SORT' => 'ASC'], // Сортировка по порядку
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'], // Фильтрация по активным секциям инфоблока
        false, // Не считаем количество элементов
        ['ID', 'NAME'] // Поля, которые необходимо получить
    );

    // Заполняем массив названиями секций
    while ($section = $sectionRes->GetNext()) {
        $sections[$section['ID']] = $section['NAME'];
    }

    array_push($sections, 'Текст');
    $arResult['SECTIONS'] = $sections;
}

// Функция для получения даты последнего заказа пользователя
function getLastOrderDate($userId) {
    // Запрос к заказам через CSaleOrder
    $orderRes = CSaleOrder::GetList(
        ['DATE_INSERT' => 'DESC'], // Сортировка по дате создания заказа
        ['USER_ID' => $userId], // Фильтр по ID пользователя
        false,
        ['nTopCount' => 1], // Получаем только последний заказ
        ['ID', 'DATE_INSERT'] // Поля, которые необходимо получить
    );

    if ($order = $orderRes->Fetch()) {
        return $order['DATE_INSERT']; // Возвращаем дату создания заказа
    }
    return false; // Если заказов нет
}



// Генерация PDF
if ($_GET['pdf'] == 'Y') {
    // Подключаем библиотеку TCPDF из папки local/components/onelab/user.table/lib/tcpdf
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/components/onelab/user.table/lib/tcpdf/tcpdf.php');

    // Очищаем буфер вывода, если был вывод до этого
    if (ob_get_length()) {
        ob_end_clean();
    }
    class CustomPDFGenerator extends TCPDF
    {
        // Дополнительные методы или параметры, если необходимо
    }

    // Создаем новый PDF-документ
    $pdf = new CustomPDFGenerator();

    // Устанавливаем параметры документа
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Админ');
    $pdf->SetTitle('Таблица пользователей');
    $pdf->SetSubject('Сгенерировано через PHP');
    $pdf->SetKeywords('TCPDF, PDF, таблица');
// set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // Устанавливаем отступы
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Добавляем страницу
    $pdf->AddPage('L');

    // Устанавливаем шрифт
    $pdf->SetFont('dejavusans', '', 10);

    // Начало формирования таблицы
    $html = '<table 
    border="1" cellspacing="1" cellpadding="2" 
    style="table-layout: fixed; border-collapse: collapse;width: 100%;"
    >';

    // Генерация заголовков таблицы
    $html .= '<tr style="background-color:#f2f2f2;">';
    foreach ($arResult['SECTIONS'] as $key => $value) {
        $html .= '<th>' . $value . '</th>';
    }
    //$html .= '<th>' . GetMessage('CLEAR') . '</th>';
    $html .= '</tr>';

    // Генерация строк с данными пользователей
    foreach ($arResult['USERS'] as $key => $USERITEM) {
        $html .= '<tr>';

        // Дата регистрации
        $html .= '<td style="text-align: center; vertical-align: center;">' . $USERITEM['DATE_REGISTER'] . '</td>';

        // Имя пользователя и компания
        $html .= '<td style="text-align: center; vertical-align: center;">' . $USERITEM['USER_NAME'] . '<br><small>' . $USERITEM['WORK_COMPANY'] . '</small></td>';
        $html .= '<td style="text-align: center; vertical-align: center;">' . $USERITEM['UF_MANAGER'] . '</td>';

        // Дни без заказов
        if (intval($USERITEM['DAYS_SINCE_LAST_ORDER']) > 180 || $USERITEM['DAYS_SINCE_LAST_ORDER'] === false) {
            $html .= '<td style="background-color:#f34141;color:#fff;text-align: center">';
            if ($USERITEM['DAYS_SINCE_LAST_ORDER'] === false) {
                $html .= 'Нет заказов';
            } else {
                $html .= $USERITEM['DAYS_SINCE_LAST_ORDER'];
            }
            $html .= '</td>';
        } else {
            $html .= '<td style="background-color:#59b77b;color:#fff; text-align: center;vertical-align: center;">' . $USERITEM['DAYS_SINCE_LAST_ORDER'] . '</td>';
        }

        // Обработка разделов для каждого пользователя
        foreach ($USERITEM['SECTIONS'] as $USECTION) {
            if (count($USECTION['ELEMENTS']) <= 0) {
                $html .= '<td style="background-color:#f34141; text-align: center;vertical-align: center;">';
                $html .= '<a href="https://b2b.ak-cent.kz/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=32&type=sotbit_b2bcabinet_type_document&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y" target="_blank">';
                $html .= GetMessage('ADD');
                $html .= '</a></td>';
            } else {
                $html .= '<td style="background-color:#59b77b; text-align: center; vertical-align: center;">';
                foreach ($USECTION['ELEMENTS'] as $el) {
                    $html .= '<a href="' . $el . '" target="_blank">' . GetMessage('SHOW') . '</a><br>';
                }
                $html .= '</td>';
            }
        }

        // Кнопка удаления пользователя
        $html .= '<td>';
        $html .= empty($USERITEM['UF_COMMENT']) ? '' : mb_substr($USERITEM['UF_COMMENT'], 0, 10);
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    // Выводим HTML таблицу в PDF
    //$pdf->writeHTML($html, true, false, true, false, '');
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, false, true, 'L', true);
    // Очищаем буфер вывода перед отправкой PDF
    ob_clean();

    // Выводим PDF в браузер (можно заменить 'I' на 'D' для принудительной загрузки файла)
    $pdf->Output('user_table.pdf', 'I');

    // Прерываем выполнение, чтобы PDF выводился корректно
    die();
}
//excel

if ($_GET['excel'] == 'Y') {
    // Создаем новый Excel-документ
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Таблица пользователей');

    // Заголовки таблицы
    $columnIndex = 1;
    $headers = ['Дата регистрации', 'Имя пользователя и компания', 'Менеджер', 'Дни без заказов', 'НДС', 'Регистрация', 'Договор', 'Соглашение', 'Комментарий'];

    // Устанавливаем заголовки в строку 1
    foreach ($headers as $header) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
        $sheet->setCellValue($columnLetter . '1', $header);
        $columnIndex++;
    }

    // Заполняем данные пользователей
    $rowIndex = 2;
    foreach ($arResult['USERS'] as $USERITEM) {
        $columnIndex = 1;

        // Дата регистрации
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
        $sheet->setCellValue($columnLetter . $rowIndex, $USERITEM['DATE_REGISTER']);

        // Имя пользователя и компания
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
        $sheet->setCellValue($columnLetter . $rowIndex, $USERITEM['USER_NAME'] . "\n" . $USERITEM['WORK_COMPANY']);

        // Менеджер
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
        $sheet->setCellValue($columnLetter . $rowIndex, $USERITEM['UF_MANAGER'] ?? 'Без менеджера');

        // Дни без заказов
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
        $daysWithoutOrders = $USERITEM['DAYS_SINCE_LAST_ORDER'] === false ? 'Нет заказов' : $USERITEM['DAYS_SINCE_LAST_ORDER'];
        $sheet->setCellValue($columnLetter . $rowIndex, $daysWithoutOrders);

        // НДС, Регистрация, Договор, Соглашение - добавляем ссылки на файлы
        foreach (['НДС' => 1699, 'Регистрация' => 1700, 'Договор' => 1702, 'Соглашение' => 1703] as $sectionName => $sectionId) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
            if (!empty($USERITEM['SECTIONS'][$sectionId]['ELEMENTS'])) {
                $links = [];
//                echo '<pre>';
//               // print_r($USERITEM['SECTIONS'][$sectionId]);
//                echo '</pre>';
                foreach ($USERITEM['SECTIONS'][$sectionId]['ELEMENTS'] as $filePath) {
                    $links[] = $filePath; // Добавляем ссылку в массив
//                    echo '<pre>';
//                    print_r($links);
//                    echo '</pre>';
                }
                $sheet->setCellValue($columnLetter . $rowIndex, implode(', ', $links)); // Выводим все ссылки
            } else {
                $sheet->setCellValue($columnLetter . $rowIndex, 'Нет данных');
            }
        }

        // Комментарий
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex++);
        $sheet->setCellValue($columnLetter . $rowIndex, $USERITEM['UF_COMMENT'] ?? 'Без комментария');

        $rowIndex++;
    }

    // Сохраняем файл на сервере в папку /excel/
    $fileName = 'user_table_' . time() . '.xlsx';
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/excel/' . $fileName;

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    // Предоставляем ссылку на скачивание файла
    $downloadLink = '/excel/' . $fileName;
    echo "Файл готов. <a href='{$downloadLink}'>Скачать</a>";

    die();
}











