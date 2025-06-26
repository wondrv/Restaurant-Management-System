<?php
$page_title = "Menu Items";
require_once '../../includes/header.php';
require_once '../../classes/Menu.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$menu = new Menu();
$restaurant = new Restaurant();

// Handle filters
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$restaurant_filter = isset($_GET['restaurant']) ? cleanInput($_GET['restaurant']) : '';
$category_filter = isset($_GET['category']) ? cleanInput($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Get menu items
$stmt = $menu->read($restaurant_filter ?: null, $category_filter ?: null, $search, $limit, $offset);
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get restaurants for filter
$restaurants_stmt = $restaurant->read('', 100, 0);
$restaurants = $restaurants_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories_stmt = $menu->getCategories();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-utensils me-2"></i>Menu Items</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Menu Item
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="search-container">
    <form method="GET" class="row">
        <div class="col-md-4 mb-2">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search menu items..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" name="restaurant">
                <option value="">All Restaurants</option>
                <?php foreach ($restaurants as $rest): ?>
                <option value="<?php echo $rest['id']; ?>" <?php echo $restaurant_filter == $rest['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($rest['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Menu Items -->
<div class="row">
    <?php if (empty($menu_items)): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                <h4>No menu items found</h4>
                <p class="text-muted">Start by adding your first menu item!</p>
                <a href="create.php" class="btn btn-primary">Add Menu Item</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($menu_items as $item): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card item-card">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <?php if ($item['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="img-fluid" style="max-height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-utensils fa-3x text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="fas fa-store me-1"></i>
                            <?php echo htmlspecialchars($item['restaurant_name']); ?>
                        </small>
                    </p>
                    <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
                    <p class="card-text">
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($item['category_name']); ?></span>
                        <span class="fw-bold text-primary ms-2">$<?php echo number_format($item['price'], 2); ?></span>
                        <?php if ($item['availability']): ?>
                            <span class="badge bg-success ms-2">Available</span>
                        <?php else: ?>
                            <span class="badge bg-danger ms-2">Unavailable</span>
                        <?php endif; ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <a href="view.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-sm btn-outline-danger delete-btn" 
                               data-type="menu item">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input availability-toggle" 
                                   type="checkbox" 
                                   data-id="<?php echo $item['id']; ?>"
                                   <?php echo $item['availability'] ? 'checked' : ''; ?>>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>