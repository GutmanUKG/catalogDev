// 'use strict'
// document.addEventListener('DOMContentLoaded', ()=>{
//
//     let userList = document.querySelectorAll('.user_row');
//
//     let sortFields = document.querySelectorAll('td[data-key]');
//     const btnClearFilter = document.querySelector('.btn-clear');
//     userList.forEach(item=>{
//         item.addEventListener('click', (e)=>{
//             let target = e.target;
//             if(target.classList.contains('remove_btn')){
//                 if (confirm("Вы уверенны что хотите деактивировать пользователя ?")) {
//                     console.log('Удаление пользователя')
//                     removeUser(target.dataset.id)
//                 }
//             }
//         })
//     });
//     if(getCookie('sort_date')){
//         modifierUrl('sort_date', getCookie('sort_date'))
//     }
//     if(getCookie('sort_name')){
//         modifierUrl('sort_name', getCookie('sort_name'))
//     }
//     if(getCookie('sort_nds')){
//         modifierUrl('sort_nds', getCookie('sort_nds'))
//     }
//     if(getCookie('sort_reg')){
//         modifierUrl('sort_reg', getCookie('sort_reg'))
//     }
//     if(getCookie('sort_doc')){
//         modifierUrl('sort_doc', getCookie('sort_doc'))
//     }
//     if(getCookie('sort_so')){
//         modifierUrl('sort_so', getCookie('sort_so'))
//     }
//     sortFields.forEach(i=>{
//         //Вытаскиваем из куки поля сортировки и ставим их в атрибуты
//         if(getCookie('sort_date')){
//             i.dataset.sort_0 = getCookie('sort_date')
//         }
//         if(getCookie('sort_name')){
//             i.dataset.sort_1 = getCookie('sort_name')
//         }
//         if(getCookie('sort_days')){
//             i.dataset.sort_2 = getCookie('sort_days')
//         }
//         if(getCookie('sort_nds')){
//             i.dataset.sort_1699 = getCookie('sort_nds')
//         }
//         if(getCookie('sort_reg')){
//             i.dataset.sort_1700 = getCookie('sort_reg')
//         }
//         if(getCookie('sort_doc')){
//             i.dataset.sort_1702 = getCookie('sort_doc')
//         }
//         if(getCookie('sort_so')){
//             i.dataset.sort_1703 = getCookie('sort_so')
//         }
//         i.addEventListener('click', ()=>{
//             let key = i.dataset.key;
//             let sortName = `data-sort_${key}`;
//             //Сортировка по дате регистрации
//             if(sortName == 'data-sort_0'){
//                 console.log('sort by date')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_0 = 'desc'
//                     modifierUrl('sort_date', i.getAttribute(sortName))
//                     setCookie('sort_date',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_0 = 'asc'
//                     modifierUrl('sort_date', i.getAttribute(sortName))
//                     setCookie('sort_date',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по Имени
//             if(sortName == 'data-sort_1'){
//                 console.log('sort by name')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_1 = 'desc'
//                     modifierUrl('sort_name', i.getAttribute(sortName))
//                     setCookie('sort_name',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_1 = 'asc'
//                     modifierUrl('sort_name', i.getAttribute(sortName))
//                     setCookie('sort_name',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по кол-ву дней от покупки
//             if(sortName == 'data-sort_2'){
//                 console.log('sort by days')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_2 = 'desc'
//                     modifierUrl('sort_days', i.getAttribute(sortName))
//                     setCookie('sort_days',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_2 = 'asc'
//                     modifierUrl('sort_days', i.getAttribute(sortName))
//                     setCookie('sort_days',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по наличию документов НДС
//             if(sortName == 'data-sort_1699'){
//                 console.log('sort by NDS')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_1699 = 'desc'
//                     modifierUrl('sort_nds', i.getAttribute(sortName))
//                     setCookie('sort_nds',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_1699 = 'asc'
//                     modifierUrl('sort_nds', i.getAttribute(sortName))
//                     setCookie('sort_nds',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по наличию документов Регистрации
//             if(sortName == 'data-sort_1700'){
//                 console.log('sort by reg')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_1700 = 'desc'
//                     modifierUrl('sort_reg', i.getAttribute(sortName))
//                     setCookie('sort_reg',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_1700 = 'asc'
//                     modifierUrl('sort_reg', i.getAttribute(sortName))
//                     setCookie('sort_reg',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по наличию документов Договор
//             if(sortName == 'data-sort_1702'){
//                 console.log('sort by doc')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_1702 = 'desc'
//                     modifierUrl('sort_doc', i.getAttribute(sortName))
//                     setCookie('sort_doc',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_1702 = 'asc'
//                     modifierUrl('sort_doc', i.getAttribute(sortName))
//                     setCookie('sort_doc',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//             //Сортировка по наличию документов Соглашение
//             if(sortName == 'data-sort_1703'){
//                 console.log('sort by doc')
//                 if(i.getAttribute(sortName) == 'asc'){
//                     i.dataset.sort_1703 = 'desc'
//                     modifierUrl('sort_so', i.getAttribute(sortName))
//                     setCookie('sort_so',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }else{
//                     i.dataset.sort_1703 = 'asc'
//                     modifierUrl('sort_so', i.getAttribute(sortName))
//                     setCookie('sort_so',  i.getAttribute(sortName))
//                     window.location.reload()
//                 }
//             }
//
//
//
//         })
//     })
//
//
//
//
//
//     function removeUser(id) {
//         let data = new FormData;
//         data.append('ID', id);
//         fetch('/local/components/onelab/user.table/templates/.default/ajax.php', {
//             method: 'POST',
//             body: data
//         })
//             .then(res => res.json())
//             .then((data) => {
//                 if(data.status == 'success'){
//                     console.log(data.message);
//                 } else {
//                     alert(data.message);
//                 }
//             });
//     }
//
//     // Функция для обновления URL с параметрами сортировки
//     function modifierUrl(param, value) {
//         let url = new URL(window.location.href);
//
//         // Обновляем параметр сортировки
//         url.searchParams.set(param, value);
//
//         // Обновляем URL в истории браузера
//         window.history.pushState({}, '', url);
//         //window.location.reload();
//     }
//
//     // Получение значения cookie
//     function getCookie(name) {
//         let matches = document.cookie.match(new RegExp(
//             "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
//         ));
//         return matches ? decodeURIComponent(matches[1]) : undefined;
//     }
//
//     // Установка cookie
//     function setCookie(name, value, options = {}) {
//         options = {
//             path: '/',
//             ...options
//         };
//
//         if (options.expires instanceof Date) {
//             options.expires = options.expires.toUTCString();
//         }
//
//         let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
//
//         for (let optionKey in options) {
//             updatedCookie += "; " + optionKey;
//             let optionValue = options[optionKey];
//             if (optionValue !== true) {
//                 updatedCookie += "=" + optionValue;
//             }
//         }
//
//         document.cookie = updatedCookie;
//     }
//     function deleteCookie(name) {
//         setCookie(name, "", {
//             'max-age': -1
//         })
//     }
//
//     btnClearFilter.addEventListener('click', ()=>{
//         const url = window.location.href;
//         const cleanUrl = url.split('?')[0]; // Удаляем часть URL после '?'
//         window.history.replaceState(null, null, cleanUrl); // Обновляем URL без перезагрузки страницы
//         deleteCookie('sort_date')
//         deleteCookie('sort_name')
//         deleteCookie('sort_nds')
//         deleteCookie('sort_reg')
//         deleteCookie('sort_doc')
//         deleteCookie('sort_so')
//
//         window.location.reload()
//     })
// });
