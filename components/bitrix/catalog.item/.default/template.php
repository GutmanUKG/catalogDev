<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main,
    \Bitrix\Catalog\ProductTable,
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

$canBuyZero = Bitrix\Main\Config\Option::get('catalog', 'default_can_buy_zero', 'N');

$this->setFrameMode(true);

// Multi-store orphan markdown detection
$isMultiStoreMarkdown = !empty($arResult['ITEM']['IS_MULTI_STORE_MARKDOWN']) && !empty($arResult['ITEM']['SECOND_ITEMS']);

CModule::IncludeModule('catalog');

if (!isset($arResult['ITEM'])) {
    return;
}

$item = &$arResult['ITEM'];
$areaId = $arResult['AREA_ID'];
$itemIds = array(
    'ID' => $areaId,
    'PICTURE' => $areaId . '_picture',
    'LINK' => $areaId . '_link',
    'OFFERS_TOGGLER' => $areaId . '_offers-toggler',
    'SKU_TREE' => $areaId . '_sku_tree',
);
$obName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);

$skuProps = array();

$isOffers = isset($item['PRODUCT']['TYPE'])
    && $item['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU
    && isset($item['OFFERS'])
    && count($item['OFFERS']) > 0;

$productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
    ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $item['NAME'];

$jsParams = array(
    'SHOW_ABSENT' => true,
    'AJAX_ID' => $arParams['AJAX_ID'],
    'CATALOG_NOT_AVAILABLE' => $arParams['CATALOG_NOT_AVAILABLE'],
    'PRODUCT_TYPE' => $item['PRODUCT']['TYPE'],
    'PRODUCT' => array(
        'ID' => $item['ID'],
        'NAME' => $productTitle,
        'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
        'MORE_PHOTO' => $item['MORE_PHOTO'],
        'MORE_PHOTO_COUNT' => $item['MORE_PHOTO_COUNT'],
        'ITEM_PRICE_MODE' => $item['ITEM_PRICE_MODE'],
    ),
    'OFFERS_VIEW' => $arParams['OFFERS_VIEW'],
    'ARTICLE_PROPERTY' => $arParams['ARTICLE_PROPERTY'],
    'ARTICLE_PROPERTY_OFFERS' => $arParams['ARTICLE_PROPERTY_OFFERS'],
    'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
    'MESS_RELATIVE_QUANTITY_FEW' => $arParams['MESS_RELATIVE_QUANTITY_FEW'],
    'MESS_RELATIVE_QUANTITY_MANY' => $arParams['MESS_RELATIVE_QUANTITY_MANY'],
    'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
    'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR']
);

if ($item['BIG_DATA']) {
    $jsParams['PRODUCT']['RCM_ID'] = $item['RCM_ID'];
}

if ($isOffers) {
    $jsParams['DEFAULT_PICTURE'] = array(
        'PICTURE' => $item['PRODUCT_PREVIEW'],
        'PICTURE_SECOND' => $item['PRODUCT_PREVIEW_SECOND']
    );
}


foreach ($arParams['SKU_PROPS'] as $skuProperty) {
    if (!isset($item['OFFERS_PROP'][$skuProperty['CODE']])) 
        continue;

    $skuProps[] = array(
        'ID' => $skuProperty['ID'],
        'SHOW_MODE' => $skuProperty['SHOW_MODE'],
        'VALUES' => $skuProperty['VALUES'],
        'VALUES_COUNT' => $skuProprty['VALUES_COUNT'],
    );
}

if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $isOffers) {
    $jsParams['SHOW_QUANTITY'] = $arParams['USE_PRODUCT_QUANTITY'];
    $jsParams['SHOW_SKU_PROPS'] = $item['OFFERS_PROPS_DISPLAY'];
    $jsParams['OFFERS'] = $item['JS_OFFERS'];
    $jsParams['OFFER_SELECTED'] = $item['OFFERS_SELECTED'];
    $jsParams['TREE_PROPS'] = $skuProps;
}


?>

