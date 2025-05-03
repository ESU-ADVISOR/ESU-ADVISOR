document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("login-form");
  const usernameInput = document.getElementById("username");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());

    const username = usernameInput.value.trim();
    if (!username) {
      isValid = false;
      errors.push("Username obbligatorio.");
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