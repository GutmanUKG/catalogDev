<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arCloudParams = array(
    "SEARCH" => $arResult["REQUEST"]["~QUERY"],
    "TAGS" => $arResult["REQUEST"]["~TAGS"],
    "CHECK_DATES" => $arParams["CHECK_DATES"],
    "arrFILTER" => $arParams["arrFILTER"],
    "SORT" => $arParams["TAGS_SORT"],
    "PAGE_ELEMENTS" => $arParams["TAGS_PAGE_ELEMENTS"],
    "PERIOD" => $arParams["TAGS_PERIOD"],
    "URL_SEARCH" => $arParams["TAGS_URL_SEARCH"],
    "TAGS_INHERIT" => $arParams["TAGS_INHERIT"],
    "PERIOD_NEW_TAGS" => $arParams["PERIOD_NEW_TAGS"],
    "SHOW_CHAIN" => $arParams["SHOW_CHAIN"],
    "WIDTH" => $arParams["WIDTH"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "RESTART" => $arParams["RESTART"],
);
?>

    <form action="" method="get">
        <input type="hidden" name="tags" value="<? echo $arResult["REQUEST"]["TAGS"] ?>"/>
        <? if ($arParams["SHOW_WHERE"]): ?>
            &nbsp;<select name="where">
                <option value=""><?= Loc::GetMessage("SEARCH_ALL") ?></option>
                <? foreach ($arResult["DROPDOWN"] as $key => $value): ?>
                    <option value="<?= $key ?>"<? if ($arResult["REQUEST"]["WHERE"] == $key) echo " selected" ?>><?= $value ?></option>
                <? endforeach ?>
            </select>
        <? endif; ?>
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3"><?= Loc::GetMessage('SEARCH_RESULT') ?></h5>
                <div class="input-group w-md-50 w-100 mb-3">
                    <div class="form-control-feedback form-control-feedback-start w-100">
                        <input type="text" name="q" value="<?= $arResult["REQUEST"]["QUERY"] ?>"
                               class="form-control bg-white border-primary" placeholder="">
                        <button class="search__submit form-control-feedback-icon" name="s" type="submit">
                            <i class="ph-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="how" value="<? echo $arResult["REQUEST"]["HOW"] == "d" ? "d" : "r" ?>"/>
                <div class="form-group-feedback form-group-feedback-left">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:search.tags.cloud", "b2b_search_tags_cloud", $arCloudParams, $component);
                    ?>
                </div>
                <div class="form-group-feedback form-group-feedback-left news-list__sort-fields">
                    <? if ($arResult["REQUEST"]["HOW"] == "d"): ?>
                        <a href="<?= $arResult["URL"] ?>&amp;how=r"><?= Loc::GetMessage("SEARCH_SORT_BY_RANK") ?></a>&nbsp;|&nbsp;
                        <b><?= Loc::GetMessage("SEARCH_SORTED_BY_DATE") ?></b>
                    <? else: ?>
                        <b><?= Loc::GetMessage("SEARCH_SORTED_BY_RANK") ?></b>&nbsp;|&nbsp;<a
                                href="<?= $arResult["URL"] ?>&amp;how=d">
                            <?= Loc::GetMessage("SEARCH_SORT_BY_DATE") ?></a>
                    <? endif; ?>
                </div>
            </div>
        </div>
    </form>

<? if (isset($arResult["REQUEST"]["ORIGINAL_QUERY"])): ?>
    <div class="search-language-guess">
        <? echo Loc::GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#" => '<a href="' . $arResult["ORIGINAL_QUERY_URL"] . '">' . $arResult["REQUEST"]["ORIGINAL_QUERY"] . '</a>')) ?>
    </div><br/>
<? endif; ?>

<? if ($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false): ?>
<? elseif (count($arResult["SEARCH"]) > 0): ?>

    <? if ($arParams["DISPLAY_TOP_PAGER"] == "Y"): ?>
        <?= $arResult["NAV_STRING"]; ?>
    <? endif; ?>

    <div class="row">
        <? foreach ($arResult["SEARCH"] as $arItem): ?>
            <div class="col-xxl-3 col-md-4 col-sm-4 mb-3">
                <div class="card h-100">
                    <div class="card-img-actions pt-70">
                        <img
                        class="img-fluid object-fit-cover position-absolute top-0 w-100 h-100"
                        src="<?= $arItem["RESIZE_PICTURE"]["src"] ?>"
                        width="<?= $arItem["RESIZE_PICTURE"]["width"] ?>"
                        height="<?= $arItem["RESIZE_PICTURE"]["height"] ?>"
                        alt="<? echo $arItem["TITLE_FORMATED"] ?>"
                        />
                        <a class="card-img-actions-overlay" href="<?= $arItem["URL"] ?>"></a>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <? if (!empty($arItem["TITLE_FORMATED"])): ?>
                            <h5 class="font-weight-semibold mb-0">
                                <a class="text-body" href="<?= $arItem["URL"] ?>">
                                    <? echo $arItem["TITLE_FORMATED"] ?>
                                </a>
                            </h5>
                        <? endif; ?>

                        <? if (!empty($arItem["DISPLAY_FIELDS"]["DATE_ACTIVE_FROM"])): ?>
                            <span class="text-muted fs-sm"><?= $arItem["DISPLAY_FIELDS"]["DATE_ACTIVE_FROM"] ?></span>
                        <? endif; ?>

                        <div class="mt-2 mb-2 flex-grow-1">
                            <p class="max-lines mb-0">
                                <? echo $arItem["BODY_FORMATED"] ?>
                            </p>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 fix-mt-3">
                            <span class="text-muted text-nowrap">
                                <?= $arItem["DISPLAY_FIELDS"]["SHOW_COUNTER"] ? 
                                    $arItem["DISPLAY_FIELDS"]["SHOW_COUNTER"] . ' ' .\Sotbit\B2bCabinet\Element::num2word($arItem["DISPLAY_FIELDS"]["SHOW_COUNTER"], [
                                                                                Loc::GetMessage('ONE_COUNTER'),
                                                                                Loc::GetMessage('SOME_COUNTER'),
                                                                                Loc::GetMessage('MORE_COUNTER'),
                                                                                ]
                                    ) : 
                                    '' 
                                ?>
                            </span>
                            <span class="text-muted">
                                <?= $arItem["DISPLAY_FIELDS"]["CREATED_USER_NAME"] ?: '' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>

    <? if ($arParams["DISPLAY_BOTTOM_PAGER"] == "Y"): ?>
        <?= $arResult["NAV_STRING"]; ?>
    <? endif; ?>

<? else: ?>
    <? ShowError(Loc::GetMessage("SEARCH_NOTHING_TO_FOUND"), 'validation-invalid-label'); ?>
<? endif; ?>