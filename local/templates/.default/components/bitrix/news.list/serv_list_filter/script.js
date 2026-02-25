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
    //let allCity = document.querySelector('input[name="arrFilter_858_55916349"]')
   // allCity.checked = true;
   //  let parent = allCity.closest('.checkbox')
   //  if(parent.children.length < 2){
   //      parent.style.display = 'none'
   //      parent.parentNode.style.display = 'none'
   //  }

})