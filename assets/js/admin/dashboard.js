document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const menuButton = document.querySelector('[data-admin-menu]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const sidebar = document.querySelector('#admin-sidebar');

    if (!(menuButton instanceof HTMLButtonElement) || !sidebar) {
        return;
    }

    const setMenuState = (isOpen) => {
        body.classList.toggle('admin-menu-open', isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menuButton.setAttribute(
            'aria-label',
            isOpen
                ? 'Fermer le menu d’administration'
                : 'Ouvrir le menu d’administration'
        );
    };

    menuButton.addEventListener('click', () => {
        setMenuState(!body.classList.contains('admin-menu-open'));
    });

    overlay?.addEventListener('click', () => setMenuState(false));

    sidebar.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            setMenuState(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMenuState(false);
            menuButton.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.matchMedia('(min-width: 64rem)').matches) {
            setMenuState(false);
        }
    });
});
