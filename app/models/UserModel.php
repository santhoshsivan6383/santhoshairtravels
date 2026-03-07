<?php
/**
 * User Model
 * Handles user-related database operations
 */
class UserModel extends Model {
    protected $table = 'users';

    /**
     * Authenticate user with username and password
     */
    public function authenticate($username, $password) {
        $user = $this->findOne(['username' => $username]);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Get all users (for display, without passwords)
     */
    public function getAllUsers() {
        $result = $this->db->query("SELECT id, username, email, created_at FROM {$this->table} ORDER BY id ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashed]);
    }
}
?>