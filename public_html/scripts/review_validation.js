const { SymbolDisplayPartKind } = require("typescript");

document.addEventListener("DOMContentLoaded", function () {
  displayErrors("esecuzione script review_validation.js");


  const piattoInput = document.getElementById("piatto");
  const mensaInput = document.getElementById("mensa");
  const rating = document.getElementById("rating-container");
  const form = document.querySelector(".base-form");
  //let errors = new Map();
  let errors = [];

  mensaInput.addEventListener("input", updateDatalist);
  
  mensaInput.addEventListener("input", validateMensa);
  mensaInput.addEventListener("blur", validateMensa);

  piattoInput.addEventListener("input", validatePiatto);
  piattoInput.addEventListener("blur", validatePiatto);

  form.addEventListener("submit", function (event) {
    if (!validateMensa() || !validatePiatto() || !validateRating()) {
      event.preventDefault();
    }
  });

  function updateDatalist() {
    displayErrors("esecuzione update datalist");
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
    let errors = [];

    document.querySelectorAll(".mensa-error").forEach((el) => el.remove());
    document.getElementById("mensa-error-container")?.remove();

    if (mensa === "") {
      //errors.set("mensa-error", "Per favore, inserisci una mensa.");
      //refreshErrors();
      errors.push("Per favore, inserisci una mensa.");
      displayErrors(errors, "mensa-error-container", mensaInput);
      return false;
    }    

    const options = document.getElementsByClassName("mensa-option");

//    const mensaList = document.getElementById("suggerimenti-mense");
//    const options = mensaList.getElementsByTagName("option");
    for (let i = 0; i < options.length; i++) {
      if (options[i].value.toLowerCase() === mensa.trim().toLowerCase()) {
        //errors.delete("mensa-error");
        //refreshErrors();
        //mensaInput.focus();
        return true;
      }
    }

    //errors.set("mensa-error", "La mensa non esiste nel <span lang='en'>database</span>");
    errors.push("La mensa non esiste nel <span lang='en'>database</span>")
    //refreshErrors();
    displayErrors(errors, "mensa-error-container", mensaInput);
    return false;
  }

  function validatePiatto() {
    const piattoInput = document.getElementById("piatto");

    const listaPiatti = document.getElementById("suggerimenti-piatti");

    document.querySelectorAll(".piatto-error").forEach((el) => el.remove());
    document.getElementById("piatto-error-container")?.remove();

    if (!listaPiatti) return true;

    const options = listaPiatti.getElementsByTagName("option");
    for (let i = 0; i < options.length; i++) {
      if (
        options[i].value.toLowerCase() ===
        piattoInput.value.trim().toLowerCase()
      ) {
        //errors.delete("piatto-error");
        //refreshErrors();
        return true;
      }
    }
    //errors.set("piatto-error", "Il piatto non esiste nel <span lang='en'>database</span>");
    //refreshErrors();+

    errors.push("Il piatto non esiste nel <span lang='en'>database</span>");
    displayErrors(errors, "piatto-error-container", piattoInput);
    return false;
  }

  function validateRating() {
    const ratingInputs = document.getElementsByName("rating");
    const rating = document.getElementByID("rating-container");

    document.querySelectorAll(".rating-error").forEach((el) => el.remove());
    document.getElementById("rating-error-container")?.remove();

    let ratingSelected = false;
    for (let i = 0; i < ratingInputs.length; i++) {
      if (ratingInputs[i].checked) {
        ratingSelected = true;
        break;
      }
    }

    if (!ratingSelected) {
      errors.push("Per favore, seleziona almeno una stella per la valutazione.");
      displayErrors(errors, "rating-error-container", rating);
      /*
      errors.set(
        "rating-error",
        "Per favore, seleziona almeno una stella per la valutazione.",
      );
      refreshErrors();*/
      return false;
    } else {
      //errors.delete("rating-error");
      //refreshErrors();
      return true;
    }
  }

  function displayErrors(errors, container_id = null, input_element = null) {
    /*
    se si passa solo la lista di errors questi vengono mostrati all'inizio del form.
    altrimenti si può passare container_id e input_element
    per specificare esattamente dove mostrare gli errori.
    */

    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errorContainer.setAttribute("role", "alert");
    errorContainer.setAttribute("aria-live", "assertive");
    if(container_id!== null) {
      errorContainer.id = container_id;
    }

    errors.forEach((error) => {
      const errorElement = document.createElement("div");
      errorElement.classList.add("error");
      errorElement.innerHTML = error;
      errorContainer.appendChild(errorElement);
    });
    if (input_element) {
      input_element.parentNode.insertBefore(errorContainer, input_element.nextSibling);
    }
    else {
      form.prepend(errorContainer);
    }
  }

  function refreshErrors() {
    const elements = document.getElementsByTagName("small");
    for (let i = 0; i < elements.length; i++) {
      if (errors.has(elements[i].id)) {
        elements[i].display = "block";
        elements[i].innerHTML = errors.get(elements[i].id);
      } else {
        elements[i].display = "none";
        elements[i].textContent = "";
      }
    }
  }
});
