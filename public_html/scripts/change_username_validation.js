document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_username_form");
  const usernameInput = document.getElementById("new_username");

  if (!form) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());

    const username = usernameInput.value.trim();
    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push("L'username deve essere compreso tra 3 e 50 caratteri.");
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
      isValid = false;
      errors.push(
        "L'username può contenere solo lettere, numeri, underscore e trattini.",
      );
    }

    if (!isValid) {
      event.preventDefault();
      displayErrors(errors);
    }
  });

  function displayErrors(errors) {
    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errors.forEach((error) => {
      const errorElement = document.createElement("div");
      errorElement.classList.add("error");
      errorElement.textContent = error;
      errorContainer.appendChild(errorElement);
    });
    form.prepend(errorContainer);
  }
});
