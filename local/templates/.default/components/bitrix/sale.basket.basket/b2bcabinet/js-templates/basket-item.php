<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */
$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);
$usePicture = in_array("PREVIEW_PICTURE", $arParams['COLUMNS_LIST']);
$useProps = in_array("PROPS", $arParams['COLUMNS_LIST']);

$columsProp = count(array_merge($arResult['headers']['sku_data'] ?: [], $arResult['headers']['column_list']));
if($useProps) {
    $columsProp += count($arResult['headers']['props']);
}
$restoreColSpan = 2 + $columsProp + $usePicture + $usePriceInAdditionalColumn + $useSumColumn +
$useActionColumn;

?>
<script id="basket-item-template" type="text/html">
    <div class="{{#SHOW_RESTORE}} basket-items-list-item-container-expend{{/SHOW_RESTORE}}  basket__item "
		id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">

        {{#SHOW_RESTORE}}
        <div class="basket__column busket__column__size-4 basket-item-restore-block">
            <a href="javascript:void(0)"  class="basket-item-restore-button" data-entity="basket-item-restore-button" title="<?=Loc::getMessage('SBB_BASKET_ITEM_RESTORE')?>"><i  class="icon-rotate-ccw3"></i></a>
        </div>
        <div class="basket__column busket__column__size-5 basket__column__img">
            <img
                    alt="{{NAME}}"
                    src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?='/local/templates/b2bcabinet/assets/images/no_photo.svg'?>{{/IMAGE_URL}}"
                    width="45"
                    height="auto"
            >
        </div>
        <div class="basket__column busket__column__size-all">
            <div class="basket__product-discrioption">
                <span class="basket__product-name {{#NOT_AVAILABLE}} text-muted{{/NOT_AVAILABLE}}" title="{{{TITLE}}}">{{{NAME}}}</span>
                {{#NOT_AVAILABLE}}
                <div class="alert-warning">
                    <?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>.
                </div>
                {{/NOT_AVAILABLE}}
                {{#DELAYED}}
                <div class="alert-warning">
                    <?=Loc::getMessage('SBB_BASKET_ITEM_DELAYED')?>.
                    <a href="javascript:void(0)" data-entity="basket-item-remove-delayed">
                        <?=Loc::getMessage('SBB_BASKET_ITEM_REMOVE_DELAYED')?>
                    </a>
                </div>
                {{/DELAYED}}
                {{#WARNINGS.length}}
                <div class="alert-warning" data-entity="basket-item-warning-node">
                    <span class="close" data-entity="basket-item-warning-close">&times;</span>
                    {{#WARNINGS}}
                    <div data-entity="basket-item-warning-text">{{{.}}}</div>
                    {{/WARNINGS}}
                </div>
                {{/WARNINGS.length}}
                {{#PROPS}}{{#VALUE}}
                <span>{{{NAME}}} : {{{VALUE}}}</span>
                {{/VALUE}}{{/PROPS}}
                {{#SHOW_LOADING}}
                <div class="basket-items-list-item-overlay1"></div>
                {{/SHOW_LOADING}}
            </div>
        </div>
        <div class="basket__column busket__column__size-12 busket__column__font-white-space-nowrap">
            <div class="basket__column-price-wrap">
                <span>{{{PRICE_FORMATED}}}</span>
                {{#SHOW_DISCOUNT_PRICE}}
                <span class="basket__full-price-formated">{{{FULL_PRICE_FORMATED}}}</span>
                {{/SHOW_DISCOUNT_PRICE}}
                {{#SHOW_MEASURE_RATIO}}
                <span class="basket__measure-ratio"><?=Loc::getMessage('SBB_BASKET_MESURE_RATIO')?> {{{MEASURE_RATIO}}} {{{MEASURE_TEXT}}}</span>
                {{/SHOW_MEASURE_RATIO}}
            </div>
            {{#SHOW_LOADING}}
            <div class="basket-items-list-item-overlay"></div>
            {{/SHOW_LOADING}}
        </div>
        <div class="basket__column busket__column__size-12 busket__column__font-white-space-nowrap">
            <span>{{{DISCOUNT_PRICE_FORMATED}}}</span>
        </div>

        <div class="basket__column busket__column__size-16" data-entity="basket-item-quantity-block">
            <div class="input-group-basket">
                    <span class="input-group-prepend" data-entity="basket-item-quantity-minus">
                        -
                    </span>
                <input type="text" class="form-control touchspin-empty" value="{{QUANTITY}}"
                       {{#NOT_AVAILABLE}} disabled="disabled"{{/NOT_AVAILABLE}}
                data-value="{{QUANTITY}}" data-entity="basket-item-quantity-field"
                id="basket-item-quantity-{{ID}}">
                <span class="input-group-append" data-entity="basket-item-quantity-plus">
                        +
                    </span>
            </div>
        </div>

        <div class="basket__column busket__column__size-12 busket__column__font-bold busket__column__font-white-space-nowrap {{#NOT_AVAILABLE}} text-muted{{/NOT_AVAILABLE}}">
            <div class="basket__column-price-wrap">
                    <span><span class="fin-name-lable"><?=Loc::getMessage('SBB_FIN_NAME')?>
                        {{{SUM_PRICE_FORMATED}}}
                    </span>
                {{#SHOW_DISCOUNT_PRICE}}
                <span class="basket__full-price-formated"><span class="fin-name-lable"><?=Loc::getMessage('SBB_FIN_NAME')?>{{{SUM_FULL_PRICE_FORMATED}}}</span>
                {{/SHOW_DISCOUNT_PRICE}}

                {{#SHOW_LOADING}}
                <div class="basket-items-list-item-overlay"></div>
                {{/SHOW_LOADING}}
            </div>
        </div>

		{{/SHOW_RESTORE}}
        {{^SHOW_RESTORE}}

            <div class="basket__column busket__column__size-4">
                <label class="basket__checkbox" data-entity="basket-item-checkbox" id="basket__checkbox-{{ID}}">
                    <span class="basket__checkbox_content"></span>
                </label>
            </div>
            <div class="basket__column busket__column__size-5 basket__column__img">
                <img
                    alt="{{TITLE}}"
                    src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?='/local/templates/b2bcabinet/assets/images/no_photo.svg'?>{{/IMAGE_URL}}"
                    width="45"
                    height="auto"
                >
            </div>
            <div class="basket__column busket__column__size-all">
                <div class="basket__product-discrioption">
                    <span class="basket__product-name {{#NOT_AVAILABLE}} text-muted{{/NOT_AVAILABLE}}" title="{{{TITLE}}}">{{{NAME}}}</span>
                    {{#NOT_AVAILABLE}}
                            <div class="alert-warning">
                                <?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>.
                            </div>
                        {{/NOT_AVAILABLE}}
                        {{#DELAYED}}
                            <div class="alert-warning">
                                <?=Loc::getMessage('SBB_BASKET_ITEM_DELAYED')?>.
                                <a href="javascript:void(0)" data-entity="basket-item-remove-delayed">
                                    <?=Loc::getMessage('SBB_BASKET_ITEM_REMOVE_DELAYED')?>
                                </a>
                            </div>
                        {{/DELAYED}}
                        {{#WARNINGS.length}}
                            <div class="alert-warning" data-entity="basket-item-warning-node">
                                <span class="close" data-entity="basket-item-warning-close">&times;</span>
                                {{#WARNINGS}}
                                <div data-entity="basket-item-warning-text">{{{.}}}</div>
                                {{/WARNINGS}}
                            </div>
                        {{/WARNINGS.length}}
                    {{#PROPS}}{{#VALUE}}
                    <span>{{{NAME}}} : {{{VALUE}}}</span>
                    {{/VALUE}}{{/PROPS}}
                    {{#SHOW_LOADING}}
                            <div class="basket-items-list-item-overlay"></div>
                    {{/SHOW_LOADING}}
                </div>
            </div>
            <div class="basket__column busket__column__size-12 busket__column__font-white-space-nowrap">
                <div class="basket__column-price-wrap">
                    <span>{{{PRICE_FORMATED}}}</span>
                    {{#SHOW_DISCOUNT_PRICE}}
                    <span class="basket__full-price-formated">{{{FULL_PRICE_FORMATED}}}</span>
                    {{/SHOW_DISCOUNT_PRICE}}
                    {{#SHOW_MEASURE_RATIO}}
                    <span class="basket__measure-ratio"><?=Loc::getMessage('SBB_BASKET_MESURE_RATIO')?> {{{MEASURE_RATIO}}} {{{MEASURE_TEXT}}}</span>
                    {{/SHOW_MEASURE_RATIO}}
                </div>
                {{#SHOW_LOADING}}
                    <div class="basket-items-list-item-overlay2"></div>
                {{/SHOW_LOADING}}
            </div>
            <div class="basket__column busket__column__size-12 busket__column__font-white-space-nowrap">
                <span>{{{DISCOUNT_PRICE_FORMATED}}}</span>
            </div>

            <div class="basket__column busket__column__size-16" data-entity="basket-item-quantity-block">
                <div class="input-group-basket">
                    <span class="input-group-prepend" data-entity="basket-item-quantity-minus">
                        -
                    </span>
                    <input type="text" class="form-control touchspin-empty" value="{{QUANTITY}}"
                        {{#NOT_AVAILABLE}} disabled="disabled"{{/NOT_AVAILABLE}}
                        data-value="{{QUANTITY}}" data-entity="basket-item-quantity-field"
                        id="basket-item-quantity-{{ID}}">
                    <span class="input-group-append" data-entity="basket-item-quantity-plus">
                        +
                    </span>
                </div>
            </div>

            <div class="basket__column busket__column__size-12 busket__column__font-bold busket__column__font-white-space-nowrap {{#NOT_AVAILABLE}} text-muted{{/NOT_AVAILABLE}}">
                <div class="basket__column-price-wrap">
                    <span><span class="fin-name-lable"><?=Loc::getMessage('SBB_FIN_NAME')?></span>
                        {{{SUM_PRICE_FORMATED}}}
                    </span>
                    {{#SHOW_DISCOUNT_PRICE}}
                        <span class="basket__full-price-formated"><span class="fin-name-lable"><?=Loc::getMessage('SBB_FIN_NAME')?></span>{{{SUM_FULL_PRICE_FORMATED}}}</span>
                    {{/SHOW_DISCOUNT_PRICE}}

                    {{#SHOW_LOADING}}
                        <div class="basket-items-list-item-overlay"></div>
                    {{/SHOW_LOADING}}
                </div>
            </div>

        {{/SHOW_RESTORE}}

    </div>
</script>