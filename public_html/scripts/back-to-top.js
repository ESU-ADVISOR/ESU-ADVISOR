document.addEventListener("DOMContentLoaded", function () {
  var backToTopBtn = document.getElementById("back-to-top");
  var left_column = document.getElementById("content-scroll");
  var content = document.getElementById("content");

  if (!backToTopBtn) {
    return;
  }

  function scrollToTop() {
    if (content) content.scrollTop = 0;
    if (left_column) left_column.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }

  backToTopBtn.onclick = scrollToTop;

  if (left_column) {
    left_column.onscroll = function () {
      if (
        left_column.scrollTop > 200 ||
        document.documentElement.scrollTop > 200
      ) {
        backToTopBtn.style.display = "block";
      } else {
        backToTopBtn.style.display = "none";
      }
    };
  }
  if (content) {
    content.onscroll = function () {
      if (content.scrollTop > 200) {
        backToTopBtn.style.display = "block";
      } else {
        backToTopBtn.style.display = "none";
      }
    };
  }
  document.body.onscroll = function () {
    if (document.body.scrollTop > 200) {
      backToTopBtn.style.display = "block";
    } else {
      backToTopBtn.style.display = "none";
    }
  };

  backToTopBtn.style.display = "none";
});
