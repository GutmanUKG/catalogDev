<?php

use Sotbit\B2bCabinet\Helper\Config,
    \Bitrix\Main\Localization\Loc;

global $APPLICATION;
$APPLICATION->SetTitle('');
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$linkHome = Config::getMethodInstall(SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . 'b2bcabinet/' : SITE_DIR;
?>
<style>
    .content-inner {
        background: url(<?= SITE_TEMPLATE_PATH . '/assets/images/404.svg' ?>) no-repeat;
        background-size: contain;
        background-position: 82% 200px;
    }

    .not-found-container {
        position: absolute;
        top: 10%;
        right: 5%;
        width: 600px;
    }

    .not-found-container img {
        width: 100%;
    }

    .not-found-container h1 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-size: 2rem;
        font-weight: 500;
        color: var(--primary);
    }

    .not-found-container__content {
        width: 60%;
        margin: 0 auto;
    }

    @media (max-width: 1024px) {
        .content-inner {
            background-position: calc(20% + 10px) 30px;
            background-size: auto 125%;
        }

        .not-found-container {
            top: 15%;
            width: 476px;
            right: 4%;
        }
        
        .not-found-container h1 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .not-found-container {
            top: 8%;
            width: 395px;
            right: 1rem;
        }

        .not-found-container h1 {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 576px) {
        .content-inner {
            background-size: auto 60%;
            background-position: 0 75%;
        }

        .not-found-container {
            width: auto;
            inset: 1.25rem 1rem auto 1rem;
        }

        .not-found-container__content {
            width: 80%;
        }

        .not-found-container h1 {
            margin-top: 1.25rem;
            font-size: 1rem;
        }
    }

</style>

<div class="not-found-container">
    <img src="<?= SITE_TEMPLATE_PATH . '/assets/images/404_title.svg' ?>" alt="404"> 
    <div class="not-found-container__content">
        <h1><?=Loc::getMessage("B2B_404_TEXT")?></h1>
        <a href="<?= $linkHome ?>" class="btn btn-primary"><?=Loc::getMessage("B2B_404_BTN_HOME")?></a>
    </div>
</div>



