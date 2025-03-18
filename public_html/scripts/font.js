(function () {
  var currentTheme, textSize, fontFamily, iconSize;

  // Apply text size preference
  function applyTextSize(size) {
    document.documentElement.classList.remove(
      "text-size-piccolo",
      "text-size-medio",
      "text-size-grande",
    );
    if (size && ["piccolo", "medio", "grande"].includes(size)) {
      document.documentElement.classList.add("text-size-" + size);
    } else {
      document.documentElement.classList.add("text-size-medio"); // Default
    }
  }

  // Apply font family preference
  function applyFontFamily(font) {
    document.documentElement.classList.remove("font-normale", "font-dislessia");
    if (font && ["normale", "dislessia"].includes(font)) {
      document.documentElement.classList.add("font-" + font);
    } else {
      document.documentElement.classList.add("font-normale"); // Default
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
      document.documentElement.classList.add("icon-size-medio"); // Default
    }
  }

  try {
    // Apply font family
    fontFamily = localStorage.getItem("fontFamily") || "normale";
    applyFontFamily(fontFamily);
    // Apply text size
    textSize = localStorage.getItem("textSize") || "medio";
    applyTextSize(textSize);
    // Apply icon size
    iconSize = localStorage.getItem("iconSize") || "medio";
    applyIconSize(iconSize);
  } catch (err) {
    console.log(new Error("accessing preferences has been denied"));
  }
})();
