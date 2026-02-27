$(document).ready(function () {
    SweetAlert.initComponents();

    let modalManager = document.getElementById("modal_manager");
    let inputs = modalManager.querySelectorAll(".form-group.row");

    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].querySelector(".form-required.starrequired")) {
            let input = inputs[i].querySelector("input[type='text']");
            input.setAttribute('required', '');
        }
    }

    validationPhone(modalManager);

    /** @param {HTMLElement} root */
    function validationPhone(root) {
        const phone = root.querySelector("#tel");
        let mask = '';
        if (phone != null) {
        for (let i=0; i < phone.value.length; i++) {
            if (phone.value[i] === '+') {
                mask += '.';
            } else if (phone.value[i] === '_') {
                mask += '[0-9]';
            } else {
                mask += phone.value[i];
            }
        }
        phone.setAttribute('pattern', mask);
    }
    }

    function requiredAllField(inputs) {
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].querySelector(".form-required.starrequired")) {
                let input = inputs[i].querySelector("input[type='text']");
                if (input.value.length === 0) {
                    return false;
                }
            }
        }
        return true;
    }
});



var SweetAlert = function () {

    var swalInit = swal.mixin({
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn_b2b',
            cancelButton: 'btn btn-light',
            denyButton: 'btn btn-light',
            input: 'form-control'

        }
    });

    var _componentSweetAlert = function() {
        if (typeof swal == 'undefined') {
            console.warn('Warning - sweet_alert.min.js is not loaded.');
            return;
        }
    };

    var _showSuccess = function(title, text) {

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

    return {
        initComponents: function() {
            _componentSweetAlert();
        },
        showSuccess: function(title, text = '') {
            _showSuccess(title, text);
        }
    }
}();