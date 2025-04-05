<?php
// Start session and include database connection
session_start();
require_once 'config/database.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle quantity updates and removals
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    } elseif (isset($_POST['remove'])) {
        $product_id = $_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit();
}

// Fetch cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// Include header after all header() function calls
require_once 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4" style="color: var(--text-color);">Shopping Cart</h2>
    
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info" role="alert">
            Your cart is empty. <a href="index.php" style="color: var(--primary-color);">Continue shopping</a>
        </div>
    <?php else: ?>
        <form method="POST" action="">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="color: var(--text-color);">Product</th>
                            <th style="color: var(--text-color);">Price</th>
                            <th style="color: var(--text-color);">Quantity</th>
                            <th style="color: var(--text-color);">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                                        <div>
                                            <h5 class="mb-0" style="color: var(--text-color);"><?php echo htmlspecialchars($item['product']['name']); ?></h5>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['product']['description']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--text-color);">$<?php echo number_format($item['product']['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['product']['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="form-control" style="width: 80px;">
                                </td>
                                <td style="color: var(--text-color);">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                <td>
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <button type="submit" name="remove" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong style="color: var(--text-color);">Total:</strong></td>
                            <td><strong style="color: var(--text-color);">$<?php echo number_format($total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                <div>
                    <button type="submit" name="update" class="btn btn-warning me-2">Update Cart</button>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 