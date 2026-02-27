<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
CJSCore::Init(array('currency'));
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Localization\Loc;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/assets/js/plugins/nouislider/nouislider.min.js');

$startWrap = 'Y';

function renderRecursiveMenu($section, $isOpen = false) {
    if (!empty($section['CHILDS']) && is_array($section['CHILDS'])):
        echo '<ul class="nav nav-group-sub"';
        if ($isOpen) {
            echo ' style="display:block">';
        } else {
            echo '>';
        }
        foreach ($section['CHILDS'] as $item):
            if ($item["CHECKED"])
                $item["CHECKED"] = "checked=\'checked\'";
            else
                $item["CHECKED"] = "";



            echo '<li class="nav-item nav-item-submenu"> 
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input
                                        type="checkbox"
                                        class="form-input-styled checkbox__input"
                                        data-fouc
                                        id="' . $item["CONTROL_ID"] .'"
                                        value="' . $item["HTML_VALUE"] .'"
                                        name="' . $item["CONTROL_NAME"] .'"
                                        ' . $item["CHECKED"] . '
                                        data-type="section"
                                        onclick="smartFilter.click(this)">'. $item["VALUE"] . '</label>
                            </div>
                            '. renderRecursiveMenu($item) . '
                           </li>';
        endforeach;
        echo '</ul>';
    endif;
}

?>


