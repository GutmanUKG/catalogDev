/* ------------------------------------------------------------------------------
 *
 *  # Dashboard configuration
 *
 *  Demo dashboard configuration. Contains charts and plugin initializations
 *
 * ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------

var Dashboard = function () {


    //
    // Setup module components
    //

    // Use first letter as an icon
    var _componentIconLetter = function() {

        // Grab first letter and insert to the icon
        $('.table tr').each(function() {

            // Title
            var $title = $(this).find('.letter-icon-title'),
                letter = $title.eq(0).text().charAt(0).toUpperCase();

            // Icon
            var $icon = $(this).find('.letter-icon');
                $icon.eq(0).text(letter);
        });
    };

    //
    // Return objects assigned to module
    //

    return {
        initComponents: function() {
            _componentIconLetter();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    Dashboard.initComponents();
});

var lastWait = [];

BX.showWait = function(node, msg)
{
    let positionStyle = node ? 'absolute' : 'fixed';
    node = BX(node) || document.body || document.documentElement;
    msg = msg || BX.message('JS_CORE_LOADING');

    var container_id = node.id || Math.random();

    let preloader = BX.create('DIV', {
        props: {
            id: 'wait_' + container_id
        },
        style: {
            background: 'rgba(255, 255, 255, 0.42)',
            border: 'none',
            color: 'black',
            fontFamily: 'Verdana,Arial,sans-serif',
            fontSize: '11px',
            padding: '0',
            zIndex:'10000',
            textAlign:'center',
            position: positionStyle,
            width: '100%',
            height: '100%'
        },
        text: ""
    });

    preloader.setAttribute("class", "pace-demo");

    let theme_xbox = document.createElement("div");
    let pace_activity = document.createElement("div");

    theme_xbox.setAttribute("class", "theme_xbox");
    pace_activity.setAttribute("class", "pace_activity");

    theme_xbox.appendChild(pace_activity);
    preloader.appendChild(theme_xbox);

    var obMsg = node.bxmsg = node.appendChild(preloader);

    setTimeout(BX.delegate(_adjustWait, node), 10);

    lastWait[lastWait.length] = obMsg;
    return obMsg;
};

function _adjustWait()
{
    if (!this.bxmsg) return;

    var arContainerPos = BX.pos(this),
        div_top = arContainerPos.top;

    if (div_top < BX.GetDocElement().scrollTop)
        div_top = BX.GetDocElement().scrollTop + 5;

    this.bxmsg.style.top = '0px';

    if (this == BX.GetDocElement())
    {
        this.bxmsg.style.right = '0px';
    }
    else
    {
        this.bxmsg.style.left = '0px';
    }
}

BX.closeWait = function(node, obMsg)
{
    if(node && !obMsg)
        obMsg = node.bxmsg;
    if(node && !obMsg && BX.hasClass(node, 'bx-core-waitwindow'))
        obMsg = node;
    if(node && !obMsg)
        obMsg = BX('wait_' + node.id);
    if(!obMsg)
        obMsg = lastWait.pop();

    if (obMsg && obMsg.parentNode)
    {
        for (var i=0,len=lastWait.length;i<len;i++)
        {
            if (obMsg == lastWait[i])
            {
                lastWait = BX.util.deleteFromArray(lastWait, i);
                break;
            }
        }

        obMsg.parentNode.removeChild(obMsg);
        if (node) node.bxmsg = null;
        BX.cleanNode(obMsg, true);
    }
};

window.addEventListener("DOMContentLoaded", function () {
    putBodyUnderAdminPanel();

    if (document.getElementById("bx-panel-hider")) {

        let expandBxPanelButton = document.getElementById("bx-panel-hider"),
            collapseBxPanelButton = document.getElementById("bx-panel-expander"),
            adminPanel = document.getElementById("bx-panel");

        expandBxPanelButton.addEventListener("click", putBodyUnderAdminPanel);
        collapseBxPanelButton.addEventListener("click", putBodyUnderAdminPanel);
        adminPanel.addEventListener("dblclick", putBodyUnderAdminPanel);

        if (document.querySelector(".adm-warning-block")) {
            let warningClose = document.querySelectorAll(".adm-warning-close");

            for (let i = 0; i < warningClose.length; i++) {
                warningClose[i].addEventListener("click", putBodyUnderAdminPanel);
            }
        }

    }
});

window.addEventListener("load", function () {
    putBodyUnderAdminPanel();

    if (document.querySelector(".adm-warning-block")) {
        let warningClose = document.querySelectorAll(".adm-warning-close");

        for (let i = 0; i < warningClose.length; i++) {
            warningClose[i].addEventListener("click", function () {
                setTimeout(putBodyUnderAdminPanel, 600);
            });
        }
    }
});

function putBodyUnderAdminPanel() {
    if (document.getElementById("bx-panel")) {

        let adminPanel = document.getElementById("bx-panel"),
            navBar = document.querySelector(".navbar.fixed-top"),
            navBarTopPosition = adminPanel.clientHeight - document.documentElement.scrollTop,
            sidebarMenu = document.querySelector(".sidebar .b2bcabinet-sidebar");

        if (navBarTopPosition < 0) {
            navBarTopPosition = 0;
        }

        if (navBar) {
            navBar.style.top = navBarTopPosition + "px";
        }

        if (sidebarMenu) {
            // sidebarMenu.style.paddingTop = navBarTopPosition + "px";
        }
    }
}

window.addEventListener("scroll", putBodyUnderAdminPanel);