<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION, $USER;


use Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Page\Asset,
    Sotbit\B2bCabinet\Helper\Config,
    Bitrix\Main\Localization\Loc;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/demo_page.js");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/demo_page.css");
?>
<header>
    <div class="container">
        <div class="header-row">
            <a href="<?= Option::get("sotbit.b2bcabinet", "LINK_FROM_LOGO", "/", SITE_ID) ?>" class="d-inline-flex align-items-center">
                <img class="sidebar-logo-icon ms-md-1" src="<?= CFile::GetPath(Option::get(
                                                                "sotbit.b2bcabinet",
                                                                "LOGO",
                                                                "",
                                                                SITE_ID
                                                            )) ?: Option::get("sotbit.b2bcabinet", "LOGO", "", SITE_ID) ?>" alt="Logo">
            </a>
            <div class="header-bar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" target="_blank" href="https://ak-cent.kz/brand/">Наши бренды</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Контакты</a>
                    </li>
                </ul>
                <div class="header-bar_btns">
                    <div class="langs_btns">
                        <a href="">RU</a>
                        <a href="">KZ</a>
                    </div>
                    <button id="toggle_auth_form">
                        Вход в портал
                    </button>
                </div>
                <button class='close'></button>
            </div>
            <button class="burger-btn"></button>
        </div>
    </div>
</header>

<div class="container">
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
    </div>


    <div class="info_box col-6">
        <div class="icon">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/images/car.png" alt="icon">
        </div>
        <p>
            Сайт на реконструкции. <br>
            Портал <a href="/">b2b.ak-cent.kz</a> работает в штатном режиме.
        </p>
        <div class="links">
            <a href="https://ak-cent.kz/brand/" target="_blank">Наши бренды</a>
            <a href="">Контакты</a>
            <a href="" class="auth_link">Вход в портал</a>
        </div>
    </div>
    <div class="contact_info">
        <div class="contact_row">
            <div class="item">
                <span>
                    Адрес
                </span>
                <p>
                    050019, Алматы, микрорайон Атырау, пр. Рыскулова 159/8, комплекс 10, 2 этаж.
                </p>
            </div>
            <div class="item">
                <span>
                    Многоканальный номер
                </span>
                <p>
                    <a href="tel:+7 727 341-05-25">+7 727 341-05-25</a>
                </p>
                <span>
                    Мобильные номера
                </span>
                <p>
                    <a href="tel: +7 705 365 00 36"> +7 705 365 00 36</a>
                    <a href="tel: +7 777 365 00 36"> +7 777 365 00 36</a>
                    <a href="tel: +7 775 065 00 36"> +7 775 065 00 36</a>
                </p>
            </div>
            <div class="item">
                <span>
                    График работы
                </span>
                <p>
                    Пн-пт: 9:00 - 18:00. <br>
                    Перерыв: 13:00 - 14:00.<br>
                    Сб-вс: выходной.
                </p>
            </div>
            <div class="item">
                <span>
                    Социальные сети
                </span>
                <p>
                    <a href="https://www.facebook.com/AKCENT.KZ/" target="_blank" rel="nofollow noopener">facebook</a>
                    <a href="https://www.instagram.com/akcentmicrosystems/" target="_blank" rel="nofollow noopener">instagram</a>
                    <a href="https://www.youtube.com/channel/UCtlkhmCGiMBLsqm3MrJUXQQ" target="_blank" rel="nofollow noopener">youtube</a>
                </p>
            </div>
        </div>
        <div class="">
            <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ab706026885b610f85322b7afd65dfef667e8dd1a130ae24ef1eea4925439f8d3&amp;source=constructor" width="100%" height="500" frameborder="0"></iframe>
        </div>
    </div>
</div>





<div class="popup_form">
    <? $APPLICATION->AuthForm(''); ?>
</div>