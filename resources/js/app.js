import './bootstrap';

const toggle = document.querySelector('[data-theme-toggle]');
const root = document.documentElement;

console.log('app.js loaded, toggle =', toggle);

if (toggle && root) {
    const stored = localStorage.getItem('theme');
    if (stored === 'dark') {
        root.classList.add('dark');
    }

    toggle.addEventListener('click', () => {
        console.log('toggle clicked');
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
} else {
    console.log('No toggle or root found');
}
