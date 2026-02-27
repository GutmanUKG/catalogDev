import React from 'react';
import {Helper} from '../helper';
import {useDebounce} from '../castomHooks/useDebounce';
import {useQuatityViewer} from '../castomHooks/useQuatityViewer';


export const CatalogItem = ({prod, sendProductToBasket, setLastTooltip}) => {

    const [quantity, setQuantity] = React.useState(0);
    const [quantityView, setQuantityView] = useQuatityViewer(prod.quantityInBasket, quantity);
    const sendQuantity = useDebounce(quantity, 600, prod.ratio, ((prod.b_catalog_productAVAILABLE === "Y" && Number(prod.b_catalog_productQUANTITY) === 0) || prod.b_catalog_productQUANTITY_TRACE === 'N') ? Infinity : prod.b_catalog_productQUANTITY);
    const [sendQuery, setSendQuery] = React.useState(false);
    const productName = React.useRef(null);

    React.useEffect(() => {

        if (sendQuery && !isNaN(Number(sendQuantity))) {
           sendProductToBasket(
                prod.b_catalog_productID,
                sendQuantity,
                setQuantityView,
                prod.IN_BASKET,
                prod.idInBasket,
            );
        }
    }, [sendQuantity]);

    React.useEffect(() => {
        if (quantity !== prod.quantityInBasket) {
            if (prod.quantityInBasket !== undefined) {
                setQuantity(prod.quantityInBasket);
            }
        }
    }, [prod.quantityInBasket])

    return <li
        key={prod.b_catalog_productID}
        className="catalog-list__item"
        onMouseMove={() => setSendQuery(true)}>

    <div className="catalog-list__column catalog-list__column__size-all">
        <a className="img-responsive-wriper" href="#"><img
            className="img-responsive"
            src={prod.PREVIEW_PICTURE || '/local/templates/b2bcabinet_v2.0/assets/images/no_photo.svg'}
            width="74"
            height="auto"
        /></a>
        <div className="catalog-list__description">
            <span className="catalog-list__name"
                  title={prod.NAME}
                ref={productName}
                onClick={() => {
                    const http = window.location.protocol;
                    const host = window.location.host;
                    window.open(`${http}//${host}${prod.DETAIL_PAGE_URL}`)
                }}>
                {prod.NAME}
            </span>
            <span className="catalog-list__property">
                {Array.isArray(prod.LIST_PAGE_SHOW)
                    ? prod.LIST_PAGE_SHOW.map(i => <span key={i.NAME + i.VALUE} className='small-text'>{`${i.NAME}: ${i.VALUE}`}</span>)
                    : null}
            </span>
            <span className="catalog-list__property">
                {Array.isArray(prod.OFFER_TREE)
                    ? prod.OFFER_TREE.map(i => <span key={i.NAME + i.VALUE} className='small-text'>{i.NAME}: {i.TYPE === 'img'
                        ? <img src={i.VALUE} alt={i.VALUE} />
                        : <span>{i.VALUE} </span>}</span>)
                    : null}
            </span>
        </div>
    </div>
        <div className="catalog-list__column catalog-list__column__size-22">
            <div className="bootstrap-touchspin input-group">
                <span className="input-group-btn input-group-prepend">
                        <button type="button"
                                className="btn bootstrap-touchspin-down"
                                onClick={() => setQuantity(() =>
                                    Helper.calcQuantity(quantityView, ((prod.b_catalog_productAVAILABLE === "Y" && Number(prod.b_catalog_productQUANTITY) === 0) || prod.b_catalog_productQUANTITY_TRACE === 'N') ? Infinity : prod.b_catalog_productQUANTITY, -prod.ratio)
                                )}
                                >
                                <i className="ph-minus"></i>
                        </button>
                </span>
                <input
                    type="text"
                    className="touchspin-basic form-control fs-xs"
                    value={quantityView ?? 0}
                    onChange={e => setQuantity(() => {
                        if (!isNaN(Number(e.target.value)) && Number(e.target.value) > prod.b_catalog_productQUANTITY) {
                            return e.target.value;
                        }

                        return !isNaN(Number(e.target.value)) ? e.target.value : quantity;
                    })}
                />
                <span className="input-group-btn input-group-append">
                    <button type="button"
                            className="btn bootstrap-touchspin-up"
                            onClick={() => setQuantity(() =>
                                Helper.calcQuantity(quantityView,
                                    ((prod.b_catalog_productAVAILABLE === "Y" && Number(prod.b_catalog_productQUANTITY) === 0) || prod.b_catalog_productQUANTITY_TRACE === 'N') ? Infinity : prod.b_catalog_productQUANTITY,
                                    prod.ratio
                                ),
                            )}
                            >
                            <i className="ph-plus"></i>
                    </button>
                </span>
            </div>
        </div>
        <div
            className="catalog-list__column catalog-list__column__size-18 catalog-list__font-white-space-nowrap">
            <div dangerouslySetInnerHTML={{ __html: prod.DISPLAY_PRICE }}></div>
            {prod.DISPLAY_PRICE_WHITHOUT_DISCOND
                ? <div className='price_whithout_discond'
                        dangerouslySetInnerHTML={{ __html: prod.DISPLAY_PRICE_WHITHOUT_DISCOND }}>
                    </div>
                : null}
        </div>
</li>
}