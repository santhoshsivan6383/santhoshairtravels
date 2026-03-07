<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database WITHOUT session
$conn = new mysqli('localhost', 'root', '', 'santhosh_travels');

if ($conn->connect_error) {
    die("❌ Database Error: " . $conn->connect_error);
}

$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $message = '❌ Please fill all fields!';
    } elseif ($new_password !== $confirm_password) {
        $message = '❌ Passwords do not match!';
    } elseif (strlen($new_password) < 6) {
        $message = '❌ Password must be at least 6 characters!';
    } else {
        // Hash the password with bcrypt
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        // Update admin password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->bind_param("s", $hashed_password);
        
        if ($stmt->execute()) {
            $success = '✅ Admin password updated successfully!<br>
            Username: <strong>admin</strong><br>
            Password: <strong>' . htmlspecialchars($new_password) . '</strong><br><br>
            Now <a href="login.php">go to login page →</a>';
        } else {
            $message = '❌ Error updating password: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password - Santhosh Air Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            padding: 50px;
            width: 100%;
            max-width: 400px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #999;
            font-size: 12px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f0f4ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="header">
            <h1>🔐 Reset Admin Password</h1>
            <p>Set a new password for admin account</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" placeholder="Enter new password" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="btn btn-reset">Reset Password</button>
        </form>

        <div class="info-box">
            <strong>💡 Tip:</strong> Use a strong password with mix of letters, numbers, and special characters for security.
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #667eea; text-decoration: none;">← Back to Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
