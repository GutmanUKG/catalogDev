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
?>
<?php

$filter_result = $GLOBALS['arrFilter'];

$arrBrands = $brand_ids = [];
// Если фильтруем по свойству CITY, добавляем элементы с пустым значением CITY

// Получаем все товары отсортированные по фильтру Уже фильтр отработал
foreach ($arResult['ITEMS'] as $arItem) {
    // Предполагаем, что ID сервисного центра это $arItem['ID']
    // и его имя $arItem['NAME']
    $serviceCenterId = $arItem['ID'];
    $serviceCenterName = $arItem['NAME'];
    $serviceCenterProps = $arItem['PROPERTIES'];

    ?>
    <?php
    foreach ($arItem['PROPERTIES']['BRAND']['VALUE'] as $brandId) {

        // Если бренды выбраны в фильтре проверяем если такой не выбран не записываем его
        if(LANGUAGE_ID == 'ru'){
            if (isset($filter_result['=PROPERTY_843']) && !in_array($brandId, $filter_result['=PROPERTY_843'])) {
                continue;
            }
        }
        if(LANGUAGE_ID == 'kz'){
            if (isset($filter_result['=PROPERTY_844']) && !in_array($brandId, $filter_result['=PROPERTY_844'])) {
                continue;
            }
        }
        if(LANGUAGE_ID == 'en'){
            if (isset($filter_result['=PROPERTY_851']) && !in_array($brandId, $filter_result['=PROPERTY_851'])) {
                continue;
            }
        }
        $brand_ids[] = $brandId;

        // Добавляем сервисный центр в массив бренда
        $arrBrands[$brandId]['SERVICE_CENTERS'][] = [
            'ID' => $serviceCenterId,
            'NAME' => $serviceCenterName,
            'PROPERTIES' => $serviceCenterProps
        ];
    }
}

// Функция для получения данных бренда по его ID
function getBrandData($brandId) {
    $brandData = [];
    $res = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => 20, 'ID' => $brandId],
        false,
        false,
        ['ID', 'NAME', 'PREVIEW_PICTURE']
    );

    $brandData = [];
    while ($arBrand = $res->GetNext()) {
        $brandData[$arBrand['ID']]['NAME'] = $arBrand['NAME'];
        if ($arBrand['PREVIEW_PICTURE']) {
            $brandData[$arBrand['ID']]['PREVIEW_PICTURE'] = CFile::GetPath($arBrand['PREVIEW_PICTURE']);
        } else {
            $brandData[$arBrand['ID']]['PREVIEW_PICTURE'] = '';
        }
    }

    return $brandData;
}

// тут передаем все собранные бренды (уже отфильтрованные)
// записываем в массив чтобы все чётко было
$brand_list = getBrandData($brand_ids);
foreach ($brand_list as $brand_id => $row) {
    $arrBrands[$brand_id]['NAME'] = $row['NAME'];
    $arrBrands[$brand_id]['PREVIEW_PICTURE'] = $row['PREVIEW_PICTURE'];
}

// Функция сортировки сервисных центров по городу
function sortServiceCentersByCity($a, $b) {
    return strcmp($a['PROPERTIES']['CITY']['VALUE'], $b['PROPERTIES']['CITY']['VALUE']);
}

// Сортируем каждый бренд по алфавиту по свойству города

foreach ($arrBrands as &$brand) {
    if (isset($brand['SERVICE_CENTERS']) && is_array($brand['SERVICE_CENTERS'])) {
        usort($brand['SERVICE_CENTERS'], 'sortServiceCentersByCity');
    }
}


unset($brand);

// Сортировка брендов по имени
uasort($arrBrands, function($a, $b) {
    return strcmp($a['NAME'], $b['NAME']);
});

$isFound = false;
?>

<ul class="serv_list">
    <?foreach ($arrBrands as $item):?>
        <?if(!empty($item['SERVICE_CENTERS'])):?>
            <?$isFound = true?>
            <li class="serv_item">
                <div class="image">

                    <?if(!empty($item['PREVIEW_PICTURE'])):?>
                        <img src="<?=$item['PREVIEW_PICTURE']?>" alt="<?=$item['NAME']?>">
                    <?endif;?>
                </div>
                <div>
                    <div>
                        <h5 class="text-left"><?=$item['NAME']?></h5>
                        <div class="centers">

                            <?$count = 0;?>
                            <?foreach ($item['SERVICE_CENTERS'] as $center):?>
                                <div class="item">

                                    <div>
                                        <b><?=GetMessage('CITY')?> :
                                            <?if($center['PROPERTIES']['CITY']['VALUE'] == 'All' || empty($center['PROPERTIES']['CITY']['VALUE'])):?>
                                            <?=GetMessage('ALL_CITY')?>
                                            <?else:?>
                                            <?=$center['PROPERTIES']['CITY']['VALUE']?>
                                            <?endif;?>
                                        </b>
                                    </div>
                                    <div>
                                        <?=$center['PROPERTIES']['COMPANY']['VALUE']?>
                                    </div>
                                    <div class="">
                                        <b><?=GetMessage('ADRESS')?></b>: <?=$center['PROPERTIES']['ADRESS']['VALUE']?>
                                    </div>
                                    <?
                                    $countItems = 0;
                                    ?>
                                    <?foreach ($center['PROPERTIES']['PHONES']['VALUE'] as $phone):?>
                                        <?$countItems++?>
                                        <div class="phones" <?if($countItems > 1):?> style="display: none" <?endif?>>
                                            <a href="tel:<?=$phone?>"><?=$phone?></a>
                                        </div>
                                    <?endforeach;?>
                                    <?$countEmail = 0?>
                                    <?foreach ($center['PROPERTIES']['EMAIL']['VALUE'] as $email):?>
                                        <?$countEmail++?>
                                        <div class="emails" style="<?if($countEmail > 1):?> display: none <?endif;?>">
                                            <a href="mailto:<?=$email?>"><?=$email?></a>
                                        </div>
                                    <?endforeach;?>
                                    <?$calcItems = $countEmail + $countItems?>
                                    <?if($calcItems > 3):?>
                                    <?endif;?>
                                </div>
                                <?$count++?>
                            <?endforeach;?>
                        </div>
                    </div>

                    <?//if($count > 3):?>
                    <button class="show_more btn">
                        <b> <?=GetMessage('BTN')?></b>
                        <span class="arr"></span>
                    </button>
                    <?//endif;?>
                </div>
            </li>
        <?else:?>
            <?$isFound = false?>
        <?endif?>
    <?endforeach;?>
    <?if($isFound == false):?>
        <?=GetMessage('NOT_FOUND')?>
    <?endif;?>
</ul>
