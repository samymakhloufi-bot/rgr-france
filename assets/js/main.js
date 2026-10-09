/* =========================
   NAV BAR
========================= */
const header = document.querySelector('header');

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