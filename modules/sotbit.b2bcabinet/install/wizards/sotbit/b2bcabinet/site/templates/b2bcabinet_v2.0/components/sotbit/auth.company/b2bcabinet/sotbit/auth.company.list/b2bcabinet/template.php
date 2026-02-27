<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
    Sotbit\B2bCabinet\Helper\Config;

$methodIstall = Config::getMethodInstall(SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . \SotbitB2bCabinet::PATH . '/' : SITE_DIR;
if (strlen($arResult["ERROR_MESSAGE"]) > 0) {
    ShowError($arResult["ERROR_MESSAGE"]);
} ?>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.ui.filter",
        "b2bcabinet",
        array(
            "FILTER_ID" => "PERSONAL_PROFILE_LIST",
            "GRID_ID" => "PERSONAL_PROFILE_LIST",
            "FILTER" => array(
                array("id" => "ID", "name" => GetMessage('P_ID')),
                array("id" => "NAME", "name" => GetMessage('P_NAME')),
                array("id" => "DATE_UPDATE", "name" => GetMessage('P_DATE_UPDATE'), "type" => "date"),
                array("id" => "BUYER_TYPE", "name" => GetMessage('P_PERSON_TYPE_ID'), "type" => "list", "items" => $arResult["PERSON_TYPES"]),
                array("id" => "ACTIVE", "name" => GetMessage('P_DATE_ACTIVE'), "type" => "list", "items" => ["Y" => GetMessage('P_DATE_ACTIVE_Y'), "N" => GetMessage('P_DATE_ACTIVE_N')]),
                array("id" => "STATUS", "name" => GetMessage('P_DATE_STATUS'), "type" => "list", "items" => ["M" => GetMessage('COMPANY_LIST_STATUS_M'), "A" => GetMessage('COMPANY_LIST_STATUS_A'), "R" => GetMessage('COMPANY_LIST_STATUS_R'),]),
            ),
            "ENABLE_LIVE_SEARCH" => true,
            "ENABLE_LABEL" => true,
            "COMPONENT_TEMPLATE" => "b2bcabinet"
        ),
        false
    );
    ?>

    <? $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => 'PERSONAL_PROFILE_LIST',

            'HEADERS' => $arParams['GRID_HEADER'],
            'ROWS' => $arResult['ROWS'],

            'AJAX_MODE' => 'Y',
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",

            "ALLOW_COLUMNS_SORT" => true,
            "ALLOW_ROWS_SORT" => $arParams['ALLOW_COLUMNS_SORT'],
            "ALLOW_COLUMNS_RESIZE" => false,
            "ALLOW_HORIZONTAL_SCROLL" => false,
            "ALLOW_SORT" => true,
            "ALLOW_PIN_HEADER" => true,
            "ACTION_PANEL" => $arResult['GROUP_ACTIONS'],

            "SHOW_CHECK_ALL_CHECKBOXES" => false,
            "SHOW_ROW_CHECKBOXES" => false,
            "SHOW_ROW_ACTIONS_MENU" => true,
            "SHOW_GRID_SETTINGS_MENU" => true,
            "SHOW_NAVIGATION_PANEL" => true,
            "SHOW_PAGINATION" => true,
            "SHOW_SELECTED_COUNTER" => false,
            "SHOW_TOTAL_COUNTER" => true,
            "SHOW_PAGESIZE" => true,
            "SHOW_ACTION_PANEL" => true,

            "ENABLE_COLLAPSIBLE_ROWS" => true,
            'ALLOW_SAVE_ROWS_STATE' => true,

            "SHOW_MORE_BUTTON" => false,
            '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
            'NAV_OBJECT' => $arResult['NAV_OBJECT'],
            'NAV_STRING' => $arResult['NAV_STRING'],
            "TOTAL_ROWS_COUNT" => $arResult['NAV_RECORD_COUNT'],
            "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
            "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
            "DEFAULT_PAGE_SIZE" => 50
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    ); ?>

    <div class="card card-position-sticky">
        <div class="card-body d-flex gap-3">
            <a class="btn btn-primary" href="<?= $methodIstall ?>personal/companies/add.php">
                <?= Loc::getMessage('SPOL_ADD_NEW_PROFILE') ?>
            </a>
            <? $APPLICATION->IncludeComponent(
                "sotbit:auth.company.join",
                "",
                array(),
                false
            ); ?>
        </div>
    </div>

