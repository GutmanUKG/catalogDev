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
        BX.ready(BX.delegate(function() {
            this.init();
            this.initChange();
        },this));
    };
    window.JCBlankZakaza.prototype = {
        init: function() {
            this.node = document.getElementById(this.obName);
            this.wrapper = document.getElementById(this.obName + "_wrapper");

            this.initHeaderSort();
            // this.initSidePanelDetailPage();
            // this.initEars();
            this.initFullScreen();
            this.initPagination();
            // this.initHeight();
            this.initScroll();
            this.initDetailLink();
        },

        initFullScreen: function () {
            this.wrapper.querySelector('a[data-action="fullscreen"]')?.addEventListener('click', this.clickFullScreen.bind(this));
        },
        initPagination: function () {
            const nodePaginations = document.querySelectorAll('.pagination');

            nodePaginations.forEach((item) => {
                item.addEventListener('click', ()=> {
                    if (!event.target.closest('.page-link')) return;

                    window.history.pushState(null, null, event.target.href);
                })
            })
        },
        clickFullScreen: function () {
            event.stopPropagation();

            const cardFullscreen = event.target.closest('.catalog__section-wrapper');
            const cardFullscreenClass = 'card-fullscreen';
            const InitOrDestroyScroll = cardFullscreen.classList.contains(cardFullscreenClass) ? 'init': 'destroy';

            // Toggle required classes
            cardFullscreen.classList.toggle(cardFullscreenClass);
            cardFullscreen.classList.toggle('m-0');
            cardFullscreen.classList.toggle('h-100');
            cardFullscreen.querySelector('.catalog__search').classList.toggle('d-none')

            $(this.wrapper).floatingScroll(InitOrDestroyScroll);

            $.ajax({
                url: '/ajax/catalog-viewer.php',
                method: 'get',
                dataType: 'text',
                data: {
                    FULLSCREEN: "Y"
                },
                success: function(data){
                    console.log(data);
                }
            });

            // var card = $(document).find('#card__catalog__section-wrapper');
            // if(!card.hasClass('card-fullscreen')) {
            //     $.cookie('fullscreen', 'off');
            // }
            // else {
            //     $.cookie('fullscreen', 'on');
            // }

            // console.log($.cookie());
        },
        initHeaderSort: function() {
            if (!this.node) {
                return;
            }

            this.node.addEventListener('click', function() {
                if(!event.target.closest('[data-property-code]')) return;

                getParamsSort.call(this, event.target.closest('[data-property-code]'));

            }.bind(this));

            function getParamsSort(node) {
                let order = 'asc,nulls',
                    code;
                const url = new URL(location);
                order = url.searchParams.get('SORT[ORDER]') === order ? 'desc,nulls' : 'asc,nulls';

                if (node.dataset.propertyCode === 'QUANTITY') return;
                if (node.dataset.propertyCode === 'OFFERS') return;

                if (OrderingrKeys[node.dataset.propertyCode]) {
                    code = OrderingrKeys[node.dataset.propertyCode];
                } else {
                    code = node.dataset.propertyCode;
                }

                url.searchParams.set('SORT[CODE]', code);
                url.searchParams.set('SORT[ORDER]', order);

                BX.showWait();

                if (this.arParams.AJAX_MODE == 'Y') {
                    BX.ajax({
                        url: url.href + '&bxajaxid=' +this.arParams.AJAX_ID,
                        method: 'GET',
                        daraType: 'json',
                        async: true,
                        onsuccess: BX.delegate(function(responce) {
                            window.history.pushState(null, null, url.href);
                            document.querySelector('#comp_'+this.arParams.AJAX_ID).innerHTML = responce;
                            BX.closeWait();
                        }, this)
                    })
                } else {
                    location.search = url.search;
                }
            }
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
        },
        initChange: function() {
            if (this.arParams.AJAX_MODE == 'N') return;

            const observerContainer = new MutationObserver(this.init.bind(this));
            observerContainer.observe(document.getElementById('comp_'+this.arParams.AJAX_ID), {
                childList: true
            });
        },
        initHeight: function () {
            const wrapperTop = this.wrapper.getBoundingClientRect().top;
            const footerHeight = document.querySelector('.catalog__footer').clientHeight;
            const navbarHeight = document.querySelector('.navbar-footer').clientHeight;
            this.wrapper.style.height = document.body.clientHeight - wrapperTop - footerHeight - navbarHeight - 16 +'px';
        },
        initScroll: function () {
            $(this.wrapper).floatingScroll();
        },
        initDetailLink: function () {
            this.wrapper.addEventListener('click', function(event) {
                let linkProduct = null;
                if (linkProduct = event.target.closest('.product__link')) {
                    event.preventDefault();
                    BX.SidePanel.Instance.open(
                        linkProduct.dataset.href,
                        {
                            width: 1344,
                            label: {
                                text: "",
                                color: "#9E9E9E",
                                bgColor: "transparent",
                                opacity: 80
                            }
                        }
                    );
                }
            })
        }
    };

    var OrderingrKeys = {
        'NAME': 'NAME',
        'AVALIABLE': 'QUANTITY',
        'MEASURE': 'MEASURE',
    };
})(window);



