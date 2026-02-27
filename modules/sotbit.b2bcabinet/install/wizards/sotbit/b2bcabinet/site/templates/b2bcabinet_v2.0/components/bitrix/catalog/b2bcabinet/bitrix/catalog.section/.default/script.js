;(function(window){
    'use strict';

	if (window.JCBlankZakaza)
		return;

	window.JCBlankZakaza = function (obName, arParams)
	{
        this.arParams = arParams;
        this.obName = obName;
        this.parameters = arParams.PARAMETERS;
        this.node = null;
        this.wrapper = null;
        BX.ready(BX.delegate(function() {
            this.init();
        },this));

        window.addEventListener('load', this.updateScroll.bind(this));
        window.addEventListener("popstate", function(event) {
            location.reload()
        });
    };
    window.JCBlankZakaza.prototype = {
        init: function() {
            this.node = document.getElementById(this.obName);
            this.wrapper = document.getElementById(this.obName + "_wrapper");

            this.initHeaderSort();
            // this.initSidePanelDetailPage();
            this.initFullScreen();
            this.initPagination();    
            this.initScroll();
            this.initDetailLink();
        },
        initFullScreen: function () {
            this.wrapper.querySelector('a[data-action="fullscreen"]')?.addEventListener('click', this.clickFullScreen.bind(this));
        },
        initPagination: function () {
            const nodePaginations = document.querySelectorAll('.pagination');
            
            nodePaginations.forEach((item) => {
                item.parentNode.addEventListener('click', (event)=> {
                    if (!event.target.closest('.page-link')) return;
    
                    event.preventDefault();

                    let data = {};
                    let link = new URL(event.target.href);
                    data['action'] = 'pagination';
                    if (link.searchParams.get('SORT[CODE]') && link.searchParams.get('SORT[ORDER]')) {
                        data['SORT_BY'] = link.searchParams.get('SORT[CODE]');
                        data['SORT_ORDER'] = link.searchParams.get('SORT[ORDER]');
                    }
                    data['PAGEN_' + this.arParams.NAV_PARAMS.NavNum] = link.searchParams.get('PAGEN_' + this.arParams.NAV_PARAMS.NavNum);
                    data['SIZEN_' + this.arParams.NAV_PARAMS.NavNum] = link.searchParams.get('SIZEN_' + this.arParams.NAV_PARAMS.NavNum);
                    data.url = link;

                    BX.showWait();
                    this.sendRequest(data);
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
        },
        initHeaderSort: function() {
            if (!this.node) {
                return;
            }

            this.node.addEventListener('click', function() {
                if(!event.target.closest('[data-property-code]')) return;

                this.changeSortHeader(event.target.closest('[data-property-code]'));
            }.bind(this));

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
        initScroll: function () {
            $(this.wrapper).floatingScroll();
        },
        updateScroll: function () {
            $(this.wrapper).floatingScroll('update');
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
        },
        sendRequest: function(data)
		{
			var defaultData = {
				siteId: this.arParams.SITE_ID,
				template: this.arParams.TEMPLATE,
				parameters: this.parameters,
                sotbit_set_site_template: this.arParams.SITE_TEMPLATE_SIGNS

			};

			if (this.arParams.AJAX_ID) {
				defaultData.AJAX_ID = this.arParams.AJAX_ID;
			}

			BX.ajax({
				url: this.arParams.AJAX_PATH + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
				method: 'POST',
				dataType: 'json',
				timeout: 120,
				data: BX.merge(defaultData, data),
				onsuccess: BX.delegate(function(result){
                    BX.closeWait();

                    if (!result || !result.JS)
						return;

                    this.showAction(result, data);
                    
					BX.ajax.processScripts(BX.processHTML(result.JS).SCRIPT);
                    window.history.pushState({urlPath: data.url.href}, null, data.url.href);
				}, this)
			});
		},
        showAction: function (result, data) {
            if (!data.action)
                return;

            switch (data.action) {
                case 'sort':
                    this.processUpdate(result, data);
                    break;
                case 'pagination':
                    this.processUpdate(result, data);
                    $(".fl-scrolls").floatingScroll("update");
                    break;
            }
        },
        processUpdate: function (result, data) {
            if (!result)
                return;

            this.node.querySelectorAll('tbody').forEach(item => item.remove());
            this.processTableHeader(result.tableHeader);
            this.processItems(result.items);
            this.processPagination(result.pagination, data);
            this.processEpilogue(result.epilogue);

            if (BX.type.isNotEmptyString(result.parameters)) {
                this.processParameters(result.parameters);
            }
        },
        processTableHeader: function(tableHeaderHtml) {
            if (!tableHeaderHtml)
                return;

            let processed = BX.processHTML(tableHeaderHtml, false);
            this.node.querySelector('.blank-zakaza__header').innerHTML = processed.HTML;

            this.initFullScreen();
            BX.ajax.processScripts(processed.SCRIPT);
        },
        processItems: function (itemsHtml) {
            if (!itemsHtml)
				return;

            let processed = BX.processHTML(itemsHtml, false);
            let temporaryNode = BX.create('template');
            temporaryNode.innerHTML = processed.HTML

            this.node.appendChild(temporaryNode.content);

            BX.ajax.processScripts(processed.SCRIPT);   
        },
        processPagination: function (paginationHtml, data) {
            if (!paginationHtml)
				return;

            let temporaryPagination = BX.create('template');
            temporaryPagination.innerHTML = paginationHtml;
            temporaryPagination.content.querySelectorAll('.page-link').forEach(link => {
                if (link.href) {
                    let urlPage = new URL(link.href);
                    if (data.SORT_BY && data.SORT_ORDER) {
                        urlPage.searchParams.set('SORT[CODE]', data.SORT_BY);
                        urlPage.searchParams.set('SORT[ORDER]', data.SORT_ORDER);
                    }

                    link.setAttribute('href', urlPage.href)
                }
            })
            document.querySelectorAll('.pagination').forEach(item => item.parentNode.innerHTML = temporaryPagination.innerHTML);
        },
        processEpilogue: function (epilogueHtml)
		{
			if (!epilogueHtml)
				return;

			var processed = BX.processHTML(epilogueHtml, false);
			BX.ajax.processScripts(processed.SCRIPT);
		},
        processParameters: function (parameters) {
            this.parameters = parameters;
        },
        changeSortHeader: function (node) {
            let order = 'ASC',
                code, 
                data = {};
            const url = new URL(location);
            order = ((url.searchParams.get('SORT[ORDER]') || order) === order) ? 'DESC' : 'ASC';

            if (node.dataset.propertyCode === 'QUANTITY') return;
            if (node.dataset.propertyCode === 'OFFERS') return;

            if (OrderingrKeys[node.dataset.propertyCode]) {
                code = OrderingrKeys[node.dataset.propertyCode];
            } else {
                code = node.dataset.propertyCode;
            }

            node.dataset.sortOrder = order;
            
            if (!this.prevNode) {
                this.wrapper.querySelectorAll('.blank-zakaza__header-property').forEach(item => item.classList.remove('active'));
            } else if(this.prevNode !== node) {
                this.prevNode.classList.remove('active')
            }
            node.classList.add('active');
            this.prevNode = node;

            url.searchParams.set('SORT[CODE]', code);
            url.searchParams.set('SORT[ORDER]', order);

            data['url'] = url;
            data['action'] = 'sort';
            data['SORT_BY'] = code;
            data['SORT_ORDER'] = order;
            data['PAGEN_' + this.arParams.NAV_PARAMS.NavNum] = url.searchParams.get('PAGEN_' + this.arParams.NAV_PARAMS.NavNum);
            data['SIZEN_' + this.arParams.NAV_PARAMS.NavNum] = url.searchParams.get('SIZEN_' + this.arParams.NAV_PARAMS.NavNum);

            BX.showWait();
            this.sendRequest(data);
        }
    };

    var OrderingrKeys = {
        'NAME': 'NAME',
        'AVALIABLE': 'QUANTITY',
        'MEASURE': 'MEASURE',
    };
})(window);