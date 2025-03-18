document.addEventListener("DOMContentLoaded", function() {
  const mensaSelect = document.getElementById("mense-select");
  
  const filterBySelectedMensa = (selectedId) => {
      // Nascondi tutti gli elementi del menù
      document.querySelectorAll("[data-mensa-id]").forEach(element => {
          element.style.display = "none";
      });
      
      // Mostra solo gli elementi della mensa selezionata
      document.querySelectorAll(`[data-mensa-id="${selectedId}"]`).forEach(element => {
          element.style.display = "";
      });
  };
  
  // Inizializza con la prima mensa selezionata
  if (mensaSelect && mensaSelect.value) {
      filterBySelectedMensa(mensaSelect.value);
  }
  
  // Gestisci il cambio di mensa
  if (mensaSelect) {
      mensaSelect.addEventListener("change", function() {
          filterBySelectedMensa(this.value);
      });
  }
});