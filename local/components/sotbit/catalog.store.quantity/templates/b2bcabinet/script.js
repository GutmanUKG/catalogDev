(function (window){
    'use strict';

    if (window.JCSotbitStoreAmount)
        return;

	window.JCSotbitStoreAmount = function (params) {
		this.obName = params.obName;
		this.infoSelector = params.infoSelector;
		this.html = params.html;
		this.node = document.getElementById(this.obName);
		this.popup = null;
	}

	window.JCSotbitStoreAmount.prototype = {
		init: function() {
			let info = this.node.querySelector(this.infoSelector);

			info?.addEventListener('mouseover', (event) => {
				console.log(event.target.getBoundingClientRect().top)
				if (this.popup === null) {
					this.popup = document.createElement('div');
					this.popup.className = "item-quantity__store-list__wrap";
					this.popup.innerHTML = this.html;
					this.popup.setAttribute(
						'style',
						`top: ${event.target.getBoundingClientRect().top - 20}px; left: ${event.target.getBoundingClientRect().left + 30}px`,
					);
					document.body.append(this.popup);
				} else {
					this.popup.style.display = 'block';
					this.popup.setAttribute(
						'style',
						`top: ${event.target.getBoundingClientRect().top - 20}px; left: ${event.target.getBoundingClientRect().left + 30}px`,
					);
				}
			});

			info?.addEventListener('mouseout', (event) => {
					if (this.popup !== null) {
						this.popup.style.display = 'none';
					}
			});
		},
	}
})(window)