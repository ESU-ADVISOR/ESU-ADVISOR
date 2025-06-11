document.addEventListener("DOMContentLoaded", function () {
  const deleteAccountButton = document.getElementById("delete-account-button");

  if (deleteAccountButton) {
    deleteAccountButton.addEventListener("click", function () {
      confirmDeleteAccount();
    });
  }
});

function confirmDeleteAccount() {
  if (
    confirm(
      "Sei sicuro di voler eliminare il tuo account? Questa azione è irreversibile.",
    )
  ) {
    const form = document.getElementById("delete-account-form");

    if (form) {
      // Create a hidden input to indicate account deletion
      const deleteInput = document.createElement("input");
      deleteInput.type = "hidden";
      deleteInput.name = "delete_account";
      deleteInput.value = "1";
      form.appendChild(deleteInput);

      form.submit();
    } else {
      console.error(
        "Impossibile trovare il form per l'eliminazione dell'account",
      );
    }
  }
}
