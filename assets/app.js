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

function initCarousel() {
    const carousel = document.querySelector('.carousel');
    const slides = document.querySelectorAll('.carousel-slide');
    if (!carousel || slides.length < 2) return;

    if (carousel.dataset.initialized === 'true') return;
    carousel.dataset.initialized = 'true';

    let current = 0;
    const showSlide = (index) => {
        slides.forEach(s => s.classList.remove('active'));
        slides[index].classList.add('active');
    };
    const next = () => {
        current = (current + 1) % slides.length;
        showSlide(current);
    };

    let timer = setInterval(next, 5000);
    document.addEventListener('turbo:before-cache', () => clearInterval(timer), { once: true });
}

const components = [initBurger, initCarousel];

const initAll = () => components.forEach(fn => fn());

document.addEventListener('DOMContentLoaded', initAll);
document.addEventListener('turbo:load', initAll);
window.addEventListener('pageshow', (e) => {
    if (e.persisted) {
        document.querySelectorAll('.carousel').forEach(c => delete c.dataset.initialized);
        initAll();
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    if (!lightbox || !lightboxImg) return;

    const container = document.querySelector('.photos-container');

    function openLightbox(src) {
        lightboxImg.src = src;
        lightbox.classList.add('is-open');
    }

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        lightboxImg.src = '';
    }

    container?.addEventListener('click', (event) => {
        const item = event.target.closest('.photo-item');
        if (item && item.dataset.lightboxSrc) {
            openLightbox(item.dataset.lightboxSrc);
        }
    });

    lightbox.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
            closeLightbox();
        }
    });
});