<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc;


global $USER, $APPLICATION;
$stateSideBar = CUserOptions::GetOption("intranet", "StateLeftPanel", "Y");

?>

<div class="sidebar sidebar-main shadow sidebar-expand-xl <?= $stateSideBar == "N" ? 'sidebar-main-resized' : '' ?>">

    <!-- Sidebar header -->
    <div class="sidebar-section">
        <div class="sidebar-logo d-flex justify-content-center align-items-center">
            <a href="<?= Option::get("sotbit.b2bcabinet", "LINK_FROM_LOGO", "/", SITE_ID) ?>" class="d-inline-flex align-items-center">
                <img class="sidebar-logo-icon" src="<?= CFile::GetPath(Option::get(
                                                        "sotbit.b2bcabinet",
                                                        "LOGO",
                                                        "",
                                                        SITE_ID
                                                    )) ?: Option::get("sotbit.b2bcabinet", "LOGO", "", SITE_ID) ?>" alt="Logo">
            </a>
        </div>
    </div>
    <!-- /sidebar header -->

    <div class="sidebar-main-resize-wrapper">
        <button 
            type="button" 
            class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-xl-inline-flex"
            onclick="BX.onCustomEvent('ToggleMainLayout');">
            <i class="ph-arrows-left-right"></i>
        </button>

        <button 
            type="button" 
            class="p-1 btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-xl-none"
            >
            <i class="ph-x"></i>
        </button>
    </div>
    <!-- Sidebar content -->
    <div class="sidebar-content b2bcabinet-sidebar">

        <!-- Choose company -->
        <? if (Loader::includeModule("sotbit.auth") && Option::get(
                "sotbit.auth",
                "EXTENDED_VERSION_COMPANIES",
                "N"
        ) == "Y") {
            $APPLICATION->IncludeComponent(
                "sotbit:auth.company.choose",
                "",
                array()
            );
        } else {
            define("EXTENDED_VERSION_COMPANIES", "N");
        }
        ?>
        <!-- /choose company -->

        <!-- Main navigation -->
        <div class="sidebar-section">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:menu",
                "b2bcabinet",
                array(
                    "ALLOW_MULTI_SELECT" => "N",
                    "CHILD_MENU_TYPE" => "b2bcabinet_menu_inner",
                    "DELAY" => "N",
                    "MAX_LEVEL" => "3",
                    "MENU_CACHE_GET_VARS" => array(),
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_TYPE" => "A",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "ROOT_MENU_TYPE" => "b2bcabinet_menu",
                    "USE_EXT" => "Y",
                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                    "MENU_THEME" => "blue",
                    "DISPLAY_USER_NANE" => "N",
                    "CACHE_SELECTED_ITEMS" => false,
                ),
                false
            );
            ?>
        </div>
        <!-- /main navigation -->
    </div>
    <!-- /sidebar content -->
</div>