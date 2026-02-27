<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\Localization\Loc;

/**
 * @var StringUfComponent $component
 * @var array $arResult
 */

$component = $this->getComponent();

$strRand = $this->randString();
?>

<div class='mb-2'>
	<?php
	foreach($arResult['fieldValues'] as $value)
	{
		?>
		<span class='field-item'>
			<?php if($value['tag'] === 'input'): ?>
				<input
					<?= $component->getHtmlBuilder()->buildTagAttributes($value['attrList']) ?>
				>
			<?php else: ?>
				<textarea
					<?= $component->getHtmlBuilder()->buildTagAttributes($value['attrList']) ?>
				><?= HtmlFilter::encode($value['attrList']['value']) ?></textarea>
			<?php endif; ?>
		</span>
		<?php
	}

	if(
		isset($arResult['userField']['MULTIPLE'])
		&& $arResult['userField']['MULTIPLE'] === 'Y'
		&&
		(
			!isset($arResult['additionalParameters']['SHOW_BUTTON'])
			|| $arResult['additionalParameters']['SHOW_BUTTON'] !== 'N'
		)
	)
	{
	?>
		<button class="btn"
			type="button"
			data-add-type="<?=$arResult['userField']['USER_TYPE_ID'] === 'string' ? 'text' : ''?>"
			data-add-placeholder="<?=$arParams['userField']['placeholder'] ?: $arParams['userField']['EDIT_FORM_LABEL']?>"
			data-add-name="<?=$arResult['fieldName']?>"
			data-add-maxlength="50"
			data-add-minlength="0"
			>
			<?=Loc::getMessage('USER_TYPE_PROP_ADD');?>
		</button>
	<?
	}
	?>
</div>

<script>
	const btnAddFilds<?=$strRand?> = document.querySelector('[data-add-name="<?=$arResult['fieldName']?>"]');
	
	if (btnAddFilds<?=$strRand?>) {
		function hideBlock(e) {
			BX.remove(e.closest(".form-control-multiple-wrap"))
		}

		BX.bind(btnAddFilds<?=$strRand?>, "click", BX.delegate(function(e) {
			if (BX.type.isDomNode(e.target)) {
				var t = BX.create("input", {
					attrs: {
						className: "form-control",
						type: "text",
						name: e.target.getAttribute("data-add-name"),
						placeholder: e.target.getAttribute("data-add-placeholder"),
						minlength: e.target.getAttribute("data-add-minlength"),
						maxlength: e.target.getAttribute("data-add-maxlength")
					}
				})
				, r = BX.create("div", {
					attrs: {
						className: "form-control-multiple position-absolute end-0 top-50 translate-middle-y me-1",
						onclick: "hideBlock(this)"
					},
					children: [BX.create("button", {
						attrs: {
							className: "form-control-multiple-ic btn btn-sm btn-icon btn-link text-muted",
							type: "button"
						},
						children: [BX.create("i", {
							attrs: {
								className: "ph-x fs-base"
							}
						})]
					})]
				})
				, a = BX.create("div", {
					attrs: {
						className: "form-control-multiple-wrap"
					},
					children: [t, r]
				});
				e.target.parentNode.insertBefore(a, e.target)
			}
		}))
	}
</script>
