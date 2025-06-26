<?php
require_once '../../config/config.php';
require_once '../../classes/Menu.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['restaurant_id']) || empty($_GET['restaurant_id'])) {
    echo json_encode([]);
    exit;
}

$menu = new Menu();
$restaurant_id = cleanInput($_GET['restaurant_id']);

$stmt = $menu->read($restaurant_id, null, '', 100, 0);
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter only available items
$available_items = array_filter($menu_items, function($item) {
    return $item['availability'] == 1;
});

header('Content-Type: application/json');
echo json_encode(array_values($available_items));
?>