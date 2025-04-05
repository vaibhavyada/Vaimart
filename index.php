<?php
require_once 'includes/header.php';

// Fetch featured products from database
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();

// Fetch top products for the slider
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");
$slider_products = $stmt->fetchAll();
?>

<!-- Hero Section with Product Slider -->
<div id="heroSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php for($i = 0; $i < count($slider_products); $i++): ?>
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
        <?php endfor; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach($slider_products as $index => $product): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="hero-section" style="background: linear-gradient(rgba(61, 39, 35, 0.7), rgba(61, 39, 35, 0.7)), url('<?php echo htmlspecialchars($product['image_url']); ?>') center/cover no-repeat;">
                    <div class="container">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-md-6" data-aos="fade-right">
                                <h1 class="display-4 text-white mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
                                <p class="lead text-white mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="d-flex gap-3">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-lg">View Details</a>
                                    <a href="products.php" class="btn btn-outline-light btn-lg">Shop All</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Featured Products Section -->
<div class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 mb-3" style="color: var(--text-color);">Featured Products</h2>
        <p class="text-muted">Discover our handpicked selection of premium products</p>
    </div>
    <div class="row g-4">
        <?php foreach($featured_products as $index => $product): ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning">New</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title" style="color: var(--text-color);"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h5 class="mb-0" style="color: var(--primary-color);">$<?php echo number_format($product['price'], 2); ?></h5>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-5" data-aos="fade-up">
        <a href="products.php" class="btn btn-outline-secondary btn-lg">
            View All Products
            <i class="bi bi-arrow-right ms-2"></i>
        </a>
    </div>
</div>

<!-- Brand Logos Section -->
<div class="py-5" style="background-color: var(--background-color);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 mb-3" style="color: var(--text-color);">Our Trusted Brands</h2>
            <p class="text-muted">We partner with the world's leading manufacturers</p>
        </div>
        <div class="row align-items-center justify-content-center g-4" data-aos="fade-up">
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand1.png" alt="Brand 1" class="img-fluid brand-logo">
            </div>
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand2.png" alt="Brand 2" class="img-fluid brand-logo">
            </div>
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand3.png" alt="Brand 3" class="img-fluid brand-logo">
            </div>
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand4.png" alt="Brand 4" class="img-fluid brand-logo">
            </div>
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand5.png" alt="Brand 5" class="img-fluid brand-logo">
            </div>
            <div class="col-6 col-md-2">
                <img src="assets/images/brands/brand6.png" alt="Brand 6" class="img-fluid brand-logo">
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-50 {
        min-height: 50vh;
    }
    
    .hero-section {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    .carousel-item {
        transition: transform 1.2s ease-in-out;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .carousel:hover .carousel-control-prev,
    .carousel:hover .carousel-control-next {
        opacity: 1;
    }
    
    .carousel-indicators {
        margin-bottom: 2rem;
    }
    
    .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 0 6px;
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .carousel-indicators button.active {
        background-color: var(--primary-light);
        transform: scale(1.2);
    }
    
    .brand-logo {
        max-height: 60px;
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.3s ease;
    }
    
    .brand-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.1);
    }
    
    @media (max-width: 768px) {
        .min-vh-50 {
            min-height: 40vh;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            display: none;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?> 