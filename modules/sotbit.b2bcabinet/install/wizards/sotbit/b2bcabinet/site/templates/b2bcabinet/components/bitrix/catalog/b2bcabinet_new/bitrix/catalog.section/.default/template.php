<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option;

$this->setFrameMode(true);

function getSortStyle($sort) {
    if ($sort === 'ASC') return 'ASC';
    if ($sort === 'DESC') return 'DESC';
    if ($sort === 'asc,nulls') return 'ASC';
    if ($sort === 'desc,nulls') return 'DESC';
    return '';
}

if (!empty($arResult['NAV_RESULT'])) {
    $navParams = array(
        'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
        'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
        'NavNum' => $arResult['NAV_RESULT']->NavNum
    );
} else {
    $navParams = array(
        'NavPageCount' => 1,
        'NavPageNomer' => 1,
        'NavNum' => $this->randString()
    );
}

$obName = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
$showTopPager = false;
$showBottomPager = false;

if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
    $showTopPager = $arParams['DISPLAY_TOP_PAGER'];
    $showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'];
}

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE');
$arParams['~MESS_SHOW_MAX_QUANTITY'] = $arParams['~MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCS_CATALOG_SHOW_MAX_QUANTITY');
$arParams['~MESS_RELATIVE_QUANTITY_MANY'] = $arParams['~MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['~MESS_RELATIVE_QUANTITY_FEW'] = $arParams['~MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCS_CATALOG_RELATIVE_QUANTITY_FEW');

//TODO: delet all unneccesary params
$generalParams = array(
    "AJAX_MODE" => $arParams['AJAX_MODE'],
    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
    'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
    'MESS_SHOW_MAX_QUANTITY' => $arParams['~MESS_SHOW_MAX_QUANTITY'],
    'MESS_RELATIVE_QUANTITY_MANY' => $arParams['~MESS_RELATIVE_QUANTITY_MANY'],
    'MESS_RELATIVE_QUANTITY_FEW' => $arParams['~MESS_RELATIVE_QUANTITY_FEW'],
    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
    'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
    'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
    'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
    'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
    'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'],
    'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
    'COMPARE_PATH' => $arParams['COMPARE_PATH'],
    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
    'CATALOG_NOT_AVAILABLE' => $arParams['CATALOG_NOT_AVAILABLE'],
    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
    'PRODUCT_BLOCKS_ORDER' => $arParams['PRODUCT_BLOCKS_ORDER'],
    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
    'LABEL_PROP' => $arParams['LABEL_PROP'],

    'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
    'SLIDER_PROGRESS' => $arParams['SLIDER_PROGRESS'],
    '~BASKET_URL' => $arParams['~BASKET_URL'],
    '~ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
    '~BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
    '~COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
    '~COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
    'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
    'BRAND_PROPERTY' => $arParams['BRAND_PROPERTY'],
    'MESS_BTN_BUY' => $arParams['~MESS_BTN_BUY'],
    'MESS_BTN_DETAIL' => $arParams['~MESS_BTN_DETAIL'],
    'MESS_BTN_COMPARE' => $arParams['~MESS_BTN_COMPARE'],
    'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
    'MESS_BTN_ADD_TO_BASKET' => $arParams['~MESS_BTN_ADD_TO_BASKET'],
    'MESS_NOT_AVAILABLE' => $arParams['~MESS_NOT_AVAILABLE'],
    'LIST_SHOW_MEASURE_RATIO' => $arParams['~LIST_SHOW_MEASURE_RATIO'],
    "ARTICLE_PROPERTY" => $arParams["ARTICLE_PROPERTY"],
    "ARTICLE_PROPERTY_OFFERS" => $arParams["ARTICLE_PROPERTY_OFFERS"],
    "OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
    "ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
    "CURRENCY_ID" => $arParams["CURRENCY_ID"],

    'STORE_PATH'                     => $arParams['STORE_PATH'],
    'MAIN_TITLE'                     => $arParams['MAIN_TITLE'],
    'USE_MIN_AMOUNT'                 => $arParams['USE_MIN_AMOUNT'],
    'MIN_AMOUNT'                     => $arParams['MIN_AMOUNT'],
    'STORES'                         => $arParams['STORES'],
    'SHOW_EMPTY_STORE'               => $arParams['SHOW_EMPTY_STORE'],
    'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
    'USER_FIELDS'                    => $arParams['USER_FIELDS'],
    'FIELDS'                         => $arParams['FIELDS'],
    'USE_STORE'                      => $arParams['USE_STORE'],
);
?>

<? if ($showTopPager): ?>
    <div class="blank-zakaza__pagination blank-zakaza__pagination--top"
         data-pagination-num="<?= $navParams['NavNum'] ?>">
        <?= $arResult['NAV_STRING'] ?>
    </div>
