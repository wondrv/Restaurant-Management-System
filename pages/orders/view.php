<?php
$page_title = "View Order";
require_once '../../includes/header.php';
require_once '../../classes/Order.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$order = new Order();

// Get order ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$order->id = cleanInput($_GET['id']);

// Get order data
$order_data = $order->readOne();
if (!$order_data) {
    redirect('index.php');
}

// Get order items
$order_items = $order->getOrderItems();

// Status options for quick update
$status_options = [
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'preparing' => 'Preparing',
    'ready' => 'Ready',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled'
];

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $order->status = cleanInput($_POST['status']);
        if ($order->updateStatus()) {
            $_SESSION['message'] = 'Order status updated successfully!';
            $_SESSION['message_type'] = 'success';
            redirect('view.php?id=' . $order->id);
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-receipt me-2"></i>Order #<?php echo $order->id; ?></h1>
            <div>
                <a href="edit.php?id=<?php echo $order->id; ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Edit Order
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['message'])): ?>
<div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
    <?php echo $_SESSION['message']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['message']);
unset($_SESSION['message_type']);
endif; ?>

<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Customer Details</h6>
                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order_data['customer_name']); ?></p>
                        <?php if ($order_data['customer_phone']): ?>
                        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order_data['customer_phone']); ?></p>
                        <?php endif; ?>
                        <?php if ($order_data['customer_email']): ?>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order_data['customer_email']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Restaurant Details</h6>
                        <p class="mb-1"><strong>Restaurant:</strong> <?php echo htmlspecialchars($order_data['restaurant_name']); ?></p>
                        <p class="mb-1"><strong>Address:</strong> <?php echo htmlspecialchars($order_data['restaurant_address']); ?></p>
                        <p class="mb-1"><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order_data['order_date'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i>Order Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($order_items)): ?>
                    <p class="text-muted">No items found for this order.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $order_total = 0;
                                foreach ($order_items as $item): 
                                    $item_total = $item['price'] * $item['quantity'];
                                    $order_total += $item_total;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><strong>$<?php echo number_format($item_total, 2); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <th colspan="3">Total Amount</th>
                                    <th>$<?php echo number_format($order_data['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Status & Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tasks me-2"></i>Order Status</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="status-badge status-<?php echo $order_data['status']; ?>" style="font-size: 1.1rem; padding: 0.75rem 1.5rem;">
                        <?php echo ucfirst($order_data['status']); ?>
                    </span>
                </div>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label for="status" class="form-label">Update Status</label>
                        <select class="form-select" id="status" name="status">
                            <?php foreach ($status_options as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $order_data['status'] === $value ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt me-1"></i>Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-calculator me-2"></i>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($order_data['total_amount'], 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax (0%):</span>
                    <span>$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Delivery Fee:</span>
                    <span>$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <strong class="text-success">$<?php echo number_format($order_data['total_amount'], 2); ?></strong>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="fas fa-print me-1"></i>Print Order
                    </button>
                    <a href="mailto:<?php echo htmlspecialchars($order_data['customer_email']); ?>" class="btn btn-outline-success">
                        <i class="fas fa-envelope me-1"></i>Email Customer
                    </a>
                    <a href="tel:<?php echo htmlspecialchars($order_data['customer_phone']); ?>" class="btn btn-outline-info">
                        <i class="fas fa-phone me-1"></i>Call Customer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>