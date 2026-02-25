<?
	if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
	/**
	 * Bitrix vars
	 *
	 * @var array $arParams
	 * @var array $arResult
	 * @var CBitrixComponentTemplate $this
	 * @global CMain $APPLICATION
	 * @global CUser $USER
	 */
?>

<div class="ol-feedback">
	<?if(!empty($arResult["ERROR_MESSAGE"])):?>
		<div class="ol-feedback-error-text">
			<?foreach($arResult["ERROR_MESSAGE"] as $v){
				ShowError($v);
			}?>
		</div>
	<?endif;?>
	<?if($arResult["OK_MESSAGE"] <> ''):?>
		<div class="ol-feedback-succes-text">
			<p><?=$arResult["OK_MESSAGE"]?></p>
		</div>
	<?endif;?>

	<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
		<?=bitrix_sessid_post()?>

		<div class="ol-mess mb-3">
			<textarea name="MESSAGE" placeholder="<?=GetMessage("OLFT_MESSAGE")?>" class="form-control"><?=$arResult["MESSAGE"]?></textarea>
		</div>

		<input type="hidden" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
		<input type="hidden" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">

		<div class="ol-submit text-center">
			<input type="submit" name="submit" value="<?=GetMessage("OLFT_SUBMIT")?>" class="btn btn-primary">
		</div>
	</form>
</div>