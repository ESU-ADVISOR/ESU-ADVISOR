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

    // Cerca tutti i menu items (incluso piatto del giorno) per la mensa selezionata
    const menuItems = document.querySelectorAll(
      `.menu-item[data-mensa-id="${selectedMensaId}"]`
    );
    
    let visibleCount = 0;
    const totalItems = menuItems.length;

    // Filtra tutti i piatti (menu + piatto del giorno)
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
        // Per i menu items normali, mostra il parent li
        if (item.parentElement && item.parentElement.tagName === 'LI') {
          item.parentElement.style.display = "";
        } 
        // Per il piatto del giorno, mostra il parent ul
        else if (item.parentElement && item.parentElement.tagName === 'UL') {
          item.parentElement.style.display = "";
        }
        visibleCount++;
      } else {
        // Nascondi l'elemento appropriato
        if (item.parentElement && item.parentElement.tagName === 'LI') {
          item.parentElement.style.display = "none";
        } 
        else if (item.parentElement && item.parentElement.tagName === 'UL') {
          item.parentElement.style.display = "none";
        }
      }
    });

    // Gestisci messaggi vuoti
    const emptyMenu = document.querySelector(`.empty-menu[data-mensa-id="${selectedMensaId}"]`);
    const emptyDishMessage = document.querySelector(`.dish-of-day-empty[data-mensa-id="${selectedMensaId}"]`);

    if (searchTerm === "") {
      resultsCount.textContent = "";
      if (emptyMenu) {
        // Conta solo i menu items normali (non piatto del giorno) per il messaggio vuoto
        const normalMenuItems = document.querySelectorAll(
          `.menu-item[data-mensa-id="${selectedMensaId}"]:not(.dish-of-day-item)`
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

  // Event listeners per la ricerca
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

  // Ascolta il cambio di mensa gestito da menu_select.js
  window.addEventListener('mensaChanged', function(event) {
    // Reset della ricerca quando cambia la mensa
    searchInput.value = "";
    toggleClearButton();
    resultsCount.textContent = "";
  });

  // Inizializzazione
  toggleClearButton();
});