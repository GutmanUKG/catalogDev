BX.ready(function() {
    const node = document.getElementById("b2b-notifications");
    const listNode = node.querySelector('.b2b-notifications__list');
    const icons = {
        warning: '<i class="ph-x-circle"></i>',
        hint: '<i class="ph-info"></i>',
        alert: '<i class="ph-warning-circle"></i>',
        success: '<i class="ph-check">',
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
                    html: icons[type] || '<i class="ph-info"></i>'
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
                    html: '<button class="b2b-notification__close-button"><i class="ph-x"></i></button>',
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