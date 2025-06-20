document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("review-edit-form");
  form.addEventListener("submit", function (event) {
    const button = event.submitter;
    if (button.name == "delete") {
      if (
        confirm(
          "Sei sicuro di voler eliminare questa recensione? Questa azione è irreversibile.",
        )
      ) {
        const actionField = document.getElementById("form-action");
        const form = document.querySelector("form");

        if (actionField && form) {
          actionField.value = "delete";
          form.submit();
        } else {
          console.error(
            "Impossibile trovare il campo azione o il form per l'eliminazione",
          );
        }
      } else {
        event.preventDefault();
      }
    }
  });
});
