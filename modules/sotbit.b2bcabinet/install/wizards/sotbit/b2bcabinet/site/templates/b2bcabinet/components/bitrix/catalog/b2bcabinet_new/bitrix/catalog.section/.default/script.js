;(function(window){
    'use strict';

	if (window.JCBlankZakaza)
		return;

	window.JCBlankZakaza = function (obName, arParams)
	{
        this.arParams = arParams;
        this.obName = obName;
        this.node = null;
        this.wrapper = null;
        BX.ready(BX.delegate(this.init,this));

    };
    window.JCBlankZakaza.prototype = {
        init: function() {
            this.node = document.getElementById(this.obName);
            this.wrapper = document.getElementById(this.obName + "_wrapper");

            this.initHeaderSort();
            // this.initSidePanelDetailPage();
            this.initEars();
            this.initFullScreen();


        },
        initFullScreen: function () {
            this.wrapper.querySelector('.blank-zakaza__header-fullscreen a')?.addEventListener('click', this.clickFullScreen.bind(this));
        },
        clickFullScreen: function () {
            if (!this.catalogSectionEars) {
                setTimeout(()=>this.clickFullScreen, 0);
            } else {
                setTimeout(() =>  this.catalogSectionEars.toggleEars(), 0);
            }
        },
        initHeaderSort: function() {
            if (!this.node) {
                return;
            }

            const sortNodes = Array.prototype.slice.call(this.node.querySelectorAll('[data-property-code]'));
            sortNodes.forEach(function(node) {
                var code;
                if (node.dataset.propertyCode === 'QUANTITY') {
                    return;
                }
                if (OrderingrKeys[node.dataset.propertyCode]) {
                    code = OrderingrKeys[node.dataset.propertyCode];
                } else {
                    code = node.dataset.propertyCode;
                }

                node.onclick = function() {
                    let order = 'asc,nulls';
                    location.search.split('&').forEach(function(param) {
                        let get = param.split('=');
                        order = get[0] === 'SORT[ORDER]' && order === get[1] ? 'desc,nulls' : 'asc,nulls';
                    })
                    let get = location.search.split('&').filter(function(param) {
                        return param.lastIndexOf('SORT', 0) !== 0;
                    }).join('&');
                    location.search = get + '&SORT[CODE]=' + code + '&SORT[ORDER]=' + order;
                }
            });
        },
        initSidePanelDetailPage: function() {
            if (this.arParams.AJAX_MODE === 'Y') {return}

            BX.loadExt('sidepanel').then(function() {
                BX.SidePanel.Instance.bindAnchors({
                    rules:
                    [
                        {
                            condition: [
                                new RegExp(location.pathname + '\?\\S*' + this.arParams.ELEMENT_ID_VARIABLE + '=[0-9]+','i'),
                                new RegExp(location.pathname + '\?\\S*' + '([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)','i')
                            ],
                            stopParameters: [
                                "PAGEN_(\\d+)"
                            ],
                            options: {
                                width: 1344,
                                label: {
                                    text: "",
                                    color: "#FFFFFF",
                                    bgColor: "#3e495f",
                                    opacity: 80
                                }
                            }
                        }
                    ],
                    handler: function(event, link)
                    {
                        event.preventDefault();
                        BX.SidePanel.Instance.open(link, {});
                    }
                })
            }.bind(this))
        },
        initEars: function() {
            BX.loadExt('ui.ears').then(function(){
                this.catalogSectionEars = new BX.UI.Ears({
                    container: this.wrapper,
                    smallSize: true,
                    noScrollbar: true,
                    className: 'blank-zakaza__ears',
                })
                this.catalogSectionEars.onWheel = function() {};
                this.catalogSectionEars.init();
            }.bind(this));
        }
    };

    var OrderingrKeys = {
        'NAME': 'NAME',
        'AVALIABLE': 'QUANTITY',
        'MEASURE': 'MEASURE',
    };
})(window);