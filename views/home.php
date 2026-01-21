<div class="container">
    <!-- Enhanced Banner Slider -->
    <div class="banner-slider">
        <div class="banner-container">
            <img src="assets/images/banners/banner1.jpg" alt="Banner 1" class="banner-slide active">
            <img src="assets/images/banners/banner2.jpg" alt="Banner 2" class="banner-slide">
            <img src="assets/images/banners/banner3.jpg" alt="Banner 3" class="banner-slide">
            <img src="assets/images/banners/banner4.jpg" alt="Banner 4" class="banner-slide">
            <img src="assets/images/banners/banner5.jpg" alt="Banner 5" class="banner-slide">
            
            <!-- Banner Navigation Dots -->
            <div class="banner-dots">
                <span class="dot active" onclick="currentSlide(0)"></span>
                <span class="dot" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
            </div>
        </div>
    </div>

    <!-- Enhanced Welcome Section -->
    <section class="welcome-section">
        <h2>Welcome to Star Tech</h2>
        <p>🚀 Your one-stop shop for all computer components and custom PC builds 🖥️</p>
        <div class="cta-buttons">
            <a href="index.php?page=products" class="btn btn-primary">Browse Products</a>
            <a href="index.php?page=build-pc" class="btn btn-secondary">Build Your PC</a>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="categories-section">
        <h2>Shop by Category</h2>
        <div class="category-grid" onclick="goToCategory('Processor')">
            <div class="category-card">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💻</div>
                <h3>Processors</h3>
                <p>Intel & AMD CPUs</p>
            </div>
            <div class="category-card" onclick="goToCategory('Motherboard')">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔌</div>
                <h3>Motherboards</h3>
                <p>Latest chipsets</p>
            </div>
            <div class="category-card" onclick="goToCategory('Graphics Card')">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎮</div>
                <h3>Graphics Cards</h3>
                <p>NVIDIA & AMD GPUs</p>
            </div>
            <div class="category-card" onclick="goToCategory('Memory')">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                <h3>Memory</h3>
                <p>DDR4 & DDR5 RAM</p>
            </div>
            <div class="category-card" onclick="goToCategory('Storage')">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💾</div>
                <h3>Storage</h3>
                <p>SSD & HDD</p>
            </div>
            <div class="category-card" onclick="goToCategory('Power Supply')">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔋</div>
                <h3>Power Supply</h3>
                <p>Reliable PSUs</p>
            </div>
        </div>
    </section>
</div>

<script>
// Enhanced Banner Auto-Slide with Dots
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.banner-slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

function showSlide(index) {
    // Remove active class from all slides and dots
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Add active class to current slide and dot
    slides[index].classList.add('active');
    dots[index].classList.add('active');
}

function nextSlide() {
    currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
    showSlide(currentSlideIndex);
}

function currentSlide(index) {
    currentSlideIndex = index;
    showSlide(currentSlideIndex);
}

function goToCategory(category) {
    window.location.href = `index.php?page=products&category=${encodeURIComponent(category)}`;
}

// Auto-slide every 10 seconds
setInterval(nextSlide, 10000);

// Add smooth entrance animation to category cards
window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
});
</script>