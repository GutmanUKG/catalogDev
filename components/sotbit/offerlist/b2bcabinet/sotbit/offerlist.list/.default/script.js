function SotbitOfferlist(offer, select, params) {
    this.offer = offer;
    this.params = params;
    this.offerId = offer.ID;
    this.cardNode = document.querySelector('[data-offerlist-id="'+this.offerId+'"]');
    this.products = offer.PRODUCTS;
    this.conditionText = offer.DESCRIPTION;
    this.select = select;
    this.editorCardIinit = {};

    if (!this.cardNode) {
        return;
    }
    SweetAlert.initComponents();
    BX.loadExt('sidepanel');
    this.initEvents();
}

SotbitOfferlist.prototype.initEvents = function () {
    document.body.append(document.querySelector(this.select.conditionTextModal));

    if (this.select.condition) {
        this.cardNode.querySelector(this.select.condition)?.addEventListener('click', this.showCondition.bind(this));
    }

    if (this.offer.ERRORS) {
        this.cardNode.querySelector(this.select.addBasket)?.addEventListener('click', this.showErrorNotavailable);
        this.cardNode.querySelector(this.select.download)?.addEventListener('click', this.showErrorNotavailable);
        return;
    }

    if (this.select.addBasket) {
        this.cardNode.querySelector(this.select.addBasket)?.addEventListener('click', this.addToBasketAction.bind(this));
    }

    if (this.select.download) {
        this.cardNode.querySelector(this.select.download)?.addEventListener('click', this.downloadAction.bind(this));
    }
}

SotbitOfferlist.prototype.showCondition = function () {
    document.querySelector(this.select.conditionTextModal + ' .modal-body').innerHTML = this.conditionText;
    document.querySelector('[data-bs-target="' + this.select.conditionTextModal + '"]').click();
}

SotbitOfferlist.prototype.showErrorNotavailable = function () {
    SweetAlert.showInfo(BX.message('SO_ACTION_NOTAVAILABLE_1'), BX.message('SO_ACTION_NOTAVAILABLE_2'));
}

SotbitOfferlist.prototype.addToBasketAction = function () {
    BX.showWait();
    BX.ajax.runComponentAction('sotbit:offerlist.list', 'addToBasket', {
        mode: 'class',
        data: {
            arProduct: this.products
        }
    }).then(response  => {
            BX.closeWait();
            BX.onCustomEvent('OnBasketChange');
            if (response.data.isSuccess === true) {
                SweetAlert.showSuccess(response.data.message);
            } else {
                let errorText = '';
                response.data.forEach((error) => errorText += error + "\n");
                SweetAlert.showError(errorText);
            }
        },
        error => {
            BX.closeWait();
            SweetAlert.showError(error);
    });
}

SotbitOfferlist.prototype.downloadAction = function () {
    if (window.innerWidth <= 576) {
        BX.showWait();

        $.get(this.offer.DETAIL_PAGE_URL + '&IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER')
            .done((result)=>{
                const reOfferList = new RegExp("<!-- Offerlist document -->(.*?)<!-- \/offerlist document -->", "s");
                const htmlOfferList = result.match(reOfferList)[1];
                
                if (htmlOfferList) {
                    BX.ajax.runComponentAction('sotbit:offerlist.editor', 'saveDocument', {
                        mode: 'class',
                        data: {
                            html: window.btoa(unescape(encodeURIComponent(htmlOfferList))),
                            offerId: this.offerId
                        }
                    }).then(response  => {
                            BX.closeWait();

                            if (response.data == true) {
                                setTimeout(() => {window.open(this.offer.DETAIL_PAGE_URL + '&OFFER_PRINT=Y&DOWNLOAD=Y')}, 0);
                            }
                        },
                        error => {
                            BX.closeWait();
                            SweetAlert.showError(error);
                        });
                } else {
                    BX.closeWait();
                    console.error('Error download offerlist');
                }
            })
            .fail((error)=>{
                BX.closeWait();
                SweetAlert.showError(error);
            });
    } else {
        BX.SidePanel.Instance.open(
            this.offer.DETAIL_PAGE_URL,
            {
            width: 1344,
                label: {
                    text: "",
                    color: "#9E9E9E",
                    bgColor: "transparent",
                    opacity: 80
                }
            }
        );
    }
}