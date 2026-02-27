<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

if ($USER->IsAuthorized()) {
    ?>
    <div class="row company-register__success-form">
        <div class="col-md-12">
            <div class="card card-body text-center">
                <div class="mx-auto mb-3 pb-1">
                    <svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_3305_29824)">
                            <path d="M36.5 72C56.3823 72 72.5 55.8823 72.5 36C72.5 16.1177 56.3823 0 36.5 0C16.6177 0 0.5 16.1177 0.5 36C0.5 55.8823 16.6177 72 36.5 72Z" fill="#32B76C"/>
                            <path d="M31.0437 54.6734L14.45 38.0797C14.225 37.8547 14.225 37.5172 14.45 37.2922L19.2313 32.5109C19.4563 32.2859 19.7938 32.2859 20.0188 32.5109L31.4375 43.9297L52.925 22.4422C53.15 22.2172 53.4875 22.2172 53.7125 22.4422L58.4937 27.2234C58.7188 27.4484 58.7188 27.7859 58.4937 28.0109L31.8313 54.6734C31.6063 54.8984 31.2687 54.8984 31.0437 54.6734Z" fill="white"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_3305_29824">
                                <rect width="72" height="72" fill="white" transform="translate(0.5)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="text-center w-sm-75 mx-auto">
                    <?= Loc::getMessage("MAIN_REGISTER_AUTH"); ?>
                    <div>
                        <a href="<?= $arParams["AUTH_URL"] ?>"
                        class="d-block btn btn-primary mt-2"><?= Loc::getMessage("REGISTER_SUCCESS_BTN") ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    return;
}

if ($arResult["USE_EMAIL_CONFIRMATION"] && $arResult['REGISTER_RESULT']['SUCCESS'] === true):?>
    <div class="row company-register__success-form">
        <div class="col-md-12">
            <div class="card card-body text-center">
                <div class="mx-auto mb-3 pb-1">
                    <svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_3305_29824)">
                            <path d="M36.5 72C56.3823 72 72.5 55.8823 72.5 36C72.5 16.1177 56.3823 0 36.5 0C16.6177 0 0.5 16.1177 0.5 36C0.5 55.8823 16.6177 72 36.5 72Z" fill="#32B76C"/>
                            <path d="M31.0437 54.6734L14.45 38.0797C14.225 37.8547 14.225 37.5172 14.45 37.2922L19.2313 32.5109C19.4563 32.2859 19.7938 32.2859 20.0188 32.5109L31.4375 43.9297L52.925 22.4422C53.15 22.2172 53.4875 22.2172 53.7125 22.4422L58.4937 27.2234C58.7188 27.4484 58.7188 27.7859 58.4937 28.0109L31.8313 54.6734C31.6063 54.8984 31.2687 54.8984 31.0437 54.6734Z" fill="white"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_3305_29824">
                                <rect width="72" height="72" fill="white" transform="translate(0.5)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="text-center w-sm-75 mx-auto">
                    <?= Loc::getMessage("REGISTER_EMAIL_SENT") ?>
                    <div>
                        <a href="<?= $arParams["AUTH_URL"] ?>"
                        class="d-block btn btn-primary mt-2"><?= Loc::getMessage("REGISTER_SUCCESS_BTN") ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    return;
endif; ?>

