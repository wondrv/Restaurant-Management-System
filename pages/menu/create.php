<?php
$page_title = "Add Menu Item";
require_once '../../includes/header.php';
require_once '../../classes/Menu.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$menu = new Menu();
$restaurant = new Restaurant();
$error = '';
$success = '';

// Get restaurants and categories
$restaurants_stmt = $restaurant->read('', 100, 0);
$restaurants = $restaurants_stmt->fetchAll(PDO::FETCH_ASSOC);

$categories_stmt = $menu->getCategories();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $menu->restaurant_id = cleanInput($_POST['restaurant_id']);
        $menu->category_id = cleanInput($_POST['category_id']);
        $menu->name = cleanInput($_POST['name']);
        $menu->description = cleanInput($_POST['description']);
        $menu->price = cleanInput($_POST['price']);
        $menu->image_url = cleanInput($_POST['image_url']);
        $menu->availability = isset($_POST['availability']) ? 1 : 0;

        if ($menu->create()) {
            $success = 'Menu item added successfully!';
            $_POST = array();
        } else {
            $error = 'Failed to add menu item. Please try again.';
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
                <h4><i class="fas fa-plus me-2"></i>Add New Menu Item</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <a href="index.php" class="alert-link">View all menu items</a>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="restaurant_id" class="form-label">Restaurant *</label>
                            <select class="form-select" id="restaurant_id" name="restaurant_id" required>
                                <option value="">Select Restaurant</option>
                                <?php foreach ($restaurants as $rest): ?>
                                <option value="<?php echo $rest['id']; ?>" 
                                        <?php echo (isset($_POST['restaurant_id']) && $_POST['restaurant_id'] == $rest['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rest['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price" 
                                       step="0.01" min="0" 
                                       value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="availability" name="availability" 
                                   <?php echo (isset($_POST['availability']) || !isset($_POST['name'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="availability">
                                Available for order
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Menu Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>