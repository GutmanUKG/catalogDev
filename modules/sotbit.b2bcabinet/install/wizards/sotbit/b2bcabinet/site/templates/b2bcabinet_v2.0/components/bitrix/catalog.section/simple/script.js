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
        this.formPosting = false;
        this.lastLoader = [];
        BX.ready(BX.delegate(function() {
            this.init();
            // this.initChange();
        },this));

        window.addEventListener('load', this.updateScroll.bind(this));
    };
    window.JCBlankZakaza.prototype = {
        init: function() {
            this.node = document.getElementById(this.obName);
            this.wrapper = document.getElementById(this.obName + "_wrapper");

            this.initHeaderSort();
            this.initPagination();
            this.initScroll();

            if (this.arParams.LOAD_ON_SCROLL === 'Y') {
                BX.bind(document.querySelector('.content-inner') || document.body, 'scroll', BX.proxy(this.loadOnScroll, this));
            }
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
        initHeaderSort: function() {
            if (!this.node) {
                return;
            }

            this.node.addEventListener('click', function() {
                if(!event.target.closest('[data-property-code]')) return;

                getParamsSort.call(this, event.target.closest('[data-property-code]'));
                   
            }.bind(this));

            function getParamsSort(node) {
                let order = 'asc',
                    code;
                
                if (node.dataset.propertyCode === 'QUANTITY') return;
                if (node.dataset.propertyCode === 'OFFERS') return;

                if (OrderingrKeys[node.dataset.propertyCode]) {
                    code = OrderingrKeys[node.dataset.propertyCode];
                } else {
                    code = node.dataset.propertyCode;
                }

                order = node.dataset.sortOrder === order ? 'desc' : 'asc';
                node.dataset.sortOrder = order;
                node.classList.add('active')
                
                if (this.prevNode && this.prevNode !== node) {
                    this.prevNode.classList.remove('active')
                    this.prevNode = node;
                }
               
                let data = {};
                data['action'] = 'sort';
                data['SORT_BY'] = code;
                data['SORT_ORDER'] = order;

                BX.showWait();
                this.sendRequest(data);
            }
        },
        initChange: function() {
            if (this.arParams.AJAX_MODE == 'N') return;

            const observerContainer = new MutationObserver(this.init.bind(this));
            observerContainer.observe(document.getElementById('comp_'+this.arParams.AJAX_ID), {
                childList: true
            });
        },
        initScroll: function () {
            $(this.wrapper).floatingScroll();
        },
        updateScroll: function () {
            $(this.wrapper).floatingScroll('update');
        },
        loadOnScroll: function()
		{
			let scrollTop = document.body.scrollTop,
				containerBottom = BX.pos(this.node).bottom;

			if (scrollTop + window.innerHeight > containerBottom)
			{
				this.showMore();
			}
		},
        showMore: function()
		{
			if (this.arParams.NAV_PARAMS.NavPageNomer < this.arParams.NAV_PARAMS.NavPageCount)
			{
				var data = {};
				data['action'] = 'showMore';
				data['PAGEN_' + this.arParams.NAV_PARAMS.NavNum] = +this.arParams.NAV_PARAMS.NavPageNomer + 1;

				if (!this.formPosting)
				{
					this.formPosting = true;
                    this.showLoader();
					this.sendRequest(data);
				}
			}
		},
        sendRequest: function(data)
		{
			var defaultData = {
				siteId: this.arParams.SITE_ID,
				template: this.arParams.TEMPLATE,
				parameters: this.arParams.ORIGINAL_PARAMETERS,
                sotbit_set_site_template: this.arParams.SITE_TEMPLATE_SIGNS

			};

			if (this.arParams.AJAX_ID)
			{
				defaultData.AJAX_ID = this.arParams.AJAX_ID;
			}

			BX.ajax({
				url: this.arParams.AJAX_PATH + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: BX.merge(defaultData, data),
				onsuccess: BX.delegate(function(result){
					if (!result || !result.JS)
						return;

                    this.showAction(result, data);
                    
					BX.ajax.processScripts(BX.processHTML(result.JS).SCRIPT);
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
					this.processDeferredLoadAction(result);
					break;
                case 'sort':
                    this.processSortAction(result);
                    break;
			}
		},
        processShowMoreAction: function(result)
		{
			this.formPosting = false;
            this.closeLoader();

			if (!result)
			    return;

            this.arParams.NAV_PARAMS.NavPageNomer++;
            this.processItems(result.items);
			this.processEpilogue(result.epilogue);
			// this.changeNumPagination();
		},
        processDeferredLoadAction: function(result)
		{
			if (!result)
				return;

			this.processItems(result.items);
		},
        processSortAction: function(result)
        {
            BX.closeWait();

            if (!result)
                return;
            this.arParams.NAV_PARAMS.NavPageNomer = 1;
            this.node.querySelectorAll('tbody').forEach(item => item.remove());
            this.processItems(result.items);
            this.processEpilogue(result.epilogue);
            // this.changeNumPagination();
        },
        processItems: function(itemsHtml)
		{
			if (!itemsHtml)
				return;

            let processed = BX.processHTML(itemsHtml, false);
            let temporaryNode = BX.create('template');
            temporaryNode.innerHTML = processed.HTML

            this.node.appendChild(temporaryNode.content);

			BX.ajax.processScripts(processed.SCRIPT);
		},
        processEpilogue: function(epilogueHtml)
		{
			if (!epilogueHtml)
				return;

			var processed = BX.processHTML(epilogueHtml, false);
			BX.ajax.processScripts(processed.SCRIPT);
		},
        changeNumPagination: function()
        {
            let numPagination = document.querySelector('.current-page');
            numPagination.innerText = this.arParams.NAV_PARAMS.NavPageNomer;
        },
        showLoader: function()
        {
            let container_id = Math.random();

            let preloader = BX.create('DIV', {
                props: {
                    id: 'loader_' + container_id
                },
                style: {
                    zIndex:'10000',
                    position: 'absolute',
                    bottom: '1.5rem',
                    left: 'calc(50% - 1.5rem)'
                }
            });

            let theme_xbox = document.createElement("div");
            let pace_activity = document.createElement("div");

            theme_xbox.setAttribute("class", "theme_xbox");
            pace_activity.setAttribute("class", "pace_activity");

            theme_xbox.appendChild(pace_activity);
            preloader.appendChild(theme_xbox);

            this.lastLoader.push(this.node.appendChild(preloader));
        },
        closeLoader: function() {
            let obMsg = this.lastLoader.pop();

            obMsg.parentNode.removeChild(obMsg);
        }
    };

    var OrderingrKeys = {
        'NAME': 'NAME',
        'AVALIABLE': 'QUANTITY',
        'MEASURE': 'MEASURE',
    };
})(window);