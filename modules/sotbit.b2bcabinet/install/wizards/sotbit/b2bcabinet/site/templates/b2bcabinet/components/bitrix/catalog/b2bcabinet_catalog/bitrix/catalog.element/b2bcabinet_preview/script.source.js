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

class QuickView {
  constructor(selector) {
    if (typeof QuickView.instance === 'object') {
      return QuickView.instance;
    }
    QuickView.instance = this;
    this.selector = selector;
    this.quantity_hide = 0;

    return this;
  }

  init() {
    const wrapper = document.querySelector(this.selector);
    const tabItem = wrapper.querySelectorAll(".tabs-list__item"),
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

      quick_view_shadow.style.display = (this.offsetHeight + this.scrollTop === this.scrollHeight)
          ? "none"
          : "block"
    });

    /* change quantity offer*/
    for (let i = 0; i < offer_quantity_plus.length; i++) {
      offer_quantity_plus[i].addEventListener("click", function () {
        for (let i = 0; i < this.parentNode.children.length; i++) {
          if (this.parentNode.children[i].classList.contains("quantity-wrap__input")) {
            if (!isNaN(this.parentNode.children[i].value)) {
              +this.parentNode.children[i].value++;
            }
          }
        }
      });
    }

    /* change quantity offer*/
    for (let i = 0; i < offer_quantity_minus.length; i++) {
      offer_quantity_minus[i].addEventListener("click", function () {
        for (let i = 0; i < this.parentNode.children.length; i++) {
          if (this.parentNode.children[i].classList.contains("quantity-wrap__input")) {
            if (!isNaN(this.parentNode.children[i].value) && +this.parentNode.children[i].value > 0) {
              +this.parentNode.children[i].value--;
            }
          }
        }
      });
    }

    /*show/hide property product*/
    for (let i = 0; i < offer_item.length; i++) {
      prop_item[i] = offer_item[i].querySelectorAll(".prop-block__item"),
          show_more_prop[i] = offer_item[i].querySelector(".prop-block__show-more");

      for (let j = 3; j < prop_item[i].length; j++) {
        prop_item[i][j].style.display = "none"
      }
      if (show_more_prop[i]) {
        show_more_prop[i].addEventListener("click", function () {
          for (let j = 3; j < prop_item[i].length; j++) {

            if (prop_item[i][j].style.display === "none") {
              prop_item[i][j].style.display = "list-item";
              show_more_prop[i].textContent = BX.message('JS_HIDE');
            } else {
              prop_item[i][j].style.display = "none";
              show_more_prop[i].textContent = BX.message('JS_SHOW_ALL');
            }
          }
        });
      }
    }

    if (btn_openup) {
      btn_openup.addEventListener("click", this.filterBlockHide.bind(this));
    }

    /*add/remove class 'active' to property item in filter*/
    for (let i = 0; i < filter_prop_item.length; i++) {
      filter_prop_item[i].addEventListener("click", function () {
        this.classList.toggle('active');
      });
    }

    /*initialize plugin PerfectScrollbar*/

    /*click event for tabs*/
    for (let i = 0; i < tabItem.length; i++) {

      const a = i;

      tabItem[a].addEventListener("click", function () {
        $.fancybox.close();
        gallary_slider.style.display = "none";

        if (this.classList.contains("item-tab-gallery")) {
          gallary_block.style.display = "block";
          gallary_slider.style.display = "none";
        }

        for (let j = 0; j < tabContent.length; j++) {
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
    for (let i = 0; i < gallary_item.length; i++) {
      gallary_item[i].addEventListener("click", function (e) {
        if (gallary_slider.style.display === "block") {
          e.preventDefault();
        } else {
          for (let j = 0; j < tabItem.length; j++) {
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

  filterBlockHide() {
    const filter_prop_block = document.querySelectorAll(".filter-block__item");
    const btn_openup = document.querySelector(".offers-block__btn-openup");
    const filter_block = document.querySelector(".offers-block__filter");
    const btn_openup_text = document.querySelector(".btn-openup__text");

    for (let i = this.quantity_hide; i < filter_prop_block.length; i++) {
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

  setQuantityHide() {
    const filter_block = document.querySelector(".offers-block__filter");
    const btn_openup_wrap = document.querySelector(".offers-block__btn-openup-wrap");
    const filter_prop_block = document.querySelectorAll(".filter-block__item");
    const btn_openup = document.querySelector(".offers-block__btn-openup");
    const btn_openup_text = document.querySelector(".btn-openup__text");

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
      for (let i = 0; i < filter_prop_block.length; i++) {
        filter_prop_block[i].style.display = "flex"
      }
    }

    if (btn_openup)
      btn_openup.classList.remove("opened");

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
      if (filter_block)
        filter_block.classList.remove("filter-block-hide");
    }

    for (let i = this.quantity_hide; i < filter_prop_block.length; i++) {
      filter_prop_block[i].style.display = "none"
    }
  }

  itemsSortDetail() {
    const btnApply = Array.prototype.slice.call(document.querySelectorAll('.filter-prop-item__item'));
    //const btnReset = Array.prototype.slice.call(document.querySelectorAll('.offer-properties-item-btnBlock__btn[data-action="reset-sort"]'));
    btnApply.forEach(function (item) {
      item.addEventListener('click', function () {
        const parentBlock = getParent(this, 'offers-block');
        const groupPropsParent = getParent(this, 'filter-block__item');
        if (groupPropsParent.querySelector('.active')) {
          groupPropsParent.classList.add('checked');
        } else {
          groupPropsParent.classList.remove('checked');
        }
        const countParams = Array.prototype.slice.call(parentBlock.querySelectorAll('.filter-block__item.checked')).length;

        const offerItems = Array.prototype.slice.call(parentBlock.querySelectorAll('.offers-block__item'));
        const propsOfferItem = offerItems.map(function (item) {
          return Array.prototype.slice.call(item.querySelectorAll('[data-propvalue]'));
        });

        const propsOfferValue = propsOfferItem.map(function (item) {
          return item.map(function (element) {
            return _defineProperty({}, element.getAttribute('data-propname'), element.getAttribute('data-propvalue'));
          });
        });

        const activeProps = Array.prototype.slice.call(parentBlock.querySelectorAll('.filter-block__item .filter-prop-item__item-wrap'));
        const activePropsItem = activeProps.map(function (item) {
          return Array.prototype.slice.call(item.querySelectorAll('.active[data-value]'));
        });

        const activePropsValue = activePropsItem.map(function (item) {
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
          let conformity = [];
          item.forEach(function (element) {

            for (let key in element) {

              activePropsValue.forEach(function (props) {
                props.forEach(function (prop) {
                  if (element[key] === prop[key]) {
                    conformity.push(index)
                  }
                });
              });
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
}

function getParent(item, className) {
  let parentItem = item;
  while (!parentItem.classList.contains(className) && (parentItem !== document.body)) {
    parentItem = parentItem.parentElement;
  }
  if (parentItem !== document.body) {
    return parentItem;
  }
}
