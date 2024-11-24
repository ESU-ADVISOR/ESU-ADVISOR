// Theme switcher functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('theme-dark');
        themeToggle.checked = true;
    }

    // Theme switch event listener
    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.documentElement.classList.add('theme-dark');
            document.documentElement.classList.remove('theme-light');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('theme-dark');
            document.documentElement.classList.add('theme-light');
            localStorage.setItem('theme', 'light');
        }
    });
});