<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>
<div class="personal-manager-button fab-menu fab-menu-bottom fab-menu-bottom-end" data-fab-position="custom" data-fab-toggle="click">
    <button type="button" class="fab-menu-btn btn btn-primary btn-icon">
        <div class="m-1">
            <i class="fab-icon-open ph-phone-call"></i>
            <i class="fab-icon-close ph-x"></i>
        </div>
    </button>

    <ul class="fab-menu-inner">
        <?if(!empty($arResult['WORK_PHONE'])):?>
            <li>
                <div class="fab-label-center" data-fab-label="<?= Loc::getMessage('SOTBIT_PERSONAL_MANAGER_PHONE') ?>">
                    <a href="tel:<?=$arResult['WORK_PHONE']?>" class="btn btn-xl btn-light btn-icon btn-float">
                        <i class="ph-phone-call"></i>
                    </a>
                </div>
            </li>
        <? endif; ?>
        <?if(!empty($arResult['UF_P_MANAGER_EMAIL'])):?>
            <li>
                <div class="fab-label-center" data-fab-label="<?= Loc::getMessage('SOTBIT_PERSONAL_MANAGER_EMAIL') ?>">
                    <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="btn btn-xl btn-light btn-icon btn-float" onclick="">
                        <i class="ph-envelope"></i>
                    </a>
                </div>
            </li>
        <? endif; ?>
        <li>
            <div class="fab-label-center" data-fab-label="<?= Loc::getMessage('SOTBIT_PERSONAL_MANAGER_REQUEST_CALL')?>">
                <button type="button" class="btn btn-xl btn-light btn-icon btn-float" data-bs-toggle="modal" data-bs-target="#modal_manager">
                    <i class="ph-headphones"></i>
                </button>
            </div>
        </li>
    </ul>
</div>
