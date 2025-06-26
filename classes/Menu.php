<?php
// Determine the correct path to database.php
$db_path = __DIR__ . '/../config/database.php';
if (!file_exists($db_path)) {
    $db_path = 'config/database.php';
}
require_once $db_path;

class Menu {
    private $conn;
    private $table = 'menu_items';

    public $id;
    public $restaurant_id;
    public $category_id;
    public $name;
    public $description;
    public $price;
    public $image_url;
    public $availability;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Create menu item
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET restaurant_id = :restaurant_id, category_id = :category_id, 
                     name = :name, description = :description, price = :price, 
                     image_url = :image_url, availability = :availability";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->restaurant_id = htmlspecialchars(strip_tags($this->restaurant_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->availability = htmlspecialchars(strip_tags($this->availability));

        // Bind parameters
        $stmt->bindParam(':restaurant_id', $this->restaurant_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':availability', $this->availability);

        return $stmt->execute();
    }

    // Read menu items
    public function read($restaurant_id = null, $category_id = null, $search = '', $limit = 10, $offset = 0) {
        $query = "SELECT m.*, r.name as restaurant_name, c.name as category_name 
                 FROM " . $this->table . " m 
                 LEFT JOIN restaurants r ON m.restaurant_id = r.id 
                 LEFT JOIN categories c ON m.category_id = c.id 
                 WHERE 1=1";

        if ($restaurant_id) {
            $query .= " AND m.restaurant_id = :restaurant_id";
        }

        if ($category_id) {
            $query .= " AND m.category_id = :category_id";
        }

        if (!empty($search)) {
            $query .= " AND (m.name LIKE :search OR m.description LIKE :search)";
        }

        $query .= " ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if ($restaurant_id) {
            $stmt->bindParam(':restaurant_id', $restaurant_id);
        }

        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    // Get single menu item
    public function readOne() {
        $query = "SELECT m.*, r.name as restaurant_name, c.name as category_name 
                 FROM " . $this->table . " m 
                 LEFT JOIN restaurants r ON m.restaurant_id = r.id 
                 LEFT JOIN categories c ON m.category_id = c.id 
                 WHERE m.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->restaurant_id = $row['restaurant_id'];
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image_url = $row['image_url'];
            $this->availability = $row['availability'];
            return true;
        }
        return false;
    }

    // Update menu item
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET restaurant_id = :restaurant_id, category_id = :category_id, 
                     name = :name, description = :description, price = :price, 
                     image_url = :image_url, availability = :availability 
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->restaurant_id = htmlspecialchars(strip_tags($this->restaurant_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->availability = htmlspecialchars(strip_tags($this->availability));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(':restaurant_id', $this->restaurant_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':availability', $this->availability);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete menu item
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Get categories
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Count total menu items
    public function count($search = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE name LIKE :search OR description LIKE :search";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $search_term = '%' . $search . '%';
            $stmt->bindParam(':search', $search_term);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>