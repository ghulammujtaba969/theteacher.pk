<?php
/**
 * Permission Model
 * Handles all permission CRUD and role-permission / user-permission operations.
 */
class Permission {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // =========================================================
    // PERMISSION CRUD
    // =========================================================

    /** Return all permissions grouped by module */
    public function getAll() {
        $stmt = $this->conn->query(
            "SELECT * FROM permissions ORDER BY module ASC, sort_order ASC, name ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Return permissions grouped as [ module => [ permissions ] ] */
    public function getAllGrouped() {
        $rows = $this->getAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['module']][] = $row;
        }
        return $grouped;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM permissions WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM permissions WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO permissions (name, display_name, description, module, sort_order)
             VALUES (:name, :display_name, :description, :module, :sort_order)"
        );
        $stmt->execute([
            ':name'         => trim($data['name']),
            ':display_name' => trim($data['display_name']),
            ':description'  => trim($data['description'] ?? ''),
            ':module'       => trim($data['module']),
            ':sort_order'   => (int)($data['sort_order'] ?? 0),
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($id, array $data) {
        $stmt = $this->conn->prepare(
            "UPDATE permissions
             SET name=:name, display_name=:display_name, description=:description,
                 module=:module, sort_order=:sort_order
             WHERE id=:id"
        );
        return $stmt->execute([
            ':name'         => trim($data['name']),
            ':display_name' => trim($data['display_name']),
            ':description'  => trim($data['description'] ?? ''),
            ':module'       => trim($data['module']),
            ':sort_order'   => (int)($data['sort_order'] ?? 0),
            ':id'           => (int)$id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM permissions WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    // =========================================================
    // ROLE  <->  PERMISSION
    // =========================================================

    /** Return all permission IDs assigned to a role */
    public function getPermissionIdsByRole($role_id) {
        $stmt = $this->conn->prepare(
            "SELECT permission_id FROM role_permissions WHERE role_id = ?"
        );
        $stmt->execute([(int)$role_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Return full permission rows for a role */
    public function getPermissionsByRole($role_id) {
        $stmt = $this->conn->prepare(
            "SELECT p.* FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?
             ORDER BY p.module, p.sort_order"
        );
        $stmt->execute([(int)$role_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Return permission names (slugs) for a role */
    public function getPermissionNamesByRole($role_id) {
        $stmt = $this->conn->prepare(
            "SELECT p.name FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             WHERE rp.role_id = ?"
        );
        $stmt->execute([(int)$role_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Sync role permissions — replaces the current set */
    public function syncRolePermissions($role_id, array $permission_ids) {
        $this->conn->beginTransaction();
        try {
            $del = $this->conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $del->execute([(int)$role_id]);

            if (!empty($permission_ids)) {
                $ins = $this->conn->prepare(
                    "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
                );
                foreach ($permission_ids as $pid) {
                    $ins->execute([(int)$role_id, (int)$pid]);
                }
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("syncRolePermissions error: " . $e->getMessage());
            return false;
        }
    }

    /** Grant a single permission to a role */
    public function grantToRole($role_id, $permission_id) {
        $stmt = $this->conn->prepare(
            "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)"
        );
        return $stmt->execute([(int)$role_id, (int)$permission_id]);
    }

    /** Revoke a single permission from a role */
    public function revokeFromRole($role_id, $permission_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?"
        );
        return $stmt->execute([(int)$role_id, (int)$permission_id]);
    }

    // =========================================================
    // USER  <->  PERMISSION  (direct overrides)
    // =========================================================

    /** Return direct permission overrides for a user */
    public function getUserPermissions($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, up.granted FROM permissions p
             JOIN user_permissions up ON p.id = up.permission_id
             WHERE up.user_id = ?
             ORDER BY p.module, p.sort_order"
        );
        $stmt->execute([(int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Grant or deny a permission directly to a user */
    public function setUserPermission($user_id, $permission_id, $granted, $granted_by) {
        $stmt = $this->conn->prepare(
            "INSERT INTO user_permissions (user_id, permission_id, granted, granted_by)
             VALUES (:user_id, :permission_id, :granted, :granted_by)
             ON DUPLICATE KEY UPDATE granted=VALUES(granted), granted_by=VALUES(granted_by)"
        );
        return $stmt->execute([
            ':user_id'       => (int)$user_id,
            ':permission_id' => (int)$permission_id,
            ':granted'       => $granted ? 1 : 0,
            ':granted_by'    => (int)$granted_by,
        ]);
    }

    /** Remove a direct user permission override */
    public function removeUserPermission($user_id, $permission_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM user_permissions WHERE user_id = ? AND permission_id = ?"
        );
        return $stmt->execute([(int)$user_id, (int)$permission_id]);
    }

    /** Clear all direct overrides for a user */
    public function clearUserPermissions($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
        return $stmt->execute([(int)$user_id]);
    }

    // =========================================================
    // LOAD ALL PERMISSIONS FOR A USER  (role + overrides)
    // =========================================================

    /**
     * Returns an associative array of [permission_name => true/false]
     * for a given user, respecting:
     *   1. Role permissions (base)
     *   2. Direct user grants (add extra)
     *   3. Direct user denials (remove, takes precedence)
     */
    public function loadUserPermissions($user_id, $role_id) {
        $permissions = [];

        // 1. Base: all permissions from the user's role
        $role_perms = $this->getPermissionNamesByRole($role_id);
        foreach ($role_perms as $pname) {
            $permissions[$pname] = true;
        }

        // 2 & 3. Apply direct user overrides
        $overrides = $this->getUserPermissions($user_id);
        foreach ($overrides as $override) {
            $permissions[$override['name']] = (bool)$override['granted'];
        }

        return $permissions;
    }

    // =========================================================
    // HELPER: list of distinct modules
    // =========================================================

    public function getModules() {
        $stmt = $this->conn->query(
            "SELECT DISTINCT module FROM permissions ORDER BY module ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // =========================================================
    // STATS
    // =========================================================

    public function getStats() {
        $total = (int)$this->conn->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
        $roles  = (int)$this->conn->query("SELECT COUNT(DISTINCT role_id) FROM role_permissions")->fetchColumn();
        $direct = (int)$this->conn->query("SELECT COUNT(*) FROM user_permissions")->fetchColumn();
        return compact('total', 'roles', 'direct');
    }
}
