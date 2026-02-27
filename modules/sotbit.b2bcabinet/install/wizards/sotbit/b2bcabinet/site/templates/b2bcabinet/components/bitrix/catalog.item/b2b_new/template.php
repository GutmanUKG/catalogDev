<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main,
    \Bitrix\Main\Grid\Declension,
    \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogProductsViewedComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$productTypes = [
    "SIMPLE" => 1,
    "SET" => 2,
    "PRODUCT_WITH_OFFERS" => 3,
    "OFFER" => 4,
    "OFFER_WITHOUT_PRODUCT" => 5
];
$canBuyZero = Bitrix\Main\Config\Option::get('catalog', 'default_can_buy_zero', 'N');

$this->setFrameMode(true);

if (!isset($arResult['ITEM'])) {
    return;
}

$item = &$arResult['ITEM'];
$areaId = $arResult['AREA_ID'];
$itemIds = array(
    'ID' => $areaId,
    'PICTURE' => $areaId . '_picture',
    'LINK' => $areaId . '_link',
    'OFFERS_TOGGLER' => $areaId . '_offers-toggler'
);
$obName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);

$isOffers = isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === $productTypes["PRODUCT_WITH_OFFERS"]
    && isset($item['OFFERS'])
    && count($item['OFFERS']) > 0;

