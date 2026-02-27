<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>
<div class="company-auth-vidget">
<? foreach ($arResult['PROFILES'] as $profile): ?>
    <div class="company-auth-vidget-item">
        <a class="company-auth-vidget-item-link" href="<?=$arParams['SEF_FOLDER'].$profile['URL_TO_DETAIL']?>">
            <span><?=$profile['NAME']?></span>
            <i class="icon-arrow-right13 mr-2"></i>
        </a>
    </div>
<?endforeach;?>
<a class="company-auth-vidget-link main-link b2b-main-link" href="<?=$arParams['SEF_FOLDER']?>"><?=Loc::getMessage('GD_SOTBIT_CABINET_BUYERS_TO_ORGANIZATION')?></a>
</div>