import React from 'react';


export const SectionsSelect = ({sections, visble, secitonName, setSectionName, selectedSection, clearSectionMame}) => {
    const patrn = new RegExp(secitonName, 'i');
    
    return <div className="filter__SectionsSelect">
        {visble
            ? <div>
                {sections
                    .filter(i => !selectedSection.includes(i.NAME))
                    .filter(i => patrn.test(i.NAME))
                    .map(i => <div
                        className="filter__SectionsSelect-item"
                        key={i.ID}
                        onMouseDown={() => {
                            setSectionName(i.NAME);
                            clearSectionMame();
                        }}>
                            {i.NAME }
                    </div>)}
            </div>
            : null}
    </div>
}