/* --------------------------------------------
   Galerie de la fiche vÃ©hicule
--------------------------------------------- */

const galerieVehicule = document.querySelector('[data-catalog-detail__gallery]');

if (galerieVehicule) {
    const imagePrincipale = galerieVehicule.querySelector('[data-gallery-main]');
    const compteur = galerieVehicule.querySelector('[data-gallery-counter]');
    const miniatures = galerieVehicule.querySelectorAll(
        '[data-gallery-thumbnail]'
    );

    miniatures.forEach(miniature => {
        miniature.addEventListener('click', () => {
            imagePrincipale.src = miniature.dataset.imageSrc;
            imagePrincipale.alt = miniature.dataset.imageAlt;

            miniatures.forEach(bouton => {
                bouton.classList.remove('catalog-detail__gallery__thumbnail--active');
                bouton.setAttribute('aria-pressed', 'false');
            });

            miniature.classList.add('catalog-detail__gallery__thumbnail--active');
            miniature.setAttribute('aria-pressed', 'true');

            compteur.textContent = `${miniature.dataset.imageIndex} / ${miniatures.length}`;
        });
    });
}