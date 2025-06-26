<?php
// Determine the correct path to database.php
$db_path = __DIR__ . '/../config/database.php';
if (!file_exists($db_path)) {
    $db_path = 'config/database.php';
}
require_once $db_path;

class Order {
    private $conn;
    private $table = 'orders';

    public $id;
    public $restaurant_id;
    public $customer_name;
    public $customer_phone;
    public $customer_email;
    public $total_amount;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Create order
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET restaurant_id = :restaurant_id, customer_name = :customer_name, 
                     customer_phone = :customer_phone, customer_email = :customer_email,
                     total_amount = :total_amount, status = :status";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->restaurant_id = htmlspecialchars(strip_tags($this->restaurant_id));
        $this->customer_name = htmlspecialchars(strip_tags($this->customer_name));
        $this->customer_phone = htmlspecialchars(strip_tags($this->customer_phone));
        $this->customer_email = htmlspecialchars(strip_tags($this->customer_email));
        $this->total_amount = htmlspecialchars(strip_tags($this->total_amount));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind parameters
        $stmt->bindParam(':restaurant_id', $this->restaurant_id);
        $stmt->bindParam(':customer_name', $this->customer_name);
        $stmt->bindParam(':customer_phone', $this->customer_phone);
        $stmt->bindParam(':customer_email', $this->customer_email);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Add order items
    public function addOrderItems($items) {
        $query = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        foreach ($items as $item) {
            $stmt->execute([
                $this->id,
                $item['menu_item_id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        return true;
    }

    // Read orders
    public function read($search = '', $status_filter = '', $limit = 10, $offset = 0) {
        $query = "SELECT o.*, r.name as restaurant_name 
                 FROM " . $this->table . " o 
                 LEFT JOIN restaurants r ON o.restaurant_id = r.id 
                 WHERE 1=1";

        if (!empty($search)) {
            $query .= " AND (o.customer_name LIKE :search OR r.name LIKE :search)";
        }

        if (!empty($status_filter)) {
            $query .= " AND o.status = :status";
        }

        $query .= " ORDER BY o.order_date DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }

        if (!empty($status_filter)) {
            $stmt->bindParam(':status', $status_filter);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    // Get single order
    public function readOne() {
        $query = "SELECT o.*, r.name as restaurant_name, r.address as restaurant_address 
                 FROM " . $this->table . " o 
                 LEFT JOIN restaurants r ON o.restaurant_id = r.id 
                 WHERE o.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->restaurant_id = $row['restaurant_id'];
            $this->customer_name = $row['customer_name'];
            $this->customer_phone = $row['customer_phone'];
            $this->customer_email = $row['customer_email'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            return $row;
        }
        return false;
    }

    // Get order items
    public function getOrderItems() {
        $query = "SELECT oi.*, mi.name as item_name 
                 FROM order_items oi 
                 LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id 
                 WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update order status
    public function updateStatus() {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    // Delete order
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Count orders
    public function count($search = '', $status_filter = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " o 
                 LEFT JOIN restaurants r ON o.restaurant_id = r.id 
                 WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (o.customer_name LIKE :search OR r.name LIKE :search)";
        }

        if (!empty($status_filter)) {
            $query .= " AND o.status = :status";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }

        if (!empty($status_filter)) {
            $stmt->bindParam(':status', $status_filter);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>