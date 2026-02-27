<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Catalog\ProductTable;
/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */
$arProductsMass = [];

$offers = [];
foreach ($arResult['ITEMS'] as $id => &$val) {
    $productPicture = $val['PREVIEW_PICTURE'];

    if (empty($productPicture['ID']) && !empty($val['DETAIL_PICTURE']['ID'])) {
        $productPicture = $val['DETAIL_PICTURE'];
    } else {
        if (empty($productPicture['ID']) && !empty($val['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
            $productPicture['ID'] = $val['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
        }
    }

    if (!empty($productPicture['ID'])) {
        $productPictureOrigin = CFile::GetPath($productPicture['ID']);

        $productPicture = CFile::ResizeImageGet(
            $productPicture['ID'],
            array('width' => 45, 'height' => 45),
            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
            true
        );
    }
    $val['PICTURE'] = $productPicture['src'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg' . $val['ID'];

    $val['IS_OFFERS'] = isset($val['PRODUCT']['TYPE'])
                        && $val['PRODUCT']['TYPE'] === ProductTable::TYPE_SKU
                        && isset($val['OFFERS'])
                        && count($val['OFFERS']) > 0;
    if ($val['IS_OFFERS']) {
        foreach ($val['OFFERS'] as &$offer) {
            $offer['DETAIL_PAGE_URL'] = $val['DETAIL_PAGE_URL'];

            // Picture
            $productPicture = $offer['PREVIEW_PICTURE'];

            if (empty($productPicture['ID']) && !empty($offer['DETAIL_PICTURE']['ID'])) {
                $productPicture = $offer['DETAIL_PICTURE'];
            } else {
                if (empty($productPicture['ID']) && !empty($offer['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0])) {
                    $productPicture['ID'] = $offer['PROPERTIES'][$arParams['ADD_PICT_PROP']]['VALUE'][0];
                }
            }

            if (!empty($productPicture['ID'])) {
                $productPictureOrigin = CFile::GetPath($productPicture['ID']);

                $productPicture = CFile::ResizeImageGet(
                    $productPicture['ID'],
                    array('width' => 45, 'height' => 45),
                    BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                    true
                );
            }
            $offer['PICTURE'] = !$productPicture['src'] ? ($val['PICTURE'] ?: SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg'): $productPicture['src'];
        }
        
        array_push($offers, ...$val['OFFERS']);
        $arResult['ITEMS'][$id] = null;
    }
}
array_push($arResult['ITEMS'], ...$offers);

$arBasketItems = Sotbit\B2BCabinet\Catalog\Basket::getBasketItemsQuantity();
foreach ($arResult['ITEMS'] as $offerId => &$item) {
    $item['ACTUAL_QUANTITY'] = !empty($arBasketItems[$item['ID']]) ? $arBasketItems[$item['ID']] : 0;
    foreach ($item['ITEM_QUANTITY_RANGES'] as $rangeName => $rangeProps ) {
        if ( $item['ACTUAL_QUANTITY'] >= $rangeProps['SORT_FROM']
            && $item['ACTUAL_QUANTITY'] <= $rangeProps['SORT_TO']
        ) {
            $item['ITEM_QUANTITY_RANGE_SELECTED'] = $rangeName;
        }
    }

    // Add private price to prices
    if (Loader::includeModule("sotbit.privateprice") && Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && $GLOBALS["USER"]->IsAuthorized()) {
        $item['PRICES']['PRIVATE_PRICE'] = [
            'CODE' => 'PRIVATE_PRICE',
            'SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY' => str_replace("PRODUCT_", "", Option::get("sotbit.privateprice", "PRODUCT_UNIQUE_KEY", "ID")),
            'PRINT_VALUE' => \SotbitPrivatePriceMain::setPlaceholder($item[str_replace("PRODUCT_", "", Option::get("sotbit.privateprice", "PRODUCT_UNIQUE_KEY", "ID"))], ''),
        ];
    }
}

if (Loader::includeModule("sotbit.privateprice") && Option::get("sotbit.privateprice", "MODULE_STATUS", 0) && $GLOBALS["USER"]->IsAuthorized()) {
    $arResult['PRICES']['PRIVATE_PRICE'] = [
        'ID' => '',
        'TITLE' => Loc::getMessage('CT_BCS_CATALOG_MESS_PRIVATE_PRICE_TITLE'),
    ];
}
unset($offerId, $item);
