document.addEventListener("DOMContentLoaded", function () {
  const piattoInput = document.getElementById("piatto");
  const mensaInput = document.getElementById("mensa");
  const form = document.querySelector(".base-form");
  let errors = new Map();

  mensaInput.addEventListener("input", updateDatalist);
  mensaInput.addEventListener("input", validateMensa);
  piattoInput.addEventListener("input", validatePiatto);
  form.addEventListener("submit", function (event) {
    if (!validatePiatto() || !validateMensa() || !validateRating()) {
      event.preventDefault();
    }
  });

  function updateDatalist() {
    const selectedMensa = mensaInput.value;

    const oldDatalist = document.getElementById("suggerimenti-piatti");
    if (oldDatalist) {
      oldDatalist.removeAttribute("id");
    }

    const dynamicDatalists = document.querySelectorAll(
      "datalist.dynamic-datalist",
    );

    dynamicDatalists.forEach(function (datalist) {
      if (datalist.getAttribute("data-mensa-name") === selectedMensa) {
        datalist.id = "suggerimenti-piatti";
      }
    });
  }
  function validateMensa() {
    const mensaInput = document.getElementById("mensa");

    const mensa = mensaInput.value;
    const mensaList = document.getElementById("suggerimenti-mense");
    const options = mensaList.getElementsByTagName("option");
    for (let i = 0; i < options.length; i++) {
      if (options[i].value.toLowerCase() === mensa.trim().toLowerCase()) {
        errors.delete("mensa-error");
        refreshErrors();
        return true;
      }
    }
    errors.set(
      "mensa-error",
      "La mensa non esiste nel <span lang='en'>database</span>",
    );
    refreshErrors();
    return false;
  }
  function validatePiatto() {
    const piattoInput = document.getElementById("piatto");

    const listaPiatti = document.getElementById("suggerimenti-piatti");
    if (!listaPiatti) return;

    const options = listaPiatti.getElementsByTagName("option");
    for (let i = 0; i < options.length; i++) {
      if (
        options[i].value.toLowerCase() ===
        piattoInput.value.trim().toLowerCase()
      ) {
        errors.delete("piatto-error");
        refreshErrors();
        return true;
      }
    }
    errors.set("piatto-error", "Il piatto non esiste nel database");
    refreshErrors();
    return false;
  }
  function validateRating() {
    const ratingInputs = document.getElementsByName("rating");
    let ratingSelected = false;
    for (let i = 0; i < ratingInputs.length; i++) {
      if (ratingInputs[i].checked) {
        ratingSelected = true;
        break;
      }
    }

    if (!ratingSelected) {
      errors.set(
        "rating-error",
        "Per favore, seleziona almeno una stella per la valutazione.",
      );
      refreshErrors();
      return false;
    } else {
      errors.delete("rating-error");
      refreshErrors();
      return true;
    }
  }
  function refreshErrors() {
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
  }
});
