/*

by: abuSpiha

*/

"use strict";

(function () {
  const pagesTitles = Array.from(document.querySelectorAll(".pageTitle"));
  const windowTitle = document.title.trim();

  const activeMenuTitle = pagesTitles.find(
    (el) => el.textContent.trim() === windowTitle
  );

  if (activeMenuTitle) {
    const closestMenuItem = activeMenuTitle.closest(".menu-item");
    closestMenuItem.classList.add("active");

    const pageCategory = closestMenuItem.parentElement?.parentElement;
    if (pageCategory?.classList.contains("menu-item")) {
      pageCategory.classList.add("active", "open");
    }
  }
})();
