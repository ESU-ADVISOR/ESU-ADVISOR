document.addEventListener("DOMContentLoaded", function () {
    const menseSelect = document.getElementById("mense-select");
    if (menseSelect) {
        menseSelect.addEventListener("change", function () {
            const value = menseSelect.value;
            if (value) {
                window.location.search = "?mensa=" + encodeURIComponent(value);
            }
        });
    }
});