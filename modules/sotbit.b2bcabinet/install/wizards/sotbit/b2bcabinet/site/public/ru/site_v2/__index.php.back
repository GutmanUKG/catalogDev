<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application,
    Bitrix\Main\Config\Option,
    Sotbit\B2bCabinet\Helper\Config,
    Sotbit\B2bcabinet\Controller\Navbar;

global $USER, $APPLICATION;

Loc::loadMessages(__FILE__);

$arNavBarTabs = [
    0 => [
        "NAME" => Loc::getMessage("B2B_CABINET_TAB_DESKTOP"),
        "LINK" => "desktop",
        "ICON" => "ph-monitor",
    ],
    1 => [
        "NAME" => Loc::getMessage("B2B_CABINET_TAB_CALENDAR"),
        "LINK" => "calendar",
        "ICON" => "ph-calendar",
        "CHECK_RIGHTS" => "Y"
    ],
    2 => [
        "NAME" => Loc::getMessage("B2B_CABINET_TAB_SETTINGS"),
        "LINK" => "settings",
        "ICON" => "ph-gear",
        "CHECK_RIGHTS" => "Y"
    ],
];

$navbar = new Navbar(Application::getInstance()->getContext()->getRequest());
$activeTab = $navbar->getActiveTab('mainpage', 'desktop');
?>
    <div class="b2b-banner-wrapper">
        <? $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "b2b_main_banner",
            array(
                "COMPONENT_TEMPLATE" => "b2b_main_banner",
                "IBLOCK_TYPE" => Config::get("BANNERS_IBLOCKS_TYPE", SITE_ID),
                "IBLOCK_ID" => Config::get("BANNERS_IBLOCKS_ID", SITE_ID),
                "NEWS_COUNT" => "20",
                "SORT_BY1" => "ACTIVE_FROM",
                "SORT_ORDER1" => "DESC",
                "SORT_BY2" => "SORT",
                "SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "",
                "FIELD_CODE" => array(
                    0 => "DETAIL_PICTURE",
                    1 => "",
                ),
                "PROPERTY_CODE" => array(
                    0 => "LINK",
                    1 => "",
                ),
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "N",
                "PREVIEW_TRUNCATE_LEN" => "",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "SET_TITLE" => "N",
                "SET_BROWSER_TITLE" => "N",
                "SET_META_KEYWORDS" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_LAST_MODIFIED" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                "PARENT_SECTION" => "",
                "PARENT_SECTION_CODE" => "",
                "INCLUDE_SUBSECTIONS" => "N",
                "STRICT_SECTION_CHECK" => "N",
                "PAGER_TEMPLATE" => ".default",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Новости",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => ""
            ),
            false,
            ['HIDE_ICONS' => 'Y']
        );
        ?>
        <div class="navbar navbar-expand-lg navbar-light navbar-b2b-mainpage">
            <div class="navbar-collapse overflow-auto" id="navbar-second">
                <ul class="nav nav-tabs nav-mainpage-tabs">
                    <? foreach ($arNavBarTabs as $tab) : ?>
                        <li class="nav-item">
                            <a href="#<?= $tab["LINK"] ?>" class="nav-link
                           <?= $tab["LINK"] == $activeTab ? "active" : "" ?>
                           <?= ($tab["CHECK_RIGHTS"] == "Y" && !$USER->IsAuthorized()) ? "disabled" : "" ?>" data-bs-toggle="tab">
                                <i class="<?= $tab["ICON"] ?> me-2"></i>
                                <?= $tab["NAME"] ?>
                            </a>
                        </li>
                    <? endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="d-flex align-items-stretch align-items-lg-start flex-column flex-lg-row">
            <div class="tab-content tab-content-b2b w-100 order-2 order-lg-1">
                <div class="tab-pane fade <?= $activeTab == 'desktop' ? 'active show' : '' ?>" id="desktop">
                    <?
                    $GADGETS = $USER->IsAuthorized() ? [0 => "ALL"] : [
                        0 => "RSSREADER",
                        1 => "PERSONAL_MANAGER",
                        2 => "PROBKI",
                        3 => "WEATHER",
                        4 => "DISCOUNT",
                    ];

                    $APPLICATION->IncludeComponent(
                        "bitrix:desktop",
                        "b2bcabinet_desktop",
                        array(
                            "SHOW_GADGET" => $GADGETS,
                            "CAN_EDIT" => "Y",
                            "COLUMNS" => "3",
                            "COLUMN_WIDTH_0" => "33%",
                            "COLUMN_WIDTH_1" => "33%",
                            "COLUMN_WIDTH_2" => "33%",
                            "GADGETS" =>  [0 => "ALL"],
                            "GU_ACCOUNTPAY_TITLE_STD" => "",
                            "GU_BASKET_TITLE_STD" => "",
                            "GU_BLANK_TITLE_STD" => "",
                            "GU_DELAYBASKET_TITLE_STD" => "",
                            "GU_FAVORITES_TITLE_STD" => "",
                            "GU_HTML_AREA_TITLE_STD" => "",
                            "GU_ORDERS_LIMIT" => "2",
                            "GU_ORDERS_STATUS" => "ALL",
                            "GU_ORDERS_TITLE_STD" => "",
                            "GU_PROBKI_CITY" => "c564",
                            "GU_PROBKI_TITLE_STD" => "",
                            "GU_PROFILE_TITLE_STD" => "",
                            "GU_REVIEWS_TITLE_STD" => "",
                            "GU_RSSREADER_CNT" => "10",
                            "GU_RSSREADER_IS_HTML" => "N",
                            "GU_RSSREADER_RSS_URL" => "",
                            "GU_RSSREADER_TITLE_STD" => "",
                            "GU_SUBSCRIBE_TITLE_STD" => "",
                            "GU_WEATHER_CITY" => "c564",
                            "GU_WEATHER_COUNTRY" => Loc::getMessage('B2B_CABINET_COUNTRY_RUSSIA'),
                            "GU_WEATHER_TITLE_STD" => "",
                            "G_ACCOUNTPAY_PATH_TO_BASKET" => SITE_DIR . "orders/make/",
                            "G_ACCOUNTPAY_PATH_TO_PAYMENT" => SITE_DIR . "orders/payment/",
                            "G_ACCOUNTPAY_PERSON_TYPE_ID" => "1",
                            "G_BASKET_PATH_TO_BASKET" => SITE_DIR . "orders/make/",
                            "G_BLANK_INIT_JQUERY" => "N",
                            "G_BLANK_PATH_TO_BLANK" => SITE_DIR . "orders/blank_zakaza/",
                            "G_BUYERS_PATH_TO_BUYER_DETAIL" => SITE_DIR . "personal/buyer/?id=#ID#",
                            "G_BUYORDER_ORG_PROP" => array(),
                            "G_BUYORDER_PATH_TO_ORDER_DETAIL" => SITE_DIR . "orders/detail/#ID#/",
                            "G_BUYORDER_PATH_TO_PAY" => SITE_DIR . "orders/payment/",
                            "G_DISCOUNT_ID_DISCOUNT" => "3",
                            "G_DISCOUNT_PATH_TO_PAGE" => "",
                            "G_ORDERS_PATH_TO_ORDERS" => SITE_DIR . "orders/",
                            "G_ORDERS_PATH_TO_ORDER_DETAIL" => SITE_DIR . "orders/detail/#ID#/",
                            "G_PROBKI_CACHE_TIME" => "3600",
                            "G_PROBKI_SHOW_URL" => "Y",
                            "G_PROFILE_PATH_TO_PROFILE" => SITE_DIR . "personal/",
                            "G_REVIEWS_MAX_RATING" => "5",
                            "G_REVIEWS_PATH_TO_REVIEWS" => SITE_DIR . "personal/reviews/",
                            "G_RSSREADER_CACHE_TIME" => "3600",
                            "G_RSSREADER_PREDEFINED_RSS" => "",
                            "G_RSSREADER_SHOW_URL" => "N",
                            "G_SUBSCRIBE_PATH_TO_SUBSCRIBES" => SITE_DIR . "personal/subscribe/",
                            "G_WEATHER_CACHE_TIME" => "3600",
                            "G_WEATHER_SHOW_URL" => "Y",
                            "ID" => "holder2",
                            "COMPONENT_TEMPLATE" => "b2bcabinet_desktop",
                            "GU_REVIEWS_CNT" => "1",
                            "GU_REVIEWS_TYPE" => "ALL",
                            "GU_BUYERS_TITLE_STD" => "",
                            "GU_DISCOUNT_TITLE_STD" => "",
                            "GU_BUYORDER_TITLE_STD" => "",
                            "G_BUYERS_SEF_FOLDER" => "/personal",
                            "G_BUYERS_PATH_TO_DETAIL" => "profile_detail.php?ID=#ID#",
                            "GU_BUYERS_PER_PAGE" => "5",
                            "G_PERSONAL_MANAGER_PERSONAL_MANAGER_NAME_TEMPLATE" => "#NOBR##NAME# #LAST_NAME##/NOBR#",
                            "GU_PERSONAL_MANAGER_TITLE_STD" => ""
                        ),
                        false
                    );
                    ?>

                </div>
                <? if ($USER->IsAuthorized()) : ?>
                    <div class="tab-pane fade <?= $activeTab == 'calendar' ? 'active show' : '' ?>" id="calendar">
                        <?
                        $APPLICATION->IncludeComponent(
                            "sotbit:b2bcabinet.calendar",
                            "",
                            array(),
                            false,
                            array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    </div>
                    <div class="tab-pane fade <?= $activeTab == 'settings' ? 'active show' : '' ?>" id="settings">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.profile",
                            "b2b_personal_data",
                            array(
                                "SET_TITLE" => "N",
                                "AJAX_MODE" => "Y",
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "Y",
                                "AJAX_OPTION_HISTORY" => "N",
                                "USER_PROPERTY" => array(),
                                "SEND_INFO" => "N",
                                "CHECK_RIGHTS" => "N",
                                "USER_PROPERTY_NAME" => "",
                                "AJAX_OPTION_ADDITIONAL" => "",
                                "COMPONENT_TEMPLATE" => "b2b_personal_data",
                                "BUYER_PERSONAL_TYPE" => unserialize(COption::GetOptionString(
                                    "sotbit.b2bcabinet",
                                    "BUYER_PERSONAL_TYPE",
                                    "a:0:{}",
                                    SITE_ID
                                )),
                                "USER_PROPERTY_GENERAL_DATA" => array(
                                    0 => "TITLE",
                                    1 => "NAME",
                                    2 => "LAST_NAME",
                                    3 => "SECOND_NAME",
                                    4 => "EMAIL",
                                ),
                                "USER_PROPERTY_PERSONAL_DATA" => array(
                                    0 => "PERSONAL_GENDER",
                                    1 => "PERSONAL_PHOTO",
                                    2 => "PERSONAL_PHONE",
                                    3 => "PERSONAL_FAX",
                                    4 => "PERSONAL_MOBILE",
                                ),
                                "USER_PROPERTY_WORK_INFORMATION_DATA" => array(
                                    0 => "HIDE",
                                ),
                                "USER_PROPERTY_FORUM_PROFILE_DATA" => array(
                                    0 => "HIDE",
                                ),
                                "USER_PROPERTY_BLOG_PROFILE_DATA" => array(
                                    0 => "HIDE",
                                ),
                                "USER_PROPERTY_STUDENT_PROFILE_DATA" => array(
                                    0 => "HIDE",
                                ),
                                "USER_PROPERTY_ADMIN_NOTE_DATA" => array(
                                    0 => "HIDE",
                                )
                            ),
                            false
                        );
                        ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tabsMainPage = document.querySelectorAll('.content .tab-pane.fade');
            const navbarItems = document.querySelectorAll('.navbar-b2b-mainpage .nav-mainpage-tabs .nav-link:not(.disabled)');

            var mutationObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    BX.onCustomEvent('ToggleMainLayout');
                    if (window.calendarB2BcabinetMainpage) {
                        window.calendarB2BcabinetMainpage.updateSize();
                    }
                });
            });

            if (tabsMainPage) {
                for (let tab of tabsMainPage) {
                    mutationObserver.observe(tab, {
                        attributes: true,
                    });
                }
            }

            if (navbarItems) {
                for (let item of navbarItems) {
                    item.addEventListener('click', function(e) {
                        BX.ajax.runAction('sotbit:b2bcabinet.navbar.add', {
                            data: {
                                navbarId: 'mainpage',
                                navbarItemId: this.getAttribute('href').replace(/([#])/, ''),
                            }
                        });
                    });
                }
            }
        });
    </script>

<?
$APPLICATION->SetTitle(Loc::getMessage('B2B_CABINET_MAIN_PAGE'));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>