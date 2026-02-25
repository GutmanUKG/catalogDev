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
$PARAMS_NAME = 'IBLOCK_NEWS_ID_' . strtoupper(LANGUAGE_ID);
$IBLOCK_ID = $arParams[$PARAMS_NAME]; // ID инфоблока новостей  брэнда


// Sort the items alphabetically by the 'NAME' key
usort($arResult['ITEMS'], function($a, $b) {
    return strcmp($a['NAME'], $b['NAME']);
});

// Group items by the first letter of their name
$groupedItems = [];
foreach ($arResult['ITEMS'] as $item) {
    $firstLetter = mb_substr($item['NAME'], 0, 1);
    if (!isset($groupedItems[$firstLetter])) {
        $groupedItems[$firstLetter] = [];
    }
    $groupedItems[$firstLetter][] = $item;
}

// Define the full English alphabet
$alphabet = range('A', 'Z');

?>

<div class="filter_by_letter">
    <div class="letter_list">
        <?php foreach ($alphabet as $letter): ?>
            <div class="item <?= !isset($groupedItems[$letter]) ? 'disabled' : '' ?>">
                <?=$letter?>
            </div>
        <?endforeach;?>
    </div>
    <button class="clear_filter"><?=GetMessage('CLEAR_BTN')?></button>
</div>
<div class="btn_filter">
    <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/filter-2.svg" alt="">
    <?=GetMessage('CATEGORY_BTN')?>
</div>

<?php foreach ($groupedItems as $letter => $items): ?>
    <div class="row_brand" data-letter="<?=$letter?>">
        <span class="brand_title"><?=$letter?></span>
        <div class="brand_list">
            <?php foreach ($items as $item): ?>

                <div class="brand_item"
                     data-id="<?=$item['ID']?>"
                     <?if(LANGUAGE_ID == "ru"): //ID секции с списком новостей?>
                         data-section_news="<?=$item['PROPERTIES']['BRAND_NEWS']['VALUE']?>"
                     <?elseif (LANGUAGE_ID == 'kz'):?>
                         data-section_news="<?=$item['PROPERTIES']['BRAND_NEWS_KZ']['VALUE']?>"
                     <?elseif (LANGUAGE_ID == 'en'):?>
                         data-section_news="<?=$item['PROPERTIES']['BRAND_NEWS_EN']['VALUE']?>"
                     <?else:?>
                     <?endif;?>

                     data-decr="<?=$item['PREVIEW_TEXT']?>"
                     data-iblock_id="<?=$IBLOCK_ID?>"
                >
                   
                    <img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" alt="<?=$item['PREVIEW_PICTURE']['ALT']?>">
                    <h5><?=$item['NAME']?></h5>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<div class="popup_info animate__animated">
    <div class="close_btn close_btn_popup"></div>
    <div class="left">
        <div>
            <h3 class="title"></h3>
            <div class="decr"></div>
        </div>
        <div class="section_names">
            <h6><?=GetMessage('CATEGORY')?></h6>
            <div class="category_items">
            </div>
        </div>
    </div>
    <div class="right">
        <img src="" alt="" class="brand_logo">
        <h6 class="news_title"><?=GetMessage('NEWS')?></h6>
        <div class="news">
        </div>
    </div>
</div>

<div class="overlay"></div>
