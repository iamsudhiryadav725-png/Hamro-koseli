(function () {
    function initTodayDeals() {
        const gridContainer = document.getElementById('product-grid');
        if (!gridContainer) return; // not on today's deals page

        const categoryPills = document.querySelectorAll('.filter-pill');
        const productCards = document.querySelectorAll('.product-card');
        const modal = document.getElementById('product-details-modal');
        const container = document.getElementById('product-details-container');
        const closeBtn = document.getElementById('close-product-details');

        // Clear existing intervals
        if (window.todayDealsInterval) clearInterval(window.todayDealsInterval);
        if (window.featuredSliderInterval) clearInterval(window.featuredSliderInterval);

        // Filter functionality
        function filterProducts(category) {
            productCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                card.style.display = (category === 'all' || cardCategory === category) ? '' : 'none';
            });
        }

        categoryPills.forEach(pill => {
            pill.addEventListener('click', function () {
                categoryPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                filterProducts(pill.getAttribute('data-category'));
            });
        });

        // Sort functionality
        const sortSelect = document.getElementById('sort-select');
        function sortProducts(criteria) {
            const cardsArray = Array.from(productCards).filter(card => card.style.display !== 'none');
            cardsArray.sort((a, b) => {
                if (criteria === 'discount') return parseInt(b.getAttribute('data-discount')) - parseInt(a.getAttribute('data-discount'));
                if (criteria === 'price-asc') return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                if (criteria === 'price-desc') return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                return 0;
            });
            cardsArray.forEach(card => gridContainer.appendChild(card));
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', () => sortProducts(sortSelect.value));
        }



        // ==================== FEATURED CAROUSEL LOGIC ====================
        const carousel = document.getElementById('featured-carousel');
        const prevBtn = document.getElementById('featured-prev');
        const nextBtn = document.getElementById('featured-next');
        const cards = document.querySelectorAll('.featured-card');
        let currentIndex = 0;

        function updateCarousel() {
            if (!carousel || !cards.length) return;
            const cardWidth = cards[0].offsetWidth;
            const offset = -currentIndex * (cardWidth + 24); // 24px is the gap
            carousel.style.transform = `translateX(${offset}px)`;
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentIndex = Math.max(0, currentIndex - 1);
                updateCarousel();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentIndex = Math.min(cards.length - 1, currentIndex + 1);
                updateCarousel();
            });
        }

        // Handle window resize for responsive behavior
        window.addEventListener('resize', () => {
            updateCarousel();
            updateTrendingCarousel();
        });

        // ==================== TRENDING NOW CAROUSEL LOGIC ====================
        const trendingCarousel = document.getElementById('trending-carousel');
        const trendingPrevBtn = document.getElementById('trending-prev');
        const trendingNextBtn = document.getElementById('trending-next');
        const trendingCards = document.querySelectorAll('.trending-card');
        let trendingIndex = 0;

        function updateTrendingCarousel() {
            if (!trendingCarousel || !trendingCards.length) return;
            const cardWidth = trendingCards[0].offsetWidth;
            const offset = -trendingIndex * (cardWidth + 24); // 24px is the gap
            trendingCarousel.style.transform = `translateX(${offset}px)`;
        }

        if (trendingPrevBtn) {
            trendingPrevBtn.addEventListener('click', () => {
                trendingIndex = Math.max(0, trendingIndex - 1);
                updateTrendingCarousel();
            });
        }

        if (trendingNextBtn) {
            trendingNextBtn.addEventListener('click', () => {
                trendingIndex = Math.min(trendingCards.length - 1, trendingIndex + 1);
                updateTrendingCarousel();
            });
        }

        // =============================
        // Featured Slider Auto Play
        // =============================
        const featuredCarousel = document.getElementById("featured-carousel");
        const featuredCards = document.querySelectorAll(".featured-card");
        let currentSlide = 0;

        function moveFeaturedSlider() {
            if (!featuredCarousel || !featuredCards.length) return;
            currentSlide++;
            if (currentSlide >= featuredCards.length) {
                currentSlide = 0;
            }
            featuredCarousel.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        if (featuredCarousel && featuredCards.length > 0) {
            window.featuredSliderInterval = setInterval(moveFeaturedSlider, 3000);
        }

        // =============================
        // Countdown Timer
        // =============================
        const countdownEl = document.getElementById('deal-countdown');
        const dealEndsAt = countdownEl ? countdownEl.getAttribute('data-ends-at') : null;
        const endTime = dealEndsAt ? new Date(dealEndsAt).getTime() : (new Date().getTime() + (8 * 60 * 60 * 1000));

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            const daysEl = document.getElementById("countdown-days");
            const hoursEl = document.getElementById("countdown-hours");
            const minutesEl = document.getElementById("countdown-minutes");
            const secondsEl = document.getElementById("countdown-seconds");

            if (distance <= 0) {
                if (daysEl) daysEl.textContent = "00";
                if (hoursEl) hoursEl.textContent = "00";
                if (minutesEl) minutesEl.textContent = "00";
                if (secondsEl) secondsEl.textContent = "00";
                if (window.todayDealsInterval) clearInterval(window.todayDealsInterval);
                return;
            }

            const days    = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = String(days).padStart(2, "0");
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, "0");
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, "0");
        }

        updateCountdown();
        window.todayDealsInterval = setInterval(updateCountdown, 1000);
    }

    initTodayDeals();
    document.addEventListener('livewire:navigated', initTodayDeals);
})();