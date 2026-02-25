import React from 'react';

export const Tooltibp = ({text, top, right, width}) => {
    const tooltipDomElement = React.useRef(null);
    const [visible, setVisible] = React.useState(false);
    const timeout = text !== '' ? 700 : 0;

    React.useEffect(() => {
        const timer = setTimeout(() => {
            setVisible(() => tooltipDomElement.current.offsetWidth > width && text !== '');
        }, timeout);

        return () => clearTimeout(timer);

    }, [text]);

    return <div style={{
        display: 'block',
        position: 'fixed',
        top: top ? top + 35 : '65%',
        right: right ? right + 100: '38.8%',
        padding: '10px',
        borderRadius: '4px',
        backgroundColor: `rgba(42, 49, 64, ${visible ? 0.90: 0})`,
        zIndex: visible ? 500 : -100,
        color: '#fff',
        transform: 'translate(0px, 0px)',
        transition: '0.3s',
    }}
        ref={tooltipDomElement}
    >{text}</div>
}