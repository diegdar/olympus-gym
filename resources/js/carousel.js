document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('testimonials-carousel');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (!carousel || !prevBtn || !nextBtn) {
        console.warn('Carousel elements not found');
        return;
    }

    const scrollAmount = 400; // Adjust based on card width

    function updateButtonStates() {
        const isAtStart = carousel.scrollLeft <= 10;
        const isAtEnd = carousel.scrollLeft >= carousel.scrollWidth - carousel.clientWidth - 10;

        prevBtn.disabled = isAtStart;
        nextBtn.disabled = isAtEnd;

        prevBtn.setAttribute('aria-disabled', isAtStart.toString());
        nextBtn.setAttribute('aria-disabled', isAtEnd.toString());

        prevBtn.classList.toggle('opacity-50', isAtStart);
        nextBtn.classList.toggle('opacity-50', isAtEnd);
        prevBtn.classList.toggle('cursor-not-allowed', isAtStart);
        nextBtn.classList.toggle('cursor-not-allowed', isAtEnd);
    }

    function handlePrevClick() {
        if (!prevBtn.disabled) {
            carousel.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    function handleNextClick() {
        if (!nextBtn.disabled) {
            carousel.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    function handleScroll() {
        updateButtonStates();
    }

    function handleResize() {
        updateButtonStates();
    }

    // Add event listeners
    prevBtn.addEventListener('click', handlePrevClick);
    nextBtn.addEventListener('click', handleNextClick);
    carousel.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);

    // Initial check
    updateButtonStates();

    // Cleanup function (for potential future use if component is destroyed)
    function cleanup() {
        prevBtn.removeEventListener('click', handlePrevClick);
        nextBtn.removeEventListener('click', handleNextClick);
        carousel.removeEventListener('scroll', handleScroll);
        window.removeEventListener('resize', handleResize);
    }

    // Expose cleanup for potential future use
    carousel._carouselCleanup = cleanup;
});