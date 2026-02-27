<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>
<div class="ordertemplates-list-wrap" id="ordertemplates-list-wrap">
    <div class="ordertemplates-filter-wrap">
        <div class="main-ui-filter-search-wrapper">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:main.ui.filter",
                "b2bcabinet",
                array(
                    "FILTER_ID" => "TEMPLATE_LIST",
                    "GRID_ID" => "TEMPLATE_LIST",
                    'FILTER' => [
                        ['id' => 'ID', 'name' => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_ID"), 'type' => 'string'],
                        ['id' => 'NAME', 'name' => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_NAME"), 'type' => 'string'],
                        ['id' => 'DATE_CREATE', 'name' => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_DATE_CREATE"), 'type' => 'date'],
                    ],
                    "ENABLE_LIVE_SEARCH" => true,
                    "ENABLE_LABEL" => true,
                    "COMPONENT_TEMPLATE" => "b2bcabinet"
                ),
                false
            );
            ?>
        </div>
    </div>
    <?
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        array(
            'GRID_ID' => 'TEMPLATE_LIST',
            'HEADERS' => array(
                array("id" => "ID", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_ID"), "sort" => "ID", "default" => false, "editable" => false),
                array("id" => "NAME", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_NAME"), "sort" => "NAME", "default" => true, "editable" => false),
                array("id" => "DATE_CREATE", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_DATE_CREATE"), "sort"=>"DATE_CREATE", "default" => true, "editable" => false),
                array("id" => "USER", "name" => GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_HEADER_USER"), "default" => true, "editable" => false),
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
            "TOTAL_ROWS_COUNT" => $arResult['TOTAL_ROWS_COUNT'],
            "CURRENT_PAGE" => $arResult['CURRENT_PAGE'],
            "PAGE_SIZES" => $arParams['ORDERS_PER_PAGE'],
            "DEFAULT_PAGE_SIZE" => 50,
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );
    ?>

    <section class="b2b_table__footer">
        <div class="b2b_table__footer-wrapper">
            <div class="b2b_table__footer__actions">
                <button type="button" class="btn btn-light btn-actions" data-toggle="dropdown">
                        <span class="ladda-label ">
                            <i class="icon-more2"></i>
                            <?=Loc::getMessage("CT_BZ_ACTION_BUTTON")?>
                        </span>
                </button>
                <div class="dropdown-menu" >
                    <?php
                    $APPLICATION->IncludeComponent(
                        "sotbit:b2bcabinet.ordertemplate.add",
                        "",
                        [
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "LIST_PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
                        ],
                        false
                    );
                    ?>
                    <?
                    $APPLICATION->IncludeComponent(
                        "sotbit:b2bcabinet.excel.export",
                        ".default",
                        array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "MODEL_OF_WORK" => "default",
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "HEADERS_COLUMN" => array(
                                0 => "NAME",
                                1 => "DETAIL_PICTURE",
                                2 => "DATE_CREATE",
                                3 => "",
                            ),
                            "PROPERTY_CODE" => array(
                                0 => "",
                                1 => "BRAND_REF",
                                2 => "MATERIAL",
                                3 => "COLOR",
                                4 => "",
                            ),
                            "OFFERS_PROPERTY_CODE" => array(
                                0 => "ARTNUMBER",
                                1 => "COLOR_REF",
                                2 => "SIZES_CLOTHES",
                                3 => "",
                            ),
                            "SORT_BY" => "NAME",
                            "SORT_ORDER" => "asc",
                            "ONLY_AVAILABLE" => "Y",
                            "FILTER_NAME" => $arParams["FILTER_NAME"],
                            "BTN_TITLE" => Loc::getMessage('SOTBIT_B2BCABINET_ORDERTEMPLATES_BTN_IMPORT'),
                            "USE_BTN" => "N"
                        ),
                        false
                    );
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<button type="button" id="btn_modal_order-add-basket" style="display: none;" data-toggle="modal" data-target="#modal_order-add-basket"></button>
<div id="modal_order-add-basket" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-main text-white">
                <h6 class="modal-title"><?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_ADD_BASKET_TITLE")?></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-description">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link btn-light" data-dismiss="modal">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_RESET")?>
                </button>
                <button type="button" class="btn btn_b2b" onclick="addToBasket()">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_ADD_BASKET_CREATE_ORDER")?>
                </button>
            </div>
        </div>
    </div>
</div>

<button type="button" id="btn_modal_order-remove" style="display: none;" data-toggle="modal" data-target="#modal_order-remove"></button>
<div id="modal_order-remove" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-main text-white">
                <h6 class="modal-title">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_TITLE")?>
                </h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-description">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link btn-light" data-dismiss="modal">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_RESET")?>
                </button>
                <button type="button" class="btn btn_b2b" onclick="removeTemplate()">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_DELETE")?>
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
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_REMOVE_TITLE")?>
                </h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-description">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn_b2b" data-dismiss="modal">
                    <?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_OK")?>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var site_path = '<?=SITE_DIR?>';
    var path_to_detail = '<?=$arParams["PATH_TO_DETAIL"]?>';
    var tableHeader = <?=CUtil::PhpToJSObject($arResult["TABLE_HEADER"])?>;
    var filterProps = <?=CUtil::PhpToJSObject($arResult["FILTER_DOCUMENT"])?>;
    var priceCodes = <?=CUtil::PhpToJSObject($arParams['PRICE_CODE'])?>;
    var id_products = <?=CUtil::PhpToJSObject($arResult["PRODUCTS"])?>;
    var quantity = <?=CUtil::PhpToJSObject($arResult["QUANTITY"])?>;
    var baseCurrency = '<?=CCurrency::GetBaseCurrency()?>';
    var add_basket_descr = '<?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_DESCRIPTION_ORDER")?>';
    var remove_descr = '<?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_DESCRIPTION_REMOVE")?>';
    var success_descr = '<?=GetMessage("SOTBIT_B2BCABINET_ORDERTEMPLATES_FORM_DESCRIPTION_REMOVE_SUCCESS")?>';
    var path_to_basket = '<?=\Bitrix\Main\Config\Option::get('sotbit.b2bcabinet', 'BASKET_URL', '', SITE_ID)?>';
</script>