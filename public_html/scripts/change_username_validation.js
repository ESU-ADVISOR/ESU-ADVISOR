document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_username_form");
  const usernameInput = document.getElementById("new_username");

  if (!form) return;

  usernameInput.addEventListener("blur", function () {
    validateUsername();
  });

  form.addEventListener("submit", function (event) {
    if (!validateUsername()) {
      event.preventDefault();
    }
  });

  function validateUsername() {
    let isValid = true;
    let errors = [];

    const username = usernameInput.value.trim();
    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push(
        "Lo <span lang='en'>username</span> deve essere compreso tra 3 e 50 caratteri.",
      );
    }
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
      isValid = false;
      errors.push(
        "Lo <span lang='en'>username</span> può contenere solo lettere, numeri e <span lang='en'>underscore</span>.",
      );
    }

    displayErrors(errors, "username-error-container", usernameInput);
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
});
