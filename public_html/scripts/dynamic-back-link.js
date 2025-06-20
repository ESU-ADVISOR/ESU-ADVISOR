document.addEventListener("DOMContentLoaded", function () {
  function updateBackLinks() {
    const backLinks = document.querySelectorAll(
      'a[href="index.php"]:not(.bottom-nav a):not(.sidebar a), a[href="./index.php"]:not(.bottom-nav a):not(.sidebar a), a[href="../index.php"]:not(.bottom-nav a):not(.sidebar a)',
    );

    if (backLinks.length === 0) return;

    const urlParams = new URLSearchParams(window.location.search);
    const fromProfile = urlParams.get("from") === "profile";

    if (fromProfile) {
      backLinks.forEach((link) => {
        link.setAttribute("href", "profile.php");

        const linkText = link.textContent.trim();
        if (
          linkText.includes("lista dei piatti") ||
          linkText.includes("lista piatti")
        ) {
          link.innerHTML = link.innerHTML.replace(
            /Torna alla lista dei piatti/gi,
            "Torna al profilo",
          );
          link.innerHTML = link.innerHTML.replace(
            /Torna alla lista piatti/gi,
            "Torna al profilo",
          );
        }

        link.setAttribute("aria-label", "Profile: Torna al profilo utente");
        link.setAttribute("title", "Torna alla pagina del profilo");
      });
    } else {
      const lastSelectedMensa = sessionStorage.getItem("currentMensa");

      backLinks.forEach((link) => {
        if (lastSelectedMensa) {
          const newHref = `index.php?mensa=${encodeURIComponent(lastSelectedMensa)}`;
          link.setAttribute("href", newHref);

          link.setAttribute(
            "aria-label",
            `Home: Torna alla lista dei piatti - Menu di ${lastSelectedMensa}`,
          );
          link.setAttribute("title", `Torna al menu di ${lastSelectedMensa}`);
        } else {
          link.setAttribute("href", "index.php");

          link.setAttribute("aria-label", "Home: Torna alla lista dei piatti");
          link.setAttribute("title", "Torna alla lista dei piatti");
        }
      });
    }
  }

  window.addEventListener("mensaChanged", function (event) {
    if (event.detail && event.detail.mensaId) {
      const urlParams = new URLSearchParams(window.location.search);
      const fromProfile = urlParams.get("from") === "profile";

      if (!fromProfile) {
        updateBackLinks();
      }
    }
  });

  window.addEventListener("storage", function (event) {
    if (event.key === "currentMensa") {
      const urlParams = new URLSearchParams(window.location.search);
      const fromProfile = urlParams.get("from") === "profile";

      if (!fromProfile) {
        updateBackLinks();
      }
    }
  });

  updateBackLinks();

  const observer = new MutationObserver(function (mutations) {
    let shouldUpdate = false;

    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType === Node.ELEMENT_NODE) {
          const hasIndexLinks =
            node.querySelectorAll &&
            node.querySelectorAll('a[href*="index.php"]').length > 0;
          if (
            hasIndexLinks ||
            (node.tagName === "A" &&
              node.href &&
              node.href.includes("index.php"))
          ) {
            shouldUpdate = true;
          }
        }
      });
    });

    if (shouldUpdate) {
      updateBackLinks();
    }
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  window.addEventListener("beforeunload", function () {
    observer.disconnect();
  });
});
