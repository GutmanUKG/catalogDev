<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

    global $USER, $APPLICATION;
    $stateSideBar = CUserOptions::GetOption("intranet", "StateLeftPanel", "Y");
    $positionSideBar = Option::get('sotbit.b2bcabinet', 'MENU_POSITION', 'LEFT', SITE_ID);

    if ($arParams["SIDEBAR_POSITION"] !== $positionSideBar) {
        return;
    }
//    Asset::getInstance()->addCss($_SERVER["DOCUMENT_ROOT"]."/local/components/sotbit/sotbit.personal.manager/templates/b2bcabinet_manager/style.min.css");

    ?>

    <div class="sidebar sidebar-dark sidebar-main sidebar-fixed sidebar-expand-md" >
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
        <div class="sidebar-content b2bcabinet-sidebar <?=$positionSideBar === "RIGHT" ? 'b2bcabinet-sidebar-right': ''?>">
            <!-- User menu -->
            <?
             if ($USER->IsAuthorized()) {
                 $APPLICATION->IncludeComponent(
                     "bitrix:main.user.link",
                     "b2bcabinet_userprofile",
                     array(
                         "CACHE_TYPE" => "A",
                         "CACHE_TIME" => "7200",
                         "ID" => $USER->getId(),
                         "NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#",
                         "SHOW_LOGIN" => "Y",
                         "THUMBNAIL_LIST_SIZE" => "38",
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
                 );
             }
            ?>
            <!-- /user menu -->

            <!-- Main navigation -->
            <div class="card <?=$stateSideBar == "N" ? 'card-sidebar-mobile' : ''?>">
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

    <style>
        .sidebar-expand-md.sidebar-main .b2bcabinet-sidebar.b2bcabinet-sidebar-right {
            left: auto;
        }

        @media (min-width: 768px) {
            .sidebar-xs .sidebar-main .b2bcabinet-sidebar-right .nav-sidebar>.nav-item-submenu>.nav-group-sub,
            .sidebar-xs .sidebar-main.sidebar-fixed .b2bcabinet-sidebar.b2bcabinet-sidebar-right .nav-sidebar > .nav-item-submenu:hover > .nav-group-sub {
                left: auto;
                right: 3.35rem;
            }
        }

    </style>



