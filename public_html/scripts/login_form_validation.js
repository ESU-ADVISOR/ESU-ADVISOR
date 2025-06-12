document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("login-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());
    document.querySelectorAll(".error-container").forEach((el) => el.remove());

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (!username || !password) {
      isValid = false;
      errors.push("È necesssario compilare tutti i campi.");
      if(!username) {
        passwordInput.focus();
        if(!password) {
          passwordInput.focus();
        }
      }
    }

    if (!isValid) {
      event.preventDefault();
      displayErrors(errors);
    }
  });

  function displayErrors(errors) {
    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errorContainer.setAttribute("role", "alert");
    errorContainer.setAttribute("aria-live", "assertive");
    errors.forEach((error) => {
      const errorElement = document.createElement("div");
      errorElement.classList.add("error");
      errorElement.innerHTML = error;
      errorContainer.appendChild(errorElement);
    });
    form.prepend(errorContainer);
  }
});