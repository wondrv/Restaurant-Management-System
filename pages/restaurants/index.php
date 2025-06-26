<?php
$page_title = "Restaurants";
require_once '../../includes/header.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$restaurant = new Restaurant();

// Handle search and pagination
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Get restaurants
$stmt = $restaurant->read($search, $limit, $offset);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$total_restaurants = $restaurant->count($search);
$total_pages = ceil($total_restaurants / $limit);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-store me-2"></i>Restaurants</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Restaurant
            </a>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="search-container">
    <form method="GET" class="row">
        <div class="col-md-10">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search restaurants by name, cuisine, or address..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>
</div>

<!-- Restaurants Grid -->
<div class="row">
    <?php if (empty($restaurants)): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                <h4>No restaurants found</h4>
                <p class="text-muted">Start by adding your first restaurant!</p>
                <a href="create.php" class="btn btn-primary">Add Restaurant</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($restaurants as $rest): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card item-card">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <?php if ($rest['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($rest['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($rest['name']); ?>" 
                             class="img-fluid" style="max-height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-store fa-3x text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($rest['name']); ?></h5>
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <?php echo htmlspecialchars($rest['address']); ?>
                        </small>
                    </p>
                    <p class="card-text">
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($rest['cuisine_type']); ?></span>
                        <span class="rating ms-2">
                            <i class="fas fa-star"></i>
                            <?php echo number_format($rest['rating'], 1); ?>
                        </span>
                    </p>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group" role="group">
                            <a href="view.php?id=<?php echo $rest['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit.php?id=<?php echo $rest['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $rest['id']; ?>" 
                               class="btn btn-sm btn-outline-danger delete-btn" 
                               data-type="restaurant">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-phone me-1"></i>
                                <?php echo htmlspecialchars($rest['phone']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<nav aria-label="Restaurant pagination">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>