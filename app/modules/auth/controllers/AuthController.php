<?php
/**
 * Auth Controller
 * Handles login, logout, and password reset
 * 
 * Routes:
 *   /auth/login   → Login page
 *   /auth/logout  → Logout and redirect
 *   /auth/reset   → Reset password page
 */
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    /**
     * Login page & authentication
     * Route: /auth/login
     */
    public function login() {
        // Already logged in? Go to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $error = '';

        if ($this->isPost()) {
            $username = $this->sanitize($this->postData('username'));
            $password = $this->postData('password'); // Don't sanitize passwords

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'] ?? '';
                    $this->setFlash('success', 'Login successful!');
                    $this->redirect('dashboard');
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }

        $this->render('auth/login', [
            'error' => $error,
            'page_title' => 'Login'
        ]);
    }

    /**
     * Logout
     * Route: /auth/logout
     */
    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit;
    }

    /**
     * Reset password page
     * Route: /auth/reset
     */
    public function reset() {
        $error = '';
        $success = '';
        $users = $this->userModel->getAllUsers();

        if ($this->isPost()) {
            $userId = (int)$this->postData('user_id');
            $newPassword = $this->postData('new_password');
            $confirmPassword = $this->postData('confirm_password');

            if ($newPassword !== $confirmPassword) {
                $error = 'Passwords do not match';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Password must be at least 6 characters';
            } else {
                if ($this->userModel->updatePassword($userId, $newPassword)) {
                    $success = 'Password updated successfully!';
                } else {
                    $error = 'Failed to update password';
                }
            }
        }

        $this->render('auth/reset_password', [
            'users'      => $users,
            'error'      => $error,
            'success'    => $success,
            'page_title' => 'Reset Password'
        ]);
    }
}
?>