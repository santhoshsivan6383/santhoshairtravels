<?php
/**
 * Database Migration - Add PDF Storage Support
 * Run this script once to add the pdf_path column to tickets table
 */

include 'config.php';

echo "Starting migration...<br>";

// Check if pdf_path column already exists
$check_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'tickets' AND COLUMN_NAME = 'pdf_path'";
$result = $conn->query($check_query);

if ($result && $result->num_rows > 0) {
    echo "<div style='color: green;'>✓ Column pdf_path already exists. No changes needed.</div>";
} else {
    // Add pdf_path column if it doesn't exist
    $alter_query = "ALTER TABLE tickets ADD COLUMN pdf_path VARCHAR(255) NULL AFTER updated_at";
    
    if ($conn->query($alter_query) === TRUE) {
        echo "<div style='color: green;'>✓ Successfully added pdf_path column to tickets table!</div>";
    } else {
        echo "<div style='color: red;'>✗ Error adding pdf_path column: " . $conn->error . "</div>";
    }
}

// Create pdfs directory if it doesn't exist
$pdf_dir = __DIR__ . '/pdfs';
if (!is_dir($pdf_dir)) {
    if (mkdir($pdf_dir, 0755, true)) {
        echo "<div style='color: green;'>✓ Created /pdfs directory</div>";
    } else {
        echo "<div style='color: red;'>✗ Failed to create /pdfs directory</div>";
    }
} else {
    echo "<div style='color: green;'>✓ /pdfs directory already exists</div>";
}

// Make pdfs directory writable
chmod($pdf_dir, 0755);

// Display completion message
echo "<div style='margin-top: 20px; background: #d4edda; padding: 15px; border-radius: 5px; color: #155724; border: 1px solid #c3e6cb;'>";
echo "<strong>Migration Complete!</strong><br>";
echo "You can now start downloading PDFs. They will be saved to the /pdfs folder.<br>";
echo "This setup page is for informational purposes only. Delete or restrict access to this file in production.";
echo "</div>";

echo "<br><a href='dashboard.php' style='text-decoration: none; color: #667eea;'>← Back to Dashboard</a>";
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database Migration - Santhosh Air Travels</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }
        .container {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        div {
            margin: 15px 0;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Database Migration</h1>
        <?php // Output from above PHP code ?>
    </div>
</body>
</html>
