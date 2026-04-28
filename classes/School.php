<?php
require_once 'config/database.php';

class School {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO schools (organization_id, name, description, address, phone, email, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            if ($stmt->execute([
                $data['organization_id'],
                $data['name'],
                $data['description'],
                $data['address'],
                $data['phone'],
                $data['email'],
                $data['status'] ?? 'active'
            ])) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating school: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll($organization_id = null) {
        try {
            if ($organization_id) {
                $query = "SELECT s.*, o.name as organization_name,
                         (SELECT COUNT(*) FROM users WHERE school_id = s.id) as user_count,
                         (SELECT u.id FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_user_id,
                         (SELECT u.username FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_username,
                         (SELECT u.email FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_email,
                         (SELECT u.full_name FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_full_name,
                         (SELECT GROUP_CONCAT(ucp.class_id) 
                          FROM user_class_permissions ucp
                          WHERE ucp.user_id = (
                              SELECT u.id FROM users u 
                              WHERE u.school_id = s.id 
                              AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                              LIMIT 1
                          )) as admin_class_ids
                         FROM schools s 
                         JOIN organizations o ON s.organization_id = o.id 
                         WHERE s.organization_id = ?
                         ORDER BY s.name";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$organization_id]);
            } else {
                $query = "SELECT s.*, o.name as organization_name,
                         (SELECT COUNT(*) FROM users WHERE school_id = s.id) as user_count,
                         (SELECT u.id FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_user_id,
                         (SELECT u.username FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_username,
                         (SELECT u.email FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_email,
                         (SELECT u.full_name FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1) as admin_full_name,
                         (SELECT GROUP_CONCAT(ucp.class_id) 
                          FROM user_class_permissions ucp
                          WHERE ucp.user_id = (
                              SELECT u.id FROM users u 
                              WHERE u.school_id = s.id 
                              AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                              LIMIT 1
                          )) as admin_class_ids
                         FROM schools s 
                         JOIN organizations o ON s.organization_id = o.id 
                         ORDER BY o.name, s.name";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching schools: " . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT s.*, o.name as organization_name,
                     (SELECT u.id FROM users u 
                      WHERE u.school_id = s.id 
                      AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                      LIMIT 1) as admin_user_id,
                     (SELECT u.username FROM users u 
                      WHERE u.school_id = s.id 
                      AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                      LIMIT 1) as admin_username,
                     (SELECT u.email FROM users u 
                      WHERE u.school_id = s.id 
                      AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                      LIMIT 1) as admin_email,
                     (SELECT u.full_name FROM users u 
                      WHERE u.school_id = s.id 
                      AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                      LIMIT 1) as admin_full_name,
                     (SELECT GROUP_CONCAT(ucp.class_id) 
                      FROM user_class_permissions ucp
                      WHERE ucp.user_id = (
                          SELECT u.id FROM users u 
                          WHERE u.school_id = s.id 
                          AND u.role_id = (SELECT id FROM roles WHERE name = 'School Admin' LIMIT 1)
                          LIMIT 1
                      )) as admin_class_ids
                     FROM schools s 
                     JOIN organizations o ON s.organization_id = o.id 
                     WHERE s.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching school: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE schools 
                     SET organization_id = ?, name = ?, description = ?, address = ?, phone = ?, email = ?, status = ?
                     WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['organization_id'],
                $data['name'],
                $data['description'],
                $data['address'],
                $data['phone'],
                $data['email'],
                $data['status'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating school: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $query = "DELETE FROM schools WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting school: " . $e->getMessage());
            return false;
        }
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) FROM schools WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting school count: " . $e->getMessage());
            return 0;
        }
    }

    public function getCountByOrganization($organization_id) {
        try {
            $query = "SELECT COUNT(*) FROM schools WHERE organization_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$organization_id]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting school count by organization: " . $e->getMessage());
            return 0;
        }
    }
}
?>
