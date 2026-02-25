<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application;

$this->setFrameMode(true);
CJSCore::Init(array("fx"));

$request = Application::getInstance()->getContext()->getRequest();

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

global $USER;
$userID = $USER->GetID();
$rsUser = CUser::GetByID($userID);
$arUser = $rsUser->Fetch();

$this->SetViewTarget("stickers");
if ($arResult['PROPERTIES'] && $arParams['LABEL_PROP']): ?>
    <div class="stickers">
    <?foreach ($arParams['LABEL_PROP'] as $label) {
        if ($arResult['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
            ?>
            <span class="badge bg-opacity-20 <?= $arResult['PROPERTIES'][$label]['HINT'] ?>">
                <?= $arResult['PROPERTIES'][$label]['NAME'] ?>
            </span>
            <?
        }
    }?>
    </div>
<?endif;
$this->EndViewTarget();
?>
    <main class="blank-zakaza-detail <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'blank-zakaza-detail-not_available' : '' ?>">
        <? if ($request->get("IFRAME") === "Y"): ?>
            <div class="blank-zakaza-detail-header">
                <h1 class="blank-zakaza-detail__title" id="<?= $itemIds['TITLE'] ?>"><?= $name ?></h1>
                <div class="stickers">
                <?foreach ($arParams['LABEL_PROP'] as $label) {
                    if ($arResult['PROPERTIES'][$label]["VALUE_XML_ID"] == "true") {
                        ?>
                        <span class="badge bg-opacity-20 <?= $arResult['PROPERTIES'][$label]['HINT'] ?>">
                            <?= $arResult['PROPERTIES'][$label]['NAME'] ?>
                        </span>
                        <?
                    }
                }?>
                </div>
            </div>
        <? endif; ?>
        <div class="blank-zakaza-detail__wrapper">
            <aside class="blank-zakaza-detail__aside card">
                <div class="blank-zakaza-detail__info">
                    <div id="blank-zakaza-detail__slider" class="carousel slide" data-bs-ride="carousel" data-bs-touch="true" data-bs-interval="60000">
                        <?/*<div class="carousel-indicators">
                            <?if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?>
                                <button type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 0"></button>
                                <?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $pict):?>
                                    <?$pictSrc = CFile::GetPath($pict);?>
                                    <?if($pictSrc != $arResult['DETAIL_PICTURE']["SRC"]):?>
                                        <button type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide-to="<?=$key+1?>"aria-label="Slide <?=$key+1?>"></button>
                                    <?endif;?>
                                <?endforeach;?>
                            <?endif;?>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="blank-zakaza-detail__image-wrapper">
                                    <a class="lightbox-toggle" data-gallery="product" href="<?= $arResult['DETAIL_PICTURE']["SRC"] ?>" data-bs-toggle="lightbox">
                                        <img class="blank-zakaza-detail__image" src="<?//= $arResult['PICTURE'] ?><?= $arResult['DETAIL_PICTURE']["SRC"] ?>" title="<?= $title ?>" alt="<?= $alt ?>">
                                    </a>
                                </div>
                            </div>
                            <?if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?>
                                <?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $pict):?>
                                    <?$pictSrc = CFile::GetPath($pict);?>
                                    <?if($pictSrc != $arResult['DETAIL_PICTURE']["SRC"]):?>
                                        <div class="carousel-item">
                                            <div class="blank-zakaza-detail__image-wrapper">
                                                <a class="lightbox-toggle" data-gallery="product" href="<?= $pictSrc ?>" data-bs-toggle="lightbox">
                                                    <img class="blank-zakaza-detail__image" src="<?= $pictSrc ?>">
                                                </a>
                                            </div>
                                        </div>
                                    <?endif;?>
                                <?endforeach;?>
                            <?endif;?>
                        </div>*/?>
                        <div class="carousel-indicators">
                            <?if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?>
                                <button type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 0"></button>
                                <?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $pict):?>
                                    <?$pictSrc = CFile::GetPath($pict);?>
                                    <?if($pictSrc != $arResult['DETAIL_PICTURE']["SRC"]):?>
                                        <button type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide-to="<?=$key+1?>"aria-label="Slide <?=$key+1?>"></button>
                                    <?endif;?>
                                <?endforeach;?>
                            <?endif;?>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="blank-zakaza-detail__image-wrapper">
                                    <a class="lightbox-toggle" data-fancybox="product" href="<?= $arResult['DETAIL_PICTURE']["SRC"] ?>">
                                        <img class="blank-zakaza-detail__image" src="<?//= $arResult['PICTURE'] ?><?= $arResult['DETAIL_PICTURE']["SRC"] ?>" title="<?= $title ?>" alt="<?= $alt ?>">
                                    </a>
                                </div>
                            </div>
                            <?if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?>
                                <?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $pict):?>
                                    <?$pictSrc = CFile::GetPath($pict);?>
                                    <?if($pictSrc != $arResult['DETAIL_PICTURE']["SRC"]):?>
                                        <div class="carousel-item">
                                            <div class="blank-zakaza-detail__image-wrapper">
                                                <a class="lightbox-toggle" data-fancybox="product" href="<?= $pictSrc ?>">
                                                    <img class="blank-zakaza-detail__image" src="<?= $pictSrc ?>">
                                                </a>
                                            </div>
                                        </div>
                                    <?endif;?>
                                <?endforeach;?>
                            <?endif;?>
                        </div>
                        <?if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#blank-zakaza-detail__slider" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        <?endif;?>
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
                                            "#CATALOG_MEASURE_NAME#" => $arParams['SHOW_MAX_QUANTITY'] !== 'M' ? $arResult['CATALOG_MEASURE_NAME'] : ''
                                        ]); ?>
                                </span>
                                <? $arResult['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                    "sotbit:catalog.store.quantity",
                                    "b2bcabinet",
                                    array(
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_TYPE" => "A",
                                        "COMPONENT_TEMPLATE" => "b2bcabinet",
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
                                        "#CATALOG_MEASURE_NAME#" => $arParams['SHOW_MAX_QUANTITY'] !== 'M' ? $arResult['CATALOG_MEASURE_NAME'] : ''
                                    ]); ?>
                            </div>
                            <?
                        } ?>
                        <div class="blank-zakaza-detail__info-item">
                            <span>В пути</span>
                            <span>
                                <?
                                    $arFilter = Array("PRODUCT_ID"=>$arResult['ID'],"STORE_ID"=>51);
                                    $res = CCatalogStoreProduct::GetList(Array(),$arFilter,false,false,Array());
                                    if ($arRes = $res->GetNext()){
                                        echo $arRes["AMOUNT"];
                                    }
                                ?>
                            </span>
                        </div>
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
                                    <?/*
                                    <?if($priceCode != "Цена  OFFLINE KZT"):?>
                                        <li class="bzd-prices__item 1">

                                            <span class="bzd-prices__item-name" title="<?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE'] ?>"><?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE'] ?></span>
                                            <span class="product__property--discount-price"
                                                id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                                <span>
                                                <?= $priceCode == "PRIVATE_PRICE" ?
                                                    "" :
                                                    $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT_WHITHOUT_DISCONT'] ?>
                                                </span>
                                            </span>
                                            <span class="bzd-prices__item-value" id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                                <?
                                                    if($arUser["UF_APPLY_PRICE"] == 1) {
                                                        if($arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] != '') {
                                                            if($arUser["UF_APPLY_PRICE_FOR"] == 8) {
                                                                echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                            }
                                                            elseif($arUser["UF_APPLY_PRICE_FOR"] == 9) {
                                                                $arBrandNames = [];
                                                                foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                                    if($arBrand = $obBrand->GetNext()) {
                                                                        $arBrandNames[] = $arBrand['NAME'];
                                                                    }
                                                                }
                                                                if(in_array($arResult["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                                    echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                                else {
                                                                    echo $priceCode == "PRIVATE_PRICE" ?
                                                            \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                            }
                                                            elseif($arUser["UF_APPLY_PRICE_FOR"] == 10) {
                                                                $arBrandNames = [];
                                                                foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                                                    $obBrand = CIBlockElement::GetByID($brandID);
                                                                    if($arBrand = $obBrand->GetNext()) {
                                                                        $arBrandNames[] = $arBrand['NAME'];
                                                                    }
                                                                }
                                                                if(in_array($arResult["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                                                    echo $priceCode == "PRIVATE_PRICE" ?
                                                                    \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                                    $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                                else {
                                                                    echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                                }
                                                            }
                                                            else {
                                                                echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                            }
                                                        }
                                                        else {
                                                            echo $priceCode == "PRIVATE_PRICE" ?
                                                            \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                        }
                                                    }
                                                    else {
                                                        echo $priceCode == "PRIVATE_PRICE" ?
                                                            \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                                                    }
                                                ?>
                                            </span>
                                        </li>
                                    <?endif;?>
                                   */ ?>
                                    <!-- Новый вывод -->
                                    <?if($priceCode != "Цена  OFFLINE KZT"):?>
    <li class="bzd-prices__item 1">

        <!-- Вывод названия типа цены -->
        <span class="bzd-prices__item-name" title="<?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE'] ?>">
