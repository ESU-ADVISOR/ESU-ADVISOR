document.addEventListener("DOMContentLoaded", function () {
  const menseSelect = document.getElementById("mense-select");
  const searchInput = document.getElementById("search-piatti");
  const resultsCount = document.getElementById("search-results-count");

  if (!menseSelect) return;

  function updateSEO(mensaName) {
    if (!mensaName) return;

    document.title = `Menu ${mensaName} - Mense Universitarie Padova | ESU Advisor`;

    const metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
      metaDescription.setAttribute(
        "content",
        `Consulta il menu di oggi di ${mensaName} a Padova. Scopri piatti, orari, recensioni e allergeni della mensa universitaria ESU.`,
      );
    }

    const metaKeywords = document.querySelector('meta[name="keywords"]');
    if (metaKeywords) {
      metaKeywords.setAttribute(
        "content",
        `${mensaName}, menu ${mensaName}, mensa ${mensaName}, orari ${mensaName}, ESU Padova, mense universitarie padova`,
      );
    }
  }

  function showMensaPiatti(mensaId) {
    if (searchInput) {
      searchInput.value = "";
    }
    if (resultsCount) {
      resultsCount.textContent = "";
    }

    const allElements = document.querySelectorAll("[data-mensa-id]");
    allElements.forEach((element) => {
      element.style.display = "none";
    });

    const currentMensaElements = document.querySelectorAll(
      `[data-mensa-id="${mensaId}"]`,
    );
    currentMensaElements.forEach((element) => {
      element.style.display = "";
    });
    updateSEO(mensaId);

    const url = new URL(window.location);
    url.searchParams.set("mensa", mensaId);

    const newTitle = `Menu ${mensaId} - ESU Advisor`;
    window.history.replaceState({ mensa: mensaId }, newTitle, url);

    window.dispatchEvent(
      new CustomEvent("mensaChanged", {
        detail: {
          mensaId: mensaId,
          mensaName: mensaId,
        },
      }),
    );
  }

  menseSelect.addEventListener("change", function () {
    const selectedMensa = this.value;
    if (selectedMensa) {
      sessionStorage.setItem("currentMensa", selectedMensa);

      showMensaPiatti(selectedMensa);

      const announcement = document.createElement("div");
      announcement.setAttribute("aria-live", "polite");
      announcement.setAttribute("aria-atomic", "true");
      announcement.className = "sr-only";
      announcement.textContent = `Menu aggiornato per ${selectedMensa}`;
      document.body.appendChild(announcement);

      setTimeout(() => {
        document.body.removeChild(announcement);
      }, 1000);
    }
  });

  const initialMensa = menseSelect.value;
  if (initialMensa) {
    sessionStorage.setItem("currentMensa", initialMensa);
    showMensaPiatti(initialMensa);
  }

  window.addEventListener("popstate", function (event) {
    const urlParams = new URLSearchParams(window.location.search);
    const mensaFromUrl = urlParams.get("mensa");

    if (mensaFromUrl && menseSelect.value !== mensaFromUrl) {
      menseSelect.value = mensaFromUrl;
      sessionStorage.setItem("currentMensa", mensaFromUrl);
      showMensaPiatti(mensaFromUrl);
    } else if (event.state && event.state.mensa) {
      menseSelect.value = event.state.mensa;
      sessionStorage.setItem("currentMensa", event.state.mensa);
      showMensaPiatti(event.state.mensa);
    }
  });

  menseSelect.addEventListener("focus", function () {
    this.setAttribute("aria-expanded", "true");
  });

  menseSelect.addEventListener("blur", function () {
    this.setAttribute("aria-expanded", "false");
  });
});
