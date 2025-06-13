document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("register-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm_password");
  const dataInput = document.getElementById("birth_date");

  usernameInput.addEventListener("blur", verifica_username);

  function verifica_username() {
    let isValid = true;
    let errors = [];
    document.querySelectorAll(".username-error").forEach((el) => el.remove());
    document.getElementById("username-error-container")?.remove();
    const username = usernameInput.value.trim();

    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push(
        'Lo <span lang="en">username</span> deve essere compreso tra 3 e 50 caratteri.',
      );
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
      isValid = false;
      errors.push(
        'Lo <span lang="en">username</span> può contenere solo lettere, numeri, <span lang="en">underscore</span> e trattini.',
      );
    }

    if (!isValid) {
      displayErrors(errors, "username-error-container", usernameInput);
    }
    return isValid;
  }

  passwordInput.addEventListener("blur", verifica_password);

  function verifica_password() {
    let isValid = true;
    let errors = [];
    document.querySelectorAll(".password-error").forEach((el) => el.remove());
    document.getElementById("password-error-container")?.remove();
    const password = passwordInput.value.trim();

    if (password.length < 8) {
      isValid = false;
      errors.push(
        'La <span lang="en">password</span> deve essere di almeno 8 caratteri.',
      );
    }
    if (
      !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
        password,
      )
    ) {
      isValid = false;
      errors.push(
        'La <span lang="en">password</span> deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&).',
      );
    }

    if (!isValid) {
      displayErrors(errors, "password-error-container", passwordInput);
    }
    return isValid;
  }

  confirmPasswordInput.addEventListener("blur", verifica_confirm_password);

  function verifica_confirm_password() {
    let isValid = true;
    let errors = [];
    document
      .querySelectorAll(".confirm-password-error")
      .forEach((el) => el.remove());
    document.getElementById("confirm-password-error-container")?.remove();
    const confirmPassword = confirmPasswordInput.value.trim();
    const password = passwordInput.value.trim();

    if (!confirmPassword) {
      isValid = false;
      errors.push(
        'È necessario confermare la <span lang="en">password</span>.',
      );
    }
    if (confirmPassword !== password) {
      isValid = false;
      errors.push('Le <span lang="en">password</span> non corrispondono.');
    }

    if (!isValid) {
      displayErrors(
        errors,
        "confirm-password-error-container",
        confirmPasswordInput,
      );
    }
    return isValid;
  }

  dataInput.addEventListener("blur", verifica_data);

  function verifica_data() {
    let isValid = true;
    let errors = [];
    document.querySelectorAll(".data-error").forEach((el) => el.remove());
    document.getElementById("data-error-container")?.remove();
    const data = dataInput.value.trim();

    if (!data) {
      isValid = false;
      errors.push("È necessaria la data di nascita.");
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(data)) {
      isValid = false;
      errors.push("Per favore inserisci una data di nascita valida.");
    }

    if (data && /^\d{4}-\d{2}-\d{2}$/.test(data)) {
      const birthDate = new Date(data);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (birthDate > today) {
        isValid = false;
        errors.push("La data di nascita non può essere nel futuro.");
      }
    }

    if (!isValid) {
      displayErrors(errors, "data-error-container", dataInput);
    }
    return isValid;
  }

  form.addEventListener("submit", function (event) {
    let isValid = true;

    const fieldsToCheck = [
      { check: verifica_username, input: usernameInput },
      { check: verifica_data, input: dataInput },
      { check: verifica_password, input: passwordInput },
      { check: verifica_confirm_password, input: confirmPasswordInput },
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

  function displayErrors(errors, container_id = null, input_element = null) {
    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errorContainer.setAttribute("role", "alert");
    errorContainer.setAttribute("aria-live", "assertive");
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
