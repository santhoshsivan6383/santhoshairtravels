<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Santhosh Air Travels</title>
    <meta name="description" content="Login to Santhosh Air Travels Ticket Management System">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✈️</text></svg>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script>
        const BASE_URL = '<?php echo rtrim(BASE_URL, '/') . '/'; ?>';
        const App = {
            route: function(routeName) {
                window.location.href = BASE_URL + routeName;
            },
            submitForm: function(routeName) {
                let form = document.querySelector('form');
                form.action = BASE_URL + routeName;
                form.submit();
                return false;
            }
        };
    </script>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">✈</div>
                <h1>Santhosh Air Travels</h1>
                <p>Ticket Management System</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm" onsubmit="App.submitForm('login'); return false;">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>
            <div style="margin-top: 16px; text-align: center;">
                <a href="#" onclick="App.route('reset'); return false;" style="color: var(--primary); font-size: 13px; font-weight: 500;">
                    <i class="bi bi-key"></i> Reset Password
                </a>
            </div>
        </div>
    </div>
</body>
</html>
