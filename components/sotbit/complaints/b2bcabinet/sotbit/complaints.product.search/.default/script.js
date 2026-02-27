function changeProd(prodId) {
    if (Array.isArray(prodId) == true) {
        BX.SidePanel.Instance.postMessageAll(window, "addPositions", {productIds: prodId, allProducts: productsObject});
    } else {
        if (productsObject[prodId]) {
            BX.SidePanel.Instance.postMessageAll(window, "addPosition", {product: productsObject[prodId]});
        }
    }
}
