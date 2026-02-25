function RequestOfferDetail(params) {
    this.id = params.ID;
    this.listPath = params.LIST_PATH_URL;
    this.deleteBtn = document.querySelector(params.DELETE_BTN);
    this.deleteBtn.addEventListener('click', ()=>{
        if (!confirm(BX.message('SOR_CONFIRM_DELETE'))) {
            return;
        }

        BX.showWait();
        BX.ajax.runComponentAction('sotbit:offerlist.request.detail', 'deleteRequest', {
            mode: 'class',
            data: {
                id: this.id
            }
        }).then(response  => {
                BX.closeWait();
                alert(BX.message('SOR_SUCCESS_DELETE'));
                window.location.replace(this.listPath);
            },
            error => {
                BX.closeWait();
                console.error(error);
            });
    })
}