<div class="js_person_type auth-form">
    <div class="card">
        <div class="card-header card-pt-2">
            <h5 class="card-title mb-0 fw-bold"><?=GetMessage("AUTH_REGISTER")?></h5>
            <span class="card-subtitle"><?=GetMessage("AUTH_REGISTER_DESCRIPTION")?></span>
        </div>
        <div class="card-body pt-0">
            <div class="chouse-company mb-4">
                <div class="bitrix-error">
                    <? if (!empty($arResult['ERRORS'])) {
                        foreach ($arResult['ERRORS'] as $errorMessage) {
                            if (mb_detect_encoding($errorMessage, 'UTF-8, CP1251') == 'UTF-8') {
                                $errorMessage = mb_convert_encoding($errorMessage, LANG_CHARSET, 'UTF-8');
                            }
                            ShowError($errorMessage, 'validation-invalid-label');
                        }
                    }
                    ?>
                </div>
                
                <div class="form-group form-group-float">
                    <label class="d-block font-weight-semibold mb-3 fw-bold"><?= Loc::getMessage("AUTH_CHOOSE_USER_TYPE") ?></label>
                    <? foreach ($arResult['PERSON_TYPES'] as $key => $group): ?>
                        <div class="form-check form-check-inline">
                            <input type="radio"
                                    class="js_checkbox_person_type form-check-input REGISTER_WHOLESALER_TYPE"
                                    name="PERSON_TYPE"
                                    id="PERSON_TYPE_<?= $group['ID']; ?>"
                                    value="<?= $group['ID']; ?>"
                                <?
                                if (isset($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                                    echo 'checked';
                                } elseif ($key === array_key_first($arResult['PERSON_TYPES']) && is_null($arResult["VALUES"]['WHOLESALER_FIELDS'][$group['ID']])) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <label class="form-check-label" for="PERSON_TYPE_<?= $group['ID']; ?>"><?= $group['NAME']; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                            enctype="multipart/form-data" onsubmit="sendForm(event); return false;">
                        <input type="hidden" name="REGISTER_WHOLESALER[TYPE]" value="<?= $group['ID'] ?>">
                        <div class="mb-3">
                            <label class="d-block mb-3 fw-bold"><?= GetMessage("AUTH_COMMON_BLOCK_TITLE") ?></label>
                            <div class="row">
                                <? foreach ($arResult["USER_REGISTER_FIELDS"][$group['ID']] as $FIELD): ?>
                                    <?
                                    $required = in_array($FIELD,
                                                is_array($arResult['USER_REQUIRED_FIELDS'][$group['ID']]) ? $arResult['USER_REQUIRED_FIELDS'][$group['ID']] : []) ? 'required' : '';
                                    ?>
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            <?= ($arResult['REGISTER_UF_FIELDS'][$FIELD]['EDIT_FORM_LABEL'] ?: Loc::getMessage("REGISTER_FIELD_" . $FIELD) ?: $FIELD) . ':' ?>
                                            <?= in_array($FIELD,
                                                is_array($arResult['USER_REQUIRED_FIELDS'][$group['ID']]) ? $arResult['USER_REQUIRED_FIELDS'][$group['ID']] : []) ? '<span class="req">*</span>' : '' ?>
                                        </label>
                                        <div class="form-group form-group-feedback <?= $FIELD !== 'PERSONAL_BIRTHDAY' ? 'form-group-feedback-right' : '' ?>">
                                            <? if ($FIELD === 'PERSONAL_PHOTO' || $FIELD === 'WORK_LOGO'): ?>
                                                <input type="file" class="form-control mb-2"
                                                        name="REGISTER_WHOLESALER_FILES_<?= $FIELD ?>"/>
                                                <input type="hidden"
                                                        name="REGISTER_WHOLESALER_USER[<?= $group['ID'] ?>][<?= $FIELD ?>]"/>
                                            <? elseif ($FIELD === 'PERSONAL_BIRTHDAY'): ?>
                                                <input type="date"
                                                        class="form-control mb-2"
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
                                                        class="form-control mb-2"
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
                        <? if (isset($arResult['OPT_ORDER_FIELDS'][$group['ID']]) && !empty($arResult['OPT_ORDER_FIELDS'][$group['ID']])): ?>
                            <div class="mb-3">
                                <label class="d-block mb-3 fw-bold"><?= Loc::getMessage("AUTH_BLOCK_WHOLESALER_ORDER_TITLE") ?></label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <? foreach ($arResult['OPT_ORDER_FIELDS'][$group['ID']] as $order): ?>
                                            <?
                                            $fieldValue = $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']];
                                            ?>
                                            <? if ($order['NAME']): ?>
                                                <label class="form-label"><?= $order['NAME'] . ":" ?>
                                                    <?= $order["REQUIRED"] == "Y" ? ' <span class="req">*</span>' : "" ?></label>
                                                <? if ($order["TYPE"] == "ENUM" && $order["VARIANTS"]): ?>
                                                    <div class="mb-2">
                                                        <select
                                                                class="form-control select"
                                                                data-minimum-results-for-search="Infinity"
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
                                                        'location-block-wrapper mb-2'
                                                    );
                                                    ?>
                                                <? else: ?>
                                                    <? if ($order["MULTIPLE"] == 'Y'): ?>
                                                        <div class="mb-2 multiple-props">
                                                            <?
                                                            $valueMultiProp = '';
                                                            if(!empty($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']])){
                                                                $valueMultiProp = $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$order['CODE']];
                                                                $valueMultiProp = is_array($valueMultiProp) ? $valueMultiProp : [$valueMultiProp];
                                                            }

                                                            if (is_array($valueMultiProp)):
                                                                foreach($valueMultiProp as $key => $item): ?>
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
                                                                        <?= !empty($item) ? 'value="' . $item . '"' : '' ?>
                                                                        <?= $order['SETTINGS']['PATTERN'] ? "pattern='" . $order['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                            id="WHOLESALER_<?= $order['CODE'] ?>"
                                                                        <?= $order['DESCRIPTION'] ? "title='" . $order['DESCRIPTION'] . "'" : "" ?>
                                                                    >
                                                                    <?if ($key !== 0): ?>
                                                                        <div class="form-control-multiple position-absolute end-0 top-50 translate-middle-y me-1" 
                                                                             onclick="hideBlock(this)">
                                                                            <button 
                                                                                class="form-control-multiple-ic btn btn-sm btn-icon btn-link text-muted" 
                                                                                type="button">
                                                                                    <i class="ph-x fs-base"></i>
                                                                            </button>
                                                                        </div>
                                                                    <?endif; ?>
                                                                </div>
                                                                <? endforeach; ?>
                                                            <? else: ?>
                                                                <div class="form-control-multiple-wrap">
                                                                    <input <?=$order["TYPE"] != "NUMBER" ? ($order["CODE"] == "EMAIL" ? 'type="email"' : 'type="text"') : 'type="number"'?>
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
                                                            <?endif; ?>
                                                            <button
                                                                    type="button"
                                                                    class="btn"
                                                                    data-add-type=<?=$order["TYPE"] != "NUMBER" ? ($order["CODE"] == "EMAIL" ? '"email"' : '"text"') : '"number"'?>
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
                                                        <div class="mb-2">
                                                            <input
                                                                    <?=$order["TYPE"] != "NUMBER" ? ($order["CODE"] == "EMAIL" ? 'type="email"' : 'type="text"') : 'type="number"'?>
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
                        <? endif; ?>
                        <div class="mb-4">
                            <label class="d-block mb-3 fw-bold"><?= Loc::getMessage('AUTH_SAVE_OF_DATA') ?></label>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label"><?= Loc::getMessage("REGISTER_FIELD_PASSWORD") ?>:<span class="req">*</span>
                                    </label>
                                    <div class="form-control-feedback form-control-feedback-end mb-2">
                                        <input required type="password" class="form-control"
                                                placeholder="<?= Loc::getMessage("REGISTER_PLACEHOLDER_PASSWORD") ?>"
                                                name="REGISTER[PASSWORD]" maxlength="50"
                                                value=""
                                                autocomplete="off">
                                            <div class="form-control-feedback-icon">
                                                <i class="ph-lock-key"></i>
                                            </div>
                                    </div>
                                    <span class="d-block form-text text-muted mb-2"><?= $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"] ?></span>
                                    <label class="form-label"><?= Loc::getMessage("REGISTER_FIELD_CONFIRM_PASSWORD") ?>
                                        :<span class="req">*</span>
                                    </label>
                                    <div class="form-control-feedback form-control-feedback-end mb-2">
                                        <input required type="password" class="form-control"
                                                placeholder="<?= Loc::getMessage("REGISTER_PLACEHOLDER_CONFIRM_PASSWORD") ?>"
                                                name="REGISTER[CONFIRM_PASSWORD]" maxlength="50"
                                                value=""
                                                autocomplete="off">
                                        <div class="form-control-feedback-icon">
                                            <i class="ph-lock-key"></i>
                                        </div>
                                    </div>

                                    <? if ($arResult["USE_CAPTCHA"]): ?>
                                        <input type="hidden" name="captcha_sid" id="captcha_sid"
                                                value="<?= $arResult["CAPTCHA_CODE"] ?>"/>

                                        <label class="form-label">
                                            <?= Loc::getMessage("REGISTER_CAPTCHA_PROMT") ?>: <span class="req">*</span>
                                        </label>
                                        <div class="password_recovery-captcha_wrap d-flex align-items-center mb-2">
                                            <div class="bx-captcha">
                                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
                                                        width="180" height="40" alt="CAPTCHA">
                                            </div>
                                            <div class="form-group feedback_block__captcha_reload" role="button"
                                                    onclick="reloadCaptcha(this,'<?= SITE_DIR ?>');return false;"
                                                    title="<?= Loc::getMessage('REGISTER_CAPTCHA_RELOAD') ?>">
                                                    <i class="ph-arrows-counter-clockwise icon_refresh"></i>
                                            </div>
                                        </div>


                                        <div class="password_recovery-captcha">
                                            <div class="password_recovery-captcha_input">
                                                <input type="text" class="form-control" name="captcha_word"
                                                        maxlength="50" autocomplete="off" required
                                                        placeholder="<?= Loc::getMessage("REGISTER_CAPTCHA_PLACEHOLDER") ?>">
                                            </div>
                                        </div>

                                    <? endif ?>
                                    <div class="d-flex align-items-center mt-3">
                                        <input name="UF_CONFIDENTIAL" type="hidden" value="Y"/>

                                        <? $APPLICATION->IncludeComponent(
                                            "bitrix:main.userconsent.request",
                                            "b2bcabinet",
                                            array(
                                                "ID" => COption::getOptionString("main", "new_user_agreement",
                                                    "") ?: \COption::GetOptionString("sotbit.b2bcabinet",
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
                        <input type="hidden" name="sotbit_b2b_register"
                                value="<?= Loc::getMessage('AUTH_REGISTER_WORD') ?>"/>
                        <div class="btnBlock">
                            <button type="submit" class="btn btn-primary">
                                <?= Loc::getMessage('AUTH_REGISTER') ?>
                            </button>
                            <a href="?register=no" class="btn btn-link">
                                <?= Loc::getMessage('AUTH_AUTH') ?>
                            </a>
                        </div>

                    </form>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>
<script>
    var companyRegisterAgreementInput = 'UF_CONFIDENTIAL';
    var siteDir = '/';
</script>
