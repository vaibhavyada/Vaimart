<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

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
                                <strong>Order ID:</strong> #<?php echo $order['id']; ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Status:</strong> 
                                <span class="badge bg-success"><?php echo ucfirst($order['status']); ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color: var(--text-color);">Customer Information</h6>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?>
                            </p>
                            <p class="mb-1" style="color: var(--text-light);">
                                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?>
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
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                     class="img-thumbnail me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: var(--text-color);"><?php echo $item['quantity']; ?></td>
                                        <td style="color: var(--text-color);">$<?php echo number_format($item['price'], 2); ?></td>
                                        <td style="color: var(--text-color);">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong style="color: var(--text-color);">Total:</strong></td>
                                    <td><strong style="color: var(--text-color);">$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
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