<tbody class="blank-zakaza__item" id="<?=$itemIds['ID']?>" data-entity="items-row" <?if($item['HAS_SECOND']):?>  data-sale="true"  <?endif;?>>
    <tr class="product">
        <td class="product__property product__property--image" <?if($item['HAS_SECOND']):?>title="" <?endif;?>>
            <div class="product__image-wrapper">
                <a class="product__link"
                    href="javascript:void(0)"
                    data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
                    id="<?= $itemIds['LINK'] ?>_img"
                    title="<?= $item['NAME'] ?>">
                <img class="product__image"
                    id=<?= $itemIds['PICTURE'] ?>
                    src="<?= $item['PICTURE'] ?>"
                    width="100%"
                    height="100%">
                </a>
            </div>
        </td>
        <?
        if (
            isset($item['DISPLAY_PROPERTIES']['BREND_ATTR_S']['LINK_ELEMENT_VALUE']) &&
            !empty($item['DISPLAY_PROPERTIES']['BREND_ATTR_S']['LINK_ELEMENT_VALUE'])
        ) {
            $value = '';
            foreach ($item['DISPLAY_PROPERTIES']['BREND_ATTR_S']['LINK_ELEMENT_VALUE'] as $DISPLAY_PROPERTY) {
                $value .= $DISPLAY_PROPERTY['NAME'] . "\n";
            }
        } else {
            $value = $item['DISPLAY_PROPERTIES']['BREND_ATTR_S']['DISPLAY_VALUE'];
        }

        if (is_array($value))
            $value = implode("\n", $value);

        ?>
        <td
                class="product__property product__property--default"
                title='<?= $value ?>'>
            <?= $value ?>
        </td>
        <td class="product__property product__property--name">
            <div class="product__container">
                <div class="product__info">
                    <?
                    if($item['PROPERTIES'] && $arParams['LABEL_PROP'])
                    {
                        ?>
                        <div class="d-flex gap-2">
                        <?
                        foreach($arParams['LABEL_PROP'] as $label)
                        {
                            if ($item['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
                                ?>
                                <span class="badge <?=$item['PROPERTIES'][$label]['HINT']?>">
                                    <?=$item['PROPERTIES'][$label]['NAME']?>
                                </span>
                                <?
                            }
                        }
                        ?>
                        </div>
                        <?
                    }
                    ?>
                    <a class="product__link"
                    href="javascript:void(0)"
                    data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
                    id="<?= $itemIds['LINK'] ?>"
                    title="<?= $item['NAME'] ?>">
                        <?//= mb_strlen($item['NAME']) > 50 ? mb_substr ($item['NAME'], 0, 50). "..." : $item['NAME']?>
                        <?=$item['NAME'];?>
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
                    } else { ?>
                        <div class="product__artnumber"></div>
                    <?
                    }
                    ?>
                    <? if (!empty($item['MARKDOWN_CATEGORY_LABELS'])): ?>
                        <div class="product__markdown-category">
                            <?= Loc::getMessage('CT_BCI_MARKDOWN_CATEGORY') ?>: <?= implode(', ', $item['MARKDOWN_CATEGORY_LABELS']) ?>
                        </div>
                    <? endif; ?>
                </div>
                <? if ($isOffers && $arParams['OFFERS_VIEW'] === 'COMBINED'): ?>
                    <div class="toggle-offers" id="<?= $itemIds['OFFERS_TOGGLER'] ?>">
                        <span class="toggle-offers__label"><?= Loc::getMessage('OFFERS') ?></span>
                        <i class="ph-caret-down"></i>
                    </div>
                <? endif; ?>
            </div>
        </td>
        <td class="product__property product__property--price-mobile">
            <span>
                <?= Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE', [
                    "#PRICE#" => !empty($item['MIN_PRICE']) ? 
                        \CCurrencyLang::CurrencyFormat(min($item['MIN_PRICE']), \CCurrency::GetBaseCurrency()) : 
                        $arResult['MIN_MOBILE_PRICE_PRINT']
                ])?>
            </span>
        </td>
        <?
        foreach ($arResult['TABLE_HEADER'] as $propertyCode => $propertyValue) {
            switch ($propertyCode) {
                case 'OFFERS':
                    if ($arParams['OFFERS_VIEW'] === 'LIST') continue;
                    ?>
                    <td class="product__property product__property--offers" id=<?= $itemIds['SKU_TREE'] ?>>
                        <?
                        foreach ($arParams['SKU_PROPS'] as $skuProperty)
                        {
                            $propertyId = $skuProperty['ID'];
                            $skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
                            if (!isset($arResult['ITEM']['SKU_TREE_VALUES'][$propertyId]))
                                continue;
                            ?>
                            <div class="product-item-scu-container" data-entity="sku-line-block">
                                <span class="product-item-scu-name">
                                    <?=$skuProperty['NAME']?>
                                </span>
                                <div class="product-item-scu-block">
                                    <ul class="product-item-scu-list">
                                        <?
                                        foreach ($skuProperty['VALUES'] as $value)
                                        {
                                            if (!isset($arResult['ITEM']['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
                                                continue;

                                            $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                            if ($skuProperty['SHOW_MODE'] === 'PICT') {
                                                ?>
                                                <li class="product-item-scu-item-container" title="<?=$value['NAME']?>"
                                                    data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>">
                                                    <div class="product-item-scu-item-block">
                                                        <img src="<?= $value['PICT']['SRC'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg' ?>" alt="<?= $value['NAME'] ?>">
                                                    </div>
                                                </li>
                                                <?
                                            } else {
                                                ?>
                                                <li class="product-item-scu-item-container" title="<?=$value['NAME']?>"
                                                    data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>">
                                                    <div class="product-item-scu-item-block product-item-scu-item-block--text">
                                                        <span class="product-item-scu-item-text"><?=$value['NAME']?></span>
                                                    </div>
                                                </li>
                                                <?
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?
                        }
                        ?>
                    </td>
                    <?
                    if ($arParams['OFFERS_VIEW'] === 'BLOCK') {
                        foreach ($item['OFFERS'] as $index => &$offer) {
                            $offer['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                "sotbit:catalog.store.quantity",
                                "b2bcabinet",
                                array(
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_TYPE" => "A",
                                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                                    "ELEMENT_ID" => $offer["ID"],
                                    "CONTAINER_ID" => "prod_qu_" . $offer['ID'],
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
                                    "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                                    "OFFERS_VIEW" => $arParams['OFFERS_VIEW']
                                ),
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        }
                    }
                    ?>
                    <?
                    break;
                case 'AVALIABLE':
                    if (\Onelab\Catalog\Product\Main::isProductPreOrder($item)) {?>
                        <td class="product__property product__property--avaliable"> 
                            Товар под заказ
                        </td>
                    <?} elseif ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                        if (!$isOffers) {
                            ?>
                            <td class="product__property product__property--avaliable">
                                <div class="product__quant" id="prod_qu_<?= $item['ID'] ?>">
                                <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>
                                <?
                                    if($item['CATALOG_QUANTITY'] > 200) {
                                        echo "> 200";
                                    }
                                    elseif($item['CATALOG_QUANTITY'] > 100) {
                                        echo "> 100";
                                    }
                                    elseif($item['CATALOG_QUANTITY'] > 50) {
                                        echo "> 50";
                                    }
                                    else {
                                        echo $item['CATALOG_QUANTITY'];
                                    }
                                    /*$item['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                        "sotbit:catalog.store.quantity",
                                        "b2bcabinet",
                                        array(
                                            "CACHE_TIME" => "36000000",
                                            "CACHE_TYPE" => "A",
                                            "COMPONENT_TEMPLATE" => "b2bcabinet",
                                            "ELEMENT_ID" => $item["ID"],
                                            "CONTAINER_ID" => "prod_qu_" . $item['ID'],
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
                                    );*/
                                    ?>
                                    <span class="title-quant">
                                        <?= 
                                        Loc::getMessage('PRODUCT_LABEL_MEASURE',
                                            [
                                                "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO']. ' ' : '',
                                                "#CATALOG_MEASURE_NAME#" => $arParams['SHOW_MAX_QUANTITY'] !== 'M' ? $item['CATALOG_MEASURE_NAME'] : ''
                                            ]); ?>
                                    </span>
                                </div>
                            </td>
                            <?
                        } else { ?>
                            <td class="product__property product__property--avaliable">
                                <div class="product__quant">
                                    <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>
                                    <span class="item-quantity__general">
                                        <? 
                                        if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                            if(empty($item['CATALOG_QUANTITY'])) {
                                                echo $arParams['MESS_NOT_AVAILABLE'];
                                            } else {
                                                echo  $item['CATALOG_QUANTITY'] > $arParams['RELATIVE_QUANTITY_FACTOR']
                                                        ? $arParams['MESS_RELATIVE_QUANTITY_MANY']
                                                        : $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                            }
                                        } else {
                                            echo $item['CATALOG_QUANTITY'];
                                        }
                                        ?>
                                    </span>
                                    <span class="title-quant">
                                        <?= 
                                        Loc::getMessage('PRODUCT_LABEL_MEASURE',
                                            [
                                                "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO']. ' ' : '',
                                                "#CATALOG_MEASURE_NAME#" => $arParams['SHOW_MAX_QUANTITY'] !== 'M' ? $item['CATALOG_MEASURE_NAME'] : ''
                                            ]); ?>
                                    </span>
                                </div>
                            </td>
                        <? 
                        }
                    } elseif (empty($item["OFFERS"])) { ?>
                        <td class="product__property product__property--avaliable"> 
                            <div class="product__quant">
                                <? echo Loc::getMessage('PRODUCT_LABEL_RATIO_MEASURE_NAME',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $item['CATALOG_MEASURE_RATIO'] != 1 ? $item['CATALOG_MEASURE_RATIO'] : '',
                                        "#CATALOG_MEASURE_NAME#" => $item['CATALOG_MEASURE_NAME']
                                    ]);?>
                            </div>
                        </td>
                   <? }
                   break;
                    /*
                case 'PRICES':
                    foreach ($propertyValue as $priceCode => $priceValue) {
                        $itemIds['PRICES'][$priceCode] = $areaId . '_price_' . $priceCode;
                        ?>
                        <?if($priceCode != "Цена  OFFLINE KZT"):?>
                            <td class="product__property product__property--price" data-entity="price-block" data-code="<?= $priceCode ?>">
                                <div class="wrap-product__property--price">
                                    <? if (!$isOffers): ?>
                                        <span id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                            <?if ($priceCode == "PRIVATE_PRICE") {
                                                if (!empty($arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"])) {
                                                    echo $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"];
                                                } else {
                                                    echo \SotbitPrivatePriceMain::setPlaceholder($item[$arParams["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '');
                                                }
                                            } else {
                                                if($priceCode == 'Цена дилерского портала KZT') {
                                                    if($arParams["USER"]["UF_APPLY_PRICE"] == 1) {
                                                        if($item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] != '') {
                                                            if($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 8) {
                                                                echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                            }
                                                            elseif($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 9) {
                                                                $arBrandNames = [];
                                                                foreach ($arParams["USER"]["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                                    if($arBrand = $obBrand->GetNext()) {
                                                                        $arBrandNames[] = $arBrand['NAME'];
                                                                    }
                                                                }
                                                                if(in_array($item["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                                    echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                                else {
                                                                    echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                            }
                                                            elseif($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 10) {
                                                                $arBrandNames = [];
                                                                foreach ($arParams["USER"]["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                                    if($arBrand = $obBrand->GetNext()) {
                                                                        $arBrandNames[] = $arBrand['NAME'];
                                                                    }
                                                                }
                                                                if(in_array($item["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                                    echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                                else {
                                                                    echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                            }
                                                            else {
                                                                echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                            }
                                                        }
                                                        else {
                                                            echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                        }
                                                    }
                                                    else {
                                                        echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                    }
                                                }

                                                if($priceCode == 'RRP') {
                                                    $db_res = CPrice::GetList(
                                                        array(),
                                                        array(
                                                            "PRODUCT_ID" => $item["ID"],
                                                            "CATALOG_GROUP_ID" => 8
                                                        )
                                                    );
                                                    if ($ar_res = $db_res->Fetch()) {
                                                        echo CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
                                                    }
                                                }
                                            }?>
                                        </span>
                                            <? if (
                                                round((float)$item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'], 2)
                                                !== round((float)$item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], 2)) {
                                                ?>
                                                <span class="product__property--old-price">
                                                    <?= CCurrencyLang::CurrencyFormat($item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'], true); ?>
                                                </span>
                                                <?
                                            } ?>
                                    <? else :?>
                                        <span><?=$item['MIN_PRICE'][$priceCode] ? 
                                            Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                            [
                                                "#PRICE#" => \CCurrencyLang::CurrencyFormat(
                                                    $item['MIN_PRICE'][$priceCode],
                                                    \CCurrency::GetBaseCurrency()
                                                )
                                            ]) :
                                            '' ?>
                                        </span>
                                        <span class="product__property--old-price"></span>
                                    <? endif; ?>
                                </div>
                            </td>
                        <?endif;?>
                        <?
                    }
                    break;

                    */
                case 'PRICES':
                    foreach ($propertyValue as $priceCode => $priceValue) {
                        $itemIds['PRICES'][$priceCode] = $areaId . '_price_' . $priceCode;

                        if ($priceCode != "Цена  OFFLINE KZT"): ?>

                            <td class="product__property product__property--price"
                                data-entity="price-block"
                                data-code="<?= $priceCode ?>">

                                <div class="wrap-product__property--price">

                                    <? if (!$isOffers): ?>

                                        <?
                                        $priceData = $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']] ?? null;

                                        $basePrice = $priceData ? round((float)$priceData['PRICE'], 2) : null;
                                        $discountPrice = $priceData ? round((float)$priceData['DISCOUNT_PRICE'], 2) : null;
                                        ?>

                                        <!-- OLD PRICE (сначала старая) -->
                                        <? if ($priceData && $discountPrice !== $basePrice): ?>
                                            <span class="product__property--old-price">
                                <?= CCurrencyLang::CurrencyFormat(
                                    $priceData['PRICE'],
                                    $priceData['CURRENCY'],
                                    true
                                ); ?>
                            </span>
                                        <? endif; ?>

                                        <!-- ACTUAL PRICE (потом актуальная) -->
                                        <span id="<?= $itemIds['PRICES'][$priceCode] ?>">
                            <?php
                            if ($priceCode == "PRIVATE_PRICE") {
                                if (!empty($arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"])) {
                                    echo $arParams['ITEMS_PRIVAT_PRICES'][$arResult['ITEM']['ID']]["PRIVAT_PRICE_PRINT"];
                                } else {
                                    echo \SotbitPrivatePriceMain::setPlaceholder($item[$arParams["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '');
                                }
                            } else {
                                if ($priceCode == 'Цена дилерского портала KZT') {
                                    if ($arParams["USER"]["UF_APPLY_PRICE"] == 1) {
                                        if ($item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] != '') {
                                            if ($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 8) {
                                                echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                            } elseif ($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 9) {
                                                $arBrandNames = [];
                                                foreach ($arParams["USER"]["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                    if ($arBrand = $obBrand->GetNext()) {
                                                        $arBrandNames[] = $arBrand['NAME'];
                                                    }
                                                }
                                                if (in_array($item["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                    echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                } else {
                                                    echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                }
                                            } elseif ($arParams["USER"]["UF_APPLY_PRICE_FOR"] == 10) {
                                                $arBrandNames = [];
                                                foreach ($arParams["USER"]["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                    if ($arBrand = $obBrand->GetNext()) {
                                                        $arBrandNames[] = $arBrand['NAME'];
                                                    }
                                                }
                                                if (in_array($item["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                    echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                } else {
                                                    echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                }
                                            } else {
                                                echo $item['PRINT_PRICES']["Цена  OFFLINE KZT"][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                            }
                                        } else {
                                            echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                        }
                                    } else {
                                        echo $item['PRINT_PRICES'][$priceCode][$item['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                    }
                                }

                                if ($priceCode == 'RRP') {
                                    $db_res = CPrice::GetList(
                                        array(),
                                        array(
                                            "PRODUCT_ID" => $item["ID"],
                                            "CATALOG_GROUP_ID" => 8
                                        )
                                    );
                                    if ($ar_res = $db_res->Fetch()) {
                                        echo CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
                                    }
                                }
                            }
                            ?>
                        </span>

                                    <? else: ?>

                                        <span>
                            <?= $item['MIN_PRICE'][$priceCode]
                                ? Loc::getMessage(
                                    'CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE',
                                    [
                                        "#PRICE#" => \CCurrencyLang::CurrencyFormat(
                                            $item['MIN_PRICE'][$priceCode],
                                            \CCurrency::GetBaseCurrency()
                                        )
                                    ]
                                )
                                : '' ?>
                        </span>

                                        <span class="product__property--old-price"></span>

                                    <? endif; ?>

                                </div>
                            </td>

                        <?php endif;
                    }
                    break;
                case 'QUANTITY':
                    ?>
                    <td>
                        <?=$arResult['ITEM']['AMOUNT_STORE_51']?>
                    </td>
                    <?if (\Onelab\Catalog\Product\Main::isProductPreOrder($item)) {?>
                        <td>
                            <?$APPLICATION->IncludeComponent(
                                'onelab:product.pre.order',
                                '',
                                [
                                    'PRODUCT_ID' => $item['ID'],
                                ],
                                $component
                            );?>
                        </td>
                    <?} else {?>
                        <td class="product__property product__property--quantity">
    <?
    if (!$isOffers || ($arParams['OFFERS_VIEW'] != 'LIST' && $isOffers)) {
        $itemIds['QUANTITY'] = $areaId . '_quantity';
        $itemIds['QUANTITY_DECREMENT'] = $areaId . '_quantity-decrement';
        $itemIds['QUANTITY_VALUE'] = $areaId . '_quantity-value';
        $itemIds['QUANTITY_INCREMENT'] = $areaId . '_quantity-increment';
        
        $quantity = (int)$item['ACTUAL_QUANTITY'];
        $showQuantity = $quantity > 0;
        ?>

        <? if ($isMultiStoreMarkdown): ?>
            <!-- Multi-store markdown: button opens detail page with modal trigger -->
            <button
                class="btn btn-primary bzd-open-markdown-detail"
                data-detail-url="<?= htmlspecialcharsbx($item['DETAIL_PAGE_URL']) ?>?openMarkdownModal=1"
                style="display: inline-block;">
                В Корзину
            </button>
        <? else: ?>
            <!-- Кнопка "В корзину" — всегда в DOM -->
            <button
                data-id="<?= $itemIds['QUANTITY'] ?>"
                class="btn btn-primary basket-item--add"
                style="<?= $showQuantity ? 'display: none;' : 'display: inline-block;' ?>">
                В Корзину
            </button>

            <!-- Блок с количеством -->
            <div class="bootstrap-touchspin input-group"
                 id="<?= $itemIds['QUANTITY'] ?>"
                 data-entity="quantity-block"
                 style="<?= $showQuantity ? 'display: flex;' : 'display: none;' ?>">
                <span class="input-group-btn input-group-prepend">
                    <button class="btn bootstrap-touchspin-down"
                            type="button"
                            id="<?= $itemIds['QUANTITY_DECREMENT'] ?>"
                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                        <i class="ph-minus"></i>
                    </button>
                </span>
                <input class="touchspin-basic form-control fs-xs"
                       type="text"
                       value="<?= $quantity ?>"
                       id="<?= $itemIds['QUANTITY_VALUE'] ?>"
                       <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                       <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                <span class="input-group-btn input-group-append">
                    <button class="btn bootstrap-touchspin-up"
                            type="button"
                            id="<?= $itemIds['QUANTITY_INCREMENT'] ?>"
                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                        <i class="ph-plus"></i>
                    </button>
                </span>
            </div>
        <? endif; ?>
    <? } else { ?>
        <div class="toggle-offers" id="<?= $itemIds['OFFERS_TOGGLER'] ?>">
            <span class="toggle-offers__label">
                <?= \Sotbit\B2bCabinet\Element::num2word(count($item['OFFERS']), [
                    Loc::getMessage('LABEL_OFFER_ONE'),
                    Loc::getMessage('LABEL_OFFER_MANY'),
                    Loc::getMessage('LABEL_OFFER_MANY'),
                ]) ?>:
            </span>
            <span class="toggle-offers__count"><?= count($item['OFFERS']) ?></span>
        </div>
    <? } ?>
</td>
                    <?}?>
                    <?
                    break;
                default:
                    if($propertyCode != 'BREND_ATTR_S'){
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
                        
                    <?}
                    break;
            }
        }
        ?>
    </tr>
</tbody>
<?
if ($isOffers && $arParams['OFFERS_VIEW'] !== 'BLOCK') {
    ?>
        <tbody class="product-offers hidden" id="<?=$itemIds['ID']?>_offers">
            <tr class="product offer-search">
                <td colspan="2" class="offer-search__td">
                    <div class="offer-search__wrapper form-control-feedback form-control-feedback-start">
                        <input class="form-control" placeholder="<?= Loc::getMessage('OFFER_PLACEHOLDER_SEARCH') ?>" data-offer-search>
                        <button class="form-control-feedback-icon">
                            <i class="ph-magnifying-glass"></i>
                        </button>
                    </div>
                </td>
                <td colspan="<?= $arResult['COUNT_TABLE_HEADER'] - 2 ?>"></td>
                <td class="product-sticky product-left-border"></td>
            </tr>
            <? $itemIds['OFFER_ROW_EMPTY'] = $areaId . '_offer_empty' ?>
            <tr class="product product-empty" id=<?=$itemIds['OFFER_ROW_EMPTY'] ?>>
                <td colspan="<?= $arResult['COUNT_TABLE_HEADER'] + 1 ?>">
                    <div class="product-offers__empty text-center">
                        <h6><?= Loc::getMessage('OFFER_NOT_SEARCH') ?></h6>
                    </div>
                </td>
            </tr>
            <?
            foreach ($item['OFFERS'] as $index => &$offer) {
                $itemIds['OFFERS'][$index]['ID'] = $areaId . '_offer_' . $offer['ID'];
                ?>
                <tr class="product product--offer" id="<?= $itemIds['OFFERS'][$index]['ID'] ?>">
                    <td class="product__property product__property--image">
                        <div class="product__image-wrapper">
                            <a class="product__link"
                                href="javascript:void(0)"
                                data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
                                id="<?= $itemIds['LINK'] ?>_img"
                                title="<?= $item['NAME'] ?>">
                                <img class="product__image" 
                                    src="<?= $offer['PICTURE'] ?>"
                                    width="100%"
                                    height="100%">
                            </a>
                        </div>
                    </td>
                    <td class="product__property product__property--name" data-product-property="NAME">
                        <div class="product__container">
                            <div class="product__info">
                                <?
                                if($offer['PROPERTIES'] && $arParams['LABEL_PROP'])
                                {
                                    ?>
                                    <div class="d-flex gap-2">
                                    <?
                                    foreach($arParams['LABEL_PROP'] as $label)
                                    {
                                        if ($offer['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
                                            ?>
                                            <span class="badge <?=$offer['PROPERTIES'][$label]['HINT']?>">
                                                <?=$offer['PROPERTIES'][$label]['NAME']?>
                                            </span>
                                            <?
                                        }
                                    }
                                    ?>
                                    </div>
                                    <?
                                }
                                ?>
                                <?$name = $offer['NAME'] ?: $item['NAME'];?>
                                <a class="product__link"
                                href="javascript:void(0)"
                                data-href="<?= $item["DETAIL_PAGE_URL"] ?>"
                                title="<?= $name ?>">
                                    <?//=  mb_strlen($name) > 50 ? mb_substr ($name, 0, 50). "..." : $name ?>
                                    <?=$item['NAME'];?>
                                </a>
                                <? if (!empty($offer["PROPERTIES"][$arParams["ARTICLE_PROPERTY_OFFERS"]]["VALUE"])) {
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
                            </div>
                        </div>
                    </td>
                    <td class="product__property product__property--price-mobile">
                            <span>
                                <?= Loc::getMessage('PRICE_FROM') ?>
                            </span>
                        <?= $offer['MIN_PRICE']['PRINT'] ?>
                    </td>
                    <?
                    foreach ($arResult['TABLE_HEADER'] as $propertyCode => $propertyValue) {
                        switch ($propertyCode) {
                            case 'OFFERS':
                                ?>
                                <td class="product__property product__property--offers">
                                <?
                                    foreach ($item['OFFERS_PROP'] as $code => $value) {
                                        if ($arParams['SKU_PROPS'][$code]['SHOW_MODE'] === 'PICT') {
                                            $idSkuProp = $arParams['SKU_PROPS'][$code]['XML_MAP'][$offer['PROPERTIES'][$code]['VALUE']];
                                            ?>
                                            <p class="bzd-offers__offer-property">
                                                <span class="bzd-offers__offer-porperty-name">
                                                    <?= $offer['PROPERTIES'][$code]['NAME'] ?>:
                                                </span>
                                                <span class="bzd-offers__offer-porperty-value">
                                                    <?= $arParams['SKU_PROPS'][$code]['VALUES'][$idSkuProp]['NAME']?>
                                                </span>
                                            </p>
                                            <?
                                        } elseif (!empty($offer['PROPERTIES'][$code]['VALUE'])) {
                                            ?>
                                            <p class="bzd-offers__offer-property">
                                                <span class="bzd-offers__offer-porperty-name">
                                                    <?= $offer['PROPERTIES'][$code]['NAME'] ?>:
                                                </span>
                                                <span class="bzd-offers__offer-porperty-value">
                                                    <?= $offer['PROPERTIES'][$code]['VALUE'] ?>
                                                </span>
                                            </p>
                                            <?
                                        }
                                    }
                                ?>
                                </td>
                                <?
                                break;
                            case 'AVALIABLE':
                                if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>
                                    <td class="product__property product__property--avaliable">
                                        <div class="product__quant" id="prod_qu_<?= $offer['ID'] ?>">
                                            <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>
                                            <?$offer['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                                "sotbit:catalog.store.quantity",
                                                "b2bcabinet",
                                                array(
                                                    "CACHE_TIME" => "36000000",
                                                    "CACHE_TYPE" => "A",
                                                    "COMPONENT_TEMPLATE" => "b2bcabinet",
                                                    "ELEMENT_ID" => $offer["ID"],
                                                    "CONTAINER_ID" => "prod_qu_" . $offer['ID'],
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
                                            <span class="title-quant">
                                                <?= 
                                                Loc::getMessage('PRODUCT_LABEL_MEASURE',
                                                    [
                                                        "#CATALOG_MEASURE_RATIO#" => $offer['CATALOG_MEASURE_RATIO'] != 1 ? $offer['CATALOG_MEASURE_RATIO']. ' ' : '',
                                                        "#CATALOG_MEASURE_NAME#" => $arParams['SHOW_MAX_QUANTITY'] !== 'M' ? $offer['CATALOG_MEASURE_NAME'] : ''
                                                    ]); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <?
                                } elseif (empty($offer["OFFERS"])) { ?>
                                    <td class="product__property product__property--avaliable"> 
                                        <div class="product__quant">
                                            <? echo Loc::getMessage('PRODUCT_LABEL_RATIO_MEASURE_NAME',
                                                [
                                                    "#CATALOG_MEASURE_RATIO#" => $offer['CATALOG_MEASURE_RATIO'] != 1 ? $offer['CATALOG_MEASURE_RATIO'] : '',
                                                    "#CATALOG_MEASURE_NAME#" => $offer['CATALOG_MEASURE_NAME']
                                                ]);?>
                                        </div>
                                    </td>
                                <? }
                                break;
                            case 'PRICES':
                                foreach ($propertyValue as $priceCode => $priceValue) {
                                    $itemIds['OFFERS'][$index]['PRICES'][$priceCode] = $areaId . '_offer_' . $offer['ID'] . '_price_' . $priceCode;
                                    ?>
                                    <td class="product__property product__property--price">
                                        <div class="wrap-product__property--price">
                                            <span id="<?= $itemIds['OFFERS'][$index]['PRICES'][$priceCode] ?>">
                                                <?= $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] ?>
                                            </span>
                                            <? if (
                                                round((float)$offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['DISCOUNT_PRICE'], 2)
                                                !== round((float)$offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], 2)
                                            ) {
                                                ?>
                                                <span class="product__property--old-price">
                                                <?= CCurrencyLang::CurrencyFormat($offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'], true); ?>
                                            </span>
                                                <?
                                            } ?>
                                        </div>
                                    </td>
                                    <?
                                }
                                break;
                            case 'QUANTITY':
                                $itemIds['OFFERS'][$index]['QUANTITY'] = $areaId . '_' . $offer['ID'] . '_quantity';
                                $itemIds['OFFERS'][$index]['QUANTITY_DECREMENT'] = $areaId . '_' . $offer['ID'] . '_quantity-decrement';
                                $itemIds['OFFERS'][$index]['QUANTITY_VALUE'] = $areaId . '_' . $offer['ID'] . '_quantity-value';
                                $itemIds['OFFERS'][$index]['QUANTITY_INCREMENT'] = $areaId . '_' . $offer['ID'] . '_quantity-increment';
                                ?>
                                <td class="product__property product__property--quantity 4422">
                                    <div class="bootstrap-touchspin input-group" id="<?= $itemIds['OFFERS'][$index]['QUANTITY'] ?>">
                                        <span class="input-group-btn input-group-prepend">
                                            <button class="btn bootstrap-touchspin-down"
                                                    type="button"
                                                    id="<?= $itemIds['OFFERS'][$index]['QUANTITY_DECREMENT'] ?>"
                                                    <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                                    <i class="ph-minus"></i>
                                            </button>
                                        </span>
                                        <button data-id="<?= $itemIds['OFFERS'][$index]['QUANTITY'] ?>"
                                                class="btn btn-primary basket-item--add"
                                                id="add-to-cart-<?= $itemIds['OFFERS'][$index]['QUANTITY'] ?>"
                                                style="display: <?= ($offer['ACTUAL_QUANTITY'] < 1) ? 'inline-block' : 'none' ?>;">
                                            В Корзину
                                        </button>
                                        <input class="touchspin-basic form-control fs-xs"
                                            type="text"
                                            value="<?= $offer['ACTUAL_QUANTITY'] ?>"
                                            id="<?= $itemIds['OFFERS'][$index]['QUANTITY_VALUE'] ?>"
                                            <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>
                                        >
                                        
                                        <span class="input-group-btn input-group-append">
                                            <button class="btn bootstrap-touchspin-up"
                                                    type="button"
                                                    id="<?= $itemIds['OFFERS'][$index]['QUANTITY_INCREMENT'] ?>"
                                                    <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                                    <i class="ph-plus"></i>
                                            </button>
                                        </span>
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
                                    $value = $offer['DISPLAY_PROPERTIES'][$propertyCode]['DISPLAY_VALUE'] ?: $item['DISPLAY_PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];
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
            ?>
        </tbody> 
    <?
}

$arResult['ITEM_IDS'] = $itemIds;

?>
<script>
    BX.message({
        BZI_PRODUCT_NAME: '<?=Loc::getMessage('CT_BZI_PRODUCT_NAME')?>',
        BZI_PRODUCT_ADD_TO_BASKET: '<?=Loc::getMessage('CT_BZI_PRODUCT_ADD_TO_BASKET')?>',
        BZI_PRODUCT_REMOVE_FROM_BASKET: '<?=Loc::getMessage('CT_BZI_PRODUCT_REMOVE_FROM_BASKET')?>'
    });

    (function (w) {
        var key = '<?=$obName?>';
        if (typeof w[key] === 'undefined' || !w[key]) {
            w[key] = new JCBlankZakazaItem(
                <?=CUtil::PhpToJSObject($arResult)?>,
                <?=CUtil::PhpToJSObject($jsParams)?>
            );
        }
    })(window);
</script>


<script>
    window['ob<?=$arItem['ID']?>_result'] = <?=CUtil::PhpToJSObject($arItem, false, true)?>;
    window['ob<?=$arItem['ID']?>_params'] = <?=CUtil::PhpToJSObject($jsParams, false, true)?>;
</script>


