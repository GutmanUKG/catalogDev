function closeModal() {
    const formAdd = document.querySelector(".popup-order-list");
    formAdd.style.display = "none";
    formAdd.querySelector(".error-block").innerHTML = "";
    const popupAddBasket = document.querySelector(".popup-order-add-basket");
    popupAddBasket.style.display = "none";
    const popupRemove = document.querySelector(".popup-order-remove");
    popupRemove.style.display = "none";
    const successBlock = document.querySelector(".popup-order-remove-success");
    successBlock.style.display = "none";
}

function showFormAddBasket(templateId, templateName) {
    const btnShowModal = document.querySelector("#btn_modal_order-add-basket");
    btnShowModal.click();

    const formAdd = document.querySelector("#modal_order-add-basket");
    formAdd.setAttribute('data-id', templateId);
    console.log(add_basket_descr.replace("#TEMPLATE_NAME#", templateName))
    formAdd.querySelector(".form-description").textContent = add_basket_descr.replace("#TEMPLATE_NAME#", templateName);
}

function addToBasket() {
    BX.showWait();
    const template_id = document.querySelector("#modal_order-add-basket").getAttribute('data-id');

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

function showRemoveForm(templateId, templateName) {
    const btnShowModal = document.querySelector("#btn_modal_order-remove");
    btnShowModal.click();

    const formRemove = document.querySelector("#modal_order-remove");
    formRemove.setAttribute('data-id', templateId);
    formRemove.querySelector(".form-description").textContent = remove_descr.replace("#TEMPLATE_NAME#", templateName);
    TEMPLATE_NAME = templateName;
}

function removeTemplate() {
    BX.showWait();
    const modalForm = document.querySelector("#modal_order-remove"),
        template_id = modalForm.getAttribute('data-id');
    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.ordertemplate.detail', 'deleteTemplate', {
        mode: 'class',
        data: {
            templateId: template_id
        }
    });

    request.then(function (response) {
        BX.Main.gridManager.reload('TEMPLATE_LIST', '');
        BX.closeWait();
        modalForm.querySelector('[data-dismiss="modal"]').click();

        const successBlockBtn = document.querySelector("#btn_modal_order-remove-success");
        let description = document.querySelector("#modal_order-remove-success .form-description");
        description.textContent = success_descr.replace("#TEMPLATE_NAME#", TEMPLATE_NAME);
        successBlockBtn.click();
    });
}

function exportExcelTemplate(templateId, name) {
    BX.showWait();
    let prod = new Object();
    prod = {"ID": id_products[templateId]};
    var file = '';

    $.ajax({
        type: 'POST',
        async: false,
        url: site_path + 'include/ajax/blank_excel_export.php',
        data: {
            table_header: tableHeader,
            filterProps: prod,
            priceCodes: priceCodes,
            file: file,
            quantity: quantity[templateId]
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

        if (name == undefined) {

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

window.addEventListener('load', function () {
    const ordertemplate = document.querySelector('.ordertemplates-list-wrap');
    const footerConteiner = ordertemplate.querySelector('.catalog__actions-container');
    const actionBtn = ordertemplate.querySelector('.catalog__actions-toggler');

    ordertemplate.addEventListener('click', function (e) {
        const result = upTheDOMTree(
            e.target,
            ['blank-excel-import', 'blank-export-in-excel'],
            'ordertemplates-list-wrap',
        );
        if (result) {
            return
        }
        if (footerConteiner != null) {
            if (footerConteiner.classList.contains('catalog__actions-container--open')) {
                footerConteiner.classList.remove('catalog__actions-container--open')
            }
        }
    })
    if (actionBtn) {
        actionBtn.addEventListener('click', function (e) {
            footerConteiner.classList.toggle('catalog__actions-container--open')
            e.stopPropagation();
        })
    }


    /**
     * @param {HTMLElement} node
     * @param {string[]} searchId
     * @param {string} stopID
     * @returns {boolean}
     */
    function upTheDOMTree(node, searchId, stopID) {
        if (node === null || node.id == stopID) {
            return false
        }
        for (const i of searchId) {
            if (node.id === i) {
                return true
            }
        }
        return upTheDOMTree(node.parentNode, searchId, stopID);
    }

})