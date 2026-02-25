import React from 'react';
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

    return <div className={cn('upselling__filter', {'upselling__filter__hidden': !state.showFilter})}>
        {state.spiner ? <Spiner /> : null}
        <div className="filter">
            <header className="filter__header">
                <h5 className="filter_title">{LangMessage.FILTER_TITLE}</h5>
                <svg
                    className={cn('filter__roll-up', {'filter__roll-down': !state.showFilter})} width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg"
                    onClick={() => dispatch(ActionType.createToggleVisibleFilter())}>
                    <path d="M7.26273 8.34035C7.02473 8.34035 6.78676 8.24948 6.60531 8.06811L0.895346 2.35809C0.532118 1.99486 0.532118 1.40595 0.895346 1.04286C1.25843 0.679783 1.84722 0.679783 2.21048 1.04286L7.26273 6.09541L12.315 1.04304C12.6782 0.67996 13.267 0.67996 13.63 1.04304C13.9934 1.40612 13.9934 1.99503 13.63 2.35826L7.92015 8.06829C7.73861 8.24968 7.50064 8.34035 7.26273 8.34035Z" fill="#38576E"/>
                </svg>
            </header>
            {state.showFilter ? <>
                <div className="filter__input-group">
                    <div className="filter__selected-section">
                        {state.selectedSectionsNames
                            .map(i => <div
                                    className="filter__selected-item"
                                    key={i}
                                    onClick={() => dispatch(ActionType.createRemoveSectionName(i))}>
                                {i} <i className="icon-cross"></i>
                            </div>)}
                    </div>
                    <input
                        type="text"
                        className="filter__input"
                        placeholder={LangMessage.FILTER_SECTION}
                        onFocus={() => setSectionsSelectVisible(true)}
                        onBlur={() => {setSectionsSelectVisible(false)}}
                        value={sectionsName}
                        onChange={e => setSectionName(e.target.value)}
                    />
                    <div>
                    <SectionsSelect
                        sections={immutableState.SECTIONS}
                        secitonName={sectionsName}
                        visble={sectionsSelectVisible}
                        selectedSection={state.selectedSectionsNames}
                        setSectionName={name => dispatch(ActionType.createAddSectionName(name))}
                        clearSectionMame={() => setSectionName(() => '')}
                    />
                    </div>
                </div>
                <div className="filter__input-group">
                    <input
                        type="text"
                        className="filter__input"
                        placeholder={LangMessage.FILTER_SEARCH_PLACEHOLDER}
                        value={state.productName}
                        onChange={e => dispatch(ActionType.creataeSetProductName(e.target.value))}
                    />
                </div>
                <div className="filter__btn-guop">
                    <button
                        className="btn btn_b2b"
                        onClick={() => dispatch(ActionType.thuncApplyFilter())}>
                            {LangMessage.FILTER_APPLY}
                    </button>
                    <button
                        className="btn btn-light"
                        onClick={() => dispatch(ActionType.thuncApplyEmptyFilter())}>
                            {LangMessage.FILTER_CANCELLATION}
                    </button>
                </div>
            </> : null}

        </div>
    </div>
}