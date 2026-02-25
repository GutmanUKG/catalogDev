<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", __DIR__."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
			$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
			$theme = COption::GetOptionString("main", "wizard_".$templateId."_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}

$arParams["FILTER_VIEW_MODE"] = (isset($arParams["FILTER_VIEW_MODE"]) && mb_strtoupper($arParams["FILTER_VIEW_MODE"]) == "HORIZONTAL") ? "HORIZONTAL" : "VERTICAL";
$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";
foreach ($arResult['ITEMS'] as &$arItem) {
    if (isset($arItem['VALUES']) && is_array($arItem['VALUES'])) {
        foreach ($arItem['VALUES'] as &$value) {
            if (isset($value['VALUE'])) {
                $value['VALUE'] = str_replace('.', '', $value['VALUE']);
            }
        }
        unset($value); // Разрываем ссылку на последний элемент
    }
}
//
if (LANGUAGE_ID === 'en') {
    $translations = [
        "Аксессуары" => "Accessories",
        "Автотовары" => "Car Products",
        "Активный отдых,туризм" => "Outdoor & Tourism",
        "Аудио-видео техника" => "Audio-Video Equipment",
        "Дом, сад, ремонт" => "Home, Garden & Renovation",
        "Всё для геймеров" => "Everything for Gamers",
        "Компьютерная и офисная техника" => "Computers & Office Equipment",
        "Мелко-бытовая техника" => "Small Household Appliances",
        "Персональная электроника и телефония" => "Personal Electronics & Telephony",
        "Хобби и развлечения" => "Hobbies & Entertainment",
        "Торгово-кассовое оборудование" => "POS Equipment"
    ];

    // Перебираем COMBO и заменяем названия
    foreach ($arResult["COMBO"] as &$comboItem) {
        foreach ($comboItem as $key => &$value) {
            $cleanValue = trim($value, "."); // Убираем точку в начале
            if (isset($translations[$cleanValue])) {
                $value = "." . $translations[$cleanValue]; // Добавляем точку обратно
            }
        }
        unset($value);
    }
    unset($comboItem);

    // Перебираем ITEMS[862]["VALUES"] и заменяем названия
    if (isset($arResult["ITEMS"][862]["VALUES"])) {
        foreach ($arResult["ITEMS"][862]["VALUES"] as &$item) {
            $cleanValue = trim($item["VALUE"], "."); // Убираем точку в начале
            if (isset($translations[$cleanValue])) {
                $item["VALUE"] = $translations[$cleanValue];
            }
        }
        unset($item);
    }

}
if (LANGUAGE_ID === 'kz') {
    $translations = [
        "Аксессуары" => "Аксессуарлар",
        "Автотовары" => "Автотауарлар",
        "Активный отдых,туризм" => "Белсенді демалыс, туризм",
        "Аудио-видео техника" => "Аудио-видео техника",
        "Дом, сад, ремонт" => "Үй, бақ, жөндеу",
        "Всё для геймеров" => "Геймерлерге арналған бәрі",
        "Компьютерная и офисная техника" => "Компьютер және кеңсе техникасы",
        "Мелко-бытовая техника" => "Ұсақ тұрмыстық техника",
        "Персональная электроника и телефония" => "Жеке электроника және телефония",
        "Хобби и развлечения" => "Хобби және ойын-сауық",
        "Торгово-кассовое оборудование" => "Сауда-кассалық жабдық"
    ];


    // Перебираем COMBO и заменяем названия
    foreach ($arResult["COMBO"] as &$comboItem) {
        foreach ($comboItem as $key => &$value) {
            $cleanValue = trim($value, "."); // Убираем точку в начале
            if (isset($translations[$cleanValue])) {
                $value = "." . $translations[$cleanValue]; // Добавляем точку обратно
            }
        }
        unset($value);
    }
    unset($comboItem);

    // Перебираем ITEMS[862]["VALUES"] и заменяем названия
    if (isset($arResult["ITEMS"][862]["VALUES"])) {
        foreach ($arResult["ITEMS"][862]["VALUES"] as &$item) {
            $cleanValue = trim($item["VALUE"], "."); // Убираем точку в начале
            if (isset($translations[$cleanValue])) {
                $item["VALUE"] = $translations[$cleanValue];
            }
        }
        unset($item);
    }

}





unset($arItem); // Разрываем ссылку на последний элемент

