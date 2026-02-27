<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<div class="card">
    <div class="card-header bg-transparent header-elements-inline">
        <span class="text-uppercase font-size-sm font-weight-semibold"><?=GetMessage("SEARCH_HEAD")?></span>
        <div class="header-elements">
            <div class="list-icons">
                <a class="list-icons-item" data-action="collapse"></a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="<?=$arResult["FORM_ACTION"]?>">
            <div class="form-group-feedback form-group-feedback-right">
                <input class="form-control" type="text" name="q" value="">
                <div class="form-control-feedback">
                    <i class="icon-search4 font-size-base text-muted" ></i>
                </div>
            </div>
        </form>
    </div>
</div>
