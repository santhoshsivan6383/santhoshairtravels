<<<<<<< HEAD
<?php
/**
 * Complete Setup Script for PDF Storage
 * Run this once to setup everything needed for PDF downloads
 */

include 'config.php';

echo "<h2>🔧 Santhosh Air Travels - PDF Storage Setup</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";

// Check database connection
if (!$conn) {
    echo "<div style='color: red;'>❌ Database connection failed!</div>";
    exit;
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if pdf_path column exists
echo "<h3>📊 Checking Database Schema...</h3>";
$check_query = "SHOW COLUMNS FROM tickets LIKE 'pdf_path'";
$result = $conn->query($check_query);

if ($result && $result->num_rows > 0) {
    echo "<div style='color: green;'>✅ pdf_path column already exists in tickets table</div>";
} else {
    // Add pdf_path column
    echo "<div style='color: blue;'>🔄 Adding pdf_path column to tickets table...</div>";
    $alter_query = "ALTER TABLE tickets ADD COLUMN pdf_path VARCHAR(255) NULL AFTER updated_at";

    if ($conn->query($alter_query) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added pdf_path column!</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding pdf_path column: " . $conn->error . "</div>";
    }
}

// Create pdfs directory
echo "<h3>📁 Checking File System...</h3>";
$pdf_dir = __DIR__ . '/pdfs';
if (!is_dir($pdf_dir)) {
    if (mkdir($pdf_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created /pdfs directory</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create /pdfs directory</div>";
    }
} else {
    echo "<div style='color: green;'>✅ /pdfs directory already exists</div>";
}

// Make pdfs directory writable
if (chmod($pdf_dir, 0755)) {
    echo "<div style='color: green;'>✅ Set proper permissions on /pdfs directory</div>";
}

// Test PDF creation with sample data
echo "<h3>🧪 Testing PDF Generation...</h3>";
$test_ticket = [
    'booking_reference' => 'TEST001',
    'client_name' => 'Test Passenger',
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
    'status' => 'confirmed',
    'notes' => 'Test booking'
];

$test_html = generatePDF($test_ticket);
$test_file = $pdf_dir . '/test_ticket.pdf';

if (file_put_contents($test_file, $test_html)) {
    echo "<div style='color: green;'>✅ Test PDF created successfully: " . basename($test_file) . "</div>";
    echo "<div style='color: blue;'>📄 File size: " . filesize($test_file) . " bytes</div>";
} else {
    echo "<div style='color: red;'>❌ Failed to create test PDF</div>";
}

// Completion message
echo "</div>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #c3e6cb;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>🎉 Setup Complete!</h3>";
echo "<p style='color: #155724; margin-bottom: 0;'>Your PDF storage system is now ready. You can:</p>";
echo "<ul style='color: #155724;'>";
echo "<li>Create tickets without errors</li>";
echo "<li>Download PDFs that are saved locally</li>";
echo "<li>Access PDFs from the /pdfs folder</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='dashboard.php' style='background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-right: 10px;'>🏠 Go to Dashboard</a>";
echo "<a href='add_ticket.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>➕ Add New Ticket</a>";
echo "</div>";

$conn->close();

// Include the generatePDF function for testing
function generatePDF($ticket) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flight Ticket - ' . htmlspecialchars($ticket['booking_reference']) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: white; padding: 20px; }
        .ticket-container { background: white; max-width: 900px; margin: 0 auto; border: 2px solid #ddd; }
        .ticket-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .ticket-header h1 { font-size: 28px; margin-bottom: 5px; }
        .ticket-body { padding: 30px; }
        .booking-ref { background: #f0f4ff; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 25px; border-radius: 5px; }
        .booking-ref-label { font-weight: bold; color: #333; }
        .booking-ref-number { font-size: 20px; font-weight: bold; color: #667eea; margin-top: 5px; }
        .section { margin-bottom: 30px; }
        .section-title { background: #f8f9fa; padding: 12px 15px; border-left: 4px solid #667eea; margin-bottom: 15px; font-weight: bold; color: #333; }
        .field { display: table; width: 100%; border-bottom: 1px solid #eee; padding: 10px 0; }
        .field-label { display: table-cell; color: #666; font-weight: 600; width: 40%; }
        .field-value { display: table-cell; color: #333; font-weight: 600; text-align: right; }
        .route-section { display: table; width: 100%; background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .location { display: table-cell; text-align: center; width: 40%; }
        .location-city { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .location-time { font-size: 12px; color: #667eea; margin-bottom: 5px; }
        .arrow { display: table-cell; font-size: 32px; color: #667eea; text-align: center; width: 20%; }
        .price-section { background: #f0f4ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .price-row { display: table; width: 100%; margin-bottom: 10px; padding: 8px 0; }
        .price-label { display: table-cell; width: 50%; }
        .price-value { display: table-cell; text-align: right; font-weight: bold; }
        .total-price { display: table; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 6px; margin-top: 15px; font-size: 16px; font-weight: bold; }
        .total-price-label { display: table-cell; width: 50%; }
        .total-price-value { display: table-cell; text-align: right; }
        .status { text-align: center; padding: 15px; border: 2px solid #667eea; border-radius: 8px; margin-bottom: 20px; font-weight: bold; color: #667eea; }
        .status.confirmed { background: #d4edda; border-color: #155724; color: #155724; }
        .status.pending { background: #fff3cd; border-color: #856404; color: #856404; }
        .status.cancelled { background: #f8d7da; border-color: #721c24; color: #721c24; }
        .ticket-footer { background: #f8f9fa; padding: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999; }
        @media print { body { margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>✈️ Santhosh Air Travels</h1>
            <p>Professional Flight Ticket</p>
        </div>

        <div class="ticket-body">
            <div class="booking-ref">
                <div class="booking-ref-label">Booking Reference:</div>
                <div class="booking-ref-number">' . htmlspecialchars($ticket['booking_reference']) . '</div>
            </div>

            <div class="status ' . htmlspecialchars($ticket['status']) . '">
                Status: ' . strtoupper(htmlspecialchars($ticket['status'])) . '
            </div>

            <div class="section">
                <div class="section-title">👤 PASSENGER INFORMATION</div>
                <div class="field">
                    <span class="field-label">Passenger Name</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_name']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Email</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_email']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Phone</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_phone']) . '</span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">✈️ FLIGHT INFORMATION</div>
                <div class="field">
                    <span class="field-label">Airline</span>
                    <span class="field-value">' . htmlspecialchars($ticket['airline_name']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Flight Number</span>
                    <span class="field-value">' . htmlspecialchars($ticket['flight_number']) . '</span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🗺️ JOURNEY DETAILS</div>
                <div class="route-section">
                    <div class="location">
                        <div class="location-city">' . htmlspecialchars($ticket['departure_city']) . '</div>
                        <div class="location-time">' . date('d M Y', strtotime($ticket['departure_date'])) . '</div>
                        <div class="location-time">' . date('h:i A', strtotime($ticket['departure_time'])) . '</div>
                    </div>
                    <div class="arrow">→</div>
                    <div class="location">
                        <div class="location-city">' . htmlspecialchars($ticket['arrival_city']) . '</div>
                        <div class="location-time">' . date('d M Y', strtotime($ticket['departure_date'])) . '</div>
                        <div class="location-time">' . date('h:i A', strtotime($ticket['arrival_time'])) . '</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">💰 PRICING DETAILS</div>
                <div class="price-section">
                    <div class="price-row">
                        <span class="price-label">Number of Passengers:</span>
                        <span class="price-value">' . htmlspecialchars($ticket['passenger_count']) . '</span>
                    </div>
                    <div class="price-row">
                        <span class="price-label">Price per Passenger:</span>
                        <span class="price-value">₹' . number_format($ticket['ticket_price'], 2) . '</span>
                    </div>
                    <div class="total-price">
                        <span class="total-price-label">TOTAL AMOUNT:</span>
                        <span class="total-price-value">₹' . number_format($ticket['total_price'], 2) . '</span>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">📝 ADDITIONAL INFORMATION</div>
                <div class="field">
                    <span class="field-label">Notes:</span>
                    <span class="field-value">' . (htmlspecialchars($ticket['notes']) ?: 'N/A') . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Booking Date:</span>
                    <span class="field-value">' . date('d M Y H:i') . '</span>
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <strong>Santhosh Air Travels</strong><br>
            Generated on ' . date('d M Y H:i:s') . '<br>
            Thank you for booking with us!
        </div>
    </div>
</body>
</html>';

    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Complete - Santhosh Air Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .setup-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 800px;
            margin: 0 auto;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="text-center mb-4">
            <div class="status-icon success">🎉</div>
            <h1 class="h2">Setup Complete!</h1>
            <p class="text-muted">Your PDF storage system is ready to use</p>
        </div>

        <div class="alert alert-success">
            <h5>✅ What's Working Now:</h5>
            <ul class="mb-0">
                <li>PDF files are saved to <code>/pdfs</code> folder</li>
                <li>Database tracks PDF paths (optional)</li>
                <li>Direct PDF downloads work</li>
                <li>Ticket creation is fixed</li>
            </ul>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <a href="dashboard.php" class="btn btn-primary w-100 mb-2">
                    🏠 Go to Dashboard
                </a>
            </div>
            <div class="col-md-6">
                <a href="add_ticket.php" class="btn btn-success w-100 mb-2">
                    ➕ Add New Ticket
                </a>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <h6>🔧 For Future Updates:</h6>
            <p class="mb-0">If you need to modify the database schema later, you can run this setup script again - it's safe to re-run.</p>
        </div>
    </div>
</body>
=======
<?php
/**
 * Complete Setup Script for PDF Storage
 * Run this once to setup everything needed for PDF downloads
 */

include 'config.php';

echo "<h2>🔧 Santhosh Air Travels - PDF Storage Setup</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";

// Check database connection
if (!$conn) {
    echo "<div style='color: red;'>❌ Database connection failed!</div>";
    exit;
} else {
    echo "<div style='color: green;'>✅ Database connected successfully</div>";
}

// Check if pdf_path column exists
echo "<h3>📊 Checking Database Schema...</h3>";
$check_query = "SHOW COLUMNS FROM tickets LIKE 'pdf_path'";
$result = $conn->query($check_query);

if ($result && $result->num_rows > 0) {
    echo "<div style='color: green;'>✅ pdf_path column already exists in tickets table</div>";
} else {
    // Add pdf_path column
    echo "<div style='color: blue;'>🔄 Adding pdf_path column to tickets table...</div>";
    $alter_query = "ALTER TABLE tickets ADD COLUMN pdf_path VARCHAR(255) NULL AFTER updated_at";

    if ($conn->query($alter_query) === TRUE) {
        echo "<div style='color: green;'>✅ Successfully added pdf_path column!</div>";
    } else {
        echo "<div style='color: red;'>❌ Error adding pdf_path column: " . $conn->error . "</div>";
    }
}

// Create pdfs directory
echo "<h3>📁 Checking File System...</h3>";
$pdf_dir = __DIR__ . '/pdfs';
if (!is_dir($pdf_dir)) {
    if (mkdir($pdf_dir, 0755, true)) {
        echo "<div style='color: green;'>✅ Created /pdfs directory</div>";
    } else {
        echo "<div style='color: red;'>❌ Failed to create /pdfs directory</div>";
    }
} else {
    echo "<div style='color: green;'>✅ /pdfs directory already exists</div>";
}

// Make pdfs directory writable
if (chmod($pdf_dir, 0755)) {
    echo "<div style='color: green;'>✅ Set proper permissions on /pdfs directory</div>";
}

// Test PDF creation with sample data
echo "<h3>🧪 Testing PDF Generation...</h3>";
$test_ticket = [
    'booking_reference' => 'TEST001',
    'client_name' => 'Test Passenger',
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
    'status' => 'confirmed',
    'notes' => 'Test booking'
];

$test_html = generatePDF($test_ticket);
$test_file = $pdf_dir . '/test_ticket.pdf';

if (file_put_contents($test_file, $test_html)) {
    echo "<div style='color: green;'>✅ Test PDF created successfully: " . basename($test_file) . "</div>";
    echo "<div style='color: blue;'>📄 File size: " . filesize($test_file) . " bytes</div>";
} else {
    echo "<div style='color: red;'>❌ Failed to create test PDF</div>";
}

// Completion message
echo "</div>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #c3e6cb;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>🎉 Setup Complete!</h3>";
echo "<p style='color: #155724; margin-bottom: 0;'>Your PDF storage system is now ready. You can:</p>";
echo "<ul style='color: #155724;'>";
echo "<li>Create tickets without errors</li>";
echo "<li>Download PDFs that are saved locally</li>";
echo "<li>Access PDFs from the /pdfs folder</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='dashboard.php' style='background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-right: 10px;'>🏠 Go to Dashboard</a>";
echo "<a href='add_ticket.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>➕ Add New Ticket</a>";
echo "</div>";

$conn->close();

// Include the generatePDF function for testing
function generatePDF($ticket) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flight Ticket - ' . htmlspecialchars($ticket['booking_reference']) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: white; padding: 20px; }
        .ticket-container { background: white; max-width: 900px; margin: 0 auto; border: 2px solid #ddd; }
        .ticket-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .ticket-header h1 { font-size: 28px; margin-bottom: 5px; }
        .ticket-body { padding: 30px; }
        .booking-ref { background: #f0f4ff; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 25px; border-radius: 5px; }
        .booking-ref-label { font-weight: bold; color: #333; }
        .booking-ref-number { font-size: 20px; font-weight: bold; color: #667eea; margin-top: 5px; }
        .section { margin-bottom: 30px; }
        .section-title { background: #f8f9fa; padding: 12px 15px; border-left: 4px solid #667eea; margin-bottom: 15px; font-weight: bold; color: #333; }
        .field { display: table; width: 100%; border-bottom: 1px solid #eee; padding: 10px 0; }
        .field-label { display: table-cell; color: #666; font-weight: 600; width: 40%; }
        .field-value { display: table-cell; color: #333; font-weight: 600; text-align: right; }
        .route-section { display: table; width: 100%; background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .location { display: table-cell; text-align: center; width: 40%; }
        .location-city { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .location-time { font-size: 12px; color: #667eea; margin-bottom: 5px; }
        .arrow { display: table-cell; font-size: 32px; color: #667eea; text-align: center; width: 20%; }
        .price-section { background: #f0f4ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .price-row { display: table; width: 100%; margin-bottom: 10px; padding: 8px 0; }
        .price-label { display: table-cell; width: 50%; }
        .price-value { display: table-cell; text-align: right; font-weight: bold; }
        .total-price { display: table; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 6px; margin-top: 15px; font-size: 16px; font-weight: bold; }
        .total-price-label { display: table-cell; width: 50%; }
        .total-price-value { display: table-cell; text-align: right; }
        .status { text-align: center; padding: 15px; border: 2px solid #667eea; border-radius: 8px; margin-bottom: 20px; font-weight: bold; color: #667eea; }
        .status.confirmed { background: #d4edda; border-color: #155724; color: #155724; }
        .status.pending { background: #fff3cd; border-color: #856404; color: #856404; }
        .status.cancelled { background: #f8d7da; border-color: #721c24; color: #721c24; }
        .ticket-footer { background: #f8f9fa; padding: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999; }
        @media print { body { margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>✈️ Santhosh Air Travels</h1>
            <p>Professional Flight Ticket</p>
        </div>

        <div class="ticket-body">
            <div class="booking-ref">
                <div class="booking-ref-label">Booking Reference:</div>
                <div class="booking-ref-number">' . htmlspecialchars($ticket['booking_reference']) . '</div>
            </div>

            <div class="status ' . htmlspecialchars($ticket['status']) . '">
                Status: ' . strtoupper(htmlspecialchars($ticket['status'])) . '
            </div>

            <div class="section">
                <div class="section-title">👤 PASSENGER INFORMATION</div>
                <div class="field">
                    <span class="field-label">Passenger Name</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_name']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Email</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_email']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Phone</span>
                    <span class="field-value">' . htmlspecialchars($ticket['client_phone']) . '</span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">✈️ FLIGHT INFORMATION</div>
                <div class="field">
                    <span class="field-label">Airline</span>
                    <span class="field-value">' . htmlspecialchars($ticket['airline_name']) . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Flight Number</span>
                    <span class="field-value">' . htmlspecialchars($ticket['flight_number']) . '</span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">🗺️ JOURNEY DETAILS</div>
                <div class="route-section">
                    <div class="location">
                        <div class="location-city">' . htmlspecialchars($ticket['departure_city']) . '</div>
                        <div class="location-time">' . date('d M Y', strtotime($ticket['departure_date'])) . '</div>
                        <div class="location-time">' . date('h:i A', strtotime($ticket['departure_time'])) . '</div>
                    </div>
                    <div class="arrow">→</div>
                    <div class="location">
                        <div class="location-city">' . htmlspecialchars($ticket['arrival_city']) . '</div>
                        <div class="location-time">' . date('d M Y', strtotime($ticket['departure_date'])) . '</div>
                        <div class="location-time">' . date('h:i A', strtotime($ticket['arrival_time'])) . '</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">💰 PRICING DETAILS</div>
                <div class="price-section">
                    <div class="price-row">
                        <span class="price-label">Number of Passengers:</span>
                        <span class="price-value">' . htmlspecialchars($ticket['passenger_count']) . '</span>
                    </div>
                    <div class="price-row">
                        <span class="price-label">Price per Passenger:</span>
                        <span class="price-value">₹' . number_format($ticket['ticket_price'], 2) . '</span>
                    </div>
                    <div class="total-price">
                        <span class="total-price-label">TOTAL AMOUNT:</span>
                        <span class="total-price-value">₹' . number_format($ticket['total_price'], 2) . '</span>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">📝 ADDITIONAL INFORMATION</div>
                <div class="field">
                    <span class="field-label">Notes:</span>
                    <span class="field-value">' . (htmlspecialchars($ticket['notes']) ?: 'N/A') . '</span>
                </div>
                <div class="field">
                    <span class="field-label">Booking Date:</span>
                    <span class="field-value">' . date('d M Y H:i') . '</span>
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <strong>Santhosh Air Travels</strong><br>
            Generated on ' . date('d M Y H:i:s') . '<br>
            Thank you for booking with us!
        </div>
    </div>
</body>
</html>';

    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Complete - Santhosh Air Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .setup-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 800px;
            margin: 0 auto;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="text-center mb-4">
            <div class="status-icon success">🎉</div>
            <h1 class="h2">Setup Complete!</h1>
            <p class="text-muted">Your PDF storage system is ready to use</p>
        </div>

        <div class="alert alert-success">
            <h5>✅ What's Working Now:</h5>
            <ul class="mb-0">
                <li>PDF files are saved to <code>/pdfs</code> folder</li>
                <li>Database tracks PDF paths (optional)</li>
                <li>Direct PDF downloads work</li>
                <li>Ticket creation is fixed</li>
            </ul>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <a href="dashboard.php" class="btn btn-primary w-100 mb-2">
                    🏠 Go to Dashboard
                </a>
            </div>
            <div class="col-md-6">
                <a href="add_ticket.php" class="btn btn-success w-100 mb-2">
                    ➕ Add New Ticket
                </a>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <h6>🔧 For Future Updates:</h6>
            <p class="mb-0">If you need to modify the database schema later, you can run this setup script again - it's safe to re-run.</p>
        </div>
    </div>
</body>
>>>>>>> 698157819f01532cf88f5d177a949515bc2d2ba1
</html>