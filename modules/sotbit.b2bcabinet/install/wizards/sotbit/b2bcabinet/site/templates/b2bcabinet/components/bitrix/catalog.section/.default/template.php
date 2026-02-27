<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
CJSCore::Init(array("fx"));
if (!function_exists('getSortStyle')) {
    function getSortStyle($sort)
    {
        if ($sort === 'ASC') return 'ASC';
        if ($sort === 'DESC') return 'DESC';
        if ($sort === 'asc,nulls') return 'ASC';
        if ($sort === 'desc,nulls') return 'DESC';
        return '';
    }
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

    'STORE_PATH' => $arParams['STORE_PATH'],
    'MAIN_TITLE' => $arParams['MAIN_TITLE'],
    'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
    'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
    'STORES' => $arParams['STORES'],
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
    'SHOW_GENERAL_STORE_INFORMATION' => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
    'USER_FIELDS' => $arParams['USER_FIELDS'],
    'FIELDS' => $arParams['FIELDS'],
    'USE_STORE' => $arParams['USE_STORE'],
);

$itemIds = [
    'OFFERS_SECTION' => $obName . '_offers-section'
];
?>

<? if (count($arResult['ITEMS'])): ?>
    <section class="blank-zakaza-detail__main-section card card-body crossell-section"
             id="<?= $itemIds['OFFERS_SECTION'] ?>">
        <h2 class="card-title"><?= $arParams['SECTION_NAME'] ?></h2>
        <div class="bzd-offers__wrapper">
            <table class="bzd-offers">
                <thead class="bzd-offers__header">
                <tr class="bzd-offers__header-row">
                    <th class="bzd-offers__header-cell"></th>
                    <th class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_NAME') ?></th>
                    <? if ($arParams['SHOW_MAX_QUANTITY'] !== "N"): ?>
                        <th class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_AVALIABLE') ?></th>
                    <? endif; ?>
                    <th class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_PROPERTIES') ?></th>
                    <th class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_PRICE') ?></th>
                    <th class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_QUANTITY') ?></th>
                </tr>
                </thead>
                <tbody class="bzd-offers__body">
                <? foreach ($arResult['ITEMS'] as &$item):
                    if (empty($item['ID'])) continue;
                    $mainId = $this->GetEditAreaId($item['ID']);

                    $itemIds['ITEMS'][$offer['ID']]['ID'] = $itemIds['OFFERS_SECTION'] . $mainId . '_offer_' . $offer['ID']; ?>
                    <tr class="bzd-offers__offer" id="<?= $itemIds['ITEMS'][$item['ID']]['ID'] ?>">
                        <td class="bzd-offers__offer-cell">
                            <img class="bzd-offers__offer-image" src="<?= $item['PICTURE'] ?>"
                                 height="45" width="45">
                        </td>
                        <td class="bzd-offers__offer-cell">
                            <a href="<?=$item['DETAIL_PAGE_URL']?>" target="_blank" class="bzd-offers__offer-name" title="<?= $item['NAME'] ?>"><?= $item['NAME'] ?></a>
                            <? if ($arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]): ?>
                                <div class="bzd-offers__offer-artnumber">
                                    <?= $item['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['NAME'] ?>
                                    <?= $item['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['VALUE'] ?>
                                </div>
                            <? endif; ?>

                        </td>
                        <? if ($arParams['SHOW_MAX_QUANTITY'] !== "N") { ?>
                            <td class="bzd-offers__offer-cell">
                                <?
                                $item['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                    "sotbit:catalog.store.quantity",
                                    ".default",
                                    array(
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_TYPE" => "A",
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "ELEMENT_ID" => $item["ID"],
                                        "MESS_RELATIVE_QUANTITY_NO" => $arParams['MESS_RELATIVE_QUANTITY_NO'],
                                        "MESS_RELATIVE_QUANTITY_FEW" => $arParams["MESS_RELATIVE_QUANTITY_FEW"],
                                        "MESS_RELATIVE_QUANTITY_MANY" => $arParams["MESS_RELATIVE_QUANTITY_MANY"],
                                        "MESS_SHOW_MAX_QUANTITY" => $arParams["MESS_SHOW_MAX_QUANTITY"],
                                        "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                                        "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                                        "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                                        "STORES" => $arParams["STORES"],
                                        "STORE_FIELDS" => $arParams["STORE_FIELDS"],
                                        "STORE_PROPERTIES" => $arParams["STORE_PROPERTIES"],
                                        "USE_STORE" => $arParams["USE_STORE"],
                                        "BASE_QUANTITY" => $item['CATALOG_QUANTITY'],
                                        "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"]
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                );

                                ?>
                            </td>
                        <? } ?>
                        <td class="bzd-offers__offer-cell">
                            <? foreach ($item['DISPLAY_PROPERTIES'] as $code => $property): ?>
                                <p class="bzd-offers__offer-porperty">
                                    <sapn class="bzd-offers__offer-porperty-name"><?= $property['NAME'] ?></sapn>
                                    <span class="bzd-offers__offer-porperty-value"><?= is_array($property['VALUE'])
                                            ? (is_array($property['DISPLAY_VALUE']) ? implode(', ', $property['DISPLAY_VALUE']) : $property['DISPLAY_VALUE'])
                                            : $property['DISPLAY_VALUE'] ?>
                                    </span>
                                </p>
                            <? endforeach; ?>
                        </td>
                        <td class="bzd-offers__offer-cell bzd-prices">
                            <ul class="bzd-prices__list">
                                <? foreach ($item['PRICES'] as $priceCode => $price): ?>
                                    <li class="bzd-prices__item">
                                        <span class="bzd-prices__item-name"><?= $arResult['PRICES'][$priceCode]['TITLE'] ? $arResult['PRICES'][$priceCode]['TITLE'] : $arResult['PRICES'][$priceCode]['CODE'] ?></span>
                                        <span class="bzd-prices__item-value">
                                            <?= $price['PRINT_VALUE'] ?>
                                        </span>
                                        <span class="product__property--discount-price">
                                            <? if (round((float)$price['VALUE_VAT'], 2) !== round((float)$price['DISCOUNT_VALUE_VAT'], 2)): ?>
                                                <?= $priceCode == "PRIVATE_PRICE" ?
                                                    "" :
                                                    CCurrencyLang::CurrencyFormat($price['DISCOUNT_VALUE_VAT'], $price['CURRENCY'], true); ?>
                                            <? endif; ?>
                                        </span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </td>
                        <td class="bzd-offers__offer-cell">
                            <?
                            $itemIds['ITEMS'][$item['ID']]['QUANTITY'] = $itemIds['OFFERS_SECTION'] . $mainId . '_quantity';
                            $itemIds['ITEMS'][$item['ID']]['QUANTITY_DECREMENT'] = $itemIds['OFFERS_SECTION'] . $mainId . '_quantity-decrement';
                            $itemIds['ITEMS'][$item['ID']]['QUANTITY_VALUE'] = $itemIds['OFFERS_SECTION'] . $mainId . '_quantity-value';
                            $itemIds['ITEMS'][$item['ID']]['QUANTITY_INCREMENT'] = $itemIds['OFFERS_SECTION'] . $mainId . '_quantity-increment';
                            ?>
                            <div class="quantity-selector"
                                 id="<?= $itemIds['ITEMS'][$item['ID']]['QUANTITY'] ?>">
                                <button class="quantity-selector__decrement"
                                        type="button"
                                        id="<?= $itemIds['ITEMS'][$item['ID']]['QUANTITY_DECREMENT'] ?>">
                                    -
                                </button>
                                <input class="quantity-selector__value"
                                       type="text"
                                       value="<?= $item['ACTUAL_QUANTITY'] ?>"
                                       id="<?= $itemIds['ITEMS'][$item['ID']]['QUANTITY_VALUE'] ?>"
                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                >
                                <button class="quantity-selector__increment"
                                        type="button"
                                        id="<?= $itemIds['ITEMS'][$item['ID']]['QUANTITY_INCREMENT'] ?>">
                                    +
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?
                    $allItemIds['ITEMS'][$item['ID']] = $itemIds['ITEMS'][$item['ID']];
                endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<? endif;
unset($arParams["OBJECT_PARENT_COMPONENT"]);
unset($arParams["~OBJECT_PARENT_COMPONENT"]);
?>
<script>
    if (typeof <?=$obName?> === 'undefined') {
        var <?=$obName?> = new JCBlankZakaza(
            <?=CUtil::PhpToJSObject($arResult["ITEMS"])?>,
            <?=CUtil::PhpToJSObject($arParams)?>,
            <?=CUtil::PhpToJSObject($allItemIds)?>
        );
    }
</script>
