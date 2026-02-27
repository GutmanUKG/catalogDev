<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult["MODEL_OF_WORK"] == "user_config") {
    $arResult["COND_TREE_PARAMS"] = [
        "NAME" => "excel_cond_form",
        "FORM_NAME" => "excel_cond_form",
        'JS_NAME' => "JSCondExcel",
        'CONT_ID' => "wrap_cond_excel",
        'data' => $arResult["COND_TREE_DATA"],
        'SEND_BTN_NAME' => "send_cond_tree",
        "RIGHTS_DISPLAY_IBLOCK" => $arResult["RIGHTS_DISPLAY_IBLOCK"],
    ];
}
