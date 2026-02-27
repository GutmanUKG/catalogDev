<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
use Bitrix\Main\Page\Asset;

// Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/perfect_scrollbar.min.js");

$config = \Bitrix\Main\Web\Json::encode($arResult['CONFIG']);
?>

<div data-bx-user-consent="<?= htmlspecialcharsbx($config) ?>" class="main-user-consent-request form-check">
    <input type="checkbox"
           class="form-check-input"
           value="Y"
           <?= ($arParams['IS_CHECKED'] ? 'checked' : '') ?>
           <?= ($arParams['IS_DISABLED'] ? 'disabled' : '') ?>
           name="<?= htmlspecialcharsbx($arParams['INPUT_NAME']) ?>"
           id="vnji534">
    <label class="form-check-label" for="cc_ls_c"><?= htmlspecialcharsbx($arResult['INPUT_LABEL']) ?></label>
</div>

<template type="text/html" data-bx-template="main-user-consent-request-loader">
    <div class="main-user-consent-request-popup">
        <div class="main-user-consent-request-popup-cont">
            <h5 data-bx-head="" class="main-user-consent-request-popup-header modal-title"></h5>
            <div class="main-user-consent-request-popup-body">
                <div data-bx-loader="" class="main-user-consent-request-loader">
                    <svg class="main-user-consent-request-circular" viewBox="25 25 50 50">
                        <circle class="main-user-consent-request-path" cx="50" cy="50" r="20" fill="none"
                                stroke-width="1" stroke-miterlimit="10"></circle>
                    </svg>
                </div>
                <div data-bx-content="" class="main-user-consent-request-popup-content">
                    <div class="main-user-consent-request-popup-textarea-block">
                        <div data-bx-textarea=""
                             class="main-user-consent-request-popup-text confidencial-text">
                        </div>
                    </div>

                </div>
                <div class="main-user-consent-request-popup-buttons">
                    <span data-bx-btn-accept="" class="btn btn_b2b">Y</span>
                    <span data-bx-btn-reject="" class="btn btn-link">N</span>
                </div>
            </div>
        </div>
    </div>
</template>