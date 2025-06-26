<?php
$page_title = "Add Restaurant";
require_once '../../includes/header.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $restaurant = new Restaurant();
        $restaurant->name = cleanInput($_POST['name']);
        $restaurant->address = cleanInput($_POST['address']);
        $restaurant->phone = cleanInput($_POST['phone']);
        $restaurant->email = cleanInput($_POST['email']);
        $restaurant->cuisine_type = cleanInput($_POST['cuisine_type']);
        $restaurant->rating = cleanInput($_POST['rating']);
        $restaurant->image_url = cleanInput($_POST['image_url']);

        if ($restaurant->create()) {
            $success = 'Restaurant added successfully!';
            // Clear form data
            $_POST = array();
        } else {
            $error = 'Failed to add restaurant. Please try again.';
        }
    } else {
        $error = 'Invalid request.';
    }
}
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus me-2"></i>Add New Restaurant</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <a href="index.php" class="alert-link">View all restaurants</a>
                    </div>
                <?php endif; ?>

                <form method="POST" id="restaurantForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cuisine_type" class="form-label">Cuisine Type *</label>
                            <select class="form-select" id="cuisine_type" name="cuisine_type" required>
                                <option value="">Select Cuisine Type</option>
                                <option value="Italian" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Italian') ? 'selected' : ''; ?>>Italian</option>
                                <option value="Chinese" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Chinese') ? 'selected' : ''; ?>>Chinese</option>
                                <option value="Mexican" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Mexican') ? 'selected' : ''; ?>>Mexican</option>
                                <option value="Japanese" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Japanese') ? 'selected' : ''; ?>>Japanese</option>
                                <option value="American" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'American') ? 'selected' : ''; ?>>American</option>
                                <option value="Indian" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Indian') ? 'selected' : ''; ?>>Indian</option>
                                <option value="French" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'French') ? 'selected' : ''; ?>>French</option>
                                <option value="Mediterranean" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Mediterranean') ? 'selected' : ''; ?>>Mediterranean</option>
                                <option value="BBQ" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'BBQ') ? 'selected' : ''; ?>>BBQ</option>
                                <option value="Vegetarian" <?php echo (isset($_POST['cuisine_type']) && $_POST['cuisine_type'] === 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating">
                                <option value="0">No Rating</option>
                                <option value="1" <?php echo (isset($_POST['rating']) && $_POST['rating'] === '1') ? 'selected' : ''; ?>>1 Star</option>
                                <option value="2" <?php echo (isset($_POST['rating']) && $_POST['rating'] === '2') ? 'selected' : ''; ?>>2 Stars</option>
                                <option value="3" <?php echo (isset($_POST['rating']) && $_POST['rating'] === '3') ? 'selected' : ''; ?>>3 Stars</option>
                                <option value="4" <?php echo (isset($_POST['rating']) && $_POST['rating'] === '4') ? 'selected' : ''; ?>>4 Stars</option>
                                <option value="5" <?php echo (isset($_POST['rating']) && $_POST['rating'] === '5') ? 'selected' : ''; ?>>5 Stars</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Restaurant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>