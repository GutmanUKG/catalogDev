function reloadCaptcha(e,siteDir){
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
        captchaImg.setAttribute('src','/bitrix/tools/captcha.php?captcha_sid='+data);
        captchaSid.value = data;

        captchaImg.onload = function() {
            captchaImg.classList.remove('disabled');
            iconButton.classList.remove('spinner-grow');
        }
    });
}
