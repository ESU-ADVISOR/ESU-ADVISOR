document.addEventListener("DOMContentLoaded", function () {
  const menseSelect = document.getElementById("mense-select");
  const submitButton = document.querySelector(
    '.mense-section input[type="submit"]',
  );

  if (menseSelect) {
    if (submitButton) {
      submitButton.style.display = "none";
    }

    menseSelect.addEventListener("change", function () {
      const form = document.getElementById("mense-selection");
      if (form) {
        form.submit();
      }
    });
  }
});
