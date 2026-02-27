"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var quickViewPs;
document.addEventListener('DOMContentLoaded', function () {
  if (document.querySelector('.quick-view__main-content')) {
    quickViewPs = new PerfectScrollbar('.quick-view__main-content', {
      wheelSpeed: 0.5,
      wheelPropagation: true,
      minScrollbarLength: 20
    });
  }
});

var QuickView = /*#__PURE__*/function () {
  function QuickView(selector) {
    _classCallCheck(this, QuickView);

    if (_typeof(QuickView.instance) === 'object') {
      return QuickView.instance;
    }

    QuickView.instance = this;
    this.selector = selector;
    this.quantity_hide = 0;
    return this;
  }

  _createClass(QuickView, [{
    key: "init",
    value: function init() {
      var wrapper = document.querySelector(this.selector);
      var tabItem = wrapper.querySelectorAll(".tabs-list__item"),
          tabContent = wrapper.querySelectorAll(".tabs-content"),
          quantityMinus = wrapper.querySelector(".input-wrap__minus"),
          quantityPlus = wrapper.querySelector(".input-wrap__plus"),
          quantity = wrapper.querySelector(".input-wrap__input"),
          gallary_item = wrapper.querySelectorAll(".gallery-item__link"),
          gallary_block = wrapper.querySelector(".gallery-block"),
          gallary_slider = wrapper.querySelector(".gallery-slider"),
          close_slider_btn = wrapper.querySelector(".gallery-slider__btn-wrap"),
          filter_prop_item = wrapper.querySelectorAll(".filter-prop-item__item"),
          btn_openup = wrapper.querySelector(".offers-block__btn-openup"),
          offer_quantity_minus = wrapper.querySelectorAll(".quantity-wrap__minus"),
          offer_quantity_plus = wrapper.querySelectorAll(".quantity-wrap__plus"),
          offer_item = wrapper.querySelectorAll(".offers-block__item"),
          prop_item = [],
          show_more_prop = [],
          quick_view_main_content = wrapper.querySelector(".quick-view__main-content"),
          quick_view_shadow = wrapper.querySelector(".quick-view__shadow");
      /*show/hide shadow in the bottom main container quick view */

      quick_view_main_content.addEventListener('scroll', function () {
        quick_view_shadow.style.bottom = -1 * quick_view_main_content.scrollTop + "px";
        quick_view_shadow.style.display = this.offsetHeight + this.scrollTop === this.scrollHeight ? "none" : "block";
      });
      /* change quantity offer*/

      for (var i = 0; i < offer_quantity_plus.length; i++) {
        offer_quantity_plus[i].addEventListener("click", function () {
          for (var _i = 0; _i < this.parentNode.children.length; _i++) {
            if (this.parentNode.children[_i].classList.contains("quantity-wrap__input")) {
              if (!isNaN(this.parentNode.children[_i].value)) {
                +this.parentNode.children[_i].value++;
              }
            }
          }
        });
      }
      /* change quantity offer*/


      for (var _i2 = 0; _i2 < offer_quantity_minus.length; _i2++) {
        offer_quantity_minus[_i2].addEventListener("click", function () {
          for (var _i3 = 0; _i3 < this.parentNode.children.length; _i3++) {
            if (this.parentNode.children[_i3].classList.contains("quantity-wrap__input")) {
              if (!isNaN(this.parentNode.children[_i3].value) && +this.parentNode.children[_i3].value > 0) {
                +this.parentNode.children[_i3].value--;
              }
            }
          }
        });
      }
      /*show/hide property product*/


      var _loop = function _loop(_i4) {
        prop_item[_i4] = offer_item[_i4].querySelectorAll(".prop-block__item"), show_more_prop[_i4] = offer_item[_i4].querySelector(".prop-block__show-more");

        for (var j = 3; j < prop_item[_i4].length; j++) {
          prop_item[_i4][j].style.display = "none";
        }

        if (show_more_prop[_i4]) {
          show_more_prop[_i4].addEventListener("click", function () {
            for (var _j = 3; _j < prop_item[_i4].length; _j++) {
              if (prop_item[_i4][_j].style.display === "none") {
                prop_item[_i4][_j].style.display = "list-item";
                show_more_prop[_i4].textContent = BX.message('JS_HIDE');
              } else {
                prop_item[_i4][_j].style.display = "none";
                show_more_prop[_i4].textContent = BX.message('JS_SHOW_ALL');
              }
            }
          });
        }
      };

      for (var _i4 = 0; _i4 < offer_item.length; _i4++) {
        _loop(_i4);
      }

      if (btn_openup) {
        btn_openup.addEventListener("click", this.filterBlockHide.bind(this));
      }
      /*add/remove class 'active' to property item in filter*/


      for (var _i5 = 0; _i5 < filter_prop_item.length; _i5++) {
        filter_prop_item[_i5].addEventListener("click", function () {
          this.classList.toggle('active');
        });
      }
      /*initialize plugin PerfectScrollbar*/

      /*click event for tabs*/


      var _loop2 = function _loop2(_i6) {
        var a = _i6;
        tabItem[a].addEventListener("click", function () {
          $.fancybox.close();
          gallary_slider.style.display = "none";

          if (this.classList.contains("item-tab-gallery")) {
            gallary_block.style.display = "block";
            gallary_slider.style.display = "none";
          }

          for (var j = 0; j < tabContent.length; j++) {
            tabContent[j].classList.remove("active");
            tabItem[j].classList.remove("active");
          }

          this.classList.add("active");
          tabContent[a].classList.add("active");

          if (quickViewPs) {
            quickViewPs.update();
          }

          document.querySelector('.quick-view__main-content').scrollTop = 0;
        });
      };

      for (var _i6 = 0; _i6 < tabItem.length; _i6++) {
        _loop2(_i6);
      }
      /*change quantity product*/


      if (quantityMinus) {
        quantityMinus.addEventListener("click", function () {
          if (!isNaN(quantity.value) && +quantity.value > 0) {
            +quantity.value--;
          }
        });
      }
      /*change quantity product*/


      if (quantityPlus) {
        quantityPlus.addEventListener("click", function () {
          if (!isNaN(quantity.value)) {
            +quantity.value++;
          }
        });
      }
      /*open gallery*/


      for (var _i7 = 0; _i7 < gallary_item.length; _i7++) {
        gallary_item[_i7].addEventListener("click", function (e) {
          if (gallary_slider.style.display === "block") {
            e.preventDefault();
          } else {
            for (var j = 0; j < tabItem.length; j++) {
              tabContent[j].classList.remove("active");
              tabItem[j].classList.remove("active");

              if (tabItem[j].classList.contains("item-tab-gallery")) {
                tabContent[j].classList.add("active");
                tabItem[j].classList.add("active");
              }
            }

            gallary_block.style.display = "none";
            gallary_slider.style.display = "block";
          }
        });
      }
      /*close gallery*/


      if (close_slider_btn) {
        close_slider_btn.addEventListener("click", function () {
          gallary_block.style.display = "block";
          gallary_slider.style.display = "none";
          $.fancybox.close();
        });
      }
      /*show/hide property block in filter*/


      this.setQuantityHide();
      this.itemsSortDetail();
      window.addEventListener("resize", this.setQuantityHide.bind(this));
    }
  }, {
    key: "filterBlockHide",
    value: function filterBlockHide() {
      var filter_prop_block = document.querySelectorAll(".filter-block__item");
      var btn_openup = document.querySelector(".offers-block__btn-openup");
      var filter_block = document.querySelector(".offers-block__filter");
      var btn_openup_text = document.querySelector(".btn-openup__text");

      for (var i = this.quantity_hide; i < filter_prop_block.length; i++) {
        if (filter_prop_block[i].style.display === "none") {
          filter_prop_block[i].style.display = "flex";
          btn_openup.classList.add("opened");
          filter_block.classList.remove("filter-block-hide");
          btn_openup_text.textContent = BX.message('JS_HIDE');
        } else {
          filter_prop_block[i].style.display = "none";
          btn_openup.classList.remove("opened");
          btn_openup_text.textContent = BX.message('JS_FILTER');

          if (this.quantity_hide === 0) {
            filter_block.classList.add("filter-block-hide");
          }
        }
      }
    }
  }, {
    key: "setQuantityHide",
    value: function setQuantityHide() {
      var filter_block = document.querySelector(".offers-block__filter");
      var btn_openup_wrap = document.querySelector(".offers-block__btn-openup-wrap");
      var filter_prop_block = document.querySelectorAll(".filter-block__item");
      var btn_openup = document.querySelector(".offers-block__btn-openup");
      var btn_openup_text = document.querySelector(".btn-openup__text");

      if (btn_openup_wrap) {
        if (window.matchMedia("(min-width: 831px)").matches) {
          if (filter_prop_block.length < 4) {
            btn_openup_wrap.classList.add("hidden");
          }
        } else {
          btn_openup_wrap.classList.remove("hidden");
        }
      }

      if (filter_prop_block.length) {
        for (var i = 0; i < filter_prop_block.length; i++) {
          filter_prop_block[i].style.display = "flex";
        }
      }

      if (btn_openup) btn_openup.classList.remove("opened");

      if (window.matchMedia("(max-width: 830px)").matches) {
        this.quantity_hide = 0;

        if (filter_block) {
          filter_block.classList.add("filter-block-hide");

          if (btn_openup_wrap) {
            btn_openup_text.textContent = BX.message('JS_FILTER');
          }
        }
      } else {
        this.quantity_hide = 3;
        if (filter_block) filter_block.classList.remove("filter-block-hide");
      }

      for (var _i8 = this.quantity_hide; _i8 < filter_prop_block.length; _i8++) {
        filter_prop_block[_i8].style.display = "none";
      }
    }
  }, {
    key: "itemsSortDetail",
    value: function itemsSortDetail() {
      var btnApply = Array.prototype.slice.call(document.querySelectorAll('.filter-prop-item__item')); //const btnReset = Array.prototype.slice.call(document.querySelectorAll('.offer-properties-item-btnBlock__btn[data-action="reset-sort"]'));

      btnApply.forEach(function (item) {
        item.addEventListener('click', function () {
          var parentBlock = getParent(this, 'offers-block');
          var groupPropsParent = getParent(this, 'filter-block__item');

          if (groupPropsParent.querySelector('.active')) {
            groupPropsParent.classList.add('checked');
          } else {
            groupPropsParent.classList.remove('checked');
          }

          var countParams = Array.prototype.slice.call(parentBlock.querySelectorAll('.filter-block__item.checked')).length;
          var offerItems = Array.prototype.slice.call(parentBlock.querySelectorAll('.offers-block__item'));
          var propsOfferItem = offerItems.map(function (item) {
            return Array.prototype.slice.call(item.querySelectorAll('[data-propvalue]'));
          });
          var propsOfferValue = propsOfferItem.map(function (item) {
            return item.map(function (element) {
              return _defineProperty({}, element.getAttribute('data-propname'), element.getAttribute('data-propvalue'));
            });
          });
          var activeProps = Array.prototype.slice.call(parentBlock.querySelectorAll('.filter-block__item .filter-prop-item__item-wrap'));
          var activePropsItem = activeProps.map(function (item) {
            return Array.prototype.slice.call(item.querySelectorAll('.active[data-value]'));
          });
          var activePropsValue = activePropsItem.map(function (item) {
            return item.map(function (element) {
              return _defineProperty({}, element.getAttribute('data-name'), element.getAttribute('data-value'));
            });
          });

          function _defineProperty(obj, key, value) {
            if (key in obj) {
              Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
              });
            } else {
              obj[key] = value;
            }

            return obj;
          }

          propsOfferValue.forEach(function (item, index) {
            var conformity = [];
            item.forEach(function (element) {
              var _loop3 = function _loop3(key) {
                activePropsValue.forEach(function (props) {
                  props.forEach(function (prop) {
                    if (element[key] === prop[key]) {
                      conformity.push(index);
                    }
                  });
                });
              };

              for (var key in element) {
                _loop3(key);
              }
            });

            if (countParams === conformity.length) {
              offerItems[index].classList.remove('hide');
            } else {
              offerItems[index].classList.add('hide');
            }
          });
        });
      });
    }
  }]);

  return QuickView;
}();

function getParent(item, className) {
  var parentItem = item;

  while (!parentItem.classList.contains(className) && parentItem !== document.body) {
    parentItem = parentItem.parentElement;
  }

  if (parentItem !== document.body) {
    return parentItem;
  }
}