import React from 'react';
import {Context} from '../store/state';
import {ActionType} from '../store/actions';
import {LangMessage} from '../bitrix_api';
import {CatalogItem} from './catalogItem';
import {useSyncWithBasket} from '../castomHooks/useSyncWithBasket';
import {Helper} from '../helper';
import {Tooltibp} from './tooltibp';

export const Catalog = () => {
    const {state, dispatch} = React.useContext(Context);
    const list = state.PORODUCTS;
    const listDom = React.useRef(null);
    const [toolTip, setToolTip] = React.useState({
        text: '', top: 0, right: 0, width: 0,
    });
    useSyncWithBasket(
        (...param) => dispatch(ActionType.thuncSyncWithBasket(param)),
        [list],
    );

    React.useEffect(() => {
        const onScrolPagination = e => {
            Helper.loadeAfterSroll({
                currentScroll: e.target.scrollTop,
                maxScroll: e.target.scrollHeight - e.target.clientHeight,
                currentPage: state.CURRENT_PAGE,
                maxPage: state.NUMBER_OF_PAGES,
            }, next_page => dispatch(ActionType.thuncNexPage(next_page)))
        };

        if (listDom.current instanceof HTMLElement) {
            listDom.current.addEventListener('scroll', onScrolPagination)
        }

        return () => {
            if (listDom.current instanceof HTMLElement) {
                listDom.current.removeEventListener('scroll', onScrolPagination)
            }
        }
    }, [listDom, state.CURRENT_PAGE, state.NUMBER_OF_PAGES]);

    React.useEffect(() => {
        let handler;
        if (listDom.current instanceof HTMLElement && state.NUMBER_OF_PAGES > 1) {
            handler = setTimeout(() => {
                if (listDom.current.scrollHeight === listDom.current.clientHeight
                    && state.CURRENT_PAGE < state.NUMBER_OF_PAGES
                ) {
                    dispatch(ActionType.thuncNexPage(state.CURRENT_PAGE + 1))
                }
            }, 700);
        }

        return () => clearTimeout(handler);

    }, [state.showFilter, state.CURRENT_PAGE, state.NUMBER_OF_PAGES])

    return <div className="upselling__catalog-list">
        <ul className="catalog-list">
            <li className="catalog-list__header">
                <div className="catalog-list__column catalog-list__column__size-10"></div>
                <div className="catalog-list__column catalog-list__column__size-all">
                    {LangMessage.CATALOG_PRODUCT_NAME}
                </div>
                <div className="catalog-list__column catalog-list__column__size-22">
                    {LangMessage.CATALOG_QUANTITY}
                </div>
                <div className="catalog-list__column catalog-list__column__size-18">
                    {LangMessage.CATALOG_PRICE}
                </div>
            </li>
            <div ref={listDom}
                className="catalog-list__body">
                {list.length > 0 ? list.map(prod => <CatalogItem key={prod.b_catalog_productID}
                    prod={prod}
                    sendProductToBasket={(id, quantity, props, inBasketId) =>
                        dispatch(ActionType.thuncAddBasket(id, quantity, props, inBasketId))}
                />) : <div className="text-muted nothing_to_show">{LangMessage.NO_PRODUCTS_MATCHING_THE_SEARCH_CRITERIA}</div>}
            </div>
        </ul>
    </div>
}