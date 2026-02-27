$(document).on("click touchstart", 'button.add_to_cart', this, addToBasket);
$(document).ready(function () {
    $(document).on("click touchstart", "#blank-export-in-excel", this, excelOut);
});

class CatalogSection {

    constructor(wrapperSelector) {
        this.selector = wrapperSelector;
        this.scrollEars = {

            showAll: function () {
                let ears = document.querySelectorAll(".index_blank .table-responsive .scroll-ears");

                for (let i = 0; i < ears.length; i++) {
                    ears[i].style.display = "flex"
                }
            },

            hideAll: function () {
                let ears = document.querySelectorAll(".index_blank .table-responsive .scroll-ears");

                for (let i = 0; i < ears.length; i++) {
                    ears[i].style.display = "none"
                }
            },

            hideLeft: function () {
                const leftEar = document.querySelector('.main-grid-ear-left');
                leftEar.style.opacity = "0";
                leftEar.style.visibility = "hidden";
            },

            hideRight: function () {
                const rightEar = document.querySelector('.main-grid-ear-right');
                rightEar.style.opacity = "0";
                rightEar.style.visibility = "hidden";
            },

            showLeft: function () {
                const leftEar = document.querySelector('.main-grid-ear-left');
                leftEar.style.opacity = "1";
                leftEar.style.visibility = "visible";
            },

            showRight: function () {
                const rightEar = document.querySelector('.main-grid-ear-right');
                rightEar.style.opacity = "1";
                rightEar.style.visibility = "visible";
            },
        };
    }

    relocateTableHeader() {
        const wrapper = document.querySelector(this.selector);
        const tableHeader = wrapper.querySelector(".index_blank-thead");
        const tableHeaderTitles = tableHeader.querySelectorAll("th");
        const tableHeaderFixed = wrapper.querySelector(".index_blank-thead_fixed");

        for (let i = 0; i < tableHeaderTitles.length; i++) {
            let fixedHeaderChild = document.createElement("div");

            fixedHeaderChild.innerText = tableHeaderTitles[i].innerText;
            fixedHeaderChild.style.display = "inline-block";
            tableHeaderFixed.appendChild(fixedHeaderChild);
        }

        let displayTableHeader = {
            hide: function () {
                tableHeader.style.opacity = "0";
                tableHeader.style.visibility = "hidden";
            },
            show: function () {
                tableHeader.style.opacity = "1";
                tableHeader.style.visibility = "visible";
            }
        };

        displayTableHeader.hide();
        resizeTableFixed();

        window.addEventListener("resize", resizeTableFixed);

        function resizeTableFixed() {
            displayTableHeader.show();

            let tableHeaderFixedItems = tableHeaderFixed.querySelectorAll("div");

            for (let i = 0; i < tableHeaderTitles.length; i++) {
                tableHeaderFixedItems[i].style.width = tableHeaderTitles[i].offsetWidth + "px";
                tableHeaderFixedItems[i].style.height = tableHeaderTitles[i].offsetHeight + "px";
            }

            displayTableHeader.hide();
        }
    }

    setEarsTopPosition() {
        const wrapper = document.querySelector(this.selector);
        let ears = wrapper.querySelectorAll(".scroll-ears"),
            clientHeight = document.documentElement.clientHeight,
            anchorHeader = wrapper.querySelector('.anchor_header'),
            tableHeight = wrapper.querySelector(".table-responsive").clientHeight,
            anchorTop = 0;

        if (anchorHeader) {
            anchorTop = anchorHeader.getBoundingClientRect().top;
        }

        let earsTopPos = anchorTop < 0
            ? clientHeight / 2 - anchorTop
            : (clientHeight - anchorTop) / 2;

        earsTopPos = -anchorTop + clientHeight > tableHeight
            ? (tableHeight + anchorTop) / 2 - anchorTop
            : earsTopPos;

        let earsTopPosPercents = 100 * earsTopPos / tableHeight;

        if (earsTopPosPercents < 0) {
            earsTopPosPercents = 0;
        } else if (earsTopPosPercents > 100) {
            earsTopPosPercents = 100;
        }

        for (let i = 0; i < ears.length; i++) {
            ears[i].style.top = earsTopPosPercents + "%";
        }
    }

