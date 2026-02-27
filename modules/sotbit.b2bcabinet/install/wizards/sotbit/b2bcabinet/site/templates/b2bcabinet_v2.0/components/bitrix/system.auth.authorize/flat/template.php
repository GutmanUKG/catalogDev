<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */
?>

<div class="card wmax-486px">
<noindex>
	<div class="card-header card-p-2 pb-0">
		<h5 class="card-title mb-0 fw-bold"><?=GetMessage("AUTH_PLEASE_AUTH")?></h5>
		<span class="card-subtitle"><?=GetMessage("AUTH_SUB_TITLE")?></span>
	</div>
	<div class="card-body card-p-2 pt-0 mt-3">
	<?
	if(!empty($arParams["~AUTH_RESULT"])):
		$text = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
	?>
		<div class="bitrix-error">
                        <label class="validation-invalid-label"><?= nl2br(htmlspecialcharsbx($text)) ?></label>
                        <? if ($arResult["USER_BLOCKED"] && $arResult["USER_BLOCKED"] == "Y"):?>
                            <span class="validation-label-email"><?= GetMessage("AUTH_QUESTION_MAIL", ["#MAIL#" => \Bitrix\Main\Config\Option::get('main', 'email_from', '', SITE_ID)]) ?></span>
                        <? endif; ?>
                    </div>
	<?endif?>

	<? if (isset($_GET['ACCESS_RIGHTS_DENIED'])): ?>
		<div class="bitrix-error">
			<label class="validation-invalid-label"><? echo GetMessage('ACCOUNT_HAVNT_RIGHTS'); ?></label>
		</div>
	<? endif; ?>

	<?
	if($arResult['ERROR_MESSAGE'] <> ''):
		$text = str_replace(array("<br>", "<br />"), "\n", $arResult['ERROR_MESSAGE']);
	?>
	<div class="bitrix-error">
		<label class="validation-invalid-label"><?=nl2br(htmlspecialcharsbx($text))?></label>
	</div>
	<?endif?>


		<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

			<input type="hidden" name="AUTH_FORM" value="Y" />
			<input type="hidden" name="TYPE" value="AUTH" />
		<?if ($arResult["BACKURL"] <> ''):?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>

			<div class="form-group form-group-float">
				<label class="form-label"><?=GetMessage("AUTH_LOGIN")?></label>
				<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
			</div>
			<div class="form-group form-group-float">
				<label class="form-label"><?=GetMessage("AUTH_PASSWORD")?></label>
			<?if($arResult["SECURE_AUTH"]):?>
				<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none"><div class="bx-authform-psw-protected-desc"><span></span><?echo GetMessage("AUTH_SECURE_NOTE")?></div></div>

				<script type="text/javascript">
				document.getElementById('bx_auth_secure').style.display = '';
				</script>
			<?endif?>
				<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" />
			</div>
		<?if($arResult["CAPTCHA_CODE"]):?>
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />

			<div class="form-group form-group-float dbg_captha">
				<label class="form-label">
					<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>
				</label>
				<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></div>
				<div class="bx-authform-input-container">
					<input class="form-control" type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
				</div>
			</div>
		<?endif;?>

		<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<div class="form-group form-group-float d-flex justify-content-between align-items-center">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
					<label class="form-check-label" for="USER_REMEMBER"><?=GetMessage("AUTH_REMEMBER_ME")?></label>
				</div>

			<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
				<noindex>
					<div class="bx-authform-link-container">
						<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a>
					</div>
				</noindex>
			<?endif?>
			</div>
		<?endif?>
			<div class="form-group form-group-float">
		<?if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?>
			<noindex>
				<div class="bx-authform-link-container">
					<?=GetMessage("AUTH_FIRST_ONE")?> <a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a>
				</div>
			</noindex>
		<?endif?>
			</div>
			<div class="form-group form-group-float">
				<input type="submit" class="btn btn-primary w-100" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
			</div>
		</form>
	</div>
</noindex>
</div>

<script type="text/javascript">
<?if ($arResult["LAST_LOGIN"] <> ''):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>

