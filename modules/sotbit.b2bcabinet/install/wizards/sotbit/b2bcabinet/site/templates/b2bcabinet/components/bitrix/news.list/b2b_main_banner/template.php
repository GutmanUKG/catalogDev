<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);
?>

<?if ($arResult["BANNERS"]):?>
<div class="profile-cover">
    <div class="swiper-b2b-main-page">
        <div class="swiper-wrapper">
            <?foreach ($arResult["BANNERS"] as $banner):?>
                <a class="profile-cover-img swiper-slide" target="_blank" href="<?=$banner['LINK']?>" style="background-image: url(<?=$banner["SRC"]?>)"></a>
            <?endforeach;?>
        </div>
        <?if (count($arResult["BANNERS"]) > 1):?>
            <div class="slider-pagination swiper-pagination-bullets"></div>
            <div class="btn-slider-main btn-slider-main--prev"></div>
            <div class="btn-slider-main btn-slider-main--next"></div>
        <?endif;?>
    </div>
</div>

<script>
    var sliderMainBanner = new JCB2BMainBanner({
        BANNER_COUNT: <?=count($arResult["BANNERS"])?>,
        SWIPER_SELECTOR: '.swiper-b2b-main-page',
    });
</script>

<style>
    .page-header {
        display: none;
    }
</style>
<?endif;?>