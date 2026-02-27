<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
?>

<?if(!empty($arResult['COMPANIES'])):?>
    <div class="sidebar-section dropdown auth-company-change">
        <a class="btn btn-link text-start w-100" <?= (count($arResult['COMPANIES']) > 1 ? 'data-bs-toggle="dropdown"' : '') ?>>
            <div class="hstack gap-2 w-100" 
            <? if (strlen(htmlspecialcharsex($arResult['CURRENT_COMPANY'])) >= 14): ?>
                data-bs-popup="tooltip" 
                data-bs-placement="right" 
                data-bs-original-title="<?=htmlspecialcharsex($arResult['CURRENT_COMPANY'])?>"
            <? endif; ?>
                >
                <i class="ph ph-user flex-shrink-0"></i>
                
                <div class="me-auto overflow-hidden">
                    <div class="auth-company-change__title"><?=Loc::getMessage('SOTBIT_COMPANY_CHOOSE_TITLE')?></div>
                    <div class="fw-normal auth-company-change__name"><?= $arResult['CURRENT_COMPANY']?></div>
                </div>

                <?= (count($arResult['COMPANIES']) > 1 ? '<i class="ph ph-caret-down"></i>' : '') ?>
            </div>
        </a>
        <? if (!empty($arResult['COMPANIES'])) { ?>
            <div class="dropdown-menu dropdown-menu-sm w-100 showdown">
                <? foreach ($arResult['COMPANIES'] as $company) {
                    if($company["ID_COMPANY"] == $_SESSION['AUTH_COMPANY_CURRENT_ID'])
                        continue;
                    ?>
                    <a href="#" class="dropdown-item hstack gap-2 py-2"
                        data-company-id="<?= $company['ID_COMPANY'] ?>"
                        onclick="setCompanyID(<?= $company['ID_COMPANY'] ?>)">
                        <div class="text-wrap text-break"><?= $company['COMPANY_NAME'] ?></div>
                    </a>
                    <?
                } ?>
            </div>
        <? } ?>
        <i class="ph-dots-three sidebar-resize-show"></i>
    </div>
<?endif;?>
