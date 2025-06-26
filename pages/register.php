<?php
$page_title = "Register";
require_once '../includes/header.php';
require_once '../classes/User.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';
$success = '';

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $user = new User();
        $user->username = cleanInput($_POST['username']);
        $user->email = cleanInput($_POST['email']);
        $user->password = $_POST['password'];
        $user->role = 'staff';

        if ($_POST['password'] !== $_POST['confirm_password']) {
            $error = 'Passwords do not match';
        } elseif (strlen($_POST['password']) < 6) {
            $error = 'Password must be at least 6 characters long';
        } elseif ($user->register()) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Username or email already exists';
        }
    } else {
        $error = 'Invalid request';
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header text-center">
                <h4><i class="fas fa-user-plus me-2"></i>Register</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>