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
                <div class="input-group mb-3">
                    <div class="form-group-feedback form-group-feedback-left">
                        <input type="text" name="q" value="<?= $arResult["REQUEST"]["QUERY"] ?>"
                               class="form-control form-control-lg alpha-grey" placeholder="">
                        <div class="form-control-feedback form-control-feedback-lg">
                            <i class="icon-search4 text-muted"></i>
                        </div>
                    </div>

                    <div class="input-group-append">
                        <button type="submit" class="btn btn_b2b"><?= GetMessage("SEARCH_GO") ?></button>
                    </div>
                </div>
                <input type="hidden" name="how" value="<? echo $arResult["REQUEST"]["HOW"] == "d" ? "d" : "r" ?>"/>
                <div class="form-group-feedback form-group-feedback-left">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:search.tags.cloud", "b2b_search_tags_cloud", $arCloudParams, $component);
                    ?>
                </div>
                <div class="form-group-feedback form-group-feedback-left">
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
            <div class="col-md-4">
                <div class="card">
                    <div class="card-img-actions px-1 pt-1">
                        <? if ($arItem["PREVIEW_PICTURE"]): ?>
                            <img class="card-img img-fluid" src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="">
                        <? endif; ?>
                        <div class="card-img-actions-overlay card-img">
                            <a href="<?= $arItem["URL"] ?>"
                               class="btn btn-outline bg-white text-white border-white border-2 btn-icon rounded-round ml-2">
                                <i class="icon-link"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="font-weight-semibold">
                            <a class="text-default" href="<? echo $arItem["URL"] ?>">
                                <? echo $arItem["TITLE_FORMATED"] ?>
                            </a>
                        </h6>

                        <? if ($arItem["DISPLAY_FIELDS"]): ?>
                            <ul class="list-inline list-inline-dotted text-muted mb-3">
                                <? foreach ($arItem["DISPLAY_FIELDS"] as $key => $field): ?>
                                    <li class="list-inline-item">
                                    <span class="text-muted">
                                        <?= Loc::getMessage("NEWS_DETAIL_SEARCH_" . $key) . $field ?>
                                    </span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        <? endif; ?>

                        <? echo $arItem["BODY_FORMATED"] ?>
                    </div>
                    <div class="card-footer bg-transparent d-sm-flex justify-content-sm-between align-items-sm-center border-top-0 pt-0 pb-3">
                        <? if (!empty($arItem["TAGS"])): ?>
                            <ul class="list-inline list-inline-condensed mb-3 mb-sm-0">
                                <? foreach ($arItem["TAGS"] as $tags): ?>
                                    <li class="list-inline-item">
                                        <span class="badge badge-b2b"><?= $tagItem["TAG_NAME"] ?></span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        <? endif; ?>
                        <a class="btn btn_b2b" href="<?= $arItem["URL"] ?>">
                            <?= Loc::getMessage("READ_MORE"); ?><i class="icon-arrow-right14 ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>

    <? if ($arParams["DISPLAY_BOTTOM_PAGER"] == "Y"): ?>
        <?= $arResult["NAV_STRING"]; ?>
    <? endif; ?>

<? else: ?>
    <? ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND")); ?>
<? endif; ?>