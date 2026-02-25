import BX from 'BX';

export const LangMessage = BX.message;

BX.namespace('BX.Sotbit.Upselling')

BX.Sotbit.Upselling = {
    waitNextPage: false
}

export const Api = {
    query(action, data) {
        return BX.ajax.runComponentAction(
            'sotbit:basket.upselling',
            action,
            {
                mode: 'class',
                data: data,
            }
        )
    },

    async getProdeucts({arParams, sections, productName, CURRENT_PAGE}) {
        if(sections.length == 0) {
            sections[0] = 0
        }
        const result = await this.query('getProdeucts', {arParams, sections, productName, CURRENT_PAGE});
        return result.data;
    },

    async addBasket(productId, quantity, productProps) {
        const sendQuantity = quantity === 0 ? 1 : quantity;
        BX.showWait();
        let result;
        try {
            result = await BX.ajax.runAction('sotbit:b2bcabinet.basket.addProductToBasket', {
                data: {
                    arFields: {
                        'PRODUCT_ID': productId,
                        'QUANTITY': sendQuantity,
                        'PROPS': productProps,
                    }
                },
            })
            this.basketRecalc();
        } catch (error) {
            let errors = [];
            for (var i = 0; i<error.errors.length; i++) {
                errors.push(error.errors[i].message);
            }

            BX.onCustomEvent('B2BNotification',[
                errors.join('<br>'),
                'alert'
            ]);
        } finally {
            BX.closeWait();
        }

        return result;
    },

    removeItemFromBasket(id) {
        BX.Sale.BasketComponent.actionPool.deleteItem(id);
    },

    restoreItemFromBasket(id) {
        const item = document.getElementById(`basket-item-height-aligner-${id}`);
        const reestoreButtton = item ? item.querySelector('a[data-entity="basket-item-restore-button"]') : null;
        if (reestoreButtton) {
            reestoreButtton.click()
            return true
        }
        return false;
    },

    basketRecalc() {
        BX.Sale.BasketComponent.sendRequest('recalculateAjax', {fullRecalculation: 'Y'});
    }
}

