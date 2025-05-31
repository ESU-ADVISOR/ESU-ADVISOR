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
    const selectedMensaId = document.getElementById("mense-select").value;

    const menuItems = document.querySelectorAll(
      `.menu-item[data-mensa-id="${selectedMensaId}"]`
    );
    
    let visibleCount = 0;
    let totalItems = menuItems.length;

    menuItems.forEach((item) => {
      const article = item.querySelector("article");
      if (!article) return;
      
      const titolo = article.querySelector("h3")?.textContent.toLowerCase() || "";
      const descrizione = article.querySelector("p")?.textContent.toLowerCase() || "";

      // Ottieni informazioni allergeni se presenti
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

    const emptyMenu = document.querySelector(`.empty-menu[data-mensa-id="${selectedMensaId}"]`);

    if (searchTerm === "") {
      resultsCount.textContent = "";
      if (emptyMenu) {
        emptyMenu.style.display = totalItems === 0 ? "" : "none";
      }
    } else {
      if (visibleCount === 0) {
        resultsCount.textContent = `Nessun piatto trovato per "${searchTerm}"`;
        if (emptyMenu) {
          emptyMenu.style.display = "none";
        }
      } else if (visibleCount === 1) {
        resultsCount.textContent = `1 piatto trovato per "${searchTerm}"`;
        if (emptyMenu) {
          emptyMenu.style.display = "none";
        }
      } else {
        resultsCount.textContent = `${visibleCount} piatti trovati per "${searchTerm}"`;
        if (emptyMenu) {
          emptyMenu.style.display = "none";
        }
      }
    }
  }

  function showMensaPiatti(mensaId) {
    const allMenuItems = document.querySelectorAll(".menu-item");
    allMenuItems.forEach(item => {
      item.style.display = "none";
    });

    const allEmptyMenus = document.querySelectorAll(".empty-menu");
    allEmptyMenus.forEach(item => {
      item.style.display = "none";
    });

    const currentMensaItems = document.querySelectorAll(
      `[data-mensa-id="${mensaId}"]`
    );
    currentMensaItems.forEach(item => {
      item.style.display = "";
    });
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
      const selectedMensaId = this.value;
      searchInput.value = "";
      toggleClearButton();
      showMensaPiatti(selectedMensaId);
      resultsCount.textContent = "";
    });

  const initialMensaId = document.getElementById("mense-select").value;
  if (initialMensaId) {
    showMensaPiatti(initialMensaId);
  }
  
  toggleClearButton();

  clearButton.addEventListener("keydown", function (event) {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      clearButton.click();
    }
  });
});