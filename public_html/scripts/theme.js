(function () {
  var currentTheme;

  try {
    currentTheme = localStorage.getItem("theme") || "system";
    if (currentTheme !== "system") {
      applyTheme(currentTheme);
    }
  } catch (err) {
    console.log(new Error("accessing theme has been denied"));
  }
})();
