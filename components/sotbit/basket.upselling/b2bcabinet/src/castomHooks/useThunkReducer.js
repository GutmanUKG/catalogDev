import React from 'react';


export const useThunkReducer = (rootRedus, initState, immutableState) => {
    const [state, dispatch] = React.useReducer(rootRedus, initState)
    const thunk = action => {
        if (typeof action === 'function') {
            return action(dispatch, () => state, immutableState);
        }
        dispatch(action);
    }
    return [state, thunk, immutableState];
}