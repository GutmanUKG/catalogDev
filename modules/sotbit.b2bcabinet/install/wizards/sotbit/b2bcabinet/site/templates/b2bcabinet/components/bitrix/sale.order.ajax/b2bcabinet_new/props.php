<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<? foreach ($arResult["PROPS_GROUP"] as $key => $group): ?>
    <?
    if (!$group["PROPS"] || (is_array($group["PROPS"]) && empty(implode('', $group["PROPS"]))))
        continue;

    $row++;
    ?>
    <div class="card card-order-group <?if (array_key_last($arResult["PROPS_GROUP"]) == $key && ($row & 1) !== 0 ) echo "odd-sections";?>">
        <div class="card-header header-elements-inline checkout_form-title_inner">
            <h6 class="card-title"><span><?= $group["NAME"] ?></span>
            </h6>
        </div>
        <div class="card-body">
            <?
            PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"],
                $group['PROPS'], $arResult["EXTENDED_VERSION_COMPANIES"]);
            PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"],
                $group['PROPS'], $arResult["EXTENDED_VERSION_COMPANIES"]);
            PrintPropsForm($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"],
                $group['PROPS'], $arResult["EXTENDED_VERSION_COMPANIES"]);
            ?>

            <? if ($key === array_key_last($arResult["PROPS_GROUP"])): ?>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label"><?= GetMessage("SOA_TEMPL_SUM_COMMENTS") ?></label>
                    <div class="col-lg-9">
                    <textarea rows="5" cols="5" class="form-control" name="ORDER_DESCRIPTION"
                      id="ORDER_DESCRIPTION"><?= $arResult["USER_VALS"]["ORDER_DESCRIPTION"] ?></textarea>
                        <input type="hidden" name="" value="">
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
<? endforeach; ?>


<? if (!CSaleLocation::isLocationProEnabled()): ?>
    <div style="display:none;">

        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.ajax.locations",
            $arParams["TEMPLATE_LOCATION"],
            [
                "AJAX_CALL" => "N",
                "COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
                "REGION_INPUT_NAME" => "REGION_tmp",
                "CITY_INPUT_NAME" => "tmp",
                "CITY_OUT_LOCATION" => "Y",
                "LOCATION_VALUE" => "",
                "ONCITYCHANGE" => "submitForm()",
            ],
            null,
            ['HIDE_ICONS' => 'Y']
        ); ?>

    </div>
<? endif ?>
