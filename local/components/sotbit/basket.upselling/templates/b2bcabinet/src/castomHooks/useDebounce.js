import React from 'react';


export const useDebounce = (value, delay, step, max) => {
    const [debValues, setDebValue] = React.useState(value);
    const preValue = value * 1000;
    const preStep = step * 1000;
    const remainder = preValue % preStep;

    let validValue;

    if (value >= Number(max)) {
        validValue = max;
    } else if (value <= 0) {
        validValue = 0;
    } else {
        validValue = Math.round(preValue - remainder) / 1000;
    }

    React.useEffect(() => {
        const handler = setTimeout(() => {
            setDebValue(validValue);
        }, delay);
        return () => clearTimeout(handler);

    }, [value]);

    return debValues;
}