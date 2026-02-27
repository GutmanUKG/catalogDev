<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="ordertemplate-save" name="test"  method="post" action="<?=str_replace("#ID#", $arResult["ORDER_TEMPLATE"]["ID"], $arParams["PATH_TO_DETAIL"])?>" enctype="multipart/form-data">
                            <div class="ordertemplates-add-form">
                                <div class="ordertemplates-add-form__description">
                                    <span>
                                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_TEMPLATE_NAME")?>
                                    </span>
                                </div>
                                <input class="ordertemplates-add-form__input form-control"
                                       type="text" name="TEMPLATE_NAME"
                                       value="<?= $arResult["ORDER_TEMPLATE"]["NAME"] ?>"
                                        <?=$arResult["EDIT"] == "Y" ? "" : "disabled"?>
                                       required
                                >
                            </div>
                            <?if($arResult["EXTENDED_VERSION_COMPANIES"] == "Y"):?>
                                <div class="ordertemplates-add-form">
                                    <div class="ordertemplates-add-form__description">
                                        <span>
                                             <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_COMPANIES")?>
                                        </span>
                                    </div>
                                    <select
                                            name="COMPANY[]"
                                            class="form-control select index_blank-sorting-select select2-hidden-accessible"
                                            multiple
                                            <?=$arResult["EDIT"] == "Y" ? "" : "disabled"?>
                                    >
                                        <?foreach ($arResult["USER_COMPANY"] as $id => $company):?>
                                            <option value="<?=$id?>" <?=in_array($id, is_array($arResult["COMPANIES"][$arParams["ID"]]) ? $arResult["COMPANIES"][$arParams["ID"]] : [])? "selected" : ""?>><?=$company['COMPANY_NAME']?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            <?endif;?>
                            <input type="hidden" name="action" value="save">
                        </form>
                        <div class="card orders-templates-positions__wrapper">
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

                                <? if ($arResult["EDIT"] != "Y"):?>
                                    <div class="header-elements-inline ordertemplate-action-btn mr-3">
                                        <div class="btn-group orders-templates__dropdown_menu">
                                            <button type="button" class="btn btn_b2b" data-toggle="dropdown" aria-expanded="false">
                                                <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_ACTION")?>
                                            </button>
                                            <?
                                            $fileName = $arResult["ORDER_TEMPLATE"]["NAME"];
                                            $donwLoadFun = sprintf("excelOut('%s')", $fileName);
                                            ?>
                                            <div class="dropdown-menu b2b_detail_order__second__tab__btn__block" x-placement="bottom-end">
                                                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_order-add-basket">
                                                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_ORDER")?>
                                                </button>
                                                <button onclick="<?=$donwLoadFun?>" class="dropdown-item"><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_OUT_EXCEL")?></button>
                                                <?if($arResult["ORDER_TEMPLATE"]["USER_ID"] == $USER->GetID()):?>
                                                    <button onclick="editTemplate()" class="dropdown-item"><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_EDIT")?></button>
                                                <?endif;?>
                                                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_popup-order-remove">
                                                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_ACTION_CREATE_DELETE")?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?endif;?>
                            </div>
                            <div class="orders-templates__table-container">
                                <?$APPLICATION->IncludeComponent(
                                    'bitrix:main.ui.grid',
                                    '',
                                    array(
                                        'GRID_ID' => 'ORDER_TEMPLATE_DETAIL',
                                        'HEADERS' => array(
                                            array("id" => "ID", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_ID"), "default" => false, "editable" => false),
                                            array("id" => "NAME", "name" =>GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_NAME"), "default" => true, "editable" => false),
                                            array("id" => "QNT", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_COUNT"),  "default" => true, "editable" => false),
                                            array("id" => "PRICE", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_HEADER_PRICE"), "default" => true, "editable" => false),
                                        ),
                                        'ROWS' => $arResult['ROWS'],
                                        'AJAX_MODE' => 'Y',

                                        "AJAX_OPTION_JUMP" => "N",
                                        "AJAX_OPTION_STYLE" => "N",
                                        "AJAX_OPTION_HISTORY" => "N",

                                        "ALLOW_COLUMNS_SORT" => true,
                                        "ALLOW_ROWS_SORT" => ['ID', 'COMPANY', 'WORK_POSITION'],
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
                                        'NAV_STRING' => $arResult['NAV_STRING_REQUEST'],
                                        "TOTAL_ROWS_COUNT" => count(is_array($arResult['ROWS']) ? $arResult['ROWS'] : []),
                                        "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
                                        "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
                                        "DEFAULT_PAGE_SIZE" => 50
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                );
                                ?>
                            </div>
                        </div>


                        <div class="detail-ordertemplate__total-block">
                            <? if ($arResult["TOTAL_QUANTITY"]):?>
                                <div class="orders-templates__summary main-grid-panel-cell">
                                    <p class="templates__summary-item">
                                        <span><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_FOOTER_COUNT")?></span>
                                        <span class="orders-templates__summary-description">
                                            <?= $arResult["TOTAL_QUANTITY"] ?>
                                        </span>
                                    </p>
                                </div>
                            <? endif; ?>

                            <? if ($arResult["TOTAL_PRICE"]):?>
                                <div class="orders-templates__summary main-grid-panel-cell">
                                    <p class="templates__summary-item">
                                        <span><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_FOOTER_PRICE")?></span>
                                        <span class="orders-templates__summary-description">
                                            <?= $arResult["TOTAL_PRICE"] ?>
                                        </span>
                                    </p>
                                </div>
                            <? endif; ?>
                        </div>
                        <? if ($arResult["EDIT"] == "Y"):?>
                             <div class="card-body ordertemplates-btn-row">
                                <button type="button" class="btn btn-light" onclick="<?=$_GET["edit"]=="Y"? "resetEdit()" : "showFormRemoveSave()"?>">
                                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_RESER")?>
                                </button>
                                 <button type="button" class="btn btn_b2b save-btn" onclick="saveTemplate()">
                                     <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_BUTTON_SAVE")?>
                                 </button>
                             </div>
                        <? endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div id="modal_popup-order-remove" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-main text-white">
                    <h6 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_TITLE")?>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_DELETE_DESCR")?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link btn-light" data-dismiss="modal">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn_b2b" onclick="removeTemplate()">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_DELETE")?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" data-toggle="modal" data-target="#modal_popup-order-remove">

    <div id="modal_order-add-basket" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-main text-white">
                    <h6 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_TITLE")?>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_DESCR")?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link btn-light" data-dismiss="modal">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_BTN_RESET")?>
                    </button>
                    <button type="button" class="btn btn_b2b" onclick="addToBasket()">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_CREATE_ORDER_BTN")?>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <button type="button" id="btn_modal_order-remove-success" style="display: none;" data-toggle="modal" data-target="#modal_order-remove-success"></button>
    <div id="modal_order-remove-success" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-main text-white">
                    <h6 class="modal-title">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_SUCCESS_TITLE")?>
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-description">
                        <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_DETAIL_POPUP_SUCCESS_DESCR", ["#TEMPLATE_NAME#" => $arResult["ORDER_TEMPLATE"]["NAME"]])?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn_b2b"  onclick="goToList();">
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
    </script>
<?
}
