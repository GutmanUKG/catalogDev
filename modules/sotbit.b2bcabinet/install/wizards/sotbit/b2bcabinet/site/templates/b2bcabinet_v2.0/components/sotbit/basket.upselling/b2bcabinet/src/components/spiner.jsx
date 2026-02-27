import React from 'react';

export const Spiner = () => {
    return <div style={{
        width: '100%',
        border: 'medium none',
        color: 'black',
        fontFamily: 'Verdana, Arial, sans-serif',
        fontSize: '11px',
        padding: '0px',
        position: 'absolute',
        bottom: '50%',
        left: '0',
        zIndex: 10000,
        textAlign: 'center',
    }} className="pace-demo">
        <div className="theme_xbox">
            <div className="pace_activity">
            </div>
        </div>
    </div>
}