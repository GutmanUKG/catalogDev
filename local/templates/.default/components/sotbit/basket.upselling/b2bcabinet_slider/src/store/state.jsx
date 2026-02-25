import React from 'react';
import {useThunkReducer} from '../castomHooks/useThunkReducer';
import {reduser} from './reduser';




export const Context = React.createContext();

export const AppContext = ({children, arResult, arParams}) => {
    const [state, dispatch, immutableState] = useThunkReducer(
        reduser,
        {
            ...arResult,
            selectedSectionsNames: [],
            productName: '',
            fromBasket: false,
            spiner: false,
            showFilter: true,
        },
        arParams,
    )
    return <Context.Provider value={{ state, dispatch, immutableState }}>
            {children}
        </Context.Provider>
}
