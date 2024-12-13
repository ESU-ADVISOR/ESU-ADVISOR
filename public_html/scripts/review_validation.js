document.addEventListener("DOMContentLoaded", function () {
  const searchElement = document.getElementById("piatto-name");
  const form = document.querySelector(".base-form");
  let errors = new Map();

  searchElement.addEventListener("input", validatePiatto);

  form.addEventListener("submit", function (event) {
    if (!validatePiatto()) {
      event.preventDefault();
      displayErrors();
    }
  });

  function validatePiatto() {
    const searchElement = document.getElementById("piatto-name");
    const listaPiatti = document.getElementById("suggerimenti-piatti");

    const options = listaPiatti.getElementsByTagName("option");
    for (let i = 0; i < options.length; i++) {
      if (
        options[i].value.toLowerCase() ===
        searchElement.value.trim().toLowerCase()
      ) {
        errors.delete("piatto-error");
        return true;
      }
    }
    errors.set("piatto-error", "Il piatto non esiste nel database");
    console.log("Not valid");
    return false;
  }

  function displayErrors() {
    const elements = document.getElementsByTagName("small");
    for (let i = 0; i < elements.length; i++) {
      if (errors.has(elements[i].id)) {
        elements[i].display = "block";
        elements[i].textContent = errors.get(elements[i].id);
      } else {
        elements[i].display = "none";
        elements[i].textContent = "";
      }
    }
    errors.clear();
  }
});
