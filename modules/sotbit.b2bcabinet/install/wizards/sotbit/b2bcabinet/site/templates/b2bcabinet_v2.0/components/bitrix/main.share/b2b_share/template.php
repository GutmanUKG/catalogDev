<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
?>
<? if ($arResult["PAGE_URL"] <> ''): ?>

    <div class="card <?= $arParams['HIDE'] == 'Y' ? 'card-collapsed' : ''; ?>">
        <div class="card-header bg-transparent header-elements-inline">
            <span class="card-title font-weight-semibold"><?= Loc::getMessage('SHARE_TITLE') ?></span>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div>
            </div>
        </div>
        <div class="card-body py-0">
            <ul class="list-inline list-inline-condensed text-center social-container">
                <? if (is_array($arResult["BOOKMARKS"]) && count($arResult["BOOKMARKS"]) > 0): ?>
                    <? foreach ($arResult["BOOKMARKS"] as $name => $arBookmark): ?>
                        <li class="social-item">
                            <?= $arBookmark["ICON"] ?>
                        </li>
                    <? endforeach; ?>
                <? endif; ?>
            </ul>
        </div>
    </div>

<? else: ?>
    <?= GetMessage("SHARE_ERROR_EMPTY_SERVER") ?>
<? endif; ?>