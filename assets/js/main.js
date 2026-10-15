/* =========================
   NAV BAR
========================= */

/* Gestion de l'affichage du header lors du scroll */
const header = document.querySelector('.site-header');

if (header) {
    let previousScrollPosition = window.scrollY;
    const scrollThreshold = 8;

    window.addEventListener(
        'scroll',
        () => {
            const currentScrollPosition = window.scrollY;
            const scrollDifference =
                currentScrollPosition - previousScrollPosition;

            if (currentScrollPosition <= 20) {
                header.classList.remove('header-hidden');
            } else if (scrollDifference > scrollThreshold) {
                // L’utilisateur descend : le header disparaît
                header.classList.add('header-hidden');
            } else if (scrollDifference < -scrollThreshold) {
                // L’utilisateur remonte : le header réapparaît
                header.classList.remove('header-hidden');
            }

            previousScrollPosition = Math.max(
                currentScrollPosition,
                0
            );
        },
        { passive: true }
    );
}

/* Gestion du menu burger */
const burgerBtn = document.querySelector('.burger-btn');
const navList = document.querySelector('.nav-list');

if (burgerBtn && navList) {
    burgerBtn.addEventListener('click', () => {
        const isOpen = navList.classList.toggle('nav-list--open');

        burgerBtn.setAttribute('aria-expanded', String(isOpen));
        burgerBtn.setAttribute(
            'aria-label',
            isOpen ? 'Fermer le menu' : 'Ouvrir le menu'
        );
    });
}

/*   SOUS-MENU « PLUS » */

const dropBtn = document.querySelector('.dropbtn');
const dropMenu = document.querySelector('#drop-menu');

if (dropBtn && dropMenu) {
    dropBtn.addEventListener('click', () => {
        const isOpen =
            dropBtn.getAttribute('aria-expanded') === 'true';

        dropBtn.setAttribute(
            'aria-expanded',
            String(!isOpen)
        );
    });
}