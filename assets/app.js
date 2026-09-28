import './stimulus_bootstrap.js';
import './styles/app.css';

function initBurger() {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav');

    if (!burger || !nav) return;

    const setOpen = (open) => {
        nav.classList.toggle('is-open', open);
        burger.classList.toggle('is-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    burger.onclick = () => {
        setOpen(!nav.classList.contains('is-open'));
    };

    nav.querySelectorAll('a').forEach((link) => {
        link.onclick = () => setOpen(false);
    });

    document.onkeydown = (e) => {
        if (e.key === 'Escape') setOpen(false);
    };
}

// Au premier chargement
document.addEventListener('DOMContentLoaded', initBurger);

// À chaque changement de page avec Turbo
document.addEventListener('turbo:load', initBurger);