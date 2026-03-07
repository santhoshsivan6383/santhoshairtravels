<?php
/**
 * Ticket Model
 * Handles all ticket-related database operations
 */
class TicketModel extends Model {
    protected $table = 'tickets';

    /**
     * Get all tickets for a specific user
     */
    public function getUserTickets($userId) {
        $stmt = $this->db->prepare(
            "SELECT id, client_name, client_email, departure_city, arrival_city, 
                    departure_date, booking_reference, ticket_price, total_price, status, created_at 
             FROM {$this->table} 
             WHERE user_id = ? 
             ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Get Monthly Ticket Stats Data for Chart
     */
    public function getMonthlyTicketStats($userId) {
        // Last 6 months including current
        $data = [
            'labels' => [],
            'confirmed' => [],
            'pending' => []
        ];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));
            
            // Confirmed tickets count
            $stmtC = $this->db->prepare("SELECT COUNT(id) as cnt FROM {$this->table} WHERE user_id = ? AND status = 'confirmed' AND created_at BETWEEN ? AND ?");
            $monthEndWithTime = $monthEnd . ' 23:59:59';
            $monthStartWithTime = $monthStart . ' 00:00:00';
            $stmtC->bind_param("iss", $userId, $monthStartWithTime, $monthEndWithTime);
            $stmtC->execute();
            $confRes = $stmtC->get_result()->fetch_assoc();
            $stmtC->close();
            
            // Pending tickets count
            $stmtP = $this->db->prepare("SELECT COUNT(id) as cnt FROM {$this->table} WHERE user_id = ? AND status = 'pending' AND created_at BETWEEN ? AND ?");
            $stmtP->bind_param("iss", $userId, $monthStartWithTime, $monthEndWithTime);
            $stmtP->execute();
            $pendRes = $stmtP->get_result()->fetch_assoc();
            $stmtP->close();
            
            $data['labels'][] = $monthLabel;
            $data['confirmed'][] = (int)$confRes['cnt'];
            $data['pending'][] = (int)$pendRes['cnt'];
        }
        
        return $data;
    }

    /**
     * Get tickets with pagination and search
     */
    public function getPaginatedUserTickets($userId, $page, $limit, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $searchQuery = "";
        $bindParams = [$userId];
        $bindTypes = "i";
        
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $searchQuery = " AND (client_name LIKE ? OR booking_reference LIKE ? OR departure_city LIKE ? OR arrival_city LIKE ?)";
            array_push($bindParams, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $bindTypes .= "ssss";
        }
        
        // Count total matching records
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ? {$searchQuery}";
        $stmtC = $this->db->prepare($countSql);
        $stmtC->bind_param($bindTypes, ...$bindParams);
        $stmtC->execute();
        $totalRecords = $stmtC->get_result()->fetch_assoc()['total'];
        $stmtC->close();
        
        $totalPages = ceil($totalRecords / $limit);
        
        // Fetch matching records
        $dataSql = "SELECT id, client_name, client_email, departure_city, arrival_city, 
                    departure_date, booking_reference, ticket_price, total_price, status, created_at 
                    FROM {$this->table} WHERE user_id = ? {$searchQuery} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        // Add limit and offset params
        $bindTypes .= "ii";
        array_push($bindParams, $limit, $offset);
        
        $stmtD = $this->db->prepare($dataSql);
        $stmtD->bind_param($bindTypes, ...$bindParams);
        $stmtD->execute();
        $data = $stmtD->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmtD->close();
        
        return [
            'data' => $data,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages
        ];
    }

    /**
     * Get all tickets for a specific user for Export
     */
    public function getAllUserTicketsForExport($userId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Get a ticket ensuring it belongs to the user
     */
    public function getTicketForUser($ticketId, $userId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticketId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    /**
     * Get ticket statistics for a user
     */
    public function getStats($userId) {
        $stats = [];
        $stats['total'] = $this->count(['user_id' => $userId]);
        $stats['confirmed'] = $this->count(['user_id' => $userId, 'status' => 'confirmed']);
        $stats['pending'] = $this->count(['user_id' => $userId, 'status' => 'pending']);

        // Revenue
        $result = $this->query(
            "SELECT COALESCE(SUM(total_price), 0) as revenue FROM {$this->table} WHERE user_id = ?",
            [$userId], 'i'
        );
        $row = $result->fetch_assoc();
        $stats['revenue'] = $row['revenue'];

        return $stats;
    }

    /**
     * Generate unique booking reference
     */
    public function generateBookingRef() {
        do {
            $ref = 'SA' . date('ymd') . rand(1000, 9999);
            $existing = $this->findOne(['booking_reference' => $ref]);
        } while ($existing);
        return $ref;
    }

    /**
     * Create a new ticket
     */
    public function createTicket($data) {
        // Auto-generate booking reference
        if (empty($data['booking_reference'])) {
            $data['booking_reference'] = $this->generateBookingRef();
        }
        // Calculate total price
        if (empty($data['total_price'])) {
            $data['total_price'] = ($data['passenger_count'] ?? 1) * ($data['ticket_price'] ?? 0);
        }
        // Default status
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }
        return $this->create($data);
    }

    /**
     * Update a ticket with user ownership check
     */
    public function updateTicket($ticketId, $userId, $data) {
        // Build update query
        $parts = [];
        $params = [];
        $types = '';

        foreach ($data as $key => $value) {
            $parts[] = "$key = ?";
            $params[] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }

        // WHERE clause
        $params[] = $ticketId;
        $params[] = $userId;
        $types .= 'ii';

        $sql = "UPDATE {$this->table} SET " . implode(', ', $parts) . " WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Delete a ticket with user ownership check
     */
    public function deleteTicket($ticketId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $ticketId, $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    /**
     * Get list of airports
     */
    public function getAirports() {
        $result = $this->db->query("SELECT * FROM airports ORDER BY city_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get list of airlines
     */
    public function getAirlines() {
        $result = $this->db->query("SELECT * FROM airlines ORDER BY name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Create new airport
     */
    public function createAirport($city, $iata) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO airports (city_name, iata_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $city, $iata);
        $result = $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return $id ? $id : true;
    }

    /**
     * Create new airline
     */
    public function createAirline($name) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO airlines (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        return $id ? $id : true;
    }
}
?>