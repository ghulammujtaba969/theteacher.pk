<?php
require_once 'config/database.php';

class Subject
{
    private $conn;
    private $table_name = "subjects";

    public $id;
    public $subject_name;
    public $subject_code;
    public $class_id;
    public $description;
    public $image;
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
                  SET subject_name=:subject_name, subject_code=:subject_code, 
                      class_id=:class_id, description=:description, 
                      image=:image, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_name', $this->subject_name);
        $stmt->bindParam(':subject_code', $this->subject_code);
        $stmt->bindParam(':class_id', $this->class_id);
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

    public function read($accessible_class_ids = [])
    {
        $query = "SELECT s.*, c.class_name, c.class_code as class_code_ref
                  FROM " . $this->table_name . " s
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE s.status = 'active' AND c.status = 'active'";

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY c.class_name ASC, s.subject_name ASC";

        $stmt = $this->conn->prepare($query);
        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }

        return $stmt;
    }

    public function readByClass($class_id, $accessible_class_ids = [])
    {
        $query = "SELECT s.*, c.class_name, c.class_code as class_code_ref
              FROM " . $this->table_name . " s
              LEFT JOIN classes c ON s.class_id = c.id
              WHERE s.class_id = ? AND s.status = 'active' AND c.status = 'active'";

        $params = [$class_id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $query .= " ORDER BY s.subject_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    public function readOne($accessible_class_ids = [])
    {
        $query = "SELECT s.*, c.class_name, c.class_code as class_code_ref
              FROM " . $this->table_name . " s
              LEFT JOIN classes c ON s.class_id = c.id
              WHERE s.id = ? AND s.status = 'active' AND c.status = 'active'";

        $params = [$this->id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->subject_name = $row['subject_name'];
            $this->subject_code = $row['subject_code'];
            $this->class_id = $row['class_id'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET subject_name=:subject_name, subject_code=:subject_code, 
                      class_id=:class_id, description=:description";
        
        // Only update image if a new one is provided
        if ($this->image !== null) {
            $query .= ", image=:image";
        }
        
        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_name', $this->subject_name);
        $stmt->bindParam(':subject_code', $this->subject_code);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':description', $this->description);
        
        if ($this->image !== null) {
            $stmt->bindParam(':image', $this->image);
        }
        
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

    public function checkCodeExists($code, $class_id, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE subject_code = :code AND class_id = :class_id";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':class_id', $class_id);

        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getCount()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE s.status = 'active' AND c.status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getCountByClassIds($class_ids)
    {
        if (empty($class_ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE s.status = 'active' AND c.status = 'active' AND c.id IN (" . $placeholders . ")";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($class_ids);
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getActiveClasses($accessible_class_ids = [])
    {
        $query = "SELECT id, class_name, class_code FROM classes 
                  WHERE status = 'active' ";

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND id IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY class_name ASC";

        $stmt = $this->conn->prepare($query);
        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }
        return $stmt;
    }
}