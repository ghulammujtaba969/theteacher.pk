<?php
require_once 'config/database.php';

class ClassModel
{
    private $conn;
    private $table_name = "classes";

    public $id;
    public $class_name;
    public $class_code;
    public $type; // 'class' or 'course'
    public $registration_open; // For courses
    public $description;
    public $image; // Class/Course image
    public $created_at;
    public $updated_at;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET class_name=:class_name, class_code=:class_code, type=:type,
                      registration_open=:registration_open, description=:description, 
                      image=:image, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':class_name', $this->class_name);
        $stmt->bindParam(':class_code', $this->class_code);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':registration_open', $this->registration_open);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':image', $this->image);
        $this->status = 'active';
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read all classes/courses with access control
     * Returns an array of classes
     */
    public function readAll($accessible_class_ids = [], $can_access_all = false, $type = null, $include_inactive = false)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1 ";

        // Only filter by active status if not including inactive
        if (!$include_inactive) {
            $query .= " AND status = 'active' ";
        }

        if ($type) {
            $query .= " AND type = :type ";
        }

        // Apply access control
        if (!$can_access_all && !empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND id IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY class_name ASC";

        $stmt = $this->conn->prepare($query);
        
        $bind_index = 1;
        
        if ($type) {
            $stmt->bindValue(':type', $type);
        }
        
        if (!$can_access_all && !empty($accessible_class_ids)) {
            foreach ($accessible_class_ids as $class_id) {
                $stmt->bindValue($bind_index++, $class_id, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read all classes/courses (backward compatibility)
     * Returns PDO statement
     */
    public function read($accessible_class_ids = [], $type = null, $include_inactive = false)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1 ";

        // Only filter by active status if not including inactive
        if (!$include_inactive) {
            $query .= " AND status = 'active' ";
        }

        $params = [];

        if ($type) {
            $query .= " AND type = :type ";
            $params[':type'] = $type;
        }

        if (!empty($accessible_class_ids)) {
            // Use named placeholders to avoid mixing styles
            $inPlaceholders = [];
            foreach (array_values($accessible_class_ids) as $idx => $cid) {
                $ph = ':cid' . $idx;
                $inPlaceholders[] = $ph;
                $params[$ph] = (int)$cid;
            }
            $query .= " AND id IN (" . implode(',', $inPlaceholders) . ")";
        }

        $query .= " ORDER BY class_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function readOne($accessible_class_ids = [], $include_inactive = false)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE id = :id";
        
        if (!$include_inactive) {
            $query .= " AND status = 'active'";
        }

        if (!empty($accessible_class_ids)) {
            $inPlaceholders = [];
            foreach (array_values($accessible_class_ids) as $idx => $cid) {
                $ph = ':cid' . $idx;
                $inPlaceholders[] = $ph;
            }
            $query .= " AND id IN (" . implode(',', $inPlaceholders) . ")";
        }

        $stmt = $this->conn->prepare($query);
        $params = [':id' => $this->id];
        if (!empty($accessible_class_ids)) {
            foreach (array_values($accessible_class_ids) as $idx => $cid) {
                $params[':cid' . $idx] = (int)$cid;
            }
        }
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->class_name = $row['class_name'];
            $this->class_code = $row['class_code'];
            $this->type = $row['type'];
            $this->registration_open = $row['registration_open'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                SET class_name=:class_name, class_code=:class_code, type=:type,
                    registration_open=:registration_open, description=:description,
                    image=:image, status=:status
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':class_name', $this->class_name);
        $stmt->bindParam(':class_code', $this->class_code);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':registration_open', $this->registration_open);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET status = 'inactive'
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkCodeExists($code, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE class_code = :code";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);

        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getCount($type = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'active'";
        
        if ($type) {
            $query .= " AND type = :type";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getCountByClassIds($class_ids, $type = null)
    {
        if (empty($class_ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE status = 'active' AND id IN (" . $placeholders . ")";
        
        if ($type) {
            $query .= " AND type = ?";
            $class_ids[] = $type;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($class_ids);
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getAllActiveClasses($type = null)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active'";
            
            if ($type) {
                $query .= " AND type = :type";
            }
            
            $query .= " ORDER BY class_name ASC";
            
            $stmt = $this->conn->prepare($query);
            
            if ($type) {
                $stmt->bindParam(':type', $type);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all active classes: " . $e->getMessage());
            return [];
        }
    }

    public function toggleRegistration($class_id)
    {
        try {
            // Toggle registration for both classes and courses
            $query = "UPDATE " . $this->table_name . " 
                      SET registration_open = NOT registration_open 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $class_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error toggling registration: " . $e->getMessage());
            return false;
        }
    }

    public function uploadImage($file, $old_image = null)
    {
        try {
            // Define upload directory
            $upload_dir = 'uploads/classes/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Allowed file types
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Validate file
            if (!in_array($file['type'], $allowed_types)) {
                return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'];
            }

            if ($file['size'] > $max_size) {
                return ['success' => false, 'error' => 'File size exceeds 5MB limit.'];
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'class_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Delete old image if exists
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }

                return ['success' => true, 'filename' => $filename];
            } else {
                return ['success' => false, 'error' => 'Failed to upload file.'];
            }
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            return ['success' => false, 'error' => 'An error occurred during upload.'];
        }
    }

    public function deleteImage($filename)
    {
        try {
            $filepath = 'uploads/classes/' . $filename;
            if (file_exists($filepath)) {
                return unlink($filepath);
            }
            return true;
        } catch (Exception $e) {
            error_log("Error deleting image: " . $e->getMessage());
            return false;
        }
    }
}
