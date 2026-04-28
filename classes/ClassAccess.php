<?php

require_once __DIR__ . '/../config/database.php';

class ClassAccess {
    private $conn;
    private $table_name = "user_class_permissions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function assignAccess($user_id, $class_id, $granted_by) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (user_id, class_id, granted_by) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id, $class_id, $granted_by]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                return true; // Already has access, consider it a success
            }
            error_log("Error assigning class access: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAccess($user_id, $class_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ? AND class_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id, $class_id]);
        } catch (PDOException $e) {
            error_log("Error revoking class access: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAllAccessForUser($user_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error revoking all class access for user: " . $e->getMessage());
            return false;
        }
    }

    public function getPermissionsByUser($user_id) {
        $query = "SELECT ucp.*, c.class_name, c.class_code, u.username as granted_by_username
                  FROM " . $this->table_name . " ucp
                  JOIN classes c ON ucp.class_id = c.id
                  LEFT JOIN users u ON ucp.granted_by = u.id
                  WHERE ucp.user_id = ?
                  ORDER BY c.class_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserClassIds($user_id) {
        $query = "SELECT class_id FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

?>
