document.addEventListener("DOMContentLoaded", function () {
  const deleteReviewButton = document.getElementById("delete-review-button");

  if (deleteReviewButton) {
    deleteReviewButton.addEventListener("click", function () {
      confirmDelete();
    });
  }
});

function confirmDelete() {
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
  }
}
