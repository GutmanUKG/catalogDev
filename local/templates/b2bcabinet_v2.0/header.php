<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc,
    Sotbit\B2bCabinet\Helper\Config,
    Sotbit\Multibasket\Helpers;

global $APPLICATION, $USER;

if (defined("NEED_AUTH") && NEED_AUTH === true && !$USER->IsAuthorized()) {
    include_once "auth_header.php";
    return;
}

$userGroupRights = CUser::GetUserGroup($USER->GetID());
$b2bGroupRights = unserialize(Option::get('sotbit.b2bcabinet', 'OPT_BLANK_GROUPS'), ['allowed_classes' => false]) ?: [];

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
?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">

<head>
    <meta charset="<?=LANG_CHARSET?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><? $APPLICATION->ShowTitle(true) ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">

    <link rel="shortcut icon" href="/favicon.ico">

    <link rel="apple-touch-icon" sizes="128x128" href="/favicon_128x128.png">
    <link rel="icon" type="image/png" href="/favicon_32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon_64x64.png" sizes="64x64">
    <link rel="manifest" href="/manifest.json">

    <meta name="apple-mobile-web-app-title" content="MoonBase">
    <meta name="application-name" content="MoonBase">

    <meta name="msapplication-TileColor" content="#fff">
    <meta name="msapplication-TileImage" content="/favicon_128x128.png">

    <?
    CJSCore::Init();
    $APPLICATION->ShowHead();

    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap_limitless.min.css");
    // Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/lightbox.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/jquery.fancybox-3.5.7.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/components.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/layout.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/icons/phosphor/styles.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/constants.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/custom.css");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/jquery/jquery.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/bootstrap/bootstrap.bundle.min.js");
    // Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/lightbox/lightbox.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/jquery.fancybox-3.5.7.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/notifications/sweet_alert.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/anytime.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.date.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/picker.time.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/pickadate/legacy.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/select2.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/select2.langRu.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/owl.carousel.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/custom.js");

    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/app.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/dashboard.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/pages/sweet-alert.js");?>
    <?$APPLICATION->AddHeadString('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>');?>
    <?$APPLICATION->AddHeadString('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />');?>
    <?$APPLICATION->AddHeadString('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />');?>
    <?//$APPLICATION->AddHeadString('<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>');?>

</head>

<body>
    <? $APPLICATION->ShowPanel() ?>

    <!-- Page content -->
    <div class="page-content">
        <? $APPLICATION->IncludeComponent(
            "sotbit:sotbit.b2bcabinet.notifications",
            "b2bcabinet",
            array(),
            false,
            [
                "HIDE_ICONS" => "Y"
            ]
        );

        include "header/content_header.php";
        ?>
        <script>
            BX.message({
                'SWEETALERT_DEFAULT_QUESTION': '<?=Loc::getMessage('SWEETALERT_DEFAULT_QUESTION')?>',
                'SWEETALERT_CONFIRM_BUTTON': '<?=Loc::getMessage('SWEETALERT_CONFIRM_BUTTON')?>',
                'SWEETALERT_CANCEL_BUTTON': '<?=Loc::getMessage('SWEETALERT_CANCEL_BUTTON')?>',
            })
        </script>
