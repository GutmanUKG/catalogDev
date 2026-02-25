function OfferlistPriceListDetail(params) {
    this.params = params;

    this.init();
}

OfferlistPriceListDetail.prototype.init = function () {
    CKEditorOfferlistEditor.init(this.params.editorInput);
    SweetAlert.initComponents();

    const save = document.getElementById(this.params.actions.save);
    save?.addEventListener('click', this.save.bind(this));
};

OfferlistPriceListDetail.prototype.save = function () {
    BX.showWait();

    BX.ajax.runComponentAction('sotbit:offerlist.pricelist.detail', 'savePriceList', {
        mode: 'class',
        data: {
            arFields: {
                HTML: window.btoa(unescape(encodeURIComponent(CKEDITOR.instances[this.params.editorInput].getData()))),
                NAME: document.querySelector('input[name="NAME"]').value,
                ID: this.params.id,
            }
        }
    }).then(response => {
            BX.closeWait();
            if (response.data.success === true) {
                SweetAlert.showSuccess(BX.message('SUCCESS_TITLE'));
            }
        },
        error => {
            BX.closeWait();
            alert(error);
        });
};

var CKEditorOfferlistEditor = function() {

    this.isInit = false;
    var _componentCKEditor = function(selector) {
        if (typeof CKEDITOR == 'undefined') {
            console.warn('Warning - ckeditor.js is not loaded.');
            return;
        }

        CKEDITOR.replace(selector, {
            height: 1000,
            removeButtons: 'Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Scayt,About,pbckcode',
        });

    };

    return {
        init: function(selector) {
            _componentCKEditor(selector);
        }
    }
}();