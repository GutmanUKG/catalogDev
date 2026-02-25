$(document).ready(function() {
    SweetAlert.initComponents();
    const joinForm = document.querySelector('#modal_company_join'),
        blockSelect = document.querySelector('.company-join__select-block'),
        errorBlock = document.querySelector('.joinCompany__error-block');

    var sendJoinForm = document.querySelector('[name="company-join-send"]'),
        inputInn = document.querySelector('.join__search-company'),
        resetBtn = document.querySelector('button[type="reset"]');
    if(sendJoinForm){
        sendJoinForm.addEventListener('click', joiningCompany);
    }
    if(inputInn){
        inputInn.addEventListener('input', searchCompany);
    }
    if(resetBtn){
        resetBtn.addEventListener('click', closeModal);
    }

    if (!joinForm) {
        const btnOpenForm = document.querySelector('[data-target="#modal_company_join"]');
        if (btnOpenForm) {
            btnOpenForm.addEventListener('click',  function (){
                SweetAlert.showInfo(BX.message('INFO_TITLE'))});
        }
    }

    function closeModal()
    {
        if(blockSelect) blockSelect.innerHTML = '';
        if(errorBlock) errorBlock.innerHTML = '';
        if(inputInn) inputInn.value = '';
    }

    function searchCompany() {
        blockSelect.innerHTML = '';
        if (this.value === '') return;

        var strSearch = this.value.replace('"', '').toLowerCase();
        const companyList = companyJoin_companyList;
        var currentCompany = {};

        for (let companyId in companyList) {
            if (companyList[companyId]["SEARCH_NAME"]
                .toLowerCase()
                .includes(strSearch)) {
                currentCompany[companyId] = companyList[companyId];
            }
        }

        for (let companyId in currentCompany) {
            var item = document.createElement("DIV");
            item.setAttribute("class", "select__company-item");
            item.setAttribute("data-id", companyId);
            item.innerHTML = companyList[companyId]["PRINT_NAME"] + '<i class="select__company-icon ph-check"></i>';
            item.addEventListener('click', checkCompany);
            blockSelect.appendChild(item);
        }
    }

    function checkCompany() {
        this.classList.toggle("checked");
    }


    function joiningCompany() {
        BX.showWait('modal_company_join-dialog');
        let companyId = [];
        let companyChecked = document.querySelectorAll('.select__company-item.checked');
        for (let i = 0; i < companyChecked.length; i++) {
            companyId[i] = companyChecked[i].getAttribute("data-id");
            delete companyJoin_companyList[companyId[i]];
        }

        let errorBlock = document.querySelector('.joinCompany__error-block');

        var request = BX.ajax.runComponentAction('sotbit:auth.company.join', 'joiningCompany', {
            mode: 'class',
            data: {JOIN_COMPANY_ID: companyId }
        });

        request.then(function (response) {
            if (response.data.error === false) {
                resetBtn.click();
                SweetAlert.showSuccess(BX.message('SUCCESS_TITLE'), BX.message('SUCCESS_TEXT'));
            } else {
                if(response.data.errorMessage){
                    errorBlock.innerHTML = '<div class="bitrix-error"><label class="validation-invalid-label errortext">' + response.data.errorMessage+ '</label></div>';
                }
            }
            BX.closeWait();
        });
    }
});