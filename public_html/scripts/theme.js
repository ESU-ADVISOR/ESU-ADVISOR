(function () {
  var currentTheme;

  function changeTheme(inputTheme) {
    if (inputTheme === "dark") {
      document.documentElement.classList.add("theme-dark");
      document.documentElement.classList.remove("theme-light");
      localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.remove("theme-dark");
      document.documentElement.classList.add("theme-light");
      localStorage.setItem("theme", "light");
    }
  }
  try {
    currentTheme = localStorage.getItem("theme") || "system";
    if (currentTheme !== "system") {
      changeTheme(currentTheme);
    }
  } catch (err) {
    console.log(new Error("accessing theme has been denied"));
  }
})();
