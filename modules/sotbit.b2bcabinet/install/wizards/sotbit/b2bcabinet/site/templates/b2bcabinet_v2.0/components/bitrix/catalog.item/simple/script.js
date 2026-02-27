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
		this.showAbsent = true;
		this.showSkuProps = false;
		this.nodesQuantity = {
			wrapper: document.getElementById(arResult['ITEM_IDS']['QUANTITY']),
			increment: document.getElementById(arResult['ITEM_IDS']['QUANTITY_INCREMENT']),
			value: document.getElementById(arResult['ITEM_IDS']['QUANTITY_VALUE']),
			decrement: document.getElementById(arResult['ITEM_IDS']['QUANTITY_DECREMENT']),
		};
		this.nodePict = document.getElementById(arResult['ITEM_IDS']['PICTURE']);
		this.nodeAvaliable = this.node.querySelector('.product__quant');
		this.nodePrices = this.node.querySelectorAll('[data-entity="price-block"] .wrap-product__property--price');
		this.nodePriceMobile = this.node.querySelector('.product__property--price-mobile');
		this.nodeEmptyOffer = document.getElementById(arResult['ITEM_IDS']['OFFER_ROW_EMPTY']);
		this.nodeOffers = null;
		this.offers = {};
		this.treeProps = [];
		this.selectedValues = {};

		this.offersTogglerId = arResult['ITEM_IDS']['OFFERS_TOGGLER'];
		this.quantityTrace = arResult['ITEM'].CATALOG_QUANTITY_TRACE === "Y" ? true : false;
		this.canBuyZero = arResult['ITEM'].CATALOG_CAN_BUY_ZERO === "Y" ? true : false;
		this.ranges = [];
		this.prices = arResult['ITEM']['PRINT_PRICES'];
		this.delayAddToBasket = 0;

		this.obQuantity = {
			isSKU: false,
			itemId: arResult['ITEM']['ID'],
			name: arResult['ITEM']['NAME'],
			nodes: this.nodesQuantity,
			currentQuantity: parseFloat(arResult['ITEM']['ACTUAL_QUANTITY']) || 0,
			tmpQuantity: parseFloat(arResult['ITEM']['ACTUAL_QUANTITY']) || 0,
			maxQuantity: this.quantityTrace && !this.canBuyZero ? parseFloat(arResult['ITEM']['CATALOG_QUANTITY']) : Number.POSITIVE_INFINITY,
			minQuantity: 0,
			measureRatio: parseFloat(
				arResult['ITEM']['CATALOG_MEASURE_RATIO']
					? arResult['ITEM']['CATALOG_MEASURE_RATIO']
					: arResult['ITEM']['ITEM_MEASURE_RATIOS'].length > 0
						? arResult['ITEM']['ITEM_MEASURE_RATIOS'][arResult['ITEM']['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
						: 0
			),
			measureName: arResult['ITEM']['CATALOG_MEASURE_NAME']
				? arResult['ITEM']['CATALOG_MEASURE_NAME']
				: arResult['ITEM']['ITEM_MEASURE']['TITLE']
		};
		this.blockNodes = {};
		this.obTree = null;
		this.obSkuProps = null;
		this.errorCode = 0;
		this.product = {
			canBuy: true,
			name: '',
			pict: {},
			id: 0,
			addUrl: '',
			buyUrl: ''
		};
		this.obPrice = null;

		this.defaultPict = {
			pict: null,
			secondPict: null
		}

		if (typeof arParams === 'object') {
			if (arParams.PRODUCT_TYPE) {
				this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
			}

			this.showAbsent = arParams.SHOW_ABSENT;
			this.showSkuProps = arParams.SHOW_SKU_PROPS;
			this.blockNodes.quantity = this.node.querySelector('[data-entity="quantity-block"]');
		}

		switch (this.productType) {
			case 3: // sku
				if (arParams.PRODUCT && typeof arParams.PRODUCT === 'object') {
					this.product.name = arParams.PRODUCT.NAME;
					this.product.id = arParams.PRODUCT.ID;
					this.product.DETAIL_PAGE_URL = arParams.PRODUCT.DETAIL_PAGE_URL;
					this.product.morePhotoCount = arParams.PRODUCT.MORE_PHOTO_COUNT;
					this.product.morePhoto = arParams.PRODUCT.MORE_PHOTO;

					if (arParams.PRODUCT.RCM_ID) {
						this.product.rcmId = arParams.PRODUCT.RCM_ID;
					}
				}

				if (arParams.OFFERS && BX.type.isArray(arParams.OFFERS) && arParams.OFFERS.length !== 0) {
					this.offerNum = 0;
					
					this.obQuantity.isSKU = true;
					
					if (arParams.OFFER_SELECTED) {
						this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
					}

					if (isNaN(this.offerNum)) {
						this.offerNum = 0;
					}

					if (arParams.TREE_PROPS) {
						this.treeProps = arParams.TREE_PROPS;
					}

					if (arParams.DEFAULT_PICTURE) {
						this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;
						this.defaultPict.secondPict = arParams.DEFAULT_PICTURE.PICTURE_SECOND;
					}

					if (arParams.OFFERS_VIEW !== 'BLOCK') {
						this.nodeOffers = document.getElementById(arResult['ITEM_IDS']['ID'] + '_offers');
					}
				}

				break;
		}

		BX.ready(BX.delegate(this.init,this));
	}

	window.JCBlankZakazaItem.prototype = {
		init: function() {
			this.initRanges();
			this.initOffers();
			if (parseInt(this.productType) === 3) {
				this.initToggler();
				if (this.arParams.CATALOG_NOT_AVAILABLE === 'Y') {
					return;
				}

				for (let offer in this.offers) {
					if (this.arParams.OFFERS_VIEW !== 'BLOCK') {
						this.initQuantity(this.offers[offer].obQuantity);
					}
				}

				if (this.arParams.OFFERS_VIEW !== 'LIST') {
					this.initQuantity(this.obQuantity);
					this.setCurrentOffer();
				} 
				
				if (this.arParams.OFFERS_VIEW !== 'BLOCK') {
					this.initSearchOffers();
				}

			} else if (this.arParams.CATALOG_NOT_AVAILABLE === 'Y') {
				return;
			} else {
				this.initQuantity(this.obQuantity);
			}
		},
		initToggler: function() {
			const toggler = document.getElementById(this.offersTogglerId);
			if (!toggler) {return;}
			toggler.addEventListener('click', function(event) {
				toggler.classList.toggle('show')
				this.toggleOffers();
				$('.blank-zakaza__wrapper').floatingScroll('update');
			}.bind(this));
		},
		initRanges: function() {
			for (let price in this.prices) {
				this.ranges = Object.keys(this.prices[price])
				return
			}
		},
		initOffers: function() {
			if (this.arResult['ITEM']['OFFERS'].length === 0 && parseInt(this.productType) !== 3) {return}

			const offers = this.arResult['ITEM']['OFFERS'];
			offers.forEach(function (offer, index) {

				this.offers[index] = {
					id: offer.ID,
					name: offer.NAME ?? this.arResult.ITEM.NAME,
					sku: offer['PROPERTIES'][this.arParams['ARTICLE_PROPERTY_OFFERS']],
					picture: offer.PICTURE,
					picture_150: offer.PICTURE_150,
					nodesMainQuantity: this.nodesQuantity,
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
					measureName: offer.CATALOG_MEASURE_NAME
						? offer.CATALOG_MEASURE_NAME
						: offer['ITEM_MEASURE']['TITLE'],
					quantityTrace: offer.CATALOG_QUANTITY_TRACE === "Y" ? true : false,
					canBuyZero: offer.CATALOG_CAN_BUY_ZERO === "Y" ? true : false,
					obQuantity: {
						currentQuantity: parseFloat(offer.ACTUAL_QUANTITY),
						nodes: this.nodesQuantity,
					},
					CAN_BUY: offer.CAN_BUY,
					ITEM_PRICE_MODE: offer.ITEM_PRICE_MODE,
					ITEM_PRICES: offer.ITEM_PRICES,
					ITEM_PRICE_SELECTED: offer.ITEM_PRICE_SELECTED,
					ITEM_QUANTITY_RANGES: offer.ITEM_QUANTITY_RANGES,
					ITEM_QUANTITY_RANGE_SELECTED: offer.ITEM_QUANTITY_RANGE_SELECTED,
					CATALOG_QUANTITY: offer.CATALOG_QUANTITY,
					PRICES: offer.PRICES,
					PRINT_PRICES: offer.PRINT_PRICES,
					MIN_PRICE: offer.MIN_PRICE,
				}

				for (let i in this.arParams.OFFERS) {
					if (this.arParams.OFFERS[i].ID == this.offers[index].id) {
						this.offers[index].TREE = this.arParams.OFFERS[i].TREE;
						break;
					}
				}

				if (this.arParams.OFFERS_VIEW !== 'BLOCK') {
					const offerIds = this.arResult['ITEM_IDS']['OFFERS'][index];

					this.offers[index].node = document.getElementById(offerIds.ID);
					this.offers[index].obQuantity = this.getOfferQuantityPropsById(index);
				}

			}.bind(this))

			if (this.productType === 3) {
				this.obTree = BX(this.arResult.ITEM_IDS.SKU_TREE);
				if (!this.obTree) {
					this.errorCode = -256;
				}
			}

			if (this.arParams.OFFERS_VIEW !== 'LIST') {
				const treeItems = this.obTree.querySelectorAll('li');

				if (treeItems && treeItems.length) {
					treeItems.forEach((item) => {
						BX.bind(item, 'click', BX.delegate(this.selectOfferProp, this));
					})
				}
			} 
		},
		initQuantity: function(item) {
			if (item.nodes === null) {return}
			const nodes = item.nodes;

			BX.addCustomEvent('SidePanel.Slider:onMessage', (event) => {

				if (event.eventId !== 'addProductToBasketFromDetail') {
					return;
				}

				if (event.data.id !== item.itemId) {
					return;
				}

				nodes.value.value = event.data.quantity;
				item.currentQuantity = event.data.quantity;
				item.tmpQuantity = event.data.quantity;
				BX.onCustomEvent('OnBasketChange');

			})

			BX.addCustomEvent('UpdateQuantityOnRequest', function() {
				BX.ajax.runAction('sotbit:b2bcabinet.basket.getBasketItemsQuantity',{})
					.then(
						function(data) {
							for (let basketItemId in data.data) {
								if(item.itemId === basketItemId) {
									item.currentQuantity = parseFloat(data.data[basketItemId]);
									item.tmpQuantity = item.currentQuantity;
									nodes.value.value = item.currentQuantity;
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
					item.currentQuantity = parseFloat(data.quantity);
					item.tmpQuantity = item.currentQuantity;
					nodes.value.value = item.currentQuantity;
				}
			})

			if (this.arParams.OFFERS_VIEW !== 'LIST') {
				BX.addCustomEvent(item, 'UpdateQuantity', function(event) {
					item.itemId = event.data.id;
					nodes.value.value = event.data.quantity;
					item.currentQuantity = event.data.quantity;
					item.tmpQuantity = event.data.quantity;
					
					if (item.isSKU) {
						item.name = event.data.name || item.name;
						item.maxQuantity = event.data.maxQuantity || item.maxQuantity;
						item.minQuantity = event.data.minQuantity || item.minQuantity;
						item.measureName = event.data.measureName || item.measureName;
						item.measureRatio = event.data.measureRatio || item.measureRatio;
					}
				});
			}

			nodes.increment.onclick = function(event) {
				item.currentQuantity = item.tmpQuantity;
				item.tmpQuantity = parseFloat((Number(item.tmpQuantity) + item.measureRatio).toFixed(3));

				if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

					nodes.value.value = item.tmpQuantity;
					this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

					clearTimeout(item.delayAddToBasket);
					item.delayAddToBasket = setTimeout(
						function(){
							nodes.increment.setAttribute("disabled", "disabled");
							nodes.increment.firstElementChild.classList.add('spinner-grow');
							this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
								.then(function(response) {
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.increment.removeAttribute("disabled");
										nodes.increment.firstElementChild.classList.remove('spinner-grow');
										nodes.value.value = item.currentQuantity;
										const frames = Array.prototype.slice.call(window.frames);

										if (item.isProduct && this.arParams.OFFERS_VIEW === 'BLOCK' && this.offers.length) {
											this.setCurrentQuantityForOffer(item);
										}

										this.eventUpdateQuantity(item);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										});
											
										BX.onCustomEvent('OnBasketChange');
										this.showNotificationSuccess(item);
									}.bind(this),
									function(error){
										this.showNotificationError(error);
										nodes.value.value = item.currentQuantity;
										nodes.increment.removeAttribute("disabled");
										nodes.increment.firstElementChild.classList.remove('spinner-grow');
										console.error(error)
									}.bind(this))
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity;
				}
			}.bind(this)

			nodes.decrement.onclick = function(event) {
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
							this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
								.then(function(response) {
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.decrement.removeAttribute("disabled");
										nodes.decrement.firstElementChild.classList.remove('spinner-grow');
										nodes.value.value = item.currentQuantity;
										const frames = Array.prototype.slice.call(window.frames);

										if (item.isSKU && this.arParams.OFFERS_VIEW === 'BLOCK') {
											this.setCurrentQuantityForOffer(item);
										}

										this.eventUpdateQuantity(item);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										})
										BX.onCustomEvent('OnBasketChange');
										this.showNotificationSuccess(item);
									}.bind(this),
									function(error){
										this.showNotificationError(error);
										nodes.value.value = item.currentQuantity;
										nodes.decrement.removeAttribute("disabled");
										nodes.decrement.firstElementChild.classList.remove('spinner-grow');
										console.error(error);
									}.bind(this))
						}.bind(this)
						,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity;
				}
			}.bind(this)

			nodes.value.oninput = function(event) {
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
										item.currentQuantity = parseFloat(response.data);
										item.tmpQuantity = item.currentQuantity;
										nodes.value.value = item.currentQuantity;
										const frames = Array.prototype.slice.call(window.frames);
										
										if (item.isSKU && this.arParams.OFFERS_VIEW === 'BLOCK') {
											this.setCurrentQuantityForOffer(item);
										}
										
										this.eventUpdateQuantity(item);
										frames.forEach(function(frame) {
											frame.postMessage(JSON.stringify({
												itemId: item.itemId,
												quantity: item.currentQuantity
											}),"*")
										})
										BX.onCustomEvent('OnBasketChange');
										this.showNotificationSuccess(item);
									}.bind(this),
									function(error){
										this.showNotificationError(error);
										nodes.value.value = item.tmpQuantity = item.currentQuantity;
										console.error(error);
									}.bind(this))
						}.bind(this)
					,this.DEBOUNCE_TIME)
				} else {
					event.target.value = item.currentQuantity;
					item.tmpQuantity = item.currentQuantity
				}
						
			}.bind(this);

			nodes.value.onfocus = function (event) {
				this.value = parseFloat(this.value) || '';
			}

			nodes.value.onblur = function (event) {
				this.value = this.value || 0;
			}
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
			this.nodeOffers.classList.toggle('hidden');
		},
		initSearchOffers: function() {
			let strSearch = '',
				isEmpty = true,
				displayProduct = window.innerWidth > 1200 ? 'table-row' : 'grid';

			this.nodeOffers.querySelector('[data-offer-search]').addEventListener('input', ()=> {
				strSearch = event.target.value.toLowerCase();
				isEmpty = true;

				for (let i in this.offers) {
					if (!strSearch) {
						this.offers[i].node.style.display = displayProduct;
						isEmpty = false;
						continue;
					}

					if (this.offers[i].name.toLowerCase().includes(strSearch)) {
						this.offers[i].node.style.display = displayProduct;
						isEmpty = false
					} else if (this.offers[i].sku?.VALUE.toLowerCase().includes(strSearch)) {
						this.offers[i].node.style.display = displayProduct;
						isEmpty = false
					} else {
						this.offers[i].node.style.display = 'none';
					}
				}

				if (isEmpty) {
					this.nodeEmptyOffer.classList.add('show');
				} else {
					this.nodeEmptyOffer.classList.remove('show');
				}
			});
		},
		setCurrentOffer: function() {
			let arCanBuyValues = [],
				strName = '',
				arShowValues = false,
				arFilter = {},
				tmpFilter = [],
				current = this.offers[this.offerNum].TREE;

			for (let i = 0; i < this.treeProps.length; i++) {
				strName = 'PROP_' + this.treeProps[i].ID;
				arShowValues = this.getRowValues(arFilter, strName);
				
				if (!arShowValues) {
					break;
				}

				if (BX.util.in_array(current[strName], arShowValues)) {
					arFilter[strName] = current[strName];
				} else {
					arFilter[strName] = arShowValues[0];
					this.offerNum = 0;
				}

				if (this.showAbsent) {
					arCanBuyValues = [];
					tmpFilter = [];
					tmpFilter = BX.clone(arFilter, true);
					arShowValues.forEach((value) => {
						tmpFilter[strName] = value;
						if (this.getCanBuy(tmpFilter)) {
							arCanBuyValues[arCanBuyValues.length] = value;
						}
					})
				} else {
					arCanBuyValues = arShowValues;
				}

				this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
			}
			
			this.selectedValues = arFilter;
			this.changeInfo();
		},
		selectOfferProp: function() {
			let value = '',
				strTreeValue = '',
				arTreeItem = [],
				rowItems = null,
				target = BX.proxy_context;

			if (target && target.hasAttribute('data-treevalue')) {
				if (BX.hasClass(target, 'selected') || BX.hasClass(target, 'notallowed'))
					return;

				strTreeValue = target.getAttribute('data-treevalue');
				arTreeItem = strTreeValue.split('_');

				if (this.searchOfferPropIndex(arTreeItem[0], arTreeItem[1])){
					rowItems = target.parentNode.querySelectorAll('li');

					if (rowItems && rowItems.length > 0) {
						for (let i = 0; i < rowItems.length; i++) {
							value = rowItems[i].getAttribute('data-onevalue');

							if (value === arTreeItem[1]) {
								rowItems[i].classList.add('selected');
							} else {
								rowItems[i].classList.remove('selected');
							}
						}
					}
				}
			}
		},
		searchOfferPropIndex: function(strPropID, strPropValue)
		{
			var strName = '',
				arShowValues = false,
				i, j,
				arCanBuyValues = [],
				allValues = [],
				index = -1,
				arFilter = {},
				tmpFilter = [];

			for (i = 0; i < this.treeProps.length; i++)
			{
				if (this.treeProps[i].ID === strPropID)
				{
					index = i;
					break;
				}
			}

			if (-1 < index)
			{
				for (i = 0; i < index; i++)
				{
					strName = 'PROP_'+this.treeProps[i].ID;
					arFilter[strName] = this.selectedValues[strName];
				}
				strName = 'PROP_'+this.treeProps[index].ID;
				arShowValues = this.getRowValues(arFilter, strName);

				if (!arShowValues) return false;
				if (!BX.util.in_array(strPropValue, arShowValues)) return false;

				arFilter[strName] = strPropValue;
				for (i = index+1; i < this.treeProps.length; i++)
				{
					strName = 'PROP_'+this.treeProps[i].ID;
					arShowValues = this.getRowValues(arFilter, strName);
					if (!arShowValues)
					{
						return false;
					}
					allValues = [];
					if (this.showAbsent)
					{
						arCanBuyValues = [];
						tmpFilter = [];
						tmpFilter = BX.clone(arFilter, true);
						for (j = 0; j < arShowValues.length; j++)
						{
							tmpFilter[strName] = arShowValues[j];
							allValues[allValues.length] = arShowValues[j];
							if (this.getCanBuy(tmpFilter))
								arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					}
					else
					{
						arCanBuyValues = arShowValues;
					}
					if (this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues))
					{
						arFilter[strName] = this.selectedValues[strName];
					}
					else
					{
						if (this.showAbsent)
							arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
						else
							arFilter[strName] = arCanBuyValues[0];
					}

					this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
				}
				this.selectedValues = arFilter;
				this.changeInfo();
			}
			return true;
		},
		getRowValues: function (arFilter, index) {
			let arValues = [],
				isSearch = false,
				isOneSearch = true;
			
			if (Object.keys(arFilter).length === 0) {
				for (let i in this.offers) {
					if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
						arValues[arValues.length] = this.offers[i].TREE[index];
					}
				}
				isSearch = true;
			} else {
				for (let i in this.offers) {
					isOneSearch = true;
					for (let j in arFilter) {
						if (arFilter[j] !== this.offers[i].TREE[j]) {
							isOneSearch = false;
							break;
						}
					}

					if (isOneSearch) {
						if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
							arValues[arValues.length] = this.offers[i].TREE[index];
						}
						isSearch = true;
					}
				}
			}
			
			return (isSearch ? arValues : false);
		},
		getCanBuy: function (arFilter) {
			let isSearch = false,
				isOneSearch = true;

			for (let i in this.offers) {
				isOneSearch = true;
				for (let j in arFilter) {
					if (arFilter[j] !== this.offers[i].TREE[j]) {
						isOneSearch = false;
						break;
					}
				}

				if (isOneSearch) {
					if (this.offers[i].CAN_BUY) {
						isSearch = true;
						break;
					}
				}
			}

			return isSearch;
		},
		getOfferQuantityPropsById: function(id) {
			const offers = this.arResult['ITEM']['OFFERS']
			if (!Array.isArray(offers)) {
				console.error('JCBlankZakazaItem: Can not find offers in product id=' + this.itemId);
				return {
					nodes: null,
					currentQuantity: null,
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
				itemId: offer.id,
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
		updateRow: function (intNumber, activeID, showID, canBuyID) {
			let value = '',
				isCurrent = false,
				rowItems = null,
				listContainer;

			const lineContainer = this.obTree.querySelectorAll('[data-entity="sku-line-block"]');

			if (intNumber > -1 && intNumber < lineContainer.length) {
				rowItems = lineContainer[intNumber].querySelectorAll('ul li');

				if (rowItems && rowItems.length > 0) {
					rowItems.forEach((item) => {
						value = item.getAttribute('data-onevalue');
						isCurrent = value === activeID;

						if (isCurrent) {
							item.classList.add('selected');
						} else {
							item.classList.remove('selected');
						}

						if (BX.util.in_array(value, canBuyID)) {
							item.classList.remove('notallowed');
						} else {
							item.classList.add('notallowed');
						}

						item.style.display = BX.util.in_array(value, showID) ? '' : 'none';

						if (isCurrent) {
							lineContainer[intNumber].style.display = (value == 0 && canBuyID.length == 1) ? 'none' : '';
						}
					});
				}
			}
				  
		},
		changeInfo: function () {
			let index = -1,
				isOneSearch = true;

			for (let i in this.offers) {
				isOneSearch = true;
				for (let j in this.selectedValues) {
					if (this.selectedValues[j] !== this.offers[i].TREE[j]) {
						isOneSearch = false;
						break;
					}
				}
				if (isOneSearch) {
					index = i;
					break;
				}
			}
			if (index > -1) {
				if (this.nodePict && this.offers[index].PREVIEW_PICTURE) {
					this.nodePict.setAttribute('src', this.offers[index].PREVIEW_PICTURE.SRC);
				} else {
					this.nodePict.setAttribute('src', this.defaultPict.pict.SRC);
				}
				if (this.showSkuProps && this.offers[index].sku) {
					const nodeSku = this.node.querySelector('.product__artnumber');		
					
					if (this.offers[index].sku.VALUE && this.offers[index].sku.VALUE.length > 20) {
						nodeSku.setAttribute('title',  this.offers[index].DISPLAY_PROPERTIES);
						nodeSku.innerHTML = this.offers[index].sku.NAME + ': ' + this.offers[index].sku.VALUE.substr(0, 20) + '...';
					} else if (this.offers[index].sku.VALUE) {
						nodeSku.innerHTML = this.offers[index].sku.NAME + ': ' + this.offers[index].sku.VALUE;
					} else {
						nodeSku.innerHTML = '';
					}
				}

				this.offerNum = index;
				this.setQuantity(index);
				this.setPrice(index);
				this.setAvaliable(index);
				this.setPicture(index);
				this.setName(index);
			}
		},
		setQuantity: function(index) {
			let newOffer = this.offers[index];

			if (this.errorCode === 0) {
				this.canBuy = newOffer.CAN_BUY;

				// this.currentPriceMode = newOffer.ITEM_PRICE_MODE;
				// this.currentPrices = newOffer.ITEM_PRICES;
				// this.currentPriceSelected = newOffer.ITEM_PRICE_SELECTED;
				// this.currentQuantityRanges = newOffer.ITEM_QUANTITY_RANGES;
				// this.currentQuantityRangeSelected = newOffer.ITEM_QUANTITY_RANGE_SELECTED;

				if (this.canBuy) {
					if (this.blockNodes.quantity) {
						this.blockNodes.quantity.style.display = '';
					}

					BX.onCustomEvent(newOffer.obQuantity, 'UpdateQuantity', {
						id: newOffer.id,
						quantity: newOffer.obQuantity.currentQuantity
					});

					if (this.arParams.OFFERS_VIEW !== 'LIST') {
						BX.onCustomEvent(this.obQuantity, 'UpdateQuantity', {
							id: newOffer.id,
							name: newOffer.name,
							quantity: newOffer.obQuantity.currentQuantity,
							maxQuantity: newOffer.maxQuantity,
							minQuantity: newOffer.minQuantity,
							measureName: newOffer.measureName,
							measureRatio: newOffer.measureRatio
						});
					}
				}
			}
		},
		setPrice: function(index) {
			const prices = this.offers[index]['PRINT_PRICES'];
			let price;

			this.nodePrices.forEach(item => {
				if (prices[item.dataset.code] && prices[item.dataset.code][this.offers[index].ITEM_QUANTITY_RANGE_SELECTED]) {
					price = prices[item.dataset.code][this.offers[index].ITEM_QUANTITY_RANGE_SELECTED];
					item.querySelector('.product__price-value').innerHTML = price.PRINT;

					if (price.DISCOUNT_PRICE && Math.round(price.DISCOUNT_PRICE ?? 0, 2) !== Math.round(price.PRICE ?? 0, 2)) {
						item.querySelector('.product__property--old-price').innerHTML = price.PRINT_NOT_DISCOUNT_PRICE;
					} else {
						item.querySelector('.product__property--old-price').innerHTML = '';
					}
				} else {
					item.querySelector('.product__price-value').innerHTML = '';
					item.querySelector('.product__property--old-price').innerHTML = '';
				}
			});

			this.nodePriceMobile.innerHTML = this.offers[index].MIN_PRICE.PRINT;
		},
		setAvaliable: function (index) {
			if (!this.nodeAvaliable) return;
			let generalQuantity = 0;
			let quantityIcon = '';

			if (this.arParams.SHOW_MAX_QUANTITY === 'M') {
				if (!this.offers[index].CATALOG_QUANTITY) {
					generalQuantity = this.arParams.MESS_NOT_AVAILABLE;
					quantityIcon = 'empty';
				}
				else {
					generalQuantity = +this.offers[index].CATALOG_QUANTITY > +this.arParams.RELATIVE_QUANTITY_FACTOR
						? this.arParams.MESS_RELATIVE_QUANTITY_MANY
						: this.arParams.MESS_RELATIVE_QUANTITY_FEW;
					quantityIcon = +this.offers[index].CATALOG_QUANTITY > +this.arParams.RELATIVE_QUANTITY_FACTOR
						? "many"
						: "few";
				}
			} else {
				generalQuantity = this.offers[index].CATALOG_QUANTITY;
			}
			
			this.nodeAvaliable.querySelector('.item-quantity__general').innerHTML = generalQuantity;
			this.nodeAvaliable.querySelector('.title-quant').innerHTML = (this.offers[index].measureRatio !== 1 ? this.offers[index].measureRatio : '');
			this.nodeAvaliable.querySelector('.title-quant').innerHTML += this.arParams.SHOW_MAX_QUANTITY !== 'M' ? ' ' +  this.offers[index].measureName : '';

			if (this.arParams.SHOW_MAX_QUANTITY !== 'N' && window['ob_store_item_'+this.offers[index].id]?.setEventAvaliable) {
				this.nodeAvaliable.classList.add('show-store');
				window['ob_store_item_'+this.offers[index].id].setEventAvaliable(this.nodeAvaliable);
			}
		},
		setPicture: function (index) {
			this.nodePict.setAttribute('src', this.offers[index].picture);
			this.nodePict.setAttribute('srcset', `${this.offers[index].picture} 74w, ${this.offers[index].picture_150} 150w`);
		},
		setName: function (index) {
			this.node.querySelector('.product__link').innerHTML = this.offers[index].name;
		},
		setCurrentQuantityForOffer: function (data) {
			this.offers[this.offerNum].obQuantity.currentQuantity = data.currentQuantity;
			this.offers[this.offerNum].obQuantity.tmpQuantity = data.currentQuantity;
		},
		eventUpdateQuantity: function (item) {
			if (this.arParams.OFFERS_VIEW === 'COMBINED') {
				if (item.isSKU) {
					BX.onCustomEvent(this.offers[this.offerNum].obQuantity, 'UpdateQuantity', {
						id: item.itemId,
						quantity: item.currentQuantity
					});
				} else {
					BX.onCustomEvent(this.obQuantity, 'UpdateQuantity', {
						id: item.itemId,
						quantity: item.currentQuantity
					});
				}
			}
		},
		showNotificationError: function (error) {
			BX.onCustomEvent('B2BNotification',[
				error.errors.map(function (error) {return error.message}).join('<br>'),
				'alert'
			]);
		},
		showNotificationSuccess: function (product) {
			if (product.currentQuantity === 0) {
				BX.onCustomEvent('B2BNotification',[
					BX.message('BZI_PRODUCT_NAME') + ': ' + product.name + "<br>" +
					BX.message('BZI_PRODUCT_REMOVE_FROM_BASKET'),
					'success'
				]);
			} else {
				BX.onCustomEvent('B2BNotification',[
					BX.message('BZI_PRODUCT_NAME') + ': ' + product.name + "<br>" +
					BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + product.currentQuantity + " " + product.measureName,
					'success'
				]);
			}
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
				if (!this.arResult['ITEM_IDS']['OFFERS']?.hasOwnProperty(item.id)) {return}

				const offer = this.arResult['ITEM']['OFFERS'][item.id];
				const ranges = offer['ITEM_QUANTITY_RANGES'];
				let currentRange = offer['ITEM_QUANTITY_RANGE_SELECTED'];

				for (let range in ranges) {
					if (item.count >= (ranges[range].QUANTITY_FROM === "" ? Number.NEGATIVE_INFINITY : ranges[range].QUANTITY_FROM)
						&& item.count <= (ranges[range].QUANTITY_TO === "" ? Number.POSITIVE_INFINITY :  ranges[range].QUANTITY_TO)
					) {
						currentRange = range;
					}
				}

				for (let price in this.arResult['ITEM_IDS']['OFFERS'][this.offerNum]['PRICES']) {
					let node = document.getElementById(this.arResult['ITEM_IDS']['OFFERS'][this.offerNum]['PRICES'][price]);

					if (node && price !== 'PRIVATE_PRICE') {
						let currentPrice = this.arResult['ITEM']['OFFERS'][item.id]['PRINT_PRICES'][price];
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