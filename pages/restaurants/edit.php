<?php
$page_title = "Edit Restaurant";
require_once '../../includes/header.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$restaurant = new Restaurant();
$error = '';
$success = '';

// Get restaurant ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$restaurant->id = cleanInput($_GET['id']);

// Get restaurant data
if (!$restaurant->readOne()) {
    redirect('index.php');
}

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $restaurant->name = cleanInput($_POST['name']);
        $restaurant->address = cleanInput($_POST['address']);
        $restaurant->phone = cleanInput($_POST['phone']);
        $restaurant->email = cleanInput($_POST['email']);
        $restaurant->cuisine_type = cleanInput($_POST['cuisine_type']);
        $restaurant->rating = cleanInput($_POST['rating']);
        $restaurant->image_url = cleanInput($_POST['image_url']);

        if ($restaurant->update()) {
            $success = 'Restaurant updated successfully!';
        } else {
            $error = 'Failed to update restaurant. Please try again.';
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
                <h4><i class="fas fa-edit me-2"></i>Edit Restaurant</h4>
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

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($restaurant->name); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cuisine_type" class="form-label">Cuisine Type *</label>
                            <select class="form-select" id="cuisine_type" name="cuisine_type" required>
                                <option value="">Select Cuisine Type</option>
                                <option value="Italian" <?php echo $restaurant->cuisine_type === 'Italian' ? 'selected' : ''; ?>>Italian</option>
                                <option value="Chinese" <?php echo $restaurant->cuisine_type === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                                <option value="Mexican" <?php echo $restaurant->cuisine_type === 'Mexican' ? 'selected' : ''; ?>>Mexican</option>
                                <option value="Japanese" <?php echo $restaurant->cuisine_type === 'Japanese' ? 'selected' : ''; ?>>Japanese</option>
                                <option value="American" <?php echo $restaurant->cuisine_type === 'American' ? 'selected' : ''; ?>>American</option>
                                <option value="Indian" <?php echo $restaurant->cuisine_type === 'Indian' ? 'selected' : ''; ?>>Indian</option>
                                <option value="French" <?php echo $restaurant->cuisine_type === 'French' ? 'selected' : ''; ?>>French</option>
                                <option value="Mediterranean" <?php echo $restaurant->cuisine_type === 'Mediterranean' ? 'selected' : ''; ?>>Mediterranean</option>
                                <option value="BBQ" <?php echo $restaurant->cuisine_type === 'BBQ' ? 'selected' : ''; ?>>BBQ</option>
                                <option value="Vegetarian" <?php echo $restaurant->cuisine_type === 'Vegetarian' ? 'selected' : ''; ?>>Vegetarian</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($restaurant->address); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($restaurant->phone); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($restaurant->email); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating">
                                <option value="0" <?php echo $restaurant->rating == 0 ? 'selected' : ''; ?>>No Rating</option>
                                <option value="1" <?php echo $restaurant->rating == 1 ? 'selected' : ''; ?>>1 Star</option>
                                <option value="2" <?php echo $restaurant->rating == 2 ? 'selected' : ''; ?>>2 Stars</option>
                                <option value="3" <?php echo $restaurant->rating == 3 ? 'selected' : ''; ?>>3 Stars</option>
                                <option value="4" <?php echo $restaurant->rating == 4 ? 'selected' : ''; ?>>4 Stars</option>
                                <option value="5" <?php echo $restaurant->rating == 5 ? 'selected' : ''; ?>>5 Stars</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($restaurant->image_url); ?>">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Restaurant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>