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
            <div id="basket-item-list" class="basket__basket-item-list">
                <div class="basket__tool-bar">
                    <div class="basket__search">
                        <div class="form-control-feedback form-control-feedback-start">
                            <input type="text" class="form-control form-control-sm border-primary bg-white" data-entity="search-input"
                                   placeholder="<?= loc::getMessage('SEARCH_FROM_BAKSET') ?>">
                            <div class="dropdown-icon form-control-feedback-icon search-group__btn" data-entity="search-btn">
                                <i class="ph-magnifying-glass"></i>
                            </div>
                        </div>
                    </div>
                    <? if ($arResult['module_multibasket_is_includet']): ?>
                        <div class="basket__toolbar-btn multibasket__wrapper me-sm-3" data-entity="multibasket__wrapper">
                            <div class="multibasket__title" data-entity="multibasket__title">
                                <i class="ph ph-shopping-cart-simple me-1"></i>
                                <span class="multibasket__list__text"><?= Loc::getMessage('MOVE_TO_MULTIBASKET') ?></span>
                                <span class="multibasket__list__arrow">
                                    <i class="ph ph-caret-down align-middle fs-base p-1"></i>
                                </span>
                            </div>
                            <div class="multibasket__otherbasket_wraper" data-entity="multibasket__otherbasket_wraper"
                                 style="display: none;">
                            </div>
                        </div>
                    <?endif; ?>
                    <a class="basket__toolbar-btn" data-entity="basket-groupe-item-delete">
                        <i class="ph-trash"></i>
                        <span><?= loc::getMessage('REMOVE_FROM_BASKET') ?></span>
                    </a>
                </div>
                <div class="basket_table <?=$arResult['module_multibasket_is_includet'] ? 'bakset__multibakset-color' : ''?>">
                    <div class="basket__header">
                        <div class="basket__header-row">
                            <div class="basket__column busket__column__size-4 busket__column__cursor-pointer">
                                <label class="basket__checkbox basket__checkbox__disabled"
                                    data-entity="basket-gruope-item-checkbox">
                                    <span class="basket__checkbox_content"></span>
                                </label>
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
                            <div class="basket__column busket__column__size-16">
                                <span><?= loc::getMessage('TOTOAL_FROM_BASKET') ?></span>
                            </div>
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
$templateSigns = $signer->sign(SITE_TEMPLATE_ID, "template_preview".bitrix_sessid());
$messages = Loc::loadLanguageFile(__FILE__);
?>

    <script>
        BX.ready(() => {
            BX.message(<?=CUtil::PhpToJSObject($messages)?>);

            BX.Sale.BasketComponent.init({
                result: <?=CUtil::PhpToJSObject($arResult, false, false, true)?>,
                params: <?=CUtil::PhpToJSObject($arParams)?>,
                template: '<?=CUtil::JSEscape($signedTemplate)?>',
                signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
                siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
                ajaxUrl: '<?=CUtil::JSEscape($component->getPath() . '/ajax.php')?>',
                templateFolder: '<?=CUtil::JSEscape($templateFolder)?>',
                templateSigns: '<?=CUtil::JSEscape($templateSigns)?>',
            });
        });
        window.addEventListener('load', ()=> BX.Sale.BasketComponent.showEmptyBasket(false));
    </script>
<?
