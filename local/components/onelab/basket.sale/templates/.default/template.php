<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/function.php");
?>
<?if(!empty($arResult['ITEMS'])):?>
<div class="top">
    <div class="item"></div>
    <div class="item">
        Наименование
    </div>
    <div class="item">
        Цена
    </div>
    <div class="item">
        Количество
    </div>
    <div class="item"></div>
</div>
<div class="items basket_items">
    <?$total = 0?>
    <?php
    foreach ($arResult['ITEMS'] as $arItem):
        ?>
        <div class="item" id="<?=$arItem['ID']?>">
            <div class="image">
                <?
                $imgPath = CFile::GetPath($arItem['PREVIEW_PICTURE'])

                ?>
                <? if(!empty($imgPath)):?>
                <img src="<?=$imgPath?>" alt="<?=$arItem['NAME']?>">
                <?endif;?>
            </div>
            <div class="name">
                <?=$arItem['NAME']?> <br>
                Бренд : <b><?=$arItem['PROPERTY_BRAND_VALUE']?></b> <br>
                Артикул: <b><?=$arItem['PROPERTY_ART_VALUE']?></b>
            </div>
            <div class="price">
                <?
                if($arItem['QUANTITY'] > 1){
                    echo formatPrice($arItem['PRICE'] * $arItem['QUANTITY']);
                    echo '<br>';
                    echo 'Цена за шт <br>' . formatPrice($arItem['PRICE']) ;
                }else{
                    echo formatPrice($arItem['PRICE']);
                }
                ?>
                <?
                if($arItem['QUANTITY'] > 1){
                    $priceValue =  $arItem['PRICE'] * $arItem['QUANTITY'];
                }else{
                    $priceValue = intval(str_replace(' ', '', trim($arItem['PRICE'])));
                }

                $total += $priceValue;
                ?>
            </div>
            <div>
                <div class="quantity">
                    <button class="dicrement" data-inputId="<?=$arItem['ID']?>">-</button>
                    <input type="text" disabled value="<?=itemQuantityInBasket($arItem['ID'])?>" data-id = "<?=$arItem['ID']?>" data-quantuti="<?=itemQuantity($arItem['ID'])?>">
                    <button class="increment" data-inputId="<?=$arItem['ID']?>">+</button>
                    <button class="update_quantity" data-id="<?=$arItem['ID']?>">Ok</button>
                </div>
            </div>
            <div>
                <button class="remove_item"  data-id="<?=$arItem['ID']?>"></button>
            </div>

        </div>
    <?php
    endforeach;
    ?>
   <div class="order-footer">
       <div class="total_sum">
           Общая сумма: <b><?=formatPrice($total)?></b>
       </div>
       <button class="btn btn-submit" id="order_basket">Оформить заказ</button>
   </div>
</div>
<?else:?>
 Корзина пуста <br>
    <a href="/catalog_sale/">Вернуться в каталог</a>
<?endif;?>
<div class="popup_info">

</div>


