<?php
// Start session and include database connection
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

// Fetch cart items
$cart_items = [];
$total = 0;

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

// Process order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    
    // Validate form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zip = trim($_POST['zip']);
    $card_number = trim($_POST['card_number']);
    $expiry = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($state)) $errors[] = "State is required";
    if (empty($zip)) $errors[] = "ZIP code is required";
    if (empty($card_number)) $errors[] = "Card number is required";
    if (empty($expiry)) $errors[] = "Expiry date is required";
    if (empty($cvv)) $errors[] = "CVV is required";
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $total]);
            $order_id = $pdo->lastInsertId();
            
            // Add order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt->execute([
                    $order_id,
                    $item['product']['id'],
                    $item['quantity'],
                    $item['product']['price']
                ]);
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $pdo->commit();
            
            $_SESSION['success'] = "Order placed successfully!";
            header("Location: order_confirmation.php?order_id=" . $order_id);
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
    <h2 class="mb-4" style="color: var(--text-color);">Checkout</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="order_confirmation.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label" style="color: var(--text-color);">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label" style="color: var(--text-color);">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label" style="color: var(--text-color);">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label" style="color: var(--text-color);">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state" class="form-label" style="color: var(--text-color);">State</label>
                                <input type="text" class="form-control" id="state" name="state" value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip" class="form-label" style="color: var(--text-color);">ZIP Code</label>
                                <input type="text" class="form-control" id="zip" name="zip" value="<?php echo isset($_POST['zip']) ? htmlspecialchars($_POST['zip']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <h4 class="mt-4 mb-3" style="color: var(--text-color);">Payment Information</h4>
                        <div class="mb-3">
                            <label for="card_number" class="form-label" style="color: var(--text-color);">Card Number</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : ''; ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expiry" class="form-label" style="color: var(--text-color);">Expiry Date</label>
                                <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY" value="<?php echo isset($_POST['expiry']) ? htmlspecialchars($_POST['expiry']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label" style="color: var(--text-color);">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" value="<?php echo isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card" data-aos="fade-up">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h4 class="mb-0">Order Summary</h4>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h6 class="mb-0" style="color: var(--text-color);"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                            </div>
                            <span style="color: var(--text-color);">$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong style="color: var(--text-color);">Total:</strong>
                        <strong style="color: var(--text-color);">$<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 