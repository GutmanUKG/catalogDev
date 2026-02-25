document.addEventListener('DOMContentLoaded', ()=>{
    const basketWrapper = document.querySelector('.basket_items');
    const btnBasket = document.querySelector('#order_basket');
    basketWrapper.addEventListener('click', (e)=>{
        let target = e.target;

        if(target.classList.contains('increment')){
           if(incrementItem(target)){
               let id = target.closest('.item').id,
                   num = target.parentNode.querySelector('input').value;
               updateQuatity(id, num)
           }

        }
        if(target.classList.contains('dicrement')){
           if(decrement(target)){
               let id = target.closest('.item').id,
                   num = target.parentNode.querySelector('input').value;
               updateQuatity(id, num)
           }

        }
        if(target.classList.contains('update_quantity')){
            let id = target.dataset.id,
                num = target.parentNode.querySelector('input').value;
            updateQuatity(id, num)
        }
        if (target.classList.contains('remove_item')){
            let id = target.dataset.id;
            if(confirm('Вы действительно хотите удалить товар ?')){
                removeItem(id)
            }

        }
    })



    function incrementItem(target) {
        let input = target.parentNode.querySelector('input')
        if(input.value > 0 && input.value < input.dataset.quantuti){
            input.value++
            return true
        }else{
            return false
        }
    }


    function decrement(target) {
        let input = target.parentNode.querySelector('input')
        if(input.value > 1){
            input.value--
            return true
        }else {
            return false
        }
    }

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
                       // $('.popup_info').hide()
                        window.location.reload()
                    }, 100)
                }
            })
            .catch((e)=>{
                console.log(e)
            })
    }


    function removeItem(id) {
        let data = new FormData()
        data.append('ACTION', "REMOVE_ITEM_BASKET")
        data.append('ID', id)
        fetch('/local/classes/onelab/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then(res=> res.json())
            .then((response)=>{
                if(response.status == "error"){
                    alert(response.message)
                }
                if(response.status == "success"){
                    $('.popup_info').show()
                    $('.popup_info').text(response.message)
                    setTimeout(()=>{
                        $('.popup_info').hide()
                        window.location.reload()
                    }, 1500)
                }
            })
    }


    btnBasket.addEventListener('click', (e)=>{
        let data = new FormData()
        data.append('ACTION', "ORDER_BASKET")
        fetch('/local/classes/onelab/catalog_sale/ajax.php', {
            method: "POST",
            body: data
        })
            .then(res=> res.json())
            .then((response)=>{
                console.log(response)
                if(response.status == "success"){
                     $('.popup_info').show()
                    $('.popup_info').text(response.message)
                    setTimeout(()=>{
                         $('.popup_info').hide()
                        window.location.reload()
                    }, 1500)
                }else{
                    $('.popup_info').show()
                    $('.popup_info').text(response.message)
                    setTimeout(()=>{
                        $('.popup_info').hide()
                        window.location.reload()
                    }, 1500)
                }
            })
            .catch((e)=>{
                console.log(e)
            })
    })
})