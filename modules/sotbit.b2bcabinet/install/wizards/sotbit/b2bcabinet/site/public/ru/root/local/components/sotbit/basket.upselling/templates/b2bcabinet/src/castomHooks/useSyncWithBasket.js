import React from 'react';
import BX from 'BX';


export const useSyncWithBasket = (func, dependencies) => {
    React.useEffect(() => {
        BX.addCustomEvent(BX.Sale.BasketComponent, 'updateUpselingComponent', func);

        return () => BX.removeCustomEvent(BX.Sale.BasketComponent, 'updateUpselingComponent', func);
    }, dependencies)
}