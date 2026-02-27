document.addEventListener("DOMContentLoaded", function(event) {
	const activeElement = document.querySelector('.nav-sidebar .nav-link.active');
	if (activeElement) {
		const box = activeElement.getBoundingClientRect()

		if (box.bottom > document.documentElement.clientHeight) {
			activeElement.scrollIntoView();
		}
	}
});

function OpenMenuNode(oThis)
{
	if (oThis.parentNode.className == '')
		oThis.parentNode.className = 'menu-close';
	else
		oThis.parentNode.className = '';
	return false;
}
