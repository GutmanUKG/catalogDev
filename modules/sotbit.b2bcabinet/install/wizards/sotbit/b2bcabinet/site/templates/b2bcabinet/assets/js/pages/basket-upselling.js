"use strict";

window.addEventListener("load", function() {

    const basket = BX.Sale.BasketComponent;

    const basket_page_footer = document.getElementById('basket-page__footer');

    const checkout_btn = basket.getEntity(basket_page_footer, 'page-basket-checkout-button');

    checkout_btn.addEventListener('click', function() {
        BX.Sale.BasketComponent.checkOutAction();
    });

    /**
     * @param {HTMLElement} node
     * @param {string[]} searchId
     * @param {string} stopID
     * @returns {boolean}
     */
    function upTheDOMTree(node, searchId, stopID) {
        if (node.id == stopID) {
            return false
        }
        for (const i of searchId) {
            if (node.id === i) {
                return true
            }
        }
        return upTheDOMTree(node.parentNode, searchId, stopID);
    }

    const page_conteiner = document.querySelector('.basket-page');
    const panel = document.getElementById('bx-panel-userinfo');
    const panel_show = document.getElementById('bx-panel-site-toolbar');

    const admin_panel_colaps = 'basket-page-admin';
    const admin_panel_show = 'basket-page-admin-panel';

    if (panel_show instanceof HTMLElement && panel_show.offsetWidth > 0) {
        page_conteiner.classList.add(admin_panel_show)
    } else if (panel instanceof HTMLElement) {+
        page_conteiner.classList.add(admin_panel_colaps)
    }

    BX.addCustomEvent('onTopPanelCollapse', BX.delegate(function(data){
        if (data) {
            page_conteiner.classList.add(admin_panel_colaps)
            page_conteiner.classList.remove(admin_panel_show)
        } else {
            page_conteiner.classList.add(admin_panel_show)
            page_conteiner.classList.remove(admin_panel_colaps)
        }
     }, this));


    const basket_wrapper = document.querySelector('.basket-upselling__basket');
    basket_wrapper.querySelector('div').style = 'height: 100%;';
  });
