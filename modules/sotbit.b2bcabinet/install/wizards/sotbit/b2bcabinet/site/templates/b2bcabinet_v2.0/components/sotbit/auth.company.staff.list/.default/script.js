function logInUser($userId) {
    var request = BX.ajax.runComponentAction('sotbit:company.staff.list', 'logInUser', {
        mode: 'class',
        data: {
            userId: $userId
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
           window.location.replace(window.location.origin + window.location.pathname);
        } else {
            console.log(response.data.errorMessage);
        }
    });
}

function removeUserCompany(userTableId, companyId) {
    SweetAlert.confirm(BX.message('QUESTION_DELETE'), actionRemoveUserCompany.bind(this, userTableId, companyId))
}

function actionRemoveUserCompany(userTableId, companyId) {
    var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.list', 'removeUserCompany', {
        mode: 'class',
        data: {
            userTableId: userTableId,
            companyId: companyId,
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
            BX.Main.gridManager.reload('STAFF_LIST','');
        } else {
            console.log(response.data.errorMessage);
        }
    });
}

function confirmUser(userTableId, companyId) {
    var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.list', 'confirmUser', {
        mode: 'class',
        data: {
            userTableId: userTableId,
            companyId: companyId,
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
            SweetAlert.showSuccess(BX.message("SUCCESS_CONFIRM_TEXT"));
            BX.Main.gridManager.reload('STAFF_UNCONFIRMED_LIST','');
            BX.Main.gridManager.reload('STAFF_LIST','');
        } else {
            console.log(response.data.errorMessage);
        }
    });
}

function unconfirmUser(userTableId, companyId) {
    var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.list', 'unconfirmUser', {
        mode: 'class',
        data: {
            userTableId: userTableId,
            companyId: companyId,
        }
    });

    request.then(function (response) {
        if (response.data.error === false) {
            SweetAlert.showSuccess(BX.message("SUCCESS_UNCONFIRM_TEXT"));
            BX.Main.gridManager.reload('STAFF_UNCONFIRMED_LIST','');
        } else {
            console.log(response.data.errorMessage);
        }
    });
}

function showAllUsers() {
    var request = BX.ajax.runComponentAction('sotbit:auth.company.staff.list', 'showAllUsers', {
        mode: 'class',
    });

    request.then(function (response) {
            BX.Main.gridManager.reload('STAFF_LIST','');
    });
}

document.addEventListener("DOMContentLoaded", function () {
    $('a[data-bs-toggle="tab"]').on('show.bs.tab', function (e) {
        const checkboxShowAllUsers = document.querySelector('#show-all-users');

        if (checkboxShowAllUsers) {
            checkboxShowAllUsers.disabled = false;
            if (e.target.getAttribute('href') === '#basic-tab-request') {
                checkboxShowAllUsers.disabled = true;
            }
        }
      })
});