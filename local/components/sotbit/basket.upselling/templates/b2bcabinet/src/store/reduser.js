import {ActionType} from './actions'

export const reduser = (state, action) => {
    switch (action.type) {
        case ActionType.TOGGLE_VISIBLE_FILTER:
            return {...state, showFilter: !state.showFilter};

        case ActionType.GET_PRODUCTS:
            return {
                ...state,
                CURRENT_PAGE: action.pyload.CURRENT_PAGE,
                NUMBER_OF_PAGES: action.pyload.NUMBER_OF_PAGES,
                PORODUCTS: action.pyload.PORODUCTS,
            };

        case ActionType.ADD_PRODUCTS:
            return {
                ...state,
                CURRENT_PAGE: action.pyload.CURRENT_PAGE,
                NUMBER_OF_PAGES: action.pyload.NUMBER_OF_PAGES,
                PORODUCTS: [...state.PORODUCTS, ...action.pyload.PORODUCTS],
            }

        case ActionType.ADD_SECION_NAME:
            return {
                ...state,
                selectedSectionsNames: [
                    ...state.selectedSectionsNames,
                    action.pyload,
                ]};

        case ActionType.REMOVE_SECION_NAME:
            return {
                ...state,
                selectedSectionsNames: state.selectedSectionsNames
                    .filter(i => i !== action.pyload)
            };

        case ActionType.SET_PRODUCT_NAME:
            return {
                ...state,
                productName: action.pyload,
            };

        case ActionType.SHOW_SPINER:
            return {...state, spiner: action.pyload};

        default:
            return state;
    }
}