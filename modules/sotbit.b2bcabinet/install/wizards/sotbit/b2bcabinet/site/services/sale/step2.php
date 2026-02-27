<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

CModule::IncludeModule('main');
CModule::IncludeModule('catalog');

if (!CModule::IncludeModule('sale')) {
    return;
}

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if ($arSite = $dbSite->Fetch()) {
    $lang = $arSite["LANGUAGE_ID"];
}
if ($lang == '') {
    $lang = "ru";
}
$bRus = false;
if ($lang == "ru") {
    $bRus = true;
}

WizardServices::IncludeServiceLang("step2.php", $lang);


//Registered users group
$dbResult = CGroup::GetList($by, $order, array("STRING_ID" => "REGISTERED_USERS"));
if (!$dbResult->Fetch()) {
    $group = new CGroup;
    $arFields = array(
        "ACTIVE" => "Y",
        "C_SORT" => 3,
        "NAME" => GetMessage("REGISTERED_USERS"),
        "STRING_ID" => "REGISTERED_USERS",
    );

    $groupID = $group->Add($arFields);
    if ($groupID > 0) {
        COption::SetOptionString("main", "new_user_registration_def_group", $groupID);

    }
}

COption::SetOptionString("sotbit.b2bcabinet", "wizard_installed", "Y", false, WIZARD_SITE_ID);

?>