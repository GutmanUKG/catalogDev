$(document).ready(function() {
    let trShowHiddenBlock = document.querySelectorAll(".index_blank .index_blank-table .table .offer-footer"),
        btnShowMore = document.querySelectorAll(".offer-show-more-content .offer-show-more-content__btn"),
        propBlock = document.querySelectorAll(".offer-properties-item-inner"),
        propItem = [];

    for (let i = 0; i < trShowHiddenBlock.length; i++) {
        trShowHiddenBlock[i].addEventListener("click", function () {
            let tBodyParent = getParent(this, 'index_blank-table-tbody');

            if (this.classList.contains('active')) {

                this.classList.remove('active');

                if (tBodyParent.querySelector('.offer-properties-hidden')) {
                    tBodyParent.querySelector('.offer-properties-hidden').style.display = 'none';
                }

            } else {
                this.classList.add('active');
            }
        });
    }

    for (let k = 0; k < btnShowMore.length; k++) {
        btnShowMore[k].addEventListener("click", function () {
            let tBodyParent = getParent(this, 'index_blank-table-tbody');

            this.classList.toggle('opened');

            for (let s = 0; s < tBodyParent.children.length; s++) {

                if (tBodyParent.children[s].classList.contains("offer-properties-hidden")) {
                    if (window.getComputedStyle(tBodyParent.children[s]).getPropertyValue("display") === "none") {
                        tBodyParent.children[s].style.display = "table-row";
                    } else {
                        tBodyParent.children[s].style.display = "none";
                    }
                }

            }
        });
    }

    for (let n = 0; n < propBlock.length; n++) {
        propItem[n] = propBlock[n].querySelectorAll(".offer-properties-item-inner__item");

        for (let q = 0; q < propItem[n].length; q++) {
            propItem[n][q].addEventListener("click", function() {
                this.classList.toggle('active');
            });
        }
    }

    $(".hover").mouseleave(
        function () {
            $(this).removeClass("hover");
        }
    );

    itemsSort();
});

function showModal(html) {
    document.body.style.overflow = 'hidden';
    var block = '<div class="wrap-popup-window">' +
        '<div class="modal-popup-bg" onclick="closeModal();">&nbsp;</div>' +
        '<div class="popup-window">' +
        '<div class="popup-close" onclick="closeModal();"></div>' +
        '<div class="popup-content">';
    block = block + html;
    block = block + '</div>'
    '</div>' +
    '</div>';
    $("body").append(block);
}

function closeModal() {
    document.body.style.overflow = 'auto';
	BX.onCustomEvent('OnBasketChange');
	$('.wrap-popup-window').last().remove();
}

function quickView(url) {
	let add = '&preview=Y';
	let location = window.location.href;
	if(location.indexOf('clear_cache=Y') !== false)
	{
		add+='&clear_cache=Y';
	}
	url += add;

	$.ajax({
		url: url,
		type: 'POST',
		data:{'sessid': BX.bitrix_sessid()},
		beforeSend: function(){
			BX.showWait();
		},
		success: function(html){
            showModal(html);
		},
		complete: function(){
			BX.closeWait();
		},
	});

}

function itemsSort() {
    const btnApply = Array.prototype.slice.call(document.querySelectorAll('.offer-properties-item-inner__item'));
    const btnReset = Array.prototype.slice.call(document.querySelectorAll('.offer-properties-item-btnBlock__btn[data-action="reset-sort"]'));
    btnApply.forEach(function (item) {
        item.addEventListener('click', function () {
            const parentBlock = getParent(this, 'index_blank-table-tbody');
            const groupPropsParent =  getParent(this, 'offer-properties-item-inner');
            if (groupPropsParent.querySelector('.active')) {
                groupPropsParent.classList.add('checked');
            } else {
                groupPropsParent.classList.remove('checked');
            }
            const countParams = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-properties-item-inner.checked')).length;
            const offerItems = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-item'));
            const propsOfferItem = offerItems.map(function (item) {
                return Array.prototype.slice.call(item.querySelectorAll('[data-propvalue]'));
            });
            const propsOfferValue = propsOfferItem.map(function (item) {
                return item.map(function (element) {
                    return _defineProperty({}, element.dataset.propname, element.dataset.propvalue);
                });
            });
            const activeProps = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-properties-item-inner'));
            const activePropsItem = activeProps.map(function (item) {
                return Array.prototype.slice.call(item.querySelectorAll('.active [data-value]'));
            });
            const activePropsValue = activePropsItem.map(function (item) {
                return item.map(function (element) {
                    return _defineProperty({}, element.dataset.name, element.dataset.value);
                });
            });

            function _defineProperty(obj, key, value) {
                if (key in obj) {
                    Object.defineProperty(obj, key,
                        {value: value, enumerable: true, configurable: true, writable: true});
                } else {
                    obj[key] = value;
                }
                return obj;
            }

            propsOfferValue.forEach(function (item, index) {
                let conformity = [];
                item.forEach(function (element) {

                    for (let key in element) {

                        activePropsValue.forEach(function (props) {

                            props.forEach(function (prop) {
                                if (element[key] === prop[key]) {
                                    conformity.push(index)
                                }
                            });
                        });
                    }
                });
                if (countParams === conformity.length) {
                    offerItems[index].classList.remove('hide');
                } else {
                    offerItems[index].classList.add('hide');
                }
            });
        });

    });
    btnReset.forEach(function (item) {
        item.addEventListener('click', function () {
            const parentBlock = getParent(this, 'index_blank-table-tbody');
            const activeProps = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-properties-item-inner__item.active'));
            const offerItems = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-item'));
            const groupPropsParent = Array.prototype.slice.call(parentBlock.querySelectorAll('.offer-properties-item-inner.checked'));
            offerItems.forEach(function (item) {
                item.classList.remove('hide');
            });
            activeProps.forEach(function (item) {
                item.classList.remove('active');
            });
            groupPropsParent.forEach(function (item) {
                item.classList.remove('checked');
            });
        })
    });
}

