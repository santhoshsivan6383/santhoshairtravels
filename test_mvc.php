<?php
/**
 * MVC Framework Test
 * Test the routing and basic functionality
 */

// Include the main index.php to test routing
echo "<h1>Santhosh Air Travels MVC Framework Test</h1>";

// Test autoloading
echo "<h2>Testing Autoloading:</h2>";
try {
    $controller = new TicketController();
    echo "✓ TicketController loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Error loading TicketController: " . $e->getMessage() . "<br>";
}

try {
    $model = new TicketModel();
    echo "✓ TicketModel loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Error loading TicketModel: " . $e->getMessage() . "<br>";
}

try {
    $userModel = new UserModel();
    echo "✓ UserModel loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Error loading UserModel: " . $e->getMessage() . "<br>";
}

// Test database connection
echo "<h2>Testing Database Connection:</h2>";
require_once 'config.php';
if ($conn) {
    echo "✓ Database connection successful<br>";
    $conn->close();
} else {
    echo "✗ Database connection failed<br>";
}

// Test routing URLs
echo "<h2>Test URLs:</h2>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "' target='_blank'>Home (Auth/Login)</a></li>";
echo "<li><a href='" . BASE_URL . "auth/login' target='_blank'>Login Page</a></li>";
echo "<li><a href='" . BASE_URL . "ticket/dashboard' target='_blank'>Dashboard (requires login)</a></li>";
echo "<li><a href='" . BASE_URL . "ticket/add' target='_blank'>Add Ticket (requires login)</a></li>";
echo "</ul>";

echo "<p><strong>Note:</strong> Dashboard and Add Ticket links require user authentication.</p>";
?>