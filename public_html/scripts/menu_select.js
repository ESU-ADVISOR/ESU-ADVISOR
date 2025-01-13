document.addEventListener("DOMContentLoaded", function () {
  const selectElement = document.getElementById("mense-select");

  function edit_list(event) {
    const piatti = document.querySelectorAll(".menu-item");
    const info = document.querySelectorAll(".mense-info-item");
    const selectedMensaId = event.target.value;

    let hasVisibleItems = false;

    piatti.forEach(function (item) {
      const mensaId = item.getAttribute("data-mensa-id");

      if (selectedMensaId === "") {
        item.setAttribute("hidden", "");
      } else if (mensaId === selectedMensaId) {
        item.removeAttribute("hidden");
        hasVisibleItems = true;
      } else {
        item.setAttribute("hidden", "");
      }
    });

    info.forEach(function (item) {
      const mensaId = item.getAttribute("data-mensa-id");

      if (selectedMensaId === "") {
        item.setAttribute("hidden", "");
      } else if (mensaId === selectedMensaId) {
        item.removeAttribute("hidden");
        hasVisibleItems = true;
      } else {
        item.setAttribute("hidden", "");
      }
    });
  }

  selectElement.addEventListener("change", edit_list);

  document.addEventListener("pagehide", function () {
    selectElement.removeEventListener("change", edit_list);
  });

  edit_list({ target: { value: selectElement.value } });
});
