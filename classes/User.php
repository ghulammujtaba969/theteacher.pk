<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    
    public $id;
    public $username;
    public $email;
    public $full_name;
    public $address;
    public $gender;
    public $role_id;
    public $organization_id;
    public $school_id;
    public $status;
    public $created_at;
    public $updated_at;
    public $role_name;
    public $organization_name;
    public $school_name;
    public $can_access_all_classes;

    public function __construct($db) {
        $this->db = $db;
    }
    
    public function login($username, $password) {
        try {
            $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM users u 
                     JOIN roles r ON u.role_id = r.id 
                     LEFT JOIN organizations o ON u.organization_id = o.id
                     LEFT JOIN schools s ON u.school_id = s.id
                     WHERE (u.username = ? OR u.email = ?) AND u.status = 'active'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username, $username]);
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($user['password']) && password_verify($password, $user['password'])) {
                    // Load additional properties to the object
                    $this->id = $user['id'];
                    $this->username = $user['username'];
                    $this->email = $user['email'];
                    $this->full_name = $user['full_name'];
                    $this->address = $user['address'] ?? null;
                    $this->role_id = $user['role_id'];
                    $this->organization_id = $user['organization_id'];
                    $this->school_id = $user['school_id'];
                    $this->status = $user['status'];
                    $this->created_at = $user['created_at'];
                    $this->updated_at = $user['updated_at'];
                    $this->role_name = $user['role_name'];
                    $this->organization_name = $user['organization_name'];
                    $this->school_name = $user['school_name'];
                    $this->can_access_all_classes = $user['can_access_all_classes'];

                    return $user;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findByEmail($email) {
        try {
            $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM users u 
                     JOIN roles r ON u.role_id = r.id 
                     LEFT JOIN organizations o ON u.organization_id = o.id
                     LEFT JOIN schools s ON u.school_id = s.id
                     WHERE u.email = ? AND u.status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("findByEmail error: " . $e->getMessage());
            return false;
        }
    }
    
    public function findByUsername($username) {
        try {
            $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM users u 
                     JOIN roles r ON u.role_id = r.id 
                     LEFT JOIN organizations o ON u.organization_id = o.id
                     LEFT JOIN schools s ON u.school_id = s.id
                     WHERE u.username = ? AND u.status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("findByUsername error: " . $e->getMessage());
            return false;
        }
    }

    public function create($data, $created_by, $is_hashed = false) {
        try {
            $query = "INSERT INTO users (username, email, password, full_name, role_id, organization_id, school_id, can_access_all_classes, status, created_by, phone, gender, address) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $password_to_store = $is_hashed ? $data['password'] : password_hash($data['password'], PASSWORD_DEFAULT);
            
            error_log("User->create: Executing INSERT query. Data: " . print_r($data, true) . ", Created By: " . $created_by . ", Password to store: " . $password_to_store);
            $result = $stmt->execute([
                $data['username'],
                $data['email'],
                $password_to_store,
                $data['full_name'],
                $data['role_id'] ?? 5, // Default to Solo Student if not provided
                $data['organization_id'] ?? null,
                $data['school_id'] ?? null,
                $data['can_access_all_classes'] ?? 0,
                $data['status'] ?? 'pending', // Default to pending for self-registration
                $created_by,
                $data['phone'] ?? null,
                $data['gender'] ?? null,
                $data['address'] ?? null
            ]);
            
            if ($result) {
                return $this->db->lastInsertId(); // Return the ID of the newly created user
            } else {
                error_log("User->create: SQL execution failed. ErrorInfo: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDOException in User->create: " . $e->getMessage());
            error_log("Full Trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    public function assignClassPermission($user_id, $class_id, $granted_by) {
        try {
            $query = "INSERT INTO user_class_permissions (user_id, class_id, granted_by) 
                     VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$user_id, $class_id, $granted_by]);
        } catch (PDOException $e) {
            error_log("PDOException in User->assignClassPermission: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUsersByPermission($current_user) {
        try {
            $role_name = $current_user['role_name'];
            
            if ($role_name === 'Super Admin') {
                // Super Admin can see all users
                $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name,
                         creator.username as created_by_username
                         FROM users u 
                         JOIN roles r ON u.role_id = r.id 
                         LEFT JOIN organizations o ON u.organization_id = o.id
                         LEFT JOIN schools s ON u.school_id = s.id
                         LEFT JOIN users creator ON u.created_by = creator.id
                         ORDER BY u.created_at DESC";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            } elseif ($role_name === 'Organization Admin') {
                // Organization Admin can see users in their organization (School Admin, Teacher, Solo Student)
                $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name,
                         creator.username as created_by_username
                         FROM users u 
                         JOIN roles r ON u.role_id = r.id 
                         LEFT JOIN organizations o ON u.organization_id = o.id
                         LEFT JOIN schools s ON u.school_id = s.id
                         LEFT JOIN users creator ON u.created_by = creator.id
                         WHERE u.organization_id = ? AND r.name IN ('School Admin', 'Teacher', 'Solo Student')
                         ORDER BY u.created_at DESC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$current_user['organization_id']]);
            } elseif ($role_name === 'School Admin') {
                // School Admin can see teachers and solo students in their school
                $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name,
                         creator.username as created_by_username
                         FROM users u 
                         JOIN roles r ON u.role_id = r.id 
                         LEFT JOIN organizations o ON u.organization_id = o.id
                         LEFT JOIN schools s ON u.school_id = s.id
                         LEFT JOIN users creator ON u.created_by = creator.id
                         WHERE u.school_id = ? AND r.name IN ('Teacher', 'Solo Student')
                         ORDER BY u.created_at DESC";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$current_user['school_id']]);
            } else {
                // Teachers and Solo Students can only see themselves
                return [$current_user];
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return [];
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT u.*, r.name as role_name, o.name as organization_name, s.name as school_name
                     FROM users u 
                     JOIN roles r ON u.role_id = r.id 
                     LEFT JOIN organizations o ON u.organization_id = o.id
                     LEFT JOIN schools s ON u.school_id = s.id
                     WHERE u.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            // Fetch current user data to merge with provided data
            $existing_user = $this->getById($id);
            if (!$existing_user) {
                error_log("Error updating user: User with ID {$id} not found.");
                return false;
            }

            // Define all expected keys with their default values from existing user data
            $default_data = [
                'username' => $existing_user['username'] ?? null,
                'email' => $existing_user['email'] ?? null,
                'full_name' => $existing_user['full_name'] ?? null,
                'role_id' => $existing_user['role_id'] ?? null,
                'organization_id' => $existing_user['organization_id'] ?? null,
                'school_id' => $existing_user['school_id'] ?? null,
                'can_access_all_classes' => $existing_user['can_access_all_classes'] ?? 0,
                'status' => $existing_user['status'] ?? null,
            ];

            // Merge existing defaults with new data, prioritizing new data
            // This ensures all keys are present, and new data overrides old.
            $merged_data = array_merge($default_data, $data);

            $query = "UPDATE users 
                     SET username = ?, email = ?, full_name = ?, role_id = ?, organization_id = ?, school_id = ?, can_access_all_classes = ?, status = ?
                     WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $merged_data['username'],
                $merged_data['email'],
                $merged_data['full_name'],
                $merged_data['role_id'],
                $merged_data['organization_id'],
                $merged_data['school_id'],
                $merged_data['can_access_all_classes'],
                $merged_data['status'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePassword($id, $new_password) {
        try {
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            return $stmt->execute([$hashed_password, $id]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    // Update user photo path
    public function updatePhoto($id, $photoPath) {
        try {
            $query = "UPDATE users SET photo = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$photoPath, $id]);
        } catch (PDOException $e) {
            error_log("Error updating user photo: " . $e->getMessage());
            return false;
        }
    }
    
    // Update basic profile info used for enrollment/inquiry
    public function updateBasicInfo($id, $data) {
        try {
            $query = "UPDATE users 
                      SET country = ?, phone = ?, whatsapp_number = ?, gender = ?, address = ?
                      WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $country = $data['country'] ?? null;
            $phone = $data['phone'] ?? null;
            $whatsapp = $data['whatsapp_number'] ?? $phone;
            $gender = $data['gender'] ?? null;
            $address = $data['address'] ?? null;
            return $stmt->execute([$country, $phone, $whatsapp, $gender, $address, $id]);
        } catch (PDOException $e) {
            error_log("Error updating basic info: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            // Soft delete by setting status to inactive
            $query = "UPDATE users SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
    
    public function canManageUser($current_user, $target_user_id) {
        $current_user_role_name = $current_user['role_name'];
        
        if ($current_user_role_name === 'Super Admin') {
            return true;
        }
        
        $target_user = $this->getById($target_user_id);
        if (!$target_user) return false;
        
        $target_user_role_name = $target_user['role_name'];

        // An Organization Admin can manage School Admins, Teachers, and Solo Students within their organization
        if ($current_user_role_name === 'Organization Admin') {
            return $target_user['organization_id'] == $current_user['organization_id'] && 
                   in_array($target_user_role_name, ['School Admin', 'Teacher', 'Solo Student']);
        }
        
        // A School Admin can manage Teachers and Solo Students within their school
        if ($current_user_role_name === 'School Admin') {
            return $target_user['school_id'] == $current_user['school_id'] && 
                   in_array($target_user_role_name, ['Teacher', 'Solo Student']);
        }
        
        return false;
    }

    public function hasAccessToClass($user_id, $class_id) {
        // Check if user has can_access_all_classes flag
        $query_user = "SELECT can_access_all_classes FROM users WHERE id = ?";
        $stmt_user = $this->db->prepare($query_user);
        $stmt_user->execute([$user_id]);
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if ($user_data && $user_data['can_access_all_classes'] == 1) {
            return true;
        }

        // Check if user has specific permission for this class
        $query_permission = "SELECT COUNT(*) FROM user_class_permissions WHERE user_id = ? AND class_id = ?";
        $stmt_permission = $this->db->prepare($query_permission);
        $stmt_permission->execute([$user_id, $class_id]);
        
        return $stmt_permission->fetchColumn() > 0;
    }

    public function getAccessibleClasses($current_user) {
        $accessible_classes = [];
        
        if (isset($current_user['can_access_all_classes']) && $current_user['can_access_all_classes'] == 1) {
            // If user can access all classes, return all active classes
            require_once 'classes/ClassModel.php';
            $classModel = new ClassModel($this->db);
            $stmt = $classModel->read([]); // Explicitly pass an empty array for all classes
            $accessible_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Otherwise, fetch classes based on explicit permissions
            $query = "SELECT c.* FROM classes c
                      JOIN user_class_permissions ucp ON c.id = ucp.class_id
                      WHERE ucp.user_id = ? AND c.status = 'active'
                      ORDER BY c.class_name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$current_user['id']]);
            $accessible_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $accessible_classes;
    }
    // Method to get total users count (for Super Admin)
    public function getUsersCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count: " . $e->getMessage());
            return 0;
        }
    }

    // Method to get users count by organization (for Organization Admin)
    public function getUsersCountByOrganization($organization_id) {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE organization_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$organization_id]);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count by organization: " . $e->getMessage());
            return 0;
        }
    }

    // Method to get users count by school (for School Admin)
    public function getUsersCountBySchool($school_id) {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE school_id = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$school_id]);
            $row = $stmt->fetch();
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting users count by school: " . $e->getMessage());
            return 0;
        }
    }
}
?>
