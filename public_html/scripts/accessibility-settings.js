(function () {
  var currentTheme, textSize, fontFamily, iconSize;

  function applyTheme(theme) {
    if (theme === "dark") {
      document.documentElement.classList.add("theme-dark");
      document.documentElement.classList.remove("theme-light");
      localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.remove("theme-dark");
      document.documentElement.classList.add("theme-light");
      localStorage.setItem("theme", "light");
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

  try {
    theme = localStorage.getItem("theme") || "system";
    if (theme !== "system") {
      applyTheme(theme);
    }

    fontFamily = localStorage.getItem("fontFamily") || "normale";
    applyFontFamily(fontFamily);

    textSize = localStorage.getItem("textSize") || "medio";
    applyTextSize(textSize);

    iconSize = localStorage.getItem("iconSize") || "medio";
    applyIconSize(iconSize);
  } catch (err) {
    console.log(new Error("accessing preferences has been denied"));
  }
})();

document.addEventListener("DOMContentLoaded", function () {
  const logoutButton = document.getElementById("logout");

  if (logoutButton) {
    logoutButton.addEventListener("click", function () {
      localStorage.clear();
    });
  }
});
