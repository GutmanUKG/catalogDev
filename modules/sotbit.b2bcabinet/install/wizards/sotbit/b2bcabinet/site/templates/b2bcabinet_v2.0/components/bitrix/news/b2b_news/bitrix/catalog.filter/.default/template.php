<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<div class="card news-filter">
    <div class="card-header card-header d-flex align-items-center">
        <span class="text-uppercase font-size-sm font-weight-semibold"><?= GetMessage("IBLOCK_FILTER_TITLE") ?></span>
        <div class="d-inline-flex d-lg-none ms-auto align-items-center">
            <button type="button" class="btn-close" data-bs-target="#news__filter" data-bs-dismiss="offcanvas"></button>
        </div>
    </div>
    <div class="card-body pt-2">
        <form name="<? echo $arResult["FILTER_NAME"] . "_form" ?>" action="<? echo $arResult["FORM_ACTION"] ?>"
              method="get">
            <? foreach ($arResult["ITEMS"] as $arItem): ?>

                <? if (array_key_exists("HIDDEN", $arItem)): ?>
                    <?= $arItem["INPUT"] ?>
                <? elseif ($arItem["TYPE"] == "RANGE"): ?>
                    <div class="form-group form-group-float">
                        <label class="form-label"><?= $arItem["NAME"] ?></label>
                        <div class="row mb-2">
                            <div class="col">
                                <input
                                        class="form-control"
                                        type="text"
                                        value="<?= $arItem["INPUT_VALUES"][0] ?>"
                                        name="<?= $arItem["INPUT_NAMES"][0] ?>"
                                        placeholder="<?= GetMessage("FILTER_FROM") ?>"
                                >
                            </div>
                            <div class="col">
                                <input
                                        class="form-control"
                                        type="text"
                                        value="<?= $arItem["INPUT_VALUES"][1] ?>"
                                        name="<?= $arItem["INPUT_NAMES"][1] ?>"
                                        placeholder="<?= GetMessage("FILTER_TO") ?>"
                                >
                            </div>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "DATE_RANGE"): ?>
                    <div class="form-group form-group-float">
                        <label class="form-label"><?= $arItem["NAME"] ?></label>
                        <div class="row mb-3">
                            <label class="col-form-label col-2"><?= GetMessage("FILTER_FROM") ?>:</label>
                            <div class="col-10">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar',
                                    '',
                                    array(
                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                        'INPUT_NAME' => $arItem["INPUT_NAMES"][0],
                                        'INPUT_VALUE' => (!empty($arItem["INPUT_VALUES"][0] ) ? date("d.m.Y", strtotime($arItem["INPUT_VALUES"][0])) : ''),
                                    ),
                                    null,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-form-label col-2"><?= GetMessage("FILTER_TO") ?>:</label>
                            <div class="col-10">
                                <? $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar',
                                    '',
                                    array(
                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                        'INPUT_NAME' => $arItem["INPUT_NAMES"][1],
                                        'INPUT_VALUE' => (!empty($arItem["INPUT_VALUES"][1]) ? date("d.m.Y", strtotime($arItem["INPUT_VALUES"][1])) : ''),
                                    ),
                                    null,
                                    array('HIDE_ICONS' => 'Y')
                                ); ?>
                            </div>
                        </div>
                    </div>
                <? elseif ($arItem["TYPE"] == "SELECT"): ?>
                    <div class="form-group">
                        <div class="form-group form-group-float">
                            <label class="form-label"><?= $arItem["NAME"] ?></label>
                        </div>
                        <div class="form-group row">
                            <select class="form-control select"
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
                        <div class="form-group form-group-float">
                            <label class="form-label"><?= $arItem["NAME"] ?></label>
                        </div>
                        <div class="form-group col">
                            <div class="blank_ul_wrapper type-checkbox">
                                <? $arListValue = (is_array($arItem["~INPUT_VALUE"]) ? $arItem["~INPUT_VALUE"] : array($arItem["~INPUT_VALUE"]));
                                foreach ($arItem["LIST"] as $key => $value):?>
                                    <div class="bx_filter_parameters_box_checkbox form-check">
                                        <input
                                                type="checkbox"
                                                class="form-check-input"
                                                value="<?= htmlspecialcharsBx($key) ?>"
                                                name="<? echo $arItem["INPUT_NAME"] ?>"
                                            <? if (in_array($key, $arListValue)) echo 'checked="checked"' ?>
                                        >
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
                        <div class="form-group form-group-float">
                            <label class="form-label"><?= $arItem["NAME"] ?></label>
                        </div>
                        <div class="form-group col">
                            <? $arListValue = (is_array($arItem["~INPUT_VALUE"]) ? $arItem["~INPUT_VALUE"] : array($arItem["~INPUT_VALUE"]));
                            foreach ($arItem["LIST"] as $key => $value):?>
                                <div class="form-check">
                                    <input
                                            type="radio"
                                            class="form-check-input"
                                            value="<?= htmlspecialcharsBx($key) ?>"
                                            name="<? echo $arItem["INPUT_NAME"] ?>"
                                        <? if (in_array($key, $arListValue)) echo 'checked="checked"' ?>
                                    >
                                    <label class="form-check-label" for="<? echo $arItem["INPUT_NAME"] ?>">
                                        <?= htmlspecialcharsEx($value) ?>
                                    </label>
                                </div>
                            <? endforeach ?>
                        </div>
                    </div>
                <? else: ?>
                    <div class="form-group form-group-float">
                        <label class="form-label"><?= $arItem["NAME"] ?></label>
                        <input class="form-control" type="text" name="<?= $arItem["INPUT_NAME"] ?>"
                                value="<?= $arItem["INPUT_VALUE"] ?>"/>
                    </div>
                <? endif ?>

            <? endforeach; ?>
            <div class="d-flex justify-content-between gap-3">
                <button class="news-filter__reset-btn btn px-0 w-100" type="submit" name="del_filter" value="Y">
                    <?= GetMessage("IBLOCK_DEL_FILTER") ?>
                </button>
                <button class="news-filter__submit-btn btn btn-primary px-0 w-100" type="submit" name="set_filter" value="Y">
                    <?= GetMessage("IBLOCK_SET_FILTER") ?>
                </button>
            </div>
        </form>
    </div>
</div>
