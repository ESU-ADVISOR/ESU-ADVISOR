document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("register-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");
  const dataInput = document.getElementById("birth_date");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());
    document.querySelectorAll(".error-container").forEach((el) => el.remove());

    const username = usernameInput.value.trim();
    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push("L'<span lang=\"en\">username</span> deve essere compreso tra 3 e 50 caratteri.");
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
      isValid = false;
      errors.push(
        "L'<span lang=\"en\">username</span> può contenere solo lettere, numeri, <span lang=\"en\">underscore</span> e trattini."
      );
    }

    const data = dataInput.value.trim();
    if (!data) {
      isValid = false;
      errors.push("È necessaria la data di nascita.");
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(data)) {
      isValid = false;
      errors.push("Per favore inserisci una data di nascita valida.");
    }

    const password = passwordInput.value.trim();
    if (password.length < 8) {
      isValid = false;
      errors.push("La <span lang=\"en\">password</span> deve essere di almeno 8 caratteri.");
    }
    if (
      !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
        password
      )
    ) {
      isValid = false;
      errors.push(
        "La <span lang=\"en\">password</span> deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&)."
      );
    }

    const confirmPassword = confirmPasswordInput.value.trim();
    if (!confirmPassword) {
      isValid = false;
      errors.push("È necessario confermare la <span lang=\"en\">password</span>.");
    }
    if (confirmPassword !== password) {
      isValid = false;
      errors.push("Le <span lang=\"en\">password</span> non corrispondono.");
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
      errorElement.innerHTML = error;
      errorContainer.appendChild(errorElement);
    });
    form.prepend(errorContainer);
  }
});