<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER;
?>


<div class="navbar navbar-expand-md navbar-dark fixed-top">
    <div class="navbar__sidebar-toggle-control">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block"
                   onclick="BX.onCustomEvent('ToggleMainLayout');">
                    <i class="icon-transmission"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="navbar-brand">
        <a href="<?= Option::get("sotbit.b2bcabinet", "LINK_FROM_LOGO", "/", SITE_ID) ?>" class="d-inline-block">
            <img class="header_logo" src="<?= CFile::GetPath(Option::get("sotbit.b2bcabinet", "LOGO", "",
                SITE_ID)) ?: Option::get("sotbit.b2bcabinet", "LOGO", "", SITE_ID) ?>" alt="">
        </a>
    </div>
    <div class="d-md-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-tree5"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-more2"></i>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="navbar-mobile">
        <?
        if (Loader::includeModule("sotbit.regions") && Sotbit\Regions\Config\Option::get('ENABLE', SITE_ID) === 'Y' && $USER->IsAuthorized()):?>
            <div class="navbar-regions">
                <?
                $APPLICATION->IncludeComponent(
                    "sotbit:regions.choose",
                    "b2bcabinet",
                    array()
                );
                ?>
            </div>
        <?endif;
        if (Loader::includeModule("sotbit.auth") && Option::get("sotbit.auth", "EXTENDED_VERSION_COMPANIES",
                "N") == "Y") {
            $APPLICATION->IncludeComponent(
                "sotbit:auth.company.choose",
                "",
                array()
            );
        } else {
            define("EXTENDED_VERSION_COMPANIES", "N");
        }
        ?>
        <div class="header-elements" style="margin-left: auto;">
            <? if ($USER->IsAuthorized()): ?>
                <div class="d-flex justify-content-center align-items-center">
                    <?if (Loader::includeModule("sotbit.notification") && Option::get('sotbit.notification', 'sotbit.notification_INC_MODULE', 'N', SITE_ID) === 'Y'):?>
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:notification.notice",
                            "",
                            Array()
                        );
                        ?>
                    <? endif; ?>
                    <div class="cart_header">
                        <? if ($multibasketModuleIs) {
                            $APPLICATION->IncludeComponent(
                                "sotbit:multibasket.multibasket",
                                "b2bcabinet",
                                array(
                                    "BASKET_PAGE_URL" => Option::get("sotbit.b2bcabinet", "BASKET_URL", "", SITE_ID),
                                    "ONLY_BASKET_PAGE_RECALCULATE" => "N",
                                    "RECALCULATE_BASKET" => "PAGE_RELOAD",
                                    "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                                    "SHOW_NUM_PRODUCTS" => "Y",
                                    "SHOW_TOTAL_PRICE" => "Y",
                                    "SHOW_PERSONAL_LINK" => "N",
                                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                    "SHOW_AUTHOR" => "N",
                                    "PATH_TO_AUTHORIZE" => "",
                                    "SHOW_REGISTRATION" => "Y",
                                    "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                    "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                                    "SHOW_PRODUCTS" => "N",
                                    "POSITION_FIXED" => "N",
                                    "HIDE_ON_BASKET_PAGES" => "N",
                                    "COMPONENT_TEMPLATE" => "b2bcabinet"
                                ),
                                false
                            );
                        } else {
                            $APPLICATION->IncludeComponent(
                                "bitrix:sale.basket.basket.line",
                                "b2bcabinet",
                                array(
                                    "HIDE_ON_BASKET_PAGES" => "N",
                                    "PATH_TO_BASKET" => Option::get("sotbit.b2bcabinet", "BASKET_URL", "", SITE_ID),
                                    "SHOW_DELAY" => "N",
                                    "SHOW_EMPTY_VALUES" => "Y",
                                    "SHOW_IMAGE" => "N",
                                    "SHOW_NOTAVAIL" => "Y",
                                    "SHOW_NUM_PRODUCTS" => "Y",
                                    "SHOW_PERSONAL_LINK" => "N",
                                    "SHOW_PRICE" => "N",
                                    "SHOW_PRODUCTS" => "N",
                                    "SHOW_SUMMARY" => "Y",
                                    "SHOW_TOTAL_PRICE" => "N",
                                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                                    "SHOW_REGISTRATION" => "N",
                                ),
                                false
                            );
                        } ?>
                    </div>

                    <div class="header_logout navbar-nav-link">
                        <a href="?logout=yes&<?= bitrix_sessid_get() ?>">
                            <span><?= Loc::getMessage('LOGOUT') ?></span>
                        </a>
                    </div>
                </div>
            <? else: ?>
                <div class="d-flex justify-content-center">
                    <div class="header_logout navbar-nav-link">
                        <a href="<?= $methodInstall == "AS_TEMPLATE" ? '/b2bcabinet/' : SITE_DIR ?>auth/">
                            <span><?= Loc::getMessage('HEADER_COME_IN') ?></span>
                        </a>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>
<!-- /main navbar -->
<!-- Page content -->
<div class="page-content">
    <!-- Main sidebar -->
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => SITE_DIR . "/include/b2b/template/sidebar.php",
            "AREA_FILE_RECURSIVE" => "N",
            "EDIT_MODE" => "html",
            "SIDEBAR_POSITION" => "LEFT",
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
    ?>
    <!-- /main sidebar -->
    <!-- Main content -->
    <div class="content-wrapper content-scroll">
        <!-- Page header -->


        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-md-inline">
                <div class="page-title d-flex" style="font-size: 1.1875rem;">
                    <? $APPLICATION->ShowTitle(false); ?>
                    <? if ($multibasketModuleIs): ?>
                        <div class="multibakset-color"></div><? endif; ?>
                    <div class="product-inner__stickers">
                        <?$APPLICATION->ShowViewContent('stickers');?>
                    </div>
                    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>
            </div>
            <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
                <div class="d-flex">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:breadcrumb",
                        "b2bcabinet_breadcrumb",
                        array(
                            "START_FROM" => $methodInstall == "AS_SITE" ? '0' : '1',
                            "PATH" => "",
                            "SITE_ID" => SITE_ID,
                            "COMPONENT_TEMPLATE" => "b2bcabinet_breadcrumb"
                        ),
                        false
                    ); ?>
                    <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>
            </div>
        </div>
        <!-- /page header -->
        <!-- Content area -->
        <?
        $APPLICATION->IncludeComponent(
            "sotbit:b2bcabinet.alerts",
            "",
            array(),
            false
        );
        ?>
        <div class="content">
