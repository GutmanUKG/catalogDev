BX.ready(function(){
    init();
});

BX.addCustomEvent(
    'OnBasketChange',
    init
);

function init(){
    const addDraftBtn = document.getElementById("add-draft");
    const addDraftPopup = document.querySelector(".popup-draft-add-wrap");

    if(addDraftPopup){
        var closeFormBtn = addDraftPopup.querySelector(".popup-draft-add__close-btn");
        var saveDraftForm = addDraftPopup.querySelector("#add_draft");
        var successBlock = addDraftPopup.querySelector(".draft-add__success-block");
        var inputBlock = addDraftPopup.querySelector(".draft-add__form-add");
        var inputName = addDraftPopup.querySelector(".add-draft__input-name");


        appendPopup(addDraftPopup);

        addDraftPopup.addEventListener('click', function(e){
            if(e.target.closest('.popup-draft-add') === null){
                closeDraftPopup(addDraftPopup);
            }
        });
    }

    if(addDraftBtn){
        addDraftBtn.addEventListener("click", function(){
            showDraftPopup(addDraftPopup);
        });
    }

    if(closeFormBtn){
        closeFormBtn.addEventListener("click", function(){
            closeDraftPopup(addDraftPopup);
            closeDraftPopup(successBlock);
            showInput(inputBlock);
            inputName.value = '';
        });
    }

    if(saveDraftForm){
        saveDraftForm.addEventListener("submit", function(e){
            e.preventDefault();
            BX.showWait();
            let draftName = inputName.value;

            var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.draft.add', 'addDraft', {
                signedParameters: this.getAttribute('data-params'),
                mode: 'class',
                data: {name: draftName }
            });

            request.then(function (response) {
                if(response.data.error !== true){
                    let titleSuccessBlock = addDraftPopup.querySelector('.title__success-bold');
                    titleSuccessBlock.textContent = response.data.message;
                    inputBlock.style.display="none";
                    successBlock.style.display="block";

                    const deleteProd = document.querySelectorAll('a[data-entity="basket-item-delete"]');
                    console.log(deleteProd);
                    if(deleteProd){
                        for (let i=0; i<deleteProd.length; i++){
                            deleteProd[i].click();
                        }
                    }
                    BX.Sale.BasketComponent.sendRequest('refreshAjax', {
                        fullRecalculation: 'Y',
                        otherParams: {
                            param: 'N'
                        }
                    });
                }
                BX.closeWait();
            });

        });
    }


    function showDraftPopup(popup) {
        popup.style.display = "flex";
    }
    function closeDraftPopup(popup) {
        popup.style.display = "none";
    }
    function appendPopup(popup){
        document.body.append(popup);
    }
    function showInput(block){
        block.style.display = "block";
    }
};