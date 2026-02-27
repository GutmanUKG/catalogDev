<?
/**
 * Copyright (c) 2017. Sergey Danilkin.
 */

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arParams = [
	"REFRESHED_COMPONENT_MODE" => "Y",
	"ELIMINATED_PAY_SYSTEMS" => array("0"),
	"PATH_TO_BASKET" => $arParams['G_ACCOUNTPAY_PATH_TO_BASKET'],
	"PATH_TO_PAYMENT" => $arParams['G_ACCOUNTPAY_PATH_TO_PAYMENT'],
	"PERSON_TYPE" => $arParams['G_ACCOUNTPAY_PERSON_TYPE_ID'],
	"REDIRECT_TO_CURRENT_PAGE" => "N",
	"SELL_AMOUNT" => array(""),
	"SELL_CURRENCY" => '',
	"SELL_SHOW_FIXED_VALUES" => 'Y',
	"SELL_SHOW_RESULT_SUM" =>  '',
	"SELL_TOTAL" => array(""),
	"SELL_USER_INPUT" => 'Y',
	"SELL_VALUES_FROM_VAR" => "N",
	"SELL_VAR_PRICE_VALUE" => "",
	"SET_TITLE" => "N",
];

?>
<div id="b2b_accountpay_vidget" class="b2b_accountpay_vidget">
	<div data-entity="content"></div>
	<a class="go-to-back" data-entity="refresh" style="display: none;">
        <img src="<?=$arGadget['PATH_SITEROOT']?>/img/back.gif" width="7" height="7" border="0">
        <?=Loc::getMessage('GD_SOTBIT_CABINET_REFRESH')?>
	</a>
</div>

<script>
	var refresh = function() {
		BX.ajax.runAction('sotbit:b2bcabinet.AccountPayVigetController.getAccountPayComposent', {
            data: {arParams: <?=CUtil::PhpToJSObject($arParams)?>},
        }).then(
            function(res) {
				Array.prototype.slice.call(contentContainer.childNodes)
					.forEach(function(i) {i.remove()});

				contentContainer.innerHTML = res.data.html;
				const dataElemnt = contentContainer.querySelector('#b2b_accountpay_vidget_component');
				const data = JSON.parse(dataElemnt.innerHTML);
				dataElemnt.remove();

				sc = new BX.saleAccountPay(data);

			},
            function(err) {console.log(err)},
        )
	};

	refresh();

	var vidgetRoot = document.querySelector('#b2b_accountpay_vidget');
    var contentContainer = vidgetRoot.querySelector('[data-entity="content"]');
    var btn = vidgetRoot.querySelector('[data-entity="refresh"]');
	btn.addEventListener('click', function(e) {
		e.currentTarget.setAttribute('style', 'display: none;');
		refresh();
	});

	BX.addCustomEvent(window, 'createPersonalPymentFromVidget', function() {
		btn.setAttribute('style', 'display: block;');
	});

</script>