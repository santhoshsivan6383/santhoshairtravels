<?php
/**
 * Base Controller
 * All controllers extend this class
 */
class Controller {

    public function __construct() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login - redirect if not authenticated
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }

    /**
     * Get current user ID
     */
    protected function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current username
     */
    protected function getUsername() {
        return $_SESSION['username'] ?? '';
    }

    /**
     * Render a .tpl template file with data
     */
    protected function render($templateName, $data = []) {
        // Make data available as variables
        extract($data);

        // Make common variables available
        $BASE_URL = BASE_URL;
        $currentUser = $this->getUsername();
        $isLoggedIn = $this->isLoggedIn();

        $templatePath = TEMPLATE_DIR . $templateName . '.tpl';
        if (!file_exists($templatePath)) {
            die("Template '$templateName' not found at: $templatePath");
        }

        include $templatePath;
    }

    /**
     * Redirect to a route
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Sanitize input data
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Set a flash message
     */
    protected function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * Get and clear a flash message
     */
    protected function getFlash($key) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    /**
     * Check if POST request
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get POST data
     */
    protected function postData($key, $default = '') {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Get GET data
     */
    protected function getData($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}
?>