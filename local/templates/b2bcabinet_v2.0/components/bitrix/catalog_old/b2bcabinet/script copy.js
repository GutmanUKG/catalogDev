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
        $.ajax({
            url: '/ajax/panel-info-update.php',
            method: 'get',
            dataType: 'json',
            success: function(data){
                if(typeof data.WEIGHT === "undefined" || typeof data.VOLUME === "undefined" || typeof data.QUANTITY === "undefined") {
                    $('.catalog__footer-info').css('display', 'none');
                }
                else {
                    $('#catalog__footer-info-weight').html(data.WEIGHT);
                    $('#catalog__footer-info-volume').html(data.VOLUME);
                    $('#catalog__footer-info-quantity').html(data.QUANTITY);
                    $('.catalog__footer-info').css('display', 'block');

                    if(data.WEIGHT < 23 && data.VOLUME < 0.15) {
                        $('#ico-boxman').css('display', 'block');
                    }
                    else {
                        $('#ico-boxman').css('display', 'none');
                    }

                    if(data.WEIGHT >= 23 && data.VOLUME >= 0.15 && data.VOLUME < 0.5) {
                        $('#ico-auto-passenger').css('display', 'block');
                    }
                    else {
                        $('#ico-auto-passenger').css('display', 'none');
                    }


                    if(data.WEIGHT >= 23 && data.VOLUME >= 0.5) {
                        $('#ico-auto-cargo-sm').css('display', 'block');
                    }
                    else {
                        $('#ico-auto-cargo-sm').css('display', 'none');
                    }
                }
                console.log(data);
            }
        });
    });
    BX.onCustomEvent('OnBasketChange');
    document.querySelector('body').style = "overflow: auto;"
});