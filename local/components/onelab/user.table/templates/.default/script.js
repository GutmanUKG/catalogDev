'use strict'
document.addEventListener('DOMContentLoaded', () => {
    let table = document.querySelector('table.table');
    let userList = document.querySelectorAll('.user_row');
    let sortFields = document.querySelectorAll('td[data-key]');
    const btnClearFilter = document.querySelector('.btn-clear');
    console.log(table)
    table.style.display = 'block'
    userList.forEach(item => {
        item.addEventListener('click', (e) => {
            let target = e.target;
            if (target.classList.contains('remove_btn')) {
                if (confirm("Вы уверенны что хотите деактивировать пользователя ?")) {
                    console.log('Удаление пользователя')
                    removeUser(target.dataset.id)
                }
            }
        });
    });

    // Применение сортировки по сохраненным в cookie параметрам
    if (getCookie('sort_date')) {
        modifierUrl('sort_date', getCookie('sort_date'))
    }
    if (getCookie('sort_name')) {
        modifierUrl('sort_name', getCookie('sort_name'))
    }
    if (getCookie('sort_nds')) {
        modifierUrl('sort_nds', getCookie('sort_nds'))
    }
    if (getCookie('sort_reg')) {
        modifierUrl('sort_reg', getCookie('sort_reg'))
    }
    if (getCookie('sort_doc')) {
        modifierUrl('sort_doc', getCookie('sort_doc'))
    }
    if (getCookie('sort_so')) {
        modifierUrl('sort_so', getCookie('sort_so'))
    }
    if (getCookie('sort_manager')) {
        modifierUrl('sort_manager', getCookie('sort_manager'))
    }

    sortFields.forEach(i => {
        //Вытаскиваем из куки поля сортировки и ставим их в атрибуты
        let key = i.dataset.key;

        if (key === '0' && getCookie('sort_date')) {
            i.dataset.sort_0 = getCookie('sort_date');
        }
        if (key === '1' && getCookie('sort_name')) {
            i.dataset.sort_1 = getCookie('sort_name');
        }
        if (key === '3' && getCookie('sort_days')) {
            i.dataset.sort_3 = getCookie('sort_days');
        }
        if (key === '1699' && getCookie('sort_nds')) {
            i.dataset.sort_1699 = getCookie('sort_nds');
        }
        if (key === '1700' && getCookie('sort_reg')) {
            i.dataset.sort_1700 = getCookie('sort_reg');
        }
        if (key === '1702' && getCookie('sort_doc')) {
            i.dataset.sort_1702 = getCookie('sort_doc');
        }
        if (key === '1703' && getCookie('sort_so')) {
            i.dataset.sort_1703 = getCookie('sort_so');
        }
        if (key === '2000' && getCookie('sort_manager')) {
            i.dataset.sort_2000 = getCookie('sort_manager');
        }
        i.addEventListener('click', () => {
            let key = i.dataset.key;
            let sortName = `data-sort_${key}`;

            // Сортировка по дате регистрации
            if (sortName === 'data-sort_0') {
                console.log('sort by date');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_0 = newSort;
                modifierUrl('sort_date', newSort);
                setCookie('sort_date', newSort);
                window.location.reload();
            }

            // Сортировка по имени
            if (sortName === 'data-sort_1') {
                console.log('sort by name');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_1 = newSort;
                modifierUrl('sort_name', newSort);
                setCookie('sort_name', newSort);
                window.location.reload();
            }

            // Сортировка по количеству дней с покупки
            if (sortName === 'data-sort_3') {
                console.log('sort by days');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_3 = newSort;
                modifierUrl('sort_days', newSort);
                setCookie('sort_days', newSort);
                window.location.reload();
            }

            // Сортировка по наличию НДС
            if (sortName === 'data-sort_1699') {
                console.log('sort by NDS');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_1699 = newSort;
                modifierUrl('sort_nds', newSort);
                setCookie('sort_nds', newSort);
                window.location.reload();
            }

            // Сортировка по документам Регистрации
            if (sortName === 'data-sort_1700') {
                console.log('sort by reg');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_1700 = newSort;
                modifierUrl('sort_reg', newSort);
                setCookie('sort_reg', newSort);
                window.location.reload();
            }

            // Сортировка по документам Договора
            if (sortName === 'data-sort_1702') {
                console.log('sort by doc');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_1702 = newSort;
                modifierUrl('sort_doc', newSort);
                setCookie('sort_doc', newSort);
                window.location.reload();
            }

            // Сортировка по документам Соглашения
            if (sortName === 'data-sort_1703') {
                console.log('sort by agreement');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_1703 = newSort;
                modifierUrl('sort_so', newSort);
                setCookie('sort_so', newSort);
                window.location.reload();
            }

            // Сортировка по менеджер
            if (sortName === 'data-sort_2000') {
                console.log('sort by manager');
                let currentSort = i.getAttribute(sortName) || 'asc';
                let newSort = (currentSort === 'asc') ? 'desc' : 'asc';
                i.dataset.sort_2000 = newSort;
                modifierUrl('sort_manager', newSort);
                setCookie('sort_manager', newSort);
                window.location.reload();
            }

        });
    });

    // Функция для удаления пользователя
    function removeUser(id) {
        let data = new FormData();
        data.append('ID', id);
        data.append('ACTION', 'REMOVE');
        fetch('/local/components/onelab/user.table/templates/.default/ajax.php', {
            method: 'POST',
            body: data,

        })
            .then(res => res.json())
            .then((data) => {
                if (data.status == 'success') {
                    console.log(data.message);
                    window.location.reload()
                } else {
                    alert(data.message);
                }
            });
    }

    // Функция для обновления URL с параметрами сортировки
    function modifierUrl(param, value) {
        let url = new URL(window.location.href);
        url.searchParams.set(param, value);
        window.history.pushState({}, '', url);
    }

    // Получение значения cookie
    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    // Установка cookie
    function setCookie(name, value, options = {}) {
        options = {
            path: '/',
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }

    // Удаление cookie
    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
    }

    // Очистка фильтров и cookie
    btnClearFilter.addEventListener('click', () => {
        const url = window.location.href;
        const cleanUrl = url.split('?')[0];
        window.history.replaceState(null, null, cleanUrl);
        deleteCookie('sort_date');
        deleteCookie('sort_days');
        deleteCookie('sort_name');
        deleteCookie('sort_nds');
        deleteCookie('sort_reg');
        deleteCookie('sort_doc');
        deleteCookie('sort_so');
        deleteCookie('sort_manager');
        window.location.reload();
    });



    //Работа с коментами

    let show_comment_btns = document.querySelectorAll('.show_comment'),
        add_comment = document.querySelectorAll('.add_comment'),
        popup_comment = document.querySelector('#popup_comment'),
        overlay = document.querySelector('.overlay'),
        popupForm = popup_comment.querySelector('form'),
        btnSubmit = popupForm.querySelector('.save'),
        status = popup_comment.querySelector('.status'),
        closeBtn = popup_comment.querySelector('.close');

    show_comment_btns.forEach(item=>{
        item.addEventListener('click', ()=>{

            showComentUser(item.dataset.id);
        })
    })


    function showComentUser(userID){
        btnSubmit.disabled = true
        let data = new FormData();
        data.append('ID', userID);
        data.append('ACTION', 'SHOW');
        fetch('/local/components/onelab/user.table/templates/.default/ajax.php', {
            method: 'POST',
            body: data,
        })
            .then(res=> res.json())
            .then((data)=>{
                if(data.status == 'success'){
                    btnSubmit.disabled = false
                    buildPopupComment(data.message, userID)
                }
            })
    }


    add_comment.forEach(item=>{
        item.addEventListener('click', ()=>{
            buildPopupComment('', item.dataset.id)
        })
    })

    popupForm.addEventListener('submit', (e)=>{
        e.preventDefault();
        btnSubmit.disabled = true
        let  text = popupForm.querySelector('textarea');
        addComment(btnSubmit.dataset.id , text.value)
    })
    function addComment(id, text){
            let data = new FormData();
            data.append('ID', id);
            data.append('TEXT', text);
            data.append('ACTION', 'ADD');
            fetch('/local/components/onelab/user.table/templates/.default/ajax.php', {
                method: 'POST',
                body: data,
            })
                .then(res=>res.json())
                .then((data)=>{
                    if(data.status == 'success'){
                        console.info(data.message)
                        status.textContent = data.message
                        status.classList.add('info')
                        status.classList.remove('danger')
                        btnSubmit.disabled = false
                        setTimeout(()=>{
                            window.location.reload()
                        }, 1500)
                    }else{
                        status.textContent = data.message
                        status.classList.add('danger')
                        status.classList.remove('info')
                        btnSubmit.disabled = true
                    }
                })
    }

    function buildPopupComment(text , userID){
        let textContentPopup = popup_comment.querySelector('.text')
        let btnSubmit = popup_comment.querySelector('.save');
        btnSubmit.dataset.id = userID
        popup_comment.style.display = 'flex';
        overlay.style.display = 'block';
        textContentPopup.value = text;
    }

    closeBtn.addEventListener('click', closePopup)
    overlay.addEventListener('click', closePopup)

    function closePopup(){
        popup_comment.style.display = '';
        overlay.style.display = '';
    }
});
