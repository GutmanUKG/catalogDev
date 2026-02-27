<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
if(empty($arResult["AGREEMENT_ORIGINATOR_ID"])) {
    $arResult["AGREEMENT_ORIGINATOR_ID"] = "main/edit_user";
}

if(empty($arResult["AGREEMENT_ORIGIN_ID"])) {
    $arResult["AGREEMENT_ORIGIN_ID"] = "edit_user";
}

if(empty($arResult["AGREEMENT_INPUT_NAME"])) {
    $arResult["AGREEMENT_INPUT_NAME"] = "USER_AGREEMENT";
}