<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
?>

<?php
if (empty($arResult["CATEGORIES"]) || !$arResult['CATEGORIES_ITEMS_EXISTS']) {
    ?>
    <div class="search-result-wrapper 4">
        <div class="card-header">
            <?= Loc::getMessage('EMPTY_SEARCH_RESULT'); ?>
        </div>
    </div>
    <script>
        document.querySelector('.title-search-result').classList.add('empty-search-result');
    </script>
    <?return;
}

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if($INPUT_ID == '')
    $INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

?>
<div class="search-result-wrapper 4">
    <?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
        <?if($category_id === "all") {continue;} ?>
        <div class="search-result-products">
            <?if(count($arResult["SEARCH"]) > count($arResult["ELEMENTS"])):?>
                <div class="card-header">
                    <h5 class="card-title"><?=Loc::getMessage('CT_BST_SEARCH_RESULT_TITLE_SECTIONS')?></h5>
                </div>

                <div class="card-body">
                    <ul class="media-list">
                        <?foreach($arCategory["ITEMS"] as $i => $arItem):?>
                            <?if(!isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):?>
                                <li class="media">
                                    <div class="media-body">
                                        <a href="<?=$arItem["URL"]?>">
                                            <span class="media-title font-weight-semibold"><?=$arItem["NAME"]?></span>
                                        </a>
                                    </div>
                                </li>
                            <?endif;?>
                        <?endforeach;?>
                    </ul>
                    <hr>
                </div>
            <?endif;?>
            <?if(isset($arResult["ELEMENTS"]) && !empty($arResult["ELEMENTS"])):?>
                <div class="card-header">
                    <h5 class="card-title"><?=Loc::getMessage('CT_BST_SEARCH_RESULT_TITLE_PRODUCT')?></h5>
                </div>

                <div class="card-body">
                    <ul class="media-list <?=$arParams["CATALOG_NOT_AVAILABLE"] === "Y" ? "basket-not-available" : "basket-available"?>">
                        <?foreach($arCategory["ITEMS"] as $i => $arItem):?>
                            <?if(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
                                $arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];?>
                                <li class="media">
                                    <div class="media-block-img">
                                        <img
                                                src="<?=$arElement["PICTURE"]["src"]?>"
                                                width="<?=$arElement["PICTURE"]["width"]?>"
                                                height="<?=$arElement["PICTURE"]["height"]?>"
                                        >
                                    </div>

                                    <div class="media-body">
                                        <div class="media-title font-weight-semibold">
                                            <a href="<?=$arItem["URL"];?>">
                                                <span class="media-title font-weight-semibold">
                                                    <?
                                                        //=$arItem["NAME"]
                                                        $ob = CIBlockElement::GetByID($arItem["ITEM_ID"]);
                                                        if($res = $ob->GetNext()) echo $res['NAME'];
                                                    ?>
                                                </span>
                                            </a>
                                        </div>
                                        <?if ($arParams["PROPERTY_ARTICLE"] && $arElement["PROPERTY_" . $arParams["PROPERTY_ARTICLE"] . "_VALUE"]):?>
                                            <span class="text-muted"><?=Loc::getMessage("B2B_SEARCH_ARTICLE")?> <?=$arElement["PROPERTY_" . $arParams["PROPERTY_ARTICLE"] . "_VALUE"]?></span>
                                        <?endif;?>
                                    </div>

                                    <div class="media-body-count">
                                        <?
                                            if($arResult["ELEMENTS"][$arItem["ITEM_ID"]]["CATALOG_QUANTITY"] > 200) {
                                                echo "> 200 шт.";
                                            }
                                            elseif($arResult["ELEMENTS"][$arItem["ITEM_ID"]]["CATALOG_QUANTITY"] > 100) {
                                                echo "> 100 шт.";
                                            }
                                            elseif($arResult["ELEMENTS"][$arItem["ITEM_ID"]]["CATALOG_QUANTITY"] > 50) {
                                                echo "> 50 шт.";
                                            }
                                            else {
                                                echo $arResult["ELEMENTS"][$arItem["ITEM_ID"]]["CATALOG_QUANTITY"] . " шт.";
                                            }
                                        ?>
                                        <!-- <?//=$arResult["ELEMENTS"][$arItem["ITEM_ID"]]["CATALOG_QUANTITY"];?> шт. -->
                                    </div>
                                    <?/*
                                    <div class="media-body-price">
                                        <?
                                        if ($arElement["CATALOG_AVAILABLE"] === "Y") {
                                            foreach($arElement["PRICES"] as $code=>$arPrice)
                                            {
                                                if ($arPrice["MIN_PRICE"] != "Y")
                                                    continue;

                                                if($arPrice["CAN_ACCESS"])
                                                {

                                                    if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                                                        <div class="search-title-result-item-price">
                                                        <span class="search-title-result-item-current-price">
                                                            <? if ($arElement["CATALOG_TYPE"] != "1"){
                                                                echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                            }?>
                                                            <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
                                                        </span>
                                                            <span class="search-title-result-item-old-price text-muted">
                                                            <? if ($arElement["CATALOG_TYPE"] != "1"){
                                                                echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                            }?>

                                                            <?=$arPrice["PRINT_VALUE"]?>
                                                        </span>
                                                        </div>
                                                    <?else:?>
                                                        <div class="search-title-result-item-price">
                                                        <span class="search-title-result-item-current-price">
                                                            <? if ($arElement["CATALOG_TYPE"] != "1"){
                                                                echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                            }?>
                                                            <?=$arResult["PRODUCT_PRIVATE_PRICE"][$arItem["ITEM_ID"]] ?: $arPrice["PRINT_VALUE"]?></span>
                                                        </div>
                                                    <?endif;
                                                }
                                                if ($arPrice["MIN_PRICE"] == "Y")
                                                    break;
                                            }
                                        }
                                        ?>
                                    </div>
                                    */?>
                                    <?
                                    //Новая логика вывода цены
                                    ?>
                                    <?/*
                                        <div class="media-body-price">
                                            <?
                                            if ($arElement["CATALOG_AVAILABLE"] === "Y") {
                                                $userID = $USER->GetID();
                                                $rsUser = CUser::GetByID($userID);
                                                $arUser = $rsUser->Fetch();

                                                $intProductID = $arElement["ID"];
                                                $arOpt = [];

                                                // Получение цены по логике OnGetOptimalPriceHandler
                                                $dbProductPrice = CPrice::GetListEx(
                                                    array(),
                                                    array("PRODUCT_ID" => $intProductID),
                                                    false,
                                                    false,
                                                    array("*")
                                                );

                                                while ($arProducPrice = $dbProductPrice->GetNext()) {
                                                    if ($arProducPrice['CATALOG_GROUP_CODE'] == 'Цена дилерского портала KZT') {
                                                        $arOpt = $arProducPrice;
                                                    }
                                                }

                                                if ($arUser["UF_APPLY_PRICE"] == 1) {
                                                    $arPricesNew = [];

                                                    $allProductPrices = \Bitrix\Catalog\PriceTable::getList([
                                                        "select" => ["*"],
                                                        "filter" => [
                                                            "=PRODUCT_ID" => $intProductID,
                                                        ],
                                                    ])->fetchAll();

                                                    foreach ($allProductPrices as $allProductPrice) {
                                                        $arPricesNew[] = $allProductPrice;
                                                    }

                                                    if ($arPricesNew[2]["PRICE"] != '') {
                                                        if ($arUser["UF_APPLY_PRICE_FOR"] == 8) {
                                                            $arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
                                                        } elseif ($arUser["UF_APPLY_PRICE_FOR"] == 9) {
                                                            $arBrandNames = [];
                                                            foreach ($arUser["UF_BRAND_FOR_PRICE"] as $brandID) {
                                                                $obBrand = CIBlockElement::GetByID($brandID);
                                                                if ($arBrand = $obBrand->GetNext()) {
                                                                    $arBrandNames[] = $arBrand['NAME'];
                                                                }
                                                            }
                                                            $res = CIBlockElement::GetProperty(7, $intProductID, "sort", "asc", array("CODE" => "BREND_ATTR_S"));
                                                            if ($ob = $res->GetNext()) {
                                                                $brand = $ob['VALUE'];
                                                            }
                                                            if (in_array($brand, $arBrandNames)) {
                                                                $arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
                                                            }
                                                        } elseif ($arUser["UF_APPLY_PRICE_FOR"] == 10) {
                                                            $arBrandNames = [];
                                                            foreach ($arUser["UF_BRAND_FOR_PRICE"] as $brandID) {
                                                                $obBrand = CIBlockElement::GetByID($brandID);
                                                                if ($arBrand = $obBrand->GetNext()) {
                                                                    $arBrandNames[] = $arBrand['NAME'];
                                                                }
                                                            }
                                                            $res = CIBlockElement::GetProperty(7, $intProductID, "sort", "asc", array("CODE" => "BREND_ATTR_S"));
                                                            if ($ob = $res->GetNext()) {
                                                                $brand = $ob['VALUE'];
                                                            }
                                                            if (!in_array($brand, $arBrandNames)) {
                                                                $arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
                                                            }
                                                        } else {
                                                            $arOpt['PRICE'] = $arPricesNew[2]["PRICE"];
                                                        }
                                                    }
                                                }

                                                // Вывод цены с учетом кастомной логики
                                                foreach ($arElement["PRICES"] as $code => $arPrice) {
                                                    if ($arPrice["MIN_PRICE"] != "Y")
                                                        continue;

                                                    if ($arPrice["CAN_ACCESS"]) {
                                                        $finalPrice = $arOpt['PRICE'] ?: $arPrice["VALUE"];
                                                        $finalDiscountPrice = $arOpt['PRICE'] ?: $arPrice["DISCOUNT_VALUE"];

                                                        if ($finalDiscountPrice < $finalPrice): ?>
                                                            <div class="search-title-result-item-price">
                                                                <span class="search-title-result-item-current-price">
                                                                    <? if ($arElement["CATALOG_TYPE"] != "1") {
                                                                        echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                                    } ?>
                                                                    <?= CurrencyFormat($finalDiscountPrice, $arPrice["CURRENCY"]) ?>
                                                                </span>
                                                                <span class="search-title-result-item-old-price text-muted">
                                                                    <? if ($arElement["CATALOG_TYPE"] != "1") {
                                                                        echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                                    } ?>
                                                                    <?= CurrencyFormat($finalPrice, $arPrice["CURRENCY"]) ?>
                                                                </span>
                                                            </div>
                                                        <? else: ?>
                                                            <div class="search-title-result-item-price">
                                                                <span class="search-title-result-item-current-price">
                                                                    <? if ($arElement["CATALOG_TYPE"] != "1") {
                                                                        echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                                    } ?>
                                                                    <?= CurrencyFormat($finalPrice, $arPrice["CURRENCY"]) ?>
                                                                </span>
                                                            </div>
                                                        <? endif;
                                                    }

                                                    if ($arPrice["MIN_PRICE"] == "Y")
                                                        break;
                                                }
                                            }
                                            ?>
                                        </div>
                                        */?>
                                        <div class="media-body-price">
    <?
    if ($arElement["CATALOG_AVAILABLE"] === "Y") {
        $userID = $USER->GetID();
        $rsUser = CUser::GetByID($userID);
        $arUser = $rsUser->Fetch();

        $intProductID = $arElement["ID"];
        $arOpt = [];

        // Получение цены "Цена дилерского портала KZT"
        $dbProductPrice = CPrice::GetListEx(
            array(),
            array("PRODUCT_ID" => $intProductID),
            false,
            false,
            array("*")
        );

        while ($arProducPrice = $dbProductPrice->GetNext()) {
            if ($arProducPrice['CATALOG_GROUP_CODE'] == 'Цена дилерского портала KZT') {
                $arOpt = $arProducPrice;
                break; // как только найдём нужную цену, выходим из цикла
            }
        }

        // Вывод цены "Цена дилерского портала KZT"
        if (!empty($arOpt['PRICE'])) {
            $finalPrice = $arOpt['PRICE'];
            $finalDiscountPrice = $arOpt['PRICE']; // если есть скидочная логика, её можно дополнить
            ?>
            <div class="search-title-result-item-price">
                <span class="search-title-result-item-current-price">
                    <? if ($arElement["CATALOG_TYPE"] != "1") {
                        echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                    } ?>
                    <?= CurrencyFormat($finalDiscountPrice, $arOpt["CURRENCY"]) ?>
                </span>
            </div>
            <?
        } else {
            // Если цены нет, можно вывести другую логику или сообщение
            echo Loc::getMessage("B2B_PRICE_NOT_FOUND");
        }
    }
    ?>
