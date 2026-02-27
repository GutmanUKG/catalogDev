function deleteRequestOffer(requestId) {
    BX.showWait();
    BX.ajax.runComponentAction('sotbit:offerlist.request.list', 'deleteRequest', {
        mode: 'class',
        data: {
            id: requestId
        }
    }).then(response  => {
            BX.closeWait();
            BX.Main.gridManager.reload(requestOfferListGridID,'');
        },
        error => {
            BX.closeWait();
            console.error(error);
        });
}