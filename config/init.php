<?php
// config/init.php
session_start();

// Set timezone - update this to your timezone
date_default_timezone_set('Asia/Manila'); // Change this to your timezone

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Dynamically determine base URL based on server
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if (strpos($scriptPath, '~') !== false) {
    // Server with user directory (e.g., ~ibrahim.dasuki)
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . substr($scriptPath, 0, strrpos($scriptPath, '/')));
} else {
    // Local development or standard installation
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/e_commerce_labs');
}

// Include settings/core.php for helper functions
require_once BASE_PATH . '/settings/core.php';

// Include database connection
require_once BASE_PATH . '/classes/db_connection.php';

// Include all class files
require_once BASE_PATH . '/classes/customer_class.php';
require_once BASE_PATH . '/classes/order_class.php';
require_once BASE_PATH . '/classes/product_class.php';
require_once BASE_PATH . '/classes/brand_class.php';
require_once BASE_PATH . '/classes/category_class.php';

// Include all controller files
require_once BASE_PATH . '/controllers/customer_controller.php';
require_once BASE_PATH . '/controllers/product_controller.php';
require_once BASE_PATH . '/controllers/brand_controller.php';
require_once BASE_PATH . '/controllers/category_controller.php';

// Initialize classes
$database = new DBConnection();
$pdo = $database->getPDO();
$order = new Order();
$product = new Product();
$brand = new Brand();
$category = new Category();
$customer = new Customer();

// Helper functions
function redirect($location) {
    header("Location: $location");
    exit;
}

// Flash message helper
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}