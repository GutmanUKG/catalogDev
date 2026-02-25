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
$this->setFrameMode(true);?>
<div class="search-form">
<form action="<?=$arResult["FORM_ACTION"]?>">
	<div class="form-control-feedback form-control-feedback-start">
		<?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
					"bitrix:search.suggest.input",
					"",
					array(
						"NAME" => "q",
						"VALUE" => "",
						"INPUT_SIZE" => 15,
						"DROPDOWN_SIZE" => 10,
					),
					$component, array("HIDE_ICONS" => "Y")
		);?><?else:?><input class="form-control bg-white border-primary" name="q" value="" size="15" maxlength="50" placeholder="<?= GetMessage("BSF_T_SEARCH_PLACEHOLDER")?>"/><?endif;?>
		<button class="search__submit form-control-feedback-icon" name="s" type="submit">
			<i class="ph-magnifying-glass"></i>
		</button>
	</div>
</form>
</div>