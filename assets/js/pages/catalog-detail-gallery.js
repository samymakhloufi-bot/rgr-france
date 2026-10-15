/* ==================================================
   GALERIE DES FICHES CATALOGUE
================================================== */

const gallery = document.querySelector('[data-catalog-gallery]');

if (gallery) {
    const mainImage = gallery.querySelector('[data-gallery-main]');
    const counter = gallery.querySelector('[data-gallery-counter]');
    const previousButton = gallery.querySelector('[data-gallery-previous]');
    const nextButton = gallery.querySelector('[data-gallery-next]');
    const thumbnails = Array.from(
        gallery.querySelectorAll('[data-gallery-thumbnail]')
    );

    let currentIndex = 0;

    const displayImage = index => {
        if (!mainImage || thumbnails.length === 0) {
            return;
        }

        currentIndex = (index + thumbnails.length) % thumbnails.length;
        const selectedThumbnail = thumbnails[currentIndex];

        mainImage.src = selectedThumbnail.dataset.imageSrc;
        mainImage.alt = selectedThumbnail.dataset.imageAlt;

        thumbnails.forEach((thumbnail, thumbnailIndex) => {
            const isActive = thumbnailIndex === currentIndex;

            thumbnail.classList.toggle(
                'catalog-detail__thumbnail--active',
                isActive
            );
            thumbnail.setAttribute('aria-pressed', String(isActive));
        });

        if (counter) {
            counter.textContent = `${currentIndex + 1} / ${thumbnails.length}`;
        }

        selectedThumbnail.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'nearest',
        });
    };

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => displayImage(index));
    });

    previousButton?.addEventListener('click', () => {
        displayImage(currentIndex - 1);
    });

    nextButton?.addEventListener('click', () => {
        displayImage(currentIndex + 1);
    });

    gallery.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            displayImage(currentIndex - 1);
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            displayImage(currentIndex + 1);
        }
    });
}
