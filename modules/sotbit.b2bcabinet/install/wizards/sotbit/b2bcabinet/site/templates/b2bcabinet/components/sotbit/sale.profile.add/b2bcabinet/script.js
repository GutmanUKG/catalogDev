$(document).on("change keyup input", "#INN", function(){
	var val = $(this).val();
	$('#NAME').val(val);
});
$(document).on("change", "#person-type", function(){
    var post = 'change_person_type=' + this.value;
    if(this.value !== '') {
        $('#change_person_type').val(true);
        $('#PERSON_TYPE').val(this.value);
    }
});

BX.ready(function () {
    var multiList = document.querySelectorAll('button.add-multiple-props');
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
                        className: 'form-control',
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
});
