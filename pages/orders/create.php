<?php
$page_title = "Create Order";
require_once '../../includes/header.php';
require_once '../../classes/Order.php';
require_once '../../classes/Restaurant.php';
require_once '../../classes/Menu.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$order = new Order();
$restaurant = new Restaurant();
$menu = new Menu();
$error = '';
$success = '';

// Get restaurants
$restaurants_stmt = $restaurant->read('', 100, 0);
$restaurants = $restaurants_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        // Validate required fields
        if (empty($_POST['restaurant_id']) || empty($_POST['customer_name']) || empty($_POST['order_items'])) {
            $error = 'Please fill in all required fields and add at least one item.';
        } else {
            $order->restaurant_id = cleanInput($_POST['restaurant_id']);
            $order->customer_name = cleanInput($_POST['customer_name']);
            $order->customer_phone = cleanInput($_POST['customer_phone']);
            $order->customer_email = cleanInput($_POST['customer_email']);
            $order->status = 'pending';
            
            // Calculate total amount
            $total = 0;
            $order_items = [];
            
            foreach ($_POST['order_items'] as $item) {
                if ($item['quantity'] > 0) {
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                    $order_items[] = [
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }
            }
            
            $order->total_amount = $total;

            if ($order->create()) {
                $order->addOrderItems($order_items);
                $success = 'Order created successfully!';
                $_POST = array();
            } else {
                $error = 'Failed to create order. Please try again.';
            }
        }
    } else {
        $error = 'Invalid request.';
    }
}
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus me-2"></i>Create New Order</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <a href="index.php" class="alert-link">View all orders</a>
                    </div>
                <?php endif; ?>

                <form method="POST" id="orderForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <!-- Customer Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary-custom mb-3">Customer Information</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name *</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                   value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="customer_phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                   value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                   value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Restaurant Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary-custom mb-3">Restaurant</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="restaurant_id" class="form-label">Select Restaurant *</label>
                            <select class="form-select" id="restaurant_id" name="restaurant_id" required onchange="loadMenuItems()">
                                <option value="">Choose Restaurant</option>
                                <?php foreach ($restaurants as $rest): ?>
                                <option value="<?php echo $rest['id']; ?>" 
                                        <?php echo (isset($_POST['restaurant_id']) && $_POST['restaurant_id'] == $rest['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rest['name']); ?> - <?php echo htmlspecialchars($rest['cuisine_type']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary-custom mb-3">Order Items</h5>
                            <div id="menuItems">
                                <p class="text-muted">Please select a restaurant first to view menu items.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6 ms-auto">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Order Summary</h6>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Amount:</strong>
                                        <strong id="orderTotal" class="text-success">$0.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadMenuItems() {
    const restaurantId = document.getElementById('restaurant_id').value;
    const menuItemsDiv = document.getElementById('menuItems');
    
    if (!restaurantId) {
        menuItemsDiv.innerHTML = '<p class="text-muted">Please select a restaurant first to view menu items.</p>';
        return;
    }
    
    // Show loading
    menuItemsDiv.innerHTML = '<div class="text-center"><div class="loading"></div> Loading menu items...</div>';
    
    // Fetch menu items via AJAX
    fetch(`../menu/get_items.php?restaurant_id=${restaurantId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                menuItemsDiv.innerHTML = '<p class="text-muted">No menu items found for this restaurant.</p>';
                return;
            }
            
            let html = '<div class="row">';
            data.forEach((item, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title">${item.name}</h6>
                                        <p class="card-text small text-muted">${item.description || ''}</p>
                                        <span class="badge bg-secondary">${item.category_name}</span>
                                        <strong class="text-success ms-2">$${parseFloat(item.price).toFixed(2)}</strong>
                                    </div>
                                    <div class="text-end">
                                        <input type="hidden" name="order_items[${index}][menu_item_id]" value="${item.id}">
                                        <input type="hidden" name="order_items[${index}][price]" value="${item.price}">
                                        <label class="form-label small">Qty</label>
                                        <input type="number" class="form-control form-control-sm quantity-input" 
                                               name="order_items[${index}][quantity]" 
                                               min="0" max="99" value="0" 
                                               style="width: 60px;" 
                                               onchange="calculateTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            menuItemsDiv.innerHTML = html;
            calculateTotal();
        })
        .catch(error => {
            console.error('Error:', error);
            menuItemsDiv.innerHTML = '<div class="alert alert-danger">Error loading menu items. Please try again.</div>';
        });
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.quantity-input').forEach(input => {
        const quantity = parseInt(input.value) || 0;
        const price = parseFloat(input.closest('.card-body').querySelector('input[name*="[price]"]').value) || 0;
        total += quantity * price;
    });
    
    document.getElementById('orderTotal').textContent = '$' + total.toFixed(2);
}
</script>

<?php require_once '../../includes/footer.php'; ?>