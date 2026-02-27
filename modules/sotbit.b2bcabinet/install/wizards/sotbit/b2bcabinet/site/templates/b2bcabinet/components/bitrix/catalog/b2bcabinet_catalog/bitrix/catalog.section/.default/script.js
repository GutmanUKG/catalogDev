"use strict";

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError("Cannot call a class as a function");
    }
}

function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
    }
}

function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
}

$(document).on("click touchstart", 'button.add_to_cart', void 0, addToBasket);
$(document).ready(function () {
    $(document).on("click touchstart", "#blank-export-in-excel", this, excelOut);
});

var CatalogSection = /*#__PURE__*/function () {
    function CatalogSection(wrapperSelector) {
        _classCallCheck(this, CatalogSection);

        this.selector = wrapperSelector;
        this.scrollEars = {
            showAll: function showAll() {
                var ears = document.querySelectorAll(".index_blank .table-responsive .scroll-ears");

                for (var i = 0; i < ears.length; i++) {
                    ears[i].style.display = "flex";
                }
            },
            hideAll: function hideAll() {
                var ears = document.querySelectorAll(".index_blank .table-responsive .scroll-ears");

                for (var i = 0; i < ears.length; i++) {
                    ears[i].style.display = "none";
                }
            },
            hideLeft: function hideLeft() {
                var leftEar = document.querySelector('.main-grid-ear-left');
                leftEar.style.opacity = "0";
                leftEar.style.visibility = "hidden";
            },
            hideRight: function hideRight() {
                var rightEar = document.querySelector('.main-grid-ear-right');
                rightEar.style.opacity = "0";
                rightEar.style.visibility = "hidden";
            },
            showLeft: function showLeft() {
                var leftEar = document.querySelector('.main-grid-ear-left');
                leftEar.style.opacity = "1";
                leftEar.style.visibility = "visible";
            },
            showRight: function showRight() {
                var rightEar = document.querySelector('.main-grid-ear-right');
                rightEar.style.opacity = "1";
                rightEar.style.visibility = "visible";
            }
        };
    }

    _createClass(CatalogSection, [{
        key: "relocateTableHeader",
        value: function relocateTableHeader() {
            var wrapper = document.querySelector(this.selector);
            var tableHeader = wrapper.querySelector(".index_blank-thead");
            var tableHeaderTitles = tableHeader.querySelectorAll("th");
            var tableHeaderFixed = wrapper.querySelector(".index_blank-thead_fixed");

            for (var i = 0; i < tableHeaderTitles.length; i++) {
                var fixedHeaderChild = document.createElement("div");
                fixedHeaderChild.innerText = tableHeaderTitles[i].innerText;
                fixedHeaderChild.style.display = "inline-block";
                tableHeaderFixed.appendChild(fixedHeaderChild);
            }

            var displayTableHeader = {
                hide: function hide() {
                    tableHeader.style.opacity = "0";
                    tableHeader.style.visibility = "hidden";
                },
                show: function show() {
                    tableHeader.style.opacity = "1";
                    tableHeader.style.visibility = "visible";
                }
            };
            displayTableHeader.hide();
            resizeTableFixed();
            window.addEventListener("resize", resizeTableFixed);

            function resizeTableFixed() {
                displayTableHeader.show();
                var tableHeaderFixedItems = tableHeaderFixed.querySelectorAll("div");

                for (var _i = 0; _i < tableHeaderTitles.length; _i++) {
                    tableHeaderFixedItems[_i].style.width = tableHeaderTitles[_i].offsetWidth + "px";
                    tableHeaderFixedItems[_i].style.height = tableHeaderTitles[_i].offsetHeight + "px";
                }

                displayTableHeader.hide();
            }
        }
    }, {
        key: "setEarsTopPosition",
        value: function setEarsTopPosition() {
            var wrapper = document.querySelector(this.selector);
            var ears = wrapper.querySelectorAll(".scroll-ears"),
                clientHeight = document.documentElement.clientHeight,
                anchorHeader = wrapper.querySelector('.anchor_header'),
                tableHeight = wrapper.querySelector(".table-responsive").clientHeight,
                anchorTop = 0;

            if (anchorHeader) {
                anchorTop = anchorHeader.getBoundingClientRect().top;
            }

            var earsTopPos = anchorTop < 0 ? clientHeight / 2 - anchorTop : (clientHeight - anchorTop) / 2;
            earsTopPos = -anchorTop + clientHeight > tableHeight ? (tableHeight + anchorTop) / 2 - anchorTop : earsTopPos;
            var earsTopPosPercents = 100 * earsTopPos / tableHeight;

            if (earsTopPosPercents < 0) {
                earsTopPosPercents = 0;
            } else if (earsTopPosPercents > 100) {
                earsTopPosPercents = 100;
            }

            for (var i = 0; i < ears.length; i++) {
                ears[i].style.top = earsTopPosPercents + "%";
            }
        }
    }, {
        key: "showEars",
        value: function showEars() {
            var wrapper = document.querySelector(this.selector);
            var datatableScroll = wrapper.querySelector(".datatable-scroll");
            var tableScrollWidth = datatableScroll.scrollWidth,
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
    }, {
        key: "setEventsIndexBlankTable",
        value: function setEventsIndexBlankTable() {
            var idTimer;
            var wrapper = document.querySelector(this.selector);
            var leftEar = wrapper.querySelector('.main-grid-ear-left');
            var rightEar = wrapper.querySelector('.main-grid-ear-right');
            var table = wrapper.querySelector(".index_blank-table");

            var _this = this;

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
    }, {
        key: "initPerfectScrols",
        value: function initPerfectScrols() {
            var prfscr = new PerfectScrollbar('.datatable-scroll', {
                wheelSpeed: 0.5,
                wheelPropagation: true,
                minScrollbarLength: 20,
                suppressScrollY: true
            });
        }
    }, {
        key: "setAddCartPosition",
        value: function setAddCartPosition() {
            var scrollTop = $(document).scrollTop(),
                anchorBottom = 0,
                pip = 0;
            var wrapper = document.querySelector(this.selector);
            var wrapperTheadFixed = wrapper.querySelector(".index_blank-thead_fixed-wrapper");

            if (wrapper.querySelector(".anchor_header") && wrapper.querySelector(".anchor")) {
                pip = $(this.selector + ' .anchor_header').offset().top;
                anchorBottom = $(this.selector + ' .anchor').offset().top;
            }

            if (pip > scrollTop || anchorBottom < scrollTop + 80) {
                wrapperTheadFixed.classList.remove('thead_fixed-wrapper-fixed');
                wrapperTheadFixed.style.width = "auto";
                wrapperTheadFixed.style.height = "auto";
                wrapperTheadFixed.style.overflow = "";
                wrapper.querySelector(".index_blank-thead_fixed").style.left = "0";
            } else {
                wrapperTheadFixed.classList.add('thead_fixed-wrapper-fixed');
                var table = document.querySelector(".index_blank-table");
                var tableHeaderFixed = wrapper.querySelector(".index_blank-thead_fixed");
                var datatableScroll = wrapper.querySelector(".datatable-scroll");
                var tableResponsive = wrapper.querySelector(".table-responsive");
                wrapperTheadFixed.style.width = table.clientWidth + "px";
                wrapperTheadFixed.style.height = tableHeaderFixed.clientHeight + "px";
                wrapperTheadFixed.style.overflow = "hidden";
                datatableScroll.addEventListener("scroll", this.scrollHeader.bind(this));
                tableResponsive.addEventListener("scroll", this.scrollHeader.bind(this));
                this.scrollHeader();
            }
        }
    }, {
        key: "scrollHeader",
        value: function scrollHeader() {
            var wrapper = document.querySelector(this.selector);
            var datatableScroll = wrapper.querySelector(".datatable-scroll");
            var tableResponsive = wrapper.querySelector(".table-responsive");
            var wrapperTheadFixed = wrapper.querySelector(".index_blank-thead_fixed");
            var tableResponsiveScrollLeft = tableResponsive.scrollLeft;
            var datatableScrollLeft = datatableScroll.scrollLeft;
            var scrollLeft = Math.max(datatableScrollLeft, tableResponsiveScrollLeft);

            if (wrapper.querySelector(".thead_fixed-wrapper-fixed")) {
                wrapperTheadFixed.style.left = "-" + scrollLeft + "px";
            }
        }
    }, {
        key: "scrollTable",
        value: function scrollTable(side) {
            var wrapper = document.querySelector(this.selector);
            var datatableScroll = wrapper.querySelector(".datatable-scroll");
            var tableScrollWidth = datatableScroll.scrollWidth;
            var tableScrollWidthClientWidth = datatableScroll.clientWidth;

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
    }, {
        key: "scrollTableLeft",
        value: function scrollTableLeft() {
            var wrapper = document.querySelector(this.selector);
            var datatableScroll = wrapper.querySelector(".datatable-scroll");
            var tableScrollLeft = datatableScroll.scrollLeft;

            if (tableScrollLeft !== 0) {
                datatableScroll.scrollLeft = datatableScroll.scrollLeft - 5;
            }

            this.showEars();
        }
    }, {
        key: "scrollTableRight",
        value: function scrollTableRight() {
            var wrapper = document.querySelector(this.selector);
            var datatableScroll = wrapper.querySelector(".datatable-scroll");
            var tableScrollWidth = datatableScroll.scrollWidth,
                tableScrollWidthClientWidth = datatableScroll.clientWidth,
                tableScrollLeft = datatableScroll.scrollLeft;

            if (tableScrollWidthClientWidth + tableScrollLeft < tableScrollWidth) {
                datatableScroll.scrollLeft = datatableScroll.scrollLeft + 5;
            }

            this.showEars();
        }
    }, {
        key: "setRowUnderModificationsWidth",
        value: function setRowUnderModificationsWidth() {
            var wrapper = document.querySelector(this.selector);
            var table = wrapper.querySelector(".datatable-scroll");
            wrapper.querySelector(".row-under-modifications").style.width = table.clientWidth + "px";
            this.setHeaderPosition();
        }
    }, {
        key: "fixAddToCard",
        value: function fixAddToCard() {
            var wrapper = document.querySelector(this.selector);
            var table = wrapper.querySelector(".datatable-scroll");
            wrapper.querySelector(".row-under-modifications").style.width = table.clientWidth + "px";
        }
    }, {
        key: "setHeaderPosition",
        value: function setHeaderPosition() {
            var topPos;

            if ($(this.selector + ' .row-under-modifications').length > 0) {
                topPos = $(this.selector + ' .row-under-modifications').offset().top;

                if (topPos > $(window).height()) {
                    topPos = $(window).height();
                }
            }

            var top = $(document).scrollTop(),
                pip = $(this.selector + ' .anchor').offset().top,
                pip2 = $(this.selector + ' .anchor_header').offset().top,
                height = $(this.selector + ' .row-under-modifications').outerHeight();

            if (pip < top + height + topPos || pip2 + 100 > top + $(window).height()) {
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
    }, {
        key: "init",
        value: function init() {
            var wrapper = document.querySelector(this.selector);

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
    }]);

    return CatalogSection;
}();

function addToBasket() {
    document.querySelector(".modal_add_to_bascket-preloader").style.display = "flex";
    document.querySelector(".modal_add_to_bascket-on_success").style.display = "none";
    document.querySelector(".modal_add_to_bascket-on_error").style.display = "none";
    const errorBlock = document.querySelector(".modal_add_to_bascket-on_error");
    const errorText = errorBlock.querySelector("p.error-text");

    setTimeout(
        function () {
            $.ajax({
                type: 'POST',
                url: site_path + 'include/ajax/b2b_buy.php',
                data: {
                    'action': 'add'
                },
                success: function success(data) {
                    data = JSON.parse(data);

                    if (data.STATUS === 'OK') {
                        var arSpin = $('.form-control.touchspin-empty');
                        $.each(arSpin, function (key, el) {
                            $(el).val(0);
                        });
                        $('.cart_header a span:last-child').html(data.BASKET_ITEM_QNT);
                        document.querySelector(".modal_add_to_bascket-on_success").style.display = "block";
                        document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";
                    } else if (data.STATUS === 'ERROR') {
                        if (data.MESSAGE) {
                            if (errorText) {
                                errorText.innerHTML = data.MESSAGE;
                            } else {
                                let errorText = document.createElement('p');
                                errorText.classList.add('error-text');
                                errorText.innerHTML = data.MESSAGE;
                                errorBlock.append(errorText);
                            }
                        }
                        errorBlock.style.display = "block";
                        document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";
                    } else {
                        errorBlock.style.display = "block";
                        document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";
                    }
                },
                error: function error() {
                    document.querySelector(".modal_add_to_bascket-on_error").style.display = "block";
                    document.querySelector(".modal_add_to_bascket-preloader").style.display = "none";
                }
            })
        }, 15);
}

function setExcelOutIcon(icon) {
    var iconContainer = document.querySelector(".export_excel_preloader > i");
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
            success: function success(data) {
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
            complete: function complete() {
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
            var rand = 0 - 0.5 + Math.random() * (999999999 - 0 + 1);
            rand = Math.round(rand);
            var name = 'blank_' + now.getFullYear() + '_' + mm + '_' + dd + '_' + hh + '_' + mimi + '_' + ss + '_' + rand + '.xlsx';
            var link = document.createElement('a');
            link.setAttribute('href', file);
            link.setAttribute('download', name);
            var event = document.createEvent("MouseEvents");
            event.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            link.dispatchEvent(event);
        }
    }, 15); // BX.closeWait();
}

function setExcelOutIconImport(icon) {
    var iconContainer = document.querySelectorAll("#mfi-mfiEXCEL_FILE-button > span > i");

    for (var i = 0; i < iconContainer.length; i++) {
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
                success: function success(data) {
                    if (data !== undefined && data !== 'null' && data !== '') {
                        var arProducts = JSON.parse(data);
                        var prodCount = Object.keys(arProducts).length;
                        if (arProducts.TYPE === undefined || arProducts.TYPE === 'null' || arProducts.TYPE === '') {
                            var link = document.createElement('button');
                            link.setAttribute('type', 'button');
                            link.setAttribute('class', 'modal_excel-import-btn');
                            link.setAttribute('data-toggle', "modal");
                            link.setAttribute('data-target', "#modal_excel-import");
                            var event = document.createEvent("MouseEvents");
                            event.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                            document.body.append(link);
                            link.dispatchEvent(event);
                            var successTitleImport = BX.message('TITLE_IMPORT_SUCCESSFUL');
                            if (arProducts.TOTAL_COUNT) {
                                prodCount = prodCount - 1;
                            }
                            document.querySelector('.modal_excel-import_success h6').textContent = successTitleImport.replace('#PRODUCTS#', prodCount);
                        }
                    } else {
                        location.reload();
                    }
                },
                complete: function complete() {
                    setExcelOutIconImport("icon-upload mr-2");
                }
            });
        }, 15);
    }
});

BX.ready(function () {
    const modalImport = document.querySelector('#modal_excel-import');

    if (modalImport) {
        modalImport.addEventListener('click', reloadCatalogSection);
        const btnModalClose = modalImport.querySelectorAll('[data-dismiss="modal"]');

        for (var i = 0; i < btnModalClose.length; i++) {
            btnModalClose[i].addEventListener('click', reloadCatalogSection);
        }
    }

    function reloadCatalogSection() {
        location.reload();
    }
});