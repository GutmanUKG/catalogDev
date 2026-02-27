<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Sotbit\B2bCabinet\Helper\Config;

$sitePath = Config::getMethodInstall(SITE_ID) === 'AS_TEMPLATE' ? '/b2bcabinet/' : SITE_DIR;
?>


<li class="nav-item nav-item-dropdown-xl dropdown dropdown-user h-100">
    <a href="#" class="navbar-nav-link d-flex align-items-center h-100 dropdown-toggle"  data-toggle="modal" data-target="#user-link__right-pannel">
        <?= $arResult["User"]["PersonalPhotoImgThumbnail"]["Image"] ?>
    </a>
</li>

<div id="user-link__right-pannel" class="modal modal-right fade" data-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-main text-white border-0">
                <h6 class="modal-title">
                    <?=GetMessage("PROFILE_RIGHT_PANEL_TITLE")?>
                </h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body p-0">

                <div class="card-body text-center">
                    <div class="mb-3">
                        <h6 class="font-weight-semibold mb-0 mt-1 user-link__name-formatted">
                            <?= $arResult["User"]["NAME_FORMATTED"] ?>
                        </h6>
                        <span class="d-block text-muted">
                             <?= $arResult["User"]["WORK_POSITION"] ?: ''?>
                        </span>
                    </div>
                    <span class="d-inline-block mb-3">
                        <?=$arResult["User"]["PersonalPhotoImgThumbnail"]["Deatil_Image"]?>
                    </span>
                </div>



                <div class="bg-light text-muted py-2 px-3">
                    <?=Loc::getMessage("PROFILE_RIGHT_PANEL_GROUP_NAVIGATION")?>
                </div>
                <div class="list-group border-0 rounded-0">
                    <a href="<?=$sitePath . "?tab=settings"?>" class="list-group-item list-group-item-action">
                        <i class="icon-cog5 mr-3"></i>
                        <?=Loc::getMessage("PROFILE_BTN_SETTINGS")?>
                    </a>
                    <a href="<?=$sitePath . "support/"?>" class="list-group-item list-group-item-action">
                        <i class="icon-clippy mr-3"></i>
                        <?=Loc::getMessage("PROFILE_BTN_SUPPORT")?>
                    </a>
                    <a href="?logout=yes&<?= bitrix_sessid_get() ?>" class="list-group-item list-group-item-action">
                        <i class="icon-switch2 mr-3"></i>
                        <?=Loc::getMessage("PROFILE_BTN_LOGOUT")?>
                    </a>
                </div>

                <?
                    $APPLICATION->IncludeComponent(
                        "sotbit:sotbit.personal.manager",
                        "b2bcabinet_right_panel",
                        array(
                            "COMPONENT_TEMPLATE" => "b2bcabinet_right_panel",
                            "SHOW_FIELDS" => array(
                                0 => "NAME",
                                1 => "PERSONAL_PHOTO",
                                2 => "WORK_PHONE",
                                3 => "UF_P_MANAGER_EMAIL",
                            ),
                            "USER_PROPERTY" => array(
                                0 => "UF_P_MANAGER_ID",
                            ),
                             "NAME_TEMPLATE" => $arGadget["SETTINGS"]["PERSONAL_MANAGER_NAME_TEMPLATE"] ?: "#NOBR##NAME# #LAST_NAME##/NOBR#"
                        ),
                        false
                    );
                ?>
            </div>
        </div>
    </div>
</div>


