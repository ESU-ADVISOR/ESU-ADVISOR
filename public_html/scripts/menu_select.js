document.addEventListener("DOMContentLoaded", function () {
    const menseSelect = document.getElementById("mense-select");
    const searchInput = document.getElementById("search-piatti");
    const resultsCount = document.getElementById("search-results-count");
    
    if (!menseSelect) return;

    // Funzione per cambiare mensa senza ricaricare la pagina
    function showMensaPiatti(mensaId) {
        // Pulisci la ricerca
        if (searchInput) {
            searchInput.value = "";
        }
        if (resultsCount) {
            resultsCount.textContent = "";
        }

        // Nascondi tutti gli elementi
        const allElements = document.querySelectorAll('[data-mensa-id]');
        allElements.forEach(element => {
            element.style.display = "none";
        });

        // Mostra solo gli elementi della mensa selezionata
        const currentMensaElements = document.querySelectorAll(`[data-mensa-id="${mensaId}"]`);
        currentMensaElements.forEach(element => {
            element.style.display = "";
        });

        // Aggiorna URL senza ricaricare la pagina (per bookmarkability)
        const url = new URL(window.location);
        url.searchParams.set('mensa', mensaId);
        window.history.replaceState({}, '', url);

        // Trigger evento per notificare altri script del cambio
        window.dispatchEvent(new CustomEvent('mensaChanged', { 
            detail: { mensaId: mensaId } 
        }));
    }

    // Event listener per il cambio di mensa
    menseSelect.addEventListener("change", function () {
        const selectedMensa = this.value;
        if (selectedMensa) {
            // Save selected mensa to sessionStorage for dynamic back links
            sessionStorage.setItem('currentMensa', selectedMensa);
            
            showMensaPiatti(selectedMensa);
        }
    });

    // Inizializzazione: mostra la mensa selezionata e salva in sessionStorage
    const initialMensa = menseSelect.value;
    if (initialMensa) {
        sessionStorage.setItem('currentMensa', initialMensa);
        showMensaPiatti(initialMensa);
    }

    // Supporto per il pulsante indietro del browser
    window.addEventListener('popstate', function(event) {
        const urlParams = new URLSearchParams(window.location.search);
        const mensaFromUrl = urlParams.get('mensa');
        
        if (mensaFromUrl && menseSelect.value !== mensaFromUrl) {
            menseSelect.value = mensaFromUrl;
            sessionStorage.setItem('currentMensa', mensaFromUrl);
            showMensaPiatti(mensaFromUrl);
        }
    });
});