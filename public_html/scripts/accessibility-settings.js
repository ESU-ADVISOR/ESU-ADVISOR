function applyTheme(theme) {
  if (theme === "scuro") {
    document.documentElement.classList.add("theme-dark");
    document.documentElement.classList.remove("theme-light");
  } else if (theme === "chiaro") {
    document.documentElement.classList.remove("theme-dark");
    document.documentElement.classList.add("theme-light");
  } else {
    document.documentElement.classList.remove("theme-dark", "theme-light");
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    if (prefersDark) {
      document.documentElement.classList.add("theme-dark");
    }
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

function applyPreferences() {
  var currentTheme, textSize, fontFamily, iconSize;
  
  if (window.serverPreferences) {
    var prefs = window.serverPreferences;
    
    currentTheme = prefs.theme || "sistema";
    textSize = prefs.textSize || "medio";
    fontFamily = prefs.fontFamily || "normale";
    iconSize = prefs.iconSize || "medio";
  } else {
    currentTheme = "sistema";
    textSize = "medio";
    fontFamily = "normale";
    iconSize = "medio";
  }

  applyTheme(currentTheme);
  applyFontFamily(fontFamily);
  applyTextSize(textSize);
  applyIconSize(iconSize);
}

// Apply preferences as early as possible to avoid theme flash
if (window.serverPreferences) {
  applyPreferences();
}

document.addEventListener("DOMContentLoaded", function () {
  // Optionally re-apply in case preferences change after DOMContentLoaded
  if (window.serverPreferences) {
    applyPreferences();
  }
});