    showEars() {
        const wrapper = document.querySelector(this.selector);
        let datatableScroll = wrapper.querySelector(".datatable-scroll");
        let tableScrollWidth = datatableScroll.scrollWidth,
            tableScrollWidthClientWidth = datatableScroll.clientWidth,
            tableScrollLeft = datatableScroll.scrollLeft;

        if (tableScrollWidth - tableScrollWidthClientWidth > 2) {
            this.scrollEars.showAll();

            if (tableScrollLeft === 0) {
                this.scrollEars.hideLeft();
            } else {
                this.scrollEars.showLeft();
            }

            if (tableScrollWidthClientWidth + tableScrollLeft === tableScrollWidth) {
                this.scrollEars.hideRight();
            } else {
                this.scrollEars.showRight();
            }

        } else {
            this.scrollEars.hideAll();
        }
    }

    setEventsIndexBlankTable() {
        let idTimer;
        const wrapper = document.querySelector(this.selector);
        const leftEar = wrapper.querySelector('.main-grid-ear-left');
        const rightEar = wrapper.querySelector('.main-grid-ear-right');
        const table = wrapper.querySelector(".index_blank-table");
        const _this = this;

        if (table && wrapper.querySelector(".index_blank-thead_fixed-wrapper")) {
            wrapper.querySelector(".index_blank-thead_fixed-wrapper").style.width = table.clientWidth + "px";
        }

        leftEar.addEventListener("mouseover", function () {
            idTimer = setInterval(function () {
                _this.scrollTable("left");
            }, 0.5);
        });

        leftEar.addEventListener("mouseout", function () {
            clearTimeout(idTimer);
        });

        rightEar.addEventListener("mouseover", function () {
            idTimer = setInterval(function () {
                _this.scrollTable("right");
            }, 0.5);
        });

        rightEar.addEventListener("mouseout", function () {
            clearTimeout(idTimer);
        });

        this.showEars();
    }

    initPerfectScrols() {
        var prfscr = new PerfectScrollbar('.datatable-scroll', {
            wheelSpeed: 0.5,
            wheelPropagation: true,
            minScrollbarLength: 20,
            suppressScrollY: true
        });
    }

    setAddCartPosition() {
        let scrollTop = $(document).scrollTop(),
            anchorBottom = 0,
            pip = 0;
        const wrapper = document.querySelector(this.selector);
        const wrapperTheadFixed = wrapper.querySelector(".index_blank-thead_fixed-wrapper");

        if (wrapper.querySelector(".anchor_header") && wrapper.querySelector(".anchor")) {
            pip = $(this.selector + ' .anchor_header').offset().top;
            anchorBottom = $(this.selector + ' .anchor').offset().top;

        }

        if (pip > scrollTop || anchorBottom < (scrollTop + 80)) {

            wrapperTheadFixed.classList.remove('thead_fixed-wrapper-fixed');
            wrapperTheadFixed.style.width = "auto";
            wrapperTheadFixed.style.height = "auto";
            wrapperTheadFixed.style.overflow = "";
            wrapper.querySelector(".index_blank-thead_fixed").style.left = "0";

        } else {

            wrapperTheadFixed.classList.add('thead_fixed-wrapper-fixed');

            const table = document.querySelector(".index_blank-table");
            const tableHeaderFixed = wrapper.querySelector(".index_blank-thead_fixed");
            const datatableScroll = wrapper.querySelector(".datatable-scroll");
            const tableResponsive = wrapper.querySelector(".table-responsive");

            wrapperTheadFixed.style.width = table.clientWidth + "px";
            wrapperTheadFixed.style.height = tableHeaderFixed.clientHeight + "px";
            wrapperTheadFixed.style.overflow = "hidden";

            datatableScroll.addEventListener("scroll", this.scrollHeader.bind(this));
            tableResponsive.addEventListener("scroll", this.scrollHeader.bind(this));
            this.scrollHeader();

        }
    }

    scrollHeader() {
        const wrapper = document.querySelector(this.selector);
        const datatableScroll = wrapper.querySelector(".datatable-scroll");
        const tableResponsive = wrapper.querySelector(".table-responsive");
        const wrapperTheadFixed = wrapper.querySelector(".index_blank-thead_fixed");
        let tableResponsiveScrollLeft = tableResponsive.scrollLeft;
        let datatableScrollLeft = datatableScroll.scrollLeft;
        let scrollLeft = Math.max(datatableScrollLeft, tableResponsiveScrollLeft);

        if (wrapper.querySelector(".thead_fixed-wrapper-fixed")) {
            wrapperTheadFixed.style.left = "-" + scrollLeft + "px";
        }
    }

