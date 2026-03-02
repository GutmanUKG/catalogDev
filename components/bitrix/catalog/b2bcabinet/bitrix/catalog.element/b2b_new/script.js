;(function (window){

	'use strict';

	if (window.JCBlankZakazaDetail)
		return;

	window.JCBlankZakazaDetail = function (arResult, arParams, itemIds) {
		this.DEBOUNCE_TIME = 500;

		this.itemIds = itemIds;
		this.arResult = arResult;
		this.arParams = arParams;

		this.itemId = arResult.ID;
		this.node = document.getElementById(this.itemId);
		this.productType = arResult['CATALOG_TYPE'];
		this.nodesQuantity = {
			wrapper: document.getElementById(itemIds['QUANTITY']),
			increment: document.getElementById(itemIds['QUANTITY_INCREMENT']),
			value: document.getElementById(itemIds['QUANTITY_VALUE']),
			decrement: document.getElementById(itemIds['QUANTITY_DECREMENT']),
		};
		this.quantityTrace = arResult.CATALOG_QUANTITY_TRACE === "Y" ? true : false;
		this.canBuyZero = arResult.CATALOG_CAN_BUY_ZERO === "Y" ? true : false;
		this.currentQuantity = parseFloat(arResult['ACTUAL_QUANTITY']) || 0;
		this.tmpQuantity = this.currentQuantity;
		this.maxQuantity = this.quantityTrace && !this.canBuyZero? parseFloat(arResult['CATALOG_QUANTITY']) : Number.POSITIVE_INFINITY;
		this.minQuantity = 0;
		this.measureRatio = parseFloat(
			arResult['CATALOG_MEASURE_RATIO']
				? arResult['CATALOG_MEASURE_RATIO']
				: arResult['ITEM_MEASURE_RATIOS'].length > 0
					? arResult['ITEM_MEASURE_RATIOS'][arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
					: 0
		);
		this.measureName = arResult['CATALOG_MEASURE_NAME'] ? arResult['CATALOG_MEASURE_NAME'] : arResult['ITEM_MEASURE']['TITLE'];

		this.ranges = [];
		this.prices = arResult['PRINT_PRICES'];
		this.delayAddToBasket = 0;
		this.isOffersHidden = true;

		BX.ready(BX.delegate(this.init,this));
	}

	window.JCBlankZakazaDetail.prototype = {
		init: function() {
			this.initRanges();
			this.initOffers();
			if (parseInt(this.productType) === 3) {
				for (let offer in this.offers) {
					this.initQuantity(this.getOfferQuantityPropsById(offer))
				}
			} else {
				this.initQuantity(this.getItemQuantityProps());
			}

			this.initMarkdownItems();

			// Orphan multi-store: override main "В Корзину" to open markdown modal
			if (this.arResult.IS_ORPHAN_MARKDOWN && Array.isArray(this.arResult.SECOND_ITEMS) && this.arResult.SECOND_ITEMS.length > 1) {
				var modalEl = document.getElementById('markdownModal-' + this.itemId);
				if (modalEl && this.nodesQuantity.increment) {
					var origIncrement = this.nodesQuantity.increment;
					origIncrement.onclick = function(e) {
						e.preventDefault();
						e.stopPropagation();
						var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
						modal.show();
					};
				}
			}

			BX.addCustomEvent('SidePanel.Slider:onMessage', function(event) {
				if (event.eventId === "BZI: ItemQuantityChanged") {
					BX.onCustomEvent("BZD_ItemQuantityChanged", {
						id: event.data.itemId,
						quantity: event.data.quantity
					});
				}
			})
		},
		initRanges: function() {
			for (let price in this.prices) {
				this.ranges = Object.keys(this.prices[price])
				return
			}
		},
		initOffers: function() {
			if (!Array.isArray(this.arResult['OFFERS']) && parseInt(this.productType) !== 3) {return}
			this.offers = {};
			const offers = this.arResult['OFFERS'];

			offers.forEach(function (offer) {
				const offerIds = this.itemIds['OFFERS'][offer.ID];
				this.offers[offer.ID] = {
					id: offer.ID,
					name: offer.NAME,
					nodesQuantity:{
						wrapper: document.getElementById(offerIds.QUANTITY),
						increment: document.getElementById(offerIds.QUANTITY_INCREMENT),
						value: document.getElementById(offerIds.QUANTITY_VALUE),
						decrement: document.getElementById(offerIds.QUANTITY_DECREMENT),
					},
					currentQuantity: parseFloat(offer.ACTUAL_QUANTITY),
					tmpQuantity: parseFloat(offer.ACTUAL_QUANTITY),
					maxQuantity: (offer.CATALOG_QUANTITY_TRACE === "Y" ? true : false) && !(offer.CATALOG_CAN_BUY_ZERO === "Y" ? true : false) ? parseFloat(offer['CATALOG_QUANTITY']) : Number.POSITIVE_INFINITY,
					minQuantity: 0,
					measureRatio: parseFloat(
						offer.CATALOG_MEASURE_RATIO
							? offer.CATALOG_MEASURE_RATIO
							: offer.ITEM_MEASURE_RATIOS
								? offer.ITEM_MEASURE_RATIOS[offer.ITEM_MEASURE_RATIO_SELECTED].RATIO
								: 0
					),
					measureName: offer.CATALOG_MEASURE_NAME,
					quantityTrace: offer.CATALOG_QUANTITY_TRACE === "Y" ? true : false,
					canBuyZero: offer.CATALOG_CAN_BUY_ZERO === "Y" ? true : false
				}
			}.bind(this))

		},
		initQuantity: function(item) {
			if (item.nodes.wrapper === null) {return}
			const nodes = item.nodes;
			if (!nodes.increment || !nodes.decrement || !nodes.value) {return}
			window.addEventListener("message", function(event) {
				if (item.disableMessageSync) {
					return;
				}
				if (typeof event.data !== 'string') {
					return;
				}
				let data = null;
				try {
					data = JSON.parse(event.data);
				} catch (e) {
					return;
				}
				if (data.itemId && data.quantity && data.itemId === item.itemId) {
					item.currentQuantity = parseFloat(data.quantity);
					item.tmpQuantity = item.currentQuantity;
					nodes.value.value = item.currentQuantity;
					if (typeof item.onQuantityChanged === 'function') {
						item.onQuantityChanged(item.currentQuantity);
					}
				}
			})

			nodes.increment.addEventListener('click', function(event) {

				item.currentQuantity = item.tmpQuantity;
				item.tmpQuantity = parseFloat((item.tmpQuantity + item.measureRatio).toFixed(3));

				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							nodes.increment.setAttribute("disabled", "disabled");
							nodes.increment.firstElementChild.classList.add('spinner-grow');
							this.addToBasket(item.basketProductId || item.itemId, item.tmpQuantity, item.measureRatio, item.propsAddedToBasket, item.extraBasketFields)
								.then(function(response) {
										if (BX.SidePanel) {
											BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
												id: item.itemId, quantity: item.tmpQuantity,
											});
										} else {
											BX.onCustomEvent('OnBasketChange');
										}
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.increment.removeAttribute("disabled");
										nodes.increment.firstElementChild.classList.remove('spinner-grow');
										nodes.value.value = item.currentQuantity;
										if (typeof item.onQuantityChanged === 'function') {
											item.onQuantityChanged(item.currentQuantity);
										}
										const frames = Array.prototype.slice.call(window.frames);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										})
										if (item.currentQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currentQuantity + " " + item.measureName,
												'success'
											]);
										}
										this.deleteRepaetNotification();
									}.bind(this),
									function(error){
										let errors = [];
										for (var i = 0; i<error.errors.length; i++) {
											errors.push(error.errors[i].message);
										}

										BX.onCustomEvent('B2BNotification',[
											errors.join('<br>'),
											'alert'
										]);
										nodes.value.value = item.currentQuantity;
										if (typeof item.onQuantityChanged === 'function') {
											item.onQuantityChanged(item.currentQuantity);
										}
										nodes.increment.removeAttribute("disabled");
										nodes.increment.firstElementChild.classList.remove('spinner-grow');
										console.error(error)
									})
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity
				}
			}.bind(this))

			nodes.decrement.addEventListener('click', function(event) {

				item.currentQuantity = item.tmpQuantity;
				item.tmpQuantity = parseFloat((item.tmpQuantity - item.measureRatio).toFixed(3));


				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							nodes.decrement.setAttribute("disabled", "disabled");
							nodes.decrement.firstElementChild.classList.add('spinner-grow');
							this.addToBasket(item.basketProductId || item.itemId, item.tmpQuantity, item.measureRatio, item.propsAddedToBasket, item.extraBasketFields)
								.then(function(response) {
										if (BX.SidePanel) {
											BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
												id: item.itemId, quantity: item.tmpQuantity,
											});
										} else {
											BX.onCustomEvent('OnBasketChange');
										}
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.decrement.removeAttribute("disabled");
										nodes.decrement.firstElementChild.classList.remove('spinner-grow');
										nodes.value.value = item.currentQuantity;
										if (typeof item.onQuantityChanged === 'function') {
											item.onQuantityChanged(item.currentQuantity);
										}
										const frames = Array.prototype.slice.call(window.frames);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										})

										if (item.currentQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currentQuantity + " " + item.measureName,
												'success'
											]);
										}
										this.deleteRepaetNotification();
									}.bind(this),
									function(error){
										BX.onCustomEvent('B2BNotification',[
											error.error.map(function (error) {return error.message}).join('<br>'),
											'alert'
										]);
										nodes.value.value = item.currentQuantity;
										if (typeof item.onQuantityChanged === 'function') {
											item.onQuantityChanged(item.currentQuantity);
										}
										nodes.decrement.removeAttribute("disabled");
										nodes.decrement.firstElementChild.classList.remove('spinner-grow');
										console.error(error)
									})
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity
				}
			}.bind(this))

			nodes.value.addEventListener('input', function(event) {
				item.tmpQuantity = event.target.value === '' ? 0 : event.target.value;

				if (item.tmpQuantity > item.maxQuantity) {
					item.tmpQuantity = item.maxQuantity;
				}

				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(parseFloat(item.tmpQuantity))) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							this.addToBasket(item.basketProductId || item.itemId, item.tmpQuantity, item.measureRatio, item.propsAddedToBasket, item.extraBasketFields)
								.then(function(response) {
										if (BX.SidePanel) {
											BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
												id: item.itemId, quantity: item.tmpQuantity,
											});
										} else {
											BX.onCustomEvent('OnBasketChange');
										}
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.value.value = item.currentQuantity;
										if (typeof item.onQuantityChanged === 'function') {
											item.onQuantityChanged(item.currentQuantity);
										}
										if (window.self !== window.top) {
											window.top.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										}
										if (item.currentQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZD_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZD_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZD_PRODUCT_ADD_TO_BASKET') + " " + item.currentQuantity + " " + item.measureName,
												'success'
											]);
										}
										this.deleteRepaetNotification();
									}.bind(this),
									function(error){
										console.error(error)
									})
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity
				}
			}.bind(this))
		},
		addToBasket: function(id, quantity, measureRatio, porpsAddedToBasket, extraBasketFields) {

			const quantity1 = Math.round(quantity * 1000000);
			const measureRatio1 = Math.round(measureRatio * 1000000);
			const remainder = quantity1 % measureRatio1;
			quantity = (quantity1 - remainder) / 1000000;

			var arFields = {
				'PRODUCT_ID': id,
				'QUANTITY': quantity,
				'PROPS': porpsAddedToBasket,
				'RENEW': 'N',
			};
			if (extraBasketFields && typeof extraBasketFields === 'object') {
				for (var fieldName in extraBasketFields) {
					if (Object.prototype.hasOwnProperty.call(extraBasketFields, fieldName)) {
						arFields[fieldName] = extraBasketFields[fieldName];
					}
				}
			}

			return BX.ajax.runAction('sotbit:b2bcabinet.basket.addProductToBasket', {
				data: {
					arFields: arFields
				},
			})
		},
		getMarkdownBasketFields: function() {
			if (!this.arResult || !this.arResult.IS_ORPHAN_MARKDOWN) {
				return null;
			}
			var storeId = this.arResult.MARKDOWN_DEFAULT_STORE_ID || this.arResult.MARKDOWN_STORE_ID;
			if (!storeId) {
				return null;
			}
			var rowKey = this.arResult.MARKDOWN_DEFAULT_ROW_KEY || (String(this.itemId) + '_' + String(storeId));
			var category = this.arResult.MARKDOWN_DEFAULT_CATEGORY || '';
			var props = [
				{
					NAME: BX.message('BZD_MARKDOWN_PROP_STORE') || 'Markdown Store',
					CODE: 'MARKDOWN_STORE_ID',
					VALUE: String(storeId),
					SORT: 100
				},
				{
					NAME: BX.message('BZD_MARKDOWN_PROP_ROW_KEY') || 'Markdown Row Key',
					CODE: 'MARKDOWN_ROW_KEY',
					VALUE: String(rowKey),
					SORT: 200
				}
			];
			if (category) {
				props.push({
					NAME: BX.message('BZD_MARKDOWN_PROP_CATEGORY') || 'Markdown Category',
					CODE: 'MARKDOWN_CATEGORY',
					VALUE: String(category),
					SORT: 150
				});
			}
			var extra = {
				STORE_ID: String(storeId),
				CATALOG_STORE_ID: String(storeId),
				MARKDOWN_STORE_ID: String(storeId),
				MARKDOWN_ROW_KEY: String(rowKey),
				PRODUCT_XML_ID: 'markdown_' + String(rowKey),
				CATALOG_XML_ID: 'markdown_catalog_' + String(this.itemId)
			};
			if (category) {
				extra.MARKDOWN_CATEGORY = String(category);
			}
			return {
				props: props,
				extra: extra
			};
		},
		getItemQuantityProps: function () {
			var result = {
				itemId: this.itemId,
				name: this.arResult['NAME'],
				nodes: this.nodesQuantity,
				currentQuantity: this.currentQuantity,
				tmpQuantity: this.tmpQuantity,
				maxQuantity: this.maxQuantity,
				minQuantity: this.minQuantity,
				measureRatio: this.measureRatio,
				measureName: this.measureName
			};
			var markdownFields = this.getMarkdownBasketFields();
			if (markdownFields) {
				result.propsAddedToBasket = markdownFields.props;
				result.extraBasketFields = markdownFields.extra;
			}
			return result;
		},
		getOfferQuantityPropsById: function(id) {
			const offers = this.arResult['OFFERS']
			if (!Array.isArray(offers)) {
				console.error('JCBlankZakazaItem: Can not find offers in product id=' + this.itemId);
				return {
					offerId: id,
					nodes: null,
					currentQuantity: null,
					tmpQuantity: null,
					maxQuantity: null,
					minQuantity: null
				}
			}
			const offer = this.offers[id];

			const offerIds = this.itemIds['OFFERS'][id];

			const nodes = {
				wrapper: document.getElementById(offerIds.QUANTITY),
				increment: document.getElementById(offerIds.QUANTITY_INCREMENT),
				value: document.getElementById(offerIds.QUANTITY_VALUE),
				decrement: document.getElementById(offerIds.QUANTITY_DECREMENT),
			};
			return {
				itemId: id,
				name: offer.name,
				nodes: nodes,
				currentQuantity: offer.currentQuantity,
				tmpQuantity: offer.tmpQuantity,
				maxQuantity: offer.maxQuantity,
				minQuantity: offer.minQuantity,
				measureRatio: offer.measureRatio,
				measureName: offer.measureName
			};
		},
		redrawPrices: function (item) {
			//TODO: init price data once, not in every call of function
			if (!item.id || !item.count) {return}

			if (item.id === this.itemId) {
				if (this.arResult['ITEM_QUANTITY_RANGES'].hasOwnProperty('ZERO-INF')) {return}

				const ranges = this.arResult['ITEM_QUANTITY_RANGES'];
				let currentRange = this.arResult['ITEM_QUANTITY_RANGE_SELECTED'];
				for (let range in ranges) {
					if (item.count >= (ranges[range].QUANTITY_FROM === "" ? Number.NEGATIVE_INFINITY : ranges[range].QUANTITY_FROM)
						&& item.count <= (ranges[range].QUANTITY_TO === "" ? Number.POSITIVE_INFINITY :  ranges[range].QUANTITY_TO)
					) {
						currentRange = range;
					}
				}
				for (let price in this.itemIds['PRICES']) {
					let node = document.getElementById(this.itemIds['PRICES'][price]);
					if (node && price !== 'PRIVATE_PRICE') {
						let currentPrice = this.arResult['PRINT_PRICES'][price];
						if (currentPrice.hasOwnProperty(currentRange)) {
							node.innerHTML = currentPrice[currentRange]['PRINT'];
						} else {
							node.innerHTML = '';
						}
					}
				}
			} else {
				if (!this.itemIds['OFFERS'].hasOwnProperty(item.id)) {return}
				let tmpOfferId = 0;
				const offer = this.arResult['OFFERS'].filter(function(element, iterator){
					if (element.ID == item.id) {
						tmpOfferId = iterator;
						return true;
					}
					return false;
				}.bind(this))[0];
				const ranges = offer['ITEM_QUANTITY_RANGES'];
				let currentRange = offer['ITEM_QUANTITY_RANGE_SELECTED'];
				for (let range in ranges) {
					if (item.count >= (ranges[range].QUANTITY_FROM === "" ? Number.NEGATIVE_INFINITY : ranges[range].QUANTITY_FROM)
						&& item.count <= (ranges[range].QUANTITY_TO === "" ? Number.POSITIVE_INFINITY :  ranges[range].QUANTITY_TO)
					) {
						currentRange = range;
					}
				}
				for (let price in this.itemIds['OFFERS'][offer.ID]['PRICES']) {
					let node = document.querySelector(`#${this.itemIds['OFFERS'][offer.ID]['PRICES'][price]} .bzd-prices__item-value`);
					if (node && price !== 'PRIVATE_PRICE') {
						let currentPrice = this.arResult['OFFERS'][tmpOfferId]['PRINT_PRICES'][price];
						if (currentPrice.hasOwnProperty(currentRange)) {
							node.innerHTML = currentPrice[currentRange]['PRINT'];
						} else {
							node.innerHTML = '';
						}
					}
				}
			}
		},
		deleteRepaetNotification: function () {
			if(document.querySelectorAll('.b2b-notifications__item').length > 1) {
				document.querySelector('.b2b-notifications__item').remove();
			}
		},
		initMarkdownItems: function() {
			if (!this.arResult.HAS_SECOND || !Array.isArray(this.arResult.SECOND_ITEMS) || !this.itemIds.SECOND_PRODUCTS) {
				return;
			}

			var secondItems = this.arResult.SECOND_ITEMS;
			var secondItemIds = this.itemIds.SECOND_PRODUCTS;
			var measureName = this.arResult.CATALOG_MEASURE_NAME || BX.message('BZD_MARKDOWN_MEASURE') || '\u0448\u0442';

			for (let i = 0; i < secondItems.length; i++) {
				let secondItem = secondItems[i];
				let rowKey = secondItem.ROW_KEY;
				if (!secondItemIds[rowKey]) {
					continue;
				}

				let secondIds = secondItemIds[rowKey];
				let nodes = {
					wrapper: document.getElementById(secondIds.QUANTITY),
					increment: document.getElementById(secondIds.QUANTITY_INCREMENT),
					value: document.getElementById(secondIds.QUANTITY_VALUE),
					decrement: document.getElementById(secondIds.QUANTITY_DECREMENT),
				};

				if (!nodes.wrapper || !nodes.increment || !nodes.decrement || !nodes.value) {
					continue;
				}

				let rowNode = document.querySelector('.bzd-markdown-item[data-row-key="' + rowKey + '"]');
				let addButton = rowNode ? rowNode.querySelector('.bzd-markdown-add-basket') : null;
				let touchspinWrapper = nodes.wrapper.closest('.bzd-markdown-touchspin');
				let quantityTrace = secondItem.QUANTITY_TRACE === 'Y';
				let canBuyZero = secondItem.CAN_BUY_ZERO === 'Y';
				let maxQty = quantityTrace && !canBuyZero ? parseFloat(secondItem.TOTAL_QUANTITY) : Number.POSITIVE_INFINITY;
				let actualQuantity = parseFloat(secondItem.ACTUAL_QUANTITY) || 0;

				const toggleRowControls = function(quantity, button, counter) {
					if (!button || !counter) {
						return;
					}
					if (quantity > 0) {
						button.style.display = 'none';
						counter.style.display = '';
					} else {
						button.style.display = '';
						counter.style.display = 'none';
					}
				};

				let item = {
					itemId: String(secondItem.ROW_KEY),
					basketProductId: secondItem.ID,
					name: secondItem.NAME,
					nodes: nodes,
					currentQuantity: actualQuantity,
					tmpQuantity: actualQuantity,
					maxQuantity: maxQty,
					minQuantity: 0,
					measureRatio: parseFloat(secondItem.MEASURE_RATIO) || 1,
					measureName: measureName,
					propsAddedToBasket: [
						{
							NAME: BX.message('BZD_MARKDOWN_PROP_STORE') || 'Markdown Store',
							CODE: 'MARKDOWN_STORE_ID',
							VALUE: String(secondItem.STORE_ID),
							SORT: 100
						},
						{
							NAME: BX.message('BZD_MARKDOWN_PROP_CATEGORY') || 'Markdown Category',
							CODE: 'MARKDOWN_CATEGORY',
							VALUE: String(secondItem.CATEGORY || ''),
							SORT: 200
						},
						{
							NAME: BX.message('BZD_MARKDOWN_PROP_ROW_KEY') || 'Markdown Row Key',
							CODE: 'MARKDOWN_ROW_KEY',
							VALUE: String(secondItem.ROW_KEY || ''),
							SORT: 300
						}
					],
					extraBasketFields: {
						STORE_ID: String(secondItem.STORE_ID),
						CATALOG_STORE_ID: String(secondItem.STORE_ID),
						MARKDOWN_STORE_ID: String(secondItem.STORE_ID),
						MARKDOWN_ROW_KEY: String(secondItem.ROW_KEY || ''),
						MARKDOWN_CATEGORY: String(secondItem.CATEGORY || ''),
						PRODUCT_XML_ID: 'markdown_' + String(secondItem.ROW_KEY || ''),
						CATALOG_XML_ID: 'markdown_catalog_' + String(secondItem.ID || '')
					},
					disableMessageSync: true,
					onQuantityChanged: function(quantity) {
						toggleRowControls(quantity, addButton, touchspinWrapper);
					}
				};

				this.initQuantity(item);
				toggleRowControls(item.currentQuantity, addButton, touchspinWrapper);

				if (addButton) {
					(function(incNode, itemRef, buttonRef){
						buttonRef.addEventListener('click', function() {
							if (itemRef.currentQuantity <= 0) {
								incNode.click();
							}
						});
					})(nodes.increment, item, addButton);
				}
			}

			this.initCategoryTooltips();
		},
		initCategoryTooltips: function() {
			var tooltipElements = document.querySelectorAll('.bzd-markdown-item__category-info[data-bs-toggle="tooltip"]');
			for (var i = 0; i < tooltipElements.length; i++) {
				new bootstrap.Tooltip(tooltipElements[i]);
			}
		}
	}
})(window);
