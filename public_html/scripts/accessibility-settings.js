(function () {
  var currentTheme, textSize, fontFamily, iconSize;

  function applyTheme(theme) {
    if (theme === "scuro") {
      document.documentElement.classList.add("theme-dark");
      document.documentElement.classList.remove("theme-light");
    } else if (theme === "chiaro") {
      document.documentElement.classList.remove("theme-dark");
      document.documentElement.classList.add("theme-light");
    } else {
      document.documentElement.classList.remove("theme-dark", "theme-light");
    }
  }

  function applyTextSize(size) {
    document.documentElement.classList.remove(
      "text-size-piccolo",
      "text-size-medio",
      "text-size-grande",
    );
    if (size && ["piccolo", "medio", "grande"].includes(size)) {
      document.documentElement.classList.add("text-size-" + size);
    } else {
      document.documentElement.classList.add("text-size-medio");
    }
  }

  function applyFontFamily(font) {
    document.documentElement.classList.remove("font-normale", "font-dislessia");
    if (font && ["normale", "dislessia"].includes(font)) {
      document.documentElement.classList.add("font-" + font);
    } else {
      document.documentElement.classList.add("font-normale");
    }
  }

  function applyIconSize(size) {
    document.documentElement.classList.remove(
      "icon-size-piccolo",
      "icon-size-medio",
      "icon-size-grande",
    );
    if (size && ["piccolo", "medio", "grande"].includes(size)) {
      document.documentElement.classList.add("icon-size-" + size);
    } else {
      document.documentElement.classList.add("icon-size-medio");
    }
  }

  function syncLocalStorageFromServer() {
    if (window.serverPreferences) {
      var serverPrefs = window.serverPreferences;
      
      if (serverPrefs.theme === "scuro") {
        localStorage.setItem("theme", "dark");
      } else if (serverPrefs.theme === "chiaro") {
        localStorage.setItem("theme", "light");
      } else {
        localStorage.removeItem("theme");
      }

      localStorage.setItem("textSize", serverPrefs.textSize || "medio");
      localStorage.setItem("fontFamily", serverPrefs.fontFamily || "normale");
      localStorage.setItem("iconSize", serverPrefs.iconSize || "medio");
    }
  }

  function clearLocalStoragePreferences() {
    localStorage.removeItem("theme");
    localStorage.removeItem("textSize");
    localStorage.removeItem("fontFamily");
    localStorage.removeItem("iconSize");
  }

  function applyPreferences() {
    var useServerPrefs = window.serverPreferences && window.isLoggedIn;
    
    if (useServerPrefs) {
      syncLocalStorageFromServer();
      var prefs = window.serverPreferences;
      
      currentTheme = prefs.theme || "sistema";
      textSize = prefs.textSize || "medio";
      fontFamily = prefs.fontFamily || "normale";
      iconSize = prefs.iconSize || "medio";
    } else {
      try {
        currentTheme = localStorage.getItem("theme") || "system";
        if (currentTheme !== "system") {
          if (currentTheme === "dark") currentTheme = "scuro";
          if (currentTheme === "light") currentTheme = "chiaro";
        } else {
          currentTheme = "sistema";
        }
        
        textSize = localStorage.getItem("textSize") || "medio";
        fontFamily = localStorage.getItem("fontFamily") || "normale";
        iconSize = localStorage.getItem("iconSize") || "medio";
      } catch (err) {
        currentTheme = "sistema";
        textSize = "medio";
        fontFamily = "normale";
        iconSize = "medio";
        console.log(new Error("accessing preferences has been denied"));
      }
    }

    if (currentTheme !== "sistema") {
      applyTheme(currentTheme);
    }
    applyFontFamily(fontFamily);
    applyTextSize(textSize);
    applyIconSize(iconSize);
  }

  applyPreferences();

  window.forcePreferencesReload = function() {
    applyPreferences();
  };

  if (window.forcePreferencesReload === true) {
    setTimeout(function() {
      applyPreferences();
    }, 100);
  }

  if (window.isLoggedIn === false) {
    window.addEventListener('beforeunload', function() {
      clearLocalStoragePreferences();
    });
  }
})();

document.addEventListener("DOMContentLoaded", function () {
  const logoutButton = document.getElementById("logout");
  const sidebarLogoutButton = document.getElementById("sidebar-logout");

  function handleLogout() {
    localStorage.removeItem("theme");
    localStorage.removeItem("textSize");
    localStorage.removeItem("fontFamily");
    localStorage.removeItem("iconSize");
  }

  if (logoutButton) {
    logoutButton.addEventListener("click", handleLogout);
  }

  if (sidebarLogoutButton) {
    sidebarLogoutButton.addEventListener("click", handleLogout);
  }
});