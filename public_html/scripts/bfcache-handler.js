// Gestisce il back/forward cache
(function () {
  const logoutButton = document.getElementById("logout");
  const isLoggedIn = !!logoutButton;

  sessionStorage.setItem("userLoggedIn", isLoggedIn);

  document.addEventListener("pageshow", function (event) {
    // Check if the page was loaded from bfcache
    if (event.persisted) {
      console.log("Page restored from bfcache");

      const previousLoginStatus =
        sessionStorage.getItem("userLoggedIn") === "true";

      fetch(`session-check.php?status=${previousLoginStatus}`, {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
        credentials: "same-origin",
      })
        .then((response) => response.json())
        .then((data) => {
          sessionStorage.setItem("userLoggedIn", data.logged_in);

          if (data.reload) {
            window.location.reload();
          }
        })
        .catch((error) => {
          console.error("Session check failed:", error);
        });
    }
  });
})();