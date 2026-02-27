function EditorSotbitOferlist(params) {
    this.form = document.querySelector(params.formselector);
    this.editorInput = params.editorInput;
    this.printPageAction = params.printPageAction;
    this.offerId = params.offerId;

    this.init();
}

EditorSotbitOferlist.prototype.init = function () {
    CKEditorOfferlistEditor.init(this.editorInput);


    this.form?.addEventListener('submit', function (event) {
        event.preventDefault();
        BX.showWait();

        BX.ajax.runComponentAction('sotbit:offerlist.editor', 'saveDocument', {
            mode: 'class',
            data: {
                html: window.btoa(unescape(encodeURIComponent(CKEDITOR.instances[this.editorInput].getData()))),
                offerId: this.offerId
            }
        }).then(response  => {
                BX.closeWait();
                if (response.data == true) {
                    setTimeout(() => window.open(this.printPageAction), 0);
                }
            },
            error => {
                BX.closeWait();
                alert(error);
            });
    }.bind(this));
};


var CKEditorOfferlistEditor = function() {

    this.isInit = false;
    var _componentCKEditor = function(selector) {
        if (typeof CKEDITOR == 'undefined') {
            console.warn('Warning - ckeditor.js is not loaded.');
            return;
        }


        CKEDITOR.replace(selector, {
            height: document.documentElement.clientHeight / 100 * 65,
            removeButtons: 'Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Scayt,About,pbckcode',
        });

    };

    return {
        init: function(selector) {
            _componentCKEditor(selector);
        }
    }
}();