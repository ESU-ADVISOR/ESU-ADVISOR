document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("register-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

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
        "L'username può contenere solo lettere, numeri, underscore e trattini."
      );
    }

    const password = passwordInput.value.trim();
    if (password.length < 8) {
      isValid = false;
      errors.push("La password deve essere di almeno 8 caratteri.");
    }
    if (
      !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
        password
      )
    ) {
      isValid = false;
      errors.push(
        "La password deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&)."
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