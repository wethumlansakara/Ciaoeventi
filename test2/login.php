<?php
$pageTitle = "Login";
require_once __DIR__ . '/includes/header.php';
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate
    $errors = [];
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    
    if (empty($errors)) {
        // Check user
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Redirect
                header("Location: index.php");
                exit();
            } else {
                $errors['password'] = "Invalid password";
            }
        } else {
            $errors['email'] = "No account found with this email";
        }
    }
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-md mx-auto glass-card p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold mb-2">
                    <span class="text-gradient">Welcome Back</span>
                </h2>
                <p class="text-secondary">Sign in to your CiaoEventi account</p>
            </div>
            
            <form id="login-form" class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="<?php echo $_POST['email'] ?? ''; ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="form-error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-secondary">
                        Don't have an account? 
                        <a href="register.php" class="text-pink hover-underline">Sign up here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>