<?php
require_once 'config/database.php';

class Organization {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO organizations (name, description, address, phone, email, status) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['address'],
                $data['phone'],
                $data['email'],
                $data['status'] ?? 'active'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating organization: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll() {
        try {
            $query = "SELECT o.*, 
                     (SELECT COUNT(*) FROM schools WHERE organization_id = o.id) as school_count,
                     (SELECT COUNT(*) FROM users WHERE organization_id = o.id) as user_count
                     FROM organizations o 
                     ORDER BY o.name";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching organizations: " . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT * FROM organizations WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching organization: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE organizations 
                     SET name = ?, description = ?, address = ?, phone = ?, email = ?, status = ?
                     WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['address'],
                $data['phone'],
                $data['email'],
                $data['status'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating organization: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $query = "DELETE FROM organizations WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting organization: " . $e->getMessage());
            return false;
        }
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) FROM organizations WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting organization count: " . $e->getMessage());
            return 0;
        }
    }
}
?>
