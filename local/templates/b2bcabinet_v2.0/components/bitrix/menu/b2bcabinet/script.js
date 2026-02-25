document.addEventListener("DOMContentLoaded", function(event) {
	const activeElement = document.querySelector('.nav-sidebar .nav-link.active');

	if (activeElement) {
		const box = activeElement.getBoundingClientRect();

		if (box.bottom > document.documentElement.clientHeight) {
			activeElement.scrollIntoView();
		}
	}

	// Избранные категории
	const favoriteMenu = document.querySelector('.favorite_menu');
	const favoriteBtns = document.querySelectorAll('.favorite_btn');
	const menu = document.querySelector('.nav-b2bcabinet');
	const closeAjaxMenu = document.querySelector('#close_ajax_menu');
	const favoriteWrapper = document.querySelector('.favorite-wrapper');
	const ajaxMenu = document.querySelector('#ajax_menu');
	let itemsMenu = menu.querySelectorAll('.nav-item');
	let favoriteLinks = [];
	closeAjaxMenu.addEventListener('click', ()=>{
		favoriteWrapper.classList.remove('nav-item-open')
		ajaxMenu.classList.remove('show')
	})
	// Добавление / удаление элементов меню в избранное меню
	function menuHandlers() {
		menu.addEventListener('click', (e) => {
			let target = e.target;

			if (target.classList.contains('favorite_btn')) {
				e.preventDefault();
				e.stopPropagation();

				// Disable the button
				target.disabled = true;

				if (!target.classList.contains('active')) {
					//target.parentNode.parentNode.classList.add('favorite');
					//target.classList.add('active');
					favoriteLinks = [];
					let url = target.parentNode.href;
					let links = [];
					async function getbreadcrumb() {
						let promise = new Promise((resolve, reject) => {
							$.get(url, (data) => {
								resolve($(data).find('.breadcrumb'));
							});
						});
						let result = await promise;
						let links = result[0].querySelectorAll('a');
						let lastSpan = result[0].querySelector('span');
						let data = [];
						links.forEach((item) => {
							data.push({
								link: item.href,
								name: item.textContent
							});
						});
						data.push({
							link: url,
							name: lastSpan.textContent
						});

						addMenuUser(data, target);
					}
					getbreadcrumb();
				} else {
					let id = '';

					if (target.closest('.catalog-wrapper')) {
						id = target.closest('.nav-link').getAttribute('section-id');
						deleteFavorite(`section_id=${id}`, target);
					} else {
						let parentElement = target.closest('li'),
							link = parentElement.querySelector('a');

						deleteFavorite(`section_id=${link.getAttribute('section-id')}`, target);
					}
				}
			}
		});
	}
	menuHandlers();

	// Создание элементов инфоблока для пользователей
	function addMenuUser(data, button) {
		fetch('/generate_user_menu.php', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		})
			.then(res => res.json())
			.then((response) => {
				if (response.status == 'success') {
					getUserMenu();
				} else {
					console.error("Ошибка: " + response.message);
				}
			})
			.finally(() => {
				// Enable the button
				button.disabled = false;
			});
	}

	// Получение верстки меню
	function getUserMenu() {
		fetch('/generate_favorite_menu.php', {
			method: 'POST',
		})
			.then(response => response.json())
			.then((data) => {
				let ajax_menu = document.querySelector('#ajax_menu');
				ajax_menu.innerHTML = data.html;
				if (data.html == '') {
					ajax_menu.closest('.favorite-wrapper').classList.remove('active');
				} else {
					ajax_menu.closest('.favorite-wrapper').classList.add('active');
				}
				chekerFav();

			});
	}

	// Удаление из избранного
	function deleteFavorite(getText, button) {
		fetch(`/remove_element_user_menu.php?${getText}`, {
			method: 'GET'
		})
			.then(res => res.json())
			.then((response) => {
				if (response.status == 'success') {
					//window.location.reload()
					getUserMenu();
				} else {
					console.error("Ошибка: " + response.message);
				}
			})
			.finally(() => {
				// Enable the button
				button.disabled = false;
			});
	}

	// Выставление активности в пунктах меню если они есть в избранном меню
	function chekerFav() {

		const catalogWrapper = document.querySelector('.catalog-wrapper'),
			favoriteWrapper = document.querySelector('.favorite-wrapper'),
			catalogLinks = catalogWrapper.querySelectorAll('a'),
			favoriteLinks = favoriteWrapper.querySelectorAll('a');
		const catalogArray = Array.from(catalogLinks);
		const favoriteArray = Array.from(favoriteLinks);
		const fav_toggler = document.querySelector('#fav_toggler');
		fav_toggler.addEventListener('click', () => {
			fav_toggler.classList.toggle('down');
		});
		const favoriteSet = new Set(Array.from(favoriteArray, link => link.href));

		//Перебор ссылок основного каталога и если они пристутсвуют в избранном каталоге то выставляем классы и section-id
		// Если их уже нет т.е ссылка была удаленна то убираем классы с кнопки что бы можно было повторно добавить
		catalogArray.forEach(catalogLink => {
			let btn = catalogLink.querySelector('.favorite_btn');
			if (favoriteSet.has(catalogLink.href)) {
				const favoriteLink = Array.from(favoriteArray).find(link => link.href === catalogLink.href);
				catalogLink.setAttribute('section-id', favoriteLink.getAttribute('section-id'));
				if (btn) {
					btn.classList.add('active');
				}
			} else {
				if (btn && btn.classList.contains('active')) {
					btn.classList.remove('active')
				}
			}
		});

	}

	function OpenMenuNode(oThis) {
		if (oThis.parentNode.className == '') {
			oThis.parentNode.className = 'menu-close';
		} else {
			oThis.parentNode.className = '';
		}
		return false;
	}

	// Первый запуск функций
	getUserMenu();
});
