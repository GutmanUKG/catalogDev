const Trumbowyg = function() {

    const _componentTrumbowyg = function() {
        if (!$().trumbowyg) {
            console.warn('Warning - trumbowyg.min.js is not loaded.');
            return;
        }

		$.trumbowyg.svgPath = '/local/templates/b2bcabinet_v2.0/assets/js/plugins/editor/trumbowyg/ui/icons.svg';

		// Default initialization
        $('textarea.trumbowyg:not(.trumbowyg-textarea)').trumbowyg({
			btns: [
                ['strong', 'em'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['undo', 'redo'],
            ],
			lang: 'ru'
		});
	};

	return {
        init: function() {
            _componentTrumbowyg();
        }
    }
}();

document.addEventListener('DOMContentLoaded', function() {
    Trumbowyg.init();
});
