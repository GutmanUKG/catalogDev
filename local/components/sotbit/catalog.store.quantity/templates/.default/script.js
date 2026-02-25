(function (window){
    'use strict';

    if (window.JCSotbitStoreAmount)
        return;

	window.JCSotbitStoreAmount = function (params) {
		this.params = params
		this.stores = {}
		this.signedParameters = params.signedParameters
		this.result = params.arResult
		for (let store in params.itemIds.STORES) {
			this.stores[store] = BX(params.itemIds.STORES[store])
		}
	}

	window.JCSotbitStoreAmount.prototype = {
		init: function() {
			BX.addCustomEvent('updateItemsStoreData', BX.proxy(this.update, this))
			// for (let store in this.stores) {
			// 	BX.bind(this.stores[store], 'click', BX.proxy(function() {this.showStoreDetails(store)}, this))
			// }
		},
		update: function() {
			// BX.ajax.runComponentAction('sotbit:catalog.store.quantity','updateStoreData', {
			// 	mode: 'class',
			// 	signedParameters: this.signedParameters,
			// }).then(result => {
			// 	let stores = result.data.STORE_AMOUNT;
			// 	for (let store in stores) {
			// 		this.stores[store].querySelector('[data-store-selector="quantity"]').innerText = stores[store]
			// 	}
			// }, e => console.log(e))
		},
		showStoreDetails: function(storeId) {
			// let context = this
			// BX.loadExt('sidepanel').then(() => {
			// 	BX.SidePanel.Instance.open("sotbit:store.detail", { 
			// 		contentCallback: function(slider) {
			// 			return new Promise(function(resolve, reject) {
			// 				const store = context.result.STORES[storeId]
			// 				console.log(store)
			// 				let result = []
			// 				for (const property in store) {
			// 					result.push(`${store[property].TITLE}: ${store[property].VALUE}`)
			// 				}
							
			// 				resolve(`
			// 					<div style="padding: 16px">
			// 						<img src="${store.IMAGE}" width="100%">
			// 						<div>${result.join('<br>')}</div>
			// 						<iframe 
			// 							src="https://yandex.ru/map-widget/v1/?ll=${context.result.STORES[storeId]['GPS_S']}%2C${context.result.STORES[storeId]['GPS_N']}&z=16&pt=${context.result.STORES[storeId]['GPS_S']}%2C${context.result.STORES[storeId]['GPS_N']}"
			// 							width="100%" 
			// 							height="300" 
			// 							frameborder="0" 
			// 							allowfullscreen="false"
			// 							scroll="false"
			// 							>
			// 						</iframe>
			// 					</div>
			// 				`);
			// 			});
			// 		},
			// 		width: 400,
			// 		title: context.result.STORES[storeId]['TITLE'],
			// 		label: {
			// 			text: context.result.STORES[storeId]['TITLE'],
			// 			color: '#fff',
			// 			bgColor: '#252b38',
			// 		}
			// 	});
			// })

		}
	}
})(window)