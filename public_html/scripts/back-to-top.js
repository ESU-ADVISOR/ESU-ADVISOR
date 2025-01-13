function handleScroll() {
  var backToTopButton = document.getElementById("back-to-top");
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    backToTopButton.style.display = "block";
  } else {
    backToTopButton.style.display = "none";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  window.addEventListener("scroll", handleScroll);

  // Clean up on page hide
  document.addEventListener("pagehide", function () {
    window.removeEventListener("scroll", handleScroll);
  });
});
