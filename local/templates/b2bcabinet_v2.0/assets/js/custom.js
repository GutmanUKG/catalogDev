$(document).ready(function(){
    $('.title-search-result').appendTo('.catalog__search');
    $('.sotbit-cabinet-gadget-orders .text-muted:contains(Принят, ожидается оплата)').remove();

        $(".nav-link.active").each(function(){
            menuItemPos = $(this).offset().top - 320;
        });
        sidebarHalf = $('.sidebar-section').outerHeight() / 2;
        headerHeight = $('.sidebar-logo').outerHeight();
        $('.sidebar-content').scrollTop(menuItemPos - sidebarHalf - headerHeight);


    $('.sidebar-main-resized').mouseover(function(){
        if(!$(this).hasClass('scrolled')){
            $(".nav-link.active").each(function(){
                menuItemPos = $(this).offset().top - 320;
            });
            sidebarHalf = $('.sidebar-section').outerHeight() / 2;
            headerHeight = $('.sidebar-logo').outerHeight();
            $('.sidebar-main-resized .sidebar-content').scrollTop(menuItemPos - sidebarHalf - headerHeight);
            $('.sidebar-main-unfold').addClass('scrolled');
        }
    });

    // С плавной анимацией
    $('#search_trigger').on('click', () => {
        $('#navbar_search #title-search').slideToggle(200);
    });
    let formCheckLabel = document.querySelectorAll('.form-check-label');



    $('#search_filter').on('input', () => {
        const searchVal = $('#search_filter').val().toLowerCase();

        // Сначала показываем все .form-check (чтобы сбросить фильтр)
        formCheckLabel.forEach(label => {
            const text = label.textContent.toLowerCase();
            const formCheck = label.closest('.form-check');

            if (text.includes(searchVal)) {
                formCheck.style.display = '';
            } else {
                formCheck.style.display = 'none';
            }
        });
        let bx_filter_parameters_box_collapse_btn = document.querySelectorAll('.bx_filter_parameters_box_collapse_btn')
        let collapse  = document.querySelectorAll('.bx_filter_parameters_box_collapse')
        collapse.forEach(i=>{
            i.classList.add('show')
        })
        bx_filter_parameters_box_collapse_btn.forEach(i=>{
            i.classList.remove('collapsed')
        })
        // Потом проверяем каждую .form-group
        document.querySelectorAll('.form-group').forEach(group => {
            const visibleChecks = group.querySelectorAll('.form-check:not([style*="display: none"])');

            if (visibleChecks.length === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = '';
            }
        });
        if(searchVal.length < 1){
            collapse.forEach(i=>{
                i.classList.remove('show')
            })
            bx_filter_parameters_box_collapse_btn.forEach(i=>{
                i.classList.add('collapsed')
            })
        }
    });


    let basketItemBtn = document.querySelectorAll('.basket-item--add');

    basketItemBtn.forEach(i=>{
        i.addEventListener('click', (e)=>{
            let id = i.dataset.id;
            let wrapper_input_group = document.getElementById(id)
            let increment = document.getElementById(id + '-increment')
            increment.click()
            wrapper_input_group.style.display = ''
            i.style.display = 'none';

        })
    })
    $('body').on('click', (e) => {
        const $search = $('#navbar_search');
        const $trigger = $('#search_trigger');

        // если клик вне поискового блока и вне кнопки триггера
        if (
            $search.hasClass('active') &&
            !$search.is(e.target) &&
            $search.has(e.target).length === 0 &&
            !$trigger.is(e.target) &&
            $trigger.has(e.target).length === 0
        ) {
            $search.removeClass('active');
        }
    });

    $('#search_trigger').on('click', () => {
        $('#navbar_search').addClass('active');
    });


    if(document.body.clientWidth < 600){
     let stickyPanel = document.querySelector('.sticky-panel'),
         catalogTop = document.querySelector('.page-header-content .d-flex.align-items-center.w-100');
        console.log(catalogTop)
        catalogTop.appendChild(stickyPanel)
    }

});