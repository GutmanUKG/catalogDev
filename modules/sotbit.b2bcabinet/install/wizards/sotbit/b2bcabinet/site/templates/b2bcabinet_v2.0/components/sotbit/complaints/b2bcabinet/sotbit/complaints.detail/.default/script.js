document.addEventListener('DOMContentLoaded', function () {
    clickLastActiveTab();
    const tabLinkList = document.querySelectorAll('.complaint-detail__menu .nav-link');

    if (tabLinkList) {
        for (var i=0; i < tabLinkList.length; i++)
        {
            BX.bind(tabLinkList[i], 'click', BX.delegate(function(e) {
                writeActiveTab(e.target.getAttribute('href'));
            }));
        }
    }

    function writeActiveTab(activeTab) {
        document.cookie = "active-tab=" + activeTab;
    }

    function clickLastActiveTab() {
        const lastActiveTab = getActiveTabFromCookie();
        $('a[href="' + lastActiveTab + '"]').click();
    }

    function getActiveTabFromCookie() {
        const name = 'active-tab';
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
});