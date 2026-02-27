<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CUser $USER */

/** @global CMain $APPLICATION */

use Bitrix\Main\UI;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

UI\Extension::load("ui.tooltip");
$templateDir = Option::get('sotbit.b2bcabinet', 'method_install', '',
    SITE_ID) == 'AS_TEMPLATE' ? SITE_DIR . 'b2bcabinet/' : SITE_DIR;
?>
<li class="nav-item nav-user nav-item-dropdown-lg dropdown">
    <?
    if (strlen($arResult["FatalError"]) > 0)
    {
        ?><span class='errortext'><?= $arResult["FatalError"] ?></span><br/><br/><?
    }
    else
    {
    $anchor_id = RandString(8);

    if ($arParams["INLINE"] != "Y")
    {

    $tooltipUserId = (
    strlen($arResult["User"]["DETAIL_URL"]) > 0
    && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]
    && (
        !array_key_exists("USE_TOOLTIP", $arResult)
        || $arResult["USE_TOOLTIP"]
    )
        ? $arResult["User"]["ID"]
        : ''
    );

    if (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]) {
    ?>
    <a href="#" class="navbar-nav-link btn-transparent text-white align-items-center bx-user-info-anchor" id="anchor_<?= $anchor_id ?>"
         bx-tooltip-user-id="<?= $tooltipUserId ?>" data-bs-toggle="dropdown"><?
        } else {
        ?>
        <a href="#" class="navbar-nav-link btn-transparent text-white align-items-center bx-user-info-anchor-nolink" id="anchor_<?= $anchor_id ?>"
             bx-tooltip-user-id="<?= $tooltipUserId ?>" data-bs-toggle="dropdown"><?
            }
            ?>
            <div class="d-flex align-items-center"><?
                if ($arParams["USE_THUMBNAIL_LIST"] == "Y") {
                    if (strlen($arResult["User"]["HREF"]) > 0) {
                        ?>
                        <a class="rounded overflow-hidden" href="<?= $arResult["User"]["HREF"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>><?= $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] ?></a><?
                    } elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]) {
                        ?>
                        <a class="rounded overflow-hidden" href="<?= $arResult["User"]["DETAIL_URL"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>><?= $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] ?></a><?
                    } else {
                        ?><div class="rounded overflow-hidden"><?= $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] ?></div><?
                    }
                }
                ?>
                <?
                if (strlen($arResult["User"]["HREF"]) > 0) {
                    ?><a
                    class="bx-user-info-name d-lg-inline-block ms-2" href="<?= $arResult["User"]["HREF"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>><?= $arResult["User"]["NAME"] ? $arResult["User"]["NAME"] : $arResult["User"]["LAST_NAME"] ?></a><?
                } elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]) {
                    ?><a
                    class="bx-user-info-name d-lg-inline-block ms-2" href="<?= $arResult["User"]["DETAIL_URL"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>><?= $arResult["User"]["NAME"] ? $arResult["User"]["NAME"] : $arResult["User"]["LAST_NAME"] ?></a><?
                } else {
                    ?>
                    <div class="bx-user-info-name d-lg-inline-block ms-2"><?= $arResult["User"]["NAME"] ? $arResult["User"]["NAME"] : $arResult["User"]["LAST_NAME"] ?></div><?
                }
                ?><?= (strlen($arResult["User"]["NAME_DESCRIPTION"]) > 0 ? " (" . $arResult["User"]["NAME_DESCRIPTION"] . ")" : "") ?><?
                if ($arResult["bSocialNetwork"]) {
                    if (strlen($arResult["User"]["HREF"]) > 0) {
                        $link = $arResult["User"]["HREF"];
                    } elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]) {
                        $link = $arResult["User"]["DETAIL_URL"];
                    } else {
                        $link = false;
                    }
                    ?>
                    <?
                }
                ?>
            </div>
            <i class="ph ph-caret-down d-none d-sm-block p-1 ms-1"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-end">
            <a href="<?=$templateDir?>?tab=settings" class="dropdown-item">
                <i class="ph-gear me-2"></i>
                <?= Loc::getMessage('SETTINGS') ?>
            </a>
            <a href="?logout=yes&<?= bitrix_sessid_get() ?>" class="dropdown-item">
                <i class="ph-sign-out me-2"></i>
                <?= Loc::getMessage('LOGOUT') ?>
            </a>
        </div>
        <?
        }
        else {
            if (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]) {
                ?>
                <a href="<?= $arResult["User"]["DETAIL_URL"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>
                id="anchor_<?= $anchor_id ?>"
                bx-tooltip-user-id="<?= $arResult["User"]["ID"] ?>"><?= $arResult["User"]["NAME_FORMATTED"] ?></a><?
            } elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && !$arResult["bSocialNetwork"]) {
                ?>
                <a href="<?= $arResult["User"]["DETAIL_URL"] ?>"<?= ($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '') ?>
                id="anchor_<?= $anchor_id ?>"><?= $arResult["User"]["NAME_FORMATTED"] ?></a><?
            } else {
                ?><?= $arResult["User"]["NAME_FORMATTED"] ?><?
            }
            ?><?= (strlen($arResult["User"]["NAME_DESCRIPTION"]) > 0 ? " (" . $arResult["User"]["NAME_DESCRIPTION"] . ")" : "") ?><?
        }
        }
        ?>
    </li>
