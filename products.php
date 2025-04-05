<?php
require_once 'includes/header.php';

// Get category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$query = "SELECT * FROM products";
$params = [];

if ($category) {
    $query .= " WHERE category = ?";
    $params[] = $category;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get unique categories
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="row g-4">
    <!-- Category Filter -->
    <div class="col-md-3">
        <div class="card sticky-top" style="top: 2rem;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-funnel me-2"></i>Categories
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="products.php" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !$category ? 'active' : ''; ?>">
                    All Products
                    <span class="badge bg-primary rounded-pill"><?php echo count($products); ?></span>
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?php echo urlencode($cat); ?>" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $category === $cat ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                        <span class="badge bg-primary rounded-pill">
                            <?php 
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category = ?");
                            $stmt->execute([$cat]);
                            echo $stmt->fetchColumn();
                            ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="col-md-9">
        <?php if (empty($products)): ?>
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <div>No products found in this category.</div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-primary">New</span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <h5 class="mb-0 text-primary">
                                        $<?php echo number_format($product['price'], 2); ?>
                                    </h5>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 