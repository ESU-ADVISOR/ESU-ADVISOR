document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search-piatti");
  const clearButton = document.getElementById("clear-search");
  const resultsCount = document.getElementById("search-results-count");

  if (!searchInput || !clearButton || !resultsCount) return;

  function toggleClearButton() {
    clearButton.style.display = searchInput.value.length > 0 ? "block" : "none";
  }

  function filterPiatti() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedMensaId = document.getElementById("mense-select").value;

    // Seleziona solo i piatti dell'attuale mensa selezionata
    const currentMensaPiatti = document.querySelector(
      `.menu-item-container[data-mensa-id="${selectedMensaId}"]`,
    );

    if (!currentMensaPiatti) return;

    const menuItems = currentMensaPiatti.querySelectorAll(".menu-item");
    let visibleCount = 0;
    let totalItems = menuItems.length;

    menuItems.forEach((item) => {
      const titolo = item.querySelector("h3")?.textContent.toLowerCase() || "";
      const descrizione =
        item.querySelector("p")?.textContent.toLowerCase() || "";

      // Ottieni informazioni allergeni se presenti
      const allergeniElement = item.querySelector(".allergens-list");
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

    if (searchTerm === "") {
      resultsCount.textContent = "";
    } else {
      if (visibleCount === 0) {
        resultsCount.textContent = `Nessun piatto trovato per "${searchTerm}"`;
      } else if (visibleCount === 1) {
        resultsCount.textContent = `1 piatto trovato per "${searchTerm}"`;
      } else {
        resultsCount.textContent = `${visibleCount} piatti trovati per "${searchTerm}"`;
      }
    }

    if (visibleCount === 0 && currentMensaPiatti.querySelector(".empty-menu")) {
      currentMensaPiatti.querySelector(".empty-menu").style.display = searchTerm
        ? "none"
        : "";
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

  document
    .getElementById("mense-select")
    .addEventListener("change", function () {
      searchInput.value = "";
      toggleClearButton();
      filterPiatti();
    });

  toggleClearButton();
});
