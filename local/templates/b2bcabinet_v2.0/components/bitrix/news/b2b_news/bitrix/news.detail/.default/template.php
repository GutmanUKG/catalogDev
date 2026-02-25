<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/assets/js/plugins/media/fancybox.min.js");

$this->setFrameMode(true);
?>
<div class="news-detail d-flex align-items-start flex-column flex-md-row">
    <div class="w-100 order-2 order-md-1">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arResult["DETAIL_PICTURE"])): ?>
                        <div class="mb-3 text-center news-detail__image-container">
                            <img
                                    class="card-img img-fluid d-inline-block"
                                    src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>"
                                    width="<?= $arResult["DETAIL_PICTURE"]["WIDTH"] ?>"
                                    height="<?= $arResult["DETAIL_PICTURE"]["HEIGHT"] ?>"
                                    alt=""
                            />
                        </div>
                    <? endif; ?>

                    <? if ($arParams["DISPLAY_NAME"] != "N" && $arResult["NAME"]): ?>
                        <h5 class="font-weight-semibold mb-2">
                            <? echo $arResult["NAME"] ?>
                        </h5>
                    <? endif; ?>

                    <? if ($arResult["FIELDS"]): ?>
                        <ul class="list-inline list-inline-dotted text-muted mb-3">
                            <? foreach ($arResult["FIELDS"] as $key => $FieldItem): ?>
                                <? if ($FieldItem != ''): ?>
                                    <? if ($key == 'SHOW_COUNTER'): ?>
                                        <li class="list-inline-item fs-xs">
                                        <?= $FieldItem ?>
                                        <?= \Sotbit\B2bCabinet\Element::num2word($FieldItem, [
                                                                    GetMessage('ONE_COUNTER'),
                                                                    GetMessage('SOME_COUNTER'),
                                                                    GetMessage('MORE_COUNTER'),
                                                                ]
                                            ) 
                                        ?>
                                        </li>
                                    <?else:?>
                                        <li class="list-inline-item fs-xs">
                                            <?= Loc::getMessage("NEWS_DETAIL_SEARCH_" . $key); ?>
                                            <span class="text-muted fs-xs"><?= $FieldItem ?></span>
                                        </li>
                                    <? endif; ?>
                                <? endif; ?>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>
                    <div class="news-detail__content">                    
                        <? echo $arResult["DETAIL_TEXT"] ?>
                    </div>                    
                </div>

                <? if ($arResult["GALERY_PROPERTY"]): ?>
                    <h5 class="card-title"><?= Loc::getMessage('GALERY_TITLE') ?></h5>
                    <hr/>
                    <div class="row">
                        <? foreach ($arResult["GALERY_PROPERTY"] as $imgItem): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card">
                                    <div class="card-img-actions">
                                        <a href="<?= $imgItem['BIG_IMAGE']['src'] ?>"
                                            class="lightbox-toggle btn-outline bg-white text-white border-white border-2 btn-icon rounded-round"
                                            data-bs-toggle="lightbox"
                                            data-gallery="galery" rel="group">
                                            <img class="mw-100 mh-100 h-auto w-auto m-auto top-0 end-0 bottom-0 start-0 img-fluid" src="<?= $imgItem['SMALL_IMAGE']['src'] ?>" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>

            </div>
        </div>
    </div>
    <aside class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-right border-0 shadow-0 order-1 order-md-2 sidebar-expand-lg ps-3">
        <div class="sidebar-content">

            <? $APPLICATION->IncludeComponent(
                "bitrix:main.share",
                "b2b_share",
                array(
                    "HANDLERS" => array(
                        0 => "facebook",
                        1 => "lj",
                        2 => "mailru",
                        3 => "ok",
                        4 => "telegram",
                        5 => "twitter",
                        6 => "viber",
                        7 => "whatsapp",
                    ),
                    "HIDE" => "N",
                    "PAGE_TITLE" => $arResult["NAME"],
                    "PAGE_URL" => $arResult["DETAIL_PAGE_URL"],
                    "SHORTEN_URL_KEY" => "",
                    "SHORTEN_URL_LOGIN" => "",
                    "COMPONENT_TEMPLATE" => "b2b_share",
                    "IMAGE_FACEBOOK_SRC" => "",
                    "IMAGE_LJ_SRC" => "",
                    "IMAGE_MAILRU_SRC" => "",
                    "IMAGE_OK_SRC" => "",
                    "IMAGE_TELEGRAM_SRC" => "",
                    "IMAGE_TWITTER_SRC" => "",
                    "IMAGE_VIBER_SRC" => "",
                    "IMAGE_VK_SRC" => "",
                    "IMAGE_WHATSAPP_SRC" => ""
                ),
                $component
            ); ?>

            <? if ($arResult["GALERY_PROPERTY"]): ?>
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="card-title font-weight-semibold"><?= Loc::getMessage('GALERY_TITLE') ?></span>
                        <div class="header-elements">
                            <div class="list-icons">
                                <span class="list-icons-item" data-action="collapse"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <? foreach ($arResult["GALERY_PROPERTY"] as $imgItem): ?>
                                <div class="mb-3 col-md-6">
                                    <div class="card-img-actions">
                                        <a href="<?= $imgItem["SRC"] ?>" 
                                            data-bs-toggle="lightbox"
                                            data-gallery="galery_min">
                                            <img class="card-img img-fluid"
                                                 src="<?= $imgItem["SRC"] ?>"
                                                 alt="">
                                        </a>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </aside>
</div>

<script>
    App.initLightbox();
</script>