</div>


                                    <?
                                    //Новая логика вывода цены
                                    ?>

                                    <div class="media-body-basket">
                                        <div class="list-icons list-icons-extended">
                                            <?if($arElement["CATALOG_TYPE"] == "1" && !empty($arElement["PRICES"]) && $arElement["CATALOG_AVAILABLE"] === "Y"):?>
                                                <?if (is_array($arResult["PRODUCT_IN_BASKET"]) && in_array($arItem["ITEM_ID"], $arResult["PRODUCT_IN_BASKET"])):?>
                                                    <span>
                                                        <i class="ph-check-circle"></i>
                                                    </span>
                                                    <?else:?>
                                                    <span class="btn_search__product-add" data-product-id="<?=$arItem["ITEM_ID"]?>">
                                                            <i class="ph-shopping-cart-simple"></i>
                                                    </span>
                                                <?endif;?>
                                            <?endif;?>
                                        </div>
                                    </div>
                                </li>
                            <?endif;?>
                        <?endforeach;?>
                    </ul>
                </div>
            <?endif;?>
        </div>
        <?if ($allResult = $arResult["CATEGORIES"]["all"]["ITEMS"][0]):?>
            <div class="card-footer">
                <a href="<?=$allResult["URL"]?>" class="search-title-result__show-all"><?=$allResult["NAME"]?></a>
            </div>
        <?endif;?>
        </div>
    <?endforeach;?>