<form name="<? echo $arResult["FILTER_NAME"] . "_form" ?>" action="<? echo $arResult["FORM_ACTION"] ?>"
      method="get" class="smartfilter">

    <? foreach ($arResult["HIDDEN"] as $arItem): ?>
        <input type="hidden" name="<? echo $arItem["CONTROL_NAME"] ?>" id="<? echo $arItem["CONTROL_ID"] ?>"
               value="<? echo $arItem["HTML_VALUE"] ?>"/>
    <? endforeach; ?>
    <? if (!empty($arResult['ITEMS']['SECTION_ID']['NAME']) && !empty($arResult['ITEMS']['SECTION_ID']['FILTRED_FIELDS'])): ?>

        <input type="hidden" name="refresh_values">
        <div class="card index_blank-categories">

            <div class="card-header">
                <span class="text-uppercase fw-medium"><?= $arResult['ITEMS']['SECTION_ID']['NAME'] ?></span>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>

            <? if (is_array($arResult['ITEMS']['SECTION_ID']['FILTRED_FIELDS'])): ?>
                <div class="card-body border-0 p-0">
                    <ul class="nav nav-sidebar mb-2">
                        <? foreach ($arResult['ITEMS']['SECTION_ID']['FILTRED_FIELDS'] as $section): ?>
                            <? if (!empty($section['VALUE'])): ?>
                                <?
                                    $isOpen = isChildChecked($section);
                                ?>
                                <li class="nav-item <?= (!empty($section['CHILDS']) ? 'nav-item-submenu' : '') ?> catalog_section <?=$isOpen ? 'nav-item-open' : ''?>">
                            <a class="nav-link">
                                <div class="form-check">
                                    <label class="form-check-label bx_filter_parameters_box_checkbox">
                                        <input type="checkbox" class="form-input-styled checkbox__input"
                                               data-fouc id="<?= $section["CONTROL_ID"] ?>"
                                               value="<?= $section["HTML_VALUE"] ?>"
                                               name="<?= $section["CONTROL_NAME"] ?>"
                                            <?= $section["CHECKED"] || $section['CHILD_SELECTED'] == 'Y' ? 'checked="checked"' : '' ?>
                                               onclick="smartFilter.click(this);"
                                               data-type="section"
                                            <? //= $VALUE["DISABLED"] ? 'disabled': '' ?>
                                        >
                                    </label>
                                </div>
                                <?= $section['VALUE'] ?>
                            </a>
                            <?
                            if ((!empty($section['CHILDS']))):
                             renderRecursiveMenu($section, $isOpen);
                            endif;
                            ?>
                        </li>
                            <? endif; ?>
                        <? endforeach; ?>
                    </ul>
                </div>
            <? endif; ?>
        </div>

        <? unset($arResult['ITEMS']['SECTION_ID']); ?>
    <? endif; ?>

    <div class="index_blank-filter">
        <div class="bx_filter_section bx_filter">
            <div class="card-header d-flex align-items-center">
                <span class="text-uppercase fw-medium fs-md"><?= GetMessage('CT_BCSF_FILTER') ?></span>
                <div class="d-inline-flex d-xxxl-none ms-auto align-items-center">
                    <button type="button" class="btn-close" data-bs-target="#catalog__filter" data-bs-dismiss="offcanvas"></button>
                </div>
            </div>
            <div class="anchor_header_filter"></div>
            <div class="card-body">

                <?
                //prices
                foreach ($arResult["ITEMS"] as $key => $arItem) {
                    $key = $arItem["ENCODED_ID"];
                    if (isset($arItem["PRICE"])):
                        if (($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))
                            continue;
                        ?>
                        <div class="bx_filter_parameters_box active form-group"
                             data-propid="P<?= $arItem["ID"] ?>">
                            <div class="bx_filter_parameters_box_title text-uppercase text-muted fw-medium">
                                <span>
                                    <span class="item_name"><?= $arItem["NAME"] ?></span>
                                </span>
                            </div>
                            <div class="bx_filter_block bx_filter_block_wrapper" data-role="bx_filter_block">
                                <div class="noui-height-helper" id="slider_price_<?= $key ?>"></div>
                                <div class="bx_filter_parameters_box_container row">
                                    <div class="bx_filter_parameters_box_container_block">
                                        <div class="bx_filter_input_container">
                                            <input
                                                    class="min-price fonts__middle_comment form-control form-control-sm"
                                                    type="text"
                                                    name="<?
                                                    echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
                                                    id="<?
                                                    echo $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
                                                    value="<?
                                                    echo $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
                                                    size="5"
                                                    onkeyup="smartFilter.keyup(this)"
                                                    placeholder="<?= Loc::getMessage('CT_BCSF_FILTER_FROM') . CCurrencyLang::CurrencyFormat($arItem['VALUES']['MIN']['VALUE'], $arItem['VALUES']['MAX']['CURRENCY'], true); ?>"
                                            />
                                        </div>
                                    </div>
                                    <div class="bx_filter_parameters_box_container_block">
                                        <div class="bx_filter_input_container">
                                            <input
                                                    class="max-price fonts__middle_comment form-control form-control-sm"
                                                    type="text"
                                                    name="<?
                                                    echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
                                                    id="<?
                                                    echo $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
                                                    value="<?
                                                    echo $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
                                                    size="5"
                                                    onkeyup="smartFilter.keyup(this)"
                                                    placeholder="<?= Loc::getMessage('CT_BCSF_FILTER_TO') . CCurrencyLang::CurrencyFormat($arItem['VALUES']['MAX']['VALUE'], $arItem['VALUES']['MAX']['CURRENCY'], true); ?>"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?
                    $arJsParams = array(
                        "containerSlider" => 'slider_price_' . $key,
                        "minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
                        "maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
                        "minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
                        "maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
                        "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                        "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                        "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
                        "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                        "precision" => $precision,
                        "colorUnavailableActive" => 'colorUnavailableActive_' . $key,
                        "colorAvailableActive" => 'colorAvailableActive_' . $key,
                        "colorAvailableInactive" => 'colorAvailableInactive_' . $key,
                    );
                    ?>
                        <script type="text/javascript">
                            BX.ready(function () {
                                if (typeof window.trackBarOptions === 'undefined') {
                                    window.trackBarOptions = {};
                                }
                                window.trackBarOptions['<?=$key?>'] = <?=CUtil::PhpToJSObject($arJsParams)?>;
                                window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(window.trackBarOptions['<?=$key?>']);
                            });
                        </script>
                    <?endif;
                }
                //not prices
                foreach ($arResult["ITEMS"] as $key => $arItem) {
                    if (
                        empty($arItem["VALUES"])
                        || isset($arItem["PRICE"])
                    )
                        continue;

                    if (
                        $arItem["DISPLAY_TYPE"] == "A"
                        && (
                            $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0
                        )
                    )
                        continue;

                    $check = false;
                    foreach ($arItem['VALUES'] as $it) {
                        if (empty($it['DISABLED'])) {
                            $check = true;
                        }
                    }
                    if ($check):

                    ?>
                    <div class="bx_filter_parameters_box form-group
                    <? if ($arItem["DISPLAY_EXPANDED"] == "Y"): ?> active<? endif ?><? if ($arItem["CODE"] == 'RAZMER'): ?> filter-size<? endif ?>"
                         data-propid="<?= $arItem["ID"] ?>">
                        <div class="bx_filter_parameters_box_title text-uppercase text-muted fw-medium">
                             <span>
                                 <span class="item_name"><?= $arItem["NAME"] ?></span>
                             </span>
                        </div>
                        <div class="bx_filter_block bx_filter_block_wrapper" data-role="bx_filter_block">
                            <div class="bx_filter_parameters_box_container">
                                <?
                                $arCur = current($arItem["VALUES"]);
                                switch ($arItem["DISPLAY_TYPE"]) {
                                case "A"://NUMBERS_WITH_SLIDER
                                    ?>
                                    <div class="noui-height-helper" id="slider_price_<?= $key ?>"></div>
                                    <div class="input-range-number row">
                                        <div class="bx_filter_parameters_box_container-block bx-left">
                                            <div class="bx_filter_parameters_box_container_block">
                                                <div class="bx_filter_input_container">
                                                    <input
                                                            class="min-price fonts__middle_comment form-control form-control-sm"
                                                            type="text"
                                                            name="<?
                                                            echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
                                                            id="<?
                                                            echo $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
                                                            value="<?
                                                            echo $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                            placeholder="<?= Loc::getMessage('CT_BCSF_FILTER_FROM') . CCurrencyLang::CurrencyFormat($arItem['VALUES']['MIN']['VALUE'], $arItem['VALUES']['MAX']['CURRENCY'], true);?>"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bx_filter_parameters_box_container-block bx-right">
                                            <div class="bx_filter_parameters_box_container_block">
                                                <div class="bx_filter_input_container">
                                                    <input
                                                            class="max-price fonts__middle_comment form-control form-control-sm"
                                                            type="text"
                                                            name="<?
                                                            echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
                                                            id="<?
                                                            echo $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
                                                            value="<?
                                                            echo $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
                                                            size="5"
                                                            onkeyup="smartFilter.keyup(this)"
                                                            placeholder="<?= Loc::getMessage('CT_BCSF_FILTER_TO') . CCurrencyLang::CurrencyFormat($arItem['VALUES']['MAX']['VALUE'], $arItem['VALUES']['MAX']['CURRENCY'], true); ?>"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?
                                $arJsParams = array(
                                    "containerSlider" => 'slider_price_' . $key,
                                    "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                    "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                    "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
                                    "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                                    "precision" => $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0,
                                    "colorUnavailableActive" => 'colorUnavailableActive_' . $key,
                                    "colorAvailableActive" => 'colorAvailableActive_' . $key,
                                    "colorAvailableInactive" => 'colorAvailableInactive_' . $key,
                                );
                                ?>
                                    <script type="text/javascript">
                                        BX.ready(function () {
                                            if (typeof window.trackBarOptions === 'undefined') {
                                                window.trackBarOptions = {};
                                            }
                                            window.trackBarOptions['<?=$key?>'] = <?=CUtil::PhpToJSObject($arJsParams)?>;
                                            window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(window.trackBarOptions['<?=$key?>']);
                                        });
                                    </script>
                                <?
                                break;
                                case "B"://NUMBERS
                                ?>
                                    <div class="input-range-number row">
                                        <div class="bx_filter_parameters_box_container-block bx-left">
                                            <div class="bx_filter_input_container">
                                                <input
                                                        class="min-price form-control form-control-sm"
                                                        type="text"
                                                        name="<?
                                                        echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
                                                        id="<?
                                                        echo $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
                                                        value="<?
                                                        echo $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?>"
                                                        size="5"
                                                        onkeyup="smartFilter.keyup(this)"
                                                        placeholder="<?= GetMessage("CT_BCSF_FILTER_FROM") ?>"
                                                />
                                            </div>
                                        </div>
                                        <div class="bx_filter_parameters_box_container-block bx-right">
                                            <div class="bx_filter_input_container">
                                                <input
                                                        class="max-price form-control form-control-sm"
                                                        type="text"
                                                        name="<?
                                                        echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
                                                        id="<?
                                                        echo $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
                                                        value="<?
                                                        echo $arItem["VALUES"]["MAX"]["HTML_VALUE"] ?>"
                                                        size="5"
                                                        onkeyup="smartFilter.keyup(this)"
                                                        placeholder="<?= GetMessage("CT_BCSF_FILTER_TO") ?>"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                <?
                                break;
                                case "G"://CHECKBOXES_WITH_PICTURES
                                $ch = 0;
                                $maxDisplay = 7;
                                $totalValues = count($arItem["VALUES"]);
                                if ($maxDisplay > $totalValues)
                                    $maxDisplay = $totalValues;
                                ?>
                                    <div class="find_property_value_wrapper form-control-feedback form-control-feedback-start">
                                        <input type="text" class="form-control form-control-sm find_property_value"
                                               placeholder="<?= GetMessage('CT_BCSF_FILTER_SELECT') ?>">
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-search">
                                            <i class="ph-magnifying-glass find_property_value__icon-search"></i>
                                        </button>
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-close">
                                            <i class="ph-x"></i>
                                        </button>
                                    </div>
                                    <div class="blank_ul_wrapper type-checkbox-image <?= ($totalValues > $maxDisplay ? "perfectscroll" : "") ?>">
                                        <?
                                        foreach ($arItem["VALUES"] as $val => $ar):
                                            $ch++;
                                            ?>

                                            <?if(empty($ar["DISABLED"])):?>
                                            <div class="bx_filter_parameters_box_checkbox form-check-image mb-0 <? echo $ar["DISABLED"] ? 'disabled' : '' ?> <? echo $ar["CHECKED"] ? 'checked' : '' ?>">
                                                <input type="checkbox" class="position-absolute opacity-0"
                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                        value="<? echo $ar["HTML_VALUE"] ?>"
                                                        name="<? echo $ar["CONTROL_NAME"] ?>"
                                                        onclick="smartFilter.click(this)"
                                                    <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                                                    <? echo $ar["DISABLED"] ? 'disabled' : '' ?>
                                                >
                                                <label class="form-check-label"
                                                    for="<? echo $ar["CONTROL_ID"] ?>">
                                                    <span class="d-none"><?= $ar["VALUE"] ?></span>
                                                    <? if ($ar['SEO_LINK']): ?>

                                                        <a <?= ($ar['section_filter_link'] != "" ? 'href="' . $ar['section_filter_link'] . '"' : "") ?>>
                                                            <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                                <img class="bx-filter-btn-color-icon"
                                                                    src="<?= $ar["FILE"]["SRC"] ?>"
                                                                    title="<?= $ar["VALUE"] ?>"
                                                                    alt="<?= $ar["VALUE"] ?>"/>
                                                            <? else: ?>
                                                                <?= $ar["VALUE"] ?>
                                                    <? endif; ?>
                                                            <? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;

                                                                <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                    (<? echo $ar["ELEMENT_COUNT"]; ?>)
                                                                </span>

                                                            <? endif; ?>
                                                        </a>

                                                    <? else: ?>
                                                        <?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
                                                            <img class="bx-filter-btn-color-icon"
                                                                src="<?= $ar["FILE"]["SRC"] ?>"
                                                                title="<?= $ar["VALUE"] ?>"
                                                                alt="<?= $ar["VALUE"] ?>"/>
                                                        <? else: ?>
                                                            <?= $ar["VALUE"] ?>
                                                    <? endif; ?><?
                                                        if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                            ?>&nbsp;

                                                            <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                (<? echo $ar["ELEMENT_COUNT"]; ?>)
                                                            </span>

                                                            <?
                                                        endif; ?>
                                                    <? endif; ?>
                                                </label>
                                            </div>
                                            <?endif;?>
                                            <? if ($ch == $totalValues && $totalValues > $maxDisplay): ?>
                                            <!-- do nothing -->
                                        <? endif; ?>

                                        <? endforeach; ?>
                                    </div>
                                <?
                                break;
                                case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
                                    $ch = 0;
                                    $maxDisplay = 7;
                                    $totalValues = count($arItem["VALUES"]);
                                    if ($maxDisplay > $totalValues)
                                        $maxDisplay = $totalValues;
                                ?>
                                    <div class="find_property_value_wrapper form-control-feedback form-control-feedback-start">
                                        <input type="text" class="form-control form-control-sm find_property_value"
                                               placeholder="<?= GetMessage('CT_BCSF_FILTER_SELECT') ?>">
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-search">
                                            <i class="ph-magnifying-glass find_property_value__icon-search"></i>
                                        </button>
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-close">
                                            <i class="ph-x"></i>
                                        </button>
                                    </div>
                                    <div class="bx-filter-param--checkbox-pict-label">
                                        <? foreach ($arItem["VALUES"] as $val => $ar): ?>
                                            <div class="bx-filter-param-row">
                                                <input
                                                        style="display: none"
                                                        type="checkbox"
                                                        name="<?= $ar["CONTROL_NAME"] ?>"
                                                        id="<?= $ar["CONTROL_ID"] ?>"
                                                        value="<?= $ar["HTML_VALUE"] ?>"
                                                    <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                                                />
                                                <?
                                                $class = "";
                                                if ($ar["CHECKED"])
                                                    $class .= " bx-active";
                                                if ($ar["DISABLED"])
                                                    $class .= " disabled";
                                                ?>
                                                <label for="<?= $ar["CONTROL_ID"] ?>"
                                                    data-role="label_<?= $ar["CONTROL_ID"] ?>"
                                                    class="bx-filter-param-label<?= $class ?>"
                                                    onclick="selectSize(this);"
                                                >

                                                    <span class="bx-filter-param-btn">
                                                        <? if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])): ?>
                                                            <img class="bx-filter-btn-color-icon"
                                                                src="<?= $ar["FILE"]["SRC"] ?>"
                                                                alt="<?= $ar["VALUE"]; ?>"/>
                                                        <? endif ?>
                                                    </span>
                                                        <span class="bx-filter-param-text"
                                                                title="<?= $ar["VALUE"]; ?>"><?= $ar["VALUE"]; ?>
                                                    </span>
                                                </label>
                                            </div>
                                        <? endforeach ?>
                                    </div>
                                <?
                                break;
                                case "P"://DROPDOWN
                                ?>
                                    <select class="form-control select" 
                                        data-minimum-results-for-search="Infinity"
                                        name="<?= $arCur["CONTROL_NAME_ALT"] ?>"
                                        onchange="smartFilter.click(this)"
                                    >
                                        <option value="">
                                            <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
                                        </option>
                                        <?foreach ($arItem["VALUES"] as $val => $ar): ?>
                                            <option value="<?= $ar["HTML_VALUE_ALT"] ?>">
                                                <?= $ar["VALUE"] ?>
                                            </option>
                                        <?endforeach;?>
                                    </select>
                                <?
                                break;
                                case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
                                ?>
                                    <select class="form-control select-image" 
                                        data-minimum-results-for-search="Infinity"
                                        name="<?= $arCur["CONTROL_NAME_ALT"] ?>"
                                        onchange="smartFilter.click(this)"
                                    >
                                        <option value="" data-img-url="">
                                            <? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
                                        </option>
                                        <?foreach ($arItem["VALUES"] as $val => $ar): ?>
                                            <option value="<?= $ar["HTML_VALUE_ALT"] ?>" data-img-url="<?= $ar["FILE"]["SRC"] ?>">
                                                <?= $ar["VALUE"] ?>
                                            </option>
                                        <?endforeach;?>
                                    </select>
                                <?
                                break;
                                case "K"://RADIO_BUTTONS
                                    $maxDisplay = 7;
                                    $totalValues = count($arItem["VALUES"]);
                                    if ($maxDisplay > $totalValues)
                                        $maxDisplay = $totalValues;
                                ?>
                                    <div class="find_property_value_wrapper form-control-feedback form-control-feedback-start">
                                        <input type="text" class="form-control form-control-sm find_property_value"
                                               placeholder="<?= GetMessage('CT_BCSF_FILTER_SELECT') ?>">
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-search">
                                            <i class="ph-magnifying-glass find_property_value__icon-search"></i>
                                        </button>
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-close">
                                            <i class="ph-x"></i>
                                        </button>
                                    </div>

                                    <div class="blank_ul_wrapper type-radio <?= ($totalValues > $maxDisplay ? "perfectscroll" : "") ?>">
                                    <?
                                        if (!empty($arItem["VALUES"])) : ?>
                                            <div class="bx_filter_parameters_box_radio form-check">
                                                <input type="radio" class="form-check-input"
                                                    id="<?= 'all_' . $arCur["CONTROL_ID"] ?>"
                                                    name="<?= $arCur['CONTROL_NAME_ALT'] ?>"
                                                    value=""
                                                    onclick="smartFilter.click(this)"
                                                    >
                                                <label class="form-check-label" for="<?= 'all_' . $arCur["CONTROL_ID"] ?>">
                                                    <?= GetMessage("CT_BCSF_FILTER_ALL");?>
                                                </label>
                                            </div>
                                        <? endif;
                                        foreach ($arItem["VALUES"] as $val => $ar):
                                            $ch++;
                                            ?>
                                            <?if(empty($ar["DISABLED"])):?>
                                                <div class="bx_filter_parameters_box_radio form-check <? echo $ar["DISABLED"] ? 'disabled' : '' ?>">
                                                    <input type="radio" class="form-check-input" 
                                                        id="<?= $ar["CONTROL_ID"] ?>"
                                                        name="<? echo $ar["CONTROL_NAME_ALT"] ?>"
                                                        value="<? echo $ar["HTML_VALUE_ALT"] ?>"
                                                        onclick="smartFilter.click(this)"
                                                        <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                                                        <? echo $ar["DISABLED"] ? 'disabled' : '' ?>
                                                        >
                                                    <label class="form-check-label" for="<?= $ar["CONTROL_ID"] ?>">
                                                    <? if ($ar['SEO_LINK']): ?>
                                                        <a <?= ($ar['section_filter_link'] != "" ? 'href="' . $ar['section_filter_link'] . '"' : "") ?>>
                                                            <?= $ar["VALUE"]; ?>
                                                            <? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;

                                                                <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                    (<? echo $ar["ELEMENT_COUNT"]; ?>)
                                                                </span>
                                                                
                                                            <? endif; ?>
                                                        </a>

                                                        <? else: ?>
                                                        <?= $ar["VALUE"]; ?><?
                                                        if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                            ?>&nbsp;(

                                                            <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                <? echo $ar["ELEMENT_COUNT"]; ?>
                                                            </span>

                                                            )<?
                                                        endif; ?>
                                                        <? endif; ?>
                                                    </label>
                                                </div>
                                            <?endif;?>
                                            <? 
                                        endforeach; 
                                    ?>
                                    </div>
                                <?
                                break;
                                case "U"://CALENDAR
                                ?>
                                    <div class="">
                                        <div class="bx_filter_parameters_box_container-block">
                                            <div class="bx_filter_input_container bx-filter-calendar-container">
                                                <?
                                                $APPLICATION->IncludeComponent(
                                                    'bitrix:main.calendar',
                                                    '',
                                                    array(
                                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                                        'SHOW_INPUT' => 'Y',
                                                        'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                                        'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
                                                        'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                                        'SHOW_TIME' => 'N',
                                                        'HIDE_TIMEBAR' => 'Y',
                                                    ),
                                                    null,
                                                    array('HIDE_ICONS' => 'Y')
                                                ); ?>
                                            </div>
                                        </div>
                                        <div class="bx_filter_parameters_box_container-block">
                                            <div class="bx_filter_input_container bx-filter-calendar-container">
                                                <?
                                                $APPLICATION->IncludeComponent(
                                                    'bitrix:main.calendar',
                                                    '',
                                                    array(
                                                        'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
                                                        'SHOW_INPUT' => 'Y',
                                                        'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
                                                        'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
                                                        'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                                                        'SHOW_TIME' => 'N',
                                                        'HIDE_TIMEBAR' => 'Y',
                                                    ),
                                                    null,
                                                    array('HIDE_ICONS' => 'Y')
                                                ); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?
                                break;
                                default://CHECKBOXES

                                $ch = 0;
                                $maxDisplay = 7;
                                $totalValues = count($arItem["VALUES"]);
                                if ($maxDisplay > $totalValues)
                                    $maxDisplay = $totalValues;
                                ?>

                                    <div class="find_property_value_wrapper form-control-feedback form-control-feedback-start">
                                        <input type="text" class="form-control form-control-sm find_property_value"
                                               placeholder="<?= GetMessage('CT_BCSF_FILTER_SELECT') ?>">
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-search">
                                            <i class="ph-magnifying-glass find_property_value__icon-search"></i>
                                        </button>
                                        <button class="form-control-feedback-icon find_property_value__button find_property_value__button-close">
                                            <i class="ph-x"></i>
                                        </button>
                                    </div>
                                    <div class="blank_ul_wrapper type-checkbox <?= ($totalValues > $maxDisplay ? "perfectscroll" : "") ?>">
                                        <?
                                        foreach ($arItem["VALUES"] as $val => $ar):
                                            $ch++;
                                            ?>

                                            <?if(empty($ar["DISABLED"])):?>
                                            <div class="bx_filter_parameters_box_checkbox form-check <? echo $ar["DISABLED"] ? 'disabled' : '' ?>">
                                                <input type="checkbox" class="form-check-input checkbox_custom_filter"
                                                        id="<? echo $ar["CONTROL_ID"] ?>"
                                                        value="<? echo $ar["HTML_VALUE"] ?>"
                                                        name="<? echo $ar["CONTROL_NAME"] ?>"
                                                        onclick="smartFilter.click(this)"
                                                    <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                                                    <? echo $ar["DISABLED"] ? 'disabled' : '' ?>
                                                >
                                                <label class="form-check-label checkbox__label fonts__middle_comment"
                                                    for="<? echo $ar["CONTROL_ID"] ?>">
                                                    <? if ($ar['SEO_LINK']): ?>

                                                        <a <?= ($ar['section_filter_link'] != "" ? 'href="' . $ar['section_filter_link'] . '"' : "") ?>>
                                                            <?= $ar["VALUE"]; ?>
                                                            <? if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;

                                                                <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                    (<? echo $ar["ELEMENT_COUNT"]; ?>)
                                                                </span>

                                                            <? endif; ?>
                                                        </a>

                                                    <? else: ?>
                                                        <?= $ar["VALUE"]; ?><?
                                                        if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
                                                            ?>&nbsp;

                                                            <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                (<? echo $ar["ELEMENT_COUNT"]; ?>)
                                                            </span>

                                                            <?
                                                        endif; ?>
                                                    <? endif; ?>
                                                </label>
                                            </div>
                                            <?endif;?>
                                            <? if ($ch == $totalValues && $totalValues > $maxDisplay): ?>
                                            <!-- do nothing -->
                                        <? endif; ?>

                                        <? endforeach; ?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <? endif;
                }
                ?>
                <div class="clb"></div>
                <div class="anchor_filter"></div>
            </div>
            <!-- filter buttons -->
            <div class="row-under-modifications-filter row-under-modifications-filter-fixed">
                <div class="bx_filter_button_box active">
                    <div class="bx_filter_block button">
                        <div class="bx_filter_parameters_box_container filter_buttons">
                            <div class="bx_filter_popup_result <?= $arParams["POPUP_POSITION"] ?>"
                                 id="modef" <? if (!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"'; ?>
                                 style="display: inline-block;">
                                <a style="display: none;" href="<?= $arResult["SEF_DEL_FILTER_URL"] ?>"
                                   class="del_filter"><?= GetMessage("CT_BCSF_DEL_FILTER") ?></a>
                                <a style="display: none;" href="<?= $arResult["FILTER_URL"] ?>"
                                   class="set_filter"><?= GetMessage("CT_BCSF_FILTER_SHOW") ?></a>
                                <? echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">' . intval($arResult["ELEMENT_COUNT"]) . '</span>')); ?>
                            </div>
                            <input class="bx_filter_search_reset fonts__main_comment btn" type="submit"
                                   id="del_filter"
                                   name="del_filter" value="<?= GetMessage("CT_BCSF_DEL_FILTER") ?>">
                            <input class="bx_filter_search_button fonts__main_comment btn btn-primary" type="submit"
                                   id="set_filter"
                                   name="set_filter" value="<?= GetMessage("CT_BCSF_SET_FILTER") ?>">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /filter buttons -->
        </div>
    </div>
</form>
<!-- /form>-->

<?
$arResult["JS_FILTER_PARAMS"]['FROM'] = GetMessage("CT_BCSF_FILTER_FROM");
$arResult["JS_FILTER_PARAMS"]['TO'] = GetMessage("CT_BCSF_FILTER_TO");
if ($arParams['INSTANT_RELOAD'])
    $arResult["JS_FILTER_PARAMS"]['SEF_SET_FILTER_URL'] = $arResult["FILTER_AJAX_URL"];
$arResult["JS_FILTER_PARAMS"]['INSTANT_RELOAD'] = $arParams['INSTANT_RELOAD'];
?>

<script type="text/javascript">
    BX.message({
       "form_submit": '<?=GetMessage("CT_BCSF_SET_FILTER")?>'
    });
    var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>