document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search-piatti");
  const clearButton = document.getElementById("clear-search");
  const resultsCount = document.getElementById("search-results-count");

  if (!searchInput || !clearButton || !resultsCount) return;

  function toggleClearButton() {
    const isVisible = searchInput.value.length > 0;
    clearButton.style.display = isVisible ? "block" : "none";

    if (isVisible) {
      clearButton.removeAttribute("tabindex");
      clearButton.removeAttribute("aria-hidden");
    } else {
      clearButton.setAttribute("tabindex", "-1");
      clearButton.setAttribute("aria-hidden", "true");
    }
  }

  function filterPiatti() {
    const searchTerm = searchInput.value.toLowerCase().trim();

    const menuItems = document.querySelectorAll(`.piatti-list .menu-item`);

    let visibleCount = 0;

    menuItems.forEach((item) => {
      const article = item.querySelector("article");
      if (!article) return;

      const titolo =
        article.querySelector("h3")?.textContent.toLowerCase() || "";
      const descrizione =
        article.querySelector("p")?.textContent.toLowerCase() || "";

      const allergeniElement = article.querySelector(".allergens-list");
      const allergeni = allergeniElement
        ? allergeniElement.textContent.toLowerCase()
        : "";

      const matchesSearch =
        titolo.includes(searchTerm) ||
        descrizione.includes(searchTerm) ||
        allergeni.includes(searchTerm);

      if (matchesSearch) {
        item.style.display = "";
        visibleCount++;
      } else {
        item.style.display = "none";
      }
    });

    const emptyMenu = document.querySelector(`.empty-menu`);
    const emptyDishMessage = document.querySelector(`.dish-of-day-empty`);

    if (searchTerm === "") {
      resultsCount.textContent = "";
      if (emptyMenu) {
        const normalMenuItems = document.querySelectorAll(
          `.menu-item:not(.dish-of-day-item)`,
        );
        emptyMenu.style.display = normalMenuItems.length === 0 ? "" : "none";
      }
      if (emptyDishMessage) {
        emptyDishMessage.style.display = "";
      }
    } else {
      if (visibleCount === 0) {
        resultsCount.textContent = `Nessun piatto trovato per "${searchTerm}"`;
      } else if (visibleCount === 1) {
        resultsCount.textContent = `1 piatto trovato per "${searchTerm}"`;
      } else {
        resultsCount.textContent = `${visibleCount} piatti trovati per "${searchTerm}"`;
      }

      if (emptyMenu) {
        emptyMenu.style.display = "none";
      }
      if (emptyDishMessage) {
        emptyDishMessage.style.display = "none";
      }
    }
  }

  searchInput.addEventListener("input", function () {
    toggleClearButton();
    filterPiatti();
  });

  clearButton.addEventListener("click", function () {
    searchInput.value = "";
    toggleClearButton();
    filterPiatti();
    searchInput.focus();
  });

  clearButton.addEventListener("keydown", function (event) {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      clearButton.click();
    }
  });

  window.addEventListener("mensaChanged", function (event) {
    // Reset della ricerca quando cambia la mensa
    searchInput.value = "";
    toggleClearButton();
    resultsCount.textContent = "";
  });

  toggleClearButton();
});
