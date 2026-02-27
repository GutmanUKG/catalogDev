$(document).ready(function(){
    SweetAlert.initComponents();

    const modal = document.querySelector('#modal-staff-register'),
        modalDialog = modal.querySelector('.modal-dialog'),
        registerSubmit = modal.querySelector('button[name="register_submit_button"]'),
        registerForm = modal.querySelector('form[name="regform"]'),
        registerErrorBlock = modal.querySelector('.regform-error'),
        confirmForm = document.querySelector('#modal-staff-confirm'),
        confirmDialog = confirmForm.querySelector('.modal-dialog'),
        confirmBtn = confirmForm.querySelector('.btn_confirm'),
        referralLink = modal.querySelector('a.register-referral-link'),
        referralModal = document.querySelector('#modal-staff-referal-register'),
        referralForm = referralModal.querySelector('#referralform'),
        referralModalDialog = referralModal.querySelector('.modal-dialog'),
        referralExit = referralModal.querySelector('button[name="referral_exit_button"]'),
        referralSubmit = referralModal.querySelector('button[name="referral_submit_button"]');

    if (modal) {
        document.body.appendChild(modal);
        document.body.appendChild(referralModal);
        document.body.appendChild(confirmForm);
    }

    referralLink.onclick = function() {
        modal.querySelector('[data-bs-dismiss="modal"]').click();
    };

    referralExit.onclick = function() {
        referralModal.querySelector('[data-bs-dismiss="modal"]').click();
        document.querySelector('[data-bs-target="#modal-staff-register"]').click();
    };

    referralSubmit.onclick = function() {
        BX.showWait(referralModalDialog);
        registerFormData = new FormData(referralForm);
        var errorReferal = document.querySelector('.referral-form .error-block');
        var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.register', 'sendReferralForm', {
            mode: 'class',
            data: registerFormData,
        });

        request.then(function (response) {
            if (response.data.error === true) {
                if(response.data.userId){
                    confirmBtn.setAttribute('data-staff-id', response.data.userId);
                    showConfirmForm(referralModal);

                }
                else {
                    let erorText = '<div class="bitrix-error"><label class="validation-invalid-label errortext">' + response.data.errorMessage+ '</label></div>'
                    errorReferal.innerHTML = erorText;
                }
            } else {
                closeModal(referralModal);
                SweetAlert.showSuccess(response.data.successMessage);
                BX.Main.gridManager.reload('STAFF_LIST','');
                BX.Main.gridManager.reload('STAFF_UNCONFIRMED_LIST','');
            }
            BX.closeWait();
        });
    };

    function showConfirmForm(closestForm) {
        closeModal(closestForm);
        document.querySelector('button[data-bs-target="#modal-staff-confirm"]').click();
    }

    registerSubmit.onclick = function() {
        BX.showWait(modalDialog);
        registerFormData = new FormData(registerForm);
        var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.register', 'sendForm', {
            signedParameters: window.arCompStaffRegisterParams,
            mode: 'class',
            data: registerFormData,
        });
        request.then(function (response) {
            if (response.data.error === true) {
                if(response.data.userId){
                    confirmBtn.setAttribute('data-staff-id', response.data.userId);
                    showConfirmForm(modal);
                }
                else {
                    let erorTextForm = '';
                    for (var key in response.data.errorMessage) {
                        erorTextForm += '<div class="bitrix-error"><label class="validation-invalid-label errortext">' + response.data.errorMessage[key]+ '</label></div>'
                    }
                    registerErrorBlock.innerHTML = erorTextForm;
                }
            }
            else {
                closeModal(registerForm);
                SweetAlert.showSuccess(response.data.successMessage);
                BX.Main.gridManager.reload('STAFF_LIST','');
                BX.Main.gridManager.reload('STAFF_UNCONFIRMED_LIST','');
            }
            BX.closeWait();
        });
    };

    confirmBtn.onclick = function() {
        BX.showWait(confirmDialog);

        var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.register', 'confirmStaff', {
            signedParameters: window.arCompStaffRegisterParams,
            mode: 'class',
            data: registerFormData
        });

        request.then(function (response) {
            if (response.data.error === false) {
                closeModal(confirmForm);
                SweetAlert.showSuccess(response.data.successMessage);
            } else {
                registerErrorBlock.innerHTML = '<div class="bitrix-error"><label class="validation-invalid-label errortext">' + response.data.errorMessage+ '</label></div>';
                closeModal(confirmForm);
            }
            BX.closeWait();
        });
    };

    function closeModal(modal) {
        registerForm.reset();
        referralForm.reset();
        registerErrorBlock.innerHTML = '';
        modal.querySelector('[data-bs-dismiss="modal"]').click();
    }
});