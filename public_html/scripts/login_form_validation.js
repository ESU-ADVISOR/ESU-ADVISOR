document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".login-form");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  form.addEventListener("submit", function (event) {
    let isValid = true;
    let errors = [];

    document.querySelectorAll(".error").forEach((el) => el.remove());

    const username = usernameInput.value.trim();
    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push("Username must be between 3 and 50 characters long.");
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
      isValid = false;
      errors.push(
        "Username can only contain letters, numbers, underscores, and hyphens.",
      );
    }

    const password = passwordInput.value.trim();
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
