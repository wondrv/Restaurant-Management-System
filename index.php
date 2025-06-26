<?php
$page_title = "Dashboard";
require_once 'includes/header.php';
require_once 'classes/Restaurant.php';
require_once 'classes/Menu.php';
require_once 'classes/Order.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('pages/login.php');
}

// Get statistics
$restaurant = new Restaurant();
$menu = new Menu();

// Get counts
$total_restaurants = $restaurant->count();
$total_menu_items = $menu->count();

// Get database connection for custom queries
$database = new Database();
$conn = $database->connect();

// Get total orders
$query = "SELECT COUNT(*) as total FROM orders";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get total revenue
$query = "SELECT SUM(total_amount) as revenue FROM orders WHERE status != 'cancelled'";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?: 0;

// Get recent orders
$query = "SELECT o.*, r.name as restaurant_name FROM orders o 
          LEFT JOIN restaurants r ON o.restaurant_id = r.id 
          ORDER BY o.order_date DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get top restaurants by rating
$query = "SELECT * FROM restaurants ORDER BY rating DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$top_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            <small class="text-muted">Welcome back, <?php echo $_SESSION['username']; ?>!</small>
        </h1>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3><?php echo number_format($total_restaurants); ?></h3>
                    <p class="mb-0">Total Restaurants</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3><?php echo number_format($total_menu_items); ?></h3>
                    <p class="mb-0">Menu Items</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3><?php echo number_format($total_orders); ?></h3>
                    <p class="mb-0">Total Orders</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card info">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p class="mb-0">Total Revenue</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Recent Orders
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_orders)): ?>
                    <p class="text-muted text-center">No orders found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Restaurant</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="pages/orders/" class="btn btn-outline-primary">View All Orders</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Top Restaurants -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>Top Restaurants
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_restaurants)): ?>
                    <p class="text-muted text-center">No restaurants found</p>
                <?php else: ?>
                    <?php foreach ($top_restaurants as $rest): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($rest['name']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($rest['cuisine_type']); ?></small>
                        </div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <?php echo number_format($rest['rating'], 1); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="pages/restaurants/" class="btn btn-outline-primary">View All Restaurants</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="pages/restaurants/create.php" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i>Add Restaurant
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/menu/create.php" class="btn btn-success w-100">
                            <i class="fas fa-plus me-1"></i>Add Menu Item
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/orders/create.php" class="btn btn-warning w-100">
                            <i class="fas fa-plus me-1"></i>New Order
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="pages/reports.php" class="btn btn-info w-100">
                            <i class="fas fa-chart-bar me-1"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>