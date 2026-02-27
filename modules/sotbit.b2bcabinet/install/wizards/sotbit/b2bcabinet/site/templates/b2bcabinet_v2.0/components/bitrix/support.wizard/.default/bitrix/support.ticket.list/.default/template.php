<?

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->AddHeadScript("/bitrix/js/main/utils.js");
?>

<div class="support_page">
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.ui.filter",
        "b2bcabinet",
        array(
            "FILTER_ID" => "TICKET_LIST",
            "GRID_ID" => "TICKET_LIST",
            "FILTER" => $arResult["FILTER"],
            "ENABLE_LIVE_SEARCH" => true,
            "ENABLE_LABEL" => true,
            "COMPONENT_TEMPLATE" => "b2bcabinet"
        ),
        false
    );
    ?>
    <?
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => 'TICKET_LIST',
            'HEADERS' => [
                // [
                //     "id" => "LAMP",
                //     "name" => Loc::getMessage('SUP_LAMP'),
                //     "sort" => "LAMP",
                //     "default" => true
                // ],
                // [
                //     "id" => "ID",
                //     "name" => Loc::getMessage('SUP_ID'),
                //     "sort" => "ID",
                //     "default" => true
                // ],
                [
                    "id" => "TITLE",
                    "name" => Loc::getMessage('SUP_TITLE'),
                    "default" => true
                ],
                [
                    "id" => "TIMESTAMP_X",
                    "name" => Loc::getMessage('SUP_TIMESTAMP'),
                    "sort" => "TIMESTAMP_X",
                    "default" => true
                ],
                [
                    "id" => "MODIFIED_BY",
                    "name" => Loc::getMessage('SUP_MODIFIED_BY'),
                    "default" => true
                ],
                [
                    "id" => "MESSAGES",
                    "name" => Loc::getMessage('SUP_MESSAGES'),
                    "default" => true
                ],
            ],
            'ROWS' => $arResult['ROWS'],
            'FILTER_STATUS_NAME' => $arResult['FILTER_STATUS_NAME'],
            'AJAX_MODE' => 'Y',

            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",

            "ALLOW_COLUMNS_SORT" => true,
            "ALLOW_ROWS_SORT" => [
                'ID',
                'LAMP',
                'TIMESTAMP_X'
            ],
            "ALLOW_COLUMNS_RESIZE" => true,
            "ALLOW_HORIZONTAL_SCROLL" => false,
            "ALLOW_SORT" => false,
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
            'NAV_STRING' => $arResult['NAV_STRING'],
            "TOTAL_ROWS_COUNT" => count(is_array($arResult['ROWS']) ? $arResult['ROWS'] : []),
            "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
            "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
            "DEFAULT_PAGE_SIZE" => 50,
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    );
    ?>

    <div class="card card-position-sticky">
        <div class="card-body d-flex gap-3">
            <button class="btn btn-primary" onclick="openModal('addSupport')" href="<?= $APPLICATION->GetCurPage() . "?show_wizard=Y&end_wizard=Y&ajax=Y" ?>"><i></i><?= Loc::getMessage("SUP_ASK") ?></button>
        </div>
    </div>
</div>

<div class="modal fade" id="addSupport" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h5 class="modal-title"><?=Loc::getMessage('MODAL_ADD_TITLE')?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>