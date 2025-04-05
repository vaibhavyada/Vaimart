<?php
// Start session and include database connection
session_start();
require_once 'config/database.php';

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        $_SESSION['success'] = "Product added to cart successfully!";
        header("Location: cart.php");
        exit();
    }
}

// Include header after all header() function calls
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6" data-aos="fade-right">
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="card-img-top"
                     style="height: 400px; object-fit: cover;">
            </div>
        </div>
        
        <div class="col-md-6" data-aos="fade-left">
            <h1 class="mb-3" style="color: var(--text-color);"><?php echo htmlspecialchars($product['name']); ?></h1>
            <h3 class="mb-4" style="color: var(--primary-color);">$<?php echo number_format($product['price'], 2); ?></h3>
            
            <p class="mb-4" style="color: var(--text-light);"><?php echo htmlspecialchars($product['description']); ?></p>
            
            <form method="POST" action="" class="mb-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label for="quantity" class="form-label" style="color: var(--text-color);">Quantity:</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" style="width: 80px;">
                    </div>
                    <div class="col">
                        <button type="submit" name="add_to_cart" class="btn" style="background-color: var(--primary-color); color: white;">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="card mb-4">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="mb-0">Product Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong style="color: var(--text-color);">Category:</strong>
                            <span style="color: var(--text-light);"><?php echo htmlspecialchars($product['category']); ?></span>
                        </li>
                        <li class="mb-2">
                            <strong style="color: var(--text-color);">Availability:</strong>
                            <span style="color: var(--text-light);">In Stock</span>
                        </li>
                        <li>
                            <strong style="color: var(--text-color);">SKU:</strong>
                            <span style="color: var(--text-light);"><?php echo htmlspecialchars($product['id']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products Section -->
    <div class="mt-5">
        <h3 class="mb-4" style="color: var(--text-color);">Related Products</h3>
        <div class="row">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
            $stmt->execute([$product['category'], $product_id]);
            $related_products = $stmt->fetchAll();
            
            foreach ($related_products as $related): ?>
                <div class="col-md-3" data-aos="fade-up">
                    <div class="card h-100">
                        <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>" 
                             class="card-img-top"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--text-color);"><?php echo htmlspecialchars($related['name']); ?></h5>
                            <p class="card-text" style="color: var(--text-light);"><?php echo htmlspecialchars(substr($related['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0" style="color: var(--primary-color);">$<?php echo number_format($related['price'], 2); ?></span>
                                <a href="product.php?id=<?php echo $related['id']; ?>" class="btn" style="background-color: var(--secondary-color); color: white;">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 