<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;

if ($USER->IsAuthorized()) {
    $userId = $USER->GetID();
    if (!empty($_SESSION['USER_BASKET'][$userId])) {
        foreach ($_SESSION['USER_BASKET'][$userId] as $item) {
            $arSelect = [
                "ID",
                "NAME",
                "IBLOCK_ID",
                "PREVIEW_PICTURE",
                "PROPERTY_ART",
                "PROPERTY_BRAND",
                "PROPERTY_CARUSEL",
                "PROPERTY_DISTROY_TYPE",
                "PROPERTY_COUNT",
                "PROPERTY_PART",
            ];
            $arFilter = [
                "IBLOCK_ID" => 33,
                "ID" => $item['ID'],
                "ACTIVE" => "Y"
            ];
            $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            $data = [];
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arFields['PRICE'] = str_replace(' ', '', $item['PRICE']);
                $arFields['FORMATED_PRICE'] = $item['PRICE'];
                $arFields['QUANTITY'] = $item['QUANTITY'];
                $data = $arFields;
            }
            $arResult['ITEMS'][] = $data;
        }
    }
}