?>
    <tr class="product">
        <td class="product__property product__property--image">
            <div class="product__image-wrapper">
                <img class="product__image"
                     id=<?= $itemIds['PICTURE'] ?>
                     src="<?= $item['PICTURE'] ?>"
                     width="100%"
                     height="100%">
            </div>
        </td>
        <td class="product__property product__property--name">
            <?
            if($item['PROPERTIES'] && $arParams['LABEL_PROP'])
            {
                foreach($arParams['LABEL_PROP'] as $label)
                {
                    if ($item['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
                        ?>
                        <span class="badge b2b_badge <?=$item['PROPERTIES'][$label]['HINT']?>">
                            <?=$item['PROPERTIES'][$label]['NAME']?>
                        </span>
                        <?
                    }
                }
            }
            ?>
            <a class="product__link"
               href="javascript:void(0)"
               data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
               id="<?= $itemIds['LINK'] ?>"
               title="<?= $item['NAME'] ?>">
                <?= mb_strlen($item['NAME']) > 50 ? mb_substr ($item['NAME'], 0, 50). "..." : $item['NAME']?>
            </a>
            <? if (!empty($item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]])) {
                if (mb_strlen($item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["VALUE"]) > 20){
                    ?>
                    <div class="product__artnumber" title = "<?=$item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["VALUE"]?>">
                        <?=$item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["NAME"] . ': ' . mb_substr ($item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["VALUE"], 0, 20). "..." ?>
                    </div>
                    <?
                } else {
                    ?>
                    <div class="product__artnumber">
                        <?= $item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["NAME"] . ': ' . $item["PROPERTIES"][$arParams["ARTICLE_PROPERTY"]]["VALUE"] ?>
                    </div>
                    <?
                }
            } ?>
            <? if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                if (!$isOffers) {
                    ?>
                    <div class="product__quant" id="prod_qu_<?= $item['ID'] ?>">
                        <span class="title-quant">
                            <? if (!empty($arParams['MESS_SHOW_MAX_QUANTITY'])) { ?>
                                <?= $arParams['MESS_SHOW_MAX_QUANTITY'] .
                                Loc::getMessage('PRODUCT_LABEL_MEASURE',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO']. ' ' : '',
                                        "#CATALOG_MEASURE_NAME#" => $item['CATALOG_MEASURE_NAME']
                                    ]); ?>
                                <?
                            } else {
                                ?><?= Loc::getMessage('PRODUCT_LABEL_AVAILABLE_NAME',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO'] : '',
                                        "#CATALOG_MEASURE_NAME#" => $item['CATALOG_MEASURE_NAME']
                                    ]); ?>
                                <?
                            } ?>
                        </span>
                        <?
                        $item['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                            "sotbit:catalog.store.quantity",
                            "b2bcabinet",
                            array(
                                "CACHE_TIME" => "36000000",
                                "CACHE_TYPE" => "A",
                                "COMPONENT_TEMPLATE" => ".default",
                                "ELEMENT_ID" => $item["ID"],
                                "MESS_RELATIVE_QUANTITY_FEW" => $arParams["MESS_RELATIVE_QUANTITY_FEW"],
                                "MESS_RELATIVE_QUANTITY_MANY" => $arParams["MESS_RELATIVE_QUANTITY_MANY"],
                                "MESS_SHOW_MAX_QUANTITY" => $arParams["MESS_SHOW_MAX_QUANTITY"],
                                "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                                "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                                "STORES" => $arParams["STORES"],
                                "STORE_FIELDS" => $arParams["FIELDS"],
                                "STORE_PROPERTIES" => $arParams["USER_FIELDS"],
                                "USE_STORE" => $arParams["USE_STORE"],
                                "BASE_QUANTITY" => $item['CATALOG_QUANTITY'],
                                "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"]
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    </div>
                    <?
                }
            } elseif (empty($item["OFFERS"])) { ?>
                <div class="product__quant">
                    <? echo Loc::getMessage('PRODUCT_LABEL_RATIO_MEASURE_NAME',
                        [
                            "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO'] : '',
                            "#CATALOG_MEASURE_NAME#" => $item['CATALOG_MEASURE_NAME']
                        ]);?>
                </div>
            <? } ?>
        </td>
        <? if (!$isOffers) {?>
            <?if (!empty($arResult['MIN_MOBILE_PRICE_PRINT']) && !empty($arResult['MIN_MOBILE_PRICE'])) {?>
                <td class="product__property product__property--price-mobile">
                    <span>
                        <?= Loc::getMessage('PRICE_FROM')?>
                    </span>
                    <?echo $arResult['MIN_MOBILE_PRICE_PRINT'];?>
                </td>
            <?}
        }
        foreach ($arResult['TABLE_HEADER'] as $propertyCode => $propertyValue) {
            switch ($propertyCode) {
                case 'PRICES':
                    foreach ($propertyValue as $priceCode => $priceValue) {
                        $itemIds['PRICES'][$priceCode] = $areaId . '_price_' . $priceCode;
                        ?>
                        <td class="product__property product__property--price">
                            <? if (!$isOffers): ?>
                                <div class="wrap-product__property--price">
                                 <span id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                     <?if ($priceCode == "PRIVATE_PRICE") {
                                         if (!empty($arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"])) {
                                             echo $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"];
                                         } else {
                                             echo \SotbitPrivatePriceMain::setPlaceholder($item[$arParams["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '');
                                         }
                                     } else{
                                         echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                     }?>
                                </span>
                                    <? if ($item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'] !== $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE']) {
                                        ?>
                                        <span class="product__property--discount-price">
                                        <?= CCurrencyLang::CurrencyFormat($item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'] * $arResult['ITEM']["CATALOG_MEASURE_RATIO"], $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'], true); ?>
                                    </span>
                                        <?
                                    } ?>
                                </div>
                            <? elseif ($item['MIN_PRICE'][$priceCode]): ?>
                                <span>
                                <?if(!empty($arParams["CURRENCY_ID"]) && \CCurrency::GetBaseCurrency() != $arParams["CURRENCY_ID"]){
                                    echo Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                        [
                                            "#PRICE#" => \CCurrencyLang::CurrencyFormat(
                                                CCurrencyRates::ConvertCurrency($item['MIN_PRICE'][$priceCode],\CCurrency::GetBaseCurrency(), $arParams["CURRENCY_ID"]),
                                                $arParams["CURRENCY_ID"]
                                            )
                                        ]);
                                } else {
                                    echo Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                        [
                                            "#PRICE#" => \CCurrencyLang::CurrencyFormat(
                                                $item['MIN_PRICE'][$priceCode],
                                                \CCurrency::GetBaseCurrency()
                                            )
                                        ]);
                                }
                                ?>
                            </span>
                            <? endif; ?>
                        </td>
                        <?
                    }
                    break;
                case 'QUANTITY':
                    ?>
                    <td class="product__property product__property--quantity"><?
                    if (!$isOffers) {
                        $itemIds['QUANTITY'] = $areaId . '_quantity';
                        $itemIds['QUANTITY_DECREMENT'] = $areaId . '_quantity-decrement';
                        $itemIds['QUANTITY_VALUE'] = $areaId . '_quantity-value';
                        $itemIds['QUANTITY_INCREMENT'] = $areaId . '_quantity-increment';
                        ?>
                        <div class="quantity-selector" id="<?= $itemIds['QUANTITY'] ?>">
                            <button class="quantity-selector__decrement"
                                    type="button"
                                    id="<?= $itemIds['QUANTITY_DECREMENT'] ?>">-
                            </button>
                            <input class="quantity-selector__value"
                                   type="text"
                                   value="<?= $item['ACTUAL_QUANTITY'] ?>"
                                   id="<?= $itemIds['QUANTITY_VALUE'] ?>"
                                <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                            >
                            <button class="quantity-selector__increment"
                                    type="button"
                                    id="<?= $itemIds['QUANTITY_INCREMENT'] ?>">+
                            </button>
                        </div>
                        <?
                    } else {
                        ?>
                        <div class="offers-info" id="<?= $itemIds['OFFERS_TOGGLER'] ?>">
                            <span class="offers-info__label"><?= Loc::getMessage('OFFERS') ?>: </span><span
                                    class="offers-info__count"><?= count($item['OFFERS']) ?></span>
                        </div>
                        <?
                    }
                    ?></td><?
                    break;
                default:
                    if (
                        isset($item['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE']) &&
                        !empty($item['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE'])
                    ) {
                        $value = '';
                        foreach ($item['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE'] as $DISPLAY_PROPERTY) {
                            $value .= $DISPLAY_PROPERTY['NAME'] . "\n";
                        }
                    } else {
                        $value = $item['DISPLAY_PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];
                    }

                    if (is_array($value))
                        $value = implode("\n", $value);

                    ?>
                    <td
                            class="product__property product__property--default"
                            title="<?= $value ?>">
                        <?= $value ?>
                    </td>
                    <?
                    break;
            }
        }
        ?>
    </tr>
<?
if ($isOffers) {
    foreach ($item['OFFERS'] as &$offer) {
        $itemIds['OFFERS'][$offer['ID']]['ID'] = $areaId . '_offer_' . $offer['ID'];
        ?>
        <tr class="product product--offer hidden" id="<?= $itemIds['OFFERS'][$offer['ID']]['ID'] ?>">
            <td class="product__property product__property--image" data-product-property="PICTURE">
                <div class="product__image-wrapper">
                    <img class="product__image" src="<?= $offer['PICTURE'] ?>">
                </div>
            </td>
            <td class="product__property product__property--name" data-product-property="NAME">
                <?$name = $offer['NAME'] ?: $item['NAME'];?>
                <a class="product__link"
                   href="javascript:void(0)"
                   data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
                   title="<?= $offer['NAME'] ?: $item['NAME'] ?>">
                    <?=  mb_strlen($name) > 50 ? mb_substr ($name, 0, 50). "..." : $name ?>
                </a>
                <? if (!empty($offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]])) {
                    if (mb_strlen($offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["VALUE"]) > 20){
                        ?>
                        <div class="product__artnumber" title = "<?=$offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["VALUE"]?>">
                            <?=$offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["NAME"] . ': ' . mb_substr ($offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["VALUE"], 0, 20). "..." ?>
                        </div>
                        <?
                    } else {
                        ?>
                        <div class="product__artnumber">
                            <?=$offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["NAME"] . ': ' . $offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["VALUE"] ?>
                        </div>
                        <?
                    }
                } ?>
                <? if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                    ?>
                    <div class="product__quant" id="prod_qu_<?= $offer['ID'] ?>">
                        <span class="title-quant">
                            <? if (!empty($arParams['MESS_SHOW_MAX_QUANTITY'])) { ?>
                                <?= $arParams['MESS_SHOW_MAX_QUANTITY'] .
                                Loc::getMessage('PRODUCT_LABEL_MEASURE',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $offer['CATALOG_MEASURE_RATIO'] != 1 ? $offer['CATALOG_MEASURE_RATIO'] : '',
                                        "#CATALOG_MEASURE_NAME#" => $offer['CATALOG_MEASURE_NAME']
                                    ]); ?>
                                <?
                            } else {
                                ?><?= Loc::getMessage('PRODUCT_LABEL_AVAILABLE_NAME',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $offer['CATALOG_MEASURE_RATIO'] != 1 ? $offer['CATALOG_MEASURE_RATIO'] : '',
                                        "#CATALOG_MEASURE_NAME#" => $offer['CATALOG_MEASURE_NAME']
                                    ]); ?>
                                <?
                            } ?>
                        </span>
                        <?
                        $offer['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                            "sotbit:catalog.store.quantity",
                            "b2bcabinet",
                            array(
                                "CACHE_TIME" => "36000000",
                                "CACHE_TYPE" => "A",
                                "COMPONENT_TEMPLATE" => ".default",
                                "ELEMENT_ID" => $offer["ID"],
                                "MESS_RELATIVE_QUANTITY_FEW" => $arParams["MESS_RELATIVE_QUANTITY_FEW"],
                                "MESS_RELATIVE_QUANTITY_MANY" => $arParams["MESS_RELATIVE_QUANTITY_MANY"],
                                "MESS_SHOW_MAX_QUANTITY" => $arParams["MESS_SHOW_MAX_QUANTITY"],
                                "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                                "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                                "STORES" => $arParams["STORES"],
                                "STORE_FIELDS" => $arParams["FIELDS"],
                                "STORE_PROPERTIES" => $arParams["USER_FIELDS"],
                                "USE_STORE" => $arParams["USE_STORE"],
                                "BASE_QUANTITY" => $offer['CATALOG_QUANTITY'],
                                "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"]
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                        ?>
                    </div>
                    <?
                } else { ?>
                    <div class="product__quant">
                        <? echo Loc::getMessage('PRODUCT_LABEL_RATIO_MEASURE_NAME',
                            [
                                "#CATALOG_MEASURE_RATIO#" => $offer['CATALOG_MEASURE_RATIO'] != 1 ? $offer['CATALOG_MEASURE_RATIO'] : '',
                                "#CATALOG_MEASURE_NAME#" => $offer['CATALOG_MEASURE_NAME']
                            ]);?>
                    </div>
                <? } ?>
            </td>
            <? if (!empty($offer['MIN_PRICE']['PRINT_DISCOUNT_VALUE'])) { ?>
                <td class="product__property product__property--price-mobile">
                        <span>
                            <?= Loc::getMessage('PRICE_FROM') ?>
                        </span>
                    <?
                    if (!empty($offer['MIN_PRICE']['MIN_PRICE_WITH_PRIVAT_PRICE'])) {
                        echo $offer['MIN_PRICE']['MIN_PRICE_WITH_PRIVAT_PRICE'];
                    } else {
                        echo $offer['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
                    }
                    ?>
                </td>
                <?
            }
            foreach ($arResult['TABLE_HEADER'] as $propertyCode => $propertyValue) {
                switch ($propertyCode) {
                    case 'PRICES':
                        foreach ($propertyValue as $priceCode => $priceValue) {
                            $itemIds['OFFERS'][$offer['ID']]['PRICES'][$priceCode] = $areaId . '_offer_' . $offer['ID'] . '_price_' . $priceCode;
                            ?>
                            <td class="product__property product__property--price">
                                <div class="wrap-product__property--price">
                                    <span id="<?= $itemIds['OFFERS'][$offer['ID']]['PRICES'][$priceCode] ?>">
                                        <?if ( $priceCode == "PRIVATE_PRICE") {
                                            echo \SotbitPrivatePriceMain::setPlaceholder($offer[$arParams["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '');
                                        } elseif ($offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]["CURRENCY"] == $arParams['CURRENCY_ID'] || empty($arParams['CURRENCY_ID'])) {
                                            echo $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                        } elseif ($offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'] != 0) {
                                            echo CCurrencyLang::CurrencyFormat(
                                                CCurrencyRates::ConvertCurrency(
                                                    $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'],
                                                    $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'],
                                                    $arParams["CURRENCY_ID"]),
                                                $arParams["CURRENCY_ID"]
                                                , true);
                                        }

                                        ?>
                                    </span>
                                    <? if (
                                        round((float)$offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'], 2)
                                        !== round((float)$offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], 2)
                                    ) {
                                        ?>
                                        <span class="product__property--discount-price">
                                        <?if ($offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'] == $arParams['CURRENCY_ID'] || empty($arParams['CURRENCY_ID'])) {
                                            echo CCurrencyLang::CurrencyFormat(
                                                $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'],
                                                $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY']
                                                , true);
                                        } else {
                                            echo CCurrencyLang::CurrencyFormat(
                                                CCurrencyRates::ConvertCurrency(
                                                    $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'],
                                                    $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'],
                                                    $arParams["CURRENCY_ID"]),
                                                $arParams["CURRENCY_ID"]
                                                , true);
                                        }?>
                                    </span>
                                        <?
                                    } ?>
                                </div>
                            </td>
                            <?
                        }
                        break;
                    case 'QUANTITY':
                        $itemIds['OFFERS'][$offer['ID']]['QUANTITY'] = $areaId . '_' . $offer['ID'] . '_quantity';
                        $itemIds['OFFERS'][$offer['ID']]['QUANTITY_DECREMENT'] = $areaId . '_' . $offer['ID'] . '_quantity-decrement';
                        $itemIds['OFFERS'][$offer['ID']]['QUANTITY_VALUE'] = $areaId . '_' . $offer['ID'] . '_quantity-value';
                        $itemIds['OFFERS'][$offer['ID']]['QUANTITY_INCREMENT'] = $areaId . '_' . $offer['ID'] . '_quantity-increment';
                        ?>
                        <td class="product__property product__property--quantity">
                            <div class="quantity-selector" id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY'] ?>">
                                <button class="quantity-selector__decrement"
                                        type="button"
                                        id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_DECREMENT'] ?>">-
                                </button>
                                <input class="quantity-selector__value"
                                       type="text"
                                       value="<?= $offer['ACTUAL_QUANTITY'] ?>"
                                       id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_VALUE'] ?>"
                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                >
                                <button class="quantity-selector__increment"
                                        type="button"
                                        id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_INCREMENT'] ?>">+
                                </button>
                            </div>
                        </td>
                        <?
                        break;
                    default:
                        if (isset($offer['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE'])
                            && !empty($offer['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE'])
                        ) {
                            $value = '';
                            foreach ($offer['DISPLAY_PROPERTIES'][$propertyCode]['LINK_ELEMENT_VALUE'] as $DISPLAY_PROPERTY) {
                                $value .= $DISPLAY_PROPERTY['NAME'] . "\n";
                            }
                        } else {
                            $value = $offer['DISPLAY_PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];
                        }

                        if (is_array($value))
                            $value = implode("\n", $value);

                        ?>
                        <td class="product__property product__property--default"
                            title="<?= $value ?>">
                            <?= $value ?>
                        </td>
                        <?
                        break;
                }
            } ?>
        </tr>
        <?
    }
}

$arResult['ITEM_IDS'] = $itemIds;

$minQuant = $arParams['RELATIVE_QUANTITY_FACTOR'] ? $arParams['RELATIVE_QUANTITY_FACTOR'] : $arParams['MIN_AMOUNT'];
if ($isOffers) {
    foreach ($item['OFFERS'] as $key => $val) {
        $offersMassJs[] = ["id" => $val['ID'], "quant" => $val['CATALOG_QUANTITY']];
    }
    $json =json_encode($offersMassJs);
} else {
    $isOffers = 0;
}

if($item['CATALOG_QUANTITY'] == "")
    $item['CATALOG_QUANTITY'] = 0;
?>
    <script>
        BX.message({
            BZI_PRODUCT_NAME: '<?=Loc::getMessage('CT_BZI_PRODUCT_NAME')?>',
            BZI_PRODUCT_ADD_TO_BASKET: '<?=Loc::getMessage('CT_BZI_PRODUCT_ADD_TO_BASKET')?>',
            BZI_PRODUCT_REMOVE_FROM_BASKET: '<?=Loc::getMessage('CT_BZI_PRODUCT_REMOVE_FROM_BASKET')?>'
        });
        if (!<?=$obName?>) {
            var <?=$obName?> = new JCBlankZakazaItem(
                <?=CUtil::PhpToJSObject($arResult)?>,
                <?=CUtil::PhpToJSObject($arParams)?>
            )
        }
        var minquant = <?=$minQuant?>;

        if (<?=$isOffers?>) {
            var arr = JSON.parse('<?php echo $json; ?>');
            for (var i=0; i<=arr.length-1; i++) {
                var id = arr[i]['id'];
                var quant = arr[i]['quant'];
                var element = document.getElementById('prod_qu_'+id);
                if (typeof quant !== 'undefined' && element !== null) {
                    if (quant > minquant) {
                        element.classList.add("green");
                    } else if (quant > 0) {
                        element.classList.add("orange");
                    } else {
                        element.classList.add("red");
                    }
                }
            }
        } else {
            var quant = <?=$item['CATALOG_QUANTITY']?>;
            var element = document.getElementById('prod_qu_<?=$item['ID']?>');
            if (typeof quant !== 'undefined' &&  element !== null) {
                if (quant > minquant) {
                    element.classList.add("green");
                } else if (quant > 0) {
                    element.classList.add("orange");
                } else {
                    element.classList.add("red");
                }
            }
        }
    </script>
<? unset($item, $actualItem, $minOffer, $itemIds, $jsParams); ?>