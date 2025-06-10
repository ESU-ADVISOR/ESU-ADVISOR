document.addEventListener('DOMContentLoaded', function() {
    // Update back link to include current mensa if available
    const backLink = document.querySelector('a[href="index.php"]');
    
    if (backLink) {
        // Check for mensa in current URL (if user came from a direct link)
        const urlParams = new URLSearchParams(window.location.search);
        const currentMensa = urlParams.get('mensa');
        
        // Check for mensa in sessionStorage (if user navigated through the site)
        const sessionMensa = sessionStorage.getItem('currentMensa');
        
        // Use current URL mensa first, then session mensa
        const selectedMensa = currentMensa || sessionMensa;
        
        if (selectedMensa) {
            // Update the link to include mensa parameter
            const newHref = `index.php?mensa=${encodeURIComponent(selectedMensa)}`;
            backLink.setAttribute('href', newHref);
        }
    }
});