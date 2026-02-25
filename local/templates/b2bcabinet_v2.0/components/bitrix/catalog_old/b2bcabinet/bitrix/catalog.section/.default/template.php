<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);
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
$showLazyLoad = false;

if ($arParams['PAGE_ELEMENT_COUNT'] > 0 && $navParams['NavPageCount'] > 1) {
    $showTopPager = $arParams['DISPLAY_TOP_PAGER'];
    $showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'];
    $showLazyLoad = $arParams['LAZY_LOAD'] === 'Y' && $navParams['NavPageNomer'] != $navParams['NavPageCount'];
}

$containerName = 'container-'.$navParams['NavNum'];

$templateLibrary = array('popup', 'ajax', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'USE_PAGINATION_CONTAINER' => $showTopPager || $showBottomPager,
);
unset($currencyList, $templateLibrary);

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
    "AJAX_ID" => $arParams['AJAX_ID'],
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
    "OFFERS_VIEW" => $arParams["OFFERS_VIEW"],

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
    <div class="blank-zakaza__pagination blank-zakaza__pagination--top" data-pagination-num="<?= $navParams['NavNum'] ?>">
        <!-- pagination-container -->
        <?= $arResult['NAV_STRING'] ?>
        <!-- pagination-container -->
    </div>
<? endif; ?>

<?

$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$parts = parse_url($url);
parse_str($parts['query'], $query);

if(str_contains($url, '/filter/') || $query['set_filter'] || ($query['q'] && $query['set_filter'])) {
    if (is_object($arResult['NAV_RESULT'])) {
        $prods = $arResult["NAV_RESULT"]->NavRecordCount;
    }
}
elseif($query['q']) {
    $prods = isset($arResult["ORIGINAL_PARAMETERS"]["GLOBAL_FILTER"]["ID"]) && is_array($arResult["ORIGINAL_PARAMETERS"]["GLOBAL_FILTER"]["ID"])
        ? count($arResult["ORIGINAL_PARAMETERS"]["GLOBAL_FILTER"]["ID"])
        : 0;

}
else {
    $prods = CIBlockSection::GetSectionElementsCount($arResult["ORIGINAL_PARAMETERS"]["SECTION_ID"], Array("CNT_ACTIVE"=>"Y"));
}

$this->SetViewTarget('els_count');
echo "Всего: " . $prods;
$this->EndViewTarget();
?>

<?
global $USER;
$userID = $USER->GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();

?>

