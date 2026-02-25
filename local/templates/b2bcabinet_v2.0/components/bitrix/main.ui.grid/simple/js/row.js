;(function() {
	'use strict';

	BX.namespace('BX.Grid');

	/**
	 * BX.Grid.Row
	 * @param {BX.Main.Grid} parent
	 * @param {HtmlElement} node
	 * @constructor
	 */
	BX.Grid.Row = function(parent, node)
	{
		this.node = null;
		this.checkbox = null;
		this.sort = null;
		this.actions = null;
		this.settings = null;
		this.index = null;
		this.actionsButton = null;
		this.parent = null;
		this.depth = null;
		this.parentId = null;
		this.editData = null;
		this.custom = null;
		this.init(parent, node);
	};

	//noinspection JSUnusedGlobalSymbols,JSUnusedGlobalSymbols
	BX.Grid.Row.prototype = {
	    init: function init(parent, node) {
	      if (BX.type.isDomNode(node)) {
	        this.node = node;
	        this.parent = parent;
	        this.settings = new BX.Grid.Settings();
	        this.bindNodes = [];

	        if (this.isBodyChild()) {
	          this.bindNodes = [].slice.call(this.node.parentNode.querySelectorAll("tr[data-bind=\"" + this.getId() + "\"]"));

	          if (this.bindNodes.length) {
	            this.node.addEventListener("mouseover", this.onMouseOver.bind(this));
	            this.node.addEventListener("mouseleave", this.onMouseLeave.bind(this));
	            this.bindNodes.forEach(function (row) {
	              row.addEventListener("mouseover", this.onMouseOver.bind(this));
	              row.addEventListener("mouseleave", this.onMouseLeave.bind(this));
	              row.addEventListener("click", function () {
	                if (this.isSelected()) {
	                  this.unselect();
	                } else {
	                  this.select();
	                }
	              }.bind(this));
	            }, this);
	          }
	        }

	        if (this.parent.getParam('ALLOW_CONTEXT_MENU')) {
	          BX.bind(this.getNode(), 'contextmenu', BX.delegate(this._onRightClick, this));
	        }
	      }
	    },
	    onMouseOver: function onMouseOver() {
	      this.node.classList.add("main-grid-row-over");
	      this.bindNodes.forEach(function (row) {
	        row.classList.add("main-grid-row-over");
	      });
	    },
	    onMouseLeave: function onMouseLeave() {
	      this.node.classList.remove("main-grid-row-over");
	      this.bindNodes.forEach(function (row) {
	        row.classList.remove("main-grid-row-over");
	      });
	    },
	    isCustom: function isCustom() {
	      if (this.custom === null) {
	        this.custom = BX.hasClass(this.getNode(), this.parent.settings.get('classRowCustom'));
	      }

	      return this.custom;
	    },
	    _onRightClick: function _onRightClick(event) {
	      event.preventDefault();

	      if (!this.isHeadChild()) {
	        this.showActionsMenu(event);
	      }
	    },
	    getDefaultAction: function getDefaultAction() {
	      return BX.data(this.getNode(), 'default-action');
	    },
	    getEditorValue: function getEditorValue() {
	      var self = this;
	      var cells = this.getCells();
	      var values = {};
	      var cellValues;
	      [].forEach.call(cells, function (current) {
	        cellValues = self.getCellEditorValue(current);

	        if (BX.type.isArray(cellValues)) {
	          cellValues.forEach(function (cellValue) {
	            values[cellValue.NAME] = cellValue.VALUE !== undefined ? cellValue.VALUE : "";

	            if (cellValue.hasOwnProperty("RAW_NAME") && cellValue.hasOwnProperty("RAW_VALUE")) {
	              values[cellValue.NAME + "_custom"] = values[cellValue.NAME + "_custom"] || {};
	              values[cellValue.NAME + "_custom"][cellValue.RAW_NAME] = values[cellValue.NAME + "_custom"][cellValue.RAW_NAME] || cellValue.RAW_VALUE;
	            }
	          });
	        } else if (cellValues) {
	          values[cellValues.NAME] = cellValues.VALUE !== undefined ? cellValues.VALUE : "";
	        }
	      });
	      return values;
	    },

	    /**
	     * @deprecated
	     * @use this.getEditorValue()
	     */
	    editGetValues: function editGetValues() {
	      return this.getEditorValue();
	    },
	    getCellEditorValue: function getCellEditorValue(cell) {
	      var editor = BX.Grid.Utils.getByClass(cell, this.parent.settings.get('classEditor'), true);
	      var result = null;

	      if (BX.type.isDomNode(editor)) {
	        if (BX.hasClass(editor, 'main-grid-editor-checkbox')) {
	          result = {
	            'NAME': editor.getAttribute('name'),
	            'VALUE': editor.checked ? 'Y' : 'N'
	          };
	        } else if (BX.hasClass(editor, 'main-grid-editor-custom')) {
	          result = this.getCustomValue(editor);
	        } else if (BX.hasClass(editor, 'main-grid-editor-money')) {
	          result = this.getMoneyValue(editor);
	        } else if (BX.hasClass(editor, 'main-ui-multi-select')) {
	          result = this.getMultiSelectValues(editor);
	        } else {
	          result = this.getImageValue(editor);
	        }
	      }

	      return result;
	    },
	    isEdit: function isEdit() {
	      return BX.hasClass(this.getNode(), 'main-grid-row-edit');
	    },
	    hide: function hide() {
	      BX.addClass(this.getNode(), this.parent.settings.get('classHide'));
	    },
	    show: function show() {
	      BX.Dom.attr(this.getNode(), 'hidden', null);
	      BX.removeClass(this.getNode(), this.parent.settings.get('classHide'));
	    },
	    isShown: function isShown() {
	      return !BX.hasClass(this.getNode(), this.parent.settings.get('classHide'));
	    },
	    isNotCount: function isNotCount() {
	      return BX.hasClass(this.getNode(), this.parent.settings.get('classNotCount'));
	    },
	    getContentContainer: function getContentContainer(target) {
	      if (BX.Type.isDomNode(target)) {
	        var cell = target.closest('.main-grid-cell');

	        if (BX.Type.isDomNode(cell)) {
	          return cell.querySelector('.main-grid-cell-content');
	        }
	      }

	      return target;
	    },
	    getContent: function getContent(cell) {
	      var container = this.getContentContainer(cell);
	      var content;

	      if (BX.type.isDomNode(container)) {
	        content = BX.html(container);
	      }

	      return content;
	    },
	    getMoneyValue: function getMoneyValue(editor) {
	      var result = [];
	      var filteredValue = {
	        PRICE: {},
	        CURRENCY: {},
	        HIDDEN: {}
	      };
	      var fieldName = editor.getAttribute('data-name');
	      var inputs = [].slice.call(editor.querySelectorAll('input'));
	      inputs.forEach(function (element) {
	        result.push({
	          NAME: fieldName,
	          RAW_NAME: element.name,
	          RAW_VALUE: element.value || '',
	          VALUE: element.value || ''
	        });

	        if (element.classList.contains('main-grid-editor-money-price')) {
	          filteredValue.PRICE = {
	            NAME: element.name,
	            VALUE: element.value
	          };
	        } else if (element.type === ' hidden') {
	          filteredValue.HIDDEN[element.name] = element.value;
	        }
	      });
	      var currencySelector = editor.querySelector('.main-grid-editor-dropdown');

	      if (currencySelector) {
	        var currencyFieldName = currencySelector.getAttribute('name');

	        if (BX.type.isNotEmptyString(currencyFieldName)) {
	          result.push({
	            NAME: fieldName,
	            RAW_NAME: currencyFieldName,
	            RAW_VALUE: currencySelector.dataset.value || '',
	            VALUE: currencySelector.dataset.value || ''
	          });
	          filteredValue.CURRENCY = {
	            NAME: currencyFieldName,
	            VALUE: currencySelector.dataset.value
	          };
	        }
	      }

	      result.push({
	        NAME: fieldName,
	        VALUE: filteredValue
	      });
	      return result;
	    },
	    getCustomValue: function getCustomValue(editor) {
	      var map = new Map(),
	          name = editor.getAttribute('data-name');
	      var inputs = [].slice.call(editor.querySelectorAll('input, select, checkbox, textarea'));
	      inputs.forEach(function (element) {
	        var resultObject = {
	          'NAME': name,
	          'RAW_NAME': element.name,
	          'RAW_VALUE': element.value,
	          'VALUE': element.value
	        };

	        switch (element.tagName) {
	          case 'SELECT':
	            if (element.multiple) {
	              var selectValues = [];
	              element.querySelectorAll('option').forEach(function (option) {
	                if (option.selected) {
	                  selectValues.push(option.value);
	                }
	              });
	              resultObject['RAW_VALUE'] = selectValues;
	              resultObject['VALUE'] = selectValues;
	              map.set(element.name, resultObject);
	            } else {
	              map.set(element.name, resultObject);
	            }

	            break;

	          case 'INPUT':
	            switch (element.type.toUpperCase()) {
	              case 'RADIO':
	                if (element.checked) {
	                  resultObject['RAW_VALUE'] = element.value;
	                  resultObject['VALUE'] = element.value;
	                  map.set(element.name, resultObject);
	                }

	                break;

	              case 'CHECKBOX':
	                resultObject['RAW_VALUE'] = element.checked ? element.value : '';
	                resultObject['VALUE'] = element.checked ? element.value : '';
	                map.set(element.name, resultObject);
	                break;

	              case 'FILE':
	                resultObject['RAW_VALUE'] = element.files[0];
	                resultObject['VALUE'] = element.files[0];
	                map.set(element.name, resultObject);
	                break;

	              default:
	                map.set(element.name, resultObject);
	            }

	            break;

	          default:
	            map.set(element.name, resultObject);
	        }
	      });
	      var result = [];
	      map.forEach(function (value) {
	        result.push(value);
	      });
	      return result;
	    },
	    getImageValue: function getImageValue(editor) {
	      var result = null;

	      if (BX.hasClass(editor, 'main-grid-image-editor')) {
	        var input = editor.querySelector('.main-grid-image-editor-file-input');

	        if (input) {
	          result = {
	            'NAME': input.name,
	            'VALUE': input.files[0]
	          };
	        } else {
	          var fakeInput = editor.querySelector('.main-grid-image-editor-fake-file-input');

	          if (fakeInput) {
	            result = {
	              'NAME': fakeInput.name,
	              'VALUE': fakeInput.value
	            };
	          }
	        }
	      } else if (editor.value) {
	        result = {
	          'NAME': editor.getAttribute('name'),
	          'VALUE': editor.value
	        };
	      } else {
	        result = {
	          'NAME': editor.getAttribute('name'),
	          'VALUE': BX.data(editor, 'value')
	        };
	      }

	      return result;
	    },
	    getMultiSelectValues: function getMultiSelectValues(editor) {
	      var value = JSON.parse(BX.data(editor, 'value'));
	      return {
	        'NAME': editor.getAttribute('name'),
	        'VALUE': main_core.Type.isArrayFilled(value) ? value : ''
	      };
	    },

	    /**
	     * @param {HTMLTableCellElement} cell
	     * @return {?HTMLElement}
	     */
	    getEditorContainer: function getEditorContainer(cell) {
	      return BX.Grid.Utils.getByClass(cell, this.parent.settings.get('classEditorContainer'), true);
	    },

	    /**
	     * @return {HTMLElement}
	     */
	    getCollapseButton: function getCollapseButton() {
	      if (!this.collapseButton) {
	        this.collapseButton = BX.Grid.Utils.getByClass(this.getNode(), this.parent.settings.get('classCollapseButton'), true);
	      }

	      return this.collapseButton;
	    },
	    stateLoad: function stateLoad() {
	      BX.addClass(this.getNode(), this.parent.settings.get('classRowStateLoad'));
	    },
	    stateUnload: function stateUnload() {
	      BX.removeClass(this.getNode(), this.parent.settings.get('classRowStateLoad'));
	    },
	    stateExpand: function stateExpand() {
	      BX.addClass(this.getNode(), this.parent.settings.get('classRowStateExpand'));
	    },
	    stateCollapse: function stateCollapse() {
	      BX.removeClass(this.getNode(), this.parent.settings.get('classRowStateExpand'));
	    },
	    getParentId: function getParentId() {
	      if (this.parentId === null) {
	        this.parentId = BX.data(this.getNode(), 'parent-id');

	        if (typeof this.parentId !== 'undefined' && this.parentId !== null) {
	          this.parentId = this.parentId.toString();
	        }
	      }

	      return this.parentId;
	    },

	    /**
	     * @return {DOMStringMap}
	     */
	    getDataset: function getDataset() {
	      return this.getNode().dataset;
	    },

	    /**
	     * Gets row depth level
	     * @return {?number}
	     */
	    getDepth: function getDepth() {
	      if (this.depth === null) {
	        this.depth = BX.data(this.getNode(), 'depth');
	      }

	      return this.depth;
	    },

	    /**
	     * Set row depth
	     * @param {number} depth
	     */
	    setDepth: function setDepth(depth) {
	      depth = parseInt(depth);

	      if (BX.type.isNumber(depth)) {
	        var depthOffset = depth - parseInt(this.getDepth());
	        var Rows = this.parent.getRows();
	        this.getDataset().depth = depth;
	        this.getShiftCells().forEach(function (cell) {
	          BX.data(cell, 'depth', depth);
	          BX.style(cell, 'padding-left', depth * 20 + 'px');
	        }, this);
	        Rows.getRowsByParentId(this.getId(), true).forEach(function (row) {
	          var childDepth = parseInt(depthOffset) + parseInt(row.getDepth());
	          row.getDataset().depth = childDepth;
	          row.getShiftCells().forEach(function (cell) {
	            BX.data(cell, 'depth', childDepth);
	            BX.style(cell, 'padding-left', childDepth * 20 + 'px');
	          });
	        });
	      }
	    },

	    /**
	     * Sets parent id
	     * @param {string|number} id
	     */
	    setParentId: function setParentId(id) {
	      this.getDataset()['parentId'] = id;
	    },

	    /**
	     * @return {HTMLTableRowElement}
	     */
	    getShiftCells: function getShiftCells() {
	      return BX.Grid.Utils.getBySelector(this.getNode(), 'td[data-shift="true"]');
	    },
	    showChildRows: function showChildRows() {
	      var rows = this.getChildren();
	      var isCustom = this.isCustom();
	      rows.forEach(function (row) {
	        row.show();

	        if (!isCustom && row.isExpand()) {
	          row.showChildRows();
	        }
	      });
	      this.parent.updateCounterDisplayed();
	      this.parent.updateCounterSelected();
	      this.parent.adjustCheckAllCheckboxes();
	      this.parent.adjustRows();
	    },

	    /**
	     * @return {BX.Grid.Row[]}
	     */
	    getChildren: function getChildren() {
	      var functionName = this.isCustom() ? 'getRowsByGroupId' : 'getRowsByParentId';
	      var id = this.isCustom() ? this.getGroupId() : this.getId();
	      return this.parent.getRows()[functionName](id, true);
	    },
	    hideChildRows: function hideChildRows() {
	      var rows = this.getChildren();
	      rows.forEach(function (row) {
	        row.hide();
	      });
	      this.parent.updateCounterDisplayed();
	      this.parent.updateCounterSelected();
	      this.parent.adjustCheckAllCheckboxes();
	      this.parent.adjustRows();
	    },
	    isChildsLoaded: function isChildsLoaded() {
	      if (!BX.type.isBoolean(this.childsLoaded)) {
	        this.childsLoaded = this.isCustom() || BX.data(this.getNode(), 'child-loaded') === 'true';
	      }

	      return this.childsLoaded;
	    },
	    expand: function expand() {
	      var self = this;
	      this.stateExpand();

	      if (this.isChildsLoaded()) {
	        this.showChildRows();
	      } else {
	        this.stateLoad();
	        this.loadChildRows(function (rows) {
	          rows.reverse().forEach(function (current) {
	            BX.insertAfter(current, self.getNode());
	          });
	          self.parent.getRows().reset();
	          self.parent.bindOnRowEvents();

	          if (self.parent.getParam('ALLOW_ROWS_SORT')) {
	            self.parent.getRowsSortable().reinit();
	          }

	          if (self.parent.getParam('ALLOW_COLUMNS_SORT')) {
	            self.parent.getColsSortable().reinit();
	          }

	          self.stateUnload();
	          BX.data(self.getNode(), 'child-loaded', 'true');
	          self.parent.updateCounterDisplayed();
	          self.parent.updateCounterSelected();
	          self.parent.adjustCheckAllCheckboxes();
	        });
	      }
	    },
	    collapse: function collapse() {
	      this.stateCollapse();
	      this.hideChildRows();
	    },
	    isExpand: function isExpand() {
	      return BX.hasClass(this.getNode(), this.parent.settings.get('classRowStateExpand'));
	    },
	    toggleChildRows: function toggleChildRows() {
	      if (!this.isExpand()) {
	        this.expand();
	      } else {
	        this.collapse();
	      }
	    },
	    loadChildRows: function loadChildRows(callback) {
	      if (BX.type.isFunction(callback)) {
	        var self = this;
	        var depth = parseInt(this.getDepth());
	        var action = this.parent.getUserOptions().getAction('GRID_GET_CHILD_ROWS');
	        depth = BX.type.isNumber(depth) ? depth + 1 : 1;
	        this.parent.getData().request('', 'POST', {
	          action: action,
	          parent_id: this.getId(),
	          depth: depth
	        }, null, function () {
	          var rows = this.getRowsByParentId(self.getId());
	          callback.apply(null, [rows]);
	        });
	      }
	    },
	    update: function update(data, url, callback) {
	      data = !!data ? data : '';
	      var action = this.parent.getUserOptions().getAction('GRID_UPDATE_ROW');
	      var depth = this.getDepth();
	      var id = this.getId();
	      var parentId = this.getParentId();
	      var rowData = {
	        id: id,
	        parentId: parentId,
	        action: action,
	        depth: depth,
	        data: data
	      };
	      var self = this;
	      this.stateLoad();
	      this.parent.getData().request(url, 'POST', rowData, null, function () {
	        var bodyRows = this.getBodyRows();
	        self.parent.getUpdater().updateBodyRows(bodyRows);
	        self.stateUnload();
	        self.parent.getRows().reset();
	        self.parent.getUpdater().updateFootRows(this.getFootRows());
	        self.parent.getUpdater().updatePagination(this.getPagination());
	        self.parent.getUpdater().updateMoreButton(this.getMoreButton());
	        self.parent.getUpdater().updateCounterTotal(this.getCounterTotal());
	        self.parent.bindOnRowEvents();
	        self.parent.adjustEmptyTable(bodyRows);
	        self.parent.bindOnMoreButtonEvents();
	        self.parent.bindOnClickPaginationLinks();
	        self.parent.updateCounterDisplayed();
	        self.parent.updateCounterSelected();

	        if (self.parent.getParam('ALLOW_COLUMNS_SORT')) {
	          self.parent.colsSortable.reinit();
	        }

	        if (self.parent.getParam('ALLOW_ROWS_SORT')) {
	          self.parent.rowsSortable.reinit();
	        }

	        BX.onCustomEvent(window, 'Grid::rowUpdated', [{
	          id: id,
	          data: data,
	          grid: self.parent,
	          response: this
	        }]);
	        BX.onCustomEvent(window, 'Grid::updated', [self.parent]);

	        if (BX.type.isFunction(callback)) {
	          callback({
	            id: id,
	            data: data,
	            grid: self.parent,
	            response: this
	          });
	        }
	      });
	    },
	    remove: function remove(data, url, callback) {
	      data = !!data ? data : '';
	      var action = this.parent.getUserOptions().getAction('GRID_DELETE_ROW');
	      var depth = this.getDepth();
	      var id = this.getId();
	      var parentId = this.getParentId();
	      var rowData = {
	        id: id,
	        parentId: parentId,
	        action: action,
	        depth: depth,
	        data: data
	      };
	      var self = this;
	      this.stateLoad();
	      this.parent.getData().request(url, 'POST', rowData, null, function () {
	        var bodyRows = this.getBodyRows();
	        self.parent.getUpdater().updateBodyRows(bodyRows);
	        self.stateUnload();
	        self.parent.getRows().reset();
	        self.parent.getUpdater().updateFootRows(this.getFootRows());
	        self.parent.getUpdater().updatePagination(this.getPagination());
	        self.parent.getUpdater().updateMoreButton(this.getMoreButton());
	        self.parent.getUpdater().updateCounterTotal(this.getCounterTotal());
	        self.parent.bindOnRowEvents();
	        self.parent.adjustEmptyTable(bodyRows);
	        self.parent.bindOnMoreButtonEvents();
	        self.parent.bindOnClickPaginationLinks();
	        self.parent.updateCounterDisplayed();
	        self.parent.updateCounterSelected();

	        if (self.parent.getParam('ALLOW_COLUMNS_SORT')) {
	          self.parent.colsSortable.reinit();
	        }

	        if (self.parent.getParam('ALLOW_ROWS_SORT')) {
	          self.parent.rowsSortable.reinit();
	        }

	        BX.onCustomEvent(window, 'Grid::rowRemoved', [{
	          id: id,
	          data: data,
	          grid: self.parent,
	          response: this
	        }]);
	        BX.onCustomEvent(window, 'Grid::updated', [self.parent]);

	        if (BX.type.isFunction(callback)) {
	          callback({
	            id: id,
	            data: data,
	            grid: self.parent,
	            response: this
	          });
	        }
	      });
	    },
	    editCancel: function editCancel() {
	      var cells = this.getCells();
	      var self = this;
	      var editorContainer;
	      [].forEach.call(cells, function (current) {
	        editorContainer = self.getEditorContainer(current);

	        if (BX.type.isDomNode(editorContainer)) {
	          BX.remove(self.getEditorContainer(current));
	          BX.show(self.getContentContainer(current));
	        }
	      });
	      BX.removeClass(this.getNode(), 'main-grid-row-edit');
	    },
	    getCellByIndex: function getCellByIndex(index) {
	      return this.getCells()[index];
	    },
	    getEditDataByCellIndex: function getEditDataByCellIndex(index) {
	      return eval(BX.data(this.getCellByIndex(index), 'edit'));
	    },
	    getCellNameByCellIndex: function getCellNameByCellIndex(index) {
	      return BX.data(this.getCellByIndex(index), 'name');
	    },
	    resetEditData: function resetEditData() {
	      this.editData = null;
	    },
	    setEditData: function setEditData(editData) {
	      this.editData = editData;
	    },
	    getEditData: function getEditData() {
	      if (this.editData === null) {
	        var editableData = this.parent.getParam('EDITABLE_DATA');
	        var rowId = this.getId();

	        if (BX.type.isPlainObject(editableData) && rowId in editableData) {
	          this.editData = editableData[rowId];
	        } else {
	          this.editData = {};
	        }
	      }

	      return this.editData;
	    },
	    getCellEditDataByCellIndex: function getCellEditDataByCellIndex(cellIndex) {
	      var editData = this.getEditData();
	      var result = null;
	      cellIndex = parseInt(cellIndex);

	      if (BX.type.isNumber(cellIndex) && BX.type.isPlainObject(editData)) {
	        var columnEditData = this.parent.getRows().getHeadFirstChild().getEditDataByCellIndex(cellIndex);

	        if (BX.type.isPlainObject(columnEditData)) {
	          result = columnEditData;
	          result.VALUE = editData[columnEditData.NAME];
	        }
	      }

	      return result;
	    },
	    edit: function edit() {
	      var cells = this.getCells();
	      var self = this;
	      var editObject, editor, height, contentContainer;
	      [].forEach.call(cells, function (current, index) {
	        if (current.dataset.editable === 'true') {
	          try {
	            editObject = self.getCellEditDataByCellIndex(index);
	          } catch (err) {
	            throw new Error(err);
	          }

	          if (self.parent.getEditor().validateEditObject(editObject)) {
	            contentContainer = self.getContentContainer(current);
	            height = BX.height(contentContainer);
	            editor = self.parent.getEditor().getEditor(editObject, height);

	            if (!self.getEditorContainer(current) && BX.type.isDomNode(editor)) {
	              current.appendChild(editor);
	              BX.hide(contentContainer);
	            }
	          }
	        }
	      });
	      BX.addClass(this.getNode(), 'main-grid-row-edit');
	    },
	    setDraggable: function setDraggable(value) {
	      if (!value) {
	        BX.addClass(this.getNode(), this.parent.settings.get('classDisableDrag'));
	        this.parent.getRowsSortable().unregister(this.getNode());
	      } else {
	        BX.removeClass(this.getNode(), this.parent.settings.get('classDisableDrag'));
	        this.parent.getRowsSortable().register(this.getNode());
	      }
	    },
	    isDraggable: function isDraggable() {
	      return !BX.hasClass(this.getNode(), this.parent.settings.get('classDisableDrag'));
	    },
	    getNode: function getNode() {
	      return this.node;
	    },
	    getIndex: function getIndex() {
	      return this.getNode().rowIndex;
	    },
	    getId: function getId() {
	      return String(BX.data(this.getNode(), 'id'));
	    },
	    getGroupId: function getGroupId() {
	      return BX.data(this.getNode(), 'group-id').toString();
	    },
	    getObserver: function getObserver() {
	      return BX.Grid.observer;
	    },
	    getCheckbox: function getCheckbox() {
	      if (!this.checkbox) {
	        this.checkbox = BX.Grid.Utils.getByClass(this.getNode(), this.settings.get('classRowCheckbox'), true);
	      }

	      return this.checkbox;
	    },
	    getActionsMenu: function getActionsMenu() {
	      if (!this.actionsMenu) {
	        var buttonRect = this.getActionsButton().getBoundingClientRect();
	        this.actionsMenu = BX.PopupMenu.create('main-grid-actions-menu-' + this.getId(), this.getActionsButton(), this.getMenuItems(), {
	          'autoHide': true,
	          'offsetTop': -(buttonRect.height / 2 + 26),
	          'offsetLeft': 30,
	          'angle': {
	            'position': 'left',
	            'offset': buttonRect.height / 2 - 8
	          },
	          'events': {
	            'onPopupClose': BX.delegate(this._onCloseMenu, this),
	            'onPopupShow': BX.delegate(this._onPopupShow, this)
	          }
	        });
	        BX.addCustomEvent('Grid::updated', function () {
	          if (this.actionsMenu) {
	            this.actionsMenu.destroy();
	            this.actionsMenu = null;
	          }
	        }.bind(this));
	        BX.bind(this.actionsMenu.popupWindow.popupContainer, 'click', BX.delegate(function (event) {
	          var actionsMenu = this.getActionsMenu();

	          if (actionsMenu) {
	            var target = BX.getEventTarget(event);
	            var item = BX.findParent(target, {
	              className: 'menu-popup-item'
	            }, 10);

	            if (!item || !item.dataset.preventCloseContextMenu) {
	              actionsMenu.close();
	            }
	          }
	        }, this));
	      }

	      return this.actionsMenu;
	    },
	    _onCloseMenu: function _onCloseMenu() {},
	    _onPopupShow: function _onPopupShow(popupMenu) {
	      popupMenu.setBindElement(this.getActionsButton());
	    },
	    actionsMenuIsShown: function actionsMenuIsShown() {
	      return this.getActionsMenu().popupWindow.isShown();
	    },
	    showActionsMenu: function showActionsMenu(event) {
	      BX.fireEvent(document.body, 'click');
	      this.getActionsMenu().popupWindow.show();

	      if (event) {
	        this.getActionsMenu().popupWindow.popupContainer.style.top = event.pageY - 25 + BX.PopupWindow.getOption("offsetTop") + "px";
	        this.getActionsMenu().popupWindow.popupContainer.style.left = event.pageX + 20 + BX.PopupWindow.getOption("offsetLeft") + "px";
	      }
	    },
	    closeActionsMenu: function closeActionsMenu() {
	      if (this.actionsMenu) {
	        if (this.actionsMenu.popupWindow) {
	          this.actionsMenu.popupWindow.close();
	        }
	      }
	    },
	    getMenuItems: function getMenuItems() {
	      return this.getActions() || [];
	    },
	    getActions: function getActions() {
	      try {
	        this.actions = this.actions || eval(BX.data(this.getActionsButton(), this.settings.get('dataActionsKey')));
	      } catch (err) {
	        this.actions = null;
	      }

	      return this.actions;
	    },
	    getActionsButton: function getActionsButton() {
	      if (!this.actionsButton) {
	        this.actionsButton = BX.Grid.Utils.getByClass(this.getNode(), this.settings.get('classRowActionButton'), true);
	      }

	      return this.actionsButton;
	    },
	    initSelect: function initSelect() {
	      if (this.isSelected() && !BX.hasClass(this.getNode(), this.settings.get('classCheckedRow'))) {
	        BX.addClass(this.getNode(), this.settings.get('classCheckedRow'));
	      }
	    },
	    getParentNode: function getParentNode() {
	      var result;

	      try {
	        result = this.getNode().parentNode;
	      } catch (err) {
	        result = null;
	      }

	      return result;
	    },
	    getParentNodeName: function getParentNodeName() {
	      var result;

	      try {
	        result = this.getParentNode().nodeName;
	      } catch (err) {
	        result = null;
	      }

	      return result;
	    },
	    isSelectable: function isSelectable() {
	      return !this.isEdit() || this.parent.getParam('ALLOW_EDIT_SELECTION');
	    },
	    select: function select() {
	      var checkbox;

	      if (this.isSelectable() && (this.parent.getParam('ADVANCED_EDIT_MODE') || !this.parent.getRows().hasEditable())) {
	        checkbox = this.getCheckbox();

	        if (checkbox) {
	          if (!BX.data(checkbox, 'disabled')) {
	            BX.addClass(this.getNode(), this.settings.get('classCheckedRow'));
	            this.bindNodes.forEach(function (row) {
	              BX.addClass(row, this.settings.get('classCheckedRow'));
	            }, this);
	            checkbox.checked = true;
	          }
	        }
	      }
	    },
	    unselect: function unselect() {
	      if (this.isSelectable()) {
	        BX.removeClass(this.getNode(), this.settings.get('classCheckedRow'));
	        this.bindNodes.forEach(function (row) {
	          BX.removeClass(row, this.settings.get('classCheckedRow'));
	        }, this);

	        if (this.getCheckbox()) {
	          this.getCheckbox().checked = false;
	        }
	      }
	    },
	    getCells: function getCells() {
	      return this.getNode().cells;
	    },
	    isSelected: function isSelected() {
	      return this.getCheckbox() && this.getCheckbox().checked || BX.hasClass(this.getNode(), this.settings.get('classCheckedRow'));
	    },
	    isHeadChild: function isHeadChild() {
	      return this.getParentNodeName() === 'THEAD' && BX.hasClass(this.getNode(), this.settings.get('classHeadRow'));
	    },
	    isBodyChild: function isBodyChild() {
	      return BX.hasClass(this.getNode(), this.settings.get('classBodyRow')) && !BX.hasClass(this.getNode(), this.settings.get('classEmptyRows'));
	    },
	    isFootChild: function isFootChild() {
	      return this.getParentNodeName() === 'TFOOT' && BX.hasClass(this.getNode(), this.settings.get('classFootRow'));
	    },
	    prependTo: function prependTo(target) {
	      BX.Dom.prepend(this.getNode(), target);
	    },
	    appendTo: function appendTo(target) {
	      BX.Dom.append(this.getNode(), target);
	    },
	    setId: function setId(id) {
	      BX.Dom.attr(this.getNode(), 'data-id', id);
	    },
	    setActions: function setActions(actions) {
	      var actionCell = this.getNode().querySelector('.main-grid-cell-action');

	      if (actionCell) {
	        var actionButton = actionCell.querySelector('.main-grid-row-action-button');

	        if (!actionButton) {
	          actionButton = BX.Dom.create({
	            tag: 'div',
	            props: {
	              className: 'main-grid-row-action-button'
	            }
	          });
	          var container = this.getContentContainer(actionCell);
	          BX.Dom.append(actionButton, container);
	        }

	        BX.Dom.attr(actionButton, {
	          href: '#',
	          'data-actions': actions
	        });
	        this.actions = actions;

	        if (this.actionsMenu) {
	          this.actionsMenu.destroy();
	          this.actionsMenu = null;
	        }
	      }
	    },
	    makeCountable: function makeCountable() {
	      BX.Dom.removeClass(this.getNode(), 'main-grid-not-count');
	    },
	    makeNotCountable: function makeNotCountable() {
	      BX.Dom.addClass(this.getNode(), 'main-grid-not-count');
	    },
	    getColumnOptions: function getColumnOptions(columnId) {
	      var columns = this.parent.getParam('COLUMNS_ALL');

	      if (BX.Type.isPlainObject(columns) && Reflect.has(columns, columnId)) {
	        return columns[columnId];
	      }

	      return null;
	    },
	    setCellsContent: function setCellsContent(content) {
	      var _this = this;

	      var headRow = this.parent.getRows().getHeadFirstChild();
	      babelHelpers.toConsumableArray(this.getCells()).forEach(function (cell, cellIndex) {
	        var cellName = headRow.getCellNameByCellIndex(cellIndex);

	        if (Reflect.has(content, cellName)) {
	          var columnOptions = _this.getColumnOptions(cellName);

	          var container = _this.getContentContainer(cell);

	          var cellContent = content[cellName];

	          if (columnOptions.type === 'labels' && BX.Type.isArray(cellContent)) {
	            var labels = cellContent.map(function (labelOptions) {
	              var label = BX.Tag.render(_templateObject$1 || (_templateObject$1 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"ui-label ", "\"></span>\n\t\t\t\t\t\t\t"])), labelOptions.color);

	              if (labelOptions.light !== true) {
	                BX.Dom.addClass(label, 'ui-label-fill');
	              }

	              if (BX.Type.isPlainObject(labelOptions.events)) {
	                if (Reflect.has(labelOptions.events, 'click')) {
	                  BX.Dom.addClass(label, 'ui-label-link');
	                }

	                _this.bindOnEvents(label, labelOptions.events);
	              }

	              var labelContent = function () {
	                if (BX.Type.isStringFilled(labelOptions.html)) {
	                  return labelOptions.html;
	                }

	                return labelOptions.text;
	              }();

	              var inner = BX.Tag.render(_templateObject2$1 || (_templateObject2$1 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"ui-label-inner\">", "</span>\n\t\t\t\t\t\t\t"])), labelContent);
	              BX.Dom.append(inner, label);

	              if (BX.Type.isPlainObject(labelOptions.removeButton)) {
	                var button = function () {
	                  if (labelOptions.removeButton.type === BX.Grid.Label.RemoveButtonType.INSIDE) {
	                    return BX.Tag.render(_templateObject3 || (_templateObject3 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t\t\t\t<span class=\"ui-label-icon\"></span>\t\n\t\t\t\t\t\t\t\t\t\t"])));
	                  }

	                  return BX.Tag.render(_templateObject4 || (_templateObject4 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t\t\t<span class=\"main-grid-label-remove-button ", "\"></span>\t\n\t\t\t\t\t\t\t\t\t"])), labelOptions.removeButton.type);
	                }();

	                if (BX.Type.isPlainObject(labelOptions.removeButton.events)) {
	                  _this.bindOnEvents(button, labelOptions.removeButton.events);
	                }

	                BX.Dom.append(button, label);
	              }

	              return label;
	            });
	            var labelsContainer = BX.Tag.render(_templateObject5 || (_templateObject5 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t<div class=\"main-grid-labels\">", "</div>\n\t\t\t\t\t\t"])), labels);
	            BX.Dom.clean(container);
	            var oldLabelsContainer = container.querySelector('.main-grid-labels');

	            if (BX.Type.isDomNode(oldLabelsContainer)) {
	              BX.Dom.replace(oldLabelsContainer, labelsContainer);
	            } else {
	              BX.Dom.append(labelsContainer, container);
	            }
	          } else if (columnOptions.type === 'tags' && BX.Type.isPlainObject(cellContent)) {
	            var tags = cellContent.items.map(function (tagOptions) {
	              var tag = BX.Tag.render(_templateObject6 || (_templateObject6 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"main-grid-tag\"></span>\n\t\t\t\t\t\t\t"])));

	              _this.bindOnEvents(tag, tagOptions.events);

	              if (tagOptions.active === true) {
	                BX.Dom.addClass(tag, 'main-grid-tag-active');
	              }

	              var tagContent = function () {
	                if (BX.Type.isStringFilled(tagOptions.html)) {
	                  return tagOptions.html;
	                }

	                return BX.Text.encode(tagOptions.text);
	              }();

	              var tagInner = BX.Tag.render(_templateObject7 || (_templateObject7 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"main-grid-tag-inner\">", "</span>\n\t\t\t\t\t\t\t"])), tagContent);
	              BX.Dom.append(tagInner, tag);

	              if (tagOptions.active === true) {
	                var removeButton = BX.Tag.render(_templateObject8 || (_templateObject8 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t\t<span class=\"main-grid-tag-remove\"></span>\n\t\t\t\t\t\t\t\t"])));
	                BX.Dom.append(removeButton, tag);

	                if (BX.Type.isPlainObject(tagOptions.removeButton)) {
	                  _this.bindOnEvents(removeButton, tagOptions.removeButton.events);
	                }
	              }

	              return tag;
	            });
	            var tagsContainer = BX.Tag.render(_templateObject9 || (_templateObject9 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t<span class=\"main-grid-tags\">", "</span>\n\t\t\t\t\t\t"])), tags);
	            var addButton = BX.Tag.render(_templateObject10 || (_templateObject10 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t<span class=\"main-grid-tag-add\"></span>\n\t\t\t\t\t\t"])));

	            if (BX.Type.isPlainObject(cellContent.addButton)) {
	              _this.bindOnEvents(addButton, cellContent.addButton.events);
	            }

	            BX.Dom.append(addButton, tagsContainer);
	            var oldTagsContainer = container.querySelector('.main-grid-tags');

	            if (BX.Type.isDomNode(oldTagsContainer)) {
	              BX.Dom.replace(oldTagsContainer, tagsContainer);
	            } else {
	              BX.Dom.append(tagsContainer, container);
	            }
	          } else {
	            BX.Runtime.html(container, cellContent);
	          }
	        }
	      });
	    },
	    getCellById: function getCellById(id) {
	      var headRow = this.parent.getRows().getHeadFirstChild();
	      return babelHelpers.toConsumableArray(this.getCells()).find(function (cell, index) {
	        return headRow.getCellNameByCellIndex(index) === id;
	      });
	    },
	    isTemplate: function isTemplate() {
	      return this.isBodyChild() && /^template_[0-9]$/.test(this.getId());
	    },
	    enableAbsolutePosition: function enableAbsolutePosition() {
	      var headCells = babelHelpers.toConsumableArray(this.parent.getRows().getHeadFirstChild().getCells());
	      var cellsWidth = headCells.map(function (cell) {
	        return BX.Dom.style(cell, 'width');
	      });
	      var cells = this.getCells();
	      cellsWidth.forEach(function (width, index) {
	        BX.Dom.style(cells[index], 'width', width);
	      });
	      BX.Dom.style(this.getNode(), 'position', 'absolute');
	    },
	    disableAbsolutePosition: function disableAbsolutePosition() {
	      BX.Dom.style(this.getNode(), 'position', null);
	    },
	    getHeight: function getHeight() {
	      return BX.Text.toNumber(BX.Dom.style(this.getNode(), 'height'));
	    },
	    setCellActions: function setCellActions(cellActions) {
	      var _this2 = this;

	      Object.entries(cellActions).forEach(function (_ref) {
	        var _ref2 = babelHelpers.slicedToArray(_ref, 2),
	            cellId = _ref2[0],
	            actions = _ref2[1];

	        var cell = _this2.getCellById(cellId);

	        if (cell) {
	          var inner = cell.querySelector('.main-grid-cell-inner');

	          if (inner) {
	            var container = function () {
	              var currentContainer = inner.querySelector('.main-grid-cell-content-actions');

	              if (currentContainer) {
	                BX.Dom.clean(currentContainer);
	                return currentContainer;
	              }

	              var newContainer = BX.Tag.render(_templateObject11 || (_templateObject11 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<div class=\"main-grid-cell-content-actions\"></div>\n\t\t\t\t\t\t\t"])));
	              BX.Dom.append(newContainer, inner);
	              return newContainer;
	            }();

	            if (BX.Type.isArrayFilled(actions)) {
	              actions.forEach(function (action) {
	                var actionClass = function () {
	                  if (BX.Type.isArrayFilled(action["class"])) {
	                    return action["class"].join(' ');
	                  }

	                  return action["class"];
	                }();

	                var button = BX.Tag.render(_templateObject12 || (_templateObject12 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t\t<span class=\"main-grid-cell-content-action ", "\"></span>\n\t\t\t\t\t\t\t\t"])), actionClass);

	                if (BX.Type.isPlainObject(action.events)) {
	                  _this2.bindOnEvents(button, action.events);
	                }

	                if (BX.Type.isPlainObject(action.attributes)) {
	                  BX.Dom.attr(button, action.attributes);
	                }

	                BX.Dom.append(button, container);
	              });
	            }
	          }
	        }
	      });
	    },

	    /**
	     * @private
	     */
	    initElementsEvents: function initElementsEvents() {
	      var _this3 = this;

	      var buttons = babelHelpers.toConsumableArray(this.getNode().querySelectorAll('.main-grid-cell [data-events]'));

	      if (BX.Type.isArrayFilled(buttons)) {
	        buttons.forEach(function (button) {
	          var events = eval(BX.Dom.attr(button, 'data-events'));

	          if (BX.Type.isPlainObject(events)) {
	            BX.Dom.attr(button, 'data-events', null);

	            _this3.bindOnEvents(button, events);
	          }
	        });
	      }
	    },

	    /**
	     * @private
	     * @param event
	     */
	    onElementClick: function onElementClick(event) {
	      event.stopPropagation();
	    },

	    /**
	     * @private
	     */
	    bindOnEvents: function bindOnEvents(button, events) {
	      if (BX.Type.isDomNode(button) && BX.Type.isPlainObject(events)) {
	        BX.Event.bind(button, 'click', this.onElementClick.bind(this));

	        var target = function () {
	          var selector = BX.Dom.attr(button, 'data-target');

	          if (selector) {
	            return button.closest(selector);
	          }

	          return button;
	        }();

	        var event = new BX.Event.BaseEvent({
	          data: {
	            button: button,
	            target: target,
	            row: this
	          }
	        });
	        event.setTarget(target);
	        Object.entries(events).forEach(function (_ref3) {
	          var _ref4 = babelHelpers.slicedToArray(_ref3, 2),
	              eventName = _ref4[0],
	              handler = _ref4[1];

	          var preparedHandler = eval(handler);
	          BX.Event.bind(button, eventName, preparedHandler.bind(null, event));
	        });
	      }
	    },
	    setCounters: function setCounters(counters) {
	      var _this4 = this;

	      if (BX.Type.isPlainObject(counters)) {
	        Object.entries(counters).forEach(function (_ref5) {
	          var _ref6 = babelHelpers.slicedToArray(_ref5, 2),
	              columnId = _ref6[0],
	              counter = _ref6[1];

	          var cell = _this4.getCellById(columnId);

	          if (BX.Type.isDomNode(cell)) {
	            var cellInner = cell.querySelector('.main-grid-cell-inner');

	            var counterContainer = function () {
	              var container = cell.querySelector('.main-grid-cell-counter');

	              if (BX.Type.isDomNode(container)) {
	                return container;
	              }

	              return BX.Tag.render(_templateObject13 || (_templateObject13 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"main-grid-cell-counter\"></span>\n\t\t\t\t\t\t\t"])));
	            }();

	            var uiCounter = function () {
	              var currentCounter = counterContainer.querySelector('.ui-counter');

	              if (BX.Type.isDomNode(currentCounter)) {
	                return currentCounter;
	              }

	              var newCounter = BX.Tag.render(_templateObject14 || (_templateObject14 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"ui-counter\"></span>\n\t\t\t\t\t\t\t"])));
	              BX.Dom.append(newCounter, counterContainer);
	              return newCounter;
	            }();

	            if (BX.Type.isPlainObject(counter.events)) {
	              _this4.bindOnEvents(uiCounter, counter.events);
	            }

	            var counterInner = function () {
	              var currentInner = uiCounter.querySelector('.ui-counter-inner');

	              if (BX.Type.isDomNode(currentInner)) {
	                return currentInner;
	              }

	              var newInner = BX.Tag.render(_templateObject15 || (_templateObject15 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t\t\t\t<span class=\"ui-counter-inner\"></span>\n\t\t\t\t\t\t\t"])));
	              BX.Dom.append(newInner, uiCounter);
	              return newInner;
	            }();

	            if (BX.Type.isStringFilled(counter.type)) {
	              Object.values(BX.Grid.Counters.Type).forEach(function (type) {
	                BX.Dom.removeClass(counterContainer, "main-grid-cell-counter-".concat(type));
	              });
	              BX.Dom.addClass(counterContainer, "main-grid-cell-counter-".concat(counter.type));
	            }

	            if (BX.Type.isStringFilled(counter.color)) {
	              Object.values(BX.Grid.Counters.Color).forEach(function (color) {
	                BX.Dom.removeClass(uiCounter, color);
	              });
	              BX.Dom.addClass(uiCounter, counter.color);
	            }

	            if (BX.Type.isStringFilled(counter.size)) {
	              Object.values(BX.Grid.Counters.Size).forEach(function (size) {
	                BX.Dom.removeClass(uiCounter, size);
	              });
	              BX.Dom.addClass(uiCounter, counter.size);
	            }

	            if (BX.Type.isStringFilled(counter["class"])) {
	              BX.Dom.addClass(uiCounter, counter["class"]);
	            }

	            if (BX.Type.isStringFilled(counter.value) || BX.Type.isNumber(counter.value)) {
	              var currentValue = BX.Text.toNumber(counterInner.innerText);
	              var value = BX.Text.toNumber(counter.value);

	              if (value > 0) {
	                if (value < 100) {
	                  counterInner.innerText = counter.value;
	                } else {
	                  counterInner.innerText = '99+';
	                }

	                if (counter.animation !== false) {
	                  if (value !== currentValue) {
	                    if (value > currentValue) {
	                      BX.Dom.addClass(counterInner, 'ui-counter-plus');
	                    } else {
	                      BX.Dom.addClass(counterInner, 'ui-counter-minus');
	                    }
	                  }

	                  BX.Event.bindOnce(counterInner, 'animationend', function (event) {
	                    if (event.animationName === 'uiCounterPlus' || event.animationName === 'uiCounterMinus') {
	                      BX.Dom.removeClass(counterInner, ['ui-counter-plus', 'ui-counter-minus']);
	                    }
	                  });
	                }
	              }
	            }

	            if (BX.Text.toNumber(counter.value) > 0) {
	              var align = counter.type === BX.Grid.Counters.Type.RIGHT ? 'right' : 'left';

	              if (align === 'left') {
	                BX.Dom.prepend(counterContainer, cellInner);
	              } else if (align === 'right') {
	                BX.Dom.append(counterContainer, cellInner);
	              }
	            } else {
	              var leftAlignedClass = "main-grid-cell-counter-".concat(BX.Grid.Counters.Type.LEFT_ALIGNED);

	              if (BX.Dom.hasClass(counterContainer, leftAlignedClass)) {
	                BX.remove(uiCounter);
	              } else {
	                BX.remove(counterContainer);
	              }
	            }
	          }
	        });
	      }
	    }
	  };
})();