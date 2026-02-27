(function (window){
    'use strict';

    if (window.JCSotbitStoreAmount)
        return;

	window.JCSotbitStoreAmount = function (params) {
		this.obName = params.obName;
		this.html = params.html;
		this.node = document.getElementById(this.obName);
		this.popup = null;
	}

	window.JCSotbitStoreAmount.prototype = {
		init: function() {
			let info = this.node;

			if (info) {
				this.setEventAvaliable(info);
			}
			document.querySelector('.content-inner')?.addEventListener('scroll', this.closeAllPopupHandler);
		},

		setEventAvaliable: function(node) {
			if (window.innerWidth > 576) {
				node.onmouseover = this.showPopupHandler.bind(this);

				node.onmouseout = () => {
					if (this.popup !== null) {
						this.popup.classList.remove('show');
					}
				}
			} else {
				node.onclick = this.showPopupHandler.bind(this);
			}
		},

		showPopupHandler: function() {
			if (event.type === 'click') {
				this.closeAllPopupHandler();
			}

			if (this.popup === null) {
				this.popup = document.createElement('div');
				this.popup.className = "item-quantity__store-list__wrap";
				this.popup.innerHTML = this.html;
				
				document.body.append(this.popup);
				this.setPositionPopup();
			} else {
				this.setPositionPopup();
			}
			this.popup.classList.add('show');
		},

		closeAllPopupHandler: function() {
			const allPopups = document.querySelectorAll('.item-quantity__store-list__wrap');
			
			if (allPopups.length !== 0) {
				allPopups.forEach((item) => {
					item.classList.remove('show');
				})
			}
		},

		setPositionPopup: function() {
			this.widthPopup =  this.widthPopup || this.popup.offsetWidth;
			if (window.innerWidth > 576) {
				this.popup.setAttribute(
					'style',
					`top: ${event.target.getBoundingClientRect().top - 20}px; left: ${event.target.getBoundingClientRect().right + 30}px`,
				);
			} else {
				this.popup.setAttribute(
					'style',
					`top: ${event.target.getBoundingClientRect().top - 78}px; left: ${event.target.getBoundingClientRect().left - this.widthPopup / 2.5}px`,
				);
			}
		}
	}
})(window)