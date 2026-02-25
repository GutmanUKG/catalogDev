document.addEventListener('DOMContentLoaded', function() {
    const brandItems = document.querySelectorAll('.brand_item');
    let newsList = '',
        sectionNames = '';
    let popupInfo = document.querySelector('.popup_info'),
        closePopup = popupInfo.querySelector('.close_btn_popup'),
        overlay = document.querySelector('.overlay'),
        titleItem = '',
        imgItem = '',
        descrItem = '';
    let preloader = true;
    let preload = document.createElement('div')
    let popupRight = popupInfo.querySelector('.right')
    let newsPopup = popupRight.querySelector('.news'),
        section_wrapper = document.createElement('div'),
        sectionNamesPopup = popupInfo.querySelector('.category_items');
    preload.classList.add('preloader')
    section_wrapper.classList.add('section_wrapper')
    if (!popupInfo) {
        console.error('popup_info element not found');
        return;
    }

    brandItems.forEach(i => {
        i.addEventListener('click', () => {

            let newsId = i.dataset.section_news,
                iblockId = i.dataset.iblock_id,
                brandID = i.dataset.id;
            titleItem = i.querySelector('h5').textContent;
            imgItem = i.querySelector('img').src;
            descrItem = i.dataset.decr;
            preloader = true
            popupRight = popupInfo.querySelector('.right')
            newsPopup.innerHTML = ''
            if(preloader){
               popupRight.appendChild(preload)
            }


                newsList = '';
                sectionNames = '';
                getNewsBrand(iblockId,newsId,brandID , i);
                popupInfo.classList.add('active')
                document.body.style.overflow = 'hidden'
                overlay.classList.add('active')

        });
    });

    function getNewsBrand(iblock_id,newsId, brandID, item){
        fetch(`/get_ajax_news.php?iblock_id=${iblock_id}&section_id=${newsId}&brand_id=${brandID}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                preloader = false
                GenerateItemPopup(titleItem, descrItem, imgItem, data.news, data.sections);
                checkPositionTrigger()

            })
            .catch(error => console.error('Error fetching data:', error));
    }



    function GenerateItemPopup(title, descr, imgSrc, data, sections) {
        if (preloader == false) {
            popupRight.removeChild(preload);
        }

        let popupLeft = popupInfo.querySelector('.left');
        popupLeft.querySelector('.title').textContent = title;
        popupLeft.querySelector('.decr').textContent = descr;

        let brandLogo = popupRight.querySelector('.brand_logo');
        let news = popupRight.querySelector('.news');

        brandLogo.src = imgSrc;
        if (data.length > 0) {
            popupRight.style.display = 'flex';
            data.forEach((i) => {
                let newsItem = `
                <div class="news_item">
                    <a href="${i.DETAIL_PAGE_URL}">
                        <span class="date">
                            ${i.DISPLAY_ACTIVE_FROM}
                        </span>
                        <h5>${i.NAME}</h5>
                        <p>${i.PREVIEW_TEXT}</p>
                    </a>
                </div>
            `;
                newsList += newsItem;

                newsPopup.innerHTML = newsList;
            });
        } else {
            popupRight.style.display = 'none';
        }

        // Clear previous sections content
        section_wrapper.innerHTML = '';

        // Process each section and create the HTML structure
        sections.forEach((section) => {
            let sectionHTML = createNestedStructure(section);
            section_wrapper.appendChild(sectionHTML);

        });

        sectionNamesPopup.appendChild(section_wrapper);

        // Add event listeners for the accordion functionality
        const itemTriggers = document.querySelectorAll('.item_trigger');
        itemTriggers.forEach(trigger => {
            trigger.addEventListener('click', function () {
                trigger.classList.toggle('active')
                const content = trigger.nextElementSibling;
                if (content && content.classList.contains('item_content')) {
                    content.style.display = content.style.display === 'block' ? 'none' : 'block';
                }
            });
        });

    }

// Function to create nested HTML structure based on the section array
    function createNestedStructure(section) {

        if (section.length === 0) return null;

        let container = document.createElement('div');
        container.classList.add('section_item');

        let head = document.createElement('div');
        head.classList.add('item_head');

        let trigger = document.createElement('span');
        trigger.classList.add('item_trigger');



        if(section[0].PICTURE != null){
            trigger.innerHTML = `
                <img src="${section[0].PICTURE}" alt="${section[0].NAME}"> ${section[0].NAME}
            `

        }else{
            trigger.textContent = section[0].NAME;

        }
        head.appendChild(trigger);
        container.appendChild(head);

        if (section.length > 1) {
            let content = document.createElement('div');
            content.classList.add('item_content');
            content.style.display = 'none'; // Initially hidden

            let nestedContent = createNestedStructure(section.slice(1));
            content.appendChild(nestedContent);

            head.appendChild(content);
        }

        return container;

    }

    function checkPositionTrigger(){
        let section_items = document.querySelectorAll('.section_item')
        section_items.forEach(i=>{
            let triggerBtn = i.querySelectorAll('.item_trigger')
            let len = triggerBtn.length
            triggerBtn.forEach((item, idx)=>{
                    if(len < 2){
                        item.classList.add('last_trigger')
                    }
            })
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


    //filter
    function clearClass(items, classActive){
        for(let i = 0; i < items.length; i++){
            items[i].classList.remove(classActive)
        }
    }
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
    // let currentUrl = new URL(window.location.href);
    // let formFilter = document.querySelector('form[name="arrFilter_form"]'),
    //     radioBtns = formFilter.querySelectorAll('.radio');
    // radioBtns.forEach(i=>{
    //     i.addEventListener('click', ()=>{
    //         let inputID = i.querySelector('input').id
    //         let paramGet = `?${inputID}&set_filter=Показать`
    //         //formFilter.submit()
    //     })
    // })




});
