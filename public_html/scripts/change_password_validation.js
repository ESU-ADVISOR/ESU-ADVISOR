document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_password_form");
  if (!form) {return;}
  const currentPasswordInput = document.getElementById("current_password");
  const passwordInput = document.getElementById("new_password");
  const confirmPasswordInput = document.getElementById("new_password_confirm");

  passwordInput.addEventListener("blur", validatePassword);
  confirmPasswordInput.addEventListener("blur", validateConfirmPassword);

  function validatePassword() {
    //diversa da current password
    //rispetta le regole della password
    let isValid = true;
    let errors = [];

    const currentPassword = currentPasswordInput.value.trim();
    const password = passwordInput.value.trim();

    if (currentPassword === password) {
      isValid = false;
      errors.push(
        'La nuova <span lang="en">password</span> deve essere diversa da quella attuale.',
      );
    }
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

    displayErrors(errors, "password-error-container", passwordInput);
    return isValid;
  }

  function validateConfirmPassword() {
    let isValid = true;
    let errors = [];
    const confirmPassword = confirmPasswordInput.value.trim();
    const password = passwordInput.value.trim();

    if (password !== confirmPassword) {
      isValid = false;
      errors.push(
        'Le nuove <span lang="en">password</span> non corrispondono.',
      );
    }

    displayErrors(
      errors,
      "confirm-password-error-container",
      confirmPasswordInput,
    );
    return isValid;
  }

  form.addEventListener("submit", function (event) {
    document.querySelectorAll(".error-container").forEach((el) => el.remove());

    let isValid = true;

    const fieldsToCheck = [
      { check: validatePassword, input: passwordInput },
      { check: validateConfirmPassword, input: confirmPasswordInput },
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

  function displayErrors(errors, container_id = null, input_element = null) {
    /*
    se si passa solo la lista di errors questi vengono mostrati all'inizio del form.
    altrimenti si può passare container_id e input_element
    per specificare esattamente dove mostrare gli errori.
    */
    if (container_id !== null) {
      document.getElementById(container_id)?.remove();
    }

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
