<?php
require_once '../../config/config.php';
require_once '../../classes/Restaurant.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Get restaurant ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$restaurant = new Restaurant();
$restaurant->id = cleanInput($_GET['id']);

// Delete restaurant
if ($restaurant->delete()) {
    $_SESSION['message'] = 'Restaurant deleted successfully!';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Failed to delete restaurant.';
    $_SESSION['message_type'] = 'danger';
}

redirect('index.php');
?>