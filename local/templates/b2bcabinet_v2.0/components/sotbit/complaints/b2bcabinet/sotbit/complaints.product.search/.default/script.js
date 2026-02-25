function changeProd(prodId) {
    if (Array.isArray(prodId) == true) {
        prodId.forEach(element =>{
                if (productsObject[element]) {
                    BX.SidePanel.Instance.postMessageAll(window, "addPosition", {product: productsObject[element]});
                }
            }
        )
    } else {
        if (productsObject[prodId]) {
            BX.SidePanel.Instance.postMessageAll(window, "addPosition", {product: productsObject[prodId]});
        }
    }
}
