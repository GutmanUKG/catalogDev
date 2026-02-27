<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;

$APPLICATION->AddChainItem(Loc::getMessage('SOA_PROFILE_ADD'));
?>

<div class="add-company__wrapper">

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
    <? else: ?>
        <?
        $arResult["PERSONAL_GROUPS_LIST"][1] = true;
        $arResult["PERSONAL_ACTIVE_GROUP"] = 1;
        ?>
    <? endif; ?>
    <div class="tab-content <?=(isset($_REQUEST['EDIT_ID']) && !empty($arResult["USE_PERSONAL_GROUPS"])) ? 'mt-4' : ''?>">
        <? foreach ($arResult["PERSONAL_GROUPS_LIST"] as $groupId => $groupList): ?>

            <div class="tab-personal-group tab-pane fade <?= $arResult["PERSONAL_ACTIVE_GROUP"] == $groupId ? 'active show' : '' ?>"
                    id="person-group-<?= $groupId ?>">
                <form method="post" id="add-org" class="sale-profile-detail-form"
                        action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" id="p_type_id" name="ID"
                            value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">
                    <input type="hidden" id="change_person_type" name="change_person_type"
                            value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">
                    <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE"
                            value="<?= $arResult["PERSON_TYPE"]['ID'] ?>">

                    <div class="card">
                        <div class="card-body">
                            <label class="form-label"><?= Loc::getMessage('SOA_SALE_PERS_TYPE') ?>:</label>
                            <div>
                            <? foreach ($arResult['PERSON_TYPES'] as $id => $name) : ?>
                            <?
                                if ($arResult["USE_PERSONAL_GROUPS"] && !in_array($id,
                                        $arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"])) {
                                    continue;
                                }
                            ?>
                                <label class="form-check form-check-inline">
                                    <input 
                                        type="radio" 
                                        id="PERSON_TYPE_<?=$id?>" 
                                        class="form-check-input" 
                                        name="PERSON_TYPE" 
                                        value="<?= $id ?>" 
                                        <?= ($id == $arResult['PERSON_TYPE']['ID'] ? 'checked' : '') ?>>
                                    <label for="PERSON_TYPE_<?=$id?>" class="form-check-label"><?= $name ?></label>
                                </label>
                            <? endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <?
                        if ($arResult["USE_PERSONAL_GROUPS"] && $arResult["PERSONAL_ACTIVE_GROUP"] == $groupId && in_array($arResult["PERSON_TYPE"]["ID"],
                                $arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"])) {
                            $personTypeID = $arResult["PERSON_TYPE"]["ID"];
                        } elseif ($arResult["USE_PERSONAL_GROUPS"]) {
                            $personTypeID = current($arResult["PERSONAL_GROUPS_LIST"][$groupId]["PERSON_TYPE"]);
                        } else {
                            $personTypeID = $arResult["PERSON_TYPE"]['ID'];
                        }
                        foreach ($arResult["ORDER_PROPS"][$personTypeID] as $block) {
                            if (!empty($block["PROPS"])) {
                                ?>
                                <div class="row-item col-md-6">
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
                                                        } elseif ($property["TYPE"] == "TEXT" || $property["TYPE"] == "STRING" || $property["TYPE"] == "NUMBER") {
                                                            if ($property["MULTIPLE"] === 'Y') {
                                                                if (empty($currentValue) || !is_array($currentValue)) {
                                                                    $currentValue = array('');
                                                                }
                                                                ?>
                                                                <div class="form-group multiple-props">
                                                                    <? foreach ($currentValue as $elementValue) {
                                                                        ?>
                                                                        <input
                                                                                class="form-control mb-2"
                                                                                type="text" name="<?= $name ?>[]"
                                                                                maxlength="50"
                                                                                id="sppd-property-<?= $key ?>"
                                                                                value="<?= htmlspecialcharsbx($elementValue) ?>"
                                                                        />
                                                                        <?
                                                                    }
                                                                    ?>
                                                                    <button
                                                                            type="button"
                                                                            class="btn"
                                                                            data-add-type="text"
                                                                            data-add-name="<?= $name ?>[]"
                                                                            data-add-maxlength="50"
                                                                            data-add-class="form-control mb-2"
                                                                    >
                                                                        <?= Loc::getMessage('SOA_BTN_MULTIPLE') ?>
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
                                                            $propValue = explode(',', $property["VALUE"]);
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
                                                                        if (in_array($variant["ID"],
                                                                                $propValue) || (!$arResult["ORDER_PROPS_VALUES"] && ($variant["ID"] == $property["DEFAULT_VALUE"] || in_array($variant["ID"],
                                                                                        is_array($property["DEFAULT_VALUE"])? $property["DEFAULT_VALUE"] : [])))) {
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
                                                            <?= $property['DESCRIPTION'] ? "title='" . $property['DESCRIPTION'] . "'" : "" ?>
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
                                                                            class="form-check-input"
                                                                            type="radio"
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
                                                                ?>
                                                                <input type="hidden" name="<?= $name ?>_del"
                                                                        class="profile-property-input-delete-file"
                                                                    <?= ($property["REQUIRED"] == "Y" ? "required" : "") ?>
                                                                >
                                                                <?
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
                                                                                href="<?= CFile::GetFileSRC($file) ?>">
                                                                                <?= Loc::getMessage('SPPD_DOWNLOAD_FILE',
                                                                                    array("#FILE_NAME#" => $file["ORIGINAL_NAME"])) ?>
                                                                            </a>
                                                                            <?
                                                                        }
                                                                        ?>
                                                                        <input type="checkbox"
                                                                                value="<?= $fileId ?>"
                                                                                class="profile-property-check-file"
                                                                                id="profile-property-check-file-<?= $fileId ?>">
                                                                        <label for="profile-property-check-file-<?= $fileId ?>"><?= Loc::getMessage('SPPD_DELETE_FILE') ?></label>
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
                                                                <?= CFile::InputFile($name . "[]", 20, null,
                                                                    false,
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
                            }
                        }
                        ?>
                    </div>
                    <div class="card card-position-sticky">
                        <div class="card-body d-flex align-items-center gap-3">
                            <input type="submit" class="btn btn-primary" name="save"
                                    value="<? echo GetMessage("SOA_SALE_SAVE") ?>">
                            <a href="<?= Context::getCurrent()->getRequest()->getRequestedPageDirectory() ?>"
                                class="btn"><? echo GetMessage("SOA_SALE_RESET") ?></a>
                            <?
                            $APPLICATION->IncludeComponent(
                                "bitrix:main.userconsent.request",
                                "b2bcabinet",
                                array(
                                    "AUTO_SAVE" => "Y",
                                    "COMPOSITE_FRAME_MODE" => "A",
                                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                                    "ID" => \COption::GetOptionString("sotbit.b2bcabinet", "AGREEMENT_ID"),
                                    "IS_CHECKED" => "Y",
                                    "IS_LOADED" => "N",
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
        <? endforeach; ?>
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
        //BX.Sale.PersonalProfileComponent.PersonalProfileDetail.init(<?//=$javascriptParams?>//);
    </script>
</div>

