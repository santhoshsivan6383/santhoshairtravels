<?php
/**
 * Base Model
 * All models extend this class
 * Handles database connection and common CRUD operations
 */
class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db->connect_error) {
            die('Database connection failed: ' . $this->db->connect_error);
        }
        $this->db->set_charset("utf8");
    }

    /**
     * Find a record by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    /**
     * Get all records
     */
    public function all($orderBy = 'id DESC') {
        $result = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Find records by conditions
     */
    public function where($conditions = [], $orderBy = 'id DESC') {
        $where = '';
        $params = [];
        $types = '';

        if (!empty($conditions)) {
            $parts = [];
            foreach ($conditions as $key => $value) {
                $parts[] = "$key = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            }
            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} $where ORDER BY $orderBy");
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Find one record by conditions
     */
    public function findOne($conditions = []) {
        $results = $this->where($conditions);
        return $results[0] ?? null;
    }

    /**
     * Insert a record
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';
        $types = '';
        $values = [];

        foreach ($data as $value) {
            $values[] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $id = $this->db->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }

    /**
     * Update a record by ID
     */
    public function update($id, $data) {
        $parts = [];
        $params = [];
        $types = '';

        foreach ($data as $key => $value) {
            $parts[] = "$key = ?";
            $params[] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }

        $params[] = $id;
        $types .= 'i';

        $stmt = $this->db->prepare("UPDATE {$this->table} SET " . implode(', ', $parts) . " WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Delete a record by ID
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        $where = '';
        $params = [];
        $types = '';

        if (!empty($conditions)) {
            $parts = [];
            foreach ($conditions as $key => $value) {
                $parts[] = "$key = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            }
            $where = 'WHERE ' . implode(' AND ', $parts);
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM {$this->table} $where");
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['cnt'];
    }

    /**
     * Run a custom query
     */
    public function query($sql, $params = [], $types = '') {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    /**
     * Get last DB error
     */
    public function getError() {
        return $this->db->error;
    }

    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}
?>