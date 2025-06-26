<?php
// Session configuration (must be set before session_start())
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site configuration (define early for functions.php security check)
define('SITE_NAME', 'Restaurant Management System');
define('SITE_URL', 'http://localhost/restaurantapp/restaurant');
define('UPLOAD_PATH', '../assets/images/uploads/');

// Include database connection
require_once 'database.php';

// Include helper functions
$functions_path = __DIR__ . '/../includes/functions.php';
if (file_exists($functions_path)) {
    require_once $functions_path;
} else {
    // Fallback path
    require_once 'includes/functions.php';
}
// Pagination settings
define('ITEMS_PER_PAGE', 10);

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('UTC');