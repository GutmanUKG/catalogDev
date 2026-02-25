<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

if ($USER->IsAuthorized()) {
    ?>
    <div class="row company-register__success-form justify-content-center">
        <div class="col-sm-12 col-md-4">
            <div class="card card-body border-top-success">
                <div class="text-center">
                    <?= Loc::getMessage("MAIN_REGISTER_AUTH") ?>
                </div>
                <div class="text-center">
                    <a href="<?= $arParams["AUTH_URL"] ?>"
                       class="btn bg-success"><?= Loc::getMessage("REGISTER_SUCCESS_BTN") ?><i
                                class="icon-circle-right2 ml-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?
    return;
}

if ($arResult["USE_EMAIL_CONFIRMATION"] && $arResult['REGISTER_RESULT']['SUCCESS'] === true):?>
    <div class="row company-register__success-form justify-content-center">
        <div class="col-sm-12 col-md-4">
            <div class="card card-body border-top-success">
                <div class="text-center">
                    <?= Loc::getMessage("REGISTER_EMAIL_SENT") ?>
                </div>
                <div class="text-center">
                    <a href="<?= $arParams["AUTH_URL"] ?>"
                       class="btn bg-success"><?= Loc::getMessage("REGISTER_SUCCESS_BTN") ?><i
                                class="icon-circle-right2 ml-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?
    return;
endif; ?>

