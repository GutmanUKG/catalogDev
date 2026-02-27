const SweetAlert = function () {

    const swalInit = swal.mixin({
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger',
            denyButton: 'btn',
            input: 'form-control'

        }
    });

    const _componentSweetAlert = function() {
        if (typeof swal == 'undefined') {
            console.warn('Warning - sweet_alert.min.js is not loaded.');
            return;
        }
    };

    const _showSuccess = function(title, text) {

        if (typeof swal == 'undefined') {
            return;
        }

        swalInit.fire({
            title: title,
            text: text,
            icon: 'success',
            showCloseButton: true
        });
    }

    const _showError = function(title, text) {

        if (typeof swal == 'undefined') {
            return;
        }

        swalInit.fire({
            title: Array.isArray(title) ? title.map((item)=> {return item.message}).join('<br>') : title,
            text: text,
            icon: 'error',
            showCloseButton: true
        });
    }

    const _showInfo = function(title, text) {

        if (typeof swal == 'undefined') {
            return;
        }

        swalInit.fire({
            title: title,
            text: text,
            icon: 'info',
            showCloseButton: true
        });

    }

    const _confirm = function(question, fulfilled, rejected) {
        if (typeof swal == 'undefiend') {
            return;
        }

        swalInit.fire({
            title: question || BX.message('SWEETALERT_DEFAULT_QUESTION'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: BX.message('SWEETALERT_CONFIRM_BUTTON'),
            cancelButtonText: BX.message('SWEETALERT_CANCEL_BUTTON'),
            }).then((result) => {
                if (result.isConfirmed) {
                    fulfilled();
                } else {
                    rejected(result);
                }
            })
    }

    return {
        initComponents: function() {
            _componentSweetAlert();
        },
        showSuccess: function(title, text = '') {
            _showSuccess(title, text);
        },
        showError: function(title, text = '') {
            _showError(title, text);
        },
        showInfo: function(title, text = '') {
            _showInfo(title, text);
        },
        confirm: function(question, fulfilled = ()=>{}, rejected = ()=>{}) {
            _confirm(question, fulfilled, rejected);
        }
    }
}();