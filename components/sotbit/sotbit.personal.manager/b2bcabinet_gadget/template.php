<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>

<div class="b2b-personal-manager">
    <div class="card-img-actions d-inline-block mb-3">
        <img class="img-fluid rounded-circle object-fit-cover w-100 h-100" src="<?=!empty($arResult['PERSONAL_PHOTO']['src']) ? $arResult['PERSONAL_PHOTO']['src'] : SITE_TEMPLATE_PATH . '/assets/images/acc_manager.svg'?>" width="135" height="135" alt="">
        <div class="card-img-actions-overlay card-img rounded-circle">
            <?if ($arResult['WORK_PHONE']):?>
                <a href="tel:<?=$arResult['WORK_PHONE']?>" class="btn btn-sm btn-outline-white text-body border-2 btn-icon rounded" title="<?=Loc::getMessage('PERSONAL_MANAGER_CALL')?>">
                    <i class="ph-phone-call fs-sm"></i>
                </a>
            <?endif;?>
            <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
                <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="btn btn-sm btn-outline-white text-body border-2 btn-icon rounded" title="<?=Loc::getMessage('PERSONAL_MANAGER_MESSAGE')?>">
                    <i class="ph-envelope fs-sm"></i>
                </a>
            <?endif;?>
            <a href="#" class="btn btn-sm btn-outline-white text-body border-2 btn-icon rounded" title="<?=Loc::getMessage('PERSONAL_MANAGER_REQUEST_CALL')?>" data-bs-toggle="modal" data-bs-target="#modal_manager">
                <i class="ph-headphones fs-sm"></i>
            </a>
        </div>
    </div>
    <div class="b2b-personal-manager-info">
        <h6 class="fw-bold"><?=$arResult['NAME_FORMATTED']?></h6>
        <?if ($arResult['WORK_PHONE']):?>
            <span class="text-muted"><?=Loc::getMessage('PERSONAL_MANAGER_PHONE')?></span>
            <a href="tel:<?=$arResult['WORK_PHONE']?>" class="your_manager-phone_number"><span class="d-block"><?=$arResult['WORK_PHONE']?></span></a>
        <?endif;?>
        <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
            <span class="d-block mt-2 text-muted"><?=Loc::getMessage('PERSONAL_MANAGER_EMAIL')?></span>
            <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="your_manager-phone_number"><span class="d-block"><?=$arResult['UF_P_MANAGER_EMAIL']?></span></a>
        <?endif;?>
    </div>
</div>