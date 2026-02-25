<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<div class="main-ui-filter-search-wrapper">
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.ui.filter",
        "b2bcabinet",
        array(
            "FILTER_ID" => "DRAFT_LIST",
            "GRID_ID" => "DRAFT_LIST",
            'FILTER' => [
                ['id' => 'ID', 'name' => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_ID"), 'type' => 'string'],
                ['id' => 'NAME', 'name' => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_NAME"), 'type' => 'string'],
                ['id' => 'DATE_CREATE', 'name' => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_DATE_CREATE"), 'type' => 'date'],
            ],
            "ENABLE_LIVE_SEARCH" => true,
            "ENABLE_LABEL" => true,
            "COMPONENT_TEMPLATE" => ".default"
        ),
        false
    );
    ?>
</div>
<?
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    array(
        'GRID_ID' => 'DRAFT_LIST',
        'HEADERS' => array(
            array("id" => "ID", "name" => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_ID"), "sort" => "ID", "default" => false, "editable" => false),
            array("id" => "NAME", "name" => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_NAME"), "sort" => "NAME", "default" => true, "editable" => false),
            array("id" => "DATE_CREATE", "name" => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_DATE_CREATE"), "sort"=>"DATE_CREATE", "default" => true, "editable" => false),
            array("id" => "PRICE", "name" => GetMessage("SOTBIT_B2BCABINET_HEADER_TITLE_PRICE"), "default" => true, "editable" => false),
        ),
        'ROWS' => $arResult['ROWS'],
        'AJAX_MODE' => 'Y',

        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",

        "ALLOW_COLUMNS_SORT" => true,
        "ALLOW_ROWS_SORT" => ['NAME'],
        "ALLOW_COLUMNS_RESIZE" => true,
        "ALLOW_HORIZONTAL_SCROLL" => true,
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
        "TOTAL_ROWS_COUNT" => $arResult['ITEMS_COUNT'],
        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
        "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
        "DEFAULT_PAGE_SIZE" => 50
    ),
    $component,
    array('HIDE_ICONS' => 'Y')
);
?>

<div class="wrap-popup-window popup-draft-list" style="display: none;">
    <div class="modal-popup-bg" onclick="closeModal();">&nbsp;</div>
    <div class="popup-window">
        <div class="popup-close" onclick="closeModal();"></div>
        <div class="popup-content">
            <div id="draft-remove-block">
                <p class="draft-remove-title">
                    <?=GetMessage("SOTBIT_B2BCABINET_POPUP_CONFIRMATION_OF_ACTION")?>
                </p>
                <div class="draft-remove-description"></div>
                <div class="confirm__row">
                    <button type="button" class="btn btn-light  btn_cancel" onclick="closeModal()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn_b2b btn_remove" onclick="removeDraft()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_REMOVE")?>
                    </button>
                </div>
            </div>
            <div id="remove-success-block">
                <p class="draft-remove-title-success">
                    <?=GetMessage("SOTBIT_B2BCABINET_POPUP_CONFIRMATION_OF_ACTION")?>
                </p>
                <div class="draft-remove-description-success"></div>
                <div class="confirm__row">
                    <button type="button" class="btn btn_b2b btn_ok" onclick="closeModal()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_OK")?>
                    </button>
                </div>
            </div>
            <div id="draft-create-order-block">
                <p class="draft-create-order-title">
                    <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_CREATE_ORDER_TITLE")?>
                </p>
                <div class="draft-create-order-description"></div>
                <div class="confirm__row">
                    <button type="button" class="btn btn-light  btn_cancel" onclick="closeModal()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn_b2b btn_create-order" onclick="createOrder()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_CREATE_ORDER")?>
                    </button>
                </div>
            </div>
            <div id="draft-create-ordertemplate-block">
                <p class="draft-remove-title">
                    <?=GetMessage("SOTBIT_B2BCABINET_POPUP_CONFIRMATION_OF_ACTION")?>
                </p>
                <div class="draft-create-ordertemplate-description"></div>
                <div class="confirm__row">
                    <button type="button" class="btn btn-light  btn_cancel" onclick="closeModal()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn_b2b btn_create-ordertemplate" onclick="createOrdertemplate()">
                        <?=GetMessage("SOTBIT_B2BCABINET_POPUP_BTN_CREATE_ORDERTEMPLATE")?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var basketPath = '<?=\Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'BASKET_URL', '', SITE_ID)?>';
    var createOrderDescription = '<?=GetMessage("SOTBIT_B2BCABINET_POPUP_ORDER_CREATE")?>';
    var successDelete = '<?=GetMessage("SOTBIT_B2BCABINET_POPUP_REMOVE_DESCRIPTION_SUCCESS")?>';
    var deleteDescr = '<?=GetMessage("SOTBIT_B2BCABINET_POPUP_REMOVE_DESCRIPTION")?>';
    var ordertemplateDescr = '<?=GetMessage("SOTBIT_B2BCABINET_POPUP_CREATE_ORDERTEMPLATE_DESCRIPTION")?>';
</script>