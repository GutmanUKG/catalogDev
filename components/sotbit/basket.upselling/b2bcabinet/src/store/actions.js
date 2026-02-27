import {Api} from '../bitrix_api';
import BX from 'BX';

export const ActionType = {
    GET_PRODUCTS: 'GET_PRODUCTS',
    ADD_SECION_NAME: 'ADD_SECION_NAME',
    REMOVE_SECION_NAME: 'REMOVE_SECION_NAME',
    SET_PRODUCT_NAME: 'SET_PRODUCT_NAME',
    ADD_PRODUCTS: 'ADD_PRODUCTS',
    TOGGLE_VISIBLE_FILTER: 'TOGGLE_VISIBLE_FILTER',
    SHOW_SPINER: 'SHOW_SPINER',

    createShowSpiner: (show) => ({
        type: ActionType.SHOW_SPINER,
        pyload: show,
    }),

    createToggleVisibleFilter: () => ({ type: ActionType.TOGGLE_VISIBLE_FILTER }),

    creataeSetProductName: name => (
        { type: ActionType.SET_PRODUCT_NAME, pyload: name }
    ),

    createRemoveSectionName: section_name => (
        { type: ActionType.REMOVE_SECION_NAME, pyload: section_name }
    ),

    createAddSectionName: section_name => (
        { type: ActionType.ADD_SECION_NAME, pyload: section_name }
    ),

    createGetProducts: ({CURRENT_PAGE, PORODUCTS, NUMBER_OF_PAGES}) => ({
        type: ActionType.GET_PRODUCTS,
        pyload: {CURRENT_PAGE, PORODUCTS ,NUMBER_OF_PAGES},
    }),

    createAddProducts: ({CURRENT_PAGE, PORODUCTS, NUMBER_OF_PAGES}) => ({
        type: ActionType.ADD_PRODUCTS,
        pyload: {CURRENT_PAGE, PORODUCTS ,NUMBER_OF_PAGES},
    }),

    createSetFromBasketFlag: flag => ({
        type: ActionType.SET_FROM_BASKET_FLAG,
        pyload: {flag},
    }),

    thuncSyncWithBasket: arProds => (dispatch ,getState) => {
        const PORODUCTS = getState().PORODUCTS.map(prod => {
            const currentQuantity = arProds.find(i => i.PRODUCT_ID === prod.b_catalog_productID);
            prod.quantityInBasket = currentQuantity ? currentQuantity.QUANTITY : 0;
            prod.idInBasket = currentQuantity
                ? currentQuantity.id
                : prod.idInBasket ?? null ;
            return prod;
        });
        const CURRENT_PAGE = getState().CURRENT_PAGE;
        const NUMBER_OF_PAGES = getState().NUMBER_OF_PAGES;

        dispatch(ActionType.createGetProducts({CURRENT_PAGE, PORODUCTS, NUMBER_OF_PAGES}));
    },

    thuncNexPage: CURRENT_PAGE => async (dispatch, getState, immutableState) => {
        const state = getState();
        const selectedSectionsId = immutableState.SECTIONS
            .filter(i => state.selectedSectionsNames.includes(i.NAME))
            .map(i => i.ID);

        if (!BX.Sotbit.Upselling.waitNextPage) {
            BX.Sotbit.Upselling.waitNextPage = true;

            dispatch(ActionType.createShowSpiner(true));

            try {
                const {PORODUCTS, NUMBER_OF_PAGES} = await Api.getProdeucts({
                    arParams: immutableState,
                    sections: selectedSectionsId.length !== 0 ? selectedSectionsId : [0],
                    productName: state.productName,
                    CURRENT_PAGE
                });
                dispatch(ActionType.createAddProducts({CURRENT_PAGE, PORODUCTS, NUMBER_OF_PAGES}));
                Api.basketRecalc();
                BX.Sotbit.Upselling.waitNextPage = false;
            } finally {
                dispatch(ActionType.createShowSpiner(false));
            }
        }
    },

    thuncApplyFilter: () => async (dispatch, getState, immutableState) => {
        const state = getState();
        const selectedSectionsId = immutableState.SECTIONS
            .filter(i => state.selectedSectionsNames.includes(i.NAME))
            .map(i => i.ID);

        dispatch(ActionType.createShowSpiner(true));

        const {PORODUCTS, NUMBER_OF_PAGES} = await Api.getProdeucts({
            arParams: immutableState,
            sections: selectedSectionsId.length !== 0 ? selectedSectionsId : [0],
            productName: state.productName,
            CURRENT_PAGE: 1,
        });
        dispatch(ActionType.createShowSpiner(false));
        dispatch(ActionType.createGetProducts({CURRENT_PAGE: 1, PORODUCTS, NUMBER_OF_PAGES}));
        Api.basketRecalc();
    },

    thuncApplyEmptyFilter: () => async (dispatch, getState, immutableState) => {
        const state = getState();
        dispatch(ActionType.creataeSetProductName(''));
        state.selectedSectionsNames.forEach(i => {
            dispatch(ActionType.createRemoveSectionName(i));
        });

        dispatch(ActionType.createShowSpiner(true));
        const {PORODUCTS, NUMBER_OF_PAGES} = await Api.getProdeucts({
            arParams: immutableState,
            sections: [0],
            productName: '',
            CURRENT_PAGE: 1,
        });
        dispatch(ActionType.createGetProducts({CURRENT_PAGE: 1, PORODUCTS, NUMBER_OF_PAGES}));
        Api.basketRecalc();
        dispatch(ActionType.createShowSpiner(false));
    },

    thuncAddBasket: (productId, quantity, setQuantityView, productProps, inBasketid) => async () => {
        if (quantity === 0 && inBasketid) {
            return Api.removeItemFromBasket(inBasketid);
        }

        const result = Api.restoreItemFromBasket(inBasketid);
        if (result) {
            setTimeout(async () => await Api.addBasket(productId, quantity, setQuantityView, productProps), 500)
            return;
        }

        await Api.addBasket(productId, quantity, setQuantityView, productProps);
    }
}
