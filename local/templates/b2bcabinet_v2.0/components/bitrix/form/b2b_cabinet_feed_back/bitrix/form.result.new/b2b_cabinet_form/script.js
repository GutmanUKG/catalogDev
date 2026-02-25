$(document).ready(function () {
    closeFabMenuWhenModalClose();
});

function closeFabMenuWhenModalClose() {
    let modalManager = document.getElementById("modal_manager");

    $(modalManager).on('hide.bs.modal', function () {
        document.querySelector('.personal-manager-button .fab-menu-btn').click();
    })
}