    scrollTable(side) {
        const wrapper = document.querySelector(this.selector);
        const datatableScroll = wrapper.querySelector(".datatable-scroll");
        const tableScrollWidth = datatableScroll.scrollWidth;
        const tableScrollWidthClientWidth = datatableScroll.clientWidth;

        if (tableScrollWidth - tableScrollWidthClientWidth > 2) {
            switch (side) {
                case 'left':
                    this.scrollTableLeft();
                    break;

                case 'right':
                    this.scrollTableRight();
                    break;
                default:
            }
        }
        this.showEars();
    }

    scrollTableLeft() {
        const wrapper = document.querySelector(this.selector);
        const datatableScroll = wrapper.querySelector(".datatable-scroll");
        const tableScrollLeft = datatableScroll.scrollLeft;

        if (tableScrollLeft !== 0) {
            datatableScroll.scrollLeft = datatableScroll.scrollLeft - 5;
        }
        this.showEars();
    }

    scrollTableRight() {
        const wrapper = document.querySelector(this.selector);
        const datatableScroll = wrapper.querySelector(".datatable-scroll");
        const tableScrollWidth = datatableScroll.scrollWidth,
            tableScrollWidthClientWidth = datatableScroll.clientWidth,
            tableScrollLeft = datatableScroll.scrollLeft;

        if (tableScrollWidthClientWidth + tableScrollLeft < tableScrollWidth) {
            datatableScroll.scrollLeft = datatableScroll.scrollLeft + 5;
        }
        this.showEars();
    }

    setRowUnderModificationsWidth() {
        const wrapper = document.querySelector(this.selector);
        const table = wrapper.querySelector(".datatable-scroll");

        wrapper.querySelector(".row-under-modifications").style.width = table.clientWidth + "px";
        this.setHeaderPosition();

    }

    fixAddToCard() {
        const wrapper = document.querySelector(this.selector);
        const table = wrapper.querySelector(".datatable-scroll");
        wrapper.querySelector(".row-under-modifications").style.width = table.clientWidth + "px";
    }

    setHeaderPosition() {
        let topPos;
        if ($(this.selector + ' .row-under-modifications').length > 0) {
            topPos = $(this.selector + ' .row-under-modifications').offset().top;
            if (topPos > $(window).height()) {
                topPos = $(window).height();
            }
        }
        let top = $(document).scrollTop(),
            pip = $(this.selector + ' .anchor').offset().top,
            pip2 = $(this.selector + ' .anchor_header').offset().top,
            height = $(this.selector + ' .row-under-modifications').outerHeight();

        if ((pip < top + height + topPos) || (pip2 + 100 > (top + $(window).height()))) {
            $(this.selector + ' .row-under-modifications').addClass('row-under-modifications-fixed');
            $(this.selector + ' .row-under-modifications').removeClass('fixed-add-cart-animation');
        } else {
            if (top > pip - height) {
                $(this.selector + ' .row-under-modifications').removeClass('row-under-modifications-fixed');
                $(this.selector + ' .row-under-modifications').addClass('fixed-add-cart-animation');
            } else {
                $(this.selector + ' .row-under-modifications').removeClass('row-under-modifications-fixed');
                $(this.selector + ' .row-under-modifications').addClass('fixed-add-cart-animation');
            }
        }
    }

    init() {
        const wrapper = document.querySelector(this.selector);
        if (!wrapper.querySelector('.nothing_to_show')) {
            window.addEventListener("DOMContentLoaded", this.setEventsIndexBlankTable.bind(this));
            window.addEventListener("DOMContentLoaded", this.fixAddToCard.bind(this));
            window.addEventListener("DOMContentLoaded", this.initPerfectScrols.bind(this));
            window.addEventListener("resize", this.setAddCartPosition.bind(this));
            window.addEventListener("resize", this.showEars.bind(this));
            window.addEventListener("resize", this.setRowUnderModificationsWidth.bind(this));
            window.addEventListener("load", this.setAddCartPosition.bind(this));
            window.addEventListener("load", this.relocateTableHeader.bind(this));
            window.addEventListener("load", this.setEarsTopPosition.bind(this));
            window.addEventListener("load", this.setHeaderPosition.bind(this));
            window.addEventListener("scroll", this.setAddCartPosition.bind(this));
            window.addEventListener("scroll", this.setEarsTopPosition.bind(this));
            window.addEventListener("scroll", this.setHeaderPosition.bind(this));
        }
    }
}