function getParent (item, className) {
    let parentItem = item;
    while (!parentItem.classList.contains(className) && (parentItem !== document.body)) {
        parentItem = parentItem.parentElement;
    }
    if(parentItem !== document.body) {
        return parentItem;
    }
}


$(document).ready(function () {
    var touchspin_up = document.querySelectorAll(".bootstrap-touchspin-up"),
        touchspin_down = document.querySelectorAll(".bootstrap-touchspin-down");
    const addToCartBtn = document.querySelector(".btn.add_to_cart");
    for (var i = 0; i < touchspin_up.length; i++) {
        if ('ontouchstart' in window) {
            touchspin_up[i].addEventListener("touchstart", function () {
                spinCount(this);
            });
            touchspin_down[i].addEventListener("touchstart", function () {
                spinCount(this);
            });
        } else {
            touchspin_up[i].addEventListener("click", function () {
                spinCount(this);
            });
            touchspin_down[i].addEventListener("click", function () {
                spinCount(this);
            });
        }
    }

    function spinCount(element) {
        var dataObj = $(element).offsetParent().find('.form-control.touchspin-empty');


        if (dataObj.length > 0) {
            var productId = $(dataObj).data('id');
            var maxQnt = $(dataObj).data('maxqnt');
            var productQnt = $(dataObj).val();
            var mainBlock = $(element).parentsUntil('tbody').filter('tr');
            var arProps = $(mainBlock).children('td.js-product-property');
            var arPrices = $(mainBlock).children('td.js-price');
            var productProps = {};
            var productPrices = {};
            $.each(arProps, function (key, value) {
                var tmpProps = {};
                tmpProps['CODE'] = $(value).data('propcode');
                tmpProps['VALUE'] = $(value).html();
                tmpProps['NAME'] = $(value).data('propname');
                productProps[key] = tmpProps;
            });
            $.each(arPrices, function (key, value) {
                var tmpPrice = {};

                if ($(value).html() !== '') {
                    tmpPrice['VALUE'] = $(value).data('price_value');
                    tmpPrice['NAME'] = $(value).data('price_name');
                    tmpPrice['CODE'] = $(value).data('price_code');
                    tmpPrice['CURRENCY'] = $(value).html().replace(/[\d\s]+/g, '');
                    productPrices[key] = tmpPrice;
                }
            });
            $.ajax({
                type: 'POST',
                url: site_path + 'include/ajax/blank_ids.php',
                data: {
                    'id': productId,
                    'qnt': productQnt,
                    'props': productProps,
                    'prices': productPrices,
                    'baseCurrency': baseCurrency,
                    'maxQnt': maxQnt
                },
                success: function success(data) {
                    var items = JSON.parse(data);
                    addToCartBtn.disabled = items.length === 0 ? true : false;

                    if (items) {
                        var totalCount = 0;

                        if (items['TOTAL_COUNT']) {
                            totalCount = items['TOTAL_COUNT'];
                        }

                        $('.index_blank-add_cart-number').html(totalCount);
                        var totalPrice = 0;

                        if (items['TOTAL_PRICE']) {
                            totalPrice = items['TOTAL_PRICE'];
                        }

                        $('.index_blank-add_cart-total').html(totalPrice);
                    }
                }
            });
        }
    }

    var spin = $('.form-control.touchspin-empty');
    $.each(spin, function (key, val) {
        val.addEventListener('input', function () {
            if (!!this.timer) {
                clearTimeout(this.timer);
            }

            this.timer = setTimeout(spinCount(this), 700);
        });
    });
});
