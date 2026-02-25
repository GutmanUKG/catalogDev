document.addEventListener("DOMContentLoaded", () => {
  const btnAuth = document.querySelector("#toggle_auth_form");
  const popupForm = document.querySelector(".popup_form");
  const authLink = document.querySelector(".auth_link");
  const burgerBtn = document.querySelector(".burger-btn");
  const headerBar = document.querySelector(".header-bar");
  const closeBurger = headerBar.querySelector(".close");
  btnAuth.addEventListener("click", showAuthForm);
  authLink.addEventListener("click", showAuthForm);
  function showAuthForm(e) {
    e.preventDefault();
    popupForm.classList.add("active");
  }
  burgerBtn.addEventListener("click", () => {
    headerBar.classList.add("mobile", "active");
  });
  closeBurger.addEventListener("click", (e) => {
    e.preventDefault();
    headerBar.classList.remove("mobile", "active");
  });
});
