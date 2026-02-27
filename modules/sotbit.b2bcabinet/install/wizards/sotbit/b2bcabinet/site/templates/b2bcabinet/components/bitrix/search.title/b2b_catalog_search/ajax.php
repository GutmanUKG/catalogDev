<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
?>

<script>
    var iconSearchB2b = document.querySelector('.b2b-catalog-search__submit i');
    iconSearchB2b.className = 'icon-search4';
</script>

<?php
if (empty($arResult["CATEGORIES"]) || !$arResult['CATEGORIES_ITEMS_EXISTS']) {
    ?>
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
                                <div class="mr-3  media-block-img">
                                    <span>
                                        <img
                                                src="<?=$arElement["PICTURE"]["src"]?>"
                                                width="<?=$arElement["PICTURE"]["width"]?>"
                                                height="<?=$arElement["PICTURE"]["height"]?>"
                                        >
                                    </span>
                                </div>

                                <div class="media-body">
                                    <div class="media-title font-weight-semibold">
                                        <a href="<?=$arItem["URL"];?>">
                                            <span class="media-title font-weight-semibold"><?=$arItem["NAME"]?></span>
                                        </a>
                                    </div>
                                    <?if ($arParams["PROPERTY_ARTICLE"] && $arElement["PROPERTY_" . $arParams["PROPERTY_ARTICLE"] . "_VALUE"]):?>
                                        <span class="text-muted"><?=Loc::getMessage("B2B_SEARCH_ARTICLE")?> <?=$arElement["PROPERTY_" . $arParams["PROPERTY_ARTICLE"] . "_VALUE"]?></span>
                                    <?endif;?>
                                </div>

                                <div class="align-self-top ml-5 mr-3">
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
                                                         <?= $arResult['RATIO'][$arElement["ID"]]["RATIO"] ?  CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arResult['RATIO'][$arElement["ID"]]["RATIO"], $arPrice["CURRENCY"]) : $arPrice["PRINT_DISCOUNT_VALUE"] ?>
                                                    </span>
                                                        <span class="search-title-result-item-old-price text-muted">
                                                        <? if ($arElement["CATALOG_TYPE"] != "1"){
                                                            echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                        }?>

                                                        <?=$arResult['RATIO'][$arElement["ID"]]["RATIO"] ? CCurrencyLang::CurrencyFormat($arPrice["VALUE"] * $arResult['RATIO'][$arElement["ID"]]["RATIO"], $arPrice["CURRENCY"])  : $arPrice["PRINT_VALUE"]?>
                                                    </span>
                                                    </div>
                                                <?else:?>
                                                    <div class="search-title-result-item-price">
                                                    <span class="search-title-result-item-current-price">
                                                        <? if ($arElement["CATALOG_TYPE"] != "1"){
                                                            echo Loc::getMessage("B2B_SEARCH_OFFERS_PRICE");
                                                        }?>
                                                        <?=$arResult["PRODUCT_PRIVATE_PRICE"][$arItem["ITEM_ID"]] ?: ($arResult['RATIO'][$arElement["ID"]]["RATIO"] ? CCurrencyLang::CurrencyFormat($arPrice["VALUE"] * $arResult['RATIO'][$arElement["ID"]]["RATIO"], $arPrice["CURRENCY"]) : $arPrice["PRINT_VALUE"])?></span>
                                                    </div>
                                                <?endif;
                                            }
                                            if ($arPrice["MIN_PRICE"] == "Y")
                                                break;
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="align-self-top ml-3">
                                    <div class="list-icons list-icons-extended">
                                        <?if($arElement["CATALOG_TYPE"] == "1" && !empty($arElement["PRICES"]) && $arElement["CATALOG_AVAILABLE"] === "Y"):?>
                                            <?if (is_array($arResult["PRODUCT_IN_BASKET"]) && in_array($arItem["ITEM_ID"], $arResult["PRODUCT_IN_BASKET"])):?>
                                                <span>
                                                     <i class="icon-checkmark3 item-in-cart mr-1"></i>
                                                </span>
                                            <?else:?>
                                                <span class="btn_search__product-add" data-product-id="<?=$arItem["ITEM_ID"]?>">
                                                    <i class="icon-cart5"></i>
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
        <div class="m-3">
            <a href="<?=$allResult["URL"]?>" class="search-title-result__show-all"><?=$allResult["NAME"]?></a>
        </div>
    <?endif;?>
<?endforeach;?>

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
            iconSearch = document.querySelector('.b2b-catalog-search__submit i'),
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
                    icon.className = 'icon-spinner2 spinner';

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
                            btn.insertAdjacentHTML('beforebegin', '<i class="icon-checkmark3 item-in-cart mr-1"></i>');
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
                            icon.className = 'icon-cart5';
                        },
                    )
                });
            }
        }


        function replaceIcon() {
            if (iconSearch.classList.contains("spinner")) {
                iconSearch.className = "icon-search4";
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