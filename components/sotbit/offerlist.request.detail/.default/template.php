<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
loc::loadLanguageFile(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('SO_REQUEST_DETAIL_TITLE', ['#ID#' => $arParams['ID']]));
$APPLICATION->AddChainItem(Loc::getMessage('SO_REQUEST_DETAIL_TITLE', ['#ID#' => $arParams['ID']]));
?>

<?if ($arResult["REQUEST"]):?>
    <div class="offer_request__card">
        <div class="d-flex gap-3 flex-wrap flex-md-nowrap">
            <div class="card w-100">
                <div class="card-header card-p-1">
                    <h6 class="card-title mb-0 fw-bold"><?=Loc::getMessage('SO_REQUEST_CARD_TITLE_1')?></h6>
                </div>
                <div class="card-body pt-0">
                    <dl class="card-content">
                        <div class="card-content__row">
                            <dt><?=Loc::getMessage('SO_REQUEST_DETAIL_DATE_CREATE');?></dt>
                            <dd><?=$arResult["REQUEST"]["DATE_CREATE"]->toString();?></dd>
                        </div>
                        <div class="card-content__row">
                            <dt><?=Loc::getMessage('SO_REQUEST_DETAIL_STATUS');?></dt>
                            <dd><?=$arResult['REQUEST_STATUS_LIST'][$arResult["REQUEST"]["STATUS"]]?></dd>
                        </div>
                        <div class="card-content__row">
                            <dt><?=Loc::getMessage('SO_REQUEST_DETAIL_COMMENT');?></dt>
                            <dd><?=$arResult["REQUEST"]["COMMENT"]?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <?if ($arResult["REQUEST"]['FIELDS']):?>
                <div class="card w-100">
                    <div class="card-header card-p-1">
                        <h6 class="card-title mb-0 fw-bold"><?=Loc::getMessage('SO_REQUEST_CARD_TITLE_2')?></h6>
                    </div>
                    <div class="card-body pt-0">
                        <dl class="card-content">
                            <?foreach ($arResult["REQUEST"]['FIELDS'] as $code => $field):?>
                                <div class="card-content__row">
                                    <dt><?=$arResult['REQUEST_FIELDS_ALIAS'][$code] ?: $code?></dt>
                                    <dd><?=$field?></dd>
                                </div>
                            <?endforeach;?>
                        </dl>
                    </div>
                </div>
            <?endif;?>
        </div>

        <div class="card mt-2">
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:main.ui.grid',
                'simple',
                array(
                    'GRID_ID' => $arParams['FILTER_NAME'],
                    'HEADERS' => $arResult["HEADERS"],
                    'ROWS' => $arResult['PRODUCTS_ROW'],
                    'AJAX_MODE' => 'Y',
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",

                    "ALLOW_COLUMNS_SORT" => false,
                    "ALLOW_ROWS_SORT" => ['NAME'],
                    "ALLOW_COLUMNS_RESIZE" => false,
                    "ALLOW_HORIZONTAL_SCROLL" => false,
                    "ALLOW_SORT" => false,
                    "ALLOW_PIN_HEADER" => true,
                    "ACTION_PANEL" => [],

                    "SHOW_CHECK_ALL_CHECKBOXES" => false,
                    "SHOW_ROW_CHECKBOXES" => false,
                    "SHOW_ROW_ACTIONS_MENU" => true,
                    "SHOW_GRID_SETTINGS_MENU" => true,
                    "SHOW_NAVIGATION_PANEL" => true,
                    "SHOW_PAGINATION" => true,
                    "SHOW_SELECTED_COUNTER" => false,
                    "SHOW_TOTAL_COUNTER" => false,
                    "SHOW_PAGESIZE" => true,
                    "SHOW_ACTION_PANEL" => true,

                    "ENABLE_COLLAPSIBLE_ROWS" => true,
                    'ALLOW_SAVE_ROWS_STATE' => true,

                    "SHOW_MORE_BUTTON" => false,
                    '~NAV_PARAMS' => $arResult['GET_LIST_PARAMS']['NAV_PARAMS'],
                    'NAV_OBJECT' => $arResult['NAV_OBJECT'],
                    'NAV_STRING' => $arResult['NAV_STRING'],
                    "TOTAL_ROWS_COUNT" => $arResult['PRODUCTS_ROW'] ? count($arResult['PRODUCTS_ROW']) : 0,
                    "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                    "PAGE_SIZES" => $arParams['PER_PAGE'],
                    "DEFAULT_PAGE_SIZE" => 50
                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );
            ?>

            <div class="offer_request__footer">
                <div class="offer_request__footer__count-product">
                    <span><?=Loc::getMessage('SO_REQUEST_FOOTER_COUNT', ['#COUNT#' => $arResult['PRODUCTS_ROW'] ? count($arResult['PRODUCTS_ROW']) : 0])?>
                </div>
                <div class="offer_request__footer__totalRow">
                    <div class="total-qnt">
                        <span>
                            <?=Loc::getMessage('SO_REQUEST_FOOTER_TOTAL_QNT', ['#QUANTITY#' => $arResult['REQUEST']['ORDER']['ORDER_FIELDS']['TOTAL_QUANTITY']])?>
                        </span>
                    </div>

                    <div class="total-sum">
                        <span><?=Loc::getMessage('SO_REQUEST_FOOTER_TOTAL_TEXT')?></span>
                        <span class="fw-semibold"><?=CurrencyFormat($arResult["REQUEST"]["ORDER"]["ORDER_FIELDS"]["PRICE"], $arResult["REQUEST"]["ORDER"]["ORDER_FIELDS"]["CURRENCY"])?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?endif;?>