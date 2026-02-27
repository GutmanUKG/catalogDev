<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
use Bitrix\Main\Localization\Loc;
?>

<form action="" method="get" name="catalogSearch">
    <div class="form-group form-group-feedback form-group-feedback-right">
        <!--        <input type="submit" name="subtitle" style="display:none" />-->
        <input type="search" class="form-control" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" placeholder="<?=Loc::getMessage('SEARCH_GO')?>" />
        <div class="form-control-feedback">
            <i class="icon-search4 font-size-base text-muted" onclick="catalogSearch.submit()"></i>
        </div>
        <input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
    </div>
</form>