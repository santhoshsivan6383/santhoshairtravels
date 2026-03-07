<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Santhosh Air Travels</title>
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
                <div class="login-icon" style="background: var(--gradient-warning);">🔐</div>
                <h1>Reset Password</h1>
                <p>Update user password</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="resetForm" onsubmit="App.submitForm('reset'); return false;">
                <div class="form-group">
                    <label class="form-label">Select User</label>
                    <select class="form-select" name="user_id" id="user_id" required>
                        <option value="">-- Select User --</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter new password" required minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;" id="resetBtn">
                    <i class="bi bi-key"></i> Update Password
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center;">
                <a href="#" onclick="App.route('login'); return false;" style="color: var(--primary); font-size: 13px; font-weight: 500;">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