</div>

<script>
    BX.ready(function (){
        BX.message({
            "SEARCH_PRODUCT_ADD_TO_BASKET": '<?=Loc::getMessage("SEARCH_PRODUCT_ADD_TO_BASKET")?>',
            "SEARCH_PRODUCT_NAME": '<?=Loc::getMessage("SEARCH_PRODUCT_NAME")?>',
            "DEFAULT_MEASURE": '<?=Loc::getMessage("SEARCH_DEFAULT_MEASURE")?>',
        });

        const searchResultBlock = document.querySelector('.title-search-result'),
            inpudId = '<?echo $INPUT_ID?>',
            searchInput = document.getElementById(inpudId),
            iconSearch = document.querySelector('.search__submit i'),
            btnAddToBasket = searchResultBlock.querySelectorAll('.basket-available .btn_search__product-add'),
            contentInner = document.querySelector('.content-inner') || document.querySelector('.content-wrapper');

        var measureList = <?=CUtil::PhpToJSObject($arResult["MEASURE"])?>,
            ratioList = <?=CUtil::PhpToJSObject($arResult["RATIO"])?>,
            productList = <?=CUtil::PhpToJSObject($arResult["ELEMENTS"])?>;

        if (contentInner) {
            contentInner.addEventListener('scroll' , function (e) {
                var html = contentInner;
                var body = document.body;
                var scrollTop = html.scrollTop || body && body.scrollTop || 0;
                scrollTop -= html.clientTop;
                searchResultBlock.style.transform = 'translate3d(0px, ' + (-15-scrollTop) + 'px, 0px)';
            });
        }

        if (btnAddToBasket) {
            for (let btn of btnAddToBasket) {
                btn.addEventListener('click', function () {
                    let icon = this.querySelector('i');
                    icon.className = 'spinner-grow';

                    const productId = this.getAttribute('data-product-id');

                    BX.ajax.runAction('sotbit:b2bcabinet.basket.addProductToBasket', {
                        data: {
                            arFields: {
                                'PRODUCT_ID': productId,
                                'QUANTITY': 1,
                            }
                        },
                    }).then(
                        function(response) {
                            btn.insertAdjacentHTML('beforebegin', '<span><i class="ph-check-circle"></i></span>');
                            btn.parentNode.removeChild(btn);
                            BX.onCustomEvent('B2BNotification',[
                                BX.message('SEARCH_PRODUCT_NAME') + ': ' + productList[productId].NAME + "<br>" +
                                BX.message('SEARCH_PRODUCT_ADD_TO_BASKET') + " " + ratioList[productId].RATIO + " " + (measureList[productList[productId].MEASURE] ? measureList[productList[productId].MEASURE].SYMBOL_RUS : BX.message('DEFAULT_MEASURE')),
                                'success'
                            ]);
                            BX.onCustomEvent('OnBasketChange');
                        },
                        function(error) {
                            let errors = [];
                            for (var i = 0; i<error.errors.length; i++) {
                                errors.push(error.errors[i].message);
                            }

                            BX.onCustomEvent('B2BNotification',[
                                errors.join('<br>'),
                                'alert'
                            ]);
                            icon.className = 'ph-shopping-cart-simple';
                        },
                    )
                });
            }
        }


        function replaceIcon() {
            if (iconSearch.classList.contains("spinner")) {
                iconSearch.className = "ph-magnifying-glass";
            }
        }

        let observer = new MutationObserver(mutationRecords => {
            replaceIcon();
        });

        observer.observe(searchResultBlock, {
            attributes: true,
            childList: true,
            subtree: true,
            characterDataOldValue: true
        });

        document.onclick = function(event){
            const target = event.target;
            if (target === searchInput) {
                return;
            }
            const its_search = target === searchResultBlock || searchResultBlock.contains(target);
            if (!its_search) {
                searchResultBlock.style.display = 'none';
            };
        };
    });
</script>