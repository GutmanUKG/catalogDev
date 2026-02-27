<?php

use Sotbit\B2bCabinet\Helper\Config,
    \Bitrix\Main\Localization\Loc;

global $APPLICATION;
$APPLICATION->SetTitle('');
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$linkHome = Config::getMethodInstall(SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . 'b2bcabinet/' : SITE_DIR;
?>

<div class="flex-fill">

    <div class="text-center">
        <img src="<?= SITE_TEMPLATE_PATH . '/assets/images/404.png' ?>" class="img-fluid" height="230" width="450"
             alt="">
    </div>

    <h5 class="w-md-25 mx-md-auto text-center"><?=Loc::getMessage("B2B_404_TEXT")?></h5>
</div>

<div class="text-center">
    <a href="<?= $linkHome ?>" class="btn btn-primary btn_b2b"><i class="icon-home4 mr-2"></i> <?=Loc::getMessage("B2B_404_BTN_HOME")?></a>
</div>

