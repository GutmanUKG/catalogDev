document.addEventListener('DOMContentLoaded', ()=>{
    const sectionItems = document.querySelectorAll('.section-item');

    sectionItems.forEach(i=>{
        let btn = i.querySelector('.show_more')
        try{
            btn.addEventListener('click', (e)=>{
                e.preventDefault()
                i.classList.toggle('show_more')
            })
        }catch (e){

        }
    })
})