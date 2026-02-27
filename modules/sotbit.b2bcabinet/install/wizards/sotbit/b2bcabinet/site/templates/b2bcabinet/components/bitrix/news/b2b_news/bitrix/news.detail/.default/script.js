var BlogSingle = function() {
    var _componentFancybox = function() {
        if (!$().fancybox) {
            console.warn('Warning - fancybox.min.js is not loaded.');
            return;
        }

        $('[data-popup="lightbox"]').fancybox({
            padding: 3
        });
    };

    return {
        init: function() {
            _componentFancybox();
        }
    }
}();

document.addEventListener('DOMContentLoaded', function() {
    BlogSingle.init();
});