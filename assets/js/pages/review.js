document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('#reviews-grid');
    const sortSelect = document.querySelector('#reviews-sort');
    const loadMoreButton = document.querySelector('[data-load-more-reviews]');
    const visibleCountElement = document.querySelector('[data-visible-review-count]');

    if (!grid) {
        return;
    }

    const reviews = Array.from(grid.querySelectorAll('[data-review-card]'));
    const increment = 3;
    let visibleCount = Math.min(6, reviews.length);

    const readRating = (review) => Number.parseInt(review.dataset.rating ?? '0', 10);
    const readDate = (review) => Date.parse(review.dataset.date ?? '') || 0;

    const displayReviews = () => {
        reviews.forEach((review, index) => {
            const isVisible = index < visibleCount;
            review.classList.toggle('review-card--hidden', !isVisible);
            review.hidden = !isVisible;
        });

        if (visibleCountElement) {
            visibleCountElement.textContent = String(Math.min(visibleCount, reviews.length));
        }

        if (loadMoreButton) {
            loadMoreButton.hidden = visibleCount >= reviews.length;
        }
    };

    const sortReviews = () => {
        const selectedSort = sortSelect?.value ?? 'recent';

        reviews.sort((firstReview, secondReview) => {
            if (selectedSort === 'rating-desc') {
                return readRating(secondReview) - readRating(firstReview)
                    || readDate(secondReview) - readDate(firstReview);
            }

            if (selectedSort === 'rating-asc') {
                return readRating(firstReview) - readRating(secondReview)
                    || readDate(secondReview) - readDate(firstReview);
            }

            return readDate(secondReview) - readDate(firstReview);
        });

        reviews.forEach((review) => grid.append(review));
        displayReviews();
    };

    sortSelect?.addEventListener('change', sortReviews);

    loadMoreButton?.addEventListener('click', () => {
        visibleCount = Math.min(visibleCount + increment, reviews.length);
        displayReviews();
    });

    sortReviews();
});