<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Config\Option;
use Sotbit\B2bCabinet\Form\FormConstructor,
    Bitrix\Main\Text\HtmlFilter,
    Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);
\Bitrix\Main\UI\Extension::load(['sidepanel']);
?>

<div class="complaints-add__success-block">
    <div class="alert alert-success alert-dismissible">
    </div>
    <div class="d-flex gap-3 flex-column flex-sm-row">
        <a class="btn order-1 order-sm-0" href="<?=$arParams["SEF_URL_TEMPLATES"]["list"]?>">
            <?=Loc::getMessage("SOTBIT_COMPLAINTS_SUCCESS_BTN_TO_LIST")?>
        </a>
        <a class="btn btn-primary" href="<?=$arParams["SEF_URL_TEMPLATES"]["add"]?>">
            <?=Loc::getMessage("SOTBIT_COMPLAINTS_SUCCESS_BTN_ADD")?>
        </a>
    </div>
</div>

<div class="complaints-add-wrap">
    <div class="col-md-12">
        <div class="complaint__error-block alert alert-danger alert-dismissible"></div>
    </div>
    <form id="complaint-add" action="<?=$APPLICATION->GetCurPage()?>" method="post">
        <div class="row">
            <? foreach ($arResult["FORM_GROUPS"] as $groups): ?>
                <div class="col-lg-6">
                    <? foreach ($groups as $group): ?>
                        <div class="card">
                            <div class="card-header d-flex flex-wrap">
                                <h6 class="card-title mb-0 fw-bold">
                                    <?= HtmlFilter::encode($group["NAME"]);?>
                                </h6>
                                <div class="d-inline-flex ms-auto">
                                    <a class="text-body px-2" 
                                       data-card-action="collapse">
                                        <i class="ph ph-caret-down"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="collapse show">
                                <div class="card-body pt-0">
                                    <? foreach ($group["ITEM_ROWS"] as $row): ?>
                                        <div class="form-group">
                                            <label class="form-label">
                                                <?= HtmlFilter::encode($row["LABEL"]) ?>
                                                <? if($row["IS_REQUIRED"] === 'Y'): ?>
                                                    <span class="req">*</span>
                                                <? endif; ?>
                                            </label>
                                            <? 
                                            foreach ($row["ITEMS"] as $item) { 
                                                switch($item["INPUT_TYPE"]) {
                                                    case "SELECT":
                                                        ?>
                                                        <select class="form-control select"
                                                                <?=count(is_array($item["OPTIONS"]) ? $item["OPTIONS"] : []) < 20 ? 'data-minimum-results-for-search="Infinity"' : ''?>
                                                                name="<?=$item["ATTRIBUTES"]["NAME"]?>" 
                                                               <?= $row["IS_REQUIRED"] === 'Y' ? "required" : "" ?>
                                                               >
                                                               <? foreach ($item["OPTIONS"] as $option): ?>
                                                                <option value="<?=$option["VALUE"]?>"
                                                                    <?=$item["ATTRIBUTES"]["VALUE"] === $option["VALUE"] ? 'selected' : ''?>
                                                                    >
                                                                    <?=$option["OPTION_NAME"]?>
                                                                </option>
                                                               <? endforeach; ?> 
                                                        </select>
                                                        <?
                                                        break;
                                                    case "TEXTAREA":
                                                        ?>
                                                        <textarea class="form-control"
                                                                  name="<?=$item["ATTRIBUTES"]["NAME"]?>"
                                                                  <?= $row["IS_REQUIRED"] === 'Y' ? "required" : "" ?>
                                                                  ><?= trim(HtmlFilter::encode($item["ATTRIBUTES"]["VALUE"]))?></textarea>
                                                        <?
                                                        break;
                                                    case "TEXT":
                                                        ?>
                                                        <input class="form-control"
                                                               name="<?=$item["ATTRIBUTES"]["NAME"]?>" 
                                                               <?= $row["IS_REQUIRED"] === 'Y' ? "required" : "" ?>
                                                               value="<?=$item["ATTRIBUTES"]["VALUE"]?>"/>
                                                        <?
                                                        break;
                                                    case "FILE":
                                                        ?>
                                                        <?
                                                            $APPLICATION->IncludeComponent(
                                                                "bitrix:main.file.input",
                                                                "",
                                                                [
                                                                    "INPUT_NAME" => $item["ATTRIBUTES"]["NAME"],
                                                                    "MULTIPLE" => $item["ATTRIBUTES"]["MULTIPLE"],
                                                                    "MODULE_ID" => "main",
                                                                    "MAX_FILE_SIZE" => "",
                                                                    "ALLOW_UPLOAD" => "F",
                                                                    "ALLOW_UPLOAD_EXT" => $item["ATTRIBUTES"]["FILE_TYPE"],
                                                                ],
                                                                false
                                                            );
                                                        ?>
                                                        <?
                                                        break;
                                                    case ("Y/N" || "CHECKBOX"):
                                                        break;
                                                }
                                            } 
                                            ?>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endforeach; ?>
        </div>
        <?
        // $form = new FormConstructor("COMPLAINT_ADD", $arResult["FORM_SETTINGS"],  $arResult["FORM_GROUPS"]);
        // print $form->renderFormGroups("COMPLAINT_ADD");
        ?>
        <div class="datatable-wrapper">
            <table id="complaint-positions__grid" class="table datatable-highlight">
                <thead>
                    <tr>
                        <?foreach ($arResult["COLUMN_TITLE"] as $positionsTitle):?>
                            <th><?=$positionsTitle?></th>
                        <?endforeach;?>
                        <th>
                            <a role="button" class="delete-all-position" >
                                <?=Loc::getMessage("SOTBIT_COMPLAINTS_BTN_REMOVE")?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="card card-position-sticky">
            <div class="card-body d-flex gap-3">
                <button type="submit" class="btn btn-primary"><?=Loc::getMessage("SOTBIT_COMPLAINTS_BTN_ADD")?></button>
            </div>
        </div>
    </form>
</div>

<script>
    BX.message(<?=\Bitrix\Main\Web\Json::encode(\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__))?>);
    var searchPage = '<?=$arParams["SEF_URL_TEMPLATES"]["product_search"]?>';
    var comlaintsType = '<?=Option::get("sotbit.complaints", "COMPLAINTS_WITH_ORDER", "", SITE_ID);?>';
    var oderIdParam = '<?=$_REQUEST['orderId'];?>';
    const $tablePosition = $('#complaint-positions__grid');
    if (comlaintsType == "ORDER" && oderIdParam > 0) {
        document.getElementsByName("COMPLAINTS[PROPERTIES][ORDER_ID]")[0].value = oderIdParam;
    }

    function renderRowsItem(products) {
        if (Array.isArray(products)) {
            let rowCount = $tablePosition.DataTable().rows().count();
            const arRows = products.map(product => {
                rowCount++;
                return [<? echo "`". implode("`,`", $arResult["COLUMN_MODEL"]) . "`"?>];
            });

            $tablePosition.DataTable().rows.add(arRows).draw();
            App.initSelect2();
        }
    }

    function renderRowItem(product) {
        const rowCount = $tablePosition.DataTable().rows().count();
        const arRow = [<? echo "`". implode("`,`", $arResult["COLUMN_MODEL"]) . "`"?>];

        $tablePosition.DataTable().row.add(arRow).draw();
        App.initSelect2();
    }
</script>
