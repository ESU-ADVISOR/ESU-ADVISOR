window
  .matchMedia("(prefers-color-scheme: dark)")
  .addEventListener("change", (event) => {
    console.log("Theme preference has changed to:", event.matches);
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
          const textSizeSelect = document.getElementById("dimensioni-testo-select");
          const fontSelect = document.getElementById("modifica-font-select");
          const iconSizeSelect = document.getElementById("dimensioni-icone-select");

          if (themeSelect) {
            const selectedTheme = themeSelect.value;
            applyThemeChanges(selectedTheme);
          }

          if (textSizeSelect) {
            const selectedTextSize = textSizeSelect.value;
            applyTextSizeChanges(selectedTextSize);
          }

          if (fontSelect) {
            const selectedFont = fontSelect.value;
            applyFontChanges(selectedFont);
          }

          if (iconSizeSelect) {
            const selectedIconSize = iconSizeSelect.value;
            applyIconSizeChanges(selectedIconSize);
          }

        } else {
          showMessage("error", response.message || "Si è verificato un errore.");
        }
      } catch (e) {
        showMessage("error", "Si è verificato un errore: " + e.message);
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

function applyThemeChanges(selectedTheme) {
  if (selectedTheme === "scuro") {
    document.documentElement.classList.add("theme-dark");
    document.documentElement.classList.remove("theme-light");
    if (!window.isLoggedIn) {
      localStorage.setItem("theme", "dark");
    }
  } else if (selectedTheme === "chiaro") {
    document.documentElement.classList.remove("theme-dark");
    document.documentElement.classList.add("theme-light");
    if (!window.isLoggedIn) {
      localStorage.setItem("theme", "light");
    }
  } else {
    document.documentElement.classList.remove("theme-dark", "theme-light");
    if (!window.isLoggedIn) {
      localStorage.removeItem("theme");
    }
    
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    if (prefersDark) {
      document.documentElement.classList.add("theme-dark");
    } else {
      document.documentElement.classList.add("theme-light");
    }
  }
}

function applyTextSizeChanges(selectedTextSize) {
  document.documentElement.classList.remove(
    "text-size-piccolo",
    "text-size-medio",
    "text-size-grande",
  );
  document.documentElement.classList.add("text-size-" + selectedTextSize);
  
  if (!window.isLoggedIn) {
    localStorage.setItem("textSize", selectedTextSize);
  }
}

function applyFontChanges(selectedFont) {
  document.documentElement.classList.remove("font-normale", "font-dislessia");
  document.documentElement.classList.add("font-" + selectedFont);
  
  if (!window.isLoggedIn) {
    localStorage.setItem("fontFamily", selectedFont);
  }
}

function applyIconSizeChanges(selectedIconSize) {
  document.documentElement.classList.remove(
    "icon-size-piccolo",
    "icon-size-medio",
    "icon-size-grande",
  );
  document.documentElement.classList.add("icon-size-" + selectedIconSize);
  
  if (!window.isLoggedIn) {
    localStorage.setItem("iconSize", selectedIconSize);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const themeSelect = document.getElementById("modifica-tema-select");
  const preferencesForm = document.getElementById("preferenze-accessibilità");

  if (preferencesForm) {
    preferencesForm.addEventListener("submit", sendAJAXRequest);
  }

  if (window.serverPreferences && window.isLoggedIn) {
    const serverPrefs = window.serverPreferences;
    
    if (themeSelect && serverPrefs.theme) {
      themeSelect.value = serverPrefs.theme;
      applyThemeChanges(serverPrefs.theme);
    }
    
    const textSizeSelect = document.getElementById("dimensioni-testo-select");
    if (textSizeSelect && serverPrefs.textSize) {
      textSizeSelect.value = serverPrefs.textSize;
      applyTextSizeChanges(serverPrefs.textSize);
    }
    
    const fontSelect = document.getElementById("modifica-font-select");
    if (fontSelect && serverPrefs.fontFamily) {
      fontSelect.value = serverPrefs.fontFamily;
      applyFontChanges(serverPrefs.fontFamily);
    }
    
    const iconSizeSelect = document.getElementById("dimensioni-icone-select");
    if (iconSizeSelect && serverPrefs.iconSize) {
      iconSizeSelect.value = serverPrefs.iconSize;
      applyIconSizeChanges(serverPrefs.iconSize);
    }
  } else {
    const savedTheme = localStorage.getItem("theme");

    if (themeSelect && themeSelect.value != "sistema" && !savedTheme) {
      if (themeSelect.value == "scuro") {
        applyThemeChanges("scuro");
      } else {
        applyThemeChanges("chiaro");
      }
    }
  }

  if (themeSelect) {
    themeSelect.addEventListener("change", function (event) {
      applyThemeChanges(event.target.value);
    });
  }

  const textSizeSelect = document.getElementById("dimensioni-testo-select");
  if (textSizeSelect) {
    textSizeSelect.addEventListener("change", function (event) {
      applyTextSizeChanges(event.target.value);
    });
  }

  const fontSelect = document.getElementById("modifica-font-select");
  if (fontSelect) {
    fontSelect.addEventListener("change", function (event) {
      applyFontChanges(event.target.value);
    });
  }

  const iconSizeSelect = document.getElementById("dimensioni-icone-select");
  if (iconSizeSelect) {
    iconSizeSelect.addEventListener("change", function (event) {
      applyIconSizeChanges(event.target.value);
    });
  }
});