document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_password_form");
  const currentPasswordInput = document.getElementById("current_password");
  const passwordInput = document.getElementById("new_password");
  const confirmPasswordInput = document.getElementById("new_password_confirm");
  
  let current_password = currentPasswordInput.value.trim();
  let password = passwordInput.value.trim();
  let confirm_password = confirmPasswordInput.value.trim();

  if (!form) return;

  function toggleInstructions() {
    const instructions = document.getElementById("new-password-instructions");
    if (instructions) {
      if (passwordInput.value.trim()) {
        instructions.setAttribute('aria-hidden', 'true');
        instructions.classList.add('hidden');
      } else {
        instructions.removeAttribute('aria-hidden');
        instructions.classList.remove('hidden');
      }
    }
  }

  function validate_password(){
    current_password = currentPasswordInput.value.trim();
    password = passwordInput.value.trim();
    let errors = [];
    let isValid = true;

    document.querySelectorAll(".password-error").forEach((el) => el.remove());
    document.getElementById("password-error-container")?.remove();

    if (password.length < 8) {
      isValid = false;
      errors.push("La <span lang=\"en\">password</span> deve essere di almeno 8 caratteri.");
    }
    if (
      !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(
        password,
      )
    ) {
      isValid = false;
      errors.push(
        "La <span lang=\"en\">password</span> deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&).",
      );
    }
    if(password === current_password){
      isValid = false;
      errors.push("La nuova <span lang=\"en\">password</span> deve essere diversa da quella attuale.");
    }

    if (!isValid) {
      displayErrors(errors, "password-error-container", passwordInput);
    }
  }

  function validate_confirm_password(){
    confirm_password = confirmPasswordInput.value.trim();
    password = passwordInput.value.trim();
    let errors = [];
    let isValid = true;

    document.querySelectorAll(".confirm-password-error").forEach((el) => el.remove());
    document.getElementById("confirm-password-error-container")?.remove();

    
    if (password !== confirm_password) {
      isValid = false;
      errors.push("Le nuove <span lang=\"en\">password</span> non corrispondono.");
    }

    if(!isValid){
      displayErrors(errors, "confirm-passowrd-error-container", confirmPasswordInput);
    }

  }

  passwordInput.addEventListener("blur", validate_password);
  confirmPasswordInput.addEventListener("blur",validate_confirm_password)
  passwordInput.addEventListener("blur", toggleInstructions);

  form.addEventListener("submit", function (event) {
    document.querySelectorAll(".error").forEach((el) => el.remove());
    document.querySelectorAll(".error-container").forEach((el) => el.remove());

    if( !validate_password || !validate_confirm_password){
      event.preventDefault();
    }
  });

  function displayErrors(errors, container_id = null, input_element = null) {
    /*
    se si passa solo la lista di errors questi vengono mostrati all'inizio del form.
    altrimenti si può passare container_id e input_element
    per specificare esattamente dove mostrare gli errori.
    */
    document.querySelectorAll(".error-container").forEach((el) => el.remove());

    const errorContainer = document.createElement("div");
    errorContainer.classList.add("error-container");
    errorContainer.setAttribute("role", "alert");
    errorContainer.setAttribute("aria-live", "assertive");
    if(container_id!== null) {
      errorContainer.id = container_id;
    }

    errors.forEach((error) => {
      const errorElement = document.createElement("div");
      errorElement.classList.add("error");
      errorElement.innerHTML = error;
      errorContainer.appendChild(errorElement);
    });
    if (input_element) {
      input_element.parentNode.insertBefore(errorContainer, input_element.nextSibling);
    }
    else {
      form.prepend(errorContainer);
    }
  }
});
