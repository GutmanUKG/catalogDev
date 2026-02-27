<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);
?>

<? if ($arResult["BANNERS"]) : ?>
    <div id="banner<?= $arResult['ID'] ?>" class="carousel carousel-b2b slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <? foreach ($arResult["BANNERS"] as $index => $banner) : ?>
                <button type="button" data-bs-target="#banner<?= $arResult['ID'] ?>" data-bs-slide-to="<?= $index ?>" <?= $index == 0 ? 'class="active" aria-current="true"' : ''?> aria-label="Slide <?= $index ?>"></button>
            <? endforeach; ?>
        </div>

        <div class="carousel-inner">
            <? foreach ($arResult["BANNERS"] as $index => $banner) : ?>
                <div class="carousel-item <?=$index == 0 ? 'active' : '' ?>">
                    <a target="_blank" href="<?= $banner['LINK'] ?>">
                        <img src="<?= $banner["SRC"] ?>" class="d-block w-100" alt="">
                    </a>
                </div>
            <? endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#banner<?= $arResult['ID'] ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#banner<?= $arResult['ID'] ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <style>
        .page-header {
            display: none;
        }
        .content-inner .content {
            padding: 0;
        }
    </style>
<? endif; ?>