(function() {
    'use strict';

    if (!!window.JCCatalogSectionComponent)
        return;

    window.JCCatalogSectionComponent = function(params) {
        this.formPosting = false;
        this.siteId = params.siteId || '';
        this.ajaxId = params.ajaxId || '';
        this.template = params.template || '';
        this.componentPath = params.componentPath || '';
        this.parameters = params.parameters || '';

        if (params.navParams)
        {
            this.navParams = {
                NavNum: params.navParams.NavNum || 1,
                NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
                NavPageCount: parseInt(params.navParams.NavPageCount) || 1
            };
        }

        this.bigData = params.bigData || {enabled: false};
        this.container = document.querySelector('[data-entity="' + params.container + '"]');
        this.showMoreButton = null;
        this.showMoreButtonMessage = null;

        if (this.bigData.enabled && BX.util.object_keys(this.bigData.rows).length > 0)
        {
            BX.cookie_prefix = this.bigData.js.cookiePrefix || '';
            BX.cookie_domain = this.bigData.js.cookieDomain || '';
            BX.current_server_time = this.bigData.js.serverTime;

            BX.ready(BX.delegate(this.bigDataLoad, this));
        }

        if (params.deferredLoad)
        {
            BX.ready(BX.delegate(this.deferredLoad, this));
        }

        if (params.lazyLoad)
        {
            this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');
            this.showMoreButtonMessage = this.showMoreButton.innerHTML;
            BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
        }

        if (params.loadOnScroll)
        {
            BX.bind(window, 'scroll', BX.proxy(this.loadOnScroll, this));
        }
    };

    window.JCCatalogSectionComponent.prototype =
        {
            checkButton: function()
            {
                if (this.showMoreButton)
                {
                    if (this.navParams.NavPageNomer == this.navParams.NavPageCount)
                    {
                        BX.remove(this.showMoreButton);
                    }
                    else
                    {
                        this.container.closest('.blank_zakaza').appendChild(this.showMoreButton);
                    }
                }
            },

            enableButton: function()
            {
                if (this.showMoreButton)
                {
                    BX.removeClass(this.showMoreButton, 'load');
                    this.showMoreButton.innerHTML = this.showMoreButtonMessage;
                }
            },

            disableButton: function()
            {
                if (this.showMoreButton)
                {
                    BX.addClass(this.showMoreButton, 'load');
                    this.showMoreButton.innerHTML = BX.message('BTN_MESSAGE_LAZY_LOAD_WAITER');
                }
            },

            loadOnScroll: function()
            {
                var scrollTop = BX.GetWindowScrollPos().scrollTop,
                    containerBottom = BX.pos(this.container).bottom;

                if (scrollTop + window.innerHeight > containerBottom)
                {
                    this.showMore();
                }
            },

            showMore: function()
            {
                if (this.navParams.NavPageNomer < this.navParams.NavPageCount)
                {
                    var data = {};
                    data['action'] = 'showMore';
                    data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

                    if (!this.formPosting)
                    {
                        this.formPosting = true;
                        this.disableButton();
                        this.sendRequest(data);
                    }
                }
            },

            bigDataLoad: function()
            {
                var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php',
                    data = BX.ajax.prepareData(this.bigData.params);

                if (data)
                {
                    url += (url.indexOf('?') !== -1 ? '&' : '?') + data;
                }

                var onReady = BX.delegate(function(result){
                    this.sendRequest({
                        action: 'deferredLoad',
                        bigData: 'Y',
                        items: result && result.items || [],
                        rid: result && result.id,
                        count: this.bigData.count,
                        rowsRange: this.bigData.rowsRange,
                        shownIds: this.bigData.shownIds
                    });
                }, this);

                BX.ajax({
                    method: 'GET',
                    dataType: 'json',
                    url: url,
                    timeout: 3,
                    onsuccess: onReady,
                    onfailure: onReady
                });
            },

            deferredLoad: function()
            {
                this.sendRequest({action: 'deferredLoad'});
            },

            sendRequest: function(data)
            {
                var defaultData = {
                    siteId: this.siteId,
                    template: this.template,
                    parameters: this.parameters
                };

                if (this.ajaxId)
                {
                    defaultData.AJAX_ID = this.ajaxId;
                }

                BX.ajax({
                    url: this.componentPath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
                    method: 'POST',
                    dataType: 'json',
                    timeout: 60,
                    data: BX.merge(defaultData, data),
                    onsuccess: BX.delegate(function(result){
                        if (!result || !result.JS)
                            return;

                        BX.ajax.processScripts(
                            BX.processHTML(result.JS).SCRIPT,
                            false,
                            BX.delegate(function(){this.showAction(result, data);}, this)
                        );
                    }, this)
                });
            },

            showAction: function(result, data)
            {
                if (!data)
                    return;

                switch (data.action)
                {
                    case 'showMore':
                        this.processShowMoreAction(result);
                        break;
                    case 'deferredLoad':
                        this.processDeferredLoadAction(result, data.bigData === 'Y');
                        break;
                }
            },

            processShowMoreAction: function(result)
            {
                this.formPosting = false;
                this.enableButton();

                if (result)
                {
                    this.navParams.NavPageNomer++;
                    this.processItems(result.items);
                    this.processPagination(result.pagination);
                    this.processEpilogue(result.epilogue);
                    this.checkButton();
                }
            },

            processDeferredLoadAction: function(result, bigData)
            {
                if (!result)
                    return;

                var position = bigData ? this.bigData.rows : {};

                this.processItems(result.items, BX.util.array_keys(position));
            },

            processItems: function(itemsHtml, position)
            {
                if (!itemsHtml)
                    return;


                var processed = BX.processHTML(itemsHtml, false),
                    temporaryNode = BX.create('TABLE');

                var items, k, origRows;

                temporaryNode.innerHTML = processed.HTML;
                items = temporaryNode.querySelectorAll('[data-entity="items-row"]');

                if (items.length)
                {

                    for (k in items)
                    {
                        if (items.hasOwnProperty(k))
                        {
                            origRows = position ? this.container.querySelectorAll('[data-entity="items-row"]') : false;
                            items[k].style.opacity = 0;

                            if (origRows && BX.type.isDomNode(origRows[position[k]]))
                            {
                                origRows[position[k]].parentNode.insertBefore(items[k], origRows[position[k]]);
                            }
                            else
                            {
                                this.container.appendChild(items[k]);
                            }
                        }
                    }

                    new BX.easing({
                        duration: 2000,
                        start: {opacity: 0},
                        finish: {opacity: 100},
                        transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
                        step: function(state){
                            for (var k in items)
                            {
                                if (items.hasOwnProperty(k))
                                {
                                    items[k].style.opacity = state.opacity / 100;
                                }
                            }
                        },
                        complete: function(){
                            for (var k in items)
                            {
                                if (items.hasOwnProperty(k))
                                {
                                    items[k].removeAttribute('style');
                                }
                            }
                        }
                    }).animate();
                }

                BX.ajax.processScripts(processed.SCRIPT);
            },

            processPagination: function(paginationHtml)
            {
                if (!paginationHtml)
                    return;

                var pagination = document.querySelectorAll('[data-pagination-num="' + this.navParams.NavNum + '"]');
                for (var k in pagination)
                {
                    if (pagination.hasOwnProperty(k))
                    {
                        pagination[k].innerHTML = paginationHtml;
                    }
                }
            },

            processEpilogue: function(epilogueHtml)
            {
                if (!epilogueHtml)
                    return;

                var processed = BX.processHTML(epilogueHtml, false);
                BX.ajax.processScripts(processed.SCRIPT);
            }
        };
})();