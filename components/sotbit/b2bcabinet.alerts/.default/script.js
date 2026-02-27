var NotyAlerts = function() {

    var _componentNoty = function(title) {
        if (typeof Noty == 'undefined') {
            console.warn('Warning - noty.min.js is not loaded.');
            return;
        }

        setTimeout(show_stack_custom_top, 1000);

        function show_stack_custom_top() {
            new Noty({
                layout: 'top',
                theme: 'limitless',
                text: `<span class="alert-icon bg-primary text-white d-flex align-items-center">
                            <i class="ph-warning-circle"></i>
                        </span>
                        ${title}`,
                type: 'default',
                timeout: 5000,
                progressBar: true,
                closeWith: ["button"],
                callbacks: {
                    onClose: function() {
                        BX.ajax.runComponentAction('sotbit:b2bcabinet.alerts', 'closeAlert', {
                            mode: 'class',
                        });
                    }
                },
            }).show();
        }
    };

    return {
        init: function(title) {
            _componentNoty(title);
        }
    }
}();

