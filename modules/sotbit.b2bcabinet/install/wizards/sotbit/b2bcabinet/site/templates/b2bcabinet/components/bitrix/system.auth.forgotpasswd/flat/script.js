function reloadCaptcha(e,siteDir){
    let img = $(e).closest('.password_recovery-captcha').find('img');
    let sid = $(e).closest('.card-body').find('[name="captcha_sid"]');
    console.log(img);
    console.log(sid);
    if(img !== undefined){
        $.getJSON(siteDir+'include/ajax/reload_captcha.php', function(data) {
            img.attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
            sid.val(data);
        });
    }
}
