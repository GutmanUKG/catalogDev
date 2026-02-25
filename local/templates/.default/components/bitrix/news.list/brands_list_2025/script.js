document.addEventListener('DOMContentLoaded', ()=>{

    let popupInfo = document.querySelector('.popup_info'),
        closePopup = popupInfo.querySelector('.close_btn_popup'),
        overlay = document.querySelector('.overlay'),
        prevText = popupInfo.querySelector('.left'),
        logo = popupInfo.querySelector('.brand_logo'),
        newsList = popupInfo.querySelector('.news'),
        categoriesList = document.createElement('div'),
        categoryItems = popupInfo.querySelector('.category_items');
        isPreloader = true;
    let preload = document.createElement('div')

    const brandItems = document.querySelectorAll('.brand_item');

    brandItems.forEach(i=>{
        i.addEventListener('click', ()=>{
            let IBLOCK_ID = i.dataset.iblock_id,
                section_news = i.dataset.section_news,
                id = i.dataset.id;
            let data = new FormData()
            data.append('IBLOCK_ID', parseInt(IBLOCK_ID));
            data.append('SECTION_NEWS_ID', parseInt(section_news));
            data.append('ITEM_ID', parseInt(id));
            fetch('/local/templates/.default/components/bitrix/news.list/brands_list_2025/ajax.php', {
                method: "POST",
                body: data
            })
                .then(res=>res.json())
                .then((response)=>{
                   
                    createPopup(i, response)
                })
                .catch((e)=>{
                    console.error(e)
                })

        })
    })

    function createPopup(item, response){
        let descr = item.dataset.decr;
        prevText.querySelector('.title').textContent = item.querySelector('h5').textContent
        prevText.querySelector('.decr').innerHTML = descr;
        logo.src = item.querySelector('img').src;
        newsList.innerHTML = '';
        categoriesList.innerHTML = '';
        categoryItems.innerHTML = '';
        response.news.news.forEach(news=>{
            let newsItem = `
                <div class="news_item" id="${news.ID}">
                    <a href="${news.DETAIL_PAGE_URL}">
                        <span class="date">
                            ${news.DISPLAY_ACTIVE_FROM}
                        </span>
                        <h5>${news.NAME}</h5>
                        <p>${news.PREVIEW_TEXT}</p>
                    </a>
                </div>
            `;
            newsList.innerHTML += newsItem;

        })
        let count = 0
        response.categories.categories.forEach(i=>{
            if(count < 2){
                let item = generateHTML(i);
                categoryItems.innerHTML += item;
            }

        })

        popupInfo.classList.add('active');
        overlay.classList.add('active');

    }
    function generateHTML(category) {
        let img = '';
        if(category.img != null){
            img = `<img src="${category.img}" alt="">`
        }else{

        }
        let isNotChild = category.CHILD && category.CHILD.length > 0 ? 'class="item"' : 'class="item no_child"';
        let html = `<div ${isNotChild}>
                    <div class="item_name">${img} ${category.NAME}</div>`;

        if (category.CHILD && category.CHILD.length > 0) {
            html += `<div class="item_child">`;
            category.CHILD.forEach(child => {
                html += generateHTML(child);
            });
            html += `</div>`;
        }

        html += `</div>`;
        return html;
    }
    function clearClass(items, classActive){
        for(let i = 0; i < items.length; i++){
            items[i].classList.remove(classActive)
        }
    }
    popupInfo.addEventListener('click', (e) => {
        let target = e.target;

        if (target.classList.contains('item_name') || target.classList.contains('item')) {
            let item = target.closest('.item');

            // Найдём все .item, но исключим текущий элемент и его родителей
            document.querySelectorAll('.category_items .item').forEach(el => {
                if (el !== item && !el.contains(item)) {
                    el.classList.remove('active');
                }
            });

            // Тоглим active только у кликнутого элемента
            item.classList.toggle('active');
        }
    });




    const letterList = document.querySelector('.letter_list'),
        letterItem = letterList.querySelectorAll('.item'),
        rowBrand = document.querySelectorAll('.row_brand'),
        clearFilterBtn = document.querySelector('.filter_by_letter .clear_filter');
    letterItem.forEach(i=>{
        if(!i.classList.contains('disabled')){
            i.addEventListener('click', ()=>{
                clearClass(letterItem, 'active')
                i.classList.add('active')
                filterRow(i.textContent.trim())
            })
        }

    })

    function filterRow(letter){
        rowBrand.forEach(i=>{
            if(i.dataset.letter != letter){
                i.style.display = 'none'
            }else{
                i.style.display = 'block'
            }
        })
    }
    clearFilterBtn.addEventListener('click', ()=>{
        clearFilter()
    })
    function clearFilter(){
        clearClass(letterItem, 'active')
        rowBrand.forEach(i=>{
            i.style.display = 'block'
        })

    }


    //
    closePopup.addEventListener('click', ()=>{
        popupInfo.classList.remove('active')
        overlay.classList.remove('active')
        document.body.style.overflow = ''
    })
    overlay.addEventListener('click', ()=>{
        popupInfo.classList.remove('active')
        overlay.classList.remove('active')
        document.body.style.overflow = ''
    })

})

