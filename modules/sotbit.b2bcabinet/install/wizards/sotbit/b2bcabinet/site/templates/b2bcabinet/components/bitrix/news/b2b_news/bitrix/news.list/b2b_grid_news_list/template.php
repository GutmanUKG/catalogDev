<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;
?>

<? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<? endif; ?>

<div class="row">
    <?foreach ($arResult["ITEMS"] as$colNum => $col):?>
        <div class="col-md-4">
            <?foreach ($col as $itemNum => $arItem):
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('NEWS_DELETE_CONFIRM')));
                ?>
                <div class="card" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <div class="card-body">
                        <? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                            <div class="card-img-actions mb-3">
                                <img
                                        class="card-img img-fluid"
                                        src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                        width="<?= $arItem["PREVIEW_PICTURE"]["WIDTH"] ?>"
                                        height="<?= $arItem["PREVIEW_PICTURE"]["HEIGHT"] ?>"
                                        alt=""
                                />
                                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                                    <div class="card-img-actions-overlay card-img">
                                        <a class="btn btn-outline bg-white text-white border-white border-2 btn-icon rounded-round"
                                           href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                            <i class="icon-link">
                                            </i>
                                        </a>
                                    </div>
                                <? endif; ?>
                            </div>
                        <? endif ?>

                        <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                            <h5 class="font-weight-semibold mb-1">
                                <a class="text-default" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                    <? echo $arItem["NAME"] ?>
                                </a>
                            </h5>
                        <? endif; ?>

                        <? if ($arItem["FIELDS"]): ?>
                            <ul class="list-inline list-inline-dotted text-muted mb-3">
                                <? foreach ($arItem["FIELDS"] as $key => $FieldItem): ?>
                                    <? if ($FieldItem != ''): ?>
                                        <li class="list-inline-item">
                                            <?= Loc::getMessage("NEWS_DETAIL_SEARCH_" . $key); ?>
                                            <span class="text-muted"><?= $FieldItem ?></span>
                                        </li>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </ul>
                        <? endif; ?>

                        <? echo $arItem["PREVIEW_TEXT"] ?>
                    </div>
                    <div class="card-footer d-flex">
                        <? if ($arItem["TAGS"]): ?>
                            <ul class="list-inline list-inline-condensed mb-3 mb-sm-0">
                                <? foreach ($arItem["TAGS"] as $tagItem): ?>
                                    <li class="list-inline-item">
                                        <span class="badge badge-b2b"><?= $tagItem ?></span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        <? endif; ?>
                        <a class="ml-auto" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                            <?= Loc::getMessage("READ_MORE"); ?><i class="icon-arrow-right14 ml-2"></i>
                        </a>
                    </div>
                </div>
            <?endforeach;?>
        </div>
    <?endforeach;?>
</div>



<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<? endif; ?>
