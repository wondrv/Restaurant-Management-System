<?php
require_once '../config/config.php';
require_once '../classes/Menu.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_POST && validateCSRFToken($_POST['csrf_token'])) {
    $menu = new Menu();
    $menu->id = cleanInput($_POST['id']);
    $menu->availability = cleanInput($_POST['availability']);
    
    // Get current item data
    if ($menu->readOne()) {
        // Update availability
        if ($menu->update()) {
            echo json_encode(['success' => true, 'message' => 'Availability updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update availability']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>