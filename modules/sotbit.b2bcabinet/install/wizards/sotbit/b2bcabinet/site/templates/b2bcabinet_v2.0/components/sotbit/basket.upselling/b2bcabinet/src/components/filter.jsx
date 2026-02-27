import React, { useEffect } from 'react';
import cn from 'classnames';
import {LangMessage} from '../bitrix_api';
import {Context} from '../store/state';
import {ActionType} from '../store/actions';
import {SectionsSelect} from './sectionsSelect';
import {Spiner} from './spiner';

export const Filter = () => {
    const {dispatch, state, immutableState} = React.useContext(Context);
    const [sectionsName, setSectionName] = React.useState('');
    const [sectionsSelectVisible, setSectionsSelectVisible] = React.useState(false);

    useEffect(() => {
            if (typeof arguments.isFirst === 'undefined')
                arguments.isFirst = true;

            if (!state.selectedSectionsNames.length && arguments.isFirst) 
                return;

            dispatch(ActionType.thuncApplyFilter());
            arguments.isFirst = false;
        }, 
        [state.selectedSectionsNames]
    );

    return <div className={cn('upselling__filter', {'upselling__filter__hidden': !state.showFilter})}>
        {state.spiner ? <Spiner /> : null}
        <div className="filter">
            <header className="filter__header">
                <h5 className="filter_title">{LangMessage.FILTER_TITLE}</h5>
                <svg
                    className={cn('filter__roll-up', {'filter__roll-down': !state.showFilter})} width="10" height="6" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg"
                    onClick={() => dispatch(ActionType.createToggleVisibleFilter())}>
                    <path d="M7.26273 8.34035C7.02473 8.34035 6.78676 8.24948 6.60531 8.06811L0.895346 2.35809C0.532118 1.99486 0.532118 1.40595 0.895346 1.04286C1.25843 0.679783 1.84722 0.679783 2.21048 1.04286L7.26273 6.09541L12.315 1.04304C12.6782 0.67996 13.267 0.67996 13.63 1.04304C13.9934 1.40612 13.9934 1.99503 13.63 2.35826L7.92015 8.06829C7.73861 8.24968 7.50064 8.34035 7.26273 8.34035Z" fill="#38576E"/>
                </svg>
            </header>
            <div className={cn('filter__body', {'filter__body-hide': !state.showFilter})}>
                <div className="filter__body-wrapper">
                    <div className="filter__input-group">
                        <label className="form-label">{LangMessage.FILTER_SECTION}</label>
                        <label className='filter-selected-multiple'>
                            <div className="filter__selected-section">
                                {state.selectedSectionsNames
                                    .map(i => <div
                                            className="filter__selected-item"
                                            key={i}
                                            onClick={() => dispatch(ActionType.createRemoveSectionName(i))}>
                                        {i} <i className="ph-x fs-base align-middle"></i>
                                    </div>)}
                                <input
                                    type="text"
                                    className="filter__input"
                                    onFocus={() => setSectionsSelectVisible(true)}
                                    onBlur={() => {setSectionsSelectVisible(false)}}
                                    value={sectionsName}
                                    onChange={e => {e.target.style.width = (e.target.value.length + 1) * .75 + 'em';setSectionName(e.target.value)}}
                                />
                            </div>
                            <SectionsSelect
                                sections={immutableState.SECTIONS}
                                secitonName={sectionsName}
                                visble={sectionsSelectVisible}
                                selectedSection={state.selectedSectionsNames}
                                setSectionName={name => dispatch(ActionType.createAddSectionName(name))}
                                clearSectionMame={() => {setSectionName('')}}
                            />
                        </label>
                    </div>
                    <div className="filter__input-group">
                        <label className="form-label">{LangMessage.FILTER_SEARCH_PLACEHOLDER}</label>
                        <div className='form-control-feedback form-control-feedback-end'>
                            <input
                                type="text"
                                className="filter__input form-control"
                                value={state.productName}
                                onChange={e => dispatch(ActionType.creataeSetProductName(e.target.value))}
                            />
                            <div className='form-control-feedback-icon'>
                                <button
                                    className="btn btn-sm btn-icon bg-transparent border-0 px-0"
                                    title={LangMessage.FILTER_SEARCH}
                                    onClick={() => dispatch(ActionType.thuncApplyFilter())}>
                                        <i className='ph-magnifying-glass'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="filter__btn-guop">
                        <button
                            className="btn btn-link p-0"
                            onClick={() => dispatch(ActionType.thuncApplyEmptyFilter())}>
                                {LangMessage.FILTER_CANCELLATION}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
}