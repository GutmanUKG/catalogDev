function OfferlistPriceListAdd(params) {
    SweetAlert.initComponents();
    this.params = params;

    if (this.params.AJAX_CALL === true && !this.params.ORDER) {
        this.initGetComponent();
    } else {
        new ProductsPriceLIst(this.params.ORDER);
        this.initFormAction();
    }
}

OfferlistPriceListAdd.prototype = {
    initGetComponent: function () {
        this.btnGetComponent = document.getElementById(this.params.BUTTON_GET_COMPONENT);
        document.body.append(document.getElementById(this.params.RESULT_BLOCK));
        
        if (!this.btnGetComponent) {
            return;
        }

        this.btnGetComponent.addEventListener('click', () => {
            BX.showWait();

            BX.ajax.runComponentAction('sotbit:offerlist.pricelist.add', 'getComponentAjax', {
                mode: 'class',
                signedParameters: this.params.signedParameters,
                data: {
                        sotbit_set_site_template: this.params.templateSigns
                    }
            }).then(response  => {
                    BX.closeWait();
                    document.getElementById(this.params.RESULT_BLOCK).innerHTML = response.data.html;
                    BX.ajax.processScripts(BX.processHTML(response.data.html, false).SCRIPT);
                    document.querySelector('[data-bs-target="#'+this.params.MODAL+'"]').click();
                },
                error => {
                    BX.closeWait();
                    if (error.errors[0].code === 'no_item') {
                        SweetAlert.showError(error.errors[0].message);
                    } else {
                        SweetAlert.showError(error.errors.map((item) => item.message).join('\n'));
                    }
                });
        });
    },
    initFormAction: function () {
        this.formAdd = document.getElementById(this.params.FORM_ADD);
        this.formAdd.addEventListener('submit' , (event) => {
            event.preventDefault();
            var formData = new FormData(event.target);


            BX.showWait();
            BX.ajax.runComponentAction('sotbit:offerlist.pricelist.add', 'addPriceList', {
                mode: 'class',
                signedParameters: this.params.signedParameters,
                data: formData
            }).then(response  => {
                    BX.closeWait();
                    if (response.data.detail_page_url) {
                        window.location.assign(response.data.detail_page_url)
                    }
                },
                error => {
                    BX.closeWait();
                    SweetAlert.showError(error.data);
                });
        });
    }
}

function ProductsPriceLIst(params) {
    this.actualProduct = {};
    this.dataProduct = {};
    this.productRowElement = {};

    this.totalSumWrap = document.querySelector('[data-entity="total-sum"]');
    this.inputAllMargin = document.querySelector('[data-entity="margin-all"]');
    this.buttonToggleSignMargin = document.querySelector('[data-entity="toggle-margin-all"]')
    this.currency = params.ORDER_FIELDS.CURRENCY
    BX.Currency.loadCurrencyFormat(this.currency);
    params.BASKET_ITEMS.forEach(product => this.initProductAction(product));
    this.initAllMarginProducts();
}

