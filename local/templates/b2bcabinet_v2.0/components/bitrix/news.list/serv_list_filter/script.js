document.addEventListener('DOMContentLoaded', ()=>{
    const btns = document.querySelectorAll('.show_more'),
        servItem = document.querySelectorAll('.serv_item');
    btns.forEach((item,id)=>{
        item.addEventListener('click', ()=>{
            servItem[id].classList.toggle('active')
            item.classList.toggle('active')
            let items = servItem[id].querySelectorAll('.item');
            items.forEach(item=>{
                item.classList.toggle('show')
            })
        })
    })

})