<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/assets/js/plugins/media/fancybox.min.js");

$this->setFrameMode(true);
?>
<div class="news-detail d-flex align-items-start flex-column flex-md-row">
    <div class="w-100 overflow-auto order-2 order-md-1">
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
                        <h4 class="font-weight-semibold mb-1">
                            <a class="text-default" href="<?= $arResult["DETAIL_PAGE_URL"] ?>">
                                <? echo $arResult["NAME"] ?>
                            </a>
                        </h4>
                    <? endif; ?>

                    <? if ($arResult["FIELDS"]): ?>
                        <ul class="list-inline list-inline-dotted text-muted mb-3">
                            <? foreach ($arResult["FIELDS"] as $key => $FieldItem): ?>
                                <? if ($FieldItem != ''): ?>
                                    <li class="list-inline-item">
                                        <?= Loc::getMessage("NEWS_DETAIL_SEARCH_" . $key); ?>
                                        <span class="text-muted"><?= $FieldItem ?></span>
                                    </li>
                                <? endif; ?>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>

                    <? echo $arResult["DETAIL_TEXT"] ?>

                </div>

                <? if ($arResult["GALERY_PROPERTY"]): ?>
                    <h5 class="card-title"><?= Loc::getMessage('GALERY_TITLE') ?></h5>
                    <hr/>
                    <div class="row">
                        <? foreach ($arResult["GALERY_PROPERTY"] as $imgItem): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card">
                                    <div class="card-img-actions m-1">
                                        <img class="card-img img-fluid" src="<?= $imgItem["SRC"] ?>" alt="">
                                        <div class="card-img-actions-overlay card-img">
                                            <a href="<?= $imgItem["SRC"] ?>"
                                               class="btn btn-outline bg-white text-white border-white border-2 btn-icon rounded-round"
                                               data-popup="lightbox" rel="group">
                                                <i class="icon-plus3"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>

            </div>
        </div>
    </div>
    <aside class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-right border-0 shadow-0 order-1 order-md-2 sidebar-expand-md">
        <div class="sidebar-content">

            <? if ($arParams["USE_SEARCH"] == "Y"): ?>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:search.form",
                    "flat",
                    array(
                        "PAGE" => $arParams["SEARCH_URL"]
                    ),
                    $component
                ); ?>
            <? endif; ?>

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
                                <div class="mb-2">
                                    <div class="card-img-actions">
                                        <a href="<?= $imgItem["SRC"] ?>" data-popup="lightbox">
                                            <img class="card-img img-fluid"
                                                 src="<?= $imgItem["SRC"] ?>"
                                                 alt="">
                                            <span class="card-img-actions-overlay card-img">
                                                <i class="icon-plus3"></i>
                                            </span>
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