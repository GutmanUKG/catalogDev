;(function (window){

	'use strict';

	if (window.JCBlankZakazaItem)
		return;


	window.JCBlankZakazaItem = function (arResult, arParams)
	{
		this.DEBOUNCE_TIME = 500;
		this.arResult = arResult;
		this.arParams = arParams;
		this.itemId = arResult['ITEM']['ID'];
		this.node = document.getElementById(arResult['ITEM_IDS']['ID']);
		this.productType = arResult['ITEM']['CATALOG_TYPE'];
		this.nodesQuantity = {
			wrapper: document.getElementById(arResult['ITEM_IDS']['QUANTITY']),
			increment: document.getElementById(arResult['ITEM_IDS']['QUANTITY_INCREMENT']),
			value: document.getElementById(arResult['ITEM_IDS']['QUANTITY_VALUE']),
			decrement: document.getElementById(arResult['ITEM_IDS']['QUANTITY_DECREMENT']),
		};

		this.offersTogglerId = arResult['ITEM_IDS']['OFFERS_TOGGLER'];
		this.quantityTrace = arResult['ITEM'].CATALOG_QUANTITY_TRACE === "Y" ? true : false;
		this.canBuyZero = arResult['ITEM'].CATALOG_CAN_BUY_ZERO === "Y" ? true : false;
		this.currnetQuantity = parseFloat(arResult['ITEM']['ACTUAL_QUANTITY']) || 0;
		this.tmpQuantity = this.currnetQuantity;
		this.maxQuantity = this.quantityTrace && !this.canBuyZero ? parseFloat(arResult['ITEM']['CATALOG_QUANTITY']) : Number.POSITIVE_INFINITY;
		this.minQuantity = 0;
		this.measureRatio = parseFloat(
			arResult['ITEM']['CATALOG_MEASURE_RATIO']
				? arResult['ITEM']['CATALOG_MEASURE_RATIO']
				: arResult['ITEM']['ITEM_MEASURE_RATIOS'].length > 0
					? arResult['ITEM']['ITEM_MEASURE_RATIOS'][arResult['ITEM']['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
					: 0
		);
		this.measureName = arResult['ITEM']['CATALOG_MEASURE_NAME']
			? arResult['ITEM']['CATALOG_MEASURE_NAME']
			: arResult['ITEM']['ITEM_MEASURE']['TITLE'];
		this.ranges = [];
		this.prices = arResult['ITEM']['PRINT_PRICES'];
		this.delayAddToBasket = 0;
		this.isOffersHidden = true;
		BX.ready(BX.delegate(this.init,this));
	}

	window.JCBlankZakazaItem.prototype = {
		init: function() {
			this.initRanges();
			this.initOffers();
			this.initDetailLink();
			if (parseInt(this.productType) === 3) {
				this.initToggler();
				if (this.arParams.CATALOG_NOT_AVAILABLE === 'Y') {
					return;
				}
				for (let offer in this.offers) {

					this.initQuantity(this.getOfferQuantityPropsById(offer))
				}
			} else if (this.arParams.CATALOG_NOT_AVAILABLE === 'Y') {
				return;
			} else {
				this.initQuantity(this.getItemQuantityProps());
			}
		},
		initToggler: function() {
			const toggler = document.getElementById(this.offersTogglerId);
			if (!toggler) {return;}
			toggler.addEventListener('click', function(event) {
				this.toggleOffers();
			}.bind(this));
		},
		initRanges: function() {
			for (let price in this.prices) {
				this.ranges = Object.keys(this.prices[price])
				return
			}
		},
		initDetailLink: function () {
			const detailLink = this.node.querySelectorAll('.product__link');
			if (!detailLink || detailLink.length <= 0) return;

			detailLink.forEach(
				function(currentValue, currentIndex) {
					currentValue.addEventListener('click', function(event) {
						event.preventDefault();
						BX.SidePanel.Instance.open(
							currentValue.dataset.href,
							{
								width: 1344,
								label: {
									text: "",
									color: "#FFFFFF",
									bgColor: "#3e495f",
									opacity: 80
								}
							}
						);
					});
				}
			);
		},
		initOffers: function() {
			if (!Array.isArray(this.arResult['ITEM']['OFFERS']) && parseInt(this.productType) !== 3) {return}
			this.offers = {};
			const offers = this.arResult['ITEM']['OFFERS'];

			offers.forEach(function (offer) {
				// console.log(Object.keys(offer))
				// console.log(this.arResult.ITEM.NAME)
				const offerIds = this.arResult['ITEM_IDS']['OFFERS'][offer.ID];
				this.offers[offer.ID] = {
					id: offer.ID,
					// name: offer.NAME,
					name: this.arResult.ITEM.NAME,
					nodesQuantity:{
						wrapper: document.getElementById(offerIds.QUANTITY),
						increment: document.getElementById(offerIds.QUANTITY_INCREMENT),
						value: document.getElementById(offerIds.QUANTITY_VALUE),
						decrement: document.getElementById(offerIds.QUANTITY_DECREMENT),
					},
					currnetQuantity: parseFloat(offer.ACTUAL_QUANTITY),
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
					measureName: offer.CATALOG_MEASURE_NAME
						? offer.CATALOG_MEASURE_NAME
						: offer['ITEM_MEASURE']['TITLE'],
					quantityTrace: offer.CATALOG_QUANTITY_TRACE === "Y" ? true : false,
					canBuyZero: offer.CATALOG_CAN_BUY_ZERO === "Y" ? true : false
				}
			}.bind(this))

		},
		initQuantity: function(item) {
			if (item.nodes.wrapper === null) {return}
			const nodes = item.nodes;

			BX.addCustomEvent('SidePanel.Slider:onMessage', (event) => {

				if (event.eventId !== 'addProductToBasketFromDetail') {
					return;
				}

				if (event.data.id !== item.itemId) {
					return;
				}

				nodes.value.value = event.data.quantity;
				item.currnetQuantity = event.data.quantity;
				item.tmpQuantity = event.data.quantity;
				BX.onCustomEvent('OnBasketChange');

			})

			BX.addCustomEvent('UpdateItemQuantity', function() {
				BX.ajax.runAction('sotbit:b2bcabinet.basket.getBasketItemsQuantity',{})
					.then(
						function(data) {
							for (let basketItemId in data.data) {
								if(item.itemId === basketItemId) {
									item.currnetQuantity = parseFloat(data.data[basketItemId]);
									item.tmpQuantity = item.currnetQuantity;
									nodes.value.value = item.currnetQuantity;
								}
							}
						},
						function(error) {
							console.error(error);
						}
					)
			});

			window.addEventListener("message", function(event) {
				if (typeof event.data === 'object') {
					return;
				}

				const data = JSON.parse(event.data)
				if (data.itemId && data.quantity && data.itemId === item.itemId) {
					item.currnetQuantity = parseFloat(data.quantity);
					item.tmpQuantity = item.currnetQuantity;
					nodes.value.value = item.currnetQuantity;
				}
			})

			nodes.increment.addEventListener('click', function(event) {

				item.currnetQuantity = item.tmpQuantity;
				item.tmpQuantity = parseFloat((Number(item.tmpQuantity) + item.measureRatio).toFixed(3));

				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							nodes.increment.setAttribute("disabled", "disabled");
							this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
								.then(function(response) {
										item.currnetQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currnetQuantity;
										nodes.increment.removeAttribute("disabled");
										nodes.value.value = item.currnetQuantity;
										const frames = Array.prototype.slice.call(window.frames);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currnetQuantity
											}),"*")
										})
										BX.onCustomEvent('OnBasketChange');
										if (item.currnetQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_REMOVE_FROM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
												'success'
											]);
										}
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
										nodes.value.value = item.currnetQuantity;
										nodes.increment.removeAttribute("disabled");
										console.error(error)
									})
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currnetQuantity;
					item.tmpQuantity = item.currnetQuantity
				}
			}.bind(this))

			nodes.decrement.addEventListener('click', function(event) {

				item.currnetQuantity = item.tmpQuantity;
				item.tmpQuantity = parseFloat((item.tmpQuantity - item.measureRatio).toFixed(3));


				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							nodes.decrement.setAttribute("disabled", "disabled");
							this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
								.then(function(response) {
										item.currnetQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currnetQuantity;
										nodes.decrement.removeAttribute("disabled");
										nodes.value.value = item.currnetQuantity;
										const frames = Array.prototype.slice.call(window.frames);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currnetQuantity
											}),"*")
										})
										BX.onCustomEvent('OnBasketChange');
										if (item.currnetQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_REMOVE_FROM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
												'success'
											]);
										}
									}.bind(this),
									function(error){
										BX.onCustomEvent('B2BNotification',[
											error.errors.map(function (error) {return error.message}).join('<br>'),
											'alert'
										]);
										nodes.value.value = item.currnetQuantity;
										nodes.decrement.removeAttribute("disabled");
										console.error(error)
									})
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currnetQuantity;
					item.tmpQuantity = item.currnetQuantity
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
							this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
								.then(function(response) {
										item.currnetQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currnetQuantity;
										nodes.value.value = item.currnetQuantity;
										const frames = Array.prototype.slice.call(window.frames);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currnetQuantity
											}),"*")
										})
										BX.onCustomEvent('OnBasketChange');
										if (item.currnetQuantity === 0) {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_REMOVE_FROM_BASKET'),
												'success'
											]);
										} else {
											BX.onCustomEvent('B2BNotification',[
												BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
												BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
												'success'
											]);
										}
									}.bind(this),
									function(error){
										BX.onCustomEvent('B2BNotification',[
											error.errors.map(function (error) {return error.message}).join('<br>'),
											'alert'
										]);
										nodes.value.value = item.tmpQuantity = item.currnetQuantity;
									})
						}.bind(this)
						,800)
				} else {
					event.target.value = item.currnetQuantity;
					item.tmpQuantity = item.currnetQuantity
				}
			}.bind(this))
		},
		addToBasket: function(id, quantity, measureRatio, porpsAddedToBasket) {

			const quantity1 = Math.round(quantity * 1000000);
			const measureRatio1 = Math.round(measureRatio * 1000000);
			const remainder = quantity1 % measureRatio1;
			quantity = (quantity1 - remainder) / 1000000;

			return BX.ajax.runAction('sotbit:b2bcabinet.basket.addProductToBasket', {
				data: {
					arFields: {
						'PRODUCT_ID': id,
						'QUANTITY': quantity,
						'PROPS': porpsAddedToBasket,
					}
				},
			})
		},
		toggleOffers: function() {
			const offers = Array.prototype.slice.call(this.node.querySelectorAll('.product--offer'));
			if (this.isOffersHidden) {
				offers.forEach(function(offer, index) {
					setTimeout(() => {offer.classList.remove('hidden')}, (200/offers.length) * (index))
					setTimeout(() => {offer.classList.add('show')}, (200/offers.length) * (index) + 10);
				})
			} else {
				offers.forEach(function(offer, index) {
					setTimeout(() => {offer.classList.remove('show')}, (200/offers.length) * (offers.length - index - 1));
					setTimeout(() => {offer.classList.add('hidden')}, (200/offers.length) * (offers.length - index - 1) + 200)
				})
			}
			this.isOffersHidden = !this.isOffersHidden;
		},
		getItemQuantityProps: function () {
			return {
				itemId: this.itemId,
				name: this.arResult['ITEM']['NAME'],
				nodes: this.nodesQuantity,
				currnetQuantity: this.currnetQuantity,
				tmpQuantity: this.tmpQuantity,
				maxQuantity: this.maxQuantity,
				minQuantity: this.minQuantity,
				measureRatio: this.measureRatio,
				measureName: this.measureName
			}
		},
		getOfferQuantityPropsById: function(id) {
			const offers = this.arResult['ITEM']['OFFERS']
			if (!Array.isArray(offers)) {
				console.error('JCBlankZakazaItem: Can not find offers in product id=' + this.itemId);
				return {
					nodes: null,
					currnetQuantity: null,
					tmpQuantity: null,
					maxQuantity: null,
					minQuantity: null
				}
			}

			const offer = this.offers[id];

			const offerIds = this.arResult['ITEM_IDS']['OFFERS'][id];



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
				currnetQuantity: offer.currnetQuantity,
				tmpQuantity: offer.tmpQuantity,
				maxQuantity: offer.maxQuantity,
				minQuantity: offer.minQuantity,
				measureRatio: offer.measureRatio,
				measureName: offer.measureName
			};
		},
		redrawPrices: function (item) { //TODO: init price data once, not in every call of function
			if (!item.id || !item.count) {return}

			if (item.id === this.itemId) {
				if (this.arResult['ITEM']['ITEM_QUANTITY_RANGES'].hasOwnProperty('ZERO-INF')) {return}

				const ranges = this.arResult['ITEM']['ITEM_QUANTITY_RANGES'];
				let currentRange = this.arResult['ITEM']['ITEM_QUANTITY_RANGE_SELECTED'];

				for (let range in ranges) {
					if (item.count >= (ranges[range].QUANTITY_FROM === "" ? Number.NEGATIVE_INFINITY : ranges[range].QUANTITY_FROM)
						&& item.count <= (ranges[range].QUANTITY_TO === "" ? Number.POSITIVE_INFINITY :  ranges[range].QUANTITY_TO)
					) {
						currentRange = range;
					}
				}

				for (let price in this.arResult['ITEM_IDS']['PRICES']) {
					let node = document.getElementById(this.arResult['ITEM_IDS']['PRICES'][price]);
					if (node && price !== 'PRIVATE_PRICE') {
						let currentPrice = this.arResult['ITEM']['PRINT_PRICES'][price];
						if (currentPrice && currentPrice.hasOwnProperty(currentRange)) {
							node.innerHTML = currentPrice[currentRange]['PRINT'];
						} else {
							node.innerHTML = '';
						}
					}
				}
			} else {
				if (!this.arResult['ITEM_IDS']['OFFERS'].hasOwnProperty(item.id)) {return}

				let tmpOfferId = 0;
				const offer = this.arResult['ITEM']['OFFERS'].filter(function(element, iterator){
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

				for (let price in this.arResult['ITEM_IDS']['OFFERS'][offer.ID]['PRICES']) {
					let node = document.getElementById(this.arResult['ITEM_IDS']['OFFERS'][offer.ID]['PRICES'][price]);

					if (node && price !== 'PRIVATE_PRICE') {
						let currentPrice = this.arResult['ITEM']['OFFERS'][tmpOfferId]['PRINT_PRICES'][price];
						if (currentPrice && currentPrice.hasOwnProperty(currentRange)) {
							node.innerHTML = currentPrice[currentRange]['PRINT'];
						} else {
							node.innerHTML = '';
						}
					}
				}
			}
		}
	}
})(window);