ProductsPriceLIst.prototype = {
    initProductAction: function (product) {
        this.initActualProduct(product);
        this.saveProductData(product);
        const productRow = document.querySelector(`[data-entity="product"][data-id="${product.PRODUCT_ID}"]`);
        this.productRowElement[product.PRODUCT_ID] = {
            'productId': product.PRODUCT_ID,
            'quantity': productRow.querySelector('[data-entity="quantity"]'),
            'price': productRow.querySelector('[data-entity="price"]'),
            'total': productRow.querySelector('[data-entity="total"]'),
            'margin': productRow.querySelector('[data-entity="margin"]'),
            'toggleMargin': productRow.querySelector('[data-entity="toggle-margin"]'),
        };

        this.productRowElement[product.PRODUCT_ID].quantity.addEventListener('input', (event) => this.changeQuantity(product.PRODUCT_ID, event.target.value));
        this.productRowElement[product.PRODUCT_ID].price.addEventListener('input', (event) => this.changePrice(product.PRODUCT_ID, event.target.value));
        this.productRowElement[product.PRODUCT_ID].margin.addEventListener('input', (event) => {

            this.setSignNumberForGreateOrLassNull(this.actualProduct[product.PRODUCT_ID].MARGIN, event.target.value, this.productRowElement[product.PRODUCT_ID].toggleMargin);
            this.changeMargin(product.PRODUCT_ID, event.target.value);
        });
        this.productRowElement[product.PRODUCT_ID].toggleMargin.addEventListener('click', () => this.toggleMargin(product.PRODUCT_ID, this.productRowElement[product.PRODUCT_ID].toggleMargin));
    },
    initAllMarginProducts: function () {
        this.inputAllMargin.addEventListener('input', (event) => {
            this.setSignNumberForGreateOrLassNull(event.target.dataset.prevValue, event.target.value, this.buttonToggleSignMargin);

            for (let product of Object.values(this.productRowElement)) {
                this.setSignNumberForGreateOrLassNull(this.actualProduct[product.productId].MARGIN, event.target.value, product.toggleMargin);
                this.changeMargin(product.productId, event.target.value);
            }

            event.target.dataset.prevValue = event.target.value;
        });

        this.buttonToggleSignMargin.addEventListener('click', () => {
            let numberSign = this.buttonToggleSignMargin.dataset.numberSign === 'minus' ? 
                                'plus' :
                                'minus';
            this.inputAllMargin.value = -this.inputAllMargin.value;
            this.changeSignNumberMargin(this.buttonToggleSignMargin, numberSign);
            for (let product of Object.values(this.productRowElement)) {
                this.changeSignNumberMargin(product.toggleMargin, numberSign)
                this.changeMargin(product.productId, this.inputAllMargin.value);
            }
        })
    },
    changeMargin: function (productId, value) {        
        this.actualProduct[productId].MARGIN = value;
        this.actualProduct[productId].PRICE = value/100 * this.dataProduct[productId].PRICE + this.dataProduct[productId].PRICE;
        this.changeProductRow(productId);
    },
    changeQuantity: function (productId, value) {

        value = Number(value);
        this.actualProduct[productId].QUANTITY = value;
        this.changeProductRow(productId);
    },
    changePrice: function (productId, value) {
        value = Number(value.replace(/\s+/g, ''));
        if (!value && value !== 0) { return;}
        this.actualProduct[productId].PRICE = value;
        this.actualProduct[productId].MARGIN = (this.actualProduct[productId].PRICE - this.dataProduct[productId].PRICE) / this.dataProduct[productId].PRICE * 100;
        this.changeProductRow(productId);
    },
    changeProductRow: function (productId) {
        this.actualProduct[productId].TOTAL = this.actualProduct[productId].QUANTITY * this.actualProduct[productId].PRICE;
        this.productRowElement[productId].quantity.value = this.actualProduct[productId].QUANTITY;
        this.productRowElement[productId].margin.value = Math.round(parseFloat(this.actualProduct[productId].MARGIN || 0) * 100) / 100;
        this.productRowElement[productId].price.value = this.htmlEntityDecode(BX.Currency.currencyFormat(this.actualProduct[productId].PRICE, this.actualProduct[productId].CURRENCY, false));
        this.productRowElement[productId].total.innerHTML = BX.Currency.currencyFormat(this.actualProduct[productId].TOTAL, this.actualProduct[productId].CURRENCY, true);

        let totalSum = 0;
        for (let product in this.actualProduct) {
            totalSum += this.actualProduct[product].TOTAL;
        }

        this.totalSumWrap.innerHTML = BX.Currency.currencyFormat(totalSum, this.currency, true);
    },
    htmlEntityDecode: function (str) {
        let element = document.createElement('div');
        let result = '';
        element.innerHTML = str;
        result = element.textContent;
        element.textContent = '';

        return result;
    },
    toggleMargin: function (productId, element) {
        let numberSign = element.dataset.numberSign === 'minus' ? 
                        'plus' :
                        'minus';
        this.changeSignNumberMargin(element, numberSign)
        this.changeMargin(productId, -this.actualProduct[productId].MARGIN);
    },
    changeSignNumberMargin: function (element, sign) {
        element.dataset.numberSign = sign;
        element.firstElementChild.classList = `ph-${sign}`;
    },
    setSignNumberForGreateOrLassNull: function (prevValue, currentValue, elementToggleMargin) {
        if (prevValue <= 0 && currentValue > 0 ) {
            this.changeSignNumberMargin(elementToggleMargin, 'plus');
        } else if(prevValue >= 0 && currentValue < 0) {
            this.changeSignNumberMargin(elementToggleMargin, 'minus');
        }
    },
    saveProductData: function (product) {
        this.dataProduct[product.PRODUCT_ID] = {
            'PRICE': Number(product.PRICE.replace(/\s+/g, '')),
        }
    },
    initActualProduct: function (product) {
        this.actualProduct[product.PRODUCT_ID] = {
            'QUANTITY': product.QUANTITY,
            'PRICE': Number(product.PRICE.replace(/\s+/g, '')),
            'TOTAL': Number(product.TOTAL),
            'CURRENCY': product.CURRENCY,
            'MARGIN': 0,
        };
    }
}