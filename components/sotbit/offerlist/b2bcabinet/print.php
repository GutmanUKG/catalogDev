<?php
define("BX_SECURITY_AV_STARTED", false);
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sotbit.offerlist/classes/mpdf/vendor/autoload.php");
use Bitrix\Main\Text\Encoding,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Context;

global $APPLICATION;
$APPLICATION->RestartBuffer();

$context = Context::getCurrent();
$request = $context->getRequest();

$arParams["ID"] = $arResult["VARIABLES"]["OFFER_ID"] ?: $arResult["VARIABLES"]["ID"] ?: null;

if (!$arParams["ID"]) {
    ShowError(Loc::getMessage('SOTBIT_OFFERLIST_DOC_ERROR'));
    die();
}

if (!$_SESSION["SOTBIT_OFFERLIST"][$arParams["ID"]]) {
    if ($arResult["URL_TEMPLATES"]["editor"]) {
        LocalRedirect(preg_replace('/\#\S+\#/', $arParams["ID"], $arResult["URL_TEMPLATES"]["editor"]));
    }

    if ($arParams["SEF_URL_TEMPLATES"]) {
        LocalRedirect($arParams["SEF_FOLDER"] . $arParams["SEF_URL_TEMPLATES"]['list']);
    }
}

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'default_font' => 'DejaVu Serif',
    'mode' => 'utf-8',
]);
$mpdf->charset_in = 'utf-8';

$html = base64_decode($_SESSION["SOTBIT_OFFERLIST"][$arParams["ID"]]);
$title = Loc::getMessage('SOTBIT_OFFERLIST_DOC_TITLE', ["#ID#" => $arParams["ID"]]);
if (!Encoding::detectUtf8($title)) {
    $title = Encoding::convertEncoding($title, "WINDOWS-1251", "UTF-8");
}

$dest = $request->get("DOWNLOAD") === "Y" ? "D" : "I";
$mpdf->SetTitle($title);
$mpdf->WriteHTML($html);
$mpdf->Output('offer_' . $arParams["ID"] . '.pdf', $dest);
die();