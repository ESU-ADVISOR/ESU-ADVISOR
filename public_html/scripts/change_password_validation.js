document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_password_form");
  const currentPasswordInput = document.getElementById("current_password");
  const passwordInput = document.getElementById("new_password");
  const confirmPasswordInput = document.getElementById("new_password_confirm");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());

    const currentPassword = currentPasswordInput.value.trim();
    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    if (password !== confirmPassword) {
      isValid = false;
      errors.push("Passwords do not match.");
    }
    if (currentPassword === password) {
      isValid = false;
      errors.push("New password must be different from the current password.");
    }
    if (password.length < 8) {
      isValid = false;
      errors.push("Password must be at least 8 characters long.");
    }
    if (
      !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
        password,
      )
    ) {
      isValid = false;
      errors.push(
        "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).",
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
