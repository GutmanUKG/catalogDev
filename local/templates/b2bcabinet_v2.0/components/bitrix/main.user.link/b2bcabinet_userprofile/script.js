document.addEventListener('DOMContentLoaded', function() {
    const url = new URL(window.location.href);
    const linkTabSetting = document.querySelector('.navbar-b2b-mainpage a[href*="settings"]')

    if (url.searchParams.get('tab') === 'settings' && linkTabSetting) {
        linkTabSetting.click();
    }

    document.querySelector('.nav-user a[href*="settings"]')?.addEventListener('click', ()=>{
        event.preventDefault();

        if (!linkTabSetting) {
            window.location.href = event.target.closest('a').getAttribute('href');
            return;
        }

        linkTabSetting.click();
    })
})