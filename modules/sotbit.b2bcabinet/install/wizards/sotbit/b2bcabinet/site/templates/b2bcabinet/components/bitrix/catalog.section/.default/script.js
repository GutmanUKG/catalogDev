;(function(window){
    'use strict';

	if (window.JCBlankZakaza)
		return;

	window.JCBlankZakaza = function (arItems, arParams, itemIds)
	{
        this.arParams = arParams;
        this.arItems = arItems;
        this.itemIds = itemIds;
        this.node = null;
        this.wrapper = null;
        BX.ready(BX.delegate(this.init,this));

    };
    window.JCBlankZakaza.prototype = {
        init: function() {
            this.initOffers();
            for (let offer in this.offers) {

                this.initQuantity(this.getOfferQuantityPropsById(offer))
            }
        },
        initOffers: function() {
            if (!Array.isArray(this.arItems)) {return}
            this.offers = {};
            const offers = this.arItems;
            offers.forEach(function (offer) {
                if (!offer.ID) {console.log(offer);return;} 
                const offerIds = this.itemIds.ITEMS[offer.ID];
                this.offers[offer.ID] = {
                    id: offer.ID,
                    name: offer.NAME,
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
                    measureName: offer.CATALOG_MEASURE_NAME,
                    quantityTrace: offer.CATALOG_QUANTITY_TRACE === "Y" ? true : false,
                    canBuyZero: offer.CATALOG_CAN_BUY_ZERO === "Y" ? true : false
                }
            }.bind(this))

        },
        initQuantity: function(item) {
            if (item.nodes.wrapper === null) {return}
            const nodes = item.nodes;
            window.addEventListener("message", function(event) {
                if (typeof event.data == "string") {
                    var data = JSON.parse(event.data);
                } else {
                    var data = event.data;
                }
                if (data.itemId && data.quantity && data.itemId === item.itemId) {
                    item.currnetQuantity = parseFloat(data.quantity);
                    item.tmpQuantity = item.currnetQuantity;
                    nodes.value.value = item.currnetQuantity;
                }
            })

            nodes.increment.addEventListener('click', function(event) {

                item.currnetQuantity = item.tmpQuantity;
                item.tmpQuantity = parseFloat((item.tmpQuantity + item.measureRatio).toFixed(3));

                if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(item.tmpQuantity)) {

                    nodes.value.value = item.tmpQuantity;
                    this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

                    clearTimeout(item.delayAddToBasket);
                    item.delayAddToBasket = setTimeout(
                        function(){
                            nodes.increment.setAttribute("disabled", "disabled");
                            this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
                                .then(function(response) {
                                        if (BX.SidePanel) {
                                            BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
                                                id: item.itemId, quantity: item.tmpQuantity,
                                            });
                                        } else {
                                            BX.onCustomEvent('OnBasketChange');
                                        }
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
                                        if (item.currnetQuantity === 0) {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
                                                'success'
                                            ]);
                                        } else {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
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
                                        if (BX.SidePanel) {
                                            BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
                                                id: item.itemId, quantity: item.tmpQuantity,
                                            });
                                        } else {
                                            BX.onCustomEvent('OnBasketChange');
                                        }
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

                                        if (item.currnetQuantity === 0) {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
                                                'success'
                                            ]);
                                        } else {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZI_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZI_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
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

                item.tmpQuantity =event.target.value;
                if (item.tmpQuantity <= item.maxQuantity && item.tmpQuantity >= item.minQuantity && !isNaN(parseFloat(item.tmpQuantity))) {

                    nodes.value.value = item.tmpQuantity;
                    this.redrawPrices({id: item.itemId, count: item.tmpQuantity});

                    clearTimeout(item.delayAddToBasket);
                    item.delayAddToBasket = setTimeout(
                        function(){
                            this.addToBasket(item.itemId, item.tmpQuantity, item.measureRatio)
                                .then(function(response) {
                                        if (BX.SidePanel) {
                                            BX.SidePanel.Instance.postMessageTop(window, "addProductToBasketFromDetail", {
                                                id: item.itemId, quantity: item.tmpQuantity,
                                            });
                                        } else {
                                            BX.onCustomEvent('OnBasketChange');
                                        }
                                        item.currnetQuantity = parseFloat(response.data);
                                        item.tmpQuantity = item.currnetQuantity;
                                        nodes.value.value = item.currnetQuantity;
                                        if (window.self !== window.top) {
                                            window.top.postMessage(JSON.stringify({
                                                itemId: item.itemId,
                                                quantity: item.currnetQuantity
                                            }),"*")
                                        }
                                        if (item.currnetQuantity === 0) {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZD_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZD_PRODUCT_REMOVE_FORM_BASKET'),
                                                'success'
                                            ]);
                                        } else {
                                            BX.onCustomEvent('B2BNotification',[
                                                BX.message('BZD_PRODUCT_NAME') + ': ' + item.name + "<br>" +
                                                BX.message('BZD_PRODUCT_ADD_TO_BASKET') + " " + item.currnetQuantity + " " + item.measureName,
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
                    event.target.value = item.currnetQuantity;
                    item.tmpQuantity = item.currnetQuantity
                }
            }.bind(this))
        },
        deleteRepaetNotification: function () {
            if(document.querySelectorAll('.b2b-notifications__item').length > 1) {
                document.querySelector('.b2b-notifications__item').remove();
            }
        },
        getOfferQuantityPropsById: function(id) {
            const offers = this.arItems
            if (!Array.isArray(offers)) {
                console.error('JCBlankZakazaItem: Can not find offers in product id=' + this.itemId);
                return {
                    offerId: id,
                    nodes: null,
                    currnetQuantity: null,
                    tmpQuantity: null,
                    maxQuantity: null,
                    minQuantity: null
                }
            }
            const offer = this.offers[id];

            const offerIds = this.itemIds['ITEMS'][id];

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
                if (!this.itemIds['ITEMS'].hasOwnProperty(item.id)) {return}
                let tmpOfferId = 0;
                const offer = this.arItems.filter(function(element, iterator){
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
                for (let price in this.itemIds['ITEMS'][offer.ID]['PRICES']) {
                    let node = document.getElementById(this.itemIds['ITEMS'][offer.ID]['PRICES'][price]);
                    if (node && price !== 'PRIVATE_PRICE') {
                        let currentPrice = this.arItems[tmpOfferId]['PRINT_PRICES'][price];
                        if (currentPrice.hasOwnProperty(currentRange)) {
                            node.innerHTML = currentPrice[currentRange]['PRINT'];
                        } else {
                            node.innerHTML = '';
                        }
                    }
                }
            }
        },
    };

    var OrderingrKeys = {
        'NAME': 'NAME',
        'AVALIABLE': 'QUANTITY',
        'MEASURE': 'MEASURE',
    };
})(window);