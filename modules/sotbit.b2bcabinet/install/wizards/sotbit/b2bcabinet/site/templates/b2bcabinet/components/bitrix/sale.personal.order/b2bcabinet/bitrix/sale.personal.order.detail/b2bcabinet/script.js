BX.namespace('BX.Sale.PersonalOrderComponent');

(function() {

	'use strict';

	var LightTableFilter = (function(Arr) {

		var _input;

		function _onInputEvent(e) {
			_input = e.target;
			var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
			Arr.forEach.call(tables, function(table) {
				Arr.forEach.call(table.tBodies, function(tbody) {
					Arr.forEach.call(tbody.rows, _filter);
				});
			});
		}

		function _filter(row) {
			var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
			row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
		}

		return {
			init: function() {
				var inputs = document.getElementsByClassName('input-search');
				Arr.forEach.call(inputs, function(input) {
					input.oninput = _onInputEvent;
				});
			}
		};
	})(Array.prototype);

	document.addEventListener('readystatechange', function() {
		if (document.readyState === 'complete') {
			LightTableFilter.init();
		}
	});

	window.B2bOrderDetail = function(arParams) {
		// this.ExcelButtonId = arParams["ExcelButtonId"];
		this.ajaxUrl = arParams["ajaxUrl"];
		this.paymentList = arParams["paymentList"];
		this.changePayment = arParams["changePayment"];
		this.changePaymentWrapper = arParams["changePaymentWrapper"];
		this.templateName = arParams["TemplateName"];
		window.changePaymentTemplateName = this.templateName;
		// this.arResult = arParams["arResult"];
		// this.arParams = arParams["arParams"];
		// this.filter = arParams["filter"];
		// this.qnts = arParams["qnts"];
		// this.TemplateFolder = arParams["TemplateFolder"];
		// this.OrderId = arParams["OrderId"];
		// this.Headers = arParams["Headers"];
		// this.HeadersSum = arParams["HeadersSum"];
		this.destroy();
		this.init();
	}
	window.B2bOrderDetail.prototype.destroy = function() {

	}
	window.B2bOrderDetail.prototype.init = function() {
		$(document).on("click", this.changePayment, this, this.clickchangePayment);
	}

	window.B2bOrderDetail.prototype.clickchangePayment = function(e) {
		var data = e.data;

		BX.ajax(
			{
				method: 'POST',
				dataType: 'html',
				url: data.ajaxUrl,
				data:
					{
						sessid: BX.bitrix_sessid(),
						orderData: data.paymentList[$(this).attr('id')],
						templateName: window.changePaymentTemplateName,
						SITE_ID: data.paymentList[$(this).attr('id')].SITE_ID
					},
				onsuccess: BX.proxy(function(result)
				{
					$(data.changePaymentWrapper + '[data-id="' + $(this).attr('id') + '"]').html(result);
					$(this).hide();
				},this),
				onfailure: BX.proxy(function()
				{
					return this;
				}, this)
			}, this
		);
	}

	function excelOut(id)
	{
		BX.showWait();
		var file = '';

		$.ajax({
			type: 'POST',
			async: false,
			url: '/include/ajax/personal_order_excel_export.php',
			data: {
				orderId:id,
				file:file
			},
			success: function(data) {
				file = data;
			},
		});

		var now = new Date();

		var dd = now.getDate();
		if (dd < 10) dd = '0' + dd;
		var mm = now.getMonth() + 1;
		if (mm < 10) mm = '0' + mm;
		var hh = now.getHours();
		if (hh < 10) hh = '0' + hh;
		var mimi = now.getMinutes();
		if (mimi < 10) mimi = '0' + mimi;
		var ss = now.getSeconds();
		if (ss < 10) ss = '0' + ss;

		var rand = 0 - 0.5 + Math.random() * (999999999 - 0 + 1)
		rand = Math.round(rand);

		var name = 'blank_' + now.getFullYear() + '_' + mm + '_' + dd + '_' + hh + '_' + mimi + '_' + ss + '_' + rand + '.xlsx';

		var link = document.createElement('a');
		link.setAttribute('href',file);
		link.setAttribute('download',name);
		var event = document.createEvent("MouseEvents");
		event.initMouseEvent(
			"click", true, false, window, 0, 0, 0, 0, 0
			, false, false, false, false, 0, null
		);
		link.dispatchEvent(event);
		BX.closeWait();
	};

	$(document).on("click", "#excel-button-export", function ()
	{
		var orderId = this.dataset.id;
		excelOut(orderId);
	});
})();

document.addEventListener('DOMContentLoaded', function () {
	clickLastActiveTab();
});

function writeActiveTab(activeTab) {
	document.cookie = "active-tab=" + activeTab;
}

function clickLastActiveTab() {
	const lastActiveTab = getActiveTabFromCookie();
	$('a[href="' + lastActiveTab + '"]').click();
}

function getActiveTabFromCookie() {
	const name = 'active-tab';
	let matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

