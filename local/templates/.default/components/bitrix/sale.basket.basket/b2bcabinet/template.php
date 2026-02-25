<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\UI\Extension::load("ui.fonts.ruble");
/**
 * @var array $arParams
 * @var array $arResult
 * @var string $templateFolder
 * @var string $templateName
 * @var CMain $APPLICATION
 * @var CBitrixBasketComponent $component
 * @var CBitrixComponentTemplate $this
 * @var array $giftParameters
 */

$documentRoot = Main\Application::getDocumentRoot();

if (!isset($arParams['DISPLAY_MODE']) || !in_array($arParams['DISPLAY_MODE'], array('extended', 'compact'))) {
    $arParams['DISPLAY_MODE'] = 'extended';
}

$arParams['USE_DYNAMIC_SCROLL'] = isset($arParams['USE_DYNAMIC_SCROLL']) && $arParams['USE_DYNAMIC_SCROLL'] === 'N' ? 'N' : 'Y';
//$arParams['SHOW_FILTER'] = isset($arParams['SHOW_FILTER']) && $arParams['SHOW_FILTER'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_FILTER'] = 'N';

$arParams['PRICE_DISPLAY_MODE'] = isset($arParams['PRICE_DISPLAY_MODE']) && $arParams['PRICE_DISPLAY_MODE'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['TOTAL_BLOCK_DISPLAY']) || !is_array($arParams['TOTAL_BLOCK_DISPLAY'])) {
    $arParams['TOTAL_BLOCK_DISPLAY'] = array('bottom');
}

if (empty($arParams['PRODUCT_BLOCKS_ORDER'])) {
    $arParams['PRODUCT_BLOCKS_ORDER'] = 'props,sku,columns';
}

if (is_string($arParams['PRODUCT_BLOCKS_ORDER'])) {
    $arParams['PRODUCT_BLOCKS_ORDER'] = explode(',', $arParams['PRODUCT_BLOCKS_ORDER']);
}

$arParams['USE_PRICE_ANIMATION'] = isset($arParams['USE_PRICE_ANIMATION']) && $arParams['USE_PRICE_ANIMATION'] === 'N' ? 'N' : 'Y';
$arParams['EMPTY_BASKET_HINT_PATH'] = isset($arParams['EMPTY_BASKET_HINT_PATH']) ? (string)$arParams['EMPTY_BASKET_HINT_PATH'] : '/';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

\CJSCore::Init(array('fx', 'popup', 'ajax'));

$this->addExternalJs($templateFolder . '/js/mustache.js');
$this->addExternalJs($templateFolder . '/js/action-pool.js');
$this->addExternalJs($templateFolder . '/js/filter.js');
$this->addExternalJs($templateFolder . '/js/component.js');

$mobileColumns = isset($arParams['COLUMNS_LIST_MOBILE'])
    ? $arParams['COLUMNS_LIST_MOBILE']
    : $arParams['COLUMNS_LIST'];
$mobileColumns = array_fill_keys($mobileColumns, true);

$jsTemplates = new Main\IO\Directory($documentRoot . $templateFolder . '/js-templates');

/** @var Main\IO\File $jsTemplate */
foreach ($jsTemplates->getChildren() as $jsTemplate) {
    if (pathinfo($jsTemplate->getPath(), PATHINFO_EXTENSION) != "php") {
        continue;
    }
    include($jsTemplate->getPath());
}

$displayModeClass = $arParams['DISPLAY_MODE'] === 'compact' ? ' basket-items-list-wrapper-compact' : '';


