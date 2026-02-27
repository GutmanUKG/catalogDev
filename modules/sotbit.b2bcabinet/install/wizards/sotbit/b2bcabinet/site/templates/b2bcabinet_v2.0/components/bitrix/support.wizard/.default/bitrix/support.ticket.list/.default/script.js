function openModal(name) {
    event.preventDefault();
    BX.showWait();

    if (!document.querySelector(`#${name} .modal-body form`)){
        BX.ajax({
            url: event.target.getAttribute('href'),
            method: 'get',
            onsuccess: function(response) {
                BX.closeWait();
                document.querySelector(`#${name} .modal-body`).innerHTML = response;
                document.body.append(document.getElementById(name));
                $(`#${name}`).modal('show');
            },
            onfailure: function(error) {
                alert(error);
            }
        })
        return
    }

    BX.closeWait();
    $(`#${name}`).modal('show');
}