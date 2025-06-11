window.onload = function () {
  var backToTopBtn = document.getElementById("back-to-top");

  if (!backToTopBtn) {
    return;
  }

  function scrollToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }

  backToTopBtn.onclick = scrollToTop;

  window.onscroll = function () {
    if (
      document.body.scrollTop > 200 ||
      document.documentElement.scrollTop > 200
    ) {
      backToTopBtn.style.display = "block";
    } else {
      backToTopBtn.style.display = "none";
    }
  };

  backToTopBtn.style.display = "none";
};
