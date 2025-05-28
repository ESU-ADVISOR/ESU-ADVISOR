document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("register-form");
  
    if (form) {
        Array.from(form.elements).forEach(function(field) {
            const describedbyId = field.getAttribute('aria-describedby');
            if (describedbyId) {
                const describedElem = document.getElementById(describedbyId);
                if (describedElem) {
                    const toggleDescription = () => {
                        if (field.value.trim()) {
                            describedElem.setAttribute('aria-hidden', 'true');
                            describedElem.classList.add('hidden');
                        } else {
                            describedElem.removeAttribute('aria-hidden');
                            describedElem.classList.remove('hidden');
                        }
                    };
                    field.addEventListener('input', toggleDescription);
                    // Initialize state on load
                    toggleDescription();
                }
            }
        });
    }
});
