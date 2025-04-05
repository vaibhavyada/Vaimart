<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Process order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    // Validate form fields
    $required_fields = ['name', 'email', 'address', 'city', 'state', 'zip', 'card_number', 'expiry', 'cvv'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    
    // Calculate total
    $total = 0;
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $total += $product['price'] * $quantity;
    }
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $total]);
            $order_id = $pdo->lastInsertId();
            
            // Add order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($products as $product) {
                $quantity = $_SESSION['cart'][$product['id']];
                $stmt->execute([
                    $order_id,
                    $product['id'],
                    $quantity,
                    $product['price']
                ]);
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $pdo->commit();
            
            $_SESSION['success'] = "Order placed successfully!";
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Failed to place order. Please try again.";
        }
    }
}

// Include header after all header() function calls
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>
        <h1 class="display-4 mb-3" style="color: var(--text-color);">Order Placed Successfully!</h1>
        <p class="lead text-muted">Thank you for your purchase. Your order has been confirmed.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color: var(--text-color);">Order Information</h6>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Order ID:</strong> #<?php echo $order_id; ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Date:</strong> <?php echo date('F j, Y'); ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Status:</strong> 
                                <span class="badge bg-success">Pending</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color: var(--text-color);">Shipping Information</h6>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Name:</strong> <?php echo htmlspecialchars($_POST['name']); ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Address:</strong> <?php echo htmlspecialchars($_POST['address']); ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>City:</strong> <?php echo htmlspecialchars($_POST['city']); ?>, 
                                <strong>State:</strong> <?php echo htmlspecialchars($_POST['state']); ?>, 
                                <strong>ZIP:</strong> <?php echo htmlspecialchars($_POST['zip']); ?>
                            </p>
                        </div>
                    </div>

                    <h6 class="mb-3" style="color: var(--text-color);">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="color: var(--text-color);">Product</th>
                                    <th style="color: var(--text-color);">Quantity</th>
                                    <th style="color: var(--text-color);">Price</th>
                                    <th style="color: var(--text-color);">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="img-thumbnail me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: var(--text-color);"><?php echo $_SESSION['cart'][$product['id']]; ?></td>
                                        <td style="color: var(--text-color);">$<?php echo number_format($product['price'], 2); ?></td>
                                        <td style="color: var(--text-color);">$<?php echo number_format($product['price'] * $_SESSION['cart'][$product['id']], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong style="color: var(--text-color);">Total:</strong></td>
                                    <td><strong style="color: var(--text-color);">$<?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="index.php" class="btn" style="background-color: var(--primary-color); color: white;">
                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 