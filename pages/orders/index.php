<?php
$page_title = "Orders";
require_once '../../includes/header.php';
require_once '../../classes/Order.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$order = new Order();

// Handle filters
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? cleanInput($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Get orders
$stmt = $order->read($search, $status_filter, $limit, $offset);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$total_orders = $order->count($search, $status_filter);
$total_pages = ceil($total_orders / $limit);

// Status options
$status_options = [
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'preparing' => 'Preparing',
    'ready' => 'Ready',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled'
];
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-shopping-cart me-2"></i>Orders</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>New Order
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="search-container">
    <form method="GET" class="row">
        <div class="col-md-6 mb-2">
            <input type="text" class="form-control" name="search" 
                   placeholder="Search by customer name or restaurant..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4 mb-2">
            <select class="form-select" name="status">
                <option value="">All Status</option>
                <?php foreach ($status_options as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo $status_filter === $value ? 'selected' : ''; ?>>
                    <?php echo $label; ?>
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

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>No orders found</h4>
                <p class="text-muted">Start by creating your first order!</p>
                <a href="create.php" class="btn btn-primary">Create Order</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Restaurant</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo $ord['id']; ?></strong>
                            </td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($ord['customer_name']); ?></strong>
                                    <?php if ($ord['customer_phone']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($ord['customer_phone']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($ord['restaurant_name']); ?></td>
                            <td>
                                <strong class="text-success">$<?php echo number_format($ord['total_amount'], 2); ?></strong>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $ord['status']; ?>">
                                    <?php echo ucfirst($ord['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($ord['order_date'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($ord['order_date'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $ord['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $ord['id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary"
                                       data-bs-toggle="tooltip" title="Edit Order">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $ord['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger delete-btn" 
                                       data-type="order"
                                       data-bs-toggle="tooltip" title="Delete Order">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<nav aria-label="Order pagination" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter ? '&status=' . urlencode($status_filter) : ''; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>