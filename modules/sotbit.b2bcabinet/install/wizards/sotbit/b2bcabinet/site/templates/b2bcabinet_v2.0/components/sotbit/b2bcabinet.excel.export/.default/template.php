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
                class="btn btn-ladda w-100 w-sm-auto"
                aria-label="<?=$arParams["BTN_TITLE"] ?: Loc::getMessage("B2B_EXCEL_EXPORT_BTN_TITLE")?>"
                data-style="slide-right" id="blank-export-in-excel"
        >
        <span class="ladda-label export_excel_preloader">
            <i class="ph-arrow-line-up me-2"></i>
            <?=$arParams["BTN_TITLE"] ?: Loc::getMessage("B2B_EXCEL_EXPORT_BTN_TITLE")?>
        </span>
        </button>
    <?else:?>
        <span
                id="blank-export-in-excel"
                class="dropdown-item"
        >
            <i class="ph-arrow-line-up me-2"></i>
            <?=$arParams["BTN_TITLE"] ?: Loc::getMessage("B2B_EXCEL_EXPORT_BTN_TITLE")?>
        </span>
    <?endif;?>

    <script>
        var <?=$strObName?> = new JCB2BExcelExport({
            siteId: '<?=$this->__component->getSiteId()?>',
            componentPath: '<?=$componentPath?>',
            parameters: '<?=$this->getComponent()->getSignedParameters()?>',
            btnSelector: '#blank-export-in-excel',
            filter: <?=CUtil::PhpToJSObject(${$arParams["FILTER_NAME"]} ?: [])?>,
        });
    </script>

<? endif; ?>