<?/*if($USER->IsAdmin()):?>
    <pre>
        <?print_r($arUser["UF_APPLY_PRICE_FOR"]);?>
    </pre>
<?endif;*/?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="blank-zakaza__scroll-wrapper bx-<?=$arParams['TEMPLATE_THEME']?>">
        <div class="blank-zakaza__wrapper" id=<?=$obName . '_wrapper'?>>
            <table class="blank-zakaza" id=<?=$obName?> data-entity="<?=$containerName?>">
                <thead class="blank-zakaza__header">
                <tr class="blank-zakaza__header-row" role="row">
                    <? foreach ($arParams['TABLE_HEADER'] as $propertyCode => $property) {
                        if (is_array($property) && $propertyCode == 'PRICES') {
                            foreach ($property as $singlePropertyCode => $singleProperty) {
                                ?>
                                <?if($singleProperty["ID"] != 12):?>
                                    <th class=" blank-zakaza__header-property
                                                <?=$arParams["ELEMENT_SORT_FIELD"] === ("PRICE_" . $singleProperty["ID"]) ? 'active sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                        data-property-code="PRICE_<?=$singleProperty["ID"]?>">
                                        <?if($singleProperty["ID"] == 7):?>
                                            Цена
                                        <?elseif($singleProperty["ID"] == 8):?>
                                            РРЦ
                                        <?else:?>
                                            <?=$singleProperty["NAME"]?>
                                        <?endif;?>
                                    </th>
                                <?endif;?>
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
                            if ($property == $arParams['TABLE_HEADER']['NAME']) {
                                ?>
                                <th class=" blank-zakaza__header-property" data-property-code="PROPERTY_BREND_ATTR_S">
                                    <a class="list-icons-item" data-action="fullscreen" title="<?=Loc::getMessage('CATALOG_HEADER_FULLSCREEN_TITLE')?>">
                                        <i class="ph-frame-corners"></i>
                                    </a>
                                </th>
                                <th class=" blank-zakaza__header-property" data-property-code="PROPERTY_BREND_ATTR_S">
                                    Бренд
                                </th>
                                <th class="blank-zakaza__header-property blank-zakaza__header-property--name
                                            <?=$arParams["ELEMENT_SORT_FIELD"] === $propertyCode ? 'active sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="NAME">
                                    <?=$property?>
                                </th>
                                <?
                            } else if ($property == $arParams['TABLE_HEADER']['QUANTITY']) {
                                ?>
                                <th class="blank-zakaza__header-property" data-property-code="PROPERTY_TRANSIT">
                                    В пути
                                </th>
                                <th class=" blank-zakaza__header-property blank-zakaza__header-property--quantity" data-property-code="QUANTITY">
                                    <?=$property?>
                                </th>
                                <?
                            } else if (isset($arParams['TABLE_HEADER']['AVALIABLE']) && stristr($property, $arParams['TABLE_HEADER']['AVALIABLE'])) {
                                ?>
                                <th class=" blank-zakaza__header-property
                                        <?=$arParams["ELEMENT_SORT_FIELD"] === "QUANTITY" ? 'active  sort-' . getSortStyle($arParams["ELEMENT_SORT_ORDER"]) : ''?>"
                                    data-property-code="<?=$propertyCode?>">
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
                    ?>
                    <!-- items-container -->
                    <?
                    foreach ($arResult['ITEMS'] as $item) {
                        $uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
                        $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
                        $this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
                        $this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);

                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.item',
                            '',
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
                                    ),
                                'USER' => $arUser
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }
                    unset($generalParams);
                    ?>
                    <!-- items-container -->
                    <?
                } else {
                    $APPLICATION->IncludeComponent(
                        'bitrix:catalog.item',
                        '',
                        array(),
                        $component,
                        false
                    );
                }
                ?>
            </table>
            <? if ($showLazyLoad): ?>
                <div class="btn btn-show-more" data-use="show-more-<?=$navParams['NavNum']?>">
                    <?=$arParams['MESS_BTN_LAZY_LOAD']?>
                </div>
            <? endif; ?>
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
    <div class="blank-zakaza__pagination blank-zakaza__pagination--bottom" data-pagination-num="<?= $navParams['NavNum'] ?>">
        <!-- pagination-container -->
        <?= $arResult['NAV_STRING'] ?>
        <!-- pagination-container -->
    </div>
<? endif; ?>

<?
$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'catalog.section');
$signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'catalog.section');
?>

<script>
    if (typeof <?=$obName?> === 'undefined') {
        var <?=$obName?> = new JCBlankZakaza(
            <?=CUtil::PhpToJSObject($obName)?>,
            <?=CUtil::PhpToJSObject($arParams)?>
        );
    }
</script>

<script>
    BX.message({
        BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
        BASKET_URL: '<?=$arParams['BASKET_URL']?>',
        ADD_TO_BASKET_OK: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
        TITLE_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR')?>',
        TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS')?>',
        TITLE_SUCCESSFUL: '<?=GetMessageJS('ADD_TO_BASKET_OK')?>',
        BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR')?>',
        BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS')?>',
        BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE')?>',
        BTN_MESSAGE_CLOSE_POPUP: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
        COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK')?>',
        COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
        COMPARE_TITLE: '<?=GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE')?>',
        PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCS_CATALOG_PRICE_TOTAL_PREFIX')?>',
        RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
        RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
        BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
        BTN_MESSAGE_LAZY_LOAD: '<?=CUtil::JSEscape($arParams['MESS_BTN_LAZY_LOAD'])?>',
        BTN_MESSAGE_LAZY_LOAD_WAITER: '<?=GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_LAZY_LOAD_WAITER')?>',
        SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
    });
    var <?=$obName?> = new JCCatalogSectionComponent({
        siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
        componentPath: '<?=CUtil::JSEscape($componentPath)?>',
        navParams: <?=CUtil::PhpToJSObject($navParams)?>,
        deferredLoad: false,
        initiallyShowHeader: '<?=!empty($arResult['ITEM_ROWS'])?>',
        bigData: <?=CUtil::PhpToJSObject($arResult['BIG_DATA'])?>,
        lazyLoad: !!'<?=$showLazyLoad?>',
        loadOnScroll: !!'<?=($arParams['LOAD_ON_SCROLL'] === 'Y')?>',
        template: '<?=CUtil::JSEscape($signedTemplate)?>',
        ajaxId: '<?=CUtil::JSEscape($arParams['AJAX_ID'] ?? '')?>',
        parameters: '<?=CUtil::JSEscape($signedParams)?>',
        container: '<?=$containerName?>'
    });
</script>

<!-- component-end -->