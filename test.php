<<<<<<< HEAD
<?php
/**
 * Quick Test Script - Run this to verify everything works
 */

include 'config.php';

echo "<h1>🧪 Santhosh Air Travels - Quick Test</h1>";

// Test 1: Database connection
echo "<h3>Test 1: Database Connection</h3>";
if ($conn) {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
} else {
    echo "<div style='color: red;'>❌ Database connection failed</div>";
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

// Test 4: Try creating a test ticket
echo "<h3>Test 4: Ticket Creation</h3>";
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

        // Test 5: PDF Download
        echo "<h3>Test 5: PDF Download</h3>";
        $pdf_filename = 'Ticket_' . $test_data['booking_reference'] . '.pdf';
        $pdf_path = $pdf_dir . '/' . $pdf_filename;

        // Generate PDF content
        $html_content = generateTestPDF($test_data);

        if (file_put_contents($pdf_path, $html_content)) {
            echo "<div style='color: green;'>✅ PDF saved successfully: $pdf_filename</div>";
            echo "<div style='color: blue;'>📄 File size: " . filesize($pdf_path) . " bytes</div>";
            echo "<div style='color: blue;'>📂 Location: /pdfs/$pdf_filename</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to save PDF</div>";
        }

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
echo "<li>Go to <a href='dashboard.php'>Dashboard</a> to see your tickets</li>";
echo "<li>Try <a href='add_ticket.php'>adding a new ticket</a></li>";
echo "<li>Download PDFs from the dashboard</li>";
echo "</ol>";
echo "</div>";

function generateTestPDF($ticket) {
    return '<!DOCTYPE html><html><head><title>Test PDF</title></head><body><h1>Test Ticket</h1><p>Booking: ' . $ticket['booking_reference'] . '</p></body></html>';
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h3 { color: #333; }
div { margin: 10px 0; padding: 10px; border-radius: 5px; }
a { color: #667eea; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>5️⃣ Next Steps</h2>";
echo "<ol>
    <li>Make sure <strong>Apache</strong> is running (green in XAMPP)</li>
    <li>Make sure <strong>MySQL</strong> is running (green in XAMPP)</li>
    <li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>
    <li>Go to Import tab and select <strong>db_setup.sql</strong></li>
    <li>Click <strong>Go</strong> to import database</li>
    <li><a href='http://localhost/santhosh-airtravels/login.php'>Try login page →</a></li>
</ol>";
?>
=======
<?php
/**
 * Quick Test Script - Run this to verify everything works
 */

include 'config.php';

echo "<h1>🧪 Santhosh Air Travels - Quick Test</h1>";

// Test 1: Database connection
echo "<h3>Test 1: Database Connection</h3>";
if ($conn) {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
} else {
    echo "<div style='color: red;'>❌ Database connection failed</div>";
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

// Test 4: Try creating a test ticket
echo "<h3>Test 4: Ticket Creation</h3>";
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

        // Test 5: PDF Download
        echo "<h3>Test 5: PDF Download</h3>";
        $pdf_filename = 'Ticket_' . $test_data['booking_reference'] . '.pdf';
        $pdf_path = $pdf_dir . '/' . $pdf_filename;

        // Generate PDF content
        $html_content = generateTestPDF($test_data);

        if (file_put_contents($pdf_path, $html_content)) {
            echo "<div style='color: green;'>✅ PDF saved successfully: $pdf_filename</div>";
            echo "<div style='color: blue;'>📄 File size: " . filesize($pdf_path) . " bytes</div>";
            echo "<div style='color: blue;'>📂 Location: /pdfs/$pdf_filename</div>";
        } else {
            echo "<div style='color: red;'>❌ Failed to save PDF</div>";
        }

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
echo "<li>Go to <a href='dashboard.php'>Dashboard</a> to see your tickets</li>";
echo "<li>Try <a href='add_ticket.php'>adding a new ticket</a></li>";
echo "<li>Download PDFs from the dashboard</li>";
echo "</ol>";
echo "</div>";

function generateTestPDF($ticket) {
    return '<!DOCTYPE html><html><head><title>Test PDF</title></head><body><h1>Test Ticket</h1><p>Booking: ' . $ticket['booking_reference'] . '</p></body></html>';
}
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
h1, h3 { color: #333; }
div { margin: 10px 0; padding: 10px; border-radius: 5px; }
a { color: #667eea; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>5️⃣ Next Steps</h2>";
echo "<ol>
    <li>Make sure <strong>Apache</strong> is running (green in XAMPP)</li>
    <li>Make sure <strong>MySQL</strong> is running (green in XAMPP)</li>
    <li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>
    <li>Go to Import tab and select <strong>db_setup.sql</strong></li>
    <li>Click <strong>Go</strong> to import database</li>
    <li><a href='http://localhost/santhosh-airtravels/login.php'>Try login page →</a></li>
</ol>";
?>
>>>>>>> 698157819f01532cf88f5d177a949515bc2d2ba1
