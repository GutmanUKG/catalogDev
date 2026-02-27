<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
loc::loadLanguageFile(__FILE__);
?>

<div class="complaints-list-wrap">
    <div class="complaints-filter-wrap">
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:main.ui.filter",
            "b2bcabinet",
            array(
                "FILTER_ID" => $arParams['FILTER_NAME'],
                "GRID_ID" => $arParams['FILTER_NAME'],
                "FILTER" => $arResult["FILTER"],
                "ENABLE_LIVE_SEARCH" => true,
                "ENABLE_LABEL" => true,
                "COMPONENT_TEMPLATE" => ".default"
            ),
            false
        );
        ?>
    </div>
    <div class="complaints-add-wrap position-relative zindex-1 ms-sm-3 ms-0 my-2 my-sm-0 float-start w-100 w-sm-auto">
        <a href="<?=$arParams["ADD_URL"]?>" class="btn btn-sm btn-primary w-100 w-sm-auto">
           <?=Loc::getMessage("SOTBIT_COMPLAINTS_BTN_ADD_TITLE")?>
        </a>
    </div>
    <?
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        array(
            'GRID_ID' => $arParams['FILTER_NAME'],
            'HEADERS' => $arResult["HEADERS"],
            'ROWS' => $arResult['ROWS'],
            'AJAX_MODE' => 'Y',
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",
            "ALLOW_COLUMNS_SORT" => true,
            "ALLOW_ROWS_SORT" => ['NAME'],
            "ALLOW_COLUMNS_RESIZE" => false,
            "ALLOW_HORIZONTAL_SCROLL" => false,
            "ALLOW_SORT" => true,
            "ALLOW_PIN_HEADER" => true,
            "ACTION_PANEL" => [],
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
            "PAGE_SIZES" => $arParams['PER_PAGE'],
            "DEFAULT_PAGE_SIZE" => 50
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );
    ?>
</div>