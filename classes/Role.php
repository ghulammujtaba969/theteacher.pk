<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Role Model — extended with permission management helpers.
 */
class Role {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // =========================================================
    // BASIC ROLE CRUD
    // =========================================================

    public function getAll() {
        try {
            $stmt = $this->db->query(
                "SELECT r.*,
                        COUNT(DISTINCT rp.permission_id) AS permission_count,
                        COUNT(DISTINCT u.id)             AS user_count
                 FROM roles r
                 LEFT JOIN role_permissions rp ON r.id = rp.role_id
                 LEFT JOIN users u             ON u.role_id = r.id AND u.status = 'active'
                 GROUP BY r.id
                 ORDER BY r.name ASC"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Role::getAll error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*,
                        COUNT(DISTINCT rp.permission_id) AS permission_count,
                        COUNT(DISTINCT u.id)             AS user_count
                 FROM roles r
                 LEFT JOIN role_permissions rp ON r.id = rp.role_id
                 LEFT JOIN users u             ON u.role_id = r.id AND u.status = 'active'
                 WHERE r.id = ?
                 GROUP BY r.id"
            );
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Role::getById error: " . $e->getMessage());
            return false;
        }
    }

    public function getByName($name) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM roles WHERE name = ? LIMIT 1");
            $stmt->execute([$name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Role::getByName error: " . $e->getMessage());
            return false;
        }
    }

    /** Create a new custom role */
    public function create(array $data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO roles (name, description) VALUES (:name, :description)"
            );
            $stmt->execute([
                ':name'        => trim($data['name']),
                ':description' => trim($data['description'] ?? ''),
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Role::create error: " . $e->getMessage());
            return false;
        }
    }

    /** Update a role's metadata */
    public function update($id, array $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE roles SET name=:name, description=:description WHERE id=:id"
            );
            return $stmt->execute([
                ':name'        => trim($data['name']),
                ':description' => trim($data['description'] ?? ''),
                ':id'          => (int)$id,
            ]);
        } catch (PDOException $e) {
            error_log("Role::update error: " . $e->getMessage());
            return false;
        }
    }

    /** Delete a role (guards against removing system roles) */
    public function delete($id) {
        $system_roles = ['Super Admin', 'Organization Admin', 'School Admin', 'Teacher', 'Solo Student'];
        $role = $this->getById($id);
        if (!$role || in_array($role['name'], $system_roles)) {
            return false; // cannot delete system roles
        }
        try {
            $stmt = $this->db->prepare("DELETE FROM roles WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (PDOException $e) {
            error_log("Role::delete error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // PERMISSION HELPERS (delegates to Permission model)
    // =========================================================

    /**
     * Sync the full permission set for a role.
     * $permission_ids = array of permission IDs to assign.
     */
    public function syncPermissions($role_id, array $permission_ids) {
        $this->db->beginTransaction();
        try {
            $del = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $del->execute([(int)$role_id]);

            if (!empty($permission_ids)) {
                $ins = $this->db->prepare(
                    "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
                );
                foreach (array_unique($permission_ids) as $pid) {
                    $ins->execute([(int)$role_id, (int)$pid]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Role::syncPermissions error: " . $e->getMessage());
            return false;
        }
    }

    /** Return permission IDs for a role */
    public function getPermissionIds($role_id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT permission_id FROM role_permissions WHERE role_id = ?"
            );
            $stmt->execute([(int)$role_id]);
            return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        } catch (PDOException $e) {
            error_log("Role::getPermissionIds error: " . $e->getMessage());
            return [];
        }
    }

    /** Return permission slugs (names) for a role */
    public function getPermissionNames($role_id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT p.name FROM permissions p
                 JOIN role_permissions rp ON p.id = rp.permission_id
                 WHERE rp.role_id = ?"
            );
            $stmt->execute([(int)$role_id]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Role::getPermissionNames error: " . $e->getMessage());
            return [];
        }
    }

    /** Clone an existing role's permissions into a new role */
    public function clonePermissions($source_role_id, $target_role_id) {
        $ids = $this->getPermissionIds($source_role_id);
        return $this->syncPermissions($target_role_id, $ids);
    }

    /** Check if a role has a specific permission by slug */
    public function hasPermission($role_id, string $permission_name) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM role_permissions rp
                 JOIN permissions p ON rp.permission_id = p.id
                 WHERE rp.role_id = ? AND p.name = ?"
            );
            $stmt->execute([(int)$role_id, $permission_name]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // =========================================================
    // USERS IN ROLE
    // =========================================================

    public function getUsersByRole($role_id, $limit = 50) {
        try {
            $stmt = $this->db->prepare(
                "SELECT u.id, u.username, u.full_name, u.email, u.status
                 FROM users u WHERE u.role_id = ? AND u.status = 'active'
                 ORDER BY u.full_name LIMIT ?"
            );
            $stmt->execute([(int)$role_id, (int)$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Role::getUsersByRole error: " . $e->getMessage());
            return [];
        }
    }

    // =========================================================
    // UTILITY: Is a role a "system" (protected) role?
    // =========================================================

    public function isSystemRole($role_id) {
        $system = ['Super Admin', 'Organization Admin', 'School Admin', 'Teacher', 'Solo Student'];
        $role = $this->getById($role_id);
        return $role && in_array($role['name'], $system);
    }
}