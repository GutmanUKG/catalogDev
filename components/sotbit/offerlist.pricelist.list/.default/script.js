function deletePriceList(requestId) {
    BX.showWait();
    BX.ajax.runComponentAction('sotbit:offerlist.pricelist.list', 'deletePriceList', {
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