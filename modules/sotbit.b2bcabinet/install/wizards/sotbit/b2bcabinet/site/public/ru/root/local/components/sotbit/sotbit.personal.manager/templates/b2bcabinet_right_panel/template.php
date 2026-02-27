<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
?>


<div class="bg-light text-muted py-2 px-3">
    <?=Loc::getMessage("PROFILE_RIGHT_PANEL_GROUP_PERSONAL_MANAGER")?>
</div>
<div class="p-3 user-link__right-pannel__personal-manager">

    <div class="card b2b-personal-manager">
        <div class="card-body text-center">
            <div class="card-img-actions d-inline-block mb-3">
                <img class="img-fluid rounded-circle" src="<?=!empty($arResult['PERSONAL_PHOTO']['src']) ? $arResult['PERSONAL_PHOTO']['src'] : SITE_TEMPLATE_PATH . '/assets/images/acc_manager.svg'?>" width="100" height="100" alt="">
            </div>

            <h6 class="font-weight-semibold mb-0"><?=$arResult['NAME_FORMATTED']?></h6>
            <?if ($arResult['WORK_PHONE']):?>
                <div>
                    <span><?=Loc::getMessage('PERSONAL_MANAGER_PHONE')?></span>
                    <a href="tel:<?=$arResult['WORK_PHONE']?>" class="your_manager-phone_number"><?=$arResult['WORK_PHONE']?></a>
                </div>
            <?endif;?>
            <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
                <div>
                    <span><?=Loc::getMessage('PERSONAL_MANAGER_EMAIL')?></span>
                    <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="your_manager-phone_number"><?=$arResult['UF_P_MANAGER_EMAIL']?></a>
                </div>
            <?endif;?>

            <ul class="list-inline list-inline-condensed mt-3 mb-0">
                <?if ($arResult['WORK_PHONE']):?>
                    <li class="list-inline-item">
                        <a href="tel:<?=$arResult['WORK_PHONE']?>" class="btn btn-call btn-icon border-2 rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_CALL')?>">
                            <i class="icon-phone2"></i>
                        </a>
                    </li>
                <?endif;?>
                <?if ($arResult['UF_P_MANAGER_EMAIL']):?>
                    <li class="list-inline-item">
                        <a href="mailto:<?=$arResult['UF_P_MANAGER_EMAIL']?>" class="btn btn-mail btn-icon border-2 rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_MAIL')?>">
                            <i class="icon-mention"></i>
                        </a>
                    </li>
                <?endif;?>
                <li class="list-inline-item">
                    <a href="javascript:void(0);" class="btn btn-request btn-icon border-2 rounded-pill" title="<?=Loc::getMessage('PERSONAL_MANAGER_REQUEST_CALL')?>" data-toggle="modal" data-target="#modal_manager" data-dismiss="modal">
                        <i class="icon-bubbles4"></i>
                    </a>
                </li>

            </ul>
        </div>
    </div>

</div>