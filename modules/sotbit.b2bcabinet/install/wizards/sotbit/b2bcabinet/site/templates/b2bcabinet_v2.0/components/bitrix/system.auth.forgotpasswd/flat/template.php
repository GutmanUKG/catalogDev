<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
?>

<div class="card wmax-486px">
	<div class="card-header card-pt-2">
		<h5 class="card-title mb-0 fw-bold"><?=GetMessage("AUTH_GET_CHECK_STRING")?></h5>
		<span class="card-subtitle"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></span>
	</div>
	<div class="card-body pt-0">
	<?
	if(!empty($arParams["~AUTH_RESULT"])):
		$text = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
	?>
		<div class="alert alert-dismissible fade show <?=($arParams["~AUTH_RESULT"]["TYPE"] == "OK"? "alert-success":"alert-danger")?>"><?=nl2br(htmlspecialcharsbx($text))?></div>
	<?endif?>

		<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
	<?if($arResult["BACKURL"] <> ''):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	<?endif?>
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="SEND_PWD">

			<div class="form-group form-group-float">
				<label class="form-label"><?echo GetMessage("AUTH_LOGIN_EMAIL")?></label>
				<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["USER_LOGIN"]?>" data-bs-popup="tooltip"  data-bs-original-title="<?echo GetMessage("forgot_pass_email_note")?>"/>
				<input type="hidden" name="USER_EMAIL" />
			</div>

	<?if($arResult["PHONE_REGISTRATION"]):?>
			<div class="form-group form-group-float">
				<label class="form-label"><?echo GetMessage("forgot_pass_phone_number")?></label>
				<input class="form-control" type="text" name="USER_PHONE_NUMBER" maxlength="255" value="<?=$arResult["USER_PHONE_NUMBER"]?>" />
				<div class="bx-authform-note-container"><?echo GetMessage("forgot_pass_phone_number_note")?></div>
			</div>
	<?endif?>

	<? if ($arResult["USE_CAPTCHA"]): ?>
		<input type="hidden" name="captcha_sid" id="captcha_sid"
				value="<?= $arResult["CAPTCHA_CODE"] ?>"/>

		<label class="form-label">
			<?= GetMessage("system_auth_captcha") ?>: <span class="req">*</span>
		</label>
		<div class="password_recovery-captcha_wrap d-flex align-items-center mb-2">
			<div class="bx-captcha">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>"
						width="180" height="40" alt="CAPTCHA">
			</div>
			<div class="form-group feedback_block__captcha_reload p-2" role="button"
					onclick="reloadCaptcha(this,'<?= SITE_DIR ?>');return false;"
					>
					<i class="ph-arrows-counter-clockwise icon_refresh"></i>
			</div>
		</div>

		<div class="password_recovery-captcha">
			<div class="password_recovery-captcha_input">
				<input type="text" class="form-control" name="captcha_word"
						maxlength="50" autocomplete="off" required
						placeholder="<?= GetMessage("system_auth_captcha") ?>">
			</div>
		</div>

	<? endif ?>

			<div class="form-group form-group-float mt-3">
				<input type="submit" class="btn btn-primary" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
				<a class="btn btn-link" href="<?=$arResult["AUTH_AUTH_URL"]?>"><?=GetMessage("AUTH_AUTH")?></a>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
document.bform.onsubmit = function(){document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;};
document.bform.USER_LOGIN.focus();
</script>
