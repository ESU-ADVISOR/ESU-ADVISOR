document.addEventListener("DOMContentLoaded", function () {
  const piattoInput = document.getElementById("piatto");
  const mensaInput = document.getElementById("mensa");
  const reviewInput = document.getElementById("review");
  const ratingContainer = document.getElementById("rating-container");
  const form = document.getElementById("review-form");

  mensaInput.addEventListener("blur", updateDatalist);
  mensaInput.addEventListener("blur", validateMensa);
  piattoInput.addEventListener("blur", validatePiatto);
  reviewInput.addEventListener("blur", validateReview);
  ratingContainer.addEventListener("change", validateRating);

  handlePrefilledValues();

  form.addEventListener("submit", function (event) {
    let isValid = true;

    const fieldsToCheck = [
      { check: validateMensa, input: mensaInput },
      { check: validatePiatto, input: piattoInput },
      { check: validateReview, input: reviewInput },
      { check: validateRating, input: ratingContainer },
    ];

    let firstInvalidField = null;

    fieldsToCheck.forEach((field) => {
      if (!field.check()) {
        isValid = false;
        if (!firstInvalidField) {
          firstInvalidField = field.input;
        }
      }
    });

    if (!isValid) {
      event.preventDefault();
      if (firstInvalidField) {
        firstInvalidField.focus();
      }
    }
  });

  function validateReview() {
    let errors = [];
    let isValid = true;

    const reviewInput = document.getElementById("review");
    const review = reviewInput.value.trim();

    if (!review) {
      errors.push("Per favore, inserisci una recensione.");
      isValid = false;
    }

    displayErrors(errors, "review-error-container", reviewInput);
    return isValid;
  }

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
    let errors = [];
    const mensaInput = document.getElementById("mensa");

    const mensa = mensaInput.value;
    if (mensa === "") {
      errors.push("Per favore, inserisci una mensa.");
      displayErrors(errors, "mensa-error-container", mensaInput);
      return false;
    }

    const mensaList = document.getElementById("mensa");
    const options = mensaList.getElementsByTagName("option");

    for (let i = 0; i < options.length; i++) {
      if (options[i].value.toLowerCase() === mensa.trim().toLowerCase()) {
        displayErrors(errors, "mensa-error-container", mensaInput);
        return true;
      }
    }

    errors.push("La mensa non esiste nel <span lang='en'>database</span>");
    displayErrors(errors, "mensa-error-container", mensaInput);
    return false;
  }

  function validatePiatto() {
    let errors = [];
    const piattoInput = document.getElementById("piatto");

    const listaPiatti = document.getElementById("suggerimenti-piatti");
    if (!listaPiatti) return;

    const options = listaPiatti.getElementsByTagName("option");

    for (let i = 0; i < options.length; i++) {
      if (
        options[i].value.toLowerCase() ===
        piattoInput.value.trim().toLowerCase()
      ) {
        displayErrors(errors, "piatto-error-container", piattoInput);
        return true;
      }
    }

    errors.push("Il piatto non esiste nel <span lang='en'>database</span>");
    displayErrors(errors, "piatto-error-container", piattoInput);
    return false;
  }

  function validateRating() {
    let errors = [];
    let isValid = true;

    let ratingSelected = false;

    const ratingInputs = ratingContainer.querySelectorAll('[name="rating"]');

    for (let i = 0; i < ratingInputs.length; i++) {
      if (ratingInputs[i].checked) {
        ratingSelected = true;
        break;
      }
    }

    if (!ratingSelected) {
      errors.push(
        "Per favore, seleziona almeno una stella per la valutazione.",
      );
      isValid = false;
    }

    displayErrors(errors, "rating-error-container", ratingContainer);
    return isValid;
  }

  function displayErrors(errors, container_id = null, input_element = null) {
    document.getElementById(container_id)?.remove();

    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errorContainer.setAttribute("role", "alert");
    errorContainer.setAttribute("aria-live", "assertive");
    if (errors.length === 0) {
      return;
    }
    if (container_id !== null) {
      errorContainer.id = container_id;
    }
    errors.forEach((error) => {
      const errorElement = document.createElement("div");
      errorElement.classList.add("error");
      errorElement.innerHTML = error;
      errorContainer.appendChild(errorElement);
    });
    if (input_element) {
      input_element.parentNode.insertBefore(
        errorContainer,
        input_element.nextSibling,
      );
    } else {
      form.prepend(errorContainer);
    }
  }

  function handlePrefilledValues() {
    const body = document.body;
    const prefilledMensa = body.getAttribute("data-prefilled-mensa");
    const prefilledPiatto = body.getAttribute("data-prefilled-piatto");

    if (prefilledMensa) {
      mensaInput.value = prefilledMensa;
      updateDatalist();
    }

    if (prefilledPiatto) {
      piattoInput.value = prefilledPiatto;
    }
  }
});
