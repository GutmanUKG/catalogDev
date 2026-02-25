<?php

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;
?>
<?
$showSearch = $APPLICATION->GetDirProperty('SHOW_TOP_SEARCH');
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
<div class="content-wrapper  <?if($showSearch == "N"):?> top_search <?endif;?>" >
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
            <div class="navbar-collapse collapse" id="navbar_search">
                <? if($showSearch !== "N"):?>
                <?$APPLICATION->IncludeComponent(
	"arturgolubev:search.title", 
	"onelab", 
	array(
		"CATEGORY_0" => array(
			0 => "iblock_sotbit_b2bcabinet_type_catalog",
		),
		"CATEGORY_0_TITLE" => "",
		"CHECK_DATES" => "N",
		"CONTAINER_ID" => "title-search",
		"INPUT_ID" => "title-search-input",
		"NUM_CATEGORIES" => "1",
		"ORDER" => "date",
		"PAGE" => "/orders/blank_zakaza/",
		"SHOW_INPUT" => "Y",
		"SHOW_OTHERS" => "N",
		"TOP_COUNT" => "5",
		"USE_LANGUAGE_GUESS" => "Y",
		"COMPONENT_TEMPLATE" => "onelab",
		"PRICE_CODE" => array(
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SHOW_PREVIEW" => "Y",
		"PROPERTY_ARTICLE" => "",
		"CONVERT_CURRENCY" => "N",
		"PREVIEW_WIDTH" => "75",
		"PREVIEW_HEIGHT" => "75",
		"ANIMATE_HINTS" => array(
		),
		"ANIMATE_HINTS_SPEED" => "1",
		"INPUT_PLACEHOLDER" => "Поиск",
		"SHOW_PROPS" => array(
		),
		"PREVIEW_WIDTH_NEW" => "70",
		"PREVIEW_HEIGHT_NEW" => "70",
		"SHOW_LOADING_ANIMATE" => "Y",
		"SHOW_HISTORY" => "N",
		"SHOW_PREVIEW_TEXT" => "N",
		"SHOW_QUANTITY" => "N",
		"FILTER_NAME" => "",
		"CATEGORY_0_iblock_sotbit_b2bcabinet_type_catalog" => array(
			0 => "7",
		)
	),
	false
);?>
                <?endif;?>
            </div>

            <ul class="nav hstack flex-row justify-content-end order-1 order-lg-2">

                <li class="nav-item">
                    <?if($showSearch !== "N"):?>
                    <button id="search_trigger"></button>
                    <?endif;?>
                </li>

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
                <div class="d-flex flex-column align-items-start flex-wrap justify-content-between w-100">
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
                    <div class="d-flex align-items-center w-100">
                        <h5 class="page-title mb-0 p-0 <?=$multibasketModuleIs ? 'multibakset-color-title' : ''?>">

                            <? if ($APPLICATION->GetCurPage(false) === '/catalog_sale/'): ?>
                                <a href="/catalog_sale/" style="color:#000;">
                                    <? $APPLICATION->ShowTitle(false); ?>
                                </a>
                            <?else:?>
                                <? $APPLICATION->ShowTitle(false); ?>
                            <?endif;?>
                            <!--                        --><?//if ($USER->IsAdmin()):?>
                            <!--                        <button class="add_fav_section"></button>-->
                            <!--                        --><?//endif;?>
                        </h5>
                    </div>

                    <div class="product-inner__stickers">
                        <?$APPLICATION->ShowViewContent('stickers');?>
                    </div>
            

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