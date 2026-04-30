document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const rootElement = document.documentElement;
    
    // Load saved theme
    const savedTheme = localStorage.getItem('dev-theme') || 'dark';
    rootElement.setAttribute('data-dev-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = rootElement.getAttribute('data-dev-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            rootElement.setAttribute('data-dev-theme', newTheme);
            localStorage.setItem('dev-theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (!themeToggleBtn) return;
        if (theme === 'dark') {
            themeToggleBtn.innerHTML = '☀️ Light Mode';
        } else {
            themeToggleBtn.innerHTML = '🌙 Dark Mode';
        }
    }
});