function addToBasket() {

    document.querySelector(".modal_add_to_bascket-preloader").style.display = "flex";
    document.querySelector(".modal_add_to_bascket-on_success").style.display = "none";
    document.querySelector(".modal_add_to_bascket-on_error").style.display = "none";

    setTimeout(
        $.ajax({
            type: 'POST',
            url: site_path + 'include/ajax/b2b_buy.php',
            data: {'action': 'add'},

            success: function (data) {
                data = JSON.parse(data);

                if (data.STATUS === 'OK') {
                    var arSpin = $('.form-control.touchspin-empty');

                    $.each(arSpin, function (key, el) {
                        $(el).val(0);
                    });

                    $('.cart_header a span:last-child').html(data.BASKET_ITEM_QNT);

                    document.querySelector(".modal_add_to_bascket-on_success").style.display = "block";
                    document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";

                } else if (data.length === 0) {

                    document.querySelector(".modal_add_to_bascket-on_error").style.display = "block";
                    document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";

                }

            },

            error: function () {
                document.querySelector(".modal_add_to_bascket-on_error").style.display = "block";
                document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";
            }
        }), 15);
}

function setExcelOutIcon(icon) {
    let iconContainer = document.querySelector(".export_excel_preloader > i");
    iconContainer.setAttribute("class", icon);
}

function excelOut() {
    setExcelOutIcon("icon-spinner2 spinner` mr-2");

    setTimeout(function () {

        // BX.showWait();
        var file = '';

        $.ajax({
            type: 'POST',
            async: false,
            url: site_path + 'include/ajax/blank_excel_export.php',
            data: {
                table_header: tableHeader,
                filterProps: filterProps,
                priceCodes: priceCodes,
                file: file
            },
            success: function (data) {
                if (data !== undefined && data !== '') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {

                    }
                }

                if (data.TYPE !== undefined) {
                    console.log(data.MESSAGE);
                } else if (data !== undefined && data !== '') {
                    file = data;
                }
            },
            complete: function () {
                setExcelOutIcon("icon-upload mr-2");
            }
        });

        if (file !== undefined && file !== '') {
            var now = new Date();

            var dd = now.getDate();
            if (dd < 10) dd = '0' + dd;
            var mm = now.getMonth() + 1;
            if (mm < 10) mm = '0' + mm;
            var hh = now.getHours();
            if (hh < 10) hh = '0' + hh;
            var mimi = now.getMinutes();
            if (mimi < 10) mimi = '0' + mimi;
            var ss = now.getSeconds();
            if (ss < 10) ss = '0' + ss;

            var rand = 0 - 0.5 + Math.random() * (999999999 - 0 + 1)
            rand = Math.round(rand);

            var name = 'blank_' + now.getFullYear() + '_' + mm + '_' + dd + '_' + hh + '_' + mimi + '_' + ss + '_' + rand + '.xlsx';

            var link = document.createElement('a');
            link.setAttribute('href', file);
            link.setAttribute('download', name);
            var event = document.createEvent("MouseEvents");
            event.initMouseEvent(
                "click", true, false, window, 0, 0, 0, 0, 0
                , false, false, false, false, 0, null
            );
            link.dispatchEvent(event);
        }
    }, 15);

    // BX.closeWait();
}

function setExcelOutIconImport(icon) {
    let iconContainer = document.querySelectorAll("#mfi-mfiEXCEL_FILE-button > span > i");
    for (let i = 0; i < iconContainer.length; i++) {
        iconContainer[i].setAttribute("class", icon);
    }
}

BX.addCustomEvent('onUploadDone', function (file) {
    if (file['file_id'] !== '' && file['file_id'] !== undefined) {
        setExcelOutIconImport("icon-spinner2 spinner mr-2");

        setTimeout(function () {
            $.ajax({
                type: 'POST',
                async: false,
                url: site_path + 'include/ajax/blank_excel_import.php',
                data: {
                    file_id: file['file_id'],
                    quantity: tableHeader['QUANTITY']
                },
                success: function (data) {
                    if (data !== undefined && data !== 'null' && data !== '') {
                        var arProducts = JSON.parse(data);
                        var prodCount = Object.keys(arProducts).length;

                        if (arProducts.TYPE === undefined || arProducts.TYPE === 'null' || arProducts.TYPE === '') {
                            location.reload();
                        }

                    } else {
                        location.reload();
                    }
                },
                complete: function () {
                    setExcelOutIconImport("icon-upload mr-2");
                },
            });
        }, 15);
    }
});
