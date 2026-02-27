<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<div class="card news-filter">
    <div class="card-header bg-transparent header-elements-inline">
        <span class="text-uppercase font-size-sm font-weight-semibold"><?= GetMessage("IBLOCK_FILTER_TITLE") ?></span>
        <div class="header-elements">
            <div class="list-icons">
                <span class="list-icons-item" data-action="collapse"></span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form name="<? echo $arResult["FILTER_NAME"] . "_form" ?>" action="<? echo $arResult["FORM_ACTION"] ?>"
              method="get">
            <? foreach ($arResult["ITEMS"] as $arItem): ?>

                <? if (array_key_exists("HIDDEN", $arItem)): ?>
                    <?= $arItem["INPUT"] ?>
                <? elseif ($arItem["TYPE"] == "RANGE"): ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2"><?= GetMessage("FILTER_FROM") ?>:</label>
                            <div class="col-md-4">
                                <input
                                        class="form-control"
                                        type="text"
                                        value="<?= $arItem["INPUT_VALUES"][0] ?>"
                                        name="<?= $arItem["INPUT_NAMES"][0] ?>"
                                        placeholder="<?= GetMessage("CT_BCF_FROM") ?>"
                                >
                            </div>
                            <label class="col-form-label col-md-2"><?= GetMessage("FILTER_TO") ?>:</label>
                            <div class="col-md-4">
                                <input
                                        class="form-control"
                                        type="text"
                                        value="<?= $arItem["INPUT_VALUES"][1] ?>"
                                        name="<?= $arItem["INPUT_NAMES"][1] ?>"
                                        placeholder="<?= GetMessage("CT_BCF_TO") ?>"
                                >
                            </div>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "DATE_RANGE"): ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2"><?= GetMessage("FILTER_FROM") ?></label>
                            <div class="col-md-10">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar',
                                    '',
                                    array(
                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                        'INPUT_NAME' => $arItem["INPUT_NAMES"][0],
                                        'INPUT_VALUE' => $arItem["INPUT_VALUES"][0],
                                    ),
                                    null,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2"><?= GetMessage("FILTER_TO") ?></label>
                            <div class="col-md-10">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar',
                                    '',
                                    array(
                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                        'INPUT_NAME' => $arItem["INPUT_NAMES"][1],
                                        'INPUT_VALUE' => $arItem["INPUT_VALUES"][1],
                                    ),
                                    null,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "SELECT"): ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                        <div class="form-group row">
                            <select class="custom-select"
                                    name="<?= $arItem["INPUT_NAME"] . ($arItem["MULTIPLE"] == "Y" ? "[]" : "") ?>">
                                <? foreach ($arItem["LIST"] as $key => $value): ?>
                                    <option value="<?= htmlspecialcharsBx($key) ?>"
                                        <? if ($key == $arItem["INPUT_VALUE"]) echo 'selected="selected"' ?>>
                                        <?= htmlspecialcharsEx($value) ?>
                                    </option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "CHECKBOX"): ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                        <div class="form-group col">
                            <div class="blank_ul_wrapper type-checkbox">
                                <? $arListValue = (is_array($arItem["~INPUT_VALUE"]) ? $arItem["~INPUT_VALUE"] : array($arItem["~INPUT_VALUE"]));
                                foreach ($arItem["LIST"] as $key => $value):?>
                                    <div class="bx_filter_parameters_box_checkbox form-check">
                                        <div class="uniform-checker">
                                            <input
                                                    type="checkbox"
                                                    class="checkbox__input form-input-styled"
                                                    value="<?= htmlspecialcharsBx($key) ?>"
                                                    name="<? echo $arItem["INPUT_NAME"] ?>"
                                                <? if (in_array($key, $arListValue)) echo 'checked="checked"' ?>
                                            >
                                        </div>
                                        <label class="form-check-label" for="<? echo $arItem["INPUT_NAME"] ?>">
                                            <?= htmlspecialcharsEx($value) ?>
                                        </label>
                                    </div>
                                <? endforeach ?>
                            </div>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "RADIO"): ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                        <div class="form-group col">
                            <? $arListValue = (is_array($arItem["~INPUT_VALUE"]) ? $arItem["~INPUT_VALUE"] : array($arItem["~INPUT_VALUE"]));
                            foreach ($arItem["LIST"] as $key => $value):?>
                                <div class="form-check">
                                    <div class="uniform-choice">
                                        <input
                                                type="radio"
                                                class="form-check-input-styled"
                                                value="<?= htmlspecialcharsBx($key) ?>"
                                                name="<? echo $arItem["INPUT_NAME"] ?>"
                                            <? if (in_array($key, $arListValue)) echo 'checked="checked"' ?>
                                        >
                                    </div>
                                    <label class="form-check-label" for="<? echo $arItem["INPUT_NAME"] ?>">
                                        <?= htmlspecialcharsEx($value) ?>
                                    </label>
                                </div>
                            <? endforeach ?>
                        </div>
                    </div>
                <? else: ?>
                    <div class="form-group">
                        <div class="bx_filter_parameters_box_title fonts__middle_text font-size-xs text-uppercase text-muted mb-3">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                        </div>
                            <input class="form-control" type="text" name="<?= $arItem["INPUT_NAME"] ?>"
                                   value="<?= $arItem["INPUT_VALUE"] ?>"/>
                    </div>
                <? endif ?>

            <? endforeach; ?>
            <div class="d-flex flex-column">
                <button class="news-filter__submit-btn btn btn_b2b order-1" type="submit" name="set_filter" value="Y">
                    <?= GetMessage("IBLOCK_SET_FILTER") ?>
                </button>
                <button class="news-filter__reset-btn btn btn-light" type="submit" name="del_filter" value="Y">
                    <?= GetMessage("IBLOCK_DEL_FILTER") ?>
                </button>
            </div>
        </form>
    </div>
</div>
