<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
use Bitrix\Main\Page\Asset;

?>
<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
<head>
    <meta charset="<?=LANG_CHARSET?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?$APPLICATION->ShowTitle()?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    
    <?
    CJSCore::Init('jquery');
    $APPLICATION->ShowHead();
    
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap_limitless.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/components.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/layout.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/icons/phosphor/styles.min.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/constants.css");
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/custom.css");
    
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/jquery/jquery.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/bootstrap/bootstrap.bundle.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/select2.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/forms/selects/select2.langRu.js");
    
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/app.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/dashboard.js");

    ?>
</head>

<body>
<!-- Page content -->
<div class="page-content bg-secondary">
        
        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center overflow-auto">
        <?
            $APPLICATION->AuthForm('');
        ?>