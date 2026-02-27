<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<? if (!empty($arResult['COMPANIES'])): ?>
    <div class="sidebar-section sidebar-resize-hide dropdown mx-2">
    <?if (count($arResult['COMPANIES']) > 1):?>
        <a href="#" class="btn btn-link text-body text-start lh-1 dropdown-toggle p-2 my-1 w-100" data-bs-toggle="dropdown" data-color-theme="dark"
           title='<?= \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE") ?>'>
            <i class="ph ph-user"></i>
            <span class="ml-2 fw-semibold">
                <?= mb_strimwidth($arResult['CURRENT_COMPANY'], 0, 30, "...");?>
            </span>
        </a>

        <? if (!empty($arResult['COMPANIES']) && count($arResult['COMPANIES']) > 1): ?>
            <div class="dropdown-menu w-100">
                <? foreach ($arResult['COMPANIES'] as $company) {
                    if ($company["ID_COMPANY"] == $_SESSION['AUTH_COMPANY_CURRENT_ID']) {
                        continue;
                    }
                    ?>
                    <span class="dropdown-item hstack"
                          data-company-id="<?= $company['ID_COMPANY'] ?>"
                          onclick="setCompanyID(<?= $company['ID_COMPANY'] ?>)"
                    >
                            <?= $company['COMPANY_NAME'] ?>
                     </span>
                    <?
                }
                ?>
            </div>
        <? endif; ?>
    </div>
    <?else:?>
        <span class="nbtn btn-link text-body text-start lh-1 p-2 my-1 w-100 b2b_company__single"
           title='<?= \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE_2") ?>'>
           <i class="ph ph-user"></i>
            <span class="ml-2 fw-semibold">
                <?= mb_strimwidth($arResult['CURRENT_COMPANY'], 0, 30, "...");?>
            </span>
        </span>
    <?endif;?>
    </div>
<? endif; ?>