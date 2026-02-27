<?

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<div class="staff_wrapper">

    <div class="detail-menu overflow-auto">
        <ul class="nav nav-tabs nav-mainpage-tabs">
            <li class="nav-item">
                <a href="#basic-tab1" class="nav-link active" data-bs-toggle="tab">
                    <?=GetMessage("SOTBIT_COMPANY_STAFF_LIST_TAB_1_TEXT")?>
                </a>
            </li>
            <?if($arResult['CONFIRM_N']['ROWS']):?>
                <li class="nav-item">
                    <a href="#basic-tab-request" class="nav-link" data-bs-toggle="tab">
                        <?=GetMessage("SOTBIT_COMPANY_STAFF_LIST_TAB_2_TEXT")?>
                    </a>
                </li>
            <?endif;?>
        </ul>
    </div>
    <div class="tab-content mt-4">
        <div class="tab-pane fade show active" id="basic-tab1">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:main.ui.filter",
                "b2bcabinet",
                array(
                    "FILTER_ID" => "STAFF_LIST",
                    "GRID_ID" => "STAFF_LIST",
                    'FILTER' => [
                        ['id' => 'ID', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_ID_TITLE"), 'type' => 'string'],
                        ['id' => 'FULL_NAME', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_FULL_NAME_TITLE"), 'type' => 'string'],
                        ['id' => 'COMPANY', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_COMPANY_TITLE"), 'type' => 'string'],
                        ['id' => 'WORK_POSITION', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_WORK_POSITION_TITLE"), 'type' => 'list', 'params' => ['multiple' => 'Y'], 'items' => $arResult["FILTER_ROLES"]],
                    ],
                    "ENABLE_LIVE_SEARCH" => true,
                    "ENABLE_LABEL" => true,
                    "COMPONENT_TEMPLATE" => ".default"
                ),
                false
            );
            ?>
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:main.ui.grid',
                '',
                array(
                    'GRID_ID' => 'STAFF_LIST',
                    'HEADERS' => array(
                        array("id" => "ID", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_ID_TITLE"), "sort" => "USER_ID", "default" => false, "editable" => false),
                        array("id" => "FULL_NAME", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_FULL_NAME_TITLE"), "sort" => "LAST_NAME", "default" => true, "editable" => false),
                        array("id" => "COMPANY", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_COMPANY_TITLE"),  "sort" => "NAME_COMPANY",  "default" => true,  "editable" => false),
                        array("id" => "WORK_POSITION", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_WORK_POSITION_TITLE"), "sort" => "ROLE", "default" => true, "editable" => false),
                        array("id" => "USER_SHOW_GROUPS", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_USER_SHOW_GROUPS_TITLE"), "default" => true, "editable" => false),
                    ),
                    'ROWS' => $arResult['CONFIRM_Y']['ROWS'],
                    'AJAX_MODE' => 'Y',

                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",

                    "ALLOW_COLUMNS_SORT" => true,
                    "ALLOW_ROWS_SORT" => ['ID', 'COMPANY', 'WORK_POSITION'],
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
                    'NAV_STRING' => $arResult['NAV_STRING_STAFF_A'],
                    "TOTAL_ROWS_COUNT" => $arResult["NAV_RECORD_COUNT_A"],
                    "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                    "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
                    "DEFAULT_PAGE_SIZE" => 50,
                    "VIEW_ACTIONS" => "table"
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );
            ?>
        </div>
        <?if($arResult['CONFIRM_N']['ROWS']):?>
            <div class="tab-pane fade" id="basic-tab-request">
                <?
                $APPLICATION->IncludeComponent(
                    "bitrix:main.ui.filter",
                    "b2bcabinet",
                    array(
                        "FILTER_ID" => "STAFF_UNCONFIRMED_LIST",
                        "GRID_ID" => "STAFF_UNCONFIRMED_LIST",
                        'FILTER' => [
                            ['id' => 'ID', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_ID_TITLE"), 'type' => 'string'],
                            ['id' => 'FULL_NAME', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_FULL_NAME_TITLE"), 'type' => 'string'],
                            ['id' => 'COMPANY', 'name' => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_COMPANY_TITLE"), 'type' => 'string'],
                        ],
                        "ENABLE_LIVE_SEARCH" => true,
                        "ENABLE_LABEL" => true,
                        "COMPONENT_TEMPLATE" => ".default"
                    ),
                    false
                );
                ?>
                <?
                $APPLICATION->IncludeComponent(
                    'bitrix:main.ui.grid',
                    '',
                    array(
                        'GRID_ID' => 'STAFF_UNCONFIRMED_LIST',
                        'HEADERS' => array(
                            array("id" => "ID", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_ID_TITLE"), "sort" => "USER_ID", "default" => false, "editable" => false),
                            array("id" => "FULL_NAME", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_FULL_NAME_TITLE"), "sort" => "LAST_NAME", "default" => true, "editable" => false),
                            array("id" => "COMPANY", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_COMPANY_TITLE"), "sort" => "NAME_COMPANY", "default" => true, "editable" => false),
                            array("id" => "USER_SHOW_GROUPS", "name" => GetMessage("SOTBIT_COMPANY_STAFF_HEADER_USER_SHOW_GROUPS_TITLE"), "default" => true, "editable" => false),
                        ),
                        'ROWS' => $arResult['CONFIRM_N']['ROWS'],
                        'AJAX_MODE' => 'Y',

                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",

                        "ALLOW_COLUMNS_SORT" => true,
                        "ALLOW_ROWS_SORT" => ['ID', 'COMPANY', 'WORK_POSITION'],
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
                        'NAV_STRING' => $arResult['NAV_STRING_STAFF_M'],
                        "TOTAL_ROWS_COUNT" => $arResult["NAV_RECORD_COUNT_M"],
                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                        "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
                        "DEFAULT_PAGE_SIZE" => 50,
                        "VIEW_ACTIONS" => "table"
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
            </div>
        <?endif;?>
    </div>
    <div class="card card-position-sticky">
        <div class="card-body d-flex align-items-center gap-3">
            <button id="staff-list__add-staff" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-staff-register" <?= !$arResult["IS_ADMIN"] ? 'disabled' : ''?>>
                <?=GetMessage("SOTBIT_COMPANY_STAFF_STAFF_REGISTER_BTN")?>
            </button>

            <?if(!empty($arResult['CONFIRM_Y']['ROWS'])):?>
                <div class="form-check">
                    <input type="checkbox" id="show-all-users" name="show-all-users" class="form-check-input" onclick="showAllUsers();" <?=($_SESSION["SHOW_ALL_USERS"] == "Y") ? 'checked' : ''?>>
                    <label for="show-all-users" class="form-check-label"><?=GetMessage("SOTBIT_COMPANY_STAFF_LABEL_SHOW_ALL")?></label>
                </div>
            <?endif;?>
        </div>
    </div>
</div>
<?if($arResult["IS_ADMIN"]):?>

                <?$APPLICATION->IncludeComponent(
	"sotbit:auth.company.staff.register", 
	".default", 
	array(
		"AUTH" => "N",
		"REQUIRED_FIELDS" => array(
			0 => "EMAIL",
		),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array(
			0 => "EMAIL",
			1 => "NAME",
		),
		"SUCCESS_PAGE" => "",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"USE_CAPTCHA" => "N",
		"USER_GROUPS" => array(
			0 => "2",
			1 => "3",
			2 => "4",
			3 => "5",
			4 => "6",
			5 => "7",
			6 => "8",
			7 => "9",
			8 => "10",
		),
		"ABILITY_TO_SET_ROLE" => "Y"
	),
	false
);?>
<?endif;?>

<script>
    BX.message({
        "SUCCESS_CONFIRM_TEXT": "<?=Loc::getMessage("SUCCESS_CONFIRM_TEXT")?>",
        "SUCCESS_UNCONFIRM_TEXT": "<?=Loc::getMessage("SUCCESS_UNCONFIRM_TEXT")?>",
        "QUESTION_DELETE": "<?=Loc::getMessage("SOTBIT_COMPANY_STAFF_QUESTION_DELETE")?>",
    });
</script>