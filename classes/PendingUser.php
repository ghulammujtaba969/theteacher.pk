<?php
require_once 'config/database.php';

class PendingUser {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        try {
            $query = "INSERT INTO pending_users (username, email, password, full_name, address, phone, gender, role_id, organization_id, school_id, can_access_all_classes, status, selected_classes, created_by) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $selected_classes_json = json_encode($data['selected_classes'] ?? []);

            $result = $stmt->execute([
                $data['username'],
                $data['email'],
                $hashed_password,
                $data['full_name'],
                $data['address'] ?? null,
                $data['phone'] ?? null,
                $data['gender'] ?? null,
                $data['role_id'] ?? 5, // Default to Solo Student
                $data['organization_id'] ?? null,
                $data['school_id'] ?? null,
                $data['can_access_all_classes'] ?? 0,
                'pending', // Always pending for new registrations
                $selected_classes_json,
                $data['created_by'] ?? null
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            } else {
                error_log("PendingUser->create: Execute returned false");
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDOException in PendingUser->create: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0] . ", Error Code: " . $e->errorInfo[1]);
            error_log("Data being inserted: " . print_r($data, true));
            return false;
        }
    }

    public function getAllPending() {
        try {
            $query = "SELECT pu.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM pending_users pu
                     LEFT JOIN roles r ON pu.role_id = r.id
                     LEFT JOIN organizations o ON pu.organization_id = o.id
                     LEFT JOIN schools s ON pu.school_id = s.id
                     WHERE pu.status = 'pending'
                     ORDER BY pu.submission_timestamp DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in PendingUser->getAllPending: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT pu.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM pending_users pu
                     LEFT JOIN roles r ON pu.role_id = r.id
                     LEFT JOIN organizations o ON pu.organization_id = o.id
                     LEFT JOIN schools s ON pu.school_id = s.id
                     WHERE pu.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDOException in PendingUser->getById: " . $e->getMessage());
            return false;
        }
    }

    public function approve($pending_user_id, $admin_id, $admin_notes = null) {
        try {
            $this->db->beginTransaction();

            $pending_user = $this->getById($pending_user_id);
            if (!$pending_user) {
                throw new Exception("Pending user not found.");
            }

            error_log("PendingUser->approve: Found pending user: " . print_r($pending_user, true));

            // 1. Insert into users table with the already hashed password
            require_once 'classes/User.php';
            $user_obj = new User($this->db);
            $user_data = [
                'username' => $pending_user['username'],
                'email' => $pending_user['email'],
                'password' => $pending_user['password'], // Already hashed password from pending_users
                'full_name' => $pending_user['full_name'],
                'address' => $pending_user['address'],
                'phone' => $pending_user['phone'],
                'gender' => $pending_user['gender'],
                'role_id' => $pending_user['role_id'],
                'organization_id' => $pending_user['organization_id'],
                'school_id' => $pending_user['school_id'],
                'can_access_all_classes' => $pending_user['can_access_all_classes'],
                'status' => 'active', // Approved users are active
            ];
            
            error_log("PendingUser->approve: Preparing to create user. User data: " . print_r($user_data, true) . ", Admin ID: " . $admin_id);
            
            // Pass true for $is_hashed parameter since password is already hashed
            $new_user_id = $user_obj->create($user_data, $admin_id, true);
            error_log("PendingUser->approve: User creation result: " . ($new_user_id ?: 'false'));

            if (!$new_user_id) {
                throw new Exception("Failed to create user in users table.");
            }

            // 2. Assign classes if any were selected
            $selected_classes = json_decode($pending_user['selected_classes'], true);
            error_log("PendingUser->approve: Selected classes: " . print_r($selected_classes, true));
            
            if (!empty($selected_classes)) {
                foreach ($selected_classes as $class_id) {
                    $permission_result = $user_obj->assignClassPermission($new_user_id, $class_id, $admin_id);
                    error_log("PendingUser->approve: Class permission assignment for class $class_id: " . ($permission_result ? 'Success' : 'Failed'));
                }
            }

            // 3. Update pending_users status
            $query = "UPDATE pending_users SET status = 'approved', admin_notes = ?, admin_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $update_result = $stmt->execute([$admin_notes, $admin_id, $pending_user_id]);
            error_log("PendingUser->approve: Pending user status update: " . ($update_result ? 'Success' : 'Failed'));

            $this->db->commit();
            error_log("PendingUser->approve: Transaction committed successfully");
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Exception in PendingUser->approve: " . $e->getMessage());
            error_log("Full Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function reject($pending_user_id, $admin_id, $admin_notes = null) {
        try {
            $query = "UPDATE pending_users SET status = 'rejected', admin_notes = ?, admin_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$admin_notes, $admin_id, $pending_user_id]);
        } catch (PDOException $e) {
            error_log("PDOException in PendingUser->reject: " . $e->getMessage());
            return false;
        }
    }

    // Method to check if username or email already exists in pending_users
    public function exists($username, $email) {
        try {
            $query = "SELECT COUNT(*) FROM pending_users WHERE (username = ? OR email = ?) AND status = 'pending'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username, $email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("PDOException in PendingUser->exists: " . $e->getMessage());
            return false;
        }
    }
}