<!--           --><?//  if($arUser["UF_APPLY_PRICE_FOR"] == 8) :?>
<!--            Цена  OFFLINE KZT-->
<!--           --><?//=$priceCode?>
<!--                --><?//else:?>
<!--                --><?//= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE'] ?>
<!--            --><?//endif?>
            <?if($arUser["UF_APPLY_PRICE"] == 1):?>
                Цена  OFFLINE KZT
            <?else:?>
            <?=$priceCode?>
            <?endif;?>
        </span>

        <span class="product__property--discount-price" id="<?= $itemIds['PRICES'][$priceCode] ?>">
            <span>
            <?= $priceCode == "PRIVATE_PRICE" ?
                "" :
                $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT_WHITHOUT_DISCONT'] ?>
            </span>
        </span>

        <!-- Логика вывода цены в зависимости от пользователя и других условий -->
        <span class="bzd-prices__item-value" id="<?= $itemIds['PRICES'][$priceCode] ?>">
            <?php
                if($arUser["UF_APPLY_PRICE"] == 1) {
                    // Проверка наличия "Цена OFFLINE KZT"
                    if($arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] != '') {

                        // Если UF_APPLY_PRICE_FOR == 8, выводим "Цена OFFLINE KZT"
                        if($arUser["UF_APPLY_PRICE_FOR"] == 8) {
                            echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                        }
                        // Если UF_APPLY_PRICE_FOR == 9, проверяем бренды
                        elseif($arUser["UF_APPLY_PRICE_FOR"] == 9) {
                            $arBrandNames = [];
                            foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                $obBrand = CIBlockElement::GetByID($brandID);
                                if($arBrand = $obBrand->GetNext()) {
                                    $arBrandNames[] = $arBrand['NAME'];
                                }
                            }

                            if(in_array($arResult["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                            } else {
                                echo $priceCode == "PRIVATE_PRICE" ?
                                    \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                    $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                            }
                        }
                        // Если UF_APPLY_PRICE_FOR == 10, проверяем бренды аналогично предыдущему случаю
                        elseif($arUser["UF_APPLY_PRICE_FOR"] == 10) {
                            $arBrandNames = [];
                            foreach ($arUser["UF_BRAND_FOR_PRICE"] as $key => $brandID) {
                                $obBrand = CIBlockElement::GetByID($brandID);
                                if($arBrand = $obBrand->GetNext()) {
                                    $arBrandNames[] = $arBrand['NAME'];
                                }
                            }

                            if(in_array($arResult["PROPERTIES"]["BREND_ATTR_S"]["VALUE"], $arBrandNames)) {
                                echo $priceCode == "PRIVATE_PRICE" ?
                                    \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                    $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                            } else {
                                echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                            }
                        } else {
                            echo $arResult['PRINT_PRICES']["Цена  OFFLINE KZT"][$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                        }
                    } else {
                        echo $priceCode == "PRIVATE_PRICE" ?
                            \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                            $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                    }
                } else {
                    echo $priceCode == "PRIVATE_PRICE" ?
                        \SotbitPrivatePriceMain::setPlaceholder($arResult[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                        $price[$arResult['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'];
                }
            ?>
        </span>
    </li>
<?endif;?>

                                     <!-- Новый вывод -->
                                <? endforeach; ?>
                            </ul>





                        </div>
                        <?
                        $itemIds['QUANTITY'] = $mainId . '_quantity';
                        $itemIds['QUANTITY_DECREMENT'] = $mainId . '_quantity-decrement';
                        $itemIds['QUANTITY_VALUE'] = $mainId . '_quantity-value';
                        $itemIds['QUANTITY_INCREMENT'] = $mainId . '_quantity-increment';
                        ?>
                        <?if($arResult['ACTUAL_QUANTITY'] < 1):?>
                        <button class="add-basket-detail btn btn-primary btn-small" data-id="<?=$itemIds['QUANTITY']?>"><i class="ph-shopping-cart-simple me-2"></i>В корзину</button>
                        <?endif;?>


                        <?global $USER;
                        if($USER->isAdmin()):
                        ?>
                            <?if($arResult['HAS_SECOND']):?>
                            <?/*Тут нужно по нажатию выводить примерно такую же всплывашку но со списком товаров уценки*/?>
                            <a href="" class="btn btn-danger btn-small mt-2" data-second="<?=$arResult['SECOND_ITEM']['ID']?>">Уценка</a>
                            <?endif;?>
                        <?endif;?>

                        <?if(!empty($arResult['PROPERTIES']['PDF_FILES']['VALUE'])):?>
                        <ul class="files_list">
                            <?foreach ($arResult['PROPERTIES']['PDF_FILES']['VALUE'] as $FILE):?>
                            <?$f = CFile::GetFileArray($FILE);?>

                            <li>
                                <a download="<?=$f['SRC']?>" href="<?=$f['SRC']?>"><?=$f['ORIGINAL_NAME']?></a>
                            </li>
                            <?endforeach;?>
                        </ul>
                        <?endif;?>

                        <div class="blank-zakaza-detail__info-item bzd-quantity"  <?if($arResult['ACTUAL_QUANTITY'] < 1):?>style="display: none" <?endif;?>>

                            <div class="bootstrap-touchspin bootstrap-touchspin-lg input-group" id="<?= $itemIds['QUANTITY'] ?>">
                                <span class="input-group-btn input-group-prepend">
                                    <button class="btn bootstrap-touchspin-down"
                                            type="button"
                                            id="<?= $itemIds['QUANTITY_DECREMENT'] ?>"
                                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                            <i class="ph-minus"></i>
                                    </button>
                                </span>
                                <input class="touchspin-basic form-control"
                                       type="text"
                                       value="<?= $arResult['ACTUAL_QUANTITY'] ?>"
                                       id="<?= $itemIds['QUANTITY_VALUE'] ?>"
                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                    <?= $USER->IsAuthorized() ? "" : "disabled" ?>
                                >
                                <span class="input-group-btn input-group-append">
                                    <button class="btn bootstrap-touchspin-up"
                                            type="button"
                                            id="<?= $itemIds['QUANTITY_INCREMENT'] ?>"
                                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                            <i class="ph-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        


                    <? endif; ?>
                </div>
            </aside>
            <div class="blank-zakaza-detail__main">

            <? if (count($arResult['OFFERS'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['OFFERS_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_OFFERS") ?></h2>
                        <div class="bzd-offers__wrapper">
                            <table class="bzd-offers">
                                <thead class="bzd-offers__header">
                                <tr class="bzd-offers__header-row">
                                    <th colspan="2" class="bzd-offers__header-cell"><?= Loc::getMessage('CT_BZD_OFFERS_NAME') ?></th>
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
                                    <tr class="bzd-offers__offer" id="<?= $itemIds['OFFERS'][$offer['ID']]['ID'] ?>">
                                        <td class="bzd-offers__offer-cell bzd-offers__offer-cell--image">
                                            <div class="bzd-offers__offer-image-wrapper">
                                                <img class="bzd-offers__offer-image rounded" src="<?= $offer['PICTURE'] ?>"
                                                    height="74" width="74">
                                            </div>
                                        </td>
                                        <td class="bzd-offers__offer-cell bzd-offers__offer-cell--name">
                                            <div class="bzd-offers__offer-wraper">
                                                <p class="bzd-offers__offer-name" title="<?= $offer['NAME'] ?>"><?= $offer['NAME'] ?></p>
                                                <? if ($arResult['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]): ?>
                                                    <div class="bzd-offers__offer-artnumber">
                                                        <?= $offer['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['NAME'] ?>
                                                        <?= $offer['PROPERTIES'][$arResult['ORIGINAL_PARAMETERS']['DETAIL_MAIN_ARTICLE_PROPERTY_OFFERS']]['VALUE'] ?>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <? if ($arParams['SHOW_MAX_QUANTITY'] !== "N") { ?>
                                            <td class="bzd-offers__offer-cell">
                                                <div class="product__quant" id="prod_qu_<?= $offer['ID'] ?>">
                                                    <?
                                                    $offer['CATALOG_QUANTITY'] = $APPLICATION->IncludeComponent(
                                                        "sotbit:catalog.store.quantity",
                                                        "b2bcabinet",
                                                        array(
                                                            "CACHE_TIME" => "36000000",
                                                            "CACHE_TYPE" => "A",
                                                            "COMPONENT_TEMPLATE" => "b2bcabinet",
                                                            "ELEMENT_ID" => $offer["ID"],
                                                            "CONTAINER_ID" => "prod_qu_" . $offer['ID'],
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
                                                        <span class="bzd-prices__item-name">

                                                            <?= $arResult['CAT_PRICES'][$priceCode]['TITLE'] ? $arResult['CAT_PRICES'][$priceCode]['TITLE'] : $arResult['CAT_PRICES'][$priceCode]['CODE']?>
                                                        </span>
                                                        <span class="bzd-prices__item-value-wrapper"
                                                              id="<?= $itemIds['OFFERS'][$offer['ID']]['PRICES'][$priceCode] ?>">
                                                            <span class="bzd-prices__item-value">
                                                                <?= $priceCode == "PRIVATE_PRICE" ?
                                                                \SotbitPrivatePriceMain::setPlaceholder($offer[$arResult['PRINT_PRICES']['PRIVATE_PRICE']["SOTBIT_PRIVATE_PRICE_PRODUCT_UNIQUE_KEY"]], '') :
                                                                $price[$offer['ITEM_QUANTITY_RANGE_SELECTED']]['PRINT'] ?>
                                                            </span>
                                                            <span class="product__property--discount-price"
                                                                id="<?= $itemIds['PRICES'][$priceCode] ?>">
                                                                <? if (round((float)$offer["PRICES"][$priceCode]['VALUE_VAT'], 2) !== round((float)$offer["PRICES"][$priceCode]['DISCOUNT_VALUE_VAT'], 2)): ?>
                                                                    <span><?= $priceCode == "PRIVATE_PRICE" ?
                                                                        "" :
                                                                        $offer["PRICES"][$priceCode]['PRINT_VALUE_NOVAT'] ?>
                                                                    </span>
                                                                <? endif; ?>
                                                            </span>
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
                                            <div class="bootstrap-touchspin input-group"
                                                 id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY'] ?>">
                                                <span class="input-group-btn input-group-prepend">
                                                    <button class="btn bootstrap-touchspin-down"
                                                            type="button"
                                                            id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_DECREMENT'] ?>"
                                                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                                            <i class="ph-minus"></i>
                                                    </button>
                                                </span>
                                                <input class="touchspin-basic form-control fs-xs"
                                                       type="text"
                                                       value="<?= $offer['ACTUAL_QUANTITY'] ?>"
                                                       id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_VALUE'] ?>"
                                                    <?= $arParams["CATALOG_NOT_AVAILABLE"] == "Y" ? 'readonly' : '' ?>
                                                    <?= $USER->IsAuthorized() ? "" : "disabled" ?>
                                                >
                                                <span class="input-group-btn input-group-append">
                                                    <button class="btn bootstrap-touchspin-up"
                                                            type="button"
                                                            id="<?= $itemIds['OFFERS'][$offer['ID']]['QUANTITY_INCREMENT'] ?>"
                                                            <?= $USER->IsAuthorized() ? "" : "disabled" ?>>
                                                            <i class="ph-plus"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <? endif; ?>

                <? if (!empty($arResult['DETAIL_TEXT'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                            id="<?= $itemIds['DESCRIPTION_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_DESCRIPTION") ?></h2>
                        <?= $arResult['DETAIL_TEXT'] ?>
                    </section>
                <? endif; ?>

                <? if (count($arResult['DISPLAY_PROPERTIES'])): ?>
                    <section class="blank-zakaza-detail__main-section card card-body"
                             id="<?= $itemIds['PROPERTIES_SECTION'] ?>">
                        <h2 class="card-title"><?= Loc::getMessage("CT_BZD_TAB_PROPERTIES") ?></h2>
                        <div class="bzd-props">
                            <table class="bzd-props__table">
                                <? foreach ($arResult['DISPLAY_PROPERTIES'] as $property): ?>
                                <?if($property['NAME'] != 'Файлы'):?>
                                    <tr class="bzd-props__table-row">
                                        <td class="bzd-props__table-col">
                                            <?= $property['NAME'] ?>
                                        </td>
                                        <td class="bzd-props__table-col">
                                            <?= (is_array($property['DISPLAY_VALUE'])
                                                ? implode(', ', $property['DISPLAY_VALUE'])
                                                : html_entity_decode($property['DISPLAY_VALUE']))
                                            ?>
                                        </td>
                                    </tr>
                                <?endif;?>
                                <? endforeach; ?>
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
                                        <a class="lightbox-toggle" data-gallery="product" href="<?= $image['BIG_IMAGE']['src'] ?>" data-bs-toggle="lightbox">
                                            <img class="bzd-gallery__image mw-100 mh-100 h-auto w-auto m-auto top-0 end-0 bottom-0 start-0 img-fluid" src="<?= $image['SMALL_IMAGE']['src'] ?>" title="<?= $name ?>" alt="<?= $name ?>">
                                        </a>
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

        // App.initLightbox();
    </script>
<? unset($arResult['actualItem'], $itemIds, $jsParams); ?>

<!-- <script>
    $(document).ready(function(){
        $('.blank-zakaza-detail__image-wrapper .lightbox-toggle').click(function(){
            var lightboxCarousel = $(document).find('.lightbox-carousel'),
                lightboxCarouselId = $(document).find('.lightbox-carousel').attr('id');
            $('<div class="carousel-indicators"></div>').prependTo(lightboxCarousel);
            $(document).find('.carousel-indicators').html('<?/*if($arResult['PROPERTIES']['MORE_PHOTO']['VALUE']):?><button type="button" data-bs-target="'+lightboxCarouselId+'" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button><?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $key => $pict):?><button type="button" data-bs-target="'+lightboxCarouselId+'" data-bs-slide-to="<?=$key+1?>" aria-label="Slide <?=$key+1?>"></button><?endforeach;?><?endif;*/?>');
        });
    });
</script> -->

<script>
    $(document).ready(function(){
        $('[data-fancybox="product"]').fancybox({
            buttons : [
                'close'
            ],
            thumbs : {
                autoStart : true
            },
            transitionEffect: "slide",
            transitionDuration: 750,
            idleTime: 0
        });

        $('.add-basket-detail').on('click', function () {
            const $this = $(this);
            const id = $this.data('id');

            // Скрываем только текущую кнопку
            $this.hide();

            // Показываем соответствующий блок количества
           $('.bzd-quantity').show();

            // Имитация клика по кнопке инкремента
            $(`#${id}-increment`).click();
        });

    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const counter = document.querySelector('.bootstrap-touchspin');
    const input = counter?.querySelector('.touchspin-basic');
    const btnPlus = counter?.querySelector('.bootstrap-touchspin-up');
    const btnMinus = counter?.querySelector('.bootstrap-touchspin-down');
    const wrapper = counter?.closest('.bzd-quantity');
    const btnAdd = document.querySelector(`button[data-id="${counter?.id}"]`);

    const updateUI = () => {
        const quantity = parseInt(input?.value) || 0;
        if (quantity <= 0) {
            wrapper?.style.setProperty('display', 'none');
            btnAdd?.style.setProperty('display', 'inline-block');
        } else {
            wrapper?.style.setProperty('display', 'block');
            btnAdd?.style.setProperty('display', 'none');
        }
    };

    if (btnPlus && btnMinus && input) {
        btnPlus.addEventListener('click', () => setTimeout(updateUI, 100));
        btnMinus.addEventListener('click', () => setTimeout(updateUI, 100));
        input.addEventListener('input', updateUI);
    }

    updateUI(); // инициализация на загрузке
});
</script>

