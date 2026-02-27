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
$this->setFrameMode(true);?>
<?if($arResult['SEARCH']):?>
    <?foreach ($arResult["SEARCH"] as $key => $res): ?>
        <?if($res['IN_CHAIN']):?>
            <a class="btn btn-primary" href="<?=$res["TAG_WITHOUT"]?>" rel="nofollow">
                <?=$res['NAME']?>
            </a>
        <?else:?>
            <a class="btn" href="<?=$res["URL"]?>" rel="nofollow">
                <?=$res['NAME']?>
            </a>
        <?endif;?>
    <?endforeach;?>
<?endif;?>