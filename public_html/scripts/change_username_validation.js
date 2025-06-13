document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("change_username_form");
  const usernameInput = document.getElementById("new_username");

  if (!form) return;

  function validate_username(){
    let errors = [];
    let isValid = true;

    
    //document.querySelectorAll(".error").forEach((el) => el.remove());
    document.getElementById("username-error-container")?.remove();

    const username = usernameInput.value.trim();
    if (username.length < 3 || username.length > 50) {
      isValid = false;
      errors.push("Lo <span lang='en'>username</span> deve essere compreso tra 3 e 50 caratteri.");
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
      isValid = false;
      errors.push(
        "Lo <span lang='en'>username</span> può contenere solo lettere, numeri, <span lang='en'>underscore</span> e trattini.",
      );
    }

    if (!isValid) {
      displayErrors(errors, "username-error-container", usernameInput);
    }

    return isValid;

  }

  function toggleInstructions() {
    const instructions = document.getElementById("username-instructions");
    if (instructions) {
      if (usernameInput.value.trim()) {
        instructions.setAttribute('aria-hidden', 'true');
        instructions.classList.add('hidden');
      } else {
        instructions.removeAttribute('aria-hidden');
        instructions.classList.remove('hidden');
      }
    }
  }

  usernameInput.addEventListener("blur", validate_username);
  usernameInput.addEventListener("blur", toggleInstructions);

  form.addEventListener("submit", function (event) {
    
    let isValid = validate_username();

    if(!isValid){
      usernameInput.focus();
      event.preventDefault();
    }
    
  });

  function displayErrors(errors, container_id = null, input_element = null) {
    /*
    se si passa solo la lista di errors questi vengono mostrati all'inizio del form.
    altrimenti si può passare container_id e input_element
    per specificare esattamente dove mostrare gli errori.
    */
    // Remove existing error containers
    document.querySelectorAll(".error").forEach((el) => el.remove());

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
