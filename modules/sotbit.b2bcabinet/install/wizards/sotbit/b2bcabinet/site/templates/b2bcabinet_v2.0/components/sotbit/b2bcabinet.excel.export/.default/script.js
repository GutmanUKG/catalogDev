(function () {

    if (!!window.JCB2BExcelExport)
        return;

    window.JCB2BExcelExport = function (params) {
        this.siteId = params.siteId || '';
        this.componentPath = params.componentPath || '';
        this.parameters = params.parameters || '';
        this.btnExport = document.querySelector(params.btnSelector);
        this.filter = params.filter;
        this.cond_tree_params = params.cond_tree_params;

        if (this.btnExport) {
            BX.bind(this.btnExport, 'click', BX.delegate(this.exportModeDefaultAction, this));
        }
    };

    window.JCB2BExcelExport.prototype =
        {
            exportModeDefaultAction: function() {

                this.setDisabled(true);
                this.setIcon("spinner-grow vertical-align-top me-2");
                var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.excel.export', 'exportExcel', {
                    signedParameters: this.parameters,
                    mode: 'class',
                    data:{
                        arFilter: this.filter,
                        condTreeParams: this.cond_tree_params
                    }
                });

                request.then(
                    function (response) {
                        if (response.data.filePath) {
                            this.downloadFile(response.data.filePath);
                        }
                        this.setDisabled(false);
                        this.setDefaultIcon();
                    }.bind(this),

                    function (response) {
                        this.printConsoleError(response.errors);
                        this.setDisabled(false);
                        this.setDefaultIcon();
                    }.bind(this),
                );
            },
            downloadFile(filePath) {
                var now = new Date();
                var dd = now.getDate();
                if (dd < 10) dd = '0' + dd;
                var mm = now.getMonth() + 1;
                if (mm < 10) mm = '0' + mm;
                var hh = now.getHours();
                if (hh < 10) hh = '0' + hh;
                var mimi = now.getMinutes();
                if (mimi < 10) mimi = '0' + mimi;
                var ss = now.getSeconds();
                if (ss < 10) ss = '0' + ss;
                var rand = 0 - 0.5 + Math.random() * (999999999 - 0 + 1);
                rand = Math.round(rand);
                var name = 'blank_' + now.getFullYear() + '_' + mm + '_' + dd + '_' + hh + '_' + mimi + '_' + ss + '_' + rand + '.xlsx';
                var link = document.createElement('a');
                link.setAttribute('href', filePath);
                link.setAttribute('download', name);
                var event = document.createEvent("MouseEvents");
                event.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                link.dispatchEvent(event);
            },
            setIcon: function (icon) {
                var iconContainer = this.btnExport.querySelector("i");
                iconContainer.setAttribute("class", icon);
            },
            setDefaultIcon: function () {
                var iconContainer = this.btnExport.querySelector("i");
                iconContainer.setAttribute("class", "ph-arrow-line-up me-2");
            },
            setDisabled: function (value) {
                this.btnExport.disabled = value;
            },
            printConsoleError: function(arErrors) {
                arErrors.forEach(function(error, e, arErrors) {
                    console.error(error);
                });
            }
        };
})();


function initFilterConditionsControl(params)
{
    var data = JSON.parse(params.data);
    if (data)
    {
        window['filter_conditions_' + params.propertyID] = new FilterConditionsParameterControl(data, params);
    }
}

function FilterConditionsParameterControl(data, params)
{
    this.params = params || {};
    this.data = data || {};

    BX.addCustomEvent('onTreeConditionsInit', BX.proxy(this.modifyTreeParams, this));
}

FilterConditionsParameterControl.prototype =
    {

        deleteFromArray: function(keys, array)
        {
            if (!BX.type.isArray(keys) || !BX.type.isArray(array))
                return;

            for (var i = array.length; --i;)
            {
                if (!!array[i] && array.hasOwnProperty(i))
                {
                    if (BX.util.in_array(i, keys))
                    {
                        array.splice(i, 1);
                    }
                }
            }
        },


        modifyTreeParams: function(arParams, obTree, obControls)
        {
            if (!obControls)
                return;

            var i, control, keysToDelete = [];

            for (i in obControls)
            {
                if (obControls.hasOwnProperty(i))
                {
                    control = obControls[i];
                    if (control.group)
                    {
                        this.modifyCondGroup(control);
                    }
                    else
                    {
                        if (this.modifyCondValueGroup(control))
                        {
                            keysToDelete.push(i);
                        }
                    }
                }
            }

            this.deleteFromArray(keysToDelete, obControls);
        },

        modifyCondGroup: function(ctrl)
        {
            var k;

            if (ctrl.visual)
            {
                for (k in ctrl.visual.values)
                {
                    if (ctrl.visual.values.hasOwnProperty(k))
                    {
                        if (ctrl.visual.values[k].True === 'False')
                        {
                            ctrl.visual.values.splice(k, 1);
                            ctrl.visual.logic.splice(k, 1);
                        }
                    }
                }
            }

            if (ctrl.control)
            {
                for (k in ctrl.control)
                {
                    if (ctrl.control.hasOwnProperty(k))
                    {
                        ctrl.control[k].dontShowFirstOption = true;

                        if (ctrl.control[k].id === 'True')
                        {
                            delete ctrl.control[k].values.False;
                        }
                    }
                }
            }
        },

        modifyCondValueGroup: function(ctrl)
        {
            if (!ctrl || !ctrl.children || !ctrl.children.length)
                return;

            var propertyPrefix = 'CondIBProp',
                allowedFields = [
                    'CondIBXmlID', /*'CondIBActive',*/ 'CondIBDateActiveFrom', 'CondIBDateActiveTo',
                    'CondIBSort', 'CondIBDateCreate', 'CondIBCreatedBy', 'CondIBTimestampX', 'CondIBModifiedBy',
                    'CondIBTags', 'CondCatQuantity', 'CondCatWeight'
                ],
                del, current, name;

            if (this.params.RIGHTS_DISPLAY_IBLOCK) {
                allowedFields.push('CondIBSection');
            }
            for (var k in ctrl.children)
            {
                if (ctrl.children.hasOwnProperty(k))
                {
                    current = ctrl.children[k];
                    del = true;

                    if (BX.util.in_array(current.controlId, allowedFields))
                    {
                        del = false;
                    }
                    else
                    {
                        name = current.controlId.split(':');
                        if (name[1] && name[1] != this.data.iblockId && name[1] != this.data.offersIblockId)
                        {
                            return true;
                        }

                        if (name[0] === propertyPrefix && name[2])
                        {
                            del = false;
                        }
                    }

                    if (del)
                    {
                        delete ctrl.children[k];
                    }
                }
            }

            ctrl.children = ctrl.children.filter(function(val){return val});

            return false;
        },
    };