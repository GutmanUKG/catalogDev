<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<? if (!empty($arResult['COMPANIES'])): ?>
    <?if (count($arResult['COMPANIES']) > 1):?>
        <li class="nav-item dropdown b2b-bavbar-choose">
        <a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown"
           title='<?= \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE") ?>'>
            <i class="icon-users2"></i>
            <span class="ml-2">
                <?= mb_strimwidth($arResult['CURRENT_COMPANY'], 0, 30, "...");?>
            </span>
        </a>

        <? if (!empty($arResult['COMPANIES']) && count($arResult['COMPANIES']) > 1): ?>
            <div class="dropdown-menu dropdown-menu-right">
                <? foreach ($arResult['COMPANIES'] as $company) {
                    if ($company["ID_COMPANY"] == $_SESSION['AUTH_COMPANY_CURRENT_ID']) {
                        continue;
                    }
                    ?>
                    <span class="dropdown-item"
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
    </li>
    <?else:?>
        <span class="navbar-nav-link b2b_company__single"
           title='<?= \Bitrix\Main\Localization\Loc::getMessage("SOTBIT_COMPANY_CHOOSE_DROPDOWN_TITLE_2") ?>'>
            <i class="icon-users2"></i>
            <span class="ml-2">
                <?= mb_strimwidth($arResult['CURRENT_COMPANY'], 0, 30, "...");?>
            </span>
        </span>
    <?endif;?>
<? endif; ?>