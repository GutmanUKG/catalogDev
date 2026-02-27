<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;
?>
<!-- Main sidebar -->
<?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => SITE_TEMPLATE_PATH.'/header/sidebar.php',
            "AREA_FILE_RECURSIVE" => "N",
            "EDIT_MODE" => "html",
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );
?>
<!-- /main sidebar -->

<!-- Main content -->
<div class="content-wrapper">
    <!-- Main navbar -->
    <div class="navbar navbar-header navbar-expand-xl navbar-static shadow gradient">
        <div class="container-fluid p-0">
            <div class="d-flex d-xl-none me-sm-2 me-0">
                <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                    <i class="ph-list"></i>
                </button>
                <a href="<?=Option::get("sotbit.b2bcabinet", "LINK_FROM_LOGO", "/", SITE_ID) ?>" class="d-inline-flex align-items-center">
                    <img class="sidebar-logo-icon ms-md-1" src="<?= CFile::GetPath(Option::get(
                                                        "sotbit.b2bcabinet",
                                                        "LOGO",
                                                        "",
                                                        SITE_ID
                                                    )) ?: Option::get("sotbit.b2bcabinet", "LOGO", "", SITE_ID) ?>" alt="Logo">
                </a>
            </div>

            <div class="navbar-collapse collapse" id="navbar_search"></div>

            <ul class="nav hstack flex-row justify-content-end order-1 order-lg-2">
                <?
                if (Loader::includeModule("sotbit.regions") && Sotbit\Regions\Config\Option::get('ENABLE', SITE_ID) === 'Y' && $USER->IsAuthorized()) : ?>
                    <li class="nav-item nav-item-dropdown-lg dropdown regions">
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:regions.choose",
                            "b2bcabinet",
                            array()
                        );
                        ?>
                    </li>
                <? endif; ?>
                <? if ($USER->IsAuthorized()) : ?>
                    <?if (Loader::includeModule("sotbit.notification") && Option::get('sotbit.notification', 'sotbit.notification_INC_MODULE', 'N', SITE_ID) === 'Y'):?>
                    <li class="nav-item">
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:notification.notice",
                            "",
                            Array()
                        );
                        ?>
                    </li>
                    <? endif; ?>
                    <li class="nav-item cart-header">
                        <? if ($multibasketModuleIs) {
                            $APPLICATION->IncludeComponent(
                                "sotbit:multibasket.multibasket", 
                                "b2bcabinet_v2.0", 
                                array(
                                    "BASKET_PAGE_URL" => Option::get("sotbit.b2bcabinet","BASKET_URL","",SITE_ID),
                                    "ONLY_BASKET_PAGE_RECALCULATE" => "N",
                                    "RECALCULATE_BASKET" => "PAGE_RELOAD",
                                    "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
                                    "SHOW_NUM_PRODUCTS" => "Y",
                                    "SHOW_TOTAL_PRICE" => "Y",
                                    "SHOW_PERSONAL_LINK" => "N",
                                    "PATH_TO_PERSONAL" => SITE_DIR."personal/",
                                    "SHOW_AUTHOR" => "N",
                                    "PATH_TO_AUTHORIZE" => "",
                                    "SHOW_REGISTRATION" => "N",
                                    "PATH_TO_REGISTER" => SITE_DIR."login/",
                                    "PATH_TO_PROFILE" => SITE_DIR."personal/",
                                    "SHOW_PRODUCTS" => "N",
                                    "POSITION_FIXED" => "N",
                                    "HIDE_ON_BASKET_PAGES" => "N",
                                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                                    "POSITION_HORIZONTAL" => "right",
                                    "POSITION_VERTICAL" => "top"
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
                    </li>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:main.user.link",
                        "b2bcabinet_userprofile",
                        array(
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "7200",
                            "ID" => $USER->getId(),
                            "NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#",
                            "SHOW_LOGIN" => "Y",
                            "THUMBNAIL_LIST_SIZE" => "42",
                            "THUMBNAIL_DETAIL_SIZE" => "100",
                            "USE_THUMBNAIL_LIST" => "Y",
                            "SHOW_FIELDS" => array(
                                0 => "PERSONAL_BIRTHDAY",
                                1 => "PERSONAL_ICQ",
                                2 => "PERSONAL_PHOTO",
                                3 => "PERSONAL_CITY",
                                4 => "WORK_COMPANY",
                                5 => "WORK_POSITION",
                            ),
                            "USER_PROPERTY" => array(),
                            "PATH_TO_SONET_USER_PROFILE" => "",
                            "PROFILE_URL" => "",
                            "DATE_TIME_FORMAT" => "d.m.Y H:i:s",
                            "SHOW_YEAR" => "Y",
                            "COMPONENT_TEMPLATE" => "b2bcabinet_userprofile"
                        ),
                        false
                    ); ?>
                <? else : ?>
                    <li class="nav-item header-logout">
                        <a class="navbar-nav-link btn-transparent text-white rounded" href="<?= $methodInstall == "AS_TEMPLATE" ? '/b2bcabinet/' : SITE_DIR ?>auth/">
                            <span><?= Loc::getMessage('HEADER_COME_IN') ?></span>
                        </a>
                    </li>
                <? endif; ?>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->

    <!-- Inner content -->
    <div class="content-inner">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex align-items-center flex-wrap justify-content-between w-100">
                    <h5 class="page-title mb-0 p-0 <?=$multibasketModuleIs ? 'multibakset-color-title' : ''?>">
                        <? $APPLICATION->ShowTitle(false); ?>
                    </h5>
                    <div class="product-inner__stickers">
                        <?$APPLICATION->ShowViewContent('stickers');?>
                    </div>
            
                    <!-- content breadcrumb -->
                    <div class="breadcrumb-wrapper">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:breadcrumb",
                            "b2bcabinet_breadcrumb",
                            array(
                                "START_FROM" => $methodInstall == "AS_SITE" ? '0' : '1',
                                "PATH" => "",
                                "SITE_ID" => SITE_ID,
                                "COMPONENT_TEMPLATE" => "b2bcabinet_breadcrumb"
                            ),
                            false
                        );?>
                    </div>
                    <!-- /content breadcrumb -->
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