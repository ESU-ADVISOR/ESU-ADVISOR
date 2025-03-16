// Attendere che il DOM sia completamente caricato
window.onload = function() {
    // Riferimento al bottone
    var backToTopBtn = document.getElementById('back-to-top');
    
    // Se il bottone non esiste, uscire
    if (!backToTopBtn) {
        return;
    }
    
    // Funzione estremamente semplice per lo scroll
    function scrollToTop() {
        document.body.scrollTop = 0; // Per Safari
        document.documentElement.scrollTop = 0; // Per Chrome, Firefox, IE e Opera
    }
    
    // Assegna direttamente la funzione all'evento onclick
    backToTopBtn.onclick = scrollToTop;
    
    // Mostra o nascondi il bottone in base alla posizione di scroll
    window.onscroll = function() {
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
            backToTopBtn.style.display = "block";
        } else {
            backToTopBtn.style.display = "none";
        }
    };
    
    // Inizialmente nascondi il bottone
    backToTopBtn.style.display = "none";
};