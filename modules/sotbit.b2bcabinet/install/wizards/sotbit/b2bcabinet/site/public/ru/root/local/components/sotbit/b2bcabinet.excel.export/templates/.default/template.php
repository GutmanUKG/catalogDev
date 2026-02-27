<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Web\Json;


global ${$arParams["FILTER_NAME"]};
$strRand = $this->randString();
$strObName = 'obExcelExport_' . $strRand;

?>

<? if ($arResult["ERROR_LIST"]):
    foreach ($arResult["ERROR_LIST"] as $obError):
        ShowError($obError->getMessage());
    endforeach;
else:?>
    <?if ($arParams["USE_BTN"] !== 'N'):?>
        <button
                type="button"
                class="btn btn-light btn-ladda btn-ladda-spinner"
                data-spinner-color="#333"
                data-style="slide-right" id="blank-export-in-excel"
            <? if ($arResult["MODEL_OF_WORK"] == "user_config") {
                echo 'data-toggle="modal" data-target="#modal_cond_tree"';
            } ?>

        >
        <span class="ladda-label export_excel_preloader">
            <i class="icon-upload mr-2"></i>
            <?=$arParams["BTN_TITLE"] ?: Loc::getMessage("B2B_EXCEL_EXPORT_BTN_TITLE")?>
        </span>
        </button>
    <?else:?>
        <span
                id="blank-export-in-excel"
                data-spinner-color="#333"
                class="dropdown-item  btn-ladda-spinner"
         <? if ($arResult["MODEL_OF_WORK"] == "user_config") {
             echo 'data-toggle="modal" data-target="#modal_cond_tree"';
         } ?>
        >
            <i class="icon-upload mr-2"></i>
                   <?=$arParams["BTN_TITLE"] ?: Loc::getMessage("B2B_EXCEL_EXPORT_BTN_TITLE")?>
        </span>
    <?endif;?>

    <? if ($arResult["MODEL_OF_WORK"] == "user_config"): ?>
        <div id="modal_cond_tree" class="modal fade" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?=Loc::getMessage("B2B_EXCEL_EXPORT_MODAL_TITLE")?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <form action="/" name="excel_cond_form" id="excel_cond_form">
                            <input type="hidden" name="IBLOCK_ID" value="2" data-bx-property-id="IBLOCK_ID">
                            <div id="wrap_cond_excel"></div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">
                            <?=Loc::getMessage("B2B_EXCEL_EXPORT_MODAL_BTN_CLOSE")?>
                        </button>
                        <button type="button" name="send_cond_tree" class="btn btn_b2b" data-dismiss="modal">
                            <?=Loc::getMessage("B2B_EXCEL_EXPORT_MODAL_BTN_SEND")?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>initFilterConditionsControl(<?=Json::encode($arResult["COND_TREE_PARAMS"])?>);</script>
        <?
        $condObject = new CCatalogCondTree();
        $condObject->Init(
            BT_COND_MODE_DEFAULT,
            BT_COND_BUILD_CATALOG,
            $arResult["COND_TREE_PARAMS"]
        );
        $condObject->Show([]);
        ?>
    <? endif; ?>

    <script>
        var <?=$strObName?> = new JCB2BExcelExport({
            siteId: '<?=$this->__component->getSiteId()?>',
            componentPath: '<?=$componentPath?>',
            parameters: '<?=$this->getComponent()->getSignedParameters()?>',
            btnSelector: '#blank-export-in-excel',
            model_of_work: '<?=$arParams["MODEL_OF_WORK"]?>',
            filter: <?=CUtil::PhpToJSObject(${$arParams["FILTER_NAME"]} ?: [])?>,
            cond_tree_params: <?=CUtil::PhpToJSObject($arResult["COND_TREE_PARAMS"] ?: [])?>,
            modalSelector: '#modal_cond_tree',
        });
    </script>

<? endif; ?>






