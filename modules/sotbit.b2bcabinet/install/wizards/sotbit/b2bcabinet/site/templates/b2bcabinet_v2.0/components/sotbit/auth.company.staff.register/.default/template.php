<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>


<div id="modal-staff-register" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h5 class="modal-title">
                    <?=GetMessage("AUTH_REGISTER")?>
                </h5>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>
            <form name="regform" enctype="multipart/form-data">

                <div class="modal-body">
                    <p>
                        <?=GetMessage("COMPANY_REGISTER_REFERAL_LINK_TITLE_1")?>
                        <a class="register-referral-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-staff-referal-register"><?=GetMessage("COMPANY_REGISTER_REFERAL_LINK_TEXT")?></a>
                        <?=GetMessage("COMPANY_REGISTER_REFERAL_LINK_TITLE_2")?>
                    </p>
                    <div class="regform-error"></div>
                    <?
                    if ($arResult["BACKURL"] <> ''):
                        ?>
                        <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                    <?
                    endif;
                    ?>

                    <fieldset>
                    <? foreach ($arResult["SHOW_FIELDS"] as $FIELD): ?>
                        <? if ($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true): ?>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><? echo GetMessage("main_profile_time_zones_auto") ?><? if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?>
                                        <span class="starrequired">*</span><? endif ?></label>
                                <div class="col-lg-9">
                                    <select name="REGISTER[AUTO_TIME_ZONE]"
                                            onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
                                        <option value=""><? echo GetMessage("main_profile_time_zones_auto_def") ?></option>
                                        <option value="Y"<?= $arResult["VALUES"][$FIELD] == "Y" ? " selected=\"selected\"" : "" ?>><? echo GetMessage("main_profile_time_zones_auto_yes") ?></option>
                                        <option value="N"<?= $arResult["VALUES"][$FIELD] == "N" ? " selected=\"selected\"" : "" ?>><? echo GetMessage("main_profile_time_zones_auto_no") ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><? echo GetMessage("main_profile_time_zones_zones") ?></label>
                                <div class="col-lg-9">
                                    <select name="REGISTER[TIME_ZONE]"<? if (!isset($_REQUEST["REGISTER"]["TIME_ZONE"])) echo 'disabled="disabled"' ?>>
                                        <? foreach ($arResult["TIME_ZONE_LIST"] as $tz => $tz_name): ?>
                                            <option value="<?= htmlspecialcharsbx($tz) ?>"<?= $arResult["VALUES"]["TIME_ZONE"] == $tz ? " selected=\"selected\"" : "" ?>><?= htmlspecialcharsbx($tz_name) ?></option>
                                        <? endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <? else: ?>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><?= GetMessage("REGISTER_FIELD_" . $FIELD) ?>
                                    <? if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?><span
                                            class="starrequired">*</span><? endif ?></label>
                                <div class="col-lg-9"><?
                                    switch ($FIELD) {
                                        case "PASSWORD":
                                            ?><input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                                        value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off"
                                                        class="form-control" placeholder="<?=GetMessage("COMPANY_REGISTER_PLACEHOLDER_PASSWORD")?>"/>
                                        <?
                                        if ($arResult["SECURE_AUTH"]): ?>
                                            <span class="bx-auth-secure" id="bx_auth_secure" title="<?
                                            echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
                                        <div class="bx-auth-secure-icon"></div>
                                    </span>
                                            <noscript>
                                        <span class="bx-auth-secure" title="<?
                                        echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
                                            <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                        </span>
                                            </noscript>
                                            <script type="text/javascript">
                                                document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                            </script>
                                        <?
                                        endif ?>
                                            <p class="regform-password-requirements"><?=strripos($arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"], '0')!==false ? $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"] : str_replace('0', '6', $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"])?></p>
                                            <?
                                            break;
                                        case "CONFIRM_PASSWORD":
                                            ?><input size="30" type="password" name="REGISTER[<?= $FIELD ?>]"
                                                        class="form-control" value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" placeholder="<?=GetMessage("COMPANY_REGISTER_PLACEHOLDER_CONFIRM_PASSWORD")?>"/><?
                                            break;

                                        case "PERSONAL_GENDER":
                                            ?><select name="REGISTER[<?= $FIELD ?>]">
                                            <option value=""><?= GetMessage("USER_DONT_KNOW") ?></option>
                                            <option value="M"<?= $arResult["VALUES"][$FIELD] == "M" ? " selected=\"selected\"" : "" ?>><?= GetMessage("USER_MALE") ?></option>
                                            <option value="F"<?= $arResult["VALUES"][$FIELD] == "F" ? " selected=\"selected\"" : "" ?>><?= GetMessage("USER_FEMALE") ?></option>
                                            </select><?
                                            break;

                                        case "PERSONAL_COUNTRY":
                                        case "WORK_COUNTRY":
                                            ?><select name="REGISTER[<?= $FIELD ?>]"><?
                                            foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value) {
                                                ?>
                                                <option value="<?= $value ?>"<?
                                                if ($value == $arResult["VALUES"][$FIELD]):?> selected="selected"<? endif ?>><?= $arResult["COUNTRIES"]["reference"][$key] ?></option>
                                                <?
                                            }
                                            ?></select><?
                                            break;

                                        case "PERSONAL_PHOTO":
                                        case "WORK_LOGO":
                                            ?><input size="30" type="file" name="REGISTER_FILES_<?= $FIELD ?>" /><?
                                            break;

                                        case "PERSONAL_NOTES":
                                    case "WORK_NOTES":
                                        ?><textarea cols="30" rows="5"
                                                    name="REGISTER[<?= $FIELD ?>]"><?= $arResult["VALUES"][$FIELD] ?></textarea><?
                                    break;
                                    case "USER_GROUPS":
                                    $arr = array(
                                        "REFERENCE" => array_values($arResult["SELECT_USER_GROUPS"]),
                                        "REFERENCE_ID" => array_keys($arResult["SELECT_USER_GROUPS"])
                                    );
                                    ?>
                                        <select multiple="multiple" class="form-control select" name="REGISTER[<?=$FIELD?>][]" id="REGISTER[<?=$FIELD?>][]">
                                            <?foreach ($arResult["SELECT_USER_GROUPS"] as $valId => $value):?>
                                                <option value="<?=$valId?>"><?=$value?></option>
                                            <?endforeach;?>
                                        </select>
                                    <?
                                    break;
                                    case "STAFF_ROLE":
                                    foreach ($arResult["SELECT_STAFF_ROLES"] as $role){
                                        $arRoles[$role["CODE"]] = $role["NAME"];
                                    }
                                    $arr = array(
                                        "REFERENCE" =>
                                            array_values($arRoles),
                                        "REFERENCE_ID" =>
                                            array_keys($arRoles)
                                    );
                                    ?>
                                        <select multiple="multiple" class="form-control select" name="REGISTER[<?=$FIELD?>][]" id="REGISTER[<?=$FIELD?>][]">
                                            <?foreach ($arRoles as $valId => $value):?>
                                                <option value="<?=$valId?>"><?=$value?></option>
                                            <?endforeach;?>
                                        </select>
                                    <?
                                    break;
                                    default:
                                    if ($FIELD == "PERSONAL_BIRTHDAY"): ?>
                                        <small><?= $arResult["DATE_FORMAT"] ?></small><br/><?
                                    endif;
                                        ?><input size="30" type="text" name="REGISTER[<?= $FIELD ?>]" class="form-control"
                                                    value="<?= $arResult["VALUES"][$FIELD] ?>" placeholder="<?=GetMessage("COMPANY_REGISTER_PLACEHOLDER").strtolower(GetMessage("REGISTER_FIELD_" . $FIELD))?>"/><?
                                        if ($FIELD == "PERSONAL_BIRTHDAY") {
                                            $APPLICATION->IncludeComponent(
                                                'bitrix:main.calendar',
                                                '',
                                                array(
                                                    'SHOW_INPUT' => 'N',
                                                    'FORM_NAME' => 'regform',
                                                    'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                    'SHOW_TIME' => 'N'
                                                ),
                                                null,
                                                array("HIDE_ICONS" => "Y")
                                            );
                                        }
                                        ?><?
                                    } ?></div>
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                    </fieldset>
                    <? // ********************* User properties ***************************************************?>
                    <? if ($arResult["USER_PROPERTIES"]["SHOW"] == "Y"): ?>
                    <fieldset>
                        <legend class="fs-base fw-bold border-bottom pb-2 mb-3"><?= strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB") ?></legend>
                        <? foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><?= $arUserField["EDIT_FORM_LABEL"] ?>:<? if ($arUserField["MANDATORY"] == "Y"): ?><span
                                            class="starrequired">*</span><? endif; ?></label>
                                <div class="col-lg-9">
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:system.field.edit",
                                        $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                        array(
                                            "bVarsFromForm" => $arResult["bVarsFromForm"],
                                            "arUserField" => $arUserField,
                                            "form_name" => "regform"
                                        ), null, array("HIDE_ICONS" => "Y")); ?>
                                </div>>
                            </div>
                        <? endforeach; ?>
                    </fieldset>
                    <? endif; ?>
                    <?
                    if ($arResult["USE_CAPTCHA"] == "Y") {
                        ?>
                        <fieldset>
                            <legend class="fs-base fw-bold border-bottom pb-2 mb-3"><?= GetMessage("REGISTER_CAPTCHA_TITLE") ?></legend>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"></label>
                                <div class="col-lg-9">
                                    <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                            width="180" height="40" alt="CAPTCHA"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><?= GetMessage("REGISTER_CAPTCHA_PROMT") ?>:<span class="starrequired">*</span></label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off"/>
                                </div>>
                            </div>
                        </fieldset>
                        <?
                    }
                    ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <?=GetMessage("COMPANY_REGISTER_RESET_BUTTON_TEXT")?>
                    </button>
                    <button type="button" name="register_submit_button" class="btn btn-primary">
                        <?=GetMessage("COMPANY_REGISTER_SUBMIT_BUTTON_TEXT")?>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="modal-staff-referal-register" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h5 class="modal-title">
                    <?=GetMessage("AUTH_REGISTER")?>
                </h5>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="referralform" name="referralform" class="referral-form" enctype="multipart/form-data">
                    <div class="error-block"></div>
                    <div class="staff-register__referal">
                        <div class="row mb-3">
                            <label class="col-lg-3 col-form-label"><?=GetMessage("REGISTER_REFERRAL_FIELD_EMAIL")?></label>
                            <div class="col-lg-9">
                                <input class="referral__input-email form-control" type="email" name="REGISTER[EMAIL]"
                                    placeholder="<?= GetMessage("COMPANY_REGISTER_REFERRAL_PLACEHOLDER_EMAIL") ?>"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-3 col-form-label"><?=GetMessage("REGISTER_FIELD_STAFF_ROLE")?></label>
                            <div class="col-lg-9">
                                <?
                                foreach ($arResult["SELECT_STAFF_ROLES"] as $role){
                                    $arRoles[$role["CODE"]] = $role["NAME"];
                                }
                                $arr = array(
                                    "REFERENCE" =>
                                        array_values($arRoles),
                                    "REFERENCE_ID" =>
                                        array_keys($arRoles)
                                );
                                ?>
                                <select multiple="multiple" class="form-control select" name="REGISTER[STAFF_ROLE][]">
                                    <?foreach ($arRoles as $valId => $value):?>
                                        <option value="<?=$valId?>"><?=$value?></option>
                                    <?endforeach;?>
                                </select>
                            </div>
                        </div>
                        <?if($arResult["SELECT_USER_GROUPS"]):?>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label"><?=GetMessage("REGISTER_FIELD_USER_GROUPS")?></label>
                                <div class="col-lg-9">
                                    <?
                                    $arr = array(
                                        "REFERENCE" => array_values($arResult["SELECT_USER_GROUPS"]),
                                        "REFERENCE_ID" => array_keys($arResult["SELECT_USER_GROUPS"])
                                    );
                                    ?>
                                    <select multiple="multiple" class="form-control select" name="REGISTER[USER_GROUPS][]">
                                        <?foreach ($arResult["SELECT_USER_GROUPS"] as $valId => $value):?>
                                            <option value="<?=$valId?>"><?=$value?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal" name="referral_exit_button">
                    <?=GetMessage("COMPANY_REGISTER_REFERRAL_BTN_CANCEL")?>
                </button>
                <button type="button" class="btn btn-primary btn_confirm" name="referral_submit_button">
                    <?=GetMessage("COMPANY_REGISTER_REFERRAL_BTN_SUBMIT")?>
                </button>
            </div>
        </div>
    </div>
</div>

<button type="button" data-bs-toggle="modal" data-bs-target="#modal-staff-confirm" style="display: none"></button>

<div id="modal-staff-confirm" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header gradient-modal text-white">
                <h5 class="modal-title">
                    <?=GetMessage("AUTH_REGISTER")?>
                </h5>
                <button type="button" class="btn-close btn-close_color_white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6><?=GetMessage("COMPANY_REGISTER_CONFIRM_BLOCK_TEXT_1")?></h6>
                <p><?=GetMessage("COMPANY_REGISTER_CONFIRM_BLOCK_TEXT_2")?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <?=GetMessage("COMPANY_REGISTER_CONFIRM_BTN_CANCEL")?>
                </button>
                <button type="button" class="btn btn-primary btn_confirm">
                    <?=GetMessage("COMPANY_REGISTER_CONFIRM_BTN_CONFIRM")?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.arCompStaffRegisterParams = "<?=$this->__component->getSignedParameters();?>";
</script>
