import React from 'react';

export const Spiner = () => {
    return <div style={{
        background: 'rgba(0, 0, 0, 0)',
        border: 'medium none',
        color: 'black',
        fontFamily: 'Verdana, Arial, sans-serif',
        fontSize: '11px',
        padding: '0px',
        position: 'fixed',
        top: '40%',
        right: '28%',
        zIndex: 10000,
        textAlign: 'center',
    }} className="pace-demo">
        <div className="theme_xbox">
            <div className="pace_activity">
            </div>
        </div>
    </div>
}