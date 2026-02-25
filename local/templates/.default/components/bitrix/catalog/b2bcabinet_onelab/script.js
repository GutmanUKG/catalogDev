BX.ready(function() {
    BX.addCustomEvent('SidePanel.Slider:onMessage', (data) => {
        if (data.eventId === 'addItemBasket') {

            BX.onCustomEvent('OnBasketChange');
        }
    });

    BX.addCustomEvent('OnBasketChange', function() {
        BX.ajax.runAction('sotbit:b2bcabinet.basket.getBasketSmallState', {})
        .then(
            function(data) {
                let quantityNode = document.getElementById('catalog__basket-quantity-value');
                let priceNode = document.getElementById('catalog__basket-price-value');

                if (quantityNode && priceNode) {
                    quantity = data.data.quantity; // Записываем в глобальную переменную
                    price = data.data.print_price; // Записываем в глобальную переменную

                    quantityNode.innerHTML = quantity;
                    priceNode.innerHTML = price;
                }
            },
            function(error) {
                console.error(error);
            }
        );

        $.ajax({
            url: '/ajax/panel-info-update.php',
            method: 'get',
            dataType: 'json',
            success: function(data) {
                if (typeof data.WEIGHT === "undefined" || typeof data.VOLUME === "undefined" || typeof data.QUANTITY === "undefined") {
                    $('.catalog__footer-info').hide();
                } else {
                    $('#catalog__footer-info-weight').html(data.WEIGHT);
                    $('#catalog__footer-info-volume').html(data.VOLUME);
                    $('#catalog__footer-info-quantity').html(data.QUANTITY);
                    $('.catalog__footer-info').show();

                    function calculatePercentage(volume, divisor) {
                        let percentage = (volume / divisor) * 100;
                        return percentage < 10 ? percentage.toFixed(2) + "%" : Math.round(percentage) + "%";
                    }

                    let truckSmallPercent = calculatePercentage(data.VOLUME, 9);
                    let truckMediumPercent = calculatePercentage(data.VOLUME, 25);
                    let truckBigPercent = calculatePercentage(data.VOLUME, 90);

                    let truckSmallValue = (data.VOLUME / 9).toFixed(2);
                    let truckMediumValue = (data.VOLUME / 25).toFixed(2);
                    let truckBigValue = (data.VOLUME / 90).toFixed(2);

                    if (parseFloat(truckSmallPercent) > 100 && parseFloat(truckMediumPercent) <= 100) {
                        $('.truck-container.trucks').hide();
                        $('.truck-container.truckm').show();
                        $('.truck-container.truckb').hide();
                    } else if (parseFloat(truckSmallPercent) > 100 && parseFloat(truckMediumPercent) > 100) {
                        $('.truck-container.trucks').hide();
                        $('.truck-container.truckm').hide();
                        $('.truck-container.truckb').show();
                    } else if (parseFloat(truckSmallPercent) <= 100 && data.VOLUME > 0.5 ) {
                        $('.truck-container.trucks').show();
                        $('.truck-container.truckm').hide();
                        $('.truck-container.truckb').hide();
                    }
                    else  {
                        $('.truck-container.trucks').hide();
                        $('.truck-container.truckm').hide();
                        $('.truck-container.truckb').hide();
                    }

                    $('#truck-big-number').text(truckBigPercent);
                    $('#truck-medium-number').text(truckMediumPercent);
                    $('#truck-small-number').text(truckSmallPercent);
                  
                    let imageHtml = '';
                    
                    if (data.WEIGHT < 23 && data.VOLUME < 0.15) {
                        imageHtml = `<img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-10.png" id="ico-boxman" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Вес и объем заказа позволяют осуществить доставку заказа вручную.">`;
                    } else if ((data.WEIGHT >= 23 || data.VOLUME >= 0.15) && data.VOLUME < 0.5) {
                        imageHtml = `<img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-09.png" id="ico-auto-passenger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Заказ поместится в багажник легкового автомобиля.">`;
                    } else if (parseFloat(truckSmallPercent) <= 100 && data.VOLUME > 0.5 ) {
                        imageHtml = `<img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-08.png" class="truck-s" id="ico-auto-cargo-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Доставка возможна малотоннажным грузовиком.">`;
                    } else if (parseFloat(truckSmallPercent) > 100 && parseFloat(truckMediumPercent) <= 100) {
                        imageHtml = `<img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-07.png" class="truck-m" id="ico-auto-cargo-m" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Необходим среднетоннажный грузовик.">`;
                    } else if (parseFloat(truckSmallPercent) > 100 && parseFloat(truckMediumPercent) > 100) {
                        imageHtml = `<img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-06.png" class="truck-b" id="ico-auto-cargo-big" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Для доставки требуется еврофура.">`;
                    }

                    // let quantity = document.getElementById('catalog__basket-quantity-value').textContent;
                    // let price = document.getElementById('catalog__basket-price-value').textContent;

                    function isMobile() {
                        return window.innerWidth <= 768; 
                    }

                    console.log(isMobile());
                    
                    if (isMobile() || (data.VOLUME > 0.5 && !isMobile())) {
                        let truckInfoTooltip = `
                       
                        <div class="info-mobil">
                            <!-- Первая строка -->
                            <div class="catalog__footer-row">
                                <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Вес">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-01.png" " width="55px;">
                                    <span id="catalog__footer-info-weight">${data.WEIGHT}</span>
                                    <span class="catalog__footer-info-unit">кг</span>
                                </div>
                                <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Объём">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-03.png"  width="55px">
                                    <span id="catalog__footer-info-volume">${data.VOLUME}</span>
                                    <span class="catalog__footer-info-unit">м<sup>3</sup></span>
                                </div>
                            </div>

                            <!-- Вторая строка -->
                            <div class="catalog__footer-row">
                                <div class="catalog__footer-info-item" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Общее количество коробок">
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-04.png"  width="55px">
                                    <span id="catalog__footer-info-quantity">${data.QUANTITY}</span>
                                    <span class="catalog__footer-info-unit">шт</span>
                                </div>
                                <div class="catalog__footer-info-items" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-black" data-bs-placement="top" title="Общее количество коробок">
                                   ${imageHtml}
                                </div>
                            </div>
                        </div>
                        
        
                            <div class="linecar">
                                <div >
                                    ГАЗель 1.5т 9м³
                                    <span>${truckSmallValue} / 100%</span>
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-08.png" width="70px">
                                </div>
                                <div>
                                    Грузовик 5т 25м³
                                    <span>${truckMediumValue} / 100%</span>
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-07.png" width="70px">
                                </div>
                                <div >
                                    Еврофура 20т 90м³
                                    <span>${truckBigValue} / 100%</span>
                                    <img src="/local/templates/b2bcabinet_v2.0/assets/images/PIC-06.png" width="70px">
                                </div>
                            </div>

                            <div class="info-quant-price info-mobil">
                                <div class="catalog__basket-quantity">
                                    <span class="catalog__basket-quantity-title">Число позиций</span>
                                    <span class="catalog__basket-quantity-value" id="catalog__basket-quantity-value">${quantity}</span>
                                </div>
                                <div class="catalog__basket-price">
                                    <span class="catalog__basket-price-title">На сумму</span>
                                    <span class="catalog__basket-price-value" id="catalog__basket-price-value">${price}</span>
                                </div>
                            </div>

                        `;
                    
                        // Для desktop
                        $('#info-ico-default, .info-ico-mob').each(function () {
                            $(this).attr('data-bs-html', 'true')
                                .attr('title', truckInfoTooltip)
                                .attr('data-bs-original-title', truckInfoTooltip)
                                .attr('data-bs-custom-class', 'tooltip-black');
                    
                            let tooltipInstance = bootstrap.Tooltip.getInstance(this);
                            if (tooltipInstance) {
                                tooltipInstance.dispose();
                            }
                            new bootstrap.Tooltip(this);
                        });
                    
                    } else {
                        // Удаляем всплывающую подсказку при VOLUME <= 0.5
                        $('#info-ico-default, .info-ico-mob').each(function () {
                            let tooltipInstance = bootstrap.Tooltip.getInstance(this);
                            if (tooltipInstance) {
                                tooltipInstance.dispose();
                            }
                    
                            $(this)
                                .removeAttr('title')
                                .removeAttr('data-bs-original-title')
                                .removeAttr('data-bs-html')
                                .removeAttr('data-bs-custom-class');
                        });
                    }


                    if (data.WEIGHT < 23 && data.VOLUME < 0.15) {
                        $('#ico-boxman').show();
                        $('#info-ico-boxman').show();
                        $('#ico-auto-passenger').hide();
                        $('#info-ico-car').hide();
                        $('#info-ico-default').hide();
                    } else if ((data.WEIGHT >= 23 || data.VOLUME >= 0.15) && data.VOLUME < 0.5) {
                        $('#ico-boxman').hide();
                        $('#ico-auto-passenger').show();
                        $('#info-ico-car').show();
                        $('#info-ico-boxman').hide();
                        $('#info-ico-default').hide();
                    } else{
                        $('#ico-boxman').hide();
                        $('#ico-auto-passenger').hide();
                        $('#info-ico-boxman').hide();
                        $('#info-ico-car').hide();
                        $('#info-ico-default').show();
                    }
                    

                }
                
            }
        });
    });

    // Скрываем все иконки доставки до получения данных
    document.getElementById('ico-boxman')?.style.setProperty('display', 'none');
    document.getElementById('ico-auto-passenger')?.style.setProperty('display', 'none');
    document.getElementById('info-ico-boxman')?.style.setProperty('display', 'none');
    document.getElementById('info-ico-car')?.style.setProperty('display', 'none');
    

    // Скрываем блоки грузовиков до расчёта
    document.querySelector('.truck-container.trucks')?.style.setProperty('display', 'none');
    document.querySelector('.truck-container.truckm')?.style.setProperty('display', 'none');
    document.querySelector('.truck-container.truckb')?.style.setProperty('display', 'none');

    // Инициализация tooltip по умолчанию (если иконка отображена при загрузке)
let infoIcoDefault = document.getElementById('info-ico-default');
if (infoIcoDefault && infoIcoDefault.hasAttribute('title')) {
    new bootstrap.Tooltip(infoIcoDefault);
}

    BX.onCustomEvent('OnBasketChange');
    document.querySelector('body').style = "overflow: auto;";
});


document.addEventListener('DOMContentLoaded', ()=>{
    let title = document.querySelector('.page-title'),
        parentTitle = title.parentNode,
        elCount = document.querySelector('.dropdown-el-count'),
        count = document.querySelector('.count');

    parentTitle.appendChild(elCount);
    parentTitle.appendChild(count);
    count.style.marginLeft = 'auto';

})

