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

BX.ready(function () {
    var multiList = document.querySelectorAll('.multiple-props button');
    if (!multiList) {
        return;
    }
    for (var key in multiList) {
        BX.bind(multiList[key], 'click', BX.delegate(
            function (event) {
                if (!BX.type.isDomNode(event.target))
                    return;

                var newInput = BX.create('input', {
                    attrs: {
                        className: 'form-control mb-2',
                        type: 'text',
                        name: event.target.getAttribute('data-add-name'),
                        class: event.target.getAttribute('data-add-class'),
                        maxlength: event.target.getAttribute('data-add-maxlength'),
                    }
                });

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
