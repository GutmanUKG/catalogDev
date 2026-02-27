$(document).on("change keyup input", "#INN", function(){
	var val = $(this).val();
	$('#NAME').val(val);
});
$(document).on("change", "input[name='PERSON_TYPE']", function(){
    var post = 'change_person_type=' + this.value;
    if(this.value !== '') {
        $('#change_person_type').val(true);
        $('#PERSON_TYPE').val(this.value);
    }

    this.form.submit();
});


function submitForm() {
    if(!document.querySelector('.main-user-consent-request input').checked){
        return;
    }
    let companyId = getGet('EDIT_ID');
    if(!companyId){
        formData.append('save','Y');
        document.addOrg.submit();
        return;
    }
    BX.showWait();
    let formData = new FormData(document.addOrg);
    formData.append('EDIT_ID',companyId);
    formData.append('save','Y');
    var request = BX.ajax.runComponentAction('sotbit:auth.company.add', 'checkFields', {
        mode: 'class',
        data: formData
    });

    request.then(function (response) {
        if(response.data == "Y"){
            BX.closeWait();
            let confirmResult = confirm(title_send_moderation);
            if (confirmResult == false) return false;
            else {
                document.getElementById("apply").value = "Y";
                document.addOrg.submit();
            }
        }
        else {
            document.getElementById("apply").value = "N";
            BX.closeWait();
            document.addOrg.submit();
        }
    });
}

function getGet(name) {
    var s = window.location.search;
    s = s.match(new RegExp(name + '=([^&=]+)'));
    return s ? s[1] : false;
}

function goToList() {
    document.location.href = path_to_list;
}

const MultiAddress = function () {
    this.node = document.querySelector('.multi-address');
    if (this.node) { 
        this.listAddress = this.node.querySelector('.multi-address-items');
        this.countAddress = this.listAddress.children.length;
        this.btnAddAddress = this.node.querySelector('[data-action="add-address"]');

        document.body.append(document.querySelector('#template-address-modal').content);

        this.modalAddAddress = new bootstrap.Modal(document.querySelector('#multi-address-modal'));
        this.formModalAddress = this.modalAddAddress._element.querySelector('form[name="add-address"]');
    }
};

