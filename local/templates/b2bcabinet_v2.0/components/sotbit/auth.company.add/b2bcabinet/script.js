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

BX.ready(function () {
    var multiList = document.querySelectorAll('.multiple-props button');
    if (!multiList) { return;}
    for (var key in multiList) {
        BX.bind(multiList[key], 'click', BX.delegate(
            function(event) {
                if (!BX.type.isDomNode(event.target))
                    return;

                var newInput = BX.create('input',{attrs:{
                        className: 'form-control mb-2',
                        type: 'text',
                        name: event.target.getAttribute('data-add-name'),
                        maxlength: event.target.getAttribute('data-add-maxlength')
                    }});

                event.target.parentNode.insertBefore(newInput, event.target);
            }
        ));
    }

    const contentPersonalGroup = document.querySelectorAll('.tab-personal-group');
    if (contentPersonalGroup.length !== 0) {
        contentPersonalGroup.forEach(item => {
            if (!item.querySelector('.form-check-input[name="PERSON_TYPE"]:checked')) {
                item.querySelector('.form-check-input[name="PERSON_TYPE"]').checked = true;
            }
        })
    }
});