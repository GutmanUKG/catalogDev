BX.ready(function() {
    const node = document.getElementById("b2b-notifications");
    if (!node) {
        return
    }
    const listNode = node.querySelector('.b2b-notifications__list');
    const icons = {
        warning: '<i class="icon-warning2"></i>',
        hint: '<i class="icon-notification2"></i>',
        alert: '<i class="icon-blocked"></i>',
        success: '<i class="icon-checkmark">',
    }
    
    BX.addCustomEvent('B2BNotification', function (content, type) {
        let item = BX.create('div', {
            props: {
                className: 'b2b-notifications__item b2b-notification b2b-notification--' + type,
            },
            children: [
                BX.create('div', {
                    props: {
                        className: 'b2b-notification__icon',
                    },
                    html: icons[type] || '<i class="icon-notification2"></i>'
                }),
                BX.create('div', {
                    props: {
                        className: 'b2b-notification__content'
                    },
                    html: content
                }),
                BX.create('div', {
                    props: {
                        className: 'b2b-notification__close'
                    },
                    html: '<button class="b2b-notification__close-button"><i class="icon-cross"></i></button>',
                    events: {
                        click: function() {
                            this.parentNode.remove()
                        }
                    }
                })
            ]
        })
        listNode.appendChild(item)
        setTimeout(function() {
            new BX.easing({
                duration: 500,
                start: {opacity: 100, marginTop: 0},
                finish: {opacity: 0, marginTop: -100},
                transition : BX.easing.transitions.quart,
                step : function(state){
                    item.style.opacity = state.opacity/100;
                    item.style.marginTop = state.marginTop + "px";
                },
                complete : function() {
                    item.remove()
                }
            }).animate();
        }, 5000)
    })
})