'use strict'
// Сохраняем позицию прокрутки перед обновлением страницы
document.addEventListener('DOMContentLoaded', ()=>{

    const copyLink = document.querySelector('#copy_link');

    copyLink.addEventListener('click', (e)=>{
        e.preventDefault()
        navigator.clipboard.writeText(copyLink.href)
            .then(()=>{
                copyLink.textContent = 'готово';
            })
            .catch(err=>{
                console.log(err)
            })
    })
    let wrapperSale = document.querySelector('.wrapper_sale .col-9');
    try{
        let basketLine = document.querySelector('.basket_line')
        basketLine.style.maxWidth = wrapperSale.clientWidth + 'px';
    }catch (e) {

    }

   
    let contentInner = document.querySelector('.content-inner')
    const scrollPosition = sessionStorage.getItem("scrollPosition");
    if (scrollPosition !== null) {
        contentInner.scrollTo(0, parseInt(scrollPosition, 10));
        sessionStorage.removeItem("scrollPosition"); // Очищаем, если больше не нужно
    }


    function beforeunload(){
        const scrollPosition = contentInner.scrollTop ;
        sessionStorage.setItem("scrollPosition", scrollPosition);
        console.log(scrollPosition + ' scrollPosition')
        window.location.reload()
    }


    let showMoreButtons = document.querySelectorAll('.show_more');

    //Выпадашка со списком привязанных товаров
    showMoreButtons.forEach(i=>{
        i.addEventListener('click', ()=>{

            let parent = i.closest('.item_wrap')
            i.classList.toggle('active')
            parent.classList.toggle('active_more')
        })
    })
    const popupMess = document.querySelector('.popup_info')

    const catalog_sale = document.querySelector('.catalog_sale');

    //слушатель на весь каталог т.к каких либо кнопок может не быть
    catalog_sale.addEventListener('click', (e)=>{
        e.preventDefault()
        e.stopPropagation()
        let target = e.target;
        console.log(target)
        if(target.classList.contains('update_item')){
            let id = target.closest('.item').id;
            let item = target.closest('.item');
            let name = item.querySelector('.name_item')
            showFormUpdate(id, name.textContent)
        }
        if(target.classList.contains('add_basket')){
            addBasket(target)
        }
        if(target.classList.contains('copy_item')){
            let id = target.closest('.item').id;
            copyItem(id)
        }
        if(target.classList.contains('name_item')){
            let id = target.closest('.item').id;
            showItemInfo(id)
        }
        // if(target.classList.contains('name')){
        //     let id = target.closest('.item').id;
        //     showItemInfo(id)
        // }
        if(target.classList.contains('show_info')){
            let id = target.closest('.item').id;
            showItemPhoto(id)
        }

        if(target.classList.contains('photo') || target.closest('.photo')){
            let id = target.closest('.item').id;
            showItemInfo(id)
        }
        if(target.classList.contains('increment')){
            console.log('click')
            if(incrementItem(target)){

                let id = target.closest('.item').id;
                let num = target.parentNode.querySelector('input').value
                updateQuatity(id, num)
            }

        }
        if(target.classList.contains('dicrement')){
            if(decrement(target)){
                let id = target.closest('.item').id;
                let num = target.parentNode.querySelector('input').value
                if(num == 0){
                    let item = target.closest('.item'),
                        addBasket = item.querySelector('.add_basket'),
                        quantity = item.querySelector('.quantity');
                    addBasket.style.display = 'block'
                    quantity.style.display = 'none'

                }
                updateQuatity(id, num)
            }

        }
        if(target.classList.contains('remove_basket')){
            let id = target.closest('.item').id;
           // updateQuatity(id, num)
            removeBasketItem(id)
        }
    })
    //Копирование элемента
    function copyItem(id){
        let data = new FormData()
        data.append('ID', parseInt(id));
        data.append('ACTION', 'COPY_ITEM');
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then(res=> res.json())
            .then((response)=>{
               
                if(response.status == 'success'){
                    alert(response.message)
                    beforeunload()

                }else{
                    alert(response.message + ' Страница будет перезапущенна')
                    beforeunload()
                }
            })
            .catch((e)=>{
                console.error(e)
            })
    }


    //Добавление товара в корзину
    function addBasket(target){

        let item = target.closest('.item'),
            id = item.id,
            name = item.querySelector('.name .name_item'),
            price = item.querySelector('.rrc');
        let data = new FormData()
        data.append('ID', parseInt(id));
        data.append('NAME', name.textContent);
        data.append('ACTION', 'ADD_BASKET');
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then(res => res.json())
            .then((response)=>{
                if(response.status == "error"){
                    alert(response.message)
                }else{
                    beforeunload()
                }
            })
            .catch((e)=>{
                console.error(e)
            })
    }

    //Вызов формы редактирования
    function showFormUpdate(id, name){
        let data = new FormData();
        data.append('ID', id);
        data.append('NAME', name);
        data.append('ACTION', 'UPDATE');

        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
          method: "POST",
          body: data
      })
          .then(res=> res.json())
          .then((response)=>{
              if(document.body.querySelector('#form_upadate')){
                  let rem = document.querySelector('#form_upadate')
                    document.body.removeChild(rem)
                    createForm(response.message)

              }else {
                  createForm(response.message)
              }
          })
          .catch((e)=>{
              console.error(e)
          })
    }
    //Вывод формы редактирования
    function createForm(content, method = "update"){
        $('#overlay').show()
        //let form_upadate = document.querySelector('#form_upadate')
        let formWrapper = document.createElement('div')
        formWrapper.setAttribute('id', 'form_upadate')
        formWrapper.innerHTML = content;
        document.body.append(formWrapper)
        let form = formWrapper.querySelector('form')
        form.addEventListener('submit', (e)=>{
            e.preventDefault()
            e.stopPropagation()
            let data = new FormData(form)
           
            if(method == 'update'){
                data.append('ACTION', "SAVE_UPDATE")
            }else{
                data.append('ACTION', 'ADD_ELEMENT');
            }

            fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                method: "POST",
                body: data
            })
                .then((res)=> res.json())
                .then((response)=>{

                    if(response.status == "success"){
                        formWrapper.remove()
                        //form_upadate.remove();
                        alert(response.message)
                        beforeunload()
                    }
                })
                .catch((e)=>{
                    console.error(e)
                })
        })
    }

    //Удаление формы из DOM
    function removeForm(){
        let rem = document.querySelector('#form_upadate')
        document.body.removeChild(rem)
    }

    //Слушаетель на весь документ , для того что бы работать с динамическим DOM
    document.addEventListener('click', (e)=>{
        let target = e.target;
        if(target.id == "close_ajax_form"){
            $('.ajax_form_update').remove()
            $('#overlay').hide()
            try{
                let form_upadate = document.querySelector('#form_upadate')
                form_upadate.remove();
            }catch (e) {

            }
        }
        if(target.classList.contains('close_ajax_form')){
            $('#popup_item_info').remove()
            $('#overlay').hide()
            try{
                let form_upadate = document.querySelector('#form_upadate')
                form_upadate.remove();
            }catch (e) {

            }
        }
    })

    try {
        const btnAdd = document.querySelector('#add_item');

        //Вызов формы создания элемента
        btnAdd.addEventListener('click', ()=>{
            let data = new FormData()
            data.append('ACTION', "SHOW_ADD_FROM")
            fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                method: "POST",
                body: data
            })
                .then((res)=> res.json())
                .then((response)=>{
                    if(document.body.querySelector('#form_upadate')){
                        let rem = document.querySelector('#form_upadate')
                        document.body.removeChild(rem)
                        createForm(response.message, 'save')
                    }else {
                        createForm(response.message, 'save')
                    }
                })
                .catch((e)=>{
                    console.error(e)
                })
        })
    }catch (e) {
        console.log(e)
    }


   try {
       const deletItems = document.querySelectorAll('.delete_item');

       //Удаление элементов из каталога
       deletItems.forEach(i=>{
           i.addEventListener('click', ()=>{
               if(confirm('Удалить элемент ?')){
                   let parent = i.closest('.item')
                   let data = new FormData()
                   data.append('ACTION', "DELETE_ELEMENT")
                   data.append("ID", parent.id)
                   fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                       method: "POST",
                       body: data
                   })
                       .then((res)=> res.json())
                       .then((response)=>{
                           if(response.status == "success"){
                               alert(response.message)
                               beforeunload()
                           }
                       })
                       .catch((e)=>{
                           console.error(e)
                       })
               }

           })
       })
   }catch (e) {
       
   }

    function initializeFileDrop(){
        let image_list_destroy = document.querySelector('.image_list_destroy')
        let loader = image_list_destroy.parentNode.querySelector('.loader')
        image_list_destroy.addEventListener('click', (e)=>{
            let target = e.target;
            e.preventDefault()
            e.stopPropagation()
            if(target.classList.contains('remove_img')){
                let image_id = target.closest('.image_item').id;
                loader.style.display = "block"
                let item_id = target.closest('#form_upadate').querySelector('input[name="id_element"]').value;
                let data = new FormData()
                data.append('ACTION', "REMOVE_PHOTO")
                data.append('PHOTO_ID', image_id)
                data.append('ID', item_id)
                fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                    method: "POST",
                    body: data
                })
                    .then(res=>res.json())
                    .then((response)=>{
                        if(response.status == "success"){
                            image_list_destroy.innerHTML = response.response
                            loader.style.display = "none"
                        }
                    })
                    .catch((e)=>{
                        console.log(e)
                    })
            }
        })


        //
        const dropFileZone = document.querySelector(".upload-zone_dragover");
        const statusText = document.getElementById("uploadForm_Hint");
        const uploadInput = document.querySelector(".form-upload__input");
        const uploadUrl = "/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php";

        // Установка текста статуса
        const setStatus = (text) => {
            statusText.textContent = text;
        };

        // Обработка событий drag-and-drop
        ["dragover", "drop"].forEach((event) => {
            document.addEventListener(event, (e) => {
                loader.style.display = "block"
                e.preventDefault();
                e.stopPropagation()
            });
        });

        dropFileZone.addEventListener("dragenter", () => dropFileZone.classList.add("_active"));
        dropFileZone.addEventListener("dragleave", () => dropFileZone.classList.remove("_active"));
        dropFileZone.addEventListener("drop", (event) => {
            event.preventDefault()
            event.stopPropagation()
            dropFileZone.classList.remove("_active");
            const files = event.dataTransfer?.files;
            if (files?.length) {
                handleFiles(files);
            } else {
                setStatus("Можно загружать только изображения");
            }
        });

        // Обработка загрузки через input
        uploadInput.addEventListener("change", (event) => {
            loader.style.display = "block"
            event.preventDefault()
            event.stopPropagation()
            const files = event.target.files;
            if (files?.length) {
                handleFiles(files);
            } else {
                setStatus("Можно загружать только изображения");
            }
        });

        // Функция обработки файлов
        const handleFiles = (files) => {
            Array.from(files).forEach((file) => {
                if (file.type.startsWith("image/")) {
                    uploadFile(file);
                } else {
                    setStatus("Можно загружать только изображения");
                }
            });
        };
        let itemID = dropFileZone.closest('#ajax_form_update').querySelector('input[name="id_element"]').value;
        // Отправка файла на сервер
        const uploadFile = (file) => {

            const formData = new FormData();
            formData.append("file", file);
            formData.append("ID", itemID);
            formData.append("ACTION", "ADD_PHOTO");
            fetch(uploadUrl, {
                method: "POST",
                body: formData,
            })
                .then(res=> res.json())
                .then((response) => {
                   if(response.status == "success"){
                       if(response.status == "success"){
                           loader.style.display = "none"
                           image_list_destroy.innerHTML = response.response

                       }
                   }
                })
                .catch((error) => {
                    console.error(error);
                    setStatus("Ошибка загрузки");
                });
        };
        //


    }




    // Настраиваем MutationObserver
    const observer = new MutationObserver((mutationsList, observer) => {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' && document.querySelector('#ajax_form_update')) {
                initializeFileDrop();
                observer.disconnect(); // Отключаем, если форма загружена
                break;
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });

    //Вывод фото
    function showItemPhoto(id){
        let data = new FormData()
        data.append('ACTION', "SHOW_ITEM_PHOTO")
        data.append('ID', id)
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then((res)=> res.json())
            .then((response)=>{
                console.log(response)
                if(response.status == "success"){
                    let popup = document.querySelector("#popup_item_info")
                    if(!popup){
                        let fragment = document.createDocumentFragment()
                        let element = document.createElement('div')
                        element.id = "popup_item_info"
                        element.innerHTML = response.message
                        fragment.appendChild(element)
                        document.body.appendChild(element)
                        $('#overlay').show()

                        $('.popup_imgs').owlCarousel({
                            loop: false,
                            items: 1,
                            nav:false,
                            dots:false,
                            autoplay: true,

                        })
                    }
                }
            })
            .catch((e)=>{
                console.error(e)
            })
    }

    //Вывод информации о товаре
    function showItemInfo(id){
        let data = new FormData()
        data.append('ACTION', "SHOW_ITEM")
        data.append('ID', id)
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then((res)=> res.json())
            .then((response)=>{
                console.log(response)
                if(response.status == "success"){
                    let popup = document.querySelector("#popup_item_info")
                    if(!popup){
                        let fragment = document.createDocumentFragment()
                        let element = document.createElement('div')
                        element.id = "popup_item_info"
                        element.innerHTML = response.message
                        fragment.appendChild(element)
                        document.body.appendChild(element)
                        $('#overlay').show()

                        $('.popup_imgs').owlCarousel({
                            loop: false,
                            items: 1,
                            nav:false,
                            dots:true,
                            autoplay: true,

                        })
                    }
                }
            })
            .catch((e)=>{
                console.error(e)
            })
    }


    //Увеличение количества товара
    function incrementItem(target) {
        let input = target.parentNode.querySelector('input')
        if(parseInt(input.value) > 0 && parseInt(input.value) < parseInt(input.dataset.quantuti)){
            input.value++

            return true
        }else{
            
            return false
        }
    }

    //Уменьшение количества товара
    function decrement(target) {
        let input = target.parentNode.querySelector('input')
        if(input.value > 0){
            input.value--
            return true
        }else{
            return false
        }
    }

    //Обновление количества товара в корзине
    function updateQuatity(id, num) {
        let data = new FormData()
        data.append('ACTION', "UPDATE_QUANTITY")
        data.append('NUM', num)
        data.append('ID', id)
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then((res)=>res.json())
            .then((response) =>{
               if(response.status == "error"){
                   alert(response.message)
               }
               if(response.status == "success"){
                  // $('.popup_info').show()
                   $('.popup_info').text(response.message)
                   setTimeout(()=>{
                     //  $('.popup_info').hide()
                       beforeunload()
                   }, 100)
               }
            })
            .catch((e)=>{
                console.log(e)
            })
    }
    //Удаление товара их корзины
    function removeBasketItem(id){
        let data = new FormData()
        data.append('ACTION', "REMOVE_BASKET")
        data.append('ID', id)
        fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then(res=> res.json())
            .then((response)=>{

                if(response.status == "success"){
                    $('.popup_info').show()
                    $('.popup_info').text(response.message)
                    setTimeout(()=>{
                         $('.popup_info').hide()
                        beforeunload()
                    }, 1000)
                }else{
                    alert(response.message)
                }
            })
            .catch((e)=>{
                console.log(e)
            })
    }


   try {
       const btn_import_catalog = document.querySelector('#import_catalog');
       btn_import_catalog.addEventListener('click', ()=>{
           let data = new FormData()
           data.append('ACTION', "FORM_IMPORT")
           fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
               method: "POST",
               body: data
           })
               .then(res=> res.json())
               .then((result)=>{
                   if(result.status == "success"){
                       if(!document.querySelector('.import_popup_form')){
                           let popupImport = document.createElement('div')
                           popupImport.innerHTML = result.message
                           document.body.appendChild(popupImport)
                           $('#overlay').show()
                           eventsFormImport()
                       }
                   }
               })
               .catch((e)=>{
                   console.log(e)
               })
       })
   }catch (e) {
       
   }

    function eventsFormImport(){
        let import_popup_form = document.querySelector('.import_popup_form'),
            closePopupForm = import_popup_form.querySelector('.close_btn'),
            importForm = import_popup_form.querySelector('form');

        console.log(importForm)
        importForm.addEventListener('submit', (e)=>{
            e.preventDefault()
            let data = new FormData(importForm);
            data.append('ACTION', "IMPORT_FILE")
            fetch('/local/templates/.default/components/bitrix/news/catalog_sale/ajax.php', {
                method: "POST",
                body: data
            })
                .then(res=>res.json())
                .then((result)=>{
                    $('.popup_info').innerHTML = ''
                    if(result.status == "success"){
                        let errorsList = document.createElement('ul')
                        errorsList.classList.add('red_list')
                        result.errors.forEach(i=>{
                            let li = document.createElement('li')
                            li.textContent = i
                            errorsList.appendChild(li)
                        })

                        $('.popup_info').append(errorsList)
                        let info = document.createElement('div')
                        info.textContent = result.message
                        $('.popup_info').append(info)
                        $('.popup_info').show()
                        $('.overlay').on('click', ()=>{
                            $('.popup_info').innerHTML = ''
                            $('.popup_info').hide()
                        })

                    }
                })
                .catch((e)=>{
                    console.log(e)
                })

        })

        closePopupForm.addEventListener('click', ()=>{
            import_popup_form.parentNode.remove()
            $('#overlay').hide()
        })
    }

    $('.kat').on('click', ()=>{
        $('#overlay').show()
        $('.info_cat').show()
    })
    $('.info_cat .close_cat').on('click', ()=>{
        $('#overlay').hide()

        $('.info_cat').hide()
    })




    $('#overlay').on('click', ()=>{
        $('#overlay').hide()
        $('#form_upadate').hide()
        $('#popup_item_info').remove()
        $('.info_cat').hide()
        $('.col-3.order-1').removeClass('mobile')
        let import_popup_form = document.querySelector('.import_popup_form');
        import_popup_form.remove()
    })
    $('.btn_show_filter').on('click', ()=>{
        $('#overlay').show()
        $('.col-3.order-1').addClass('mobile')

    })




});


