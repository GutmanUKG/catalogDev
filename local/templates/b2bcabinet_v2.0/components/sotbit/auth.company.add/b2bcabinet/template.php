<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

if (isset($_REQUEST['EDIT_ID'])) {
    $APPLICATION->AddChainItem(Loc::getMessage('SOA_PROFILE_EDIT'));
} else {
    $APPLICATION->AddChainItem(Loc::getMessage('SOA_PROFILE_ADD'));
}
?>

<div class="add-company__wrapper">
    <? if ($arResult["ERRORS_FATAL"]): ?>
        <?
        foreach ($arResult["ERRORS_FATAL"] as $strError) {
            ShowError($strError, 'validation-invalid-label');
        }
        ?>
    <? elseif ($arResult["COMPANY_ADD_MODERATE_OK"]): ?>
        <?= Loc::getMessage('SOA_COMPANY_CONFIRM', ["#COMPANY_LIST#" => $arParams["PATH_TO_LIST"]]) ?>
    <? elseif ($arResult["COMPANY_ADD_OK"]): ?>
        <?= Loc::getMessage('SOA_COMPANY_ADD', ["#COMPANY_LIST#" => $arParams["PATH_TO_LIST"]]) ?>
    <? else: ?>
        
        <? if ($arResult['RESULT_MESSAGE']): ?>
            <div class="alert alert-success border-0 alert-dismissible">
                <i class="ph-check-circle me-2"></i>
                <?= $arResult['RESULT_MESSAGE'] ?>
            </div>
        <? endif; ?>
        <?
        if (is_array($arResult["ERRORS"]) && count($arResult["ERRORS"]) > 0) {
            foreach ($arResult["ERRORS"] as $strError) {
                ShowError($strError, 'validation-invalid-label');
            }
        }
        ?>

        <? if ($arResult["USE_PERSONAL_GROUPS"]): ?>
        <span class="d-block text-muted mb-2"><?= $arResult["PERSONAL_GROUPS_LIST_TITLE"] ?: Loc::getMessage('PERSONAL_GROUPS_LIST_TITLE') ?></span>
        <div class="detail-menu d-flex justify-content-between align-items-center overflow-auto mb-4">
            <ul class="nav nav-tabs nav-mainpage-tabs">
                <? foreach ($arResult["PERSONAL_GROUPS_LIST"] as $groupId => $groupList): ?>
                    <li class="nav-item">
                        <a href="#person-group-<?= $groupId ?>"
                            class="nav-link <?= $arResult["PERSONAL_ACTIVE_GROUP"] == $groupId ? 'active' : '' ?>"
                            data-bs-toggle="tab" role="tab">
                            <?= $groupList["VALUE"] ?>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
        <?else:?>
        <?
            $arResult["PERSONAL_GROUPS_LIST"][1] = true;
            $arResult["PERSONAL_ACTIVE_GROUP"] = 1;
            ?>
        <?endif;?>
            <div class="tab-content <?=(isset($_REQUEST['EDIT_ID']) && !empty($arResult["USE_PERSONAL_GROUPS"])) ? 'mt-4' : ''?>">
                <? foreach ($arResult["PERSONAL_GROUPS_LIST"] as $groupId => $groupList): ?>
                    <div class="tab-personal-group tab-pane fade <?= $arResult["PERSONAL_ACTIVE_GROUP"] == $groupId ? 'active show' : '' ?>"
                            id="person-group-<?= $groupId ?>">

                        <form name="addOrg" method="post" id="add-org"
                                class="sale-profile-detail-form"
                                action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data"
                                onsubmit="submitForm();return false;">
                            <?= bitrix_sessid_post() ?>
                            <input type="hidden" id="p_type_id" name="ID"
                                    value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">
                            <input type="hidden" id="apply" name="apply" value="">

                            <input type="hidden" id="change_person_type" name="change_person_type"
                                    value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">
                            <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE"
                                    value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">

                            <div class="card">
                                <div class="card-body">
                                    <label class="form-label mb-0"><?= Loc::getMessage('SOA_SALE_PERS_TYPE') ?>:</label>
                                    <? if (!isset($_REQUEST['EDIT_ID'])):?>
                                        <div class="mt-2">
                                            <? foreach ($arResult['PERSON_TYPES'] as $id => $name) : ?>
                                            <? 
                                                if ($arResult["USE_PERSONAL_GROUPS"] && !in_array($id,
                                                        $arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"])) {
                                                    continue;
                                                }
                                            ?>
                                            <div class="form-check form-check-inline">
                                                <input 
                                                    type="radio" 
                                                    id="PERSON_TYPE_<?=$id?>" 
                                                    class="form-check-input" 
                                                    name="PERSON_TYPE" 
                                                    value="<?= $id ?>" 
                                                    <?= ($id == $arResult['PERSON_TYPE']['ID'] ? 'checked' : '') ?>>
                                                <label for="PERSON_TYPE_<?=$id?>" class="form-check-label"><?= $name ?></label>
                                            </div>
                                            <? endforeach; ?>
                                        </div>
                                    <? else: ?>
                                        <span class="form-value"><?=$arResult["PERSON_TYPE"]["NAME"]?></span>
                                    <? endif; ?>
                                </div>
                            </div>
                            <div class="row d-flow-root mt-4">
                                <?
                                if ($arResult["USE_PERSONAL_GROUPS"] && $arResult["PERSONAL_ACTIVE_GROUP"] == $groupId && in_array($arResult["PERSON_TYPE"]["ID"],
                                        $arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"])) {
                                    $personTypeID = $arResult["PERSON_TYPE"]["ID"];
                                } elseif ($arResult["USE_PERSONAL_GROUPS"]) {
                                    $personTypeID = current($arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"]);
                                } else {
                                    $personTypeID = $arResult["PERSON_TYPE"]['ID'];
                                }

                                $countProps = 0;
                                $isEmptyPersonTypeProps = true;
                                foreach ($arResult["ORDER_PROPS"][$personTypeID] as $block) {
                                    if (!empty($block["PROPS"])) {
                                        $isEmptyPersonTypeProps = false;
                                        ?>
                                        <div class="row-item col-md-6 <?= $countProps%2 === 0 ? 'float-start' : 'float-end' ?>">
                                            <div class="card">
                                                <div class="card-header d-flex flex-wrap">
                                                    <h6 class="card-title mb-0 fw-bold"><?= $block["NAME"] ?></h6>
                                                    <div class="d-inline-flex ms-auto">
                                                        <a class="text-body px-2" data-card-action="collapse">
                                                            <i class="ph ph-caret-down"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="collapse show">
                                                    <div class="card-body pt-0">
                                                        <?
                                                        $arProps = [];
                                                        foreach ($block["PROPS"] as $property) {
                                                            if (!empty($property['NAME'])) {
                                                                $arProps[$property['CODE']] = $property['NAME'];
                                                            }
                                                            $key = (int)$property["ID"];
                                                            $name = "ORDER_PROP_" . $key;
                                                            $currentValue = $arResult["ORDER_PROPS_VALUES"][$name];
                                                            $alignTop = ($property["TYPE"] === "LOCATION" && $arParams['USE_AJAX_LOCATIONS'] === 'Y') ? "vertical-align-top" : "";
                                                            ?>
                                                            <div class="form-group form-group-float<?= ($property["TYPE"] == "CHECKBOX" || $property["TYPE"] == 'Y/N') ? ' form-check' : '' ?>">
                                                                <label for="sppd-property-<?= $key ?>"
                                                                        class="form-label">
                                                                    <?= $property["NAME"] ?><?= ($property["TYPE"] == "CHECKBOX" || $property["TYPE"] == 'Y/N') ? '' : ':' ?>
                                                                    <? if ($property["REQUIRED"] == "Y") {
                                                                        ?>
                                                                        <span class="req">*</span>
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </label>
                                                                <?
                                                                if ($property["TYPE"] == "CHECKBOX" || $property["TYPE"] == 'Y/N') {
                                                                    ?>
                                                                        <input
                                                                            class="form-check-input"
                                                                            id="sppd-property-<?= $key ?>"
                                                                            type="checkbox"
                                                                            name="<?= $name ?>"
                                                                            value="Y"
                                                                        <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>
                                                                        <? if ($currentValue == "Y" || !isset($currentValue) && $property["DEFAULT_VALUE"] == "Y") {
                                                                            echo " checked";
                                                                        } ?>/>
                                                                    <?
                                                                } elseif ($property["TYPE"] == "TEXT" || $property["TYPE"] == "STRING"  || $property["TYPE"] == "NUMBER") {
                                                                    if ($property["MULTIPLE"] === 'Y') {
                                                                        if ($currentValue && !is_array($currentValue)) {
                                                                            $currentValue = explode(",",
                                                                                $currentValue);
                                                                        } elseif (!$currentValue) {
                                                                            $currentValue = array('');
                                                                        }
                                                                        ?>
                                                                        <div class="form-group multiple-props">
                                                                            <? foreach ($currentValue as $elementValue): ?>
                                                                                <input
                                                                                        class="form-control mb-2"
                                                                                        type="text"
                                                                                        name="<?= $name ?>[]"
                                                                                        maxlength="50"
                                                                                        id="sppd-property-<?= $key ?>"
                                                                                        value="<?= htmlspecialcharsbx($elementValue) ?>"
                                                                                />
                                                                            <? endforeach; ?>
                                                                            <button
                                                                                    type="button"
                                                                                    class="btn"
                                                                                    data-add-type=<?= $property["TYPE"] ?>
                                                                                    data-add-name="<?= $name ?>[]"
                                                                            >
                                                                                <?= Loc::getMessage('SOA_MULTIPLE_BTN') ?>
                                                                            </button>
                                                                        </div>
                                                                        <?
                                                                    } else {
                                                                        ?>
                                                                        <input
                                                                                class="form-control"
                                                                                type="text" name="<?= $name ?>"
                                                                                id="sppd-property-<?= $key ?>"
                                                                                value="<?= htmlspecialcharsbx($currentValue) ?>"
                                                                            <?= $property["REQUIRED"] == "Y" ? 'required ' : '' ?>
                                                                                maxlength="<?=
                                                                                !empty($property['SETTINGS']['MAXLENGTH']) ? $property['SETTINGS']['MAXLENGTH'] :
                                                                                    (!empty($property['SETTINGS']['SIZE']) ? $property['SETTINGS']['SIZE'] : 50)
                                                                                ?>"
                                                                                minlength="<?= !empty($property['SETTINGS']['MINLENGTH']) ? $property['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                            <?= !empty($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$property['CODE']]) ? 'value="' . $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$property['CODE']] . '"' : '' ?>
                                                                            <?= $property['SETTINGS']['PATTERN'] ? "pattern='" . $property['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                            <?= $property['DESCRIPTION'] ? "title='" . $property['DESCRIPTION'] . "'" : "" ?>
                                                                        />
                                                                        <?
                                                                    }
                                                                } elseif ($property["TYPE"] == "SELECT") {
                                                                    ?>
                                                                    <select
                                                                            class="form-control"
                                                                            name="<?= $name ?>"
                                                                            id="sppd-property-<?= $key ?>"
                                                                            size="<? echo (intval($property["SIZE1"]) > 0) ? $property["SIZE1"] : 1; ?>"
                                                                        <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>
                                                                    >
                                                                        <?
                                                                        foreach ($property["VALUES"] as $value) {
                                                                            ?>
                                                                            <option value="<?= $value["VALUE"] ?>" <? if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"]) echo " selected" ?>>
                                                                                <?= $value["NAME"] ?>
                                                                            </option>
                                                                            <?
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <?
                                                                } elseif ($property["TYPE"] == "MULTISELECT") {
                                                                    ?>
                                                                    <select
                                                                            class="form-control"
                                                                            id="sppd-property-<?= $key ?>"
                                                                            multiple name="<?= $name ?>[]"
                                                                            size="<? echo (intval($property["SIZE1"]) > 0) ? $property["SIZE1"] : 5; ?>
                                                                <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>">
                                                                        <?
                                                                        $arCurVal = array();
                                                                        $arCurVal = explode(",", $currentValue);
                                                                        for ($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++) {
                                                                            $arCurVal[$i] = trim($arCurVal[$i]);
                                                                        }
                                                                        $arDefVal = explode(",",
                                                                            $property["DEFAULT_VALUE"]);
                                                                        for ($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++) {
                                                                            $arDefVal[$i] = trim($arDefVal[$i]);
                                                                        }
                                                                        foreach ($property["VALUES"] as $value) {
                                                                            ?>
                                                                            <option value="<?= $value["VALUE"] ?>"<? if (in_array($value["VALUE"],
                                                                                    $arCurVal) || !isset($currentValue) && in_array($value["VALUE"],
                                                                                    $arDefVal)) echo " selected" ?>>
                                                                                <?= $value["NAME"] ?>
                                                                            </option>
                                                                            <?
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <?
                                                                } elseif ($property["TYPE"] == "ENUM") {
                                                                    $propValue = explode(',',
                                                                        $property["VALUE"]);
                                                                    if ($arResult["ORDER_PROPS_VALUES"][$name] && is_array($arResult["ORDER_PROPS_VALUES"][$name])) {
                                                                        $propValue = $arResult["ORDER_PROPS_VALUES"][$name];
                                                                    }
                                                                    ?>
                                                                    <select
                                                                            class="form-control"
                                                                            name="<?= $name ?><?= $property['MULTIPLE'] == "Y" ? "[]" : "" ?>"
                                                                            id="sppd-property-<?= $key ?>"
                                                                        <?= $property['MULTIPLE'] == "Y" ? "multiple" : "" ?>
                                                                        <?= $property['REQUIRED'] == 'Y' ? 'required' : '' ?>
                                                                    >
                                                                        <? foreach ($property["VALUES"] as $variant): ?>
                                                                            <option
                                                                                    value="<?= $variant["ID"] ?>"
                                                                                <?
                                                                            if ((is_array($propValue) && in_array($variant["ID"], $propValue))
                                                                                || (!$arResult["ORDER_PROPS_VALUES"] && ($variant["ID"] == $property["DEFAULT_VALUE"]
                                                                                        || (is_array($property["DEFAULT_VALUE"]) && in_array($variant["ID"], $property["DEFAULT_VALUE"]))))) {
                                                                                    echo "selected";
                                                                                }
                                                                                ?>
                                                                            >
                                                                                <?= $variant["NAME"] ?>
                                                                            </option>
                                                                        <? endforeach; ?>
                                                                    </select>
                                                                    <?
                                                                } elseif ($property["TYPE"] == "TEXTAREA") {
                                                                    ?>
                                                                    <textarea
                                                                            class="form-control"
                                                                            id="sppd-property-<?= $key ?>"
                                                                            rows="<? echo ((int)($property["SIZE2"]) > 0) ? $property["SIZE2"] : 4; ?>"
                                                                            cols="<? echo ((int)($property["SIZE1"]) > 0) ? $property["SIZE1"] : 40; ?>"
                                                                            name="<?= $name ?>"
                                                                <?= (isset($currentValue)) ? $currentValue : $property["DEFAULT_VALUE"]; ?>
                                                                        <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>
                                                                maxlength="<?=
                                                                !empty($property['SETTINGS']['MAXLENGTH']) ? $property['SETTINGS']['MAXLENGTH'] :
                                                                    (!empty($property['SETTINGS']['SIZE']) ? $property['SETTINGS']['SIZE'] : 50)
                                                                ?>"
                                                                            minlength="<?= !empty($property['SETTINGS']['MINLENGTH']) ? $property['SETTINGS']['MINLENGTH'] : 0 ?>"
                                                                    <?= !empty($arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$property['CODE']]) ? 'value="' . $arResult["VALUES"]['WHOLESALER_ORDER_FIELDS'][$group['ID']][$property['CODE']] . '"' : '' ?>
                                                                    <?= $property['SETTINGS']['PATTERN'] ? "pattern='" . $property['SETTINGS']['PATTERN'] . "'" : "" ?>
                                                                    <?= $property['DESCRIPTION'] ? "title='" . $property['DESCRIPTION'] . "'" : "" ?>>
                                                                    </textarea>
                                                                    <?
                                                                } elseif ($property["TYPE"] == "LOCATION") {
                                                                    $locationTemplate = ($arParams['USE_AJAX_LOCATIONS'] !== 'Y') ? "popup" : "";
                                                                    $locationClassName = 'location-block-wrapper';
                                                                    if ($arParams['USE_AJAX_LOCATIONS'] === 'Y') {
                                                                        $locationClassName .= ' location-block-wrapper-delimeter';
                                                                    }
                                                                    if ($property["MULTIPLE"] === 'Y') {
                                                                        if (empty($currentValue) || !is_array($currentValue)) {
                                                                            $currentValue = array($property["DEFAULT_VALUE"]);
                                                                        }

                                                                        foreach ($currentValue as $code => $elementValue) {
                                                                            $locationValue = intval($elementValue) ? $elementValue : $property["DEFAULT_VALUE"];
                                                                            CSaleLocation::proxySaleAjaxLocationsComponent(
                                                                                array(
                                                                                    "ID" => "propertyLocation" . $name . "[$code]",
                                                                                    "AJAX_CALL" => "N",
                                                                                    'CITY_OUT_LOCATION' => 'Y',
                                                                                    'COUNTRY_INPUT_NAME' => $name . '_COUNTRY',
                                                                                    'CITY_INPUT_NAME' => $name . "[$code]",
                                                                                    'LOCATION_VALUE' => $locationValue,
                                                                                ),
                                                                                array(),
                                                                                $locationTemplate,
                                                                                true,
                                                                                $locationClassName
                                                                            );
                                                                        }
                                                                        ?>
                                                                        <span class="btn-themes btn-default btn-md btn input-add-multiple"
                                                                                data-add-type=<?= $property["TYPE"] ?>
                                                                                data-add-name="<?= $name ?>"
                                                                                data-add-last-key="<?= $code ?>"
                                                                                data-add-template="<?= $locationTemplate ?>"><?= Loc::getMessage('SPPD_ADD') ?></span>
                                                                        <?
                                                                    } else {
                                                                        $locationValue = (int)($currentValue) ? (int)$currentValue : $property["DEFAULT_VALUE"];

                                                                        CSaleLocation::proxySaleAjaxLocationsComponent(
                                                                            array(
                                                                                "AJAX_CALL" => "N",
                                                                                'CITY_OUT_LOCATION' => 'Y',
                                                                                'COUNTRY_INPUT_NAME' => $name . '_COUNTRY',
                                                                                'CITY_INPUT_NAME' => $name,
                                                                                'LOCATION_VALUE' => $locationValue,
                                                                            ),
                                                                            array(
                                                                                "SUPPRESS_ERRORS" => true,
                                                                            ),
                                                                            $locationTemplate,
                                                                            true,
                                                                            'location-block-wrapper'
                                                                        );
                                                                    }
                                                                } elseif ($property["TYPE"] == "RADIO") {
                                                                    foreach ($property["VALUES"] as $value) {
                                                                        ?>
                                                                        <div class="form-check form-check-inline">
                                                                            <input
                                                                                    type="radio"
                                                                                    class="form-check-input"
                                                                                    id="sppd-property-<?= $key ?>"
                                                                                    name="<?= $name ?>"
                                                                                    value="<?= $value["VALUE"] ?>"
                                                                                <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>
                                                                                <? if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"]) echo " checked" ?>>
                                                                                <label class="form-check-label" for="sppd-property-<?= $key ?>"><?= $value["NAME"] ?></label>
                                                                        </div>
                                                                        <?
                                                                    }
                                                                } elseif ($property["TYPE"] == "FILE") {
                                                                    $multiple = ($property["MULTIPLE"] === "Y") ? "multiple" : '';
                                                                    $profileFiles = is_array($currentValue) ? $currentValue : array($currentValue);
                                                                    if (count($currentValue) > 0) {
                                                                        foreach ($profileFiles as $file) {
                                                                            ?>
                                                                            <div class="sale-personal-profile-detail-form-file">
                                                                                <?
                                                                                $fileId = $file['ID'];
                                                                                if (CFile::IsImage($file['FILE_NAME'])) {
                                                                                    ?>
                                                                                    <div class="sale-personal-profile-detail-prop-img">
                                                                                        <?= CFile::ShowImage($fileId,
                                                                                            150,
                                                                                            150, "border=0", "",
                                                                                            true) ?>
                                                                                    </div>
                                                                                    <?
                                                                                } else {
                                                                                    ?>
                                                                                    <a download="<?= $file["ORIGINAL_NAME"] ?>"
                                                                                        href="<?= CFile::GetPath($file) ?>">
                                                                                        <?= Loc::getMessage('SPPD_DOWNLOAD_FILE',
                                                                                            array("#FILE_NAME#" => $file["ORIGINAL_NAME"])) ?>
                                                                                    </a>
                                                                                    <?
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                            <?
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <label>
                                                                        <span class="btn-themes btn-default btn-md btn">
                                                                            <?= Loc::getMessage('SPPD_SELECT') ?>
                                                                        </span>
                                                                                                            <span class="sale-personal-profile-detail-load-file-info">
                                                                            <?= Loc::getMessage('SPPD_FILE_NOT_SELECTED') ?>
                                                                        </span>
                                                                        <?= CFile::InputFile($name, 20,
                                                                            $property["VALUE"], false,
                                                                            0, "IMAGE",
                                                                            "class='btn sale-personal-profile-detail-input-file' " . $multiple) ?>
                                                                    </label>
                                                                    <span class="sale-personal-profile-detail-load-file-cancel sale-personal-profile-hide"></span>
                                                                    <?
                                                                }

                                                                if (strlen($property["DESCRIPTION"]) > 0) {
                                                                    ?>
                                                                    <br/>
                                                                    <small><?= $property["DESCRIPTION"] ?></small>
                                                                    <?
                                                                }
                                                                ?>
                                                            </div>
                                                            <?
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?
                                        $countProps++;
                                    }
                                }
                                ?>

                                <? if ($arResult["COMPANY_USER_FIELDS"][$personTypeID]): ?>
                                    <div class="row-item col-md-6 <?= $countProps%2 === 0 ? 'float-start' : 'float-end' ?>">
                                        <div class="card">
                                            <div class="card-header d-flex flex-wrap">
                                                <h6 class="card-title mb-0 fw-bold"><?= Option::get('sotbit.auth', 'COMPANY_USER_FIELDS_TITLE_' . $arResult["PERSON_TYPE"]['ID'], '', SITE_ID) ?: Loc::getMessage("SOA_COMPANY_USER_FIELDS_GROUP") ?></h6>
                                                <div class="d-inline-flex ms-auto">
                                                    <a class="text-body px-2" data-card-action="collapse">
                                                        <i class="ph ph-caret-down"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="collapse show">
                                                <div class="card-body pt-0">
                                                    <? foreach ($arResult["COMPANY_USER_FIELDS"][$personTypeID] as $arCompanyUserField): ?>
                                                        <div class="form-group form-group-float">
                                                            <label for="sppd-property-<?= $arCompanyUserField["XML_ID"] ?>"
                                                                    class="form-label">
                                                                <?= $arCompanyUserField["EDIT_FORM_LABEL"] ?>:
                                                                <? if ($property["MANDATORY"] == "Y") {
                                                                    ?>
                                                                    <span class="req">*</span>
                                                                    <?
                                                                }
                                                                ?>
                                                            </label>
                                                            <?
                                                            $arCompanyUserField["CLASS"] = "form-control";
                                                            $APPLICATION->IncludeComponent(
                                                                "bitrix:system.field.edit",
                                                                $arCompanyUserField["USER_TYPE"]["USER_TYPE_ID"],
                                                                array(
                                                                    "bVarsFromForm" => false,
                                                                    "arUserField" => $arCompanyUserField
                                                                ),
                                                                null, array("HIDE_ICONS" => "Y"));
                                                            ?>
                                                        </div>
                                                    <? endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? endif; ?>
                            </div>
                            <div class="card card-position-sticky">
                                <div class="card-body d-flex align-items-center gap-custom-1">
                                    <input type="submit" class="btn btn-primary" name="save"
                                            value="<? echo GetMessage("SOA_SALE_SAVE") ?>"
                                            <?=$isEmptyPersonTypeProps && empty($arResult["COMPANY_USER_FIELDS"][$personTypeID]) ? 'disabled' : ''?>>
                                    <input type="button" class="btn" name="cancel"
                                            value="<? echo GetMessage("SOA_SALE_RESET") ?>"
                                            onclick="goToList()">
                                    <?
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.userconsent.request",
                                        "b2bcabinet",
                                        array(
                                            "AUTO_SAVE" => "Y",
                                            "COMPOSITE_FRAME_MODE" => "A",
                                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                                            "ID" => $arParams["AGREEMENT_ID"] ?: \COption::GetOptionString("sotbit.b2bcabinet",
                                                "AGREEMENT_ID"),
                                            "IS_CHECKED" => "Y",
                                            "IS_LOADED" => "N",
                                            "IS_DISABLED" => $isEmptyPersonTypeProps && empty($arResult["COMPANY_USER_FIELDS"][$personTypeID]),
                                            "REPLACE" => array(
                                                'button_caption' => GetMessage("SOA_SALE_SAVE"),
                                                'fields' => $arProps
                                            ),
                                            "COMPONENT_TEMPLATE" => "b2bcabinet"
                                        ),
                                        false
                                    ); ?>
                                </div>
                            </div>
                        </form>
                    </div>
                <?endforeach; ?>
            </div>
        <?
        $javascriptParams = array(
            "ajaxUrl" => CUtil::JSEscape($this->__component->GetPath() . '/ajax.php'),
        );
        $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
        ?>
        <script>
            BX.message({
                SPPD_FILE_COUNT: '<?=Loc::getMessage('SPPD_FILE_COUNT')?>',
                SPPD_FILE_NOT_SELECTED: '<?=Loc::getMessage('SPPD_FILE_NOT_SELECTED')?>'
            });
        </script>
    
    <? endif; ?>
</div>

<script>
    var path_to_list = '<?=$arParams["PATH_TO_LIST"]?>';
    var title_send_moderation = '<?=GetMessage("SOA_COMPANY_CONSENT_TO_SEND_FOR_MODERATION")?>';
</script>