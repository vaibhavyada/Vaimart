<?php
require_once 'includes/header.php';

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = "Thank you for your message. We'll get back to you soon!";
            
            // Clear form data after successful submission
            $name = $email = $message = '';
        } catch (PDOException $e) {
            $error = "Failed to send message. Please try again.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>Contact Us
                    </h4>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div><?php echo $success; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?php echo $error; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="name" class="form-label">Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Message</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php 
                                    echo isset($message) ? htmlspecialchars($message) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>Visit Us
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">yadavvaibhav930@gmail.com</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-telephone-fill text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Phone</h6>
                                    <p class="text-muted mb-0">+91 7704868375</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-fill text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Business Hours</h6>
                                    <p class="text-muted mb-0">Monday - Friday, 9:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-person-badge text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Developer</h6>
                                    <p class="text-muted mb-0">Vaibhav Yadav</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-card-text text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Student ID</h6>
                                    <p class="text-muted mb-0">22BBS0030</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-building text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Institution</h6>
                                    <p class="text-muted mb-0">VIT Vellore University</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

 