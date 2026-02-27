<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Bitrix\Main\Page\Asset,
    Sotbit\B2bCabinet\Helper\Config,
    Sotbit\Multibasket\Helpers;

global $APPLICATION, $USER;

if (defined("NEED_AUTH") && NEED_AUTH === true && !$USER->IsAuthorized()) {
    include_once "auth_header.php";
    return;
}

$userGroupRights = CUser::GetUserGroup($USER->GetID());
$b2bGroupRights = unserialize(Option::get('sotbit.b2bcabinet', 'OPT_BLANK_GROUPS'));

if (!array_intersect($userGroupRights, $b2bGroupRights)) {
    $_SESSION['USER_ID_RIGHTS_DENIED'] = $USER->GetID();
    $_GET['ACCESS_RIGHTS_DENIED'] = "Y";
    $USER->Logout();
    define("NEED_AUTH", true);
    include_once "auth_header.php";
    return;
}

$multibasketModuleIs = Loader::includeModule('sotbit.multibasket')
    && Helpers\Config::moduleIsEnabled(SITE_ID);

$methodInstall = Config::getMethodInstall(SITE_ID);
$stateLeftPanel = CUserOptions::GetOption("intranet", "StateLeftPanel", "Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><? $APPLICATION->ShowTitle() ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
          type="text/css">

    <?
    CJSCore::Init();
    $APPLICATION->ShowHead();

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/icons/icomoon/styles.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap_limitless.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/layout.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/components.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/colors.min.css");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/main/jquery.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/main/bootstrap.bundle.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/loaders/blockui.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/notifications/sweet_alert.min.js");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/visualization/d3/d3.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/visualization/d3/d3_tooltip.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/styling/switchery.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/bootstrap_multiselect.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/moment/moment.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/daterangepicker.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/main/app.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/main/dashboard.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/anytime.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.date.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.time.js");
    //Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/legacy.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/notifications/jgrowl.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker_date.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/styling/switch.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/pages/form_checkboxes_radios.js");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/select2.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/styling/uniform.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/styling/form_layouts.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/perfect_scrollbar.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/layout_fixed_sidebar_custom.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/loaders/progressbar.min.js");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/pages/form_select2.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/pages/uniform_init.js");
    ?>
</head>

<body class="<?= $stateLeftPanel != "Y" ? "sidebar-xs" : "" ?>">
<? $APPLICATION->ShowPanel() ?>
<? $APPLICATION->IncludeComponent(
    "sotbit:sotbit.b2bcabinet.notifications",
    "b2bcabinet",
    array(),
    false,
    [
        "HIDE_ICONS" => "Y"
    ]
);

include Config::getHeaderPath(SITE_ID);

if ($stylePath = Config::getHeaderStylePath(SITE_ID)) {
    Asset::getInstance()->addCss(  $stylePath);
}

if ($jsPath = Config::getHeaderJSPath(SITE_ID)) {
    Asset::getInstance()->addJs($jsPath);
}
?>
