<?php
/**
 * Quick Test Script - Run this to verify everything works
 */

require_once __DIR__ . '/config.php';

echo "<h1>🧪 Santhosh Air Travels - Quick Test</h1>";

// Test 1: Database connection
echo "<h3>Test 1: Database Connection</h3>";
if (isset($conn) && $conn) {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
} else {
    echo "<div style='color: red;'>❌ Database connection failed. Please check config.php</div>";
    exit;
}

// Test 2: Check if pdf_path column exists
echo "<h3>Test 2: Database Schema</h3>";
$check_query = "SHOW COLUMNS FROM tickets LIKE 'pdf_path'";
$result = $conn->query($check_query);
if ($result && $result->num_rows > 0) {
    echo "<div style='color: green;'>✅ pdf_path column exists</div>";
} else {
    echo "<div style='color: orange;'>⚠️ pdf_path column missing (run setup.php)</div>";
}

// Test 3: Check /pdfs directory
echo "<h3>Test 3: File System</h3>";
$pdf_dir = __DIR__ . '/pdfs';
if (is_dir($pdf_dir)) {
    echo "<div style='color: green;'>✅ /pdfs directory exists</div>";
    if (is_writable($pdf_dir)) {
        echo "<div style='color: green;'>✅ /pdfs directory is writable</div>";
    } else {
        echo "<div style='color: red;'>❌ /pdfs directory is not writable</div>";
    }
} else {
    echo "<div style='color: red;'>❌ /pdfs directory missing (run setup.php)</div>";
}

// Test 4: Dynamic Path Detection
echo "<h3>Test 4: Dynamic Path Detection</h3>";
echo "<div style='background: #eee; padding: 10px;'>";
echo "<strong>Detected Base URL:</strong> " . BASE_URL . "<br>";
echo "<strong>Detected App Path:</strong> " . APP_PATH . "<br>";
echo "</div>";

// Test 5: Try creating a test ticket
echo "<h3>Test 5: Ticket Creation</h3>";
$user_id = 1; // Assuming admin user exists
$test_data = [
    'client_name' => 'Test User',
    'client_email' => 'test@example.com',
    'client_phone' => '9876543210',
    'departure_city' => 'Chennai',
    'arrival_city' => 'Mumbai',
    'departure_date' => date('Y-m-d'),
    'departure_time' => '10:00:00',
    'arrival_time' => '12:00:00',
    'airline_name' => 'Test Airlines',
    'flight_number' => 'TA101',
    'passenger_count' => 1,
    'ticket_price' => 5000.00,
    'total_price' => 5000.00,
    'booking_reference' => 'TEST' . time(),
    'status' => 'confirmed',
    'notes' => 'Test booking'
];

$query = "INSERT INTO tickets (user_id, client_name, client_email, client_phone, departure_city, arrival_city, departure_date, departure_time, arrival_time, airline_name, flight_number, passenger_count, ticket_price, total_price, booking_reference, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("isssssssssiddss",
        $user_id,
        $test_data['client_name'],
        $test_data['client_email'],
        $test_data['client_phone'],
        $test_data['departure_city'],
        $test_data['arrival_city'],
        $test_data['departure_date'],
        $test_data['departure_time'],
        $test_data['arrival_time'],
        $test_data['airline_name'],
        $test_data['flight_number'],
        $test_data['passenger_count'],
        $test_data['ticket_price'],
        $test_data['total_price'],
        $test_data['booking_reference'],
        $test_data['status'],
        $test_data['notes']
    );

    if ($stmt->execute()) {
        $ticket_id = $conn->insert_id;
        echo "<div style='color: green;'>✅ Test ticket created successfully (ID: $ticket_id)</div>";

        // Clean up test ticket
        $conn->query("DELETE FROM tickets WHERE id = $ticket_id");
        echo "<div style='color: blue;'>🧹 Test ticket cleaned up</div>";

    } else {
        echo "<div style='color: red;'>❌ Failed to create test ticket: " . $stmt->error . "</div>";
    }
    $stmt->close();
} else {
    echo "<div style='color: red;'>❌ Failed to prepare statement: " . $conn->error . "</div>";
}

$conn->close();

echo "<hr>";
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px;'>";
echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li>If any tests failed, run <a href='setup.php'>setup.php</a> first</li>";
echo "<li>Go to <a href='" . BASE_URL . "Dashboard'>Dashboard</a> to see your tickets</li>";
echo "<li>Try <a href='" . BASE_URL . "login'>Login page</a></li>";
echo "</ol>";
echo "</div>";

?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; line-height: 1.6; }
h1, h3 { color: #333; }
div { margin: 10px 0; padding: 10px; border-radius: 5px; }
a { color: #667eea; text-decoration: none; font-weight: bold; }
a:hover { text-decoration: underline; }
</style>
