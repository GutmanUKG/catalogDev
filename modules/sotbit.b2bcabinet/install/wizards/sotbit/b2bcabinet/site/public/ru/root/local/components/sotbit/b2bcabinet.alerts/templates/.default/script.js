var Pnotify = function() {

    var _componentPnotify = function(title) {
        if (typeof PNotify == 'undefined') {
            console.warn('Warning - pnotify.min.js is not loaded.');
            return;
        }

        setTimeout(show_stack_custom_top, 1000);

        function show_stack_custom_top() {
            var opts = {
                title: title,
                width: "100%",
                height: "65px",
                addclass: "alert alert-info stack-custom-top border-info alert-styled-left alert-dismissible b2bcabinet-alert",
                hide: true,
                delay: 5000,
                buttons: {
                    closer: true,
                    sticker: false,
                    closer_hover: false,
                    sticker_hover: false
                },
                type: "info",
                before_close: function(PNotify, timer_hide) {
                    var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.alerts', 'closeAlert', {
                        mode: 'class',
                    });
                },
            };
            new PNotify(opts);
        }
    };

    return {
        init: function(title) {
            _componentPnotify(title);
        }
    }
}();

