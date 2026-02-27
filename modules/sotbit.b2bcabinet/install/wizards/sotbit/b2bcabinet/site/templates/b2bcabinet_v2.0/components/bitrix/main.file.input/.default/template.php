<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams["SHOW_AVATAR_EDITOR"] = $arParams["SHOW_AVATAR_EDITOR"] ?? null;
if ($arParams["SHOW_AVATAR_EDITOR"] == "Y")
{
	\CJSCore::Init(array("webrtc_adapter", "avatar_editor"));
}
else
{
	\CJSCore::Init(array("uploader"));
}

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
if ($arParams["ALLOW_UPLOAD"] == "N" && empty($arResult['FILES']))
	return "";
$cnt = count($arResult['FILES']);
$id = CUtil::JSEscape($arParams['CONTROL_ID']);
if ($arParams['MULTIPLE'] == 'Y' && mb_substr($arParams['INPUT_NAME'], -2) !== "[]")
	$arParams['INPUT_NAME'] .= "[]";
$thumbForUploaded = <<<HTML
<div class="webform-field-item-wrap">
	<span class="webform-field-upload-icon ph-file ph-file-#ext#">
		<img src="#preview_url#" onerror="BX.remove(this);" />
	</span>
	<a href="#url#" target="_blank" title="#name#" data-bx-role="file-name" class="upload-file-name">#name#</a>
	<span class="upload-file-size ms-2" data-bx-role="file-size">#size#</span>
	<i class="spinner-grow spinner-grow-sm ms-2"></i>
	<del class="ph-x" data-bx-role="file-delete"></del>
	<input id="file-#file_id#" data-bx-role="file-id" type="hidden" name="#input_name#" value="#file_id#" />
</div>
HTML;
$thumb = <<<HTML
<div class="webform-field-item-wrap">
	<span class="webform-field-upload-icon ph-file ph-file-#ext#" data-bx-role="file-preview"></span>
	<a href="#" target="_blank" data-bx-role="file-name" class="upload-file-name">#name#</a>
	<span class="upload-file-size ms-2" data-bx-role="file-size">#size#</span>
	<i class="spinner-grow spinner-grow-sm ms-2"></i>
	<del class="ph-x" data-bx-role="file-delete"></del>
</div>
HTML;
?>
<div class="file-input">
	<ol class="webform-field-upload-list webform-field-upload-icon-view webform-field-upload-list-<?=$arParams["MULTIPLE"] == "Y" ? "multiple" : "single"?>
		<?=$arParams["ALLOW_UPLOAD"] == "I" ? 'webform-field-upload-icon-image' : ''?> " 
		id="mfi-<?=$arParams['CONTROL_ID']?>"><?
		foreach ($arResult['FILES'] as $file)
		{
			$ext = strtolower(GetFileExtension($file['ORIGINAL_NAME']));
			$isImage = CFile::IsImage($file["ORIGINAL_NAME"], $file["CONTENT_TYPE"]);
			$t = ($isImage ? CFile::ResizeImageGet($file, array( "width" => 100, "height" => 100 ), BX_RESIZE_IMAGE_EXACT, false, false, true) : array("src" => "/bitrix/images/1.gif"));
			?><li class="saved"><?=str_replace(
				array("#input_name#", "#file_id#", "#name#", "#size#", "#url#", "#url_delete#", "#preview_url#", "#ext#"),
				array($arParams['INPUT_NAME'],
					intval($file['ID']),
					htmlspecialcharsEx($file['ORIGINAL_NAME']),
					CFile::FormatSize($file["FILE_SIZE"]),
					$file["URL"],
					$file["URL_DELETE"],
					$t["src"],
					$ext
				),
				$thumbForUploaded
			)?></li><?
		}
	?></ol>
	<?if ($arParams["ALLOW_UPLOAD"] != "N")
	{
		?><div class="webform-field-upload" id="mfi-<?=$arParams['CONTROL_ID']?>-button"><?
			if (isset($arParams["INPUT_CAPTION"]) && !empty($arParams["INPUT_CAPTION"]))
			{
				$inputCaption = $arParams["INPUT_CAPTION"];
			}
			else
			{
				$inputCaption = ($arParams["ALLOW_UPLOAD"] == "I" ? GetMessage('MFI_INPUT_CAPTION_ADD_IMAGE') : GetMessage('MFI_INPUT_CAPTION_ADD'));
			}
			?><span class="btn ps-3"><i class="ph-plus me-2"></i><?=$inputCaption?></span><?
			if ($arParams["MULTIPLE"] == "N")
			{
				?><span class="webform-button-replace"><?=($arParams["ALLOW_UPLOAD"] == "I" ? GetMessage('MFI_INPUT_CAPTION_REPLACE_IMAGE') : GetMessage('MFI_INPUT_CAPTION_REPLACE'))?></span><?
			}
			if ($arParams["SHOW_AVATAR_EDITOR"] == "Y" && $arParams["ALLOW_UPLOAD"] == "I")
			{
				?><input type="button" id="mfi-<?=$arParams['CONTROL_ID']?>-editor" /><?
			}
			else
			{
				?><input type="file" class="form-control" id="file_input_<?=$arParams['CONTROL_ID']?>" <?=$arParams["MULTIPLE"] === 'Y' ? ' multiple="multiple"' : ''?> /><?
			}
		?></div><?
		if (!empty($arParams["ALLOW_UPLOAD_EXT"]) || $arParams['MAX_FILE_SIZE'] > 0)
		{
			$message = ((!empty($arParams["ALLOW_UPLOAD_EXT"]) && $arParams['MAX_FILE_SIZE'] > 0) ? GetMessage("MFI_NOTICE_1") : (
					!empty($arParams["ALLOW_UPLOAD_EXT"]) ? GetMessage("MFI_NOTICE_2") : GetMessage("MFI_NOTICE_3")
			));
			?><div class="webform-field-upload-notice"><?=str_replace(array("#ext#", "#size#"), array(htmlspecialcharsBx($arParams["ALLOW_UPLOAD_EXT"]), CFile::FormatSize($arParams['MAX_FILE_SIZE'])), $message);?></div><?
		}
	}
?>
<script type="text/javascript">
		BX.message(<?=CUtil::PhpToJSObject(array(
			"MFI_THUMB" => $thumb,
			"MFI_THUMB2" => $thumbForUploaded,
			"MFI_UPLOADING_ERROR" => GetMessage("MFI_UPLOADING_ERROR")
		))?>);
		BX.ready(function(){
			BX.MFInput.init(<?=CUtil::PhpToJSObject(array(
				"controlId" => $arParams['CONTROL_ID'],
				"controlUid" => $arParams['CONTROL_UID'],
				"controlSign" => $arParams["CONTROL_SIGN"],
				"inputName" => $arParams['INPUT_NAME'],
				"maxCount" => $arParams["MULTIPLE"] == "N" ? 1 : 0,
				"moduleId" => $arParams["MODULE_ID"],
				"forceMd5" => $arParams["FORCE_MD5"],

				"allowUpload" => $arParams["ALLOW_UPLOAD"],
				"allowUploadExt" => $arParams["ALLOW_UPLOAD_EXT"],
				"uploadMaxFilesize" => $arParams['MAX_FILE_SIZE'],
				"enableCamera" => !isset($arParams['ENABLE_CAMERA']) || $arParams['ENABLE_CAMERA'] !== "N",

				"urlUpload" => $arParams["URL_TO_UPLOAD"]
			))?>);
		});
	</script>
</div>
