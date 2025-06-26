<?php
$page_title = "Profile";
require_once '../includes/header.php';
require_once '../classes/User.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user = new User();
$user->id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user data
$user->readOne();

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $user->username = cleanInput($_POST['username']);
        $user->email = cleanInput($_POST['email']);
        
        // Check if password is being updated
        if (!empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $error = 'Passwords do not match';
            } elseif (strlen($_POST['password']) < 6) {
                $error = 'Password must be at least 6 characters long';
            } else {
                $user->password = $_POST['password'];
            }
        }

        if (empty($error)) {
            if ($user->update()) {
                $_SESSION['username'] = $user->username;
                $success = 'Profile updated successfully!';
            } else {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    } else {
        $error = 'Invalid request.';
    }
}
?>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-user me-2"></i>My Profile</h4>
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
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user->username); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user->email); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?php echo ucfirst($user->role); ?>" readonly>
                    </div>
                    
                    <hr>
                    
                    <h6>Change Password (Optional)</h6>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>