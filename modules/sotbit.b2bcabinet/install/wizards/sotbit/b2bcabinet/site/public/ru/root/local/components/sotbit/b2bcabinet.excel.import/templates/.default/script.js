(function () {
    if (!!window.JCB2BExcelImport)
        return;

    window.JCB2BExcelImport = function (params) {
        this.siteId = params.siteId || '';
        this.componentPath = params.componentPath || '';
        this.parameters = params.parameters || '';
        this.filesInputName = params.inputName;
        this.modal = document.querySelector(params.modalSelector);
        this.form = document.querySelector(params.formSelector);
        this.btnSend = document.querySelector(params.btnSendSelector);
        this.formData = {};
        this.resultBlock = document.querySelector(params.resultBlockSelector);
        this.errorBlock = document.querySelector(params.errorBlockSelector);
        this.modalSuccessFooter = document.querySelector(params.modalSuccessFooter);
        this.modalFooter = document.querySelector(params.modalFooter);

        this.subscribeChangeUploadedFiles(this);

        const changePopup = new MutationObserver(function (mutations){
            for(let mutation of mutations) {
                if (!mutation.target.classList.contains('show')) {
                    this.closePopup();
                }
            }
        }.bind(this));

        if (this.modal) {
            document.body.appendChild(this.modal);
        }

        changePopup.observe(this.modal, {
            childList: false,
            subtree: false,
            characterDataOldValue: true,
            attributeFilter: ['class']
        });

        if (this.btnSend) {
            BX.unbindAll(this.btnSend);
            BX.bind(this.btnSend, 'click', BX.delegate(this.importFilesAction, this));
        }

        this.uploudFilesCounter = 0;
    };

    window.JCB2BExcelImport.prototype =
        {
            importFilesAction: function () {
                this.showWait();
                if (!this.checkFiles()) {
                    this.showError(BX.message('error_no_file'));
                    this.closeWait();
                    return;
                }

                var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.excel.import', 'importExcel', {
                    signedParameters: this.parameters,
                    mode: 'class',
                    data:{
                        formData: this.formData,
                    }
                });

                request.then(
                    function (response) {

                        BX.onCustomEvent("UpdateItemQuantity");
                        this.hideError();
                        this.resetForm();
                        this.form.style.display = "none";
                        this.resultBlock.innerHTML = response.data.html;
                        this.modalFooter.style.display = "none";
                        this.modalSuccessFooter.style.display = "flex";
                        this.closeWait();
                        BX.onCustomEvent('OnBasketChange');
                        if (BX.Sale && BX.Sale.BasketComponent) {
                            BX.Sale.BasketComponent.sendRequest('recalculateAjax', {fullRecalculation: 'Y'});
                        }
                    }.bind(this),

                    function (response) {
                        var errorMessage = '';
                        response.errors.forEach(function(error, e, arErrors) {
                            errorMessage += error.message + "\n";
                        });
                        this.showError(errorMessage);
                        this.closeWait();
                    }.bind(this),
                );
            },
            closePopup: function () {
                this.form.style.display = "block";
                this.resetForm();
                this.hideError();
                if (this.resultBlock) {
                    this.resultBlock.innerHTML = "";
                }
                this.modalSuccessFooter.style.display = "none";
                this.modalFooter.style.display = "flex";
            },
            resetForm: function () {
                var arItemFiles = this.form.querySelectorAll('.del-but');
                if (arItemFiles) {
                    for (var i = 0; i < arItemFiles.length; i++) {
                        arItemFiles[i].click();
                    }
                }
            },
            showWait: function () {
                var iconContainer = this.btnSend.querySelector("i");
                iconContainer.setAttribute("class", "icon-spinner2 spinner mr-2");
            },
            closeWait: function () {
                var iconContainer = this.btnSend.querySelector("i");
                iconContainer.setAttribute("class", "icon-download mr-2");
            },
            hideError: function () {
                this.errorBlock.textContent = "";
                this.errorBlock.style.display = 'none';
            },
            showError: function (error) {
                this.errorBlock.textContent = error;
                this.errorBlock.style.display = 'block';
            },
            checkFiles: function () {
                this.formData = BX.ajax.prepareForm(this.form);
                return this.formData.data[this.filesInputName];
            },

            subscribeChangeUploadedFiles: function(object) {
                const target = document.querySelector('.file-placeholder-tbody');
                const config = {
                    attributes: true,
                    // childList: true,
                    subtree: true,
                };
                const callback = function(mutationsList) {
                    for (let mutation of mutationsList) {
                        let minus = Array.prototype.slice
                            .call(mutation.target.style)
                            .indexOf('display');
                        if (mutation.target.classList.contains('files-storage')) {
                            object.uploudFilesCounter += 1;
                        } else if (minus > -1) {
                            object.uploudFilesCounter -= 1;
                        }
                    }
                    if (object.uploudFilesCounter > 0) {
                        object.btnSend.disabled = false
                    } else {
                        object.btnSend.disabled = true
                    }
                };
                const observer = new MutationObserver(callback);
                observer.observe(target, config);
            }
        };
})();