<? endif; ?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="blank-zakaza__scroll-wrapper ">
        <div class="blank-zakaza__wrapper" id=<?=$obName . '_wrapper'?>>
            <table class="blank-zakaza" id=<?=$obName?>>
                <thead class="blank-zakaza__header">
                <tr class="blank-zakaza__header-row" role="row">
                    <th class="blank-zakaza__header-fullscreen">
                        <a class="list-icons-item" data-action="fullscreen" title="<?=Loc::getMessage('CATALOG_HEADER_FULLSCREEN_TITLE')?>"></a>
                    </th>
                    <? foreach ($arParams['TABLE_HEADER'] as $propertyCode => $property) {
                        if (is_array($property) && $propertyCode == 'PRICES') {
                            foreach ($property as $singlePropertyCode => $singleProperty) {
                                ?>
                                <th class=" blank-zakaza__header-property
                                            <?=$arParams["ELEMENT_SORT_FIELD"] === ("PRICE_" . $singleProperty["ID"]) ? 'active sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="PRICE_<?=$singleProperty["ID"]?>">
                                    <?=$singleProperty["NAME"]?>
                                </th>
                                <?
                            }
                        }
                        elseif (is_array($property)) {
                            foreach ($property as $singlePropertyCode => $singleProperty) {
                                ?>
                                <th class=" blank-zakaza__header-property
                                            <?=$arParams["ELEMENT_SORT_FIELD"] === $singlePropertyCode ? 'active sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="<?=$singlePropertyCode?>">
                                    <?=$singleProperty["NAME"]?>
                                </th>
                                <?
                            }
                        }
                        else
                            if ($propertyCode == "NAME") {
                                ?>
                                <th width="200" class=" blank-zakaza__header-property
                                            blank-zakaza__header-property--name
                                            <?=$arParams["ELEMENT_SORT_FIELD"] === $propertyCode ? 'active sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="NAME">
                                    <?=$property?>
                                </th>
                                <?
                            } else if ($propertyCode == "QUANTITY") {
                                ?>
                                <th class=" blank-zakaza__header-property
                                            blank-zakaza__header-property--quantity"
                                    data-property-code="QUANTITY">
                                    <?=$property?>
                                </th>
                                <?
                            } else {
                                ?>
                                <th class=" blank-zakaza__header-property
                                            <?=$arParams["ELEMENT_SORT_FIELD"] === "PROPERTY_" . $propertyCode ? 'active  sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="PROPERTY_<?=$propertyCode?>">
                                    <?=$property?>
                                </th>
                                <?
                            }

                    }?>
                </tr>
                </thead>
                <?
                array_shift($arParams['TABLE_HEADER']);
                if (!empty($arResult['ITEMS'])) {
                    foreach ($arResult['ITEMS'] as $item) {
                        // TODO: make it works correctly
                        $uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
                        $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
                        $this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
                        $this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);
                        ?>
                    <tbody class="blank-zakaza__item" id="<?=$areaIds[$item['ID']]?>">

                        <?
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.item',
                            'b2b_new',
                            array(
                                'RESULT' => array(
                                    'ITEM' => $item,
                                    'AREA_ID' => $areaIds[$item['ID']],
                                    'TABLE_HEADER' => $arParams['TABLE_HEADER']
                                ),
                                'ACTIONS' => [
                                    "EDIT" => $elementEdit,
                                    "DELETE" => $elementDelete,
                                    "DELETE_PARAMS" => $elementDeleteParams,
                                ],
                                'PARAMS' => $generalParams
                                    + array(
                                            'SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']],
                                            'SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY' => $arResult['SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY'],
                                            'ITEMS_PRIVAT_PRICES' => $arResult['ITEMS_PRIVAT_PRICES'],
                                            'PRIVAT_PRICES_PARAMS' => $arResult['PRIVAT_PRICES_PARAMS']
                                    )
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );

                        ?>

                    </tbody>
                        <?
                    }
                    unset($generalParams);
                } else {
                    $APPLICATION->IncludeComponent(
                        'bitrix:catalog.item',
                        'b2b_new',
                        array(),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );
                }
                ?>
            </table>
        </div>
    </div>
<? else: ?>
<div class="blank-zakaza__scroll-wrapper ">
    <div class="blank-zakaza__wrapper" id=<?=$obName . '_wrapper'?>>
        <table class="blank-zakaza" id=<?=$obName?>>
    <div class="nothing_to_show text-muted"><?= Loc::getMessage('PRODUCTS_NOTHING_TO_SHOW') ?></div>
        </table>
    </div>
</div>
<? endif; ?>

<? if ($showBottomPager): ?>
    <div class="blank-zakaza__pagination blank-zakaza__pagination--bottom"
         data-pagination-num="<?= $navParams['NavNum'] ?>">
        <?= $arResult['NAV_STRING'] ?>
    </div>
<? endif; ?>

<script>
    if (typeof <?=$obName?> === 'undefined') {
        var <?=$obName?> = new JCBlankZakaza(
            <?=CUtil::PhpToJSObject($obName)?>,
            <?=CUtil::PhpToJSObject($arParams)?>
        );
    }
</script>
