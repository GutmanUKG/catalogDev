
function showPopupRemoveDraft(draftID, draftName) {
    document.querySelector('#remove-success-block').style.display = "none";
    document.querySelector('#draft-create-ordertemplate-block').style.display = "none";
    document.querySelector('#draft-remove-block').style.display = "block";
    const popup = document.querySelector('.popup-draft-list');
    const description = deleteDescr;
    const removeBlock = popup.querySelector('#draft-remove-block');
    removeBlock.setAttribute("data-id", draftID);
    let textDescrBlock = removeBlock.querySelector('.draft-remove-description');
    textDescrBlock.textContent = description.replace("#DRAFT_NAME#", draftName);
    popup.style.display = "flex";
    NAME_DRAFT = draftName;
}

function closeModal() {
    document.querySelector('.popup-draft-list').style.display = "none";
    document.querySelector('#draft-create-order-block').style.display = "none";
    document.querySelector('#draft-remove-block').style.display = "none";
    document.querySelector('#remove-success-block').style.display = "none";
    document.querySelector('#draft-create-ordertemplate-block').style.display = "none";
}

function removeDraft() {
    BX.showWait();
    const draftId = removeBlock = document.querySelector('#draft-remove-block').getAttribute("data-id");
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.draft.list', 'removeDraft', {
        mode: 'class',
        data: {
            draftId: draftId
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
            const successTitle = successDelete;
            let successRemove = document.querySelector('#remove-success-block');
            let removeBlock = document.querySelector('#draft-remove-block');
            let successTitleBlock = successRemove.querySelector('.draft-remove-description-success');
            successTitleBlock.textContent = successTitle.replace("#DRAFT_NAME#", NAME_DRAFT);
            BX.closeWait();
            removeBlock.style.display = "none";
            successRemove.style.display = "block";
            BX.Main.gridManager.reload('DRAFT_LIST','');
        }
    });
}

function showPopupCreateOrderDraft(draftId, draftName) {
    const popup = document.querySelector('.popup-draft-list');
    popup.querySelector('#remove-success-block').style.display = "none";
    popup.querySelector('#draft-remove-block').style.display = "none";
    popup.querySelector('#draft-create-ordertemplate-block').style.display = "none";
    const createOrderBlock = popup.querySelector('#draft-create-order-block');
    createOrderBlock.style.display = "block";
    createOrderBlock.setAttribute("data-id", draftId);
    const description = createOrderDescription;
    let textDescrBlock = createOrderBlock.querySelector('.draft-create-order-description');
    textDescrBlock.textContent = description.replace("#DRAFT_NAME#", draftName);
    popup.style.display = "flex";
}

function createOrder() {
    BX.showWait();
    const draftId = removeBlock = document.querySelector('#draft-create-order-block').getAttribute("data-id");
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.draft.list', 'createOrder', {
        mode: 'class',
        data: {
            draftId: draftId
        }
    });

    request.then(function (response) {
        if (response.data === true) {
            BX.closeWait();
            document.location.href = basketPath;
        }
    });
}

function createOrdertemplate() {
    BX.showWait();
    const draftId = removeBlock = document.querySelector('#draft-create-ordertemplate-block').getAttribute("data-id");
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.draft.list', 'createOrdertemplate', {
        mode: 'class',
        data: {
            draftId: draftId
        }
    });

    request.then(function (response) {
        BX.closeWait();
        if(response.data !== false){
            const link = /b2bcabinet/.test(location.pathname)
                ? '/b2bcabinet/orders/templates/ordertemplate_detail.php?ID='
                : '/orders/templates/ordertemplate_detail.php?ID=';

            document.location.href = link + response.data;
        }
    });
}

function showPopupCreateOrdertemplate(draftId, draftName) {
    const popup = document.querySelector('.popup-draft-list');
    popup.querySelector('#remove-success-block').style.display = "none";
    popup.querySelector('#draft-remove-block').style.display = "none";
    const createOrdertmpltBlock = popup.querySelector('#draft-create-ordertemplate-block');
    createOrdertmpltBlock.style.display = "block";
    createOrdertmpltBlock.setAttribute("data-id", draftId);
    const description = ordertemplateDescr;
    let textDescrBlock = createOrdertmpltBlock.querySelector('.draft-create-ordertemplate-description');
    textDescrBlock.textContent = description.replace("#DRAFT_NAME#", draftName);
    popup.style.display = "flex";
}