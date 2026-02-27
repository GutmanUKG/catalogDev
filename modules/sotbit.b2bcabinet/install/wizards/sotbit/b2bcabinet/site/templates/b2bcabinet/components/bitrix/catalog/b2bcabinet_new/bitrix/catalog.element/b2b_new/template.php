<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Sotbit\B2bCabinet\Helper\Config;


$this->setFrameMode(true);
CJSCore::Init(array("fx"));

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = [
    'ID' => $mainId,
    'TITLE' => $mainId . '_title',
    'DESCRIPTION_SECTION' => $mainId . '_description-section',
    'PROPERTIES_SECTION' => $mainId . '_properties-section',
    'OFFERS_SECTION' => $mainId . '_offers-section',
    'GALLERY_SECTION' => $mainId . '_gallery-section',
    'DOCUMENTS_SECTION' => $mainId . '_documents-section',
    'OFFERS' => [],
    'PRICES' => []
];

$obName = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);

$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title
    = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');
$arParams['MESS_RELATIVE_QUANTITY_NO'] = $arParams['MESS_RELATIVE_QUANTITY_NO'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_NO');


$this->SetViewTarget("stickers");
if ($arResult['PROPERTIES'] && $arParams['LABEL_PROP']) {
    foreach ($arParams['LABEL_PROP'] as $label) {
        if ($arResult['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
            ?>
            <span class="badge b2b_badge <?= $arResult['PROPERTIES'][$label]['HINT'] ?>">
                <?= $arResult['PROPERTIES'][$label]['NAME'] ?>
            </span>
            <?
        }
    }
}
$this->EndViewTarget();
?>
    <main class="blank-zakaza-detail <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'blank-zakaza-detail-not_available' : '' ?>">
        <? if ($_REQUEST["IFRAME"] === "Y"): ?>
            <div class="title_wrap">
                <h1 class="blank-zakaza-detail__title" id="<?= $itemIds['TITLE'] ?>"><?= $name ?></h1>
                <div class="product-inner__stickers-frame">
                    <?
                    if ($arResult['PROPERTIES'] && $arParams['LABEL_PROP']) {
                        foreach ($arParams['LABEL_PROP'] as $label) {
                            if ($arResult['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
                                ?>
                                <span class="badge b2b_badge <?= $arResult['PROPERTIES'][$label]['HINT'] ?>">
                                    <?= $arResult['PROPERTIES'][$label]['NAME'] ?>
                                </span>
                                <?
                            }
                        }
                    } ?>
                </div>
            </div>
        <? endif; ?>
        <div class="blank-zakaza-detail__wrapper">
            <aside class="blank-zakaza-detail__aside card">
                <div class="blank-zakaza-detail__info">
                    <div class="blank-zakaza-detail__image-wrapper">
                        <img class="blank-zakaza-detail__image" src="<?= $arResult['PICTURE'] ?>" title="<?= $title ?>"
                             alt="<?= $alt ?>">
                    </div>
                    <? if ($arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY']]): ?>
                        <div class="blank-zakaza-detail__info-item">
                            <?= $arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY']]['NAME'] ?>
                            <?= $arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY']]['VALUE'] ?>
                        </div>
                    <? endif; ?>
                    <? if ($arResult['PRODUCT']['TYPE'] !== 3): ?>
                        <? if ($arParams['SHOW_MAX_QUANTITY'] !== "N") { ?>
                            <div class="blank-zakaza-detail__info-item">
                                <span>
                                    <?= Loc::getMessage('PRODUCT_LABEL_AVAILABLE_NAME',
                                        [
                                            "#CATALOG_MEASURE_RATIO#" => $arResult['CATALOG_MEASURE_RATIO'] != 1 ? $arResult['CATALOG_MEASURE_RATIO'] . ' ' : '',
                                            "#CATALOG_MEASURE_NAME#" => $arResult['CATALOG_MEASURE_NAME']
                                        ]); ?>
                                </span>
                                <? $arResult['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                    "sotbit:catalog.store.quantity",
                                    ".default",
                                    array(
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_TYPE" => "A",
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "ELEMENT_ID" => $arResult["ID"],
                                        "MESS_RELATIVE_QUANTITY_NO" => $arParams['MESS_RELATIVE_QUANTITY_NO'],
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
                                        "BASE_QUANTITY" => $arResult['CATALOG_QUANTITY'],
                                        "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"]
                                    ),
                                    $component,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        <? } else {
                            ?>
                            <div class="blank-zakaza-detail__info-item">
                                <? echo Loc::getMessage('PRODUCT_LABEL_RATIO_MEASURE_NAME',
                                    [
                                        "#CATALOG_MEASURE_RATIO#" => $arResult['CATALOG_MEASURE_RATIO'] != 1 ? $arResult['CATALOG_MEASURE_RATIO'] : '',
                                        "#CATALOG_MEASURE_NAME#" => $arResult['CATALOG_MEASURE_NAME']
                                    ]); ?>
                            </div>
                            <?
                        } ?>
                        <div class="blank-zakaza-detail__info-item bzd-prices">
                            <ul class="bzd-prices__list">
                                <?
                                foreach ($arResult['PRINT_PRICES'] as $priceCode => $price):?>
                                    <? $itemIds['PRICES'][$priceCode] = $mainId . '_price_' . $priceCode; ?>
                                    <?
                                    if ($priceCode !== "PRIVATE_PRICE" && empty($price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'])) {
                                        continue;
                                    }
                                    ?>
                                    <li class="bzd-prices__item">
                                        <span class="bzd-prices__item-name"><?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE'] ?></span>
                                        <span class="bzd-prices__item-value" id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                        <?= $priceCode == "PRIVATE_PRICE" ?
                                            \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] ?>
                                    </span>
                                        <span class="product__property--discount-price"
                                              id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                        <?= $priceCode == "PRIVATE_PRICE" ?
                                            "" :
                                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT_WHITHOUT_DISCONT'] ?>
                                    </span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                        <div class="blank-zakaza-detail__info-item">
                            <?
                            $itemIds['QUANTITY'] = $mainId . '_quantity';
                            $itemIds['QUANTITY_DECREMENT'] = $mainId . '_quantity-decrement';
                            $itemIds['QUANTITY_VALUE'] = $mainId . '_quantity-value';
                            $itemIds['QUANTITY_INCREMENT'] = $mainId . '_quantity-increment';
                            ?>
                            <div class="quantity-selector" id="<?= $itemIds['QUANTITY'] ?>">
                                <button class="quantity-selector__decrement"
                                        type="button"
                                        id="<?= $itemIds['QUANTITY_DECREMENT'] ?>">-
                                </button>
                                <input class="quantity-selector__value"
                                       type="text"
                                       value="<?= $arResult['ACTUAL_QUANTITY'] ?>"
                                       id="<?= $itemIds['QUANTITY_VALUE'] ?>"
                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                >
                                <button class="quantity-selector__increment"
                                        type="button"
                                        id="<?= $itemIds['QUANTITY_INCREMENT'] ?>">+
                                </button>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </aside>
            <div class="blank-zakaza-detail__main">

                <section class="blank-zakaza-detail__main-section card card-body"
                         id="<?= $itemIds['DESCRIPTION_SECTION'] ?>">
                    <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_DESCRIPTION") ?></h2>
                    <?= $arResult['DETAIL_TEXT'] ?>
                </section>

                <? if (count($arResult['DISPLAY_PROPERTIES'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['PROPERTIES_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_PROPERTIES") ?></h2>
                        <div class="bzd-props">
                            <table class="bzd-props__table">
                                <? foreach ($arResult['DISPLAY_PROPERTIES'] as $property): ?>
                                    <tr class="bzd-props__table-row">
                                        <td class="bzd-props__table-col">
                                            <?= $property['NAME'] ?>
                                        </td>
                                        <td class="bzd-props__table-col">
                                            <?= (is_array($property['DISPLAY_VALUE'])
                                                ? implode(', ', $property['DISPLAY_VALUE'])
                                                : $property['DISPLAY_VALUE'])
                                            ?>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </table>
                        </div>
                    </section>
                <? endif; ?>

                <? if (count($arResult['OFFERS'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['OFFERS_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_OFFERS") ?></h2>
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
                                <? foreach ($arResult['OFFERS'] as &$offer): ?>
                                    <? $itemIds['OFFERS'][$offer['ID']]['ID'] = $mainId . '_offer_' . $offer['ID']; ?>
                                    <tr class="bzd-offers__offer" id="<?= $itemIds['OFFERS'][$offer['ID']] ?>">
                                        <td class="bzd-offers__offer-cell">
                                            <img class="bzd-offers__offer-image" src="<?= $offer['PICTURE'] ?>"
                                                 height="45" width="45">
                                        </td>
                                        <td class="bzd-offers__offer-cell">
                                            <p class="bzd-offers__offer-name" title="<?= $offer['NAME'] ?>"><?= $offer['NAME'] ?></p>
                                            <? if ($arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]): ?>
                                                <div class="bzd-offers__offer-artnumber">
                                                    <?= $offer['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['NAME'] ?>
                                                    <?= $offer['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['VALUE'] ?>
                                                </div>
                                            <? endif; ?>

                                        </td>
                                        <? if ($arParams['SHOW_MAX_QUANTITY'] !== "N") { ?>
                                            <td class="bzd-offers__offer-cell">
                                                <?
                                                $offer['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                                    "sotbit:catalog.store.quantity",
                                                    ".default",
                                                    array(
                                                        "CACHE_TIME" => "36000000",
                                                        "CACHE_TYPE" => "A",
                                                        "COMPONENT_TEMPLATE" => ".default",
                                                        "ELEMENT_ID" => $offer["ID"],
                                                        "MESS_RELATIVE_QUANTITY_NO" => $arParams['MESS_RELATIVE_QUANTITY_NO'],
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
                                            </td>
                                        <? } ?>
                                        <td class="bzd-offers__offer-cell">
                                            <? foreach ($offer['DISPLAY_PROPERTIES'] as $code => $property): ?>
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
                                                <? foreach ($offer['PRINT_PRICES'] as $priceCode => $price): ?>
                                                    <? $itemIds['OFFERS'][$offer['ID']]['PRICES'][$priceCode] = $mainId . '_offer_' . $offer['ID'] . '_price_' . $priceCode; ?>
                                                    <li class="bzd-prices__item">
                                                        <span class="bzd-prices__item-name"><?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE']?></span>
                                                        <span class="bzd-prices__item-value"
                                                              id="<?= $itemIds['OFFERS'][$offer['ID']]['PRICES'][$priceCode] ?>">
                                                       <?= $priceCode == "PRIVATE_PRICE" ?
                                                           \SotbitPrivatePriceMain::setPlaceholder($offer[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                           $price[$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] ?>
                                                    </span>
                                                        <span class="product__property--discount-price"
                                                              id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                                        <? if (round((float)$offer["PRICES"][$priceCode]['VALUE_VAT'], 2) !== round((float)$offer["PRICES"][$priceCode]['DISCOUNT_VALUE_VAT'], 2)): ?>
                                                            <?= $priceCode == "PRIVATE_PRICE" ?
                                                                "" :
                                                                CCurrencyLang::CurrencyFormat($offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRICE'], $offer['PRINT_PRICES'][$priceCode][$offer['ITEM_QUANTITY_RANGE_SELECTED']]['CURRENCY'], true); ?>
                                                        <? endif; ?>
                                                    </span>
                                                    </li>
                                                <? endforeach; ?>
                                            </ul>
                                        </td>
                                        <td class="bzd-offers__offer-cell">
                                            <?
                                            $itemIds['OFFERS'][$offer['ID']]['QUANTITY'] = $mainId . '_' . $offer['ID'] . '_quantity';
                                            $itemIds['OFFERS'][$offer['ID']]['QUANTITY_DECREMENT'] = $mainId . '_' . $offer['ID'] . '_quantity-decrement';
                                            $itemIds['OFFERS'][$offer['ID']]['QUANTITY_VALUE'] = $mainId . '_' . $offer['ID'] . '_quantity-value';
                                            $itemIds['OFFERS'][$offer['ID']]['QUANTITY_INCREMENT'] = $mainId . '_' . $offer['ID'] . '_quantity-increment';
                                            ?>
                                            <div class="quantity-selector"
                                                 id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY'] ?>">
                                                <button class="quantity-selector__decrement"
                                                        type="button"
                                                        id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_DECREMENT'] ?>">
                                                    -
                                                </button>
                                                <input class="quantity-selector__value"
                                                       type="text"
                                                       value="<?= $offer['ACTUAL_QUANTITY'] ?>"
                                                       id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_VALUE'] ?>"
                                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                                >
                                                <button class="quantity-selector__increment"
                                                        type="button"
                                                        id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_INCREMENT'] ?>">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <? endif; ?>

                <? if (count($arResult['GALLERY'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['GALLERY_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_GALLERY") ?></h2>
                        <div class="bzd-gallery">
                            <? foreach ($arResult['GALLERY'] as $image): ?>
                                <div class="bzd-gallery__item">
                                    <div class="bzd-gallery__image-wrapper">
                                        <img class="bzd-gallery__image" src="<?= $image['LINK'] ?>"
                                             title="<?= $image['DECRIPTION'] ?>" alt="<?= $image['NAME'] ?>">
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </section>
                <? endif; ?>

                <? if (count($arResult['DOCUMENTS'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['DOCUMENTS_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_DOCUMENTS") ?></h2>
                        <div class="bzd-documents">
                            <? foreach ($arResult['DOCUMENTS'] as $document): ?>
                                <a class="bzd-documents__link" href="<?= $document['LINK'] ?>" target="_blank">
                                    <svg class="bzd-documents__icon" width="32" height="32" viewBox="0 0 32 32"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0)">
                                            <path d="M28.682 7.158C27.988 6.212 27.02 5.104 25.958 4.042C24.896 2.98 23.788 2.012 22.842 1.318C21.23 0.136 20.448 0 20 0H4.5C3.122 0 2 1.122 2 2.5V29.5C2 30.878 3.122 32 4.5 32H27.5C28.878 32 30 30.878 30 29.5V10C30 9.552 29.864 8.77 28.682 7.158ZM24.542 5.458C25.502 6.418 26.254 7.282 26.81 8H21.998V3.19C22.716 3.746 23.582 4.498 24.54 5.458H24.542ZM28 29.5C28 29.772 27.772 30 27.5 30H4.5C4.23 30 4 29.772 4 29.5V2.5C4 2.23 4.23 2 4.5 2C4.5 2 19.998 2 20 2V9C20 9.552 20.448 10 21 10H28V29.5Z"
                                                  fill="#3E495F"/>
                                            <path d="M23 26H9C8.448 26 8 25.552 8 25C8 24.448 8.448 24 9 24H23C23.552 24 24 24.448 24 25C24 25.552 23.552 26 23 26Z"
                                                  fill="#3E495F"/>
                                            <path d="M23 22H9C8.448 22 8 21.552 8 21C8 20.448 8.448 20 9 20H23C23.552 20 24 20.448 24 21C24 21.552 23.552 22 23 22Z"
                                                  fill="#3E495F"/>
                                            <path d="M23 18H9C8.448 18 8 17.552 8 17C8 16.448 8.448 16 9 16H23C23.552 16 24 16.448 24 17C24 17.552 23.552 18 23 18Z"
                                                  fill="#3E495F"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0">
                                                <rect width="32" height="32" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <div class="bzd-documents__info">
                                <span class="bzd-documents__name">
                                    <?= $document['NAME'] ?>
                                </span>
                                        <span class="bzd-documents__size">
                                    <svg class="bzd-documents__size-icon" width="12" height="12" viewBox="0 0 12 12"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.625 8.25C11.418 8.25 11.25 8.41801 11.25 8.62499V11.25H0.750012V8.62499C0.750012 8.41798 0.581999 8.25 0.375021 8.25C0.168044 8.25 0 8.41798 0 8.62499V11.625C0 11.832 0.168013 12 0.37499 12H11.625C11.832 12 12 11.832 12 11.625V8.62499C12 8.41798 11.832 8.25 11.625 8.25Z"
                                              fill="#333333"/>
                                        <path d="M5.72609 8.89011C5.87198 9.03449 6.11611 9.03599 6.26196 8.89011L8.88658 6.30261C9.03471 6.15598 9.03433 5.91861 8.88658 5.77236C8.73883 5.62573 8.49884 5.62573 8.35109 5.77236L6.37296 7.72237V0.37499C6.37296 0.167982 6.20345 0 5.9942 0C5.78495 0 5.61544 0.168013 5.61544 0.37499V7.72237L3.63731 5.77236C3.48918 5.62573 3.24957 5.62573 3.10182 5.77236C2.95369 5.91899 2.95369 6.15635 3.10182 6.30261L5.72609 8.89011Z"
                                              fill="#333333"/>
                                    </svg>
                                    <?= CFile::FormatSize($document['ORIGIN']['FILE_SIZE']) ?>
                                </span>
                                    </div>

                                </a>
                            <? endforeach; ?>
                        </div>
                    </section>
                <? endif; ?>
                <? if (
                    Loader::includeModule('sotbit.crosssell') &&
                    \Bitrix\Main\Config\Option::get("sotbit.crosssell", 'sotbit.crosssell_INC_MODULE', '', SITE_ID) == 'Y' &&
                    $arParams['DETAIL_CROSSSELL_STATUS'] === 'Y'
                    ) { ?>
                    <?
                    $APPLICATION->IncludeComponent(
                        "sotbit:crosssell.crosssell.list",
                        "b2bcabinet",
                        array(
                            "ACTION_VARIABLE" => $arParams['ACTION_VARIABLE'],
                            "ADD_PROPERTIES_TO_BASKET" => $arParams['ADD_PROPERTIES_TO_BASKET'],
                            "ADD_SECTIONS_CHAIN" => "N",
                            "AJAX_MODE" => $arParams['AJAX_MODE'],
                            "AJAX_OPTION_ADDITIONAL" => $arParams['AJAX_OPTION_ADDITIONAL'],
                            "AJAX_OPTION_HISTORY" => $arParams['AJAX_OPTION_HISTORY'],
                            "AJAX_OPTION_JUMP" => $arParams['AJAX_OPTION_JUMP'],
                            "AJAX_OPTION_STYLE" => $arParams['AJAX_OPTION_STYLE'],
                            "BACKGROUND_IMAGE" => $arParams['BACKGROUND_IMAGE'],
                            "BASKET_URL" => $arParams['BASKET_URL'],
                            "BROWSER_TITLE" => $arParams['BROWSER_TITLE'],
                            "CACHE_FILTER" => $arParams['CACHE_FILTER'],
                            "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                            "COMPATIBLE_MODE" => $arParams['COMPATIBLE_MODE'],
                            "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                            "CROSSSELL_LIST" => $arParams['DETAIL_CROSSSELL_LIST'],
                            "DETAIL_URL" => $arParams['DETAIL_URL'],
                            "DISABLE_INIT_JS_IN_COMPONENT" => $arParams['DISABLE_INIT_JS_IN_COMPONENT'],
                            "DISPLAY_BOTTOM_PAGER" => $arParams['DISPLAY_BOTTOM_PAGER'],
                            "DISPLAY_COMPARE" => $arParams['DISPLAY_COMPARE'],
                            "DISPLAY_TOP_PAGER" => $arParams['DISPLAY_TOP_PAGER'],
                            "ELEMENT_SORT_FIELD" => $arParams['ELEMENT_SORT_FIELD'],
                            "ELEMENT_SORT_FIELD2" => $arParams['ELEMENT_SORT_FIELD2'],
                            "ELEMENT_SORT_ORDER" => $arParams['ELEMENT_SORT_ORDER'],
                            "ELEMENT_SORT_ORDER2" => $arParams['ELEMENT_SORT_ORDER2'],
                            "FILTER_NAME" => $arParams['FILTER_NAME'],
                            "HIDE_NOT_AVAILABLE" => $arParams['HIDE_NOT_AVAILABLE'],
                            "HIDE_NOT_AVAILABLE_OFFERS" => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
                            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                            "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                            "INCLUDE_SUBSECTIONS" => $arParams['INCLUDE_SUBSECTIONS'],
                            "LINE_ELEMENT_COUNT" => $arParams['LINE_ELEMENT_COUNT'],
                            "MESSAGE_404" => $arParams['MESSAGE_404'],
                            "META_DESCRIPTION" => "-",
                            "META_KEYWORDS" => "-",
                            "OFFERS_SORT_FIELD" => $arParams['OFFERS_SORT_FIELD'],
                            "OFFERS_SORT_ORDER" => $arParams['OFFERS_SORT_ORDER'],
                            "OFFERS_SORT_FIELD2" => $arParams['OFFERS_SORT_FIELD2'],
                            "OFFERS_SORT_ORDER2" => $arParams['OFFERS_SORT_ORDER2'],
                            "OFFERS_LIMIT" => "0",
                            "OFFER_TREE_PROPS" => $arParams['OFFER_TREE_PROPS'],
                            "PAGER_BASE_LINK_ENABLE" => $arParams['PAGER_BASE_LINK_ENABLE'],
                            "PAGER_DESC_NUMBERING" => $arParams['PAGER_DESC_NUMBERING'],
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
                            "PAGER_SHOW_ALL" => $arParams['PAGER_SHOW_ALL'],
                            "PAGER_SHOW_ALWAYS" => $arParams['PAGER_SHOW_ALWAYS'],
                            "PAGER_TEMPLATE" => $arParams['PAGER_TEMPLATE'],
                            "PAGER_TITLE" => $arParams['PAGER_TITLE'],
                            "PARTIAL_PRODUCT_PROPERTIES" =>$arParams['PARTIAL_PRODUCT_PROPERTIES'],
                            "OFFERS_FIELD_CODE" => $arParams['OFFERS_FIELD_CODE'],
                            "OFFERS_PROPERTY_CODE" => $arParams['OFFERS_PROPERTY_CODE'],
                            "PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
                            "PRODUCT_ID" => $arResult['ID'],
                            "PRODUCT_ID_VARIABLE" => $arParams['PRODUCT_ID_VARIABLE'],
                            "PRODUCT_PROPERTIES" => $arParams['PRODUCT_PROPERTIES'],
                            "PRODUCT_PROPS_VARIABLE" => $arParams['PRODUCT_PROPS_VARIABLE'],
                            "PRODUCT_QUANTITY_VARIABLE" => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                            "SECTION_ID" => $arParams['SECTION_ID'],
                            "SECTION_URL" => $arParams['SECTION_URL'],
                            "STORES" => $arParams["STORES"],
                            "SEF_MODE" => "N",
                            "SET_BROWSER_TITLE" => "N",
                            "SET_LAST_MODIFIED" => $arParams['SET_LAST_MODIFIED'],
                            "SET_META_DESCRIPTION" => "N",
                            "SET_META_KEYWORDS" => "N",
                            "SET_STATUS_404" => "N",
                            "SET_TITLE" => "N",
                            "SHOW_404" => $arParams['SHOW_404'],
                            "SHOW_PRICE_COUNT" => $arParams['SHOW_PRICE_COUNT'],
                            "SHOW_TABS" => $arParams['SHOW_TABS'],
                            "USE_MAIN_ELEMENT_SECTION" => $arParams['USE_MAIN_ELEMENT_SECTION'],
                            "USE_PRICE_COUNT" => $arParams['USE_PRICE_COUNT'],
                            "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                            "COMPONENT_TEMPLATE" => ".default",
                            "SECTION_MODE" => $arParams['DETAIL_CROSSSELL_SECTION_MODE'],
                            "INTERRUPT_MODE" => "N",
                            "SECTION_TEMPLATE" => ".default",
                            "SHOW_SLIDER" => "N",
                            "PAGE_ELEMENT_COUNT" => $arParams['PAGE_ELEMENT_COUNT'],
                            "PRODUCT_DISPLAY_MODE" => $arParams['PRODUCT_DISPLAY_MODE'],
                            "ADD_PICT_PROP" => $arParams['ADD_PICT_PROP'],
                            "OFFER_ADD_PICT_PROP" => $arParams['OFFER_ADD_PICT_PROP'],
                            "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                            "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                            "SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
                            "SHOW_MAX_QUANTITY" => $arParams['SHOW_MAX_QUANTITY'],
                            "USE_VOTE_RATING" => $arParams['USE_VOTE_RATING'],
                            "COMPARE_PATH" => Config::get("COMPARE_PAGE"),
                            "COMPARE_NAME" => "CATALOG_COMPARE_LIST",
                            "USE_COMPARE_LIST" => $arParams['USE_COMPARE_LIST'],
                            "SECTION_NAME" => Loc::getMessage("SECT_NEWS_BLOCK_NAME"),
                            "LIST_PROPERTY_CODE" => $arParams['LIST_PROPERTY_CODE'],
                            "PRICE_CODE" => $arParams['PRICE_CODE'],
                            "MESS_RELATIVE_QUANTITY_NO" => $arParams['MESS_RELATIVE_QUANTITY_NO'],
                            "MESS_RELATIVE_QUANTITY_FEW" => $arParams["MESS_RELATIVE_QUANTITY_FEW"],
                            "MESS_RELATIVE_QUANTITY_MANY" => $arParams["MESS_RELATIVE_QUANTITY_MANY"],
                            "MESS_SHOW_MAX_QUANTITY" => $arParams["MESS_SHOW_MAX_QUANTITY"],
                            "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                            "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                            "STORE_FIELDS" => $arParams["FIELDS"],
                            "STORE_PROPERTIES" => $arParams["USER_FIELDS"],
                            "USE_STORE" => $arParams["USE_STORE"],
                            "BASE_QUANTITY" => $offer['CATALOG_QUANTITY'],
                            "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"]
                        ),
                        $component
                    );?>
                <?
                }
                ?>
            </div>
        </div>
    </main>

    <script>
        BX.message({
            BZD_PRODUCT_NAME: '<?=Loc::getMessage('CT_BZD_PRODUCT_NAME')?>',
            BZI_PRODUCT_NAME: '<?=Loc::getMessage('CT_BZD_PRODUCT_NAME')?>',
            BZI_PRODUCT_ADD_TO_BASKET: '<?=Loc::getMessage('CT_BZD_PRODUCT_ADD_TO_BASKET')?>',
            BZD_PRODUCT_ADD_TO_BASKET: '<?=Loc::getMessage('CT_BZD_PRODUCT_ADD_TO_BASKET')?>',
            BZD_PRODUCT_REMOVE_FORM_BASKET: '<?=Loc::getMessage('CT_BZD_PRODUCT_REMOVE_FORM_BASKET')?>',
        });
        var <?=$obName?> = new JCBlankZakazaDetail(
            <?=CUtil::PhpToJSObject($arResult)?>,
            <?=CUtil::PhpToJSObject($arParams)?>,
            <?=CUtil::PhpToJSObject($itemIds)?>
        );
    </script>
<? unset($arResult['actualItem'], $itemIds, $jsParams); ?>