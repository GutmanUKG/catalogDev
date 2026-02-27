;BX.ready(function() {
    BX.addCustomEvent('SidePanel.Slider:onMessage', (data) => {
        if (data.eventId === 'addItemBasket') {
            BX.onCustomEvent('OnBasketChange');
        }
    });

    BX.addCustomEvent('OnBasketChange', function() {
        BX.ajax.runAction('sotbit:b2bcabinet.basket.getBasketSmallState',{})
        .then(
            function(data) {
                let quantityNode = document.getElementById('catalog__basket-quantity-value');
                let priceNode = document.getElementById('catalog__basket-price-value');
                if (quantityNode && priceNode) {
                    quantityNode.innerHTML = data.data.quantity;
                    priceNode.innerHTML = data.data.print_price;
                }
            },
            function(error) {
                console.error(error);
            }
        )
    });
    BX.onCustomEvent('OnBasketChange');
    document.querySelector('body').style = "overflow: auto;"
});