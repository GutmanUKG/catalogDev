<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

unset($arResult["FILTER"][0]);

if ($arResult["IBLOCK_FIELDS_MAP"]) {
    foreach ($arResult["IBLOCK_FIELDS_MAP"] as $code => $field) {
        if ($code === 'ID') continue;
        $headers[] = ["id" => $code, "name" => $field, "sort" => $code, "default" => true, "editable" => false];
    }
}

if ($arResult["IBLOCK_PROPERTIES_MAP"]) {
    foreach ($arResult["IBLOCK_PROPERTIES_MAP"] as $code => $field) {
        $headers[] = ["id" => "PROPERTY_" . $code, "name" => $field, "sort" => "PROPERTY_" . $code, "default" => true, "editable" => false];
    }
}

$arResult["HEADERS"] = $headers;

if ($arResult["ITEMS"]) {
    foreach ($arResult["ITEMS"] as $itemId => $item) {
        $arResult["ROWS"][$itemId]["data"] = $item["FIELDS"];

        foreach ($item["PROPERTIES"] as $propCode => $prop) {
            $arResult["ROWS"][$itemId]["data"]["PROPERTY_" . $propCode] = !empty($prop["VALUE"]) ? $prop["VALUE"] : "";
            if(is_array($arResult["ROWS"][$itemId]["data"]["PROPERTY_" . $propCode]))
                $arResult["ROWS"][$itemId]["data"]["PROPERTY_" . $propCode] = Loc::getMessage("SOTBIT_COMPLAINTS_LIST_COMPLAINT_PRODUCTS_ARRAY");
        }

        $arResult["ROWS"][$itemId]['actions'][] = [
            "TEXT" => Loc::getMessage("SOTBIT_COMPLAINTS_LIST_DETAIL_ACTION"),
            "ONCLICK" => "location.assign('".$item["FIELDS"]["DETAIL_PAGE_URL"]."')",
            "DEFAULT" => true
        ];
    }
}
?>