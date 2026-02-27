<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

if ($arResult["ERROR_MESSAGE"]){
    foreach ($arResult["ERRORS"] as $error) {
        ShowError($error);
    }?>
    <br><a href="<?=$arParams["PATH_TO_LIST"]?>"><?=GetMessage("B2BCABINET_ORDEREMPLATE_BACK_TO_LIST")?></a>
    <?
}
else{
    ?>
    <div class="orders-templates">
        <div class="card">
            <div class="card-body">
                <form id="ordertemplate-save" name="test"  method="post" action="<?=str_replace("#ID#", $arResult["ORDER_TEMPLATE"]["ID"], $arParams["PATH_TO_DETAIL"])?>" enctype="multipart/form-data">
                    <div class="ordertemplates-add-form">
                        <div class="ordertemplates-add-form__description flex-shrink-0">
                            <label class="form-label mb-0">
                                <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_TEMPLATE_NAME")?>
                            </label>
                        </div>
                        <?if ($arResult["EDIT"] != "Y") : ?>
                            <span><?= $arResult["ORDER_TEMPLATE"]["NAME"] ?></span>
                            <?if($arResult["ORDER_TEMPLATE"]["USER_ID"] == $USER->GetID()):?>
                                <button type="button" onclick="editTemplate()" class="btn btn-edit btn-sm btn-link btn-icon text-primary" title="<?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_EDIT")?>">
                                <i class="ph-pencil-simple"></i>
                                
                            </button>
                            <?endif;?>
                        <?else: ?>
                            <input class="ordertemplates-add-form__input form-control"
                                type="text" name="TEMPLATE_NAME"
                                value="<?= $arResult["ORDER_TEMPLATE"]["NAME"] ?>"
                                required
                        >
                        <?endif;?>
                    </div>
                    <?if(Loader::includeModule('sotbit.auth') && $arResult["EXTENDED_VERSION_COMPANIES"] == "Y"):?>
                        <div class="ordertemplates-add-form">
                            <div class="ordertemplates-add-form__description">
                                <label class="form-label mb-0">
                                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_COMPANIES")?>
                                </label>
                            </div>
                            <?if ($arResult["EDIT"] != "Y") : ?>
                                <span>
                                <?foreach ($arResult["USER_COMPANY"] as $id => $company) {
                                    if (in_array($id, is_array($arResult["COMPANIES"][$arParams["ID"]]) ? $arResult["COMPANIES"][$arParams["ID"]] : [])) {
                                        echo $company['COMPANY_NAME'] . " ";
                                    }
                                }?>
                                </span>
                            <?else: ?>
                                <select
                                        id="company-list"
                                        name="COMPANY[]"
                                        class="form-control select"
                                        multiple
                                        <?=$arResult["EDIT"] == "Y" ? "" : "disabled"?>
                                >
                                    <?foreach ($arResult["USER_COMPANY"] as $id => $company):?>
                                        <option value="<?=$id?>" <?=in_array($id, is_array($arResult["COMPANIES"][$arParams["ID"]]) ? $arResult["COMPANIES"][$arParams["ID"]] : [])? "selected" : ""?>><?=$company['COMPANY_NAME']?></option>
                                    <?endforeach;?>
                                </select>
                            <?endif;?>
                        </div>
                    <?endif;?>
                    <input type="hidden" name="action" value="save">

                    <? if ($arResult["EDIT"] == "Y"):?>
                        <div class="mt-4 d-flex gap-3">
                        <button type="button" class="btn btn-primary save-btn" onclick="saveTemplate()">
                            <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_SAVE")?>
                        </button>
                        <button type="button" class="btn" onclick="resetEdit()">
                            <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_RESER")?>
                        </button>
                        </div>
                    <? endif; ?>
                </form>
            </div>
        </div>
        <div class="orders-templates-positions__wrapper">
            <div class="main-ui-filter-search-wrap">
                <div class="main-ui-filter-search-wrapper">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.ui.filter",
                        "b2bcabinet",
                        array(
                            "FILTER_ID" => "ORDER_TEMPLATE_DETAIL",
                            "GRID_ID" => "ORDER_TEMPLATE_DETAIL",
                            'FILTER' => [
                                ['id' => 'ID', 'name' => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_ID"), 'type' => 'integer'],
                                ['id' => 'NAME', 'name' => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_NAME"), 'type' => 'string'],
                            ],
                            "ENABLE_LIVE_SEARCH" => true,
                            "ENABLE_LABEL" => true,
                            "COMPONENT_TEMPLATE" => ".default"
                        ),
                        false
                    );
                    ?>
                </div>
            </div>
            <div class="orders-templates__table-container mb-3">
                <?$APPLICATION->IncludeComponent(
                    'bitrix:main.ui.grid',
                    'simple',
                    array(
                        'GRID_ID' => 'ORDER_TEMPLATE_DETAIL',
                        'HEADERS' => array(
                            array("id" => "ID", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_ID"), "default" => false, "editable" => false,),
                            array("id" => "NAME", "name" =>GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_NAME"), "default" => true, "editable" => false,),
                            array("id" => "QNT", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_COUNT"),  "default" => true, "editable" => false, "align" => "right"),
                            array("id" => "PRICE", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_PRICE"), "default" => true, "editable" => false, "align" => "right"),
                        ),
                        'ROWS' => $arResult['ROWS'],
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
                        'NAV_STRING' => $arResult['NAV_STRING_REQUEST'],
                        "TOTAL_ROWS_COUNT" => count(is_array($arResult['ROWS']) ? $arResult['ROWS'] : []),
                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                        "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
                        "DEFAULT_PAGE_SIZE" => 50,
                    ),
                    $component,
                    array('HIDE_ICONS' => 'Y')
                );
                ?>
            </div>
        </div>

        <div class="card card-position-sticky">
            <div class="card-body d-flex align-items-center gap-3  flex-nowrap">
                <? if ($arResult["EDIT"] != "Y"):?>
                    <div class="orders-templates__dropdown_menu">
                        <button type="button" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ph-dots-three-vertical"></i>
                            <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_ACTION")?>
                        </button>
                        <?
                        $fileName = $arResult["ORDER_TEMPLATE"]["NAME"];
                        $donwLoadFun = sprintf("excelOut('%s')", $fileName);
                        ?>
                        <div class="dropdown-menu">
                            <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#modal_order-add-basket">
                                <i class="me-2 ph-plus"></i>
                                <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_ORDER")?>
                            </button>
                            <button onclick="<?=$donwLoadFun?>" class="dropdown-item text-primary">
                                <i class="me-2 ph-arrow-line-up"></i>
                                <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_OUT_EXCEL")?>
                            </button>
                            <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#modal_popup-order-remove">
                                <i class="me-2 ph-trash"></i>
                                <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_DELETE")?>
                            </button>
                        </div>
                    </div>
                <?endif;?>
                <div class="detail-ordertemplate__total-block">
                    <? if ($arResult["TOTAL_QUANTITY"]):?>
                        <div class="templates__summary-item">
                            <span><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_FOOTER_COUNT")?></span>
                            <span class="orders-templates__summary-description">
                                <?= $arResult["TOTAL_QUANTITY"] ?>
                            </span>
                    </div>
                    <? endif; ?>

                    <? if ($arResult["TOTAL_PRICE"]):?>
                        <div class="templates__summary-item">
                            <span><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_FOOTER_PRICE")?></span>
                            <strong class="orders-templates__summary-description">
                                <?= $arResult["TOTAL_PRICE"] ?>
                            </strong>
                    </div>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </div>


    <div id="modal_popup-order-remove" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_TITLE")?>
                    </h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_DELETE_DESCR")?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn-primary" onclick="removeTemplate()">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_DELETE")?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" data-bs-toggle="modal" data-target="#modal_popup-order-remove">

    <div id="modal_order-add-basket" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_TITLE")?>
                    </h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_DESCR")?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn-primary" onclick="addToBasket()">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_BTN")?>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <button type="button" id="btn_modal_order-remove-success" style="display: none;" data-bs-toggle="modal" data-bs-target="#modal_order-remove-success"></button>
    <div id="modal_order-remove-success" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-modal text-white">
                    <h5 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_SUCCESS_TITLE")?>
                    </h5>
                    <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_SUCCESS_DESCR", ["#TEMPLATE_NAME#" => $arResult["ORDER_TEMPLATE"]["NAME"]])?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  onclick="goToList();">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_OK")?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var list_url = '<?=$arParams["PATH_TO_LIST"]?>';
        var template_name = '<?=$arResult["ORDER_TEMPLATE"]["NAME"]?>';
        var template_id = '<?=$arResult["ORDER_TEMPLATE"]["ID"]?>';
        var path_to_detail = '<?=$arParams["PATH_TO_DETAIL"]?>';
        var path_to_basket = '<?=\Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'BASKET_URL', '', SITE_ID)?>';
        var site_path = '<?=SITE_DIR?>';
        var tableHeader = <?=CUtil::PhpToJSObject($arResult["TABLE_HEADER"])?>;
        var filterProps = <?=CUtil::PhpToJSObject($arResult["FILTER_DOCUMENT"])?>;
        var priceCodes = <?=CUtil::PhpToJSObject($arParams['PRICE_CODE'])?>;
        var baseCurrency = '<?=CCurrency::GetBaseCurrency()?>';
        var quantity = <?=CUtil::PhpToJSObject($arResult["QUANTITY_PRODUCTS"])?>;

        // SelectButton({
        //     selectId: "company-list"
        // })

    </script>
<?
}
