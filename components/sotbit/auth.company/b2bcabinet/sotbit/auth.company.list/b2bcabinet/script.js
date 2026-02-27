$(document).ready(function() {
    var actionButton = document.querySelectorAll('.main-grid-row-action-button');
    for (let i=0; i<actionButton.length; i++){
        let dataAction = actionButton[i].getAttribute("data-actions");
        if(dataAction.includes("deactivate")){
            actionButton[i].style.pointerEvents = "none";
        }
    }
});

