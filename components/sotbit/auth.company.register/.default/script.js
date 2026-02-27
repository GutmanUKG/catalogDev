$(document).ready(function () {
    $('.js_person_type .js_checkbox_person_type').click(function () {
        changePersonalBlock(this);
    });

    changePersonalBlock($('.js_person_type .js_checkbox_person_type:checked'));
});

function changePersonalBlock(obj) {
    let index = $('.js_person_type .js_checkbox_person_type').index(obj);
    $('.js_person_type .js_person_type_block').hide();
    $('.js_person_type .js_person_type_block').eq(index).show();
}

function sendForm(e) {
    BX.showWait();
    const registerForm = document.querySelector('.js_person_type_block[style=""] #company-register');
    const formData = new FormData(registerForm);

    if (!registerForm.querySelector('input[name="'+companyRegisterAgreementInput+'"]').checked) {
        BX.closeWait();
        return;
    }
    const errorBlock = document.querySelector(".bitrix-error");
    let confirmJoin = registerForm.querySelector("#CONFIRM_JOIN");

    var request = BX.ajax.runComponentAction('sotbit:auth.company.register', 'registerCompany', {
        signedParameters: window.arParams,
        mode: 'class',
        data: formData,
    });
    request.then(function (response) {
        BX.closeWait();
        if (response.data.errors) {
            console.log(response.data.errors)
            errorBlock.innerHTML = response.data.errors;
            document.querySelector('.content').scroll({
                top:0,
                left: 0,
                behavior: "smooth",
            });

            const captchaSid  = e.querySelector('#captcha_sid');
            const captchaImg  = e.querySelector('.password_recovery-captcha_wrap img');
            const captchaInput  = e.querySelector('.password_recovery-captcha_input input');

            if (captchaImg) {
                $.getJSON(siteDir+'include/ajax/reload_captcha.php', function(data) {
                    captchaImg.setAttribute('src','/bitrix/tools/captcha.php?captcha_sid='+data);
                    captchaSid.value = data;
                });
                captchaInput.value = "";
            }
        }
        if (response.data == "COMPANY_ISSET") {
            let confirmResult = confirm(confirmModerationMsg);
            if (confirmResult == false) {
                confirmJoin.value = "N";
            } else {
                confirmJoin.value = "Y";
                sendForm(e);
            }
        } else {
            if (response.data.message) {
                document.querySelector('.auth-form').remove();
                const successBlock = document.querySelector('.company-register__success-form');
                successBlock.querySelector(".btn-primary").insertAdjacentHTML('beforebegin', response.data.message);
                successBlock.style.display = "flex";
            }
        }
    });
}

function hideBlock(evBlock) {
    BX.remove(evBlock.closest(".form-control-multiple-wrap"));
}

BX.ready(function () {
    var multiList = document.querySelectorAll('.multiple-props .btn');

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
                        type: event.target.getAttribute('data-add-type'),
                        name: event.target.getAttribute('data-add-name'),
                        placeholder: event.target.getAttribute('data-add-placeholder'),
                        minlength: event.target.getAttribute('data-add-minlength'),
                        maxlength: event.target.getAttribute('data-add-maxlength'),
                    }
                });

                var newButWrap = BX.create('div', {
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
                var newAllWrap = BX.create('div', {
                    attrs: {
                        className: 'form-control-multiple-wrap'
                    },
                    children:[newInput, newButWrap]
                });

                event.target.parentNode.insertBefore(newAllWrap, event.target);
            }
        ));
    }
});
function reloadCaptcha(e, siteDir){
    const captchaBlock = e.closest('.password_recovery-captcha_wrap');
    if (!captchaBlock) {
        return;
    }

    const iconButton = e.querySelector('i');
    const captchaImg = captchaBlock.querySelector('img');
    const captchaSid = document.getElementById('captcha_sid');
    captchaImg.classList.add('disabled');
    iconButton.classList.add('spinner-grow');

    $.getJSON(siteDir+'include/ajax/reload_captcha.php', data => {
        captchaImg.setAttribute('src', '/bitrix/tools/captcha.php?captcha_sid='+data);
        captchaSid.value = data;

        captchaImg.onload = function() {
            captchaImg.classList.remove('disabled');
            iconButton.classList.remove('spinner-grow');
        }
    });
}
