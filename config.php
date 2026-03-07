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
$base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (substr($base_path, -1) !== '/') $base_path .= '/';
define('BASE_URL', $base_path);
define('APP_PATH', __DIR__ . '/app/');
define('TEMPLATE_DIR', __DIR__ . '/templates/');
define('FPDF_PATH', __DIR__ . '/fpdf/fpdf.php');

// Format currency
function formatCurrency($amount) {
    return '₹' . number_format($amount, 2, '.', ',');
}
?>
