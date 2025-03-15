window
  .matchMedia("(prefers-color-scheme: dark)")
  .addEventListener("change", (event) => {
    console.log("THeme preference has changed to:", event.matches);
    if (
      !localStorage.getItem("theme") ||
      localStorage.getItem("theme") === "system"
    ) {
      document.documentElement.classList.toggle("theme-dark", event.matches);
      document.documentElement.classList.toggle("theme-light", !event.matches);
    }
  });

function showMessage(type, message) {
  const serverResponseContainer =
    document.getElementById("server-response-template") ||
    document.getElementById("server-response");

  const messageDiv = document.createElement("div");
  messageDiv.className = type;
  messageDiv.id = "server-response";
  messageDiv.textContent = message;

  serverResponseContainer.replaceWith(messageDiv);
}

function sendAJAXRequest(event) {
  event.preventDefault();

  const formData = new FormData(event.target);

  formData.append("preferences", "1");

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "settings.php", true);
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

  xhr.onload = function () {
    if (xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText);

        if (response.success) {
          showMessage("success", response.message);

          const themeSelect = document.getElementById("modifica-tema-select");
          const selectedTheme = themeSelect.value;

          if (selectedTheme === "scuro") {
            localStorage.setItem("theme", "dark");
          } else if (selectedTheme === "chiaro") {
            localStorage.setItem("theme", "light");
          } else {
            localStorage.removeItem("theme");
          }
        } else {
          showMessage(
            "error",
            response.message || "Si è verificato un errore.",
          );
        }
      } catch (e) {
        showMessage("error", "Si è verificato un errore sconosciuto");
      }
    } else {
      showMessage("error", "Richiesta fallita. Riprova più tardi.");
    }
  };

  xhr.onerror = function () {
    showMessage("error", "Richiesta fallita. Controlla la tua connessione.");
  };

  xhr.send(formData);
}

document.addEventListener("DOMContentLoaded", function () {
  const themeSelect = document.getElementById("modifica-tema-select");
  const preferencesForm = document.getElementById("preferenze-accessibilità");

  if (preferencesForm) {
    preferencesForm.addEventListener("submit", sendAJAXRequest);
  }
  const savedTheme = localStorage.getItem("theme");

  // L'utente non ha il cookie ma ha una preferenza nel db e bisogna riassegnare il cookie
  if (themeSelect.value != "sistema" && !savedTheme) {
    if (themeSelect.value == "scuro") {
      document.documentElement.classList.add("theme-dark");
      document.documentElement.classList.remove("theme-light");
      localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.add("theme-light");
      document.documentElement.classList.remove("theme-dark");
      localStorage.setItem("theme", "light");
    }
  }

  themeSelect.addEventListener("change", function (event) {
    const prefersDark = window.matchMedia(
      "(prefers-color-scheme: dark)",
    ).matches;

    if (event.target.value == "scuro") {
      document.documentElement.classList.add("theme-dark");
      document.documentElement.classList.remove("theme-light");
    } else if (event.target.value == "chiaro") {
      document.documentElement.classList.remove("theme-dark");
      document.documentElement.classList.add("theme-light");
    } else {
      if (prefersDark) {
        document.documentElement.classList.add("theme-dark");
        document.documentElement.classList.remove("theme-light");
      } else {
        document.documentElement.classList.add("theme-light");
        document.documentElement.classList.remove("theme-dark");
      }
    }
  });
});
