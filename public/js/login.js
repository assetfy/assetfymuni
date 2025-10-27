document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('default-carousel');
    const carouselItems = carousel.querySelectorAll('[data-carousel-item]');
    const indicators = carousel.querySelectorAll('[data-carousel-slide-to]');
    let currentIndex = 0;
    const totalItems = carouselItems.length;
    let intervalId;

    function showSlide(index) {
        carouselItems.forEach((item, idx) => {
            item.classList.toggle('hidden', idx !== index);
            item.classList.toggle('block', idx === index);
        });

        // Update indicators' styles
        indicators.forEach((indicator, idx) => {
            if (idx === index) {
                indicator.style.backgroundColor = '#699BDA'; // Color cuando está activo
                indicator.style.color = 'white'; // Texto en color blanco cuando está activo
            } else {
                indicator.style.backgroundColor = 'white'; // Fondo blanco cuando no está activo
                indicator.style.color = '#699BDA'; // Texto en color principal cuando no está activo
            }
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        showSlide(currentIndex);
    }

    indicators.forEach((indicator, idx) => {
        indicator.addEventListener('click', () => {
            currentIndex = idx;
            showSlide(currentIndex);
        });
    });

    function startCarousel() {
        intervalId = setInterval(nextSlide, 5000);
    }

    function stopCarousel() {
        clearInterval(intervalId);
    }

    // Initial display
    showSlide(currentIndex);
    startCarousel();

    const carouselImage = document.querySelector('#carousel-image');
    const roundedButtons = document.querySelectorAll('[data-carousel-slide-to]');

    carouselImage.addEventListener('mouseenter', stopCarousel);
    carouselImage.addEventListener('mouseleave', startCarousel);

    roundedButtons.forEach(button => {
        button.addEventListener('mouseenter', stopCarousel);
        button.addEventListener('mouseleave', startCarousel);
    });
});