if ($arResult['BASKET_ITEM_MAX_COUNT_EXCEEDED']) {
    ?>
    <div id="basket-item-message">
        <?= Loc::getMessage('SBB_BASKET_ITEM_MAX_COUNT_EXCEEDED', array('#PATH#' => $arParams['PATH_TO_BASKET'])) ?>
    </div>
    <?
}
?>
    <div id="basket-root" class="basket">
        <div id="basket-items-list-wrapper" class="basket__basket-items-list-wrapper">
            <? if ($arResult['module_multibasket_is_includet']){ ?>
                <div class="bakset__multibakset-color"></div>
                <div id="basket-item-list" class="basket__basket-item-list" style=" height: calc(100% - 8px);">
            <?} else { ?>
                <div id="basket-item-list" class="basket__basket-item-list">
            <?} ?>
                <div class="basket__tool-bar">
                    <div class="basket__search">
                        <div class="search-group">
                            <input type="text" class="search-group__input" data-entity="search-input"
                                   placeholder="<?= loc::getMessage('SEARCH_FROM_BAKSET') ?>">
                            <div class="search-group__btn" data-entity="search-btn">
                                <i>
                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.8265 15.4412L12.109 10.7207C13.0228 9.59124 13.573 8.15606 13.573 6.59294C13.573 2.97007 10.627 0.0222473 7.00645 0.0222473C3.38587 0.0222473 0.439941 2.97004 0.439941 6.5929C0.439941 10.2158 3.38591 13.1636 7.00648 13.1636C8.56862 13.1636 10.0029 12.6131 11.1316 11.6987L15.8492 16.4192C15.984 16.5541 16.1609 16.6219 16.3379 16.6219C16.5148 16.6219 16.6918 16.5541 16.8266 16.4192C17.0968 16.1488 17.0968 15.7116 16.8265 15.4412ZM7.00648 11.7803C4.14761 11.7803 1.82238 9.45358 1.82238 6.5929C1.82238 3.73223 4.14761 1.40553 7.00648 1.40553C9.86536 1.40553 12.1906 3.73223 12.1906 6.5929C12.1906 9.45358 9.86532 11.7803 7.00648 11.7803Z"
                                              fill="#999999"/>
                                    </svg>
                                </i>
                            </div>
                        </div>
                    </div>
                    <? if ($arResult['module_multibasket_is_includet']): ?>
                        <div class="multibasket__wrapper" data-entity="multibasket__wrapper">
                            <div class="multibasket__title" data-entity="multibasket__title">
                                <!-- <span class="multibasket__list__icon"><?/*include(__DIR__ . '/img/move.php')*/ ?></span> -->
                                <i class="icon-cart-add2 mr-1 icon-1x"></i>
                                <span class="multibasket__list__text"><?= Loc::getMessage('MOVE_TO_MULTIBASKET') ?></span>
                                <span class="multibasket__list__arrow"><? include(__DIR__ . '/img/arrow.php') ?></span>
                            </div>
                            <div class="multibasket__otherbasket_wraper" data-entity="multibasket__otherbasket_wraper"
                                 style="display: none;">
                            </div>
                        </div>
                    <?endif; ?>
                    <a class="basket__toolbar-btn" data-entity="basket-groupe-item-delete">
                        <!-- <i class="far fa-trash-alt mr-3 fa-2x"></i> -->
                        <i class="icon-trash mr-1 icon-1x"></i>
                        <span><?= loc::getMessage('REMOVE_FROM_BASKET') ?></span>
                    </a>
                </div>
                <div class="basket_table_wrap">
                    <div class="basket__header">
                        <div class="basket__column busket__column__size-4 busket__column__cursor-pointer">
                            <label class="basket__checkbox basket__checkbox__disabled"
                                   data-entity="basket-gruope-item-checkbox">
                                <span class="basket__checkbox_content"></span>
                            </label>
                        </div>
                        <div class="basket__column busket__column__size-5">
                        </div>
                        <div class="basket__column busket__column__size-all">
                            <span class="basket__header-name"><?= loc::getMessage('PRODUCT_NAME_FROM_BASKET') ?></span>
                        </div>
                        <div class="basket__column busket__column__size-12">
                            <span><?= loc::getMessage('PRICE_FROM_BASKET') ?></span>
                        </div>
                        <div class="basket__column busket__column__size-12">
                            <span><?= loc::getMessage('DISCONT_FROM_BASKET') ?></span>
                        </div>
                        <div class="basket__column busket__column__size-16">
                            <span><?= loc::getMessage('QUANTITY_FROM_BASKET') ?></span>
                        </div>
                        <div class="basket__column busket__column__size-12">
                            <span><?= loc::getMessage('TOTOAL_FROM_BASKET') ?></span>
                        </div>
                    </div>
                    <div id="basket-item-table" class="basket__body">
                        <div
                                class="text-muted nothing_to_show"
                                style="display: none;"
                                data-entity="use-filter-and-empty-basket"><?= Loc::getMessage('SBB_FILTER_EMPTY_RESULT') ?></div>
                        <?
                        include(Main\Application::getDocumentRoot() . $templateFolder . '/empty.php'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?
if (!empty($arResult['CURRENCIES']) && Main\Loader::includeModule('currency')) {
    CJSCore::Init('currency');

    ?>
    <script>
        BX.Currency.setCurrencies(<?=CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true)?>);
    </script>
    <?
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'sale.basket.basket');
$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.basket.basket');
$messages = Loc::loadLanguageFile(__FILE__);
?>

    <script>
        BX.message(<?=CUtil::PhpToJSObject($messages)?>);

        BX.Sale.BasketComponent.init({
            result: <?=CUtil::PhpToJSObject($arResult, false, false, true)?>,
            params: <?=CUtil::PhpToJSObject($arParams)?>,
            template: '<?=CUtil::JSEscape($signedTemplate)?>',
            signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
            siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
            ajaxUrl: '<?=CUtil::JSEscape($component->getPath() . '/ajax.php')?>',
            templateFolder: '<?=CUtil::JSEscape($templateFolder)?>'
        });

    </script>
<?