<div class="row js_person_type auth-form">
    <div class="col-lg-12">
        <div class="card mb-0">
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="icon-plus3 icon-2x text-success border-success border-3 rounded-round p-3 mb-3 mt-1"></i>
                    <h5 class="mb-0"><?= Loc::getMessage("AUTH_REGISTER") ?></h5>
                    <span class="d-block text-muted"><?= Loc::getMessage("AUTH_REGISTER_DESCRIPTION") ?></span>
                </div>
                <div class="chouse-company">
                    <div class="bitrix-error">
                        <? if (!empty($arResult['ERRORS'])) {
                            foreach ($arResult['ERRORS'] as $errorMessage) {
                                if (mb_detect_encoding($errorMessage, 'UTF-8, CP1251') == 'UTF-8') {
                                    $errorMessage = mb_convert_encoding($errorMessage, LANG_CHARSET, 'UTF-8');
                                }
                                ShowError($errorMessage);
                            }
                        }
                        ?>
                    </div>

                    <label class="d-block font-weight-semibold"><?= Loc::getMessage("AUTH_CHOOSE_USER_TYPE") ?></label>
                    <? foreach ($arResult['PERSON_TYPES'] as $key => $group): ?>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <div class="uniform-choice">
                                    <input type="radio"
                                           class="js_checkbox_person_type form-check-input-styled REGISTER_WHOLESALER_TYPE"
                                           name="PERSON_TYPE"
                                           value="<?= $group['ID']; ?>"
                                        <?
                                        if (isset($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                                            echo 'checked';
                                        } elseif ($key === array_key_first($arResult['PERSON_TYPES']) && is_null($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                                            echo 'checked';
                                        }
                                        ?>
                                           data-fouc
                                    >
                                </div>
                                <?= $group['NAME']; ?>
                            </label>

                        </div>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($arResult['PERSON_TYPES'] as $key => $group): ?>
                    <div class="js_person_type_block js_person_type_<?= $group['ID'] ?>"<? if ($key != 0): ?> style="display: none;"<? endif; ?>
                        <?
                        if (isset($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                            echo 'checked';
                        } elseif ($key == '0' && is_null($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                            echo 'checked';
                        }
                        ?>
                    >
                        <form method="post" action="<?= POST_FORM_ACTION_URI ?>" class="flex-fill"
                              enctype="multipart/form-data">
                            <input type="hidden" name="REGISTER_WHOLESALER[TYPE]" value="<?= $group['ID'] ?>">
                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <h5 class="card-title"><?= GetMessage("AUTH_COMMON_BLOCK_TITLE") ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <? foreach ($arResult["USER_REGISTER_FIELDS"][$group['ID']] as $FIELD): ?>
                                            <?
                                            $required = in_array($FIELD,
                                                $arResult['USER_REQUIRED_FIELDS'][$group['ID']]) ? 'required' : '';
                                            ?>
                                            <div class="col-md-12">
                                                <label>
                                                    <?= ($arResult['REGISTER_UF_FIELDS'][$FIELD]['EDIT_FORM_LABEL'] ?: Loc::getMessage("REGISTER_FIELD_" . $FIELD) ?: $FIELD) . ':' ?>
                                                    <?= in_array($FIELD,
                                                        $arResult['USER_REQUIRED_FIELDS'][$group['ID']]) ? '<span>*</span>' : '' ?>
                                                </label>
                                                <div class="form-group form-group-feedback <?= $FIELD !== 'PERSONAL_BIRTHDAY' ? 'form-group-feedback-right' : '' ?>">
                                                    <? if ($FIELD === 'PERSONAL_PHOTO' || $FIELD === 'WORK_LOGO'): ?>
                                                        <input type="file" class="form-control"
                                                               name="REGISTER_WHOLESALER_FILES_<?= $FIELD ?>"/>
                                                        <input type="hidden"
                                                               name="REGISTER_WHOLESALER_USER[<?= $group['ID'] ?>][<?= $FIELD ?>]"/>
                                                    <? elseif ($FIELD === 'PERSONAL_BIRTHDAY'): ?>
                                                        <input type="date"
                                                               class="form-control"
                                                               name="REGISTER_WHOLESALER_USER[<?= $group['ID'] ?>][<?= $FIELD ?>]"
                                                            <?
                                                            $fieldValue = '';
                                                            if (!empty($arResult["VALUES"]['FIELDS'][$FIELD])) {
                                                                $fieldValue = $arResult["VALUES"]['FIELDS'][$FIELD];
                                                            } elseif ($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']][$FIELD]) {
                                                                $fieldValue = $arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']][$FIELD];
                                                            }

                                                            if (!empty($fieldValue)) {
                                                                echo 'value="' . date("Y-m-d",
                                                                        strtotime($fieldValue)) . '"';
                                                            }
                                                            ?>
                                                               autocomplete="off"
                                                        >
                                                    <? else: ?>
                                                        <input <?= $required ?>
                                                                type="<?= $FIELD === 'EMAIL' ? 'email' : 'text' ?>"
                                                                class="form-control"
                                                                name="REGISTER_WHOLESALER_USER[<?= $group['ID'] ?>][<?= $FIELD ?>]"
                                                                maxlength="50"
                                                            <?
                                                            $fieldValue = '';
                                                            if (!empty($arResult["VALUES"]['FIELDS'][$FIELD])) {
                                                                $fieldValue = $arResult["VALUES"]['FIELDS'][$FIELD];
                                                            } elseif ($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']][$FIELD]) {
                                                                $fieldValue = $arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']][$FIELD];
                                                            }

                                                            if (!empty($fieldValue)) {
                                                                echo 'value="' . $fieldValue . '"';
                                                            }
                                                            ?>
                                                                autocomplete="off"
                                                                placeholder="<?= Loc::getMessage('REGISTER_FIELD_PLACEHOLDER',
                                                                    ['#FIELD#' => mb_strtolower(Loc::getMessage('REGISTER_FIELD_' . $FIELD)) ?: Loc::getMessage('REGISTER_FIELD_PLACEHOLDER_DEFAULT')]) ?>"
                                                        >
                                                    <? endif; ?>
                                                </div>
                                            </div>
                                        <? endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <? if (isset($arResult['OPT_ORDER_FIELDS'][$group['ID']]) && !empty($arResult['OPT_ORDER_FIELDS'][$group['ID']])): ?>
                                <div class="card">
                                    <div class="card-header header-elements-inline">
                                        <h5 class="card-title"><?= Loc::getMessage("AUTH_BLOCK_WHOLESALER_ORDER_TITLE") ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <? foreach ($arResult['OPT_ORDER_FIELDS'][$group['ID']] as $order): ?>
                                                    <?
                                                    $fieldValue = $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']];
                                                    ?>
                                                    <? if ($order['NAME']): ?>
                                                        <label><?= $order['NAME'] . ":" ?>
                                                            <?= $order["REQUIRED"] == "Y" ? " <span>*</span>" : "" ?></label>
                                                        <? if ($order["TYPE"] == "ENUM" && $order["VARIANTS"]): ?>
                                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                                <select
                                                                        class="form-control"
                                                                        name="REGISTER_WHOLESALER_OPT[<?= $group['ID']; ?>][<?= $order['CODE'] ?>]<?= $order['MULTIPLE'] == "Y" ? "[]" : "" ?>"
                                                                        id="WHOLESALER_<?= $order['CODE'] ?>"
                                                                    <?= $order['MULTIPLE'] == "Y" ? "multiple" : "" ?>
                                                                    <?= $order['REQUIRED'] == 'Y' ? 'required' : '' ?>
                                                                >
                                                                    <? if (!$order["DEFAULT_VALUE"]): ?>
                                                                        <option disabled
                                                                                selected><?= Loc::getMessage("REGISTER_FIELD_TYPE_ENUM") ?></option>
                                                                    <? endif; ?>
                                                                    <? foreach ($order["VARIANTS"] as $variant): ?>
                                                                        <option
                                                                                value="<?= $variant["ID"] ?>"
                                                                            <?
                                                                            if ($variant["ID"] === $fieldValue) {
                                                                                echo "selected";
                                                                            }

                                                                            if ($order["MULTIPLE"] === "Y" && is_array($fieldValue) && in_array($variant["ID"],
                                                                                    $fieldValue)) {
                                                                                echo "selected";
                                                                            }

                                                                            if ($order["MULTIPLE"] === "Y" && is_array($order["DEFAULT_VALUE"]) && in_array($variant["ID"],
                                                                                    $order["DEFAULT_VALUE"])) {
                                                                                echo "selected";
                                                                            }

                                                                            if ($variant["ID"] === $order["DEFAULT_VALUE"]) {
                                                                                echo "selected";
                                                                            }
                                                                            ?>
                                                                        ><?= $variant["NAME"] ?></option>
                                                                    <? endforeach; ?>
                                                                </select>
                                                            </div>
                                                        <? elseif ($order["TYPE"] == "LOCATION"): ?>
                                                            <?
                                                            $locationTemplate = "";
                                                            $locationClassName = 'location-block-wrapper';
                                                            $locationClassName .= ' location-block-wrapper-delimeter';

                                                            CSaleLocation::proxySaleAjaxLocationsComponent(
                                                                array(
                                                                    "AJAX_CALL" => "N",
                                                                    'CITY_OUT_LOCATION' => 'Y',
                                                                    'COUNTRY_INPUT_NAME' => "REGISTER_WHOLESALER_OPT[" . $group['ID'] . "][" . $order['CODE'] . '_COUNTRY' . "]",
                                                                    'CITY_INPUT_NAME' => "REGISTER_WHOLESALER_OPT[" . $group['ID'] . "][" . $order['CODE'] . "]",
                                                                    'LOCATION_VALUE' => $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']] ?: '',
                                                                ),
                                                                array(),
                                                                $locationTemplate,
                                                                true,
                                                                'location-block-wrapper'
                                                            );
                                                            ?>
                                                        <? else: ?>
                                                            <? if ($order["MULTIPLE"] == 'Y'): ?>
                                                                <div class="form-group form-group-feedback form-group-feedback-right multiple-props">
                                                                    <?
                                                                    $valueMultiProp = '';
                                                                    if(!empty($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']])){
                                                                        $valueMultiProp = $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']];
                                                                        $valueMultiProp = is_array($valueMultiProp) ? $valueMultiProp : [$valueMultiProp];
                                                                    }

                                                                    if (is_array($valueMultiProp)){
                                                                        foreach ($valueMultiProp as $key => $val){
                                                                            ?>
                                                                            <div class="form-control-multiple-wrap">
                                                                                <input type="text"
                                                                                       class="form-control"
                                                                                       placeholder="<?= $order['NAME'] ?><?= $order['DESCRIPTION'] ? " " . $order['DESCRIPTION'] : '' ?>"
                                                                                       name="REGISTER_WHOLESALER_OPT[<?= $group['ID']; ?>][<?= $order['CODE'] ?>][]"
                                                                                    <?= $order['REQUIRED'] == 'Y' ? 'required' : '' ?>
                                                                                       maxlength="<?=
                                                                                       !empty($order['SETTINGS']['MAXLENGTH']) ? $order['SETTINGS']['MAXLENGTH'] :
                                                                                           (!empty($order['SETTINGS']['SIZE']) ? $order['SETTINGS']['SIZE'] : 50)
                                                                                       ?>"
                                                                                       minlength="<?= !empty($order['SETTINGS']['MINLENGTH']) ? $order['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                                    <?= !empty($val) ? 'value="' . $val . '"' : '' ?>
                                                                                    <?= $order['SETTINGS']['PATTERN'] ? "pattern='" . $order['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                                       id="WHOLESALER_<?= $order['CODE'] ?>"
                                                                                    <?= $order['DESCRIPTION'] ? "title='" . $order['DESCRIPTION'] . "'" : "" ?>
                                                                                >
                                                                                <?if (array_key_first($valueMultiProp) != $key) {?>
                                                                                    <div class="form-control-multiple" onclick="hideBlock(this)"><button class="form-control-multiple-ic btn btn-link" type="submit"><i class="icon-close2"></i></button></div>
                                                                                <?}?>
                                                                            </div>
                                                                        <?}
                                                                    } else {
                                                                        ?>
                                                                        <div class="form-control-multiple-wrap">
                                                                            <input type="text"
                                                                                   class="form-control"
                                                                                   placeholder="<?= $order['NAME'] ?><?= $order['DESCRIPTION'] ? " " . $order['DESCRIPTION'] : '' ?>"
                                                                                   name="REGISTER_WHOLESALER_OPT[<?= $group['ID']; ?>][<?= $order['CODE'] ?>][]"
                                                                                <?= $order['REQUIRED'] == 'Y' ? 'required' : '' ?>
                                                                                   maxlength="<?=
                                                                                   !empty($order['SETTINGS']['MAXLENGTH']) ? $order['SETTINGS']['MAXLENGTH'] :
                                                                                       (!empty($order['SETTINGS']['SIZE']) ? $order['SETTINGS']['SIZE'] : 50)
                                                                                   ?>"
                                                                                   minlength="<?= !empty($order['SETTINGS']['MINLENGTH']) ? $order['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                                <?= !empty($val) ? 'value="' . $val . '"' : '' ?>
                                                                                <?= $order['SETTINGS']['PATTERN'] ? "pattern='" . $order['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                                   id="WHOLESALER_<?= $order['CODE'] ?>"
                                                                                <?= $order['DESCRIPTION'] ? "title='" . $order['DESCRIPTION'] . "'" : "" ?>
                                                                            >
                                                                        </div>
                                                                        <?
                                                                    }?>
                                                                    <button
                                                                            type="button"
                                                                            class="btn btn-light mt-2"
                                                                            data-add-type="text"
                                                                            data-add-placeholder="<?= $order['NAME'] ?><?= $order['DESCRIPTION'] ? " " . $order['DESCRIPTION'] : '' ?>"
                                                                            data-add-name="REGISTER_WHOLESALER_OPT[<?= $group['ID']; ?>][<?= $order['CODE'] ?>][]"
                                                                            data-add-maxlength="<?=
                                                                            !empty($order['SETTINGS']['MAXLENGTH']) ? $order['SETTINGS']['MAXLENGTH'] :
                                                                                (!empty($order['SETTINGS']['SIZE']) ? $order['SETTINGS']['SIZE'] : 50)
                                                                            ?>"
                                                                            data-add-minlength="<?= !empty($order['SETTINGS']['MINLENGTH']) ? $order['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                    >
                                                                        <?= Loc::getMessage('REGISTER_BTN_MULTIPLE') ?>
                                                                    </button>
                                                                </div>
                                                            <? else: ?>
                                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                                    <input
                                                                        <?=$order["TYPE"] == "NUMBER" ? 'type="number"' : 'type="text"'?>
                                                                           class="form-control"
                                                                           placeholder="<?= $order['NAME'] ?><?= $order['DESCRIPTION'] ? " " . $order['DESCRIPTION'] : '' ?>"
                                                                           name="REGISTER_WHOLESALER_OPT[<?= $group['ID']; ?>][<?= $order['CODE'] ?>]"
                                                                        <?= $order['REQUIRED'] == 'Y' ? 'required' : '' ?>
                                                                           maxlength="<?=
                                                                           !empty($order['SETTINGS']['MAXLENGTH']) ? $order['SETTINGS']['MAXLENGTH'] :
                                                                               (!empty($order['SETTINGS']['SIZE']) ? $order['SETTINGS']['SIZE'] : 50)
                                                                           ?>"
                                                                           minlength="<?= !empty($order['SETTINGS']['MINLENGTH']) ? $order['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                        <?= !empty($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']]) ? 'value="' . htmlspecialcharsbx($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']]) . '"' : '' ?>
                                                                        <?= $order['SETTINGS']['PATTERN'] ? "pattern='" . $order['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                           id="WHOLESALER_<?= $order['CODE'] ?>"
                                                                        <?= $order['DESCRIPTION'] ? "title='" . $order['DESCRIPTION'] . "'" : "" ?>
                                                                    >
                                                                </div>
                                                            <? endif; ?>
                                                        <? endif; ?>
                                                    <? endif; ?>
                                                    <? if (is_array($order['CODE']) && $order['CODE']['FILE'] == 'Y'): ?>
                                                        <?
                                                        $APPLICATION->IncludeComponent(
                                                            "bitrix:main.file.input",
                                                            "auth_drag_n_drop",
                                                            [
                                                                "INPUT_NAME" => "FILES",
                                                                "MULTIPLE" => "Y",
                                                                "MODULE_ID" => "main",
                                                                "MAX_FILE_SIZE" => "",
                                                                "ALLOW_UPLOAD" => "F",
                                                                "ALLOW_UPLOAD_EXT" => "",
                                                                "TAB_ID" => $group['ID']
                                                            ],
                                                            false
                                                        );
                                                        ?>
                                                    <? endif; ?>
                                                <? endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? endif; ?>
                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <h5 class="card-title"><?= Loc::getMessage('AUTH_SAVE_OF_DATA') ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label><?= Loc::getMessage("REGISTER_FIELD_PASSWORD") ?>:<span>*</span>
                                            </label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input required type="password" class="form-control"
                                                       placeholder="<?= Loc::getMessage("REGISTER_PLACEHOLDER_PASSWORD") ?>"
                                                       name="REGISTER[PASSWORD]" maxlength="50"
                                                       value=""
                                                       autocomplete="off">
                                                <span class="form-text text-muted"><?= $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"] ?></span>
                                                <div class="form-control-feedback">
                                                    <i class="icon-user-lock text-muted"></i>
                                                </div>
                                            </div>
                                            <label><?= Loc::getMessage("REGISTER_FIELD_CONFIRM_PASSWORD") ?>
                                                :<span>*</span> </label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input required type="password" class="form-control"
                                                       placeholder="<?= Loc::getMessage("REGISTER_PLACEHOLDER_CONFIRM_PASSWORD") ?>"
                                                       name="REGISTER[CONFIRM_PASSWORD]" maxlength="50"
                                                       value=""
                                                       autocomplete="off">
                                                <div class="form-control-feedback">
                                                    <i class="icon-user-lock text-muted"></i>
                                                </div>
                                            </div>

                                            <? if ($arResult["USE_CAPTCHA"]): ?>
                                                <input type="hidden" name="captcha_sid" id="captcha_sid"
                                                       value="<?= $arResult["CAPTCHA_CODE"] ?>"/>

                                                <label>
                                                    <?= Loc::getMessage("REGISTER_CAPTCHA_PROMT") ?>: <span>*</span>
                                                </label>
                                                <div class="password_recovery-captcha_wrap">
                                                    <div class="bx-captcha form-group form-group-feedback form-group-feedback-right">
                                                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                                             width="180" height="40" alt="CAPTCHA">
                                                    </div>
                                                    <div class="form-group feedback_block__captcha_reload"
                                                         onclick="reloadCaptcha(this,'<?= SITE_DIR ?>');return false;"
                                                         title="<?= Loc::getMessage('REGISTER_CAPTCHA_RELOAD') ?>">
                                                        <svg class="icon_refresh" width="16" height="14">
                                                            <use xlink:href="/local/templates/b2bcabinet/assets/images/sprite.svg#icon_refresh"></use>
                                                        </svg>
                                                    </div>
                                                </div>


                                                <div class="password_recovery-captcha">
                                                    <div class="form-group form-group-feedback form-group-feedback-right password_recovery-captcha_input">
                                                        <input type="text" class="form-control" name="captcha_word"
                                                               maxlength="50" autocomplete="off" required
                                                               placeholder="<?= Loc::getMessage("REGISTER_CAPTCHA_PLACEHOLDER") ?>">
                                                    </div>
                                                </div>

                                            <? endif ?>
                                            <div class="d-flex align-items-center">
                                                <input name="UF_CONFIDENTIAL" type="hidden" value="Y"/>

                                                <? $APPLICATION->IncludeComponent(
                                                    "bitrix:main.userconsent.request",
                                                    "b2bcabinet",
                                                    array(
                                                        "ID" => \COption::GetOptionString("sotbit.b2bcabinet",
                                                            "AGREEMENT_ID"),
                                                        "IS_CHECKED" => "Y",
                                                        "AUTO_SAVE" => "Y",
                                                        "IS_LOADED" => "Y",
                                                        "ORIGINATOR_ID" => $arResult["AGREEMENT_ORIGINATOR_ID"],
                                                        "ORIGIN_ID" => $arResult["AGREEMENT_ORIGIN_ID"],
                                                        "INPUT_NAME" => $arResult["AGREEMENT_INPUT_NAME"],
                                                        "REPLACE" => array(
                                                            "button_caption" => GetMessage("AUTH_REGISTER"),
                                                            "fields" => array(
                                                                rtrim(GetMessage("AUTH_NAME"), ":"),
                                                                rtrim(GetMessage("AUTH_LAST_NAME"), ":"),
                                                                rtrim(GetMessage("AUTH_LOGIN_MIN"), ":"),
                                                                rtrim(GetMessage("AUTH_PASSWORD_REQ"), ":"),
                                                                rtrim(GetMessage("AUTH_EMAIL"), ":"),
                                                            )
                                                        ),
                                                    )
                                                ); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="sotbit_b2b_register"
                                   value="<?= Loc::getMessage('AUTH_REGISTER_WORD') ?>"/>
                            <div class="btnBlock">
                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right"><b><i
                                                class="icon-plus3"
                                                name="register"></i></b><?= Loc::getMessage('AUTH_REGISTER') ?>
                                </button>
                                <a href="?register=no"
                                   class="btnBlock__authToLink"><?= Loc::getMessage('AUTH_AUTH') ?></a>
                            </div>

                        </form>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
</div>