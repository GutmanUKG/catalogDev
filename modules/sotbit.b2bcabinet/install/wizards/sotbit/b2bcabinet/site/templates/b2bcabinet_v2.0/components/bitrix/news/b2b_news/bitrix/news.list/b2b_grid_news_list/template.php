<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc;
?>

<? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
    <div class="mb-3">
        <?= $arResult["NAV_STRING"] ?>
    </div>
<? endif; ?>

<div class="row">
<? if (count($arResult["ITEMS"]) > 0): ?>
    <?foreach ($arResult["ITEMS"] as$colNum => $arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('NEWS_DELETE_CONFIRM')));
        ?>
        <div class="col-xxxl-4 col-xxl-3 col-md-4 col-sm-6 col-12 mb-3">
            <div class="card h-100" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["RESIZE_PICTURE"])): ?>
                        <div class="card-img-actions pt-70 rounded-top overflow-hidden">
                            <img
                                class="img-fluid object-fit-cover position-absolute top-0 w-100 h-100"
                                src="<?= $arItem["RESIZE_PICTURE"]["src"] ?>"
                                width="<?= $arItem["RESIZE_PICTURE"]["width"] ?>"
                                height="<?= $arItem["RESIZE_PICTURE"]["height"] ?>"
                                alt="<? echo $arItem["TITLE_FORMATED"] ?>"
                            />
                            <a class="card-img-actions-overlay" href="<?= $arItem["DETAIL_PAGE_URL"] ?>"></a>
                        </div>
                    <? endif ?>

                <div class="card-body d-flex flex-column">
                    <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                        <h5 class="font-weight-semibold mb-0">
                            <a class="text-body" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                <? echo $arItem["NAME"] ?>
                            </a>
                        </h5>
                    <? endif; ?>

                    <? if (!empty($arItem["FIELDS"]["DATE_ACTIVE_FROM"])): ?>
                        <span class="text-muted fs-sm"><?= $arItem["FIELDS"]["DATE_ACTIVE_FROM"] ?></span>
                    <? endif; ?>
                    <div class="mt-2 mb-2 flex-grow-1">
                        <p class="max-lines mb-0">
                            <? echo $arItem["PREVIEW_TEXT"] ?>
                        </p>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 fix-mt-3">
                        <span class="text-muted text-nowrap">
                            <?= $arItem["FIELDS"]["SHOW_COUNTER"] ? 
                                $arItem["FIELDS"]["SHOW_COUNTER"] . ' ' . \Sotbit\B2bCabinet\Element::num2word($arItem["FIELDS"]["SHOW_COUNTER"], [
                                                                            Loc::getMessage('ONE_COUNTER'),
                                                                            Loc::getMessage('SOME_COUNTER'),
                                                                            Loc::getMessage('MORE_COUNTER'),
                                                                            ]
                                ) : 
                                '' 
                            ?>
                        </span>
                        <span class="text-muted">
                            <?= $arItem["FIELDS"]["CREATED_USER_NAME"] ?: '' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?endforeach;?>
    <?else:?>
        <div class="col-md-12">
            <? ShowError(Loc::GetMessage("NOT_FOUND_ITEMS"), 'validation-invalid-label'); ?>
        </div>
    <?endif;?>
</div>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<? endif; ?>
