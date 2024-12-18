document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("myModal");
  const btn = document.getElementById("delete-account-button");
  const closeBtn = document.getElementById("close-modal");

  btn.addEventListener("click", () => {
    modal.style.display = "block";
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  window.addEventListener("click", (event) => {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });
});
