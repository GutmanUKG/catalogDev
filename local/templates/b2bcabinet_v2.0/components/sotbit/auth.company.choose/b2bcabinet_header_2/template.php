<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<? if (!empty($arResult['COMPANIES'])): ?>
    <div class="breadcrumb-elements-item dropdown p-0 company-choose-dropdown">
        <a href="javascript:void(0);" class="<?=count($arResult['COMPANIES']) > 1 ? 'dropdown-toggle" title="' . \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE") .'"'  : 'no-dropdown" title="' . \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE_2") .'"'?> data-toggle="dropdown" data-hover="dropdown">
            <i class="icon-users2 mr-2"></i>
            <?= $arResult['CURRENT_COMPANY'] ?>
        </a>
        <? if (!empty($arResult['COMPANIES']) && count($arResult['COMPANIES']) > 1): ?>
            <div class="dropdown-menu dropdown-menu-right">
                <? foreach ($arResult['COMPANIES'] as $company) {
                    if ($company["ID_COMPANY"] == $_SESSION['AUTH_COMPANY_CURRENT_ID']) {
                        continue;
                    }
                    ?>
                    <span
                            class="dropdown-item"
                            data-company-id="<?= $company['ID_COMPANY'] ?>"
                            onclick="setCompanyID(<?= $company['ID_COMPANY'] ?>)"
                    >
                    <?= $company['COMPANY_NAME'] ?>
                </span>
                    <?
                } ?>
            </div>
        <? endif; ?>
    </div>
<? endif; ?>
