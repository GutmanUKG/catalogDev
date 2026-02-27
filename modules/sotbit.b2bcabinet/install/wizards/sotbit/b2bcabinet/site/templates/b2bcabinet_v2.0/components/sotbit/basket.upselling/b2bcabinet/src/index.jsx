import React from "react";
import {render} from "react-dom";
import {AppContext} from './store/state';
import {Filter} from './components/filter';
import {Catalog} from './components/catalog';

const App = ({arResult, arParams}) => {
    return <AppContext arResult={arResult} arParams={arParams}>
        <main className="upselling">
            <Filter />
            <Catalog />
        </main>
    </AppContext>
}

window.onload = () => {
    const rootElement = document.getElementById('basket_upselling_templates');
    const arResult = JSON.parse(rootElement.getAttribute('data-arResult'));
    const arParams = JSON.parse(rootElement.getAttribute('data-arParams'));
    render(<App arResult={arResult} arParams={arParams} />, rootElement)
}
