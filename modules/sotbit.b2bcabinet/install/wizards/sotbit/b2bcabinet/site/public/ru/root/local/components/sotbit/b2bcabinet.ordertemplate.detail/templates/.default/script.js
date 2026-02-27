
function removeTemplate() {
    BX.showWait();
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.ordertemplate.detail', 'deleteTemplate', {
        mode: 'class',
        data: {
            templateId: template_id
        }
    });

    request.then(function (response) {
        BX.closeWait();
        document.querySelector('#modal_popup-order-remove [data-dismiss="modal"]').click();
        const formSuccessBtn = document.querySelector("#btn_modal_order-remove-success");
        formSuccessBtn.click();
    });

}

function goToList() {
    document.location.replace(list_url);
}

function showFormRemoveSave() {
    document.querySelector('[data-target="#modal_popup-order-remove"]').click();
}

function showFormRemove() {
    const blockRemove = document.querySelector(".popup-order-remove"),
        formRemove = blockRemove.querySelector("#ordertemplates-remove-block"),
        description = formRemove.querySelector(".ordertemplates__remove .form-description");
    description.textContent = description.textContent.replace("#TEMPLATE_NAME#", template_name);
    document.querySelector(".ordertemplates__remove").style.display = "block";
    blockRemove.style.display = "flex";
}

function closeModal() {
    const formRemove = document.querySelector(".popup-order-remove");
    const formAddBasket = document.querySelector(".popup-order-add-basket");
    const formSuccess = document.querySelector(".popup-order-remove-success");
    formRemove.style.display = "none";
    formAddBasket.style.display = "none";
    formSuccess.style.display = "none";
}

function saveTemplate() {
    BX.showWait();
    const inputName = document.querySelector('.ordertemplates-add-form input[name="TEMPLATE_NAME"]');
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.ordertemplates.detail', 'saveTemplate', {
        mode: 'class',
        data: {
            templateId: template_id,
            templateName: inputName.value
        }
    });

    request.then(function (response) {
        BX.closeWait();
        document.location.href = path_to_detail.replace("#ID#", template_id);
    });
}

function editTemplate() {
    document.location.href = window.location + '&edit=Y';
}

function addToBasket() {
    BX.showWait();
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.ordertemplate.detail', 'addToBasket', {
        mode: 'class',
        data: {
            templateId: template_id,
        }
    });

    request.then(function (response) {
        document.location.href = path_to_basket;
        BX.closeWait();

    });
}

function showFormAddBasket() {
    const popupAddBasket = document.querySelector(".popup-order-add-basket");
    const formAdd = popupAddBasket.querySelector("#ordertemplates-addbasket-block");
    const description = formAdd.querySelector(".form-description");
    description.textContent = description.textContent.replace("#TEMPLATE_NAME#", template_name);
    popupAddBasket.style.display = "flex";
}


function excelOut(name=null) {
    BX.showWait();

        var file = '';

        $.ajax({
            type: 'POST',
            async: false,
            url: site_path + 'include/ajax/blank_excel_export.php',
            data: {
                table_header: tableHeader,
                filterProps: filterProps,
                priceCodes: priceCodes,
                file: file,
                quantity: quantity
            },
            success: function (data) {
                if (data !== undefined && data !== '') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {

                    }
                }

                if (data.TYPE !== undefined) {
                    console.log(data.MESSAGE);
                } else if (data !== undefined && data !== '') {
                    file = data;
                }
            },
            complete: function () {
                BX.closeWait();
            }
        });

        if (file !== undefined && file !== '') {

            if (name === null) {
                var now = new Date();

                var dd = now.getDate();
                if (dd < 10) dd = '0' + dd;
                var mm = now.getMonth() + 1;
                if (mm < 10) mm = '0' + mm;
                var hh = now.getHours();
                if (hh < 10) hh = '0' + hh;
                var mimi = now.getMinutes();
                if (mimi < 10) mimi = '0' + mimi;
                var ss = now.getSeconds();
                if (ss < 10) ss = '0' + ss;

                var rand = 0 - 0.5 + Math.random() * (999999999 - 0 + 1)
                rand = Math.round(rand);

                var name = 'blank_' + now.getFullYear() + '_' + mm + '_' + dd + '_' + hh + '_' + mimi + '_' + ss + '_' + rand + '.xlsx';
            }

            var link = document.createElement('a');
            link.setAttribute('href', file);
            link.setAttribute('download', name);
            var event = document.createEvent("MouseEvents");
            event.initMouseEvent(
                "click", true, false, window, 0, 0, 0, 0, 0
                , false, false, false, false, 0, null
            );
            link.dispatchEvent(event);
        }
}

function resetEdit() {
    document.location.href = list_url;
}

function saveTemplate() {
    const saveForm = document.querySelector("#ordertemplate-save");
    saveForm.submit();
}