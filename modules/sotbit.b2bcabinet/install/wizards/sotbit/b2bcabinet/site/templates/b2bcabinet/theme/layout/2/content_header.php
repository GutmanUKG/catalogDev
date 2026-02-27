<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Page\Asset;

global $APPLICATION, $USER;
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/buttons/hover_dropdown.min.js");

if ($isMobile = \Bitrix\Main\Loader::includeModule('conversion') && ($md = new \Bitrix\Conversion\Internals\MobileDetect) && $md->isMobile())
    $isMobileView = true;
?>
<!-- Main navbar -->
<div class="burger-menu-overlay"></div>
<div class="navbar b2bcabinet-navbar-2 navbar-expand-xl navbar-light navbar-static px-0">
    <?
    if ($isMobileView) {
        ?>
        <div class="button-menu-wrap">
            <div class="d-md-none">
                <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
                    <i class="icon-paragraph-justify3"></i>
                </button>
            </div>
        </div>
        <?
    }
    ?>
    <div class="d-flex flex-1 pl-3">
        <div class="navbar-brand wmin-0 mr-1">
            <a href="<?= Option::get("sotbit.b2bcabinet", "LINK_FROM_LOGO", "/", SITE_ID) ?>" class="d-inline-block">
                <img src="<?= CFile::GetPath(Option::get("sotbit.b2bcabinet", "LOGO", "",
                    SITE_ID)) ?: Option::get("sotbit.b2bcabinet", "LOGO", "", SITE_ID) ?>" alt="">
            </a>
        </div>
        <? if (Loader::includeModule("sotbit.regions") && Sotbit\Regions\Config\Option::get('ENABLE',
                SITE_ID) === 'Y' && $USER->IsAuthorized()): ?>
            <div class="navbar-regions">
                <?
                $APPLICATION->IncludeComponent(
                    "sotbit:regions.choose",
                    "b2bcabinet",
                    array(
                        "COMPONENT_TEMPLATE" => "b2bcabinet",
                        "FROM_LOCATION" => "Y",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO"
                    ),
                    false
                );
                ?>
            </div>
        <? endif; ?>
    </div>

    <div class="b2bcabinet-navbar-2__user-wrap d-flex flex-xl-1 justify-content-xl-end order-0 order-xl-1 pr-3">
        <ul class="navbar-nav navbar-nav-underline flex-row <? if(Loader::includeModule("sotbit.regions") && Sotbit\Regions\Config\Option::get('ENABLE',
            SITE_ID) === 'Y' && $USER->IsAuthorized()) {echo "regions";}?>">
            <? if ($USER->IsAuthorized()): ?>
                <?if (Loader::includeModule("sotbit.notification") && Option::get('sotbit.notification', 'sotbit.notification_INC_MODULE', 'N', SITE_ID) === 'Y'):?>
                    <li class="nav-item navbar-nav-link py-0 px-2 my-2">
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:notification.notice",
                            "",
                            Array()
                        );
                        ?>
                    </li>
                <? endif; ?>
                <? if ($multibasketModuleIs) {
                    $APPLICATION->IncludeComponent(
                        "sotbit:multibasket.multibasket",
                        "b2bcabinet_new",
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
                            "COMPONENT_TEMPLATE" => "b2bcabinet_new"
                        ),
                        false
                    );
                } else {
                    $APPLICATION->IncludeComponent(
                        "bitrix:sale.basket.basket.line",
                        "b2bcabinet_header_2",
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
                            "SHOW_SUMMARY" => "N",
                            "SHOW_TOTAL_PRICE" => "Y",
                            "COMPONENT_TEMPLATE" => "b2bcabinet_header_2",
                            "SHOW_REGISTRATION" => "N",
                            "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                            "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                            "SHOW_AUTHOR" => "N",
                            "PATH_TO_AUTHORIZE" => "",
                            "PATH_TO_REGISTER" => SITE_DIR . "login/",
                            "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                            "POSITION_FIXED" => "N"
                        ),
                        false
                    );
                }

                $APPLICATION->IncludeComponent(
                    "bitrix:main.user.link",
                    "b2bcabinet_header_2",
                    array(
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "7200",
                        "ID" => $USER->getId(),
                        "NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#",
                        "SHOW_LOGIN" => "Y",
                        "THUMBNAIL_LIST_SIZE" => "38",
                        "THUMBNAIL_DETAIL_SIZE" => "120",
                        "USE_THUMBNAIL_LIST" => "Y",
                        "SHOW_FIELDS" => array(
                            0 => "PERSONAL_PHOTO",
                            1 => "WORK_POSITION",
                        ),
                        "COMPONENT_TEMPLATE" => "b2bcabinet_header_2"
                    ),
                    false
                );
                ?>
            <? else: ?>
                <li class="nav-item">
                    <a href="<?= $methodInstall == "AS_TEMPLATE" ? '/b2bcabinet/' : SITE_DIR ?>auth/"
                       class="navbar-nav-link b2b-come-in">
                        <i class="icon-enter"></i>
                        <span class="ml-1">
                            <?= \Bitrix\Main\Localization\Loc::getMessage("HEADER_COME_IN") ?>
                        </span>
                    </a>
                </li>
            <? endif; ?>
        </ul>
    </div>


    <!-- top menu -->
    <?
    if (!$isMobileView) {
        $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "b2bcabinet_header_2",
            array(
                "ALLOW_MULTI_SELECT" => "N",
                "CHILD_MENU_TYPE" => "b2bcabinet_header_2_inner",
                "DELAY" => "N",
                "MAX_LEVEL" => "3",
                "MENU_CACHE_GET_VARS" => array(),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "ROOT_MENU_TYPE" => "b2bcabinet_header_2",
                "USE_EXT" => "Y",
                "COMPONENT_TEMPLATE" => "b2bcabinet_header_2",
                "MENU_THEME" => "blue",
                "DISPLAY_USER_NANE" => "N",
                "CACHE_SELECTED_ITEMS" => false
            ),
            false
        );
    }
    ?>
    <!-- /top menu -->
</div>
<!-- /main navbar -->

<!-- Page content -->
<div class="page-content b2bcabinet-navbar-2__page-content">
    <?
    if ($isMobileView) {
        ?>
        <div class="sidebar sidebar-light sidebar-main sidebar-expand-md align-self-start">

            <!-- Sidebar mobile toggler -->
            <div class="sidebar-mobile-toggler text-center">
                <a href="#" class="sidebar-mobile-main-toggle">
                    <i class="icon-arrow-left8"></i>
                </a>
                <a href="#" class="sidebar-mobile-expand">
                    <i class="icon-screen-full"></i>
                    <i class="icon-screen-normal"></i>
                </a>
            </div>
            <!-- /sidebar mobile toggler -->

            <!-- Sidebar content -->
            <div class="sidebar-content">
                <div class="card card-sidebar-mobile">
                    <!-- Main navigation -->
                    <div class="card-body p-0">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "b2bcabinet",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "b2bcabinet_header_2_inner",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "3",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "b2bcabinet_header_2",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "b2bcabinet_header_2",
                                "MENU_THEME" => "blue",
                                "DISPLAY_USER_NANE" => "N",
                                "CACHE_SELECTED_ITEMS" => false
                            ),
                            false
                        );
                        ?>
                    </div>
                    <!-- /main navigation -->
                </div>
            </div>
            <!-- /sidebar content -->
        </div>
        <?
    }
    ?>

    <!-- Main content -->
    <div class="content-wrapper">
        <!-- Inner content -->
        <div class="content-inner content-scroll">

            <!-- Page header -->
            <?
            $APPLICATION->IncludeComponent(
                "sotbit:b2bcabinet.alerts",
                "",
                array(),
                false
            );
            ?>

            <div class="page-header">
                <div class="page-header-content container">
                    <div class="page-header">
                        <div class="page-header-content header-elements-lg-inline">
                            <div class="page-title d-flex">
                                <h4 class="font-weight-semibold d-flex">
                                    <? $APPLICATION->ShowTitle(false); ?>
                                    <? if ($multibasketModuleIs): ?>
                                        <div class="multibakset-color"></div><? endif; ?>
                                </h4>
                                <div class="product-inner__stickers">
                                    <?$APPLICATION->ShowViewContent('stickers');?>
                                </div>
                            </div>
                        </div>
                        <div class="breadcrumb-line header-elements-lg-inline">
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
                            </div>
                            <? if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES == "Y"): ?>
                                <div class="header-elements">
                                    <div class="breadcrumb justify-content-center">
                                        <?
                                        $APPLICATION->IncludeComponent(
                                            "sotbit:auth.company.choose",
                                            "b2bcabinet_header_2",
                                            array()
                                        );
                                        ?>
                                    </div>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /page header -->

            <!-- Content area -->
            <div class="content container b2bcabinet-navbar-2__content">