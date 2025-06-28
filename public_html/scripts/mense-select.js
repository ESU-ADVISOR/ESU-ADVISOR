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

  const menseInfoSelect = document.getElementById("mense-info-select");
  const submitButtonInfo = document.querySelector(
    '.mense-section input[type="submit"]',
  );

  if (menseInfoSelect) {
    if (submitButtonInfo) {
      submitButtonInfo.style.display = "none";
    }

    menseInfoSelect.addEventListener("change", function () {
      const form = document.getElementById("mense-info-selection");
      if (form) {
        form.submit();
      }
    });
  }
});
