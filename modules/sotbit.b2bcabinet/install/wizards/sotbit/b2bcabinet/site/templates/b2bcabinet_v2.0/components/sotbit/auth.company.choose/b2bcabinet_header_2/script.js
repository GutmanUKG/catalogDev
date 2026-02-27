function setCompanyID(companyId) {
    BX.showWait();
    var request = BX.ajax.runComponentAction('sotbit:auth.company.choose', 'changeCompany', {
        mode: 'class',
        data: {
            companyID: companyId
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
            window.location.reload(false);
        } else {
            if(response.data.companyId){
                let node = document.querySelector(".auth-company-change__item[data-company-id='"+ response.data.companyId +"']");
                if(node){
                    node.classList.add("error-company");
                    node.removeAttribute("onclick");
                }
            }
        }
        BX.closeWait();
    });
}


