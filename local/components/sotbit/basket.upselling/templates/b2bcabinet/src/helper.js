export const Helper = {
    calcQuantity: (current, max, delta) => {
        const result = Number(current) + Number(delta);
        if (isNaN(result)) return 0;
        if (result >= max) return max;
        if (result <= 0) return 0;
        return Math.round(result * 1000) / 1000;
    },

    loadeAfterSroll: ({currentScroll, maxScroll, currentPage, maxPage}, func) => {
        if ((currentScroll + 5) < maxScroll) {
            return;
        }
        if (currentPage === maxPage) {
            return;
        }
        func(currentPage ? currentPage + 1 : 1);
    },
}