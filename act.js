document.addEventListener('DOMContentLoaded', function () {
    const watchlistWrapper = document.querySelector('.watchlist-wrapper');
    const leftArrow = document.querySelector('.left-arrow');
    const rightArrow = document.querySelector('.right-arrow');
    const watchlistItems = document.querySelectorAll('.watchlist-item');

    if (!watchlistWrapper || !leftArrow || !rightArrow || watchlistItems.length === 0) {
        console.error("One or more carousel elements not found.");
        return;
    }

    let itemWidth = watchlistItems[0].offsetWidth +
        parseInt(getComputedStyle(watchlistItems[0]).marginRight) +
        parseInt(getComputedStyle(watchlistItems[0]).marginLeft);

    const itemsToShow = 4;

    leftArrow.addEventListener('click', () => {
        watchlistWrapper.scrollBy({ left: -itemWidth * itemsToShow, behavior: "smooth" });
    });

    rightArrow.addEventListener('click', () => {
        watchlistWrapper.scrollBy({ left: itemWidth * itemsToShow, behavior: "smooth" });
    });

    const updateArrowsVisibility = () => {
        const maxScroll = watchlistWrapper.scrollWidth - watchlistWrapper.clientWidth;
        leftArrow.style.display = watchlistWrapper.scrollLeft > 0 ? 'block' : 'none';
        rightArrow.style.display = watchlistWrapper.scrollLeft < maxScroll ? 'block' : 'none';
    };

    watchlistWrapper.addEventListener('scroll', updateArrowsVisibility);
    updateArrowsVisibility();

    updateArrowsVisibility();
});