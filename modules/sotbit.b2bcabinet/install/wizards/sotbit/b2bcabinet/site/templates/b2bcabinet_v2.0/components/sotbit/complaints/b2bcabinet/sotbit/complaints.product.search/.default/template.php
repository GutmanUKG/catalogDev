<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset,
    \Bitrix\Main\Localization\Loc;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/tables/datatables.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/tables/datatables/extensions/natural-sort.js");

?>
<h1><?=Loc::getMessage('SOTBIT_COMPLAINTS_SEARCH_TITLE')?></h1>

<div class="rounded-3 border bg-white overflow-auto">
    <table id="products-grid" class="table table-mobile-v2 datatable-highlight">
        <thead class="border-bottm">
            <tr>
                <th><?=Loc::getMessage('SOTBIT_COMPLAINTS_SEARCH_TABLE_COL_NAME')?></th>
                <th class="w-0"><a role="button" class="list-icons-item" onclick="changeProd(<?=json_encode(array_keys($arResult["ITEMS"]))?>);">
                        <?=Loc::getMessage('SOTBIT_COMPLAINTS_BUTTON_ADD')?></a></th>
                <?if ($arParams["SEARCH_PRODUCTS_FIELDS"]):
                    foreach ($arParams["SEARCH_PRODUCTS_FIELDS"] as $field):
                        if ($field == "ID" || $field == "NAME" || $field == "") {
                            continue;
                        }
                        ?>
                        <th class="text-nowrap text-end"><?=$arResult["DISPLAY_FIELDS"][$field]?></th>
                    <?endforeach;
                endif;?>
                <?if ($arParams["SEARCH_PRODUCTS_PROPERTIES"]):
                    foreach ($arParams["SEARCH_PRODUCTS_PROPERTIES"] as $propCode):
                        if (!$propCode) { continue;} ?>
                        <th class="text-nowrap text-end"><?=$arResult["DISPLAY_PROPS"][$propCode]["NAME"]?></th>
                    <?endforeach;
                endif;?>
            </tr>
        </thead>
        <tbody>
        <?foreach($arResult["ITEMS"] as $item):?>
            <tr data-product-id="<?=$item["ID"]?>">
                <td class="border-0">
                    <div class="d-flex align-items-center col-name">
                        <div class="me-2 mb-2 mb-md-0 pe-1">
                            <img src="<?=$item["IMG"]["src"] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'?>" 
                                    class="rounded" 
                                    width="<?=$item["IMG"]["width"]?: "48"?>" 
                                    height="<?=$item["IMG"]["height"]?: "48"?>"
                                    alt="<?=$item["NAME"]?>">
                        </div>
                        <div>
                            <span class="fw-semibold"><?=$item["NAME"]?></span>
                        </div>
                    </div>
                </td>
                <td class="border-0 order-1 order-md-0"><a type="button" class="list-icons-item" onclick="changeProd(<?=$item["ID"]?>);">
                        <?=Loc::getMessage('SOTBIT_COMPLAINTS_BUTTON_ADD')?></a></td>
                <?if ($arParams["SEARCH_PRODUCTS_FIELDS"]):?>
                    <?foreach ($arParams["SEARCH_PRODUCTS_FIELDS"] as $field):
                        if ($field == "ID" || $field == "NAME") {
                            continue;
                        }?>
                        <td class="border-0 text-md-end" data-title="<?=$arResult["DISPLAY_FIELDS"][$field]?>"><?=$item[$field]?></td>
                    <?endforeach;?>
                <?endif;?>
                <?if ($arParams["SEARCH_PRODUCTS_PROPERTIES"]):?>
                    <?foreach ($arParams["SEARCH_PRODUCTS_PROPERTIES"] as $propCode):?>
                        <td class="border-0 text-md-end" data-title="<?=$arResult["DISPLAY_PROPS"][$propCode]["NAME"]?>"><?=$item["PROPERTY_" . $propCode . "_VALUE"]?></td>
                    <?endforeach;?>
                <?endif;?>
            </tr>
        <?endforeach;?>
        </tbody>
        <tfoot class="border-top">
            <tr>
                <td colspan="2">
                    <span>
                        <?=Loc::getMessage('SOTBIT_COMPLAINTS_TOTAL_COUNT_PRODUCTS', ['#COUNT#' => count($arResult["ITEMS"])])?>
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    BX.message(<?=\Bitrix\Main\Web\Json::encode(\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__))?>);
    var productsObject = <?=CUtil::phpToJSObject($arResult["ITEMS"] )?>;
</script>