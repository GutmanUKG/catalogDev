<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>

<div class="b2b-personal-manager">
    <div class="card-body text-center">
        <div class="card-img-actions d-inline-block mb-3">
            <img class="img-fluid rounded-circle" src="<?=!empty($arResult['PERSONAL_PHOTO']['src']) ? $arResult['PERSONAL_PHOTO']['src'] : SITE_TEMPLATE_PATH . '/assets/images/acc_manager.svg'?>" width="135" height="135" alt="">
            <div class="card-img-actions-overlay card-img rounded-circle">
                <?if ($arResult['WORK_PHONE']):?>
                    <a href="tel:<?=$arResult['WORK_PHONE']?>" class="btn btn-outline-white border-2 btn-icon rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_CALL')?>">
                        <i class="icon-phone2"></i>
                    </a>
                <?endif;?>
                <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
                    <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="btn btn-outline-white border-2 btn-icon rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_MESSAGE')?>">
                        <i class="icon-mention"></i>
                    </a>
                <?endif;?>
                <a href="#" class="btn btn-outline-white border-2 btn-icon rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_REQUEST_CALL')?>" data-toggle="modal" data-target="#modal_manager">
                    <i class="icon-bubbles4"></i>
                </a>
            </div>
        </div>

        <h6 class="font-weight-semibold mb-0"><?=$arResult['NAME_FORMATTED']?></h6>
        <?if ($arResult['WORK_PHONE']):?>
            <a href="tel:<?=$arResult['WORK_PHONE']?>" class="your_manager-phone_number"><span class="d-block"><?=Loc::getMessage('PERSONAL_MANAGER_PHONE')?> <?=$arResult['WORK_PHONE']?></span></a>
        <?endif;?>
        <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
            <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="your_manager-phone_number"><span class="d-block"><?=Loc::getMessage('PERSONAL_MANAGER_EMAIL')?> <?=$arResult['UF_P_MANAGER_EMAIL']?></span></a>
        <?endif;?>
    </div>
</div>