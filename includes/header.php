<?php
// Determine the correct path to config.php based on current location
$config_path = __DIR__ . '/../config/config.php';
if (!file_exists($config_path)) {
    $config_path = 'config/config.php';
}
require_once $config_path;

// Check if we're on login or register pages
$current_page = basename($_SERVER['PHP_SELF']);
$auth_pages = ['login.php', 'register.php'];
$is_auth_page = in_array($current_page, $auth_pages);

// Determine the correct base path for navigation links
// Use absolute paths from the site URL for more reliability
$app_base = '/restaurantapp/restaurant';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $app_base; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="<?php echo $is_auth_page ? 'auth-page' : ''; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $app_base; ?>/index.php">
                <i class="fas fa-utensils me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <?php if (!$is_auth_page): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $app_base; ?>/index.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $app_base; ?>/pages/restaurants/index.php">
                            <i class="fas fa-store me-1"></i>Restaurants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $app_base; ?>/pages/menu/index.php">
                            <i class="fas fa-list me-1"></i>Menu Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $app_base; ?>/pages/orders/index.php">
                            <i class="fas fa-shopping-cart me-1"></i>Orders
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo $app_base; ?>/pages/profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $app_base; ?>/pages/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $app_base; ?>/pages/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container-fluid py-4">