<?php
$page_title = "Reports & Analytics";
require_once '../includes/header.php';
require_once '../classes/Restaurant.php';
require_once '../classes/Menu.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get database connection for custom queries
$database = new Database();
$conn = $database->connect();

// Get date range filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Sales by restaurant
$query = "SELECT r.name, COUNT(o.id) as order_count, SUM(o.total_amount) as total_revenue
          FROM restaurants r 
          LEFT JOIN orders o ON r.id = o.restaurant_id 
          AND o.order_date BETWEEN :start_date AND :end_date
          AND o.status != 'cancelled'
          GROUP BY r.id, r.name 
          ORDER BY total_revenue DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$restaurant_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Popular menu items
$query = "SELECT mi.name, mi.price, COUNT(oi.id) as order_count, SUM(oi.quantity) as total_quantity
          FROM menu_items mi
          LEFT JOIN order_items oi ON mi.id = oi.menu_item_id
          LEFT JOIN orders o ON oi.order_id = o.id
          WHERE o.order_date BETWEEN :start_date AND :end_date
          AND o.status != 'cancelled'
          GROUP BY mi.id, mi.name, mi.price
          ORDER BY total_quantity DESC
          LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$popular_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Order status summary
$query = "SELECT status, COUNT(*) as count, SUM(total_amount) as total_amount
          FROM orders 
          WHERE order_date BETWEEN :start_date AND :end_date
          GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$status_summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Daily sales trend
$query = "SELECT DATE(order_date) as order_day, COUNT(*) as order_count, SUM(total_amount) as daily_revenue
          FROM orders 
          WHERE order_date BETWEEN :start_date AND :end_date
          AND status != 'cancelled'
          GROUP BY DATE(order_date)
          ORDER BY order_day";
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$daily_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h1>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="search-container">
    <form method="GET" class="row">
        <div class="col-md-4 mb-2">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
        </div>
        <div class="col-md-4 mb-2">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
        </div>
        <div class="col-md-4 mb-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter me-1"></i>Apply Filter
            </button>
        </div>
    </form>
</div>

<div class="row">
    <!-- Restaurant Performance -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-store me-2"></i>Restaurant Performance</h5>
            </div>
            <div class="card-body">
                <?php if (empty($restaurant_sales)): ?>
                    <p class="text-muted">No data available for the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Restaurant</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($restaurant_sales as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['name']); ?></td>
                                    <td><?php echo $sale['order_count']; ?></td>
                                    <td>$<?php echo number_format($sale['total_revenue'] ?: 0, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Popular Items -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-star me-2"></i>Popular Menu Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($popular_items)): ?>
                    <p class="text-muted">No data available for the selected period.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['total_quantity'] ?: 0; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Order Status Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tasks me-2"></i>Order Status Summary</h5>
            </div>
            <div class="card-body">
                <?php if (empty($status_summary)): ?>
                    <p class="text-muted">No orders found for the selected period.</p>
                <?php else: ?>
                    <?php foreach ($status_summary as $status): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="status-badge status-<?php echo $status['status']; ?>">
                            <?php echo ucfirst($status['status']); ?>
                        </span>
                        <div class="text-end">
                            <div><?php echo $status['count']; ?> orders</div>
                            <small class="text-muted">$<?php echo number_format($status['total_amount'], 2); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Daily Sales Trend</h5>
            </div>
            <div class="card-body">
                <?php if (empty($daily_sales)): ?>
                    <p class="text-muted">No sales data available for the selected period.</p>
                <?php else: ?>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for sales visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($daily_sales)): ?>
// Prepare data for chart
const salesData = {
    labels: [<?php echo "'" . implode("','", array_column($daily_sales, 'order_day')) . "'"; ?>],
    datasets: [{
        label: 'Daily Revenue',
        data: [<?php echo implode(',', array_column($daily_sales, 'daily_revenue')); ?>],
        borderColor: 'rgb(255, 107, 53)',
        backgroundColor: 'rgba(255, 107, 53, 0.1)',
        tension: 0.1
    }]
};

const config = {
    type: 'line',
    data: salesData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
};

new Chart(document.getElementById('salesChart'), config);
<?php endif; ?>
</script>

<?php require_once '../includes/footer.php'; ?>