import React from 'react'

export const useQuatityViewer = (oneValue, twoValue) => {
    const [result, setResult] = React.useState(oneValue);

    React.useEffect(() => {
        setResult(oneValue);
    }, [oneValue])

    React.useEffect(() => {
        setResult(twoValue);
    }, [twoValue])

    return [result, setResult];
}