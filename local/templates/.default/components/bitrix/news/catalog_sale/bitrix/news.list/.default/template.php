<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
use Bitrix\Main\Page\Asset;
$this->setFrameMode(true);
Asset::getInstance()->addJs($this->GetFolder() . '/lib/js/fancybox.umd.js');
Asset::getInstance()->addCss($this->GetFolder(). '/lib/css/fancybox.css');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/catalog_sale/function.php");

?>

<div class="catalog_sale ">
    <div class="top row_element <?if(!check_user_group()):?> user <?endif;?>">
        <div class="item"></div>
        <div class="item">Наименование</div>
        <div class="item" style="text-align: center">Цена уценки</div>
        <div class="item" style="text-align: center">РРЦ</div>
        <div class="item" style="text-align: center">В наличии</div>
        <div class="item item_btns_top" style="text-align: center">
            <?if(check_user_group()):?>
            <button id="add_item"></button>
            <?endif;?>
            <?if(check_user_group()):?>
            <button id="export_catalog">Export</button>
            <button id="import_catalog">Import</button>
            <?else:?>
                <button id="export_catalog" class="user_export" style="margin: 0 auto">Export</button>
            <?endif;?>
        </div>
    </div>
    <div class="elements_list ">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <div class="item_wrap">
                <div class="item <?if(!check_user_group()):?> user <?endif;?> <?php if(check_user() && ($arItem['PROPERTIES']['COUNT']['VALUE'] < 1 || empty($arItem['PROPERTIES']['COUNT']['VALUE']))) echo  'none_count'?>"
                     id="<?=$arItem['ID']?>">

                        <div class="photo">
                            <?if(!empty($arItem['PREVIEW_PICTURE']['SRC'])):?>
                                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>">
                            <?elseif(!empty($arItem['PROPERTIES']['PHOTO']['VALUE'])):?>
                                <?$fileSRC = CFile::GetPath($arItem['PROPERTIES']['PHOTO']['VALUE'])?>
                                <img src="<?=$fileSRC?>" alt="<?=$arItem['NAME']?>">
                            <?else:?>
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/no_photo.svg" alt="">
                            <?endif;?>
                        </div>

                        <div class="name">
                            <div class="name_item"><?=cleanHtmlEntities($arItem['NAME'])?></div>
                            <?if(!empty($arItem['PROPERTIES']['BRAND']['VALUE'])):?>
                                <p>Бренд : <b><?=$arItem['PROPERTIES']['BRAND']['VALUE']?></b></p>
                            <?endif;?>
                            <?if(!empty($arItem['PROPERTIES']['ART']['VALUE'])):?>
                            <p style="margin-bottom: 0;">Артикул: <b><?=$arItem['PROPERTIES']['ART']['VALUE']?></b></p>
                            <?endif;?>

                            <?if(!empty($arItem['PROPERTIES']['PART']['VALUE'])):?>
                                <p>SN : <b><?=$arItem['PROPERTIES']['PART']['VALUE']?></b></p>
                            <?else:?>
                                <p>SN : <b>Не указан</b></p>
                            <?endif;?>
                            <?if(check_user_group()):?>
                            <div class="d-flex align-items-center kat">
                                <?=$arItem['PROPERTIES']['KAT_CATEGORY']['VALUE']?>
                                <div class="info_ico" style="cursor: pointer"></div>
                            </div>
                            <?endif;?>

                        </div>
                        <div class="price">
                            <?php
                            if(!empty($arItem['PRICE']['PRICE'])){
                                echo formatPrice($arItem['PRICE']['PRICE']);
                            }else{
                                echo 'нет цены';
                            }
                            ?>
                            <?if(!empty($arItem['PROPERTIES']['CARUSEL']['VALUE'])):?>
                            <?
                            foreach ($arItem['PROPERTIES']['CARUSEL']['VALUE'] as $img) {
                                $imgPath = \CFile::GetPath($img);
                            }
                                ?>
                            <?if(!empty($imgPath)):?>
                            <div class="show_info">
                                фото уценки
                            </div>
                            <?endif;?>
                            <?endif;?>
                        </div>
                        <div class="rrc">
                            <?if(!empty($arItem['PROPERTIES']['RRP']['VALUE'])):?>
                                <?=formatPrice($arItem['PROPERTIES']['RRP']['VALUE'])?>
                            <?else:?>
                                нет цены
                            <?endif;?>
                        </div>
                        <div class="count">
                            <?if(!empty($arItem['PROPERTIES']['COUNT']['VALUE'])):?>
                            <?=$arItem['PROPERTIES']['COUNT']['VALUE']?> шт
                            <?endif;?>
                        </div>
                        <div class="btns  <?if(checkItemInBasket($arItem['ID'])):?>in_basket<?endif;?>">
                            <?if(!empty($arItem['PROPERTIES']['RRP']['VALUE']) &&
                                (!empty($arItem['PROPERTIES']['COUNT']['VALUE'])) ||
                                $arItem['PROPERTIES']['COUNT']['VALUE'] > 0):?>

                                <?if(check_user_sale()):?>

                                <?//if(checkItemInBasket($arItem['ID'])):?>
                                    <div class="quantity">
                                        <button class="dicrement" data-inputId="<?=$arItem['ID']?>">-</button>
                                        <input type="text" disabled value="<?=itemQuantityInBasket($arItem['ID'])?>" data-id = "<?=$arItem['ID']?>" data-quantuti="<?=itemQuantity($arItem['ID'])?>">
                                        <button class="increment" data-inputId="<?=$arItem['ID']?>">+</button>
                                        <button class="remove_basket"></button>
                                    </div>

                                <?//else:?>
                                    <button class="add_basket"></button>
                                <?//endif;?>

                                <?endif;?>
                            <?endif;?>
                            <?if(check_user_group()):?>
                                <button class="copy_item" content="Копировать" aria-description="Копировать"></button>
                                <button class="update_item"></button>
                                <button class="delete_item"></button>
                            <?endif;?>
                        </div>
                </div>
                    <?if(!empty($arItem['CHILD_ITEMS'])):?>
                    <div class="child_item">
                        <?foreach ($arItem['CHILD_ITEMS'] as $key => $value):?>
                            <div class="item <?if(!check_user_group()):?> user <?endif;?>" id="<?=$value['ID']?>">
                                    <div class="photo">
                                        <?if(!empty($value['PREVIEW_PICTURE']['SRC'])):?>
                                            <img src="<?=$value['PREVIEW_PICTURE']['SRC']?>" alt=" <?=$value['NAME']?>">
                                        <?elseif(!empty($value['PROPERTIES']['PHOTO']['VALUE'])):?>
                                            <?$fileSRC = CFile::GetPath($value['PROPERTIES']['PHOTO']['VALUE'])?>
                                            <img src="<?=$fileSRC?>" alt="<?=$value['NAME']?>">
                                        <?else:?>
                                            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/no_photo.svg" alt="">
                                        <?endif;?>
                                    </div>
                                    <div class="name">
                                        <div class="name_item"><?=cleanHtmlEntities($value['NAME'])?></div>
                                        <?if(!empty($value['PROPERTIES']['ART']['VALUE'])):?>
                                            <p style="margin-bottom: 0;">Арт: <b><?=$value['PROPERTIES']['ART']['VALUE']?></b></p>
                                        <?endif;?>
                                        <?if(!empty($value['PROPERTIES']['BRAND']['VALUE'])):?>
                                            <p>Бренд : <b><?=$value['PROPERTIES']['BRAND']['VALUE']?></b></p>
                                        <?endif;?>
                                        <?if(!empty($value['PROPERTIES']['PART']['VALUE'])):?>
                                            <p>SN : <b><?=$value['PROPERTIES']['PART']['VALUE']?></b></p>
                                        <?else:?>
                                            <p>SN : <b>Не указан</b></p>
                                        <?endif;?>
                                        <?if(check_user_group()):?>
                                        <div class="d-flex align-items-center kat">
                                            <?=$value['PROPERTIES']['KAT_CATEGORY']['VALUE']?>
                                            <div class="info_ico" style="cursor: pointer"></div>
                                        </div>
                                        <?endif?>
                                    </div>
                                    <div class="price">
                                        <?php
                                        if(!empty($value['PRICE']['PRICE'])){
                                            echo formatPrice($value['PRICE']['PRICE']);
                                        }else{
                                            echo 'нет цены';
                                        }
                                        ?>
                                            <?if(!empty($value['PROPERTIES']['CARUSEL']['VALUE'])):?>
                                                <?
                                                foreach ($value['PROPERTIES']['CARUSEL']['VALUE'] as $img) {
                                                    $imgPath = \CFile::GetPath($img);
                                                }
                                                ?>
                                                <?if(!empty($imgPath)):?>
                                                    <div class="show_info">
                                                        фото уценки
                                                    </div>
                                                <?endif;?>
                                            <?endif;?>
                                    </div>
                                    <div class="rrc">
                                        <?if(!empty($value['PROPERTIES']['RRP']['VALUE'])):?>
                                            <?=formatPrice($value['PROPERTIES']['RRP']['VALUE'])?>
                                        <?else:?>
                                            нет цены
                                        <?endif;?>
                                    </div>
                                    <div class="count">
                                        <?if(!empty($value['PROPERTIES']['COUNT']['VALUE'])):?>
                                            <?=$value['PROPERTIES']['COUNT']['VALUE']?> шт
                                        <?endif;?>
                                    </div>
                                    <div class="btns">
                                        <?if(!empty($value['PROPERTIES']['RRP']['VALUE']) &&
                                            (!empty($value['PROPERTIES']['COUNT']['VALUE'])) ||
                                            $value['PROPERTIES']['COUNT']['VALUE'] > 0):?>
                                            <?if(check_user_sale()):?>
                                                <?if(checkItemInBasket($value['ID'])):?>
                                                    <div class="quantity">
                                                        <button class="dicrement" data-inputId="<?=$value['ID']?>">-</button>
                                                        <input type="text" disabled value="<?=itemQuantityInBasket($value['ID'])?>" data-id = "<?=$value['ID']?>" data-quantuti="<?=itemQuantity($value['ID'])?>">
                                                        <button class="increment" data-inputId="<?=$value['ID']?>">+</button>
                                                        <button class="remove_basket"></button>
                                                    </div>

                                                <?else:?>
                                                    <button class="add_basket"></button>
                                                <?endif;?>
                                            <?endif;?>
                                        <?endif;?>
                                        <?if(check_user()):?>
                                            <button class="update_item"></button>
                                            <button class="delete_item"></button>
                                        <?endif;?>
                                    </div>

                            </div>
                        <?endforeach;?>
                    </div>
                        <button class="show_more">Еще <?=count($arItem['CHILD_ITEMS'])?></button>
                    <?endif;?>
                </div>
            </div>

        <?endforeach;?>
    </div>

</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br /><?=$arResult["NAV_STRING"]?>


<?endif;?>
<?if(counterBasketItems() > 0):?>
<div class="basket_line">
    <div>
        Количество товаров : <?=counterBasketItems()?>
    </div>
    <div>
        Сумма заказа : <?=totalSumInBasket()?>
    </div>
    <a href="/basket_sale/">Перейти к оформлению</a>
</div>
<?endif;?>
<div class="popup_info">

</div>
<div class="info_cat">
    <div class="close_cat"></div>
    <p>
        «Кат-1» - Любые повреждения упаковки (помятости, срыв стикера и т.д.), при этом товар не имеет внешних повреждений, исправен, укомплектован на 100% и выполняет все заявленные производителем функции
    </p>
    <p>
        «Кат-2» - Имеются видимые следы повреждения товара (царапины, потертости, незначительные сколы и вмятины и т.д.), при этом товар исправен, укомплектован на 100% и выполняет все заявленные производителем функции
    </p>
    <p>
        «Кат-3» - Бывший в употреблении товар, имеются следы использования, некомплект, при этом товар исправен и частично или полностью выполняет все заявленные производителем функции
    </p>
    <p>
        «Кат-4» - Товар после сервиса, производилось внутреннее, программное или иное вмешательство 
    </p>

</div>
<div id="overlay"></div>

<?//if(check_user_group()):?>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const btnExport = document.querySelector('#export_catalog');
        btnExport.addEventListener('click', exportCatalog);
        function exportCatalog() {
            btnExport.classList.add('loader')
            // Собираем параметры фильтра из URL
            const filterParams = new URLSearchParams(window.location.search);

            let data = new FormData();
            data.append('ACTION', 'EXPORT');
            data.append('FILTER', '<?=json_encode($GLOBALS['arrFilter'])?>');

            fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                method: 'POST',
                body: data
            })
                .then(res => res.json())
                .then(response => {
                    console.log(response);

                    if (response.status === 'success' && response.file) {
                        //window.location.href = response.file; // Скачивание файла
                        btnExport.classList.remove('loader')
                        downloadAsFile(response.file)
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }


        function downloadAsFile(href) {
            let a = document.createElement("a");
            let file = href;
            a.href = file;
            a.download = href;
            a.click();
           // console.log(a)
        }
    });



</script>
<?//endif;?>