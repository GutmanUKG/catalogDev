function SORequestAdd (params) {
    SweetAlert.initComponents();

    const btnGetResult = document.getElementById(params.btnGetReault);
    const modal = document.getElementById('modal-offerslist__request_add');
    const form = modal.querySelector('#offerslist__request_add');

    btnGetResult.addEventListener('click', function () {
        BX.showWait();
        BX.ajax.runComponentAction('sotbit:offerlist.request.add', 'checkBasketItem', {
            mode: 'class',
        }).then(response  => {
                BX.closeWait();
                modal.querySelector('[data-bs-target]').click();
            },
            error => {
                BX.closeWait();
                SweetAlert.showError(error.errors.map((item) => item.message).join('\n'));
            });
    });

    document.body.appendChild(modal);

    form?.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);

        BX.showWait();
        BX.ajax.runComponentAction('sotbit:offerlist.request.add', 'addRequest', {
            mode: 'class',
            data: formData
        }).then(response  => {
                BX.closeWait();
                modal.querySelector('[data-bs-dismiss]').click();
                SweetAlert.showSuccess(response.data.message);
                form.reset();
            },
            error => {
                BX.closeWait();
                SweetAlert.showError(error.errors.map((item) => item.message).join('\n'));
            });
    })
}