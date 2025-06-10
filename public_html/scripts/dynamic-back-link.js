document.addEventListener('DOMContentLoaded', function() {
    
    // Funzione per aggiornare tutti i link che puntano a index.php
    function updateBackLinks() {
        // Trova tutti i link che puntano a index.php
        const backLinks = document.querySelectorAll('a[href="index.php"], a[href="./index.php"], a[href="../index.php"]');
        
        if (backLinks.length === 0) return;

        // Controlla se si arriva da profile.php
        const urlParams = new URLSearchParams(window.location.search);
        const fromProfile = urlParams.get('from') === 'profile';

        if (fromProfile) {
            //  CASO: Provenienza da profile.php
            backLinks.forEach(link => {
                link.setAttribute('href', 'profile.php');
                
                // Aggiorna il testo del link se è "Torna alla lista dei piatti"
                const linkText = link.textContent.trim();
                if (linkText.includes('lista dei piatti') || linkText.includes('lista piatti')) {
                    link.innerHTML = link.innerHTML.replace(/Torna alla lista dei piatti/gi, 'Torna al profilo');
                    link.innerHTML = link.innerHTML.replace(/Torna alla lista piatti/gi, 'Torna al profilo');
                }
                
                // Aggiorna attributi di accessibilità
                link.setAttribute('aria-label', 'Torna al profilo utente');
                link.setAttribute('title', 'Torna alla pagina del profilo');
            });
            
        } else {
            //  CASO: Provenienza da index.php o link diretto
            const lastSelectedMensa = sessionStorage.getItem('currentMensa');
            
            backLinks.forEach(link => {
                if (lastSelectedMensa) {
                    // Ha una mensa salvata in sessione (probabilmente da index.php)
                    const newHref = `index.php?mensa=${encodeURIComponent(lastSelectedMensa)}`;
                    link.setAttribute('href', newHref);
                    
                    link.setAttribute('aria-label', `Torna alla lista dei piatti - Menu di ${lastSelectedMensa}`);
                    link.setAttribute('title', `Torna al menu di ${lastSelectedMensa}`);
                } else {
                    // Link diretto o nessuna mensa in sessione → mensa di default
                    link.setAttribute('href', 'index.php');
                    
                    link.setAttribute('aria-label', 'Torna alla lista dei piatti');
                    link.setAttribute('title', 'Torna alla lista dei piatti');
                }
            });
        }
    }

    // Ascolta l'evento mensaChanged da menu_select.js (quando utente cambia mensa su index.php)
    window.addEventListener('mensaChanged', function(event) {
        if (event.detail && event.detail.mensaId) {
            // Aggiorna i link quando viene cambiata mensa (solo se non si viene da profile)
            const urlParams = new URLSearchParams(window.location.search);
            const fromProfile = urlParams.get('from') === 'profile';
            
            if (!fromProfile) {
                updateBackLinks();
            }
        }
    });

    // Ascolta cambiamenti nel sessionStorage (sincronizzazione tra tab)
    window.addEventListener('storage', function(event) {
        if (event.key === 'currentMensa') {
            const urlParams = new URLSearchParams(window.location.search);
            const fromProfile = urlParams.get('from') === 'profile';
            
            if (!fromProfile) {
                updateBackLinks();
            }
        }
    });

    // Esegui l'aggiornamento iniziale
    updateBackLinks();

    // Observer per rilevare link aggiunti dinamicamente
    const observer = new MutationObserver(function(mutations) {
        let shouldUpdate = false;
        
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    const hasIndexLinks = node.querySelectorAll && 
                        node.querySelectorAll('a[href*="index.php"]').length > 0;
                    if (hasIndexLinks || (node.tagName === 'A' && node.href && node.href.includes('index.php'))) {
                        shouldUpdate = true;
                    }
                }
            });
        });
        
        if (shouldUpdate) {
            updateBackLinks();
        }
    });

    // Osserva cambiamenti nel DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Cleanup
    window.addEventListener('beforeunload', function() {
        observer.disconnect();
    });
});