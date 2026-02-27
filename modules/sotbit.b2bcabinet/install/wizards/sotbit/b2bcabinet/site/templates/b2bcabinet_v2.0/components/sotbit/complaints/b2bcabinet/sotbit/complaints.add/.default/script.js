const DatatableAdvanced = function () {
    // Basic Datatable examples
    const _componentDatatableAdvanced = function () {
        if (!$().DataTable) {
            console.warn('Warning - datatables.min.js is not loaded.');
            return;
        }

        // Setting datatable defaults
        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [
                {
                    orderable: true,
                    targets: [0],
                    width: "100%",
                },
                {
                    orderable: false,
                    targets: "_all",
                    createdCell: (td, cellData, rowData, row, col) => {
                        if(col > 1 && (rowData.length - 1) > col) {
                            td.setAttribute('data-title', document.querySelector(`#complaint-positions__grid thead th:nth-child(${col + 1})`).textContent);
                        }
                    }
                },
                {
                    orderable: false,
                    targets: [-1],
                    width: 95,
                },
            ],
            order: [[0, 'asc']],
            dom: '<"datatable-header"f<"datatable-add"b>l><"datatable-content"<"datatable-scroll"t><"datatable-footer"ip>>',
            language: {
                sZeroRecords: BX.message('SOTBIT_COMPLAINTS_POSITIONS_TABLE_EMPTY_FILTER'),
                sInfo: BX.message("SOTBIT_COMPLAINTS_POSITIONS_TABLE_PAGINATION_TITLE") + ' _MAX_',
                sInfoEmpty: "",
                sInfoFiltered: '',
                search: '<div class="form-control-feedback form-control-feedback-start flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass text-primary"></i></div></div>',
                emptyTable: BX.message('SOTBIT_COMPLAINTS_POSITIONS_TABLE_EMPTY'),
                searchPlaceholder: BX.message('SOTBIT_COMPLAINTS_POSITIONS_TABLE_SEARCHPLACEHOLDER'),
                lengthMenu: '<span class="me-2">' + BX.message("SOTBIT_COMPLAINTS_POSITIONS_TABLE_COUNT_ITEMS") + '</span> _MENU_',
                paginate: {
                    'first': 'First',
                    'last': 'Last',
                    'next': $('html').attr('dir') == 'rtl' ? '<i class="ph-caret-left fs-sm align-middle"></i>' : '<i class="ph-caret-right fs-sm align-middle"></i>',
                    'previous': $('html').attr('dir') == 'rtl' ? '<i class="ph-caret-right fs-sm align-middle"></i>' : '<i class="ph-caret-left fs-sm align-middle"></i>'
                }
            },
        });

        // Datatable 'length' options
        $('.datatable-show-all').DataTable({
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });

        // DOM positioning
        $('.datatable-dom-position').DataTable({
            dom: '<"datatable-header length-left"lp><"datatable-add"add><"datatable-scroll"t><"datatable-footer info-right"fi>',
        });

        const tableComplaintsPositions = $('.datatable-highlight').DataTable();

        $('.dataTables_length select').attr('data-minimum-results-for-search', 'Infinity');

        tableComplaintsPositions.on('draw', function () {
            DatatableAdvanced.touchspin();
        });
    };

    const _componentTouchSpin = function () {
        $('.touchspin-basic').each(function () {
            let dicim = 2

            if ($(this).attr('data-ratio') == 1){
                dicim = 0;
            } 

            if($(this).attr('data-ratio') != "" &&  $(this).attr('data-max') != ""){
                $(this).TouchSpin({
                    initval: $(this).attr('data-ratio'),
                    min: $(this).attr('data-ratio'),
                    step: $(this).attr('data-ratio'),
                    max: $(this).attr('data-max'),
                    buttondown_class: 'btn',
                    buttonup_class: 'btn',
                    buttondown_txt: '<i class="ph-minus"></i>',
                    buttonup_txt: '<i class="ph-plus"></i>',
                    decimals: dicim,
                }).addClass('fs-xs');
            }else{
                $(this).TouchSpin({
                    initval: 1,
                    min: 1,
                    max: 100,
                    step: 1,
                    decimals: 0,
                }).addClass('fs-xs');
            }

        })
    };


    return {
        listProducts: [],
        init: function () {
            _componentDatatableAdvanced();
            _componentTouchSpin();
        },
        link: _componentDatatableAdvanced,
        touchspin: function () {
            _componentTouchSpin();
        }

    }

}();


document.addEventListener('DOMContentLoaded', function () {
    DatatableAdvanced.init();
    App.initSelect2();
    setEmptyRow();
    document.querySelector('.datatable-add').innerHTML = '<button type="button" class="complaints__add-position btn btn-primary btn-sm-icon ps-sm-3"><i class="ph-plus me-sm-2"></i><span class="d-none d-sm-block">' + BX.message("SOTBIT_COMPLAINTS_POSITIONS_BTN_ADD") + '</span></button>';

    const wrap = document.querySelector(".complaints-add-wrap");
    const formAdd = wrap.querySelector("#complaint-add");
    const btnAddPosition = formAdd.querySelector(".complaints__add-position");
    const errorBlock = wrap.querySelector(".complaint__error-block");
    const successBlock = document.querySelector(".complaints-add__success-block");

    function setEmptyRow() {
        const emptyTableRow = document.querySelector('.dataTables_empty');

        if (emptyTableRow) {
            emptyTableRow.addEventListener('click', addProduct);
        }
    }

    formAdd.addEventListener('submit', function (e) {
        e.preventDefault();
        const btnSubmit = this.querySelector('button[type="submit"]');
        btnSubmit.disabled = true;
        BX.showWait();

        $('#complaint-positions__grid').DataTable().data()

        const formData = new FormData(formAdd);

        $('#complaint-positions__grid').DataTable().cells().nodes().each(function (item) {
            const input = item.querySelector('[data-item="position"]');
            if (!input) {
                return;
            }

            if (formData.has(input.name)) {
                formData.set(input.name, input.value);
                return;
            }

            formData.append(input.name, input.value);
        });

        const request = BX.ajax.runComponentAction('sotbit:complaints.add', 'addComplaint', {
            mode: 'class',
            data: formData
        });

        request.then(
            function (response) {
                if (response.data.success_title) {
                    clearError();
                    showSuccess(response.data.success_title);
                }
                btnSubmit.disabled = false;
                BX.closeWait();
            },
            function (error) {
                const arErrors = error.errors.filter(function (error) {
                    return error.code == 0;
                });

                if (arErrors.length != 0) {
                    printError(
                        arErrors.map(function (error) {
                            return error.message;
                        }).join("<br>")
                    );
                } else {
                    clearError();
                    showSuccess(
                        error.errors.filter(function (error) {
                            return error.customData != null;
                        }).map(function (error) {
                            return error.customData;
                        }).join("<br>")
                    );
                }

                btnSubmit.disabled = false;
                BX.closeWait();
            });
    });

    function showSuccess(message) {
        successBlock.querySelector('.alert-success').textContent = message;
        wrap.remove();
        successBlock.style.display = 'block';
        successBlock.scrollIntoView(false);
    }

    function printError(error) {
        errorBlock.innerHTML = error;
        errorBlock.style.display = 'block';
        errorBlock.scrollIntoView(false);
    }

    function clearError() {
        errorBlock.innerHTML = "";
        errorBlock.style.display = 'none';
    }

    if (btnAddPosition) {
        btnAddPosition.addEventListener('click', addProduct);
    }

    function addProduct() {
        if (comlaintsType == "ORDER") {
            var orderId = document.getElementsByName("COMPLAINTS[PROPERTIES][ORDER_ID]")[0].value;

            if(orderId !== ""){
                BX.SidePanel.Instance.destroy(searchPage);
                BX.SidePanel.Instance.open(
                    searchPage,
                    {
                        requestMethod: "post",
                        requestParams: {
                            orderId: orderId
                        },
                        allowChangeHistory: false,
                        animationDuration: 100,
                        width: 1450,
                    }
                );
            } else {
                console.log("error order id")
            }
        } else {
            BX.SidePanel.Instance.open(
                searchPage,
                {
                    allowChangeHistory: false,
                    animationDuration: 100,
                    width: 1450,
                }
            );
        }

    }

    BX.addCustomEvent('SidePanel.Slider:onMessage', (data) => {
        if (data.eventId === 'addPosition') {
            if (data.data.product) {
                if (!BX.util.in_array(data.data.product.ID, DatatableAdvanced.listProducts)) {
                    DatatableAdvanced.listProducts.push(data.data.product.ID)
                    renderRowItem(data.data.product);
                    BX.onCustomEvent('B2BNotification',[
                        BX.message('SOTBIT_POSITION_NAME_ADD_NOTIFICATION') + data.data.product.NAME + "<br>" +
                        BX.message('SOTBIT_POSITION_ADD_NOTIFICATION'),
                        'success'
                    ]);
                } else {
                    BX.onCustomEvent('B2BNotification',[
                        BX.message('SOTBIT_POSITION_NAME_ADD_NOTIFICATION') + data.data.product.NAME + "<br>" +
                        BX.message('SOTBIT_POSITION_NOT_ADD_NOTIFICATION'),
                        'false'
                    ]);
                }
            }
        } else if (data.eventId === 'addPositions') {
            if (data.data.productIds) {
                let addProducts = [];
                data.data.productIds.forEach(id => {
                    if (!BX.util.in_array(id, DatatableAdvanced.listProducts)) {
                        DatatableAdvanced.listProducts.push(id);
                        addProducts.push(data.data.allProducts[id]);
                    }
                });

                if (addProducts.length) {
                    renderRowsItem(addProducts);

                    BX.onCustomEvent('B2BNotification',[
                        BX.message('SOTBIT_POSITION_ADD_PRODUCTS') + addProducts.length + 
                        BX.message('SOTBIT_POSITION_ADD_PRODUCTS_NOTIFICATION'),
                        'success'
                    ]);
                } else {
                    BX.onCustomEvent('B2BNotification',[
                        BX.message('SOTBIT_POSITION_ALL_ADD'),
                        'false'
                    ]);
                }                
            }
        }
    });

    $('#complaint-positions__grid tbody').on('click', '.delete-position', function () {
        const idProduct = $(this).siblings().attr('value');

        $('#complaint-positions__grid').DataTable()
            .row($(this).parents('tr'))
            .remove()
            .draw();

        BX.onCustomEvent('B2BNotification',[
            BX.message('SOTBIT_COMPLAINTS_POSITION_NAME_NOT') + $(this).attr("data-name") + "<br>" +
            BX.message('SOTBIT_COMPLAINTS_POSITION_NOT'),
            'success'
        ]);

        DatatableAdvanced.listProducts = DatatableAdvanced.listProducts.filter((id) => {return id !== idProduct})

        setEmptyRow();
    });

    $('#complaint-positions__grid thead').on('click', '.delete-all-position', function () {
        DatatableAdvanced.listProducts = [];
        $('#complaint-positions__grid').DataTable().rows().remove().draw();
        BX.onCustomEvent('B2BNotification',[
            BX.message('SOTBIT_COMPLAINTS_POSITIONS_ALL_NOT'),
            'success'
        ]);
        setEmptyRow();
    });

    if (!$().TouchSpin) {
        console.warn('Warning - touchspin.min.js is not loaded.');
        return;
    }
});
