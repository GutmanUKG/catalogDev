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


function hideBlock(evBlock) {
    BX.remove(evBlock.closest(".form-control-multiple-wrap"));
}

BX.ready(function () {
    var multiList = document.querySelectorAll('.multiple-props .btn-light');

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
                        placeholder: event.target.getAttribute('data-add-placeholder'),
                        minlength: event.target.getAttribute('data-add-minlength'),
                        maxlength: event.target.getAttribute('data-add-maxlength'),
                    }
                });

                var newButWrap = BX.create('div', {
                    attrs: {
                        className: 'form-control-multiple',
                        onclick: "hideBlock(this)"
                    },
                    children: [
                        BX.create('button', {
                            attrs: {
                                className: 'form-control-multiple-ic btn btn-link',
                                type: 'submit',
                            },
                            children: [
                                BX.create('i', {
                                    attrs: {
                                        className: 'icon-close2'
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
                    children: [newInput, newButWrap]
                });

                event.target.parentNode.insertBefore(newAllWrap, event.target);
            }
        ));
    }
});

function reloadCaptcha(e, siteDir) {
    const captchaBlock = e.closest('.password_recovery-captcha_wrap');
    if (captchaBlock === undefined) {
        return;
    }
    e.style.display = 'none';

    const captchaImg = captchaBlock.querySelector('img');
    const captchaSid = document.getElementById('captcha_sid');
    captchaImg.classList.add('disabled');

    $.getJSON(siteDir+'include/ajax/reload_captcha.php', data => {
        captchaImg.src = '/bitrix/tools/captcha.php?captcha_sid='+data;
        captchaSid.value = data;

        captchaImg.onload = function() {
            captchaImg.classList.remove('disabled');
            e.style.display = 'block';
        };
    });
}