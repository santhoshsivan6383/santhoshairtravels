<?php
/**
 * Santhosh Air Travels - Configuration
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'santhosh_travels');

// App Configuration
define('BASE_URL', '/santhosh-airtravels/');
define('APP_PATH', __DIR__ . '/app/');
define('TEMPLATE_DIR', __DIR__ . '/templates/');
define('FPDF_PATH', __DIR__ . '/fpdf/fpdf.php');

// Format currency
function formatCurrency($amount) {
    return '₹' . number_format($amount, 2, '.', ',');
}
?>
