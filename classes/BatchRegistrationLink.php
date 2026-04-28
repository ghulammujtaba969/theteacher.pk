<?php
require_once 'config/database.php';

class BatchRegistrationLink {
    private $conn;
    private $table_name = "batch_registration_links";

    public $id;
    public $batch_id;
    public $link_token;
    public $link_type;
    public $max_uses;
    public $current_uses;
    public $expires_at;
    public $is_active;
    public $created_by;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new registration link
     */
    public function create() {
        // Generate unique token if not provided
        if (!isset($this->link_token) || empty($this->link_token)) {
            $this->link_token = $this->generateToken();
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET batch_id=:batch_id, link_token=:link_token, 
                      link_type=:link_type, max_uses=:max_uses,
                      current_uses=:current_uses, expires_at=:expires_at,
                      is_active=:is_active, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Set defaults
        if (!isset($this->link_type)) {
            $this->link_type = 'public';
        }
        if (!isset($this->current_uses)) {
            $this->current_uses = 0;
        }
        if (!isset($this->is_active)) {
            $this->is_active = 1;
        }

        // Bind values
        $stmt->bindParam(':batch_id', $this->batch_id);
        $stmt->bindParam(':link_token', $this->link_token);
        $stmt->bindParam(':link_type', $this->link_type);
        $stmt->bindParam(':max_uses', $this->max_uses);
        $stmt->bindParam(':current_uses', $this->current_uses);
        $stmt->bindParam(':expires_at', $this->expires_at);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':created_by', $this->created_by);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get all links for a batch (alias for getLinksByBatch)
     */
    public function getByBatch($batch_id) {
        return $this->getLinksByBatch($batch_id);
    }

    /**
     * Get all links for a batch
     */
    public function getLinksByBatch($batch_id) {
        $query = "SELECT brl.*, 
                         u.full_name as created_by_name,
                         b.batch_name, b.batch_code
                  FROM " . $this->table_name . " brl
                  LEFT JOIN users u ON brl.created_by = u.id
                  LEFT JOIN batches b ON brl.batch_id = b.id
                  WHERE brl.batch_id = :batch_id
                  ORDER BY brl.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get link by token
     */
    public function getByToken($token) {
        $query = "SELECT brl.*, 
                         b.batch_name, b.batch_code, b.enrollment_status, 
                         b.max_students, b.class_id,
                         c.class_name, c.type as class_type
                  FROM " . $this->table_name . " brl
                  LEFT JOIN batches b ON brl.batch_id = b.id
                  LEFT JOIN classes c ON b.class_id = c.id
                  WHERE brl.link_token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->batch_id = $row['batch_id'];
            $this->link_token = $row['link_token'];
            $this->link_type = $row['link_type'];
            $this->max_uses = $row['max_uses'];
            $this->current_uses = $row['current_uses'];
            $this->expires_at = $row['expires_at'];
            $this->is_active = $row['is_active'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return $row;
        }
        return false;
    }

    /**
     * Read single link by ID
     */
    public function readOne() {
        $query = "SELECT brl.*, 
                         u.full_name as created_by_name,
                         b.batch_name, b.batch_code
                  FROM " . $this->table_name . " brl
                  LEFT JOIN users u ON brl.created_by = u.id
                  LEFT JOIN batches b ON brl.batch_id = b.id
                  WHERE brl.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Update link
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET link_type=:link_type, max_uses=:max_uses,
                      expires_at=:expires_at, is_active=:is_active
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':link_type', $this->link_type);
        $stmt->bindParam(':max_uses', $this->max_uses);
        $stmt->bindParam(':expires_at', $this->expires_at);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Delete link
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Validate link for registration
     */
    public function isValid() {
        // Check if active
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This registration link has been deactivated.'];
        }

        // Check if expired
        if ($this->expires_at && strtotime($this->expires_at) < time()) {
            return ['valid' => false, 'message' => 'This registration link has expired.'];
        }

        // Check max uses
        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return ['valid' => false, 'message' => 'This registration link has reached its maximum number of uses.'];
        }

        return ['valid' => true, 'message' => 'Link is valid'];
    }

    /**
     * Increment usage count
     */
    public function incrementUsage() {
        $query = "UPDATE " . $this->table_name . "
                  SET current_uses = current_uses + 1
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Generate unique token
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate full registration URL
     */
    public function getFullUrl() {
        return BASE_URL . 'batch-registration.php?token=' . $this->link_token;
    }

    /**
     * Toggle active status
     */
    public function toggleActive() {
        $query = "UPDATE " . $this->table_name . "
                  SET is_active = NOT is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Get active links count for batch
     */
    public function getActiveLinksCount($batch_id) {
        $query = "SELECT COUNT(*) as count 
                  FROM " . $this->table_name . " 
                  WHERE batch_id = :batch_id 
                  AND is_active = 1
                  AND (expires_at IS NULL OR expires_at > NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] ?? 0;
    }
}
?>