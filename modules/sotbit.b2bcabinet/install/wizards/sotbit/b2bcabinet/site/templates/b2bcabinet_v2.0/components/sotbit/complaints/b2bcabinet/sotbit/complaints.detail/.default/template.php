<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Text\HtmlFilter,
    Sotbit\B2bCabinet\Form\FormConstructor,
    Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);
?>

<div class="blank_detail">
    <div class="detail-menu d-flex justify-content-between align-items-center overflow-auto">
        <ul class="nav nav-tabs nav-mainpage-tabs">
            <li class="nav-item">
                <a href="#main-tab" class="nav-link active show" data-bs-toggle="tab"><?=Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_TAB_MAIN_DATA")?></a>
            </li>
            <li class="nav-item">
                <a href="#complaint-positions" class="nav-link" data-bs-toggle="tab"><?=Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_TAB_POSITIONS")?></a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <!--main-tab-->
        <div class="tab-pane fade show active" id="main-tab">
            <div class="row">
                <div class="col-lg-6">
                    <? foreach ($arResult["FORM_GROUPS"] as $groups): ?>
                        <? foreach ($groups as $group): ?>
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <h6 class="card-title mb-0 fw-bold">
                                        <?= HtmlFilter::encode($group["NAME"]);?>
                                    </h6>
                                    <div class="d-inline-flex ms-auto">
                                        <a class="text-body px-2"
                                           data-card-action="collapse"
                                           >
                                           <i class="ph ph-caret-down"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="collapse show">
                                    <div class="card-body pt-0">
                                        <dl class="card-content">
                                            <? foreach ($group["ITEM_ROWS"] as $row): ?>
                                                <div class="card-content__row">
                                                    <dt>
                                                        <?= HtmlFilter::encode($row["LABEL"]) ?>
                                                    </dt>
                                                    <dd class="d-flex flex-column">
                                                        <? foreach ($row["ITEMS"] as $item){
                                                            switch ($item['INPUT_TYPE']) {
                                                                case 'MEDIA':
                                                                    ?>
                                                                    <a href="<?=$item["ATTRIBUTES"]["SRC"]->getUri() ?>">
                                                                        <?=$item["ATTRIBUTES"]["NAME"]?>
                                                                    </a>
                                                                    <?
                                                                    break;
                                                                default:
                                                                    ?>
                                                                    <span>
                                                                        <?= HtmlFilter::encode($item["ATTRIBUTES"]["VALUE"]) ?>
                                                                    </span>
                                                                    <?
                                                                    break;
                                                            }
                                                        } ?>
                                                    </dd>
                                                </div>
                                            <? endforeach; ?>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    <? endforeach; ?>
                </div>
                    <?
                        // $form = new FormConstructor("COMPLAINT_DETAIL", $arResult["FORM_SETTINGS"],  $arResult["FORM_GROUPS"]);
                        // $form->showForm("COMPLAINT_DETAIL");
                    ?>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header card-p-1">
                            <h6 class="card-title mb-0 fw-bold">
                                <?= Loc::getMessage('SOTBIT_COMPLAINTS_DETAIL_CARD_SUPPORT') ?>
                            </h6>
                        </div>
                        <div class="card-body pt-0 support">
                            <?
                                $APPLICATION->IncludeComponent(
                                    "bitrix:support.ticket.edit",
                                    "b2bcabinet_detail",
                                    array(
                                        "AJAX_MODE" => "Y",
                                        "MESSAGES_PER_PAGE" => "20",
                                        "MESSAGE_MAX_LENGTH" => "70",
                                        "MESSAGE_SORT_ORDER" => "asc",
                                        "SET_PAGE_TITLE" => "N",
                                        "SHOW_COUPON_FIELD" => "N",
                                        "TICKET_EDIT_TEMPLATE" => "#",
                                        "COMPONENT_TEMPLATE" => "b2bcabinet_complaints_detail",
                                        "ID" => $arResult['TICKET_ID'],
                                        "COMPLAINT_ID" => $arResult["ID"],
                                        "TICKET_LIST_URL" => $arParams["PATH_TO_LIST"],
                                        "TITLE_SUPPORT" => Loc::getMessage("SOTBIT_COMPLAINTS_DETAIL_SUPPORT_TITLE", ["#NAME#" => $arResult["COMPLAINT"]["FIELDS"]["NAME"]])
                                    ),
                                    false
                                );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--complaint-positions-->
        <div class="tab-pane fade " id="complaint-positions">
            <div class="row">
                <?
                if ($arResult["POSITIONS"]["ERRORS"]) {
                    foreach ($arResult["POSITIONS"]["ERRORS"] as $error) {
                        ShowError($error);
                    }
                }

                $APPLICATION->IncludeComponent(
                    'bitrix:main.ui.grid',
                    '',
                    array(
                        'GRID_ID' => "COMPLAINT_POSITIONS",
                        'HEADERS' => $arResult["HEADERS"],
                        'ROWS' => $arResult["GRID_POSITION"],
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
                        "TOTAL_ROWS_COUNT" => count(is_array($arResult['GRID_POSITION']) ? $arResult['GRID_POSITION']: []),
                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                        "PAGE_SIZES" => $arParams['PER_PAGE'],
                        "DEFAULT_PAGE_SIZE" => 50
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
            </div>
        </div>
    </div>
</div>