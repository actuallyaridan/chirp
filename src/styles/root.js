const htmlEl = document.documentElement;

// Load theme and accent color from localStorage if available
const currentTheme = localStorage.getItem('theme') || 'auto';
const currentAccentColor = localStorage.getItem('accent-color') || '#1AD063';

// Apply the theme and accent color settings
htmlEl.dataset.theme = currentTheme;
htmlEl.style.setProperty('--accent-color', currentAccentColor);

document.addEventListener('DOMContentLoaded', () => {
    const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

    // Function to apply the appropriate theme based on the "auto" mode
    const applyAutoTheme = () => {
        const theme = prefersDarkScheme.matches ? 'dark' : 'light';
        htmlEl.dataset.theme = theme; // Temporarily set theme based on system preference
    };

    // Function to update theme based on selection
    const toggleTheme = (theme) => {
        if (theme === 'auto') {
            htmlEl.dataset.theme = 'auto';
            localStorage.setItem('theme', 'auto');
            applyAutoTheme(); // Apply system preference without changing the selected radio
        } else {
            htmlEl.dataset.theme = theme;
            localStorage.setItem('theme', theme);
        }
    };

    // Initial theme setting
    if (currentTheme === 'auto') {
        applyAutoTheme();
    } else {
        toggleTheme(currentTheme);
    }

    // Listen for system dark mode changes when "auto" is selected
    prefersDarkScheme.addEventListener('change', () => {
        if (htmlEl.dataset.theme === 'auto') {
            applyAutoTheme();
        }
    });

    // Function to update accent color
    const updateAccentColor = (color) => {
        htmlEl.style.setProperty('--accent-color', color);
        localStorage.setItem('accent-color', color);
    };

    // Handle theme radio change
    const themeRadios = document.querySelectorAll('input[name="theme"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            toggleTheme(radio.value);
        });

        // Ensure "auto" stays checked if selected
        if (radio.value === currentTheme) {
            radio.checked = true;
        }
    });

    // Handle accent color radio change
    const colorRadios = document.querySelectorAll('input[name="accent_color"]');
    colorRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            updateAccentColor(radio.value);
        });

        // Check if the current accent color matches the radio button
        if (radio.value === currentAccentColor) {
            radio.checked = true;
        }
    });
});
