<?php
$pageTitle = "Register";
require_once __DIR__ . '/includes/header.php';
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate
    $errors = [];
    
    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters";
    }
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    // Check if username/email exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $errors['general'] = "Username or email already exists";
        }
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            // Auto login
            $user_id = $stmt->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            // Redirect
            header("Location: index.php");
            exit();
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
    }
}
?>

<section class="py-20">
    <div class="container">
        <div class="max-w-md mx-auto glass-card p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold mb-2">
                    <span class="text-gradient">Join CiaoEventi</span>
                </h2>
                <p class="text-secondary">Create your account to start discovering events</p>
            </div>
            
            <?php if (isset($errors['general'])): ?>
                <div class="mb-4 p-3 bg-red-900/20 border border-red-500/30 rounded">
                    <p class="text-red-400"><?php echo $errors['general']; ?></p>
                </div>
            <?php endif; ?>
            
            <form id="register-form" class="register-form" method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" 
                           value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <span class="form-error"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
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
                    <input type="password" id="password" name="password" class="form-input" 
                           data-validate="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="form-error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="form-error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-secondary">
                        Already have an account? 
                        <a href="login.php" class="text-pink hover-underline">Sign in here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>