MultiAddress.prototype = {
    init: function(){
        if (this.node) {
            this.node.addEventListener('click', function(event) {
                const btn = event.target.closest('[data-action]');
    
                if (!btn)
                    return;
                
                switch (btn.dataset.action) {
                    case 'edit':
                        this.editAddress(btn);
                        break;
                    case 'delete':
                        this.deleteAddress(btn);
                        break;
                }
            }.bind(this));
    
            this.btnAddAddress.addEventListener('click', function() {
                this.modalAddAddress._element.querySelector('[name="action"]').value = 'add';
                this.modalAddAddress._element.querySelectorAll('input.form-control').forEach(item => item.value = '');
                this.modalAddAddress._element.querySelector('button[type="submit"]').innerHTML = BX.message('SOA_MULTIPLE_BTN');
            }.bind(this));
            this.modalAddAddress._element.querySelector('form[name="add-address"]')?.addEventListener('submit', this.eventChangeAddress.bind(this));
        }
    },
    
    editAddress: function (btn) {
        const parentNode = btn.closest('.position-relative');
        const nodeFullAddress = parentNode.querySelector('input');
        const modalInputsAddress = this.modalAddAddress._element.querySelectorAll('input.form-control')
        const arrAddress = this.splitAddress(nodeFullAddress.value);
    
        modalInputsAddress.forEach((node, key) => {
            node.value = arrAddress[key] ?? '';
        });
    
        this.modalAddAddress._element.querySelector('[name="action"]').value = 'edit';
        this.modalAddAddress._element.querySelector('[name="address-key"]').value = nodeFullAddress.dataset.key;
        this.modalAddAddress._element.querySelector('button[type="submit"]').innerHTML = BX.message('SOA_MULTIPLE_EDIT_BTN');
        this.modalAddAddress.show();
    },
    
    deleteAddress: function (btn) {
        btn.closest('.position-relative').remove();

        if (!this.node.querySelector('.multi-address-items').children.length) {
            this.node.querySelectorAll('[type="hidden"]').forEach(item => item.value = '');
        }
    },
    
    eventChangeAddress: function (event) {
        event.preventDefault();
        const inputsAddress = this.formModalAddress.querySelectorAll('input.form-control');
    
        switch (this.formModalAddress.querySelector('[name="action"]').value) {
            case 'add':
                if (!this.listAddress.children.length) {
                    const propAddress = this.node.querySelectorAll('[type="hidden"]');
                    inputsAddress.forEach((item, key) => {propAddress[key].value = item.value});
                }
                this.listAddress.append(this.createAddress(this.joinAddress(inputsAddress)));
                break;
            case 'edit':
                const idAddress = this.formModalAddress.querySelector('[name="address-key"]').value;
                this.listAddress.querySelector(`.form-control[data-key="${idAddress}"]`).value = this.joinAddress(inputsAddress);
                break;
        }

        this.formModalAddress.querySelector('[data-bs-dismiss]').click();
    },
    
    createAddress: function (address) {
        return BX.create('div', {
            props: {
                className: 'position-relative mb-2'
            },
            children: [
                BX.create('input', {
                    props: {
                        className: 'form-control pe-6',
                        name: 'multi_address[]',
                        value: address
                    },
                    dataset: {
                        key: this.countAddress++
                    }
                }),
                BX.create('div', {
                    props: {
                        className: 'position-absolute end-0 top-50 translate-middle-y me-1 d-flex'
                    },
                    children: [
                        BX.create('button', {
                            props: {
                                className: 'btn btn-sm btn-link btn-icon bg-transparent text-muted',
                                type: 'button',
                                title: BX.message('SOA_MULTIPLE_EDIT_BTN')
                            },
                            dataset: {
                                action: 'edit'
                            },
                            children: [BX.create('i', {
                                props: {
                                    className: 'ph-pencil-simple fs-base'
                                }
                            })]
                        }),
                        BX.create('button', {
                            props: {
                                className: 'btn btn-sm btn-link btn-icon bg-transparent text-muted',
                                type: 'button',
                                title: BX.message('SOA_MULTIPLE_DELETE_BTN')
                            },
                            dataset: {
                                action: 'delete'
                            },
                            children: [BX.create('i', {
                                props: {
                                    className: 'ph-x fs-base'
                                }
                            })]
                        })
                    ]
                })
            ]
        });
    },
    
    joinAddress: function (nodesAddress) {
        return [].map.call(nodesAddress, (item) => item.value).join('; ');
    },
    
    splitAddress: function (address) {
        return address.split(';').map(item => item.trim());
    }
}

function hideBlock(evBlock) {
    BX.remove(evBlock.closest(".form-control-multiple-wrap"));
}

BX.ready(function () {
    new MultiAddress().init();

    const multiList = document.querySelectorAll('.multiple-props button');
    if (!multiList) { return;}
    for (let key in multiList) {
        BX.bind(multiList[key], 'click', BX.delegate(
            function(event) {
                if (!BX.type.isDomNode(event.target))
                    return;

                let newInput = BX.create('input',{attrs:{
                        className: 'form-control mb-2',
                        type: 'text',
                        name: event.target.getAttribute('data-add-name'),
                        maxlength: event.target.getAttribute('data-add-maxlength')
                    }});
                
                let newButWrap = BX.create('div', {
                    attrs: {
                        className: 'form-control-multiple position-absolute end-0 top-50 translate-middle-y me-1',
                        onclick: "hideBlock(this)"
                    },
                    children:[
                        BX.create('button', {
                            attrs: {
                                className: 'form-control-multiple-ic btn btn-sm btn-icon btn-link text-muted',
                                type: 'button',
                            },
                            children:[
                                BX.create('i', {
                                    attrs: {
                                        className: 'ph-x fs-base'
                                    }
                                })
                            ]
                        })
                    ]
                });

                let newAllWrap = BX.create('div', {
                    attrs: {
                        className: 'form-control-multiple-wrap'
                    },
                    children:[newInput, newButWrap]
                });

                event.target.parentNode.insertBefore(newAllWrap, event.target);
            }
        ));
    }

    const contentPersonalGroup = document.querySelectorAll('.tab-personal-group');
    if (contentPersonalGroup.length !== 0) {
        contentPersonalGroup.forEach(item => {
            if (item.querySelector('.form-check-input[name="PERSON_TYPE"]:checked') && !item.querySelector('.form-check-input[name="PERSON_TYPE"]:checked')) {
                item.querySelector('.form-check-input[name="PERSON_TYPE"]').checked = true;
            }
        })
    }
});