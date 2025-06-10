document.addEventListener("DOMContentLoaded", function () {
    const menseSelect = document.getElementById("mense-select");
    const searchInput = document.getElementById("search-piatti");
    const resultsCount = document.getElementById("search-results-count");
    
    if (!menseSelect) return;

    // Funzione per aggiornare SEO dinamicamente
    function updateSEO(mensaName) {
        if (!mensaName) return;
        
        // Aggiorna title
        document.title = `Menu ${mensaName} - Mense Universitarie Padova | ESU Advisor`;
        
        // Aggiorna meta description esistente
        const metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) {
            metaDescription.setAttribute('content', 
                `Consulta il menu di oggi di ${mensaName} a Padova. Scopri piatti, orari, recensioni e allergeni della mensa universitaria ESU.`
            );
        }
        
        // Aggiorna meta keywords esistente
        const metaKeywords = document.querySelector('meta[name="keywords"]');
        if (metaKeywords) {
            metaKeywords.setAttribute('content', 
                `${mensaName}, menu ${mensaName}, mensa ${mensaName}, orari ${mensaName}, ESU Padova, mense universitarie padova`
            );
        }
    }

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

        // Aggiorna SEO dinamicamente
        updateSEO(mensaId);

        // Aggiorna URL senza ricaricare la pagina (per bookmarkability)
        const url = new URL(window.location);
        url.searchParams.set('mensa', mensaId);
        
        // Aggiorna anche il testo nel history state per migliorare l'accessibilità
        const newTitle = `Menu ${mensaId} - ESU Advisor`;
        window.history.replaceState(
            { mensa: mensaId }, 
            newTitle, 
            url
        );

        // Trigger evento per notificare altri script del cambio
        window.dispatchEvent(new CustomEvent('mensaChanged', { 
            detail: { 
                mensaId: mensaId,
                mensaName: mensaId 
            } 
        }));
    }

    // Event listener per il cambio di mensa
    menseSelect.addEventListener("change", function () {
        const selectedMensa = this.value;
        if (selectedMensa) {
            // Save selected mensa to sessionStorage for dynamic back links
            sessionStorage.setItem('currentMensa', selectedMensa);
            
            showMensaPiatti(selectedMensa);
            
            // Annuncia il cambio agli screen reader
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = `Menu aggiornato per ${selectedMensa}`;
            document.body.appendChild(announcement);
            
            // Rimuovi l'annuncio dopo un po'
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
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
        } else if (event.state && event.state.mensa) {
            // Ripristina dallo state del browser
            menseSelect.value = event.state.mensa;
            sessionStorage.setItem('currentMensa', event.state.mensa);
            showMensaPiatti(event.state.mensa);
        }
    });

    // Gestisci il cambio di focus sulla select per l'accessibilità
    menseSelect.addEventListener('focus', function() {
        this.setAttribute('aria-expanded', 'true');
    });

    menseSelect.addEventListener('blur', function() {
        this.setAttribute('aria-expanded', 'false');
    });
});