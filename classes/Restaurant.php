<?php
// Determine the correct path to database.php
$db_path = __DIR__ . '/../config/database.php';
if (!file_exists($db_path)) {
    $db_path = 'config/database.php';
}
require_once $db_path;

class Restaurant {
    private $conn;
    private $table = 'restaurants';

    public $id;
    public $name;
    public $address;
    public $phone;
    public $email;
    public $cuisine_type;
    public $rating;
    public $image_url;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Create restaurant
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET name = :name, address = :address, phone = :phone, 
                     email = :email, cuisine_type = :cuisine_type, 
                     rating = :rating, image_url = :image_url";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->cuisine_type = htmlspecialchars(strip_tags($this->cuisine_type));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));

        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':cuisine_type', $this->cuisine_type);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':image_url', $this->image_url);

        return $stmt->execute();
    }

    // Read all restaurants
    public function read($search = '', $limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE name LIKE :search OR cuisine_type LIKE :search OR address LIKE :search";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt;
    }

    // Get single restaurant
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->address = $row['address'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->cuisine_type = $row['cuisine_type'];
            $this->rating = $row['rating'];
            $this->image_url = $row['image_url'];
            return true;
        }
        return false;
    }

    // Update restaurant
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, address = :address, phone = :phone, 
                     email = :email, cuisine_type = :cuisine_type, 
                     rating = :rating, image_url = :image_url 
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->cuisine_type = htmlspecialchars(strip_tags($this->cuisine_type));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':cuisine_type', $this->cuisine_type);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete restaurant
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Count total restaurants
    public function count($search = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        if (!empty($search)) {
            $query .= " WHERE name LIKE :search OR cuisine_type LIKE :search OR address LIKE :search";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(':search', $search_param);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>