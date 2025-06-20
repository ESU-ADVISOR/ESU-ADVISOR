document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("delete-account-form");
  if (!form) {
    return;
  }
  form.addEventListener("submit", function (event) {
    if (
      confirm(
        "Sei sicuro di voler eliminare il tuo account? Questa azione è irreversibile.",
      )
    ) {
      const form = document.getElementById("delete-account-form");

      if (form) {
        form.submit();
      } else {
        console.error(
          "Impossibile trovare il form per l'eliminazione dell'account",
        );
      }
    } else {
      event.preventDefault();
    }
  });
});
