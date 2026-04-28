<?php
require_once 'config/database.php';

class Syllabus
{
    private $conn;
    private $table_name = "syllabi";

    public $id;
    public $syllabus_title;
    public $subject_id; // For classes (traditional flow)
    public $class_id; // For courses (direct link)
    public $description;
    public $objectives;
    public $duration_weeks;
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
                  SET syllabus_title=:syllabus_title, subject_id=:subject_id, class_id=:class_id,
                      description=:description, objectives=:objectives, duration_weeks=:duration_weeks, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':syllabus_title', $this->syllabus_title);
        $stmt->bindParam(':subject_id', $this->subject_id);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':objectives', $this->objectives);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
        $this->status = 'active';
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($accessible_class_ids = [], $type = null)
    {
        // If type is 'course', get syllabi directly linked to courses
        // If type is 'class', get syllabi linked via subjects
        $query = "SELECT sy.*, 
                  COALESCE(s.subject_name, '') as subject_name, 
                  COALESCE(s.subject_code, '') as subject_code, 
                  COALESCE(c1.class_name, c2.class_name) as class_name, 
                  COALESCE(c1.class_code, c2.class_code) as class_code,
                  COALESCE(c1.id, c2.id) as class_id,
                  COALESCE(c1.type, c2.type) as type
                  FROM " . $this->table_name . " sy
                  LEFT JOIN subjects s ON sy.subject_id = s.id
                  LEFT JOIN classes c1 ON s.class_id = c1.id
                  LEFT JOIN classes c2 ON sy.class_id = c2.id
                  WHERE sy.status = 'active' AND (s.status = 'active' OR s.status IS NULL)";

        if ($type === 'course') {
            $query .= " AND sy.class_id IS NOT NULL AND c2.type = 'course'";
        } elseif ($type === 'class') {
            $query .= " AND sy.subject_id IS NOT NULL AND c1.type = 'class'";
        }

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND (c1.id IN (" . $placeholders . ") OR c2.id IN (" . $placeholders . "))";
        }

        $query .= " ORDER BY class_name ASC, subject_name ASC, sy.syllabus_title ASC";

        $stmt = $this->conn->prepare($query);

        if (!empty($accessible_class_ids)) {
            // Duplicate the accessible_class_ids for both c1 and c2
            $params = array_merge($accessible_class_ids, $accessible_class_ids);
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }

        return $stmt;
    }

    public function readBySubject($subject_id, $accessible_class_ids = [])
    {
        $query = "SELECT sy.*, s.subject_name, s.subject_code, c.class_name, c.class_code, c.id as class_id
              FROM " . $this->table_name . " sy
              LEFT JOIN subjects s ON sy.subject_id = s.id
              LEFT JOIN classes c ON s.class_id = c.id
              WHERE sy.subject_id = ? AND sy.status = 'active' AND s.status = 'active' AND c.status = 'active'";

        $params = [$subject_id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $query .= " ORDER BY sy.syllabus_title ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    public function readByCourse($course_id, $accessible_class_ids = [])
    {
        $query = "SELECT sy.*, c.class_name, c.class_code, c.id as class_id, c.registration_open
              FROM " . $this->table_name . " sy
              LEFT JOIN classes c ON sy.class_id = c.id
              WHERE sy.class_id = ? AND sy.status = 'active' AND c.status = 'active' AND c.type = 'course'";

        $params = [$course_id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $query .= " ORDER BY sy.syllabus_title ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    public function readOne($accessible_class_ids = [])
    {
        $query = "SELECT sy.*, 
                  s.subject_name, s.subject_code, s.id as subject_id,
                  COALESCE(c1.class_name, c2.class_name) as class_name, 
                  COALESCE(c1.class_code, c2.class_code) as class_code,
                  COALESCE(c1.id, c2.id) as class_id,
                  COALESCE(c1.type, c2.type) as type,
                  c2.registration_open
              FROM " . $this->table_name . " sy
              LEFT JOIN subjects s ON sy.subject_id = s.id
              LEFT JOIN classes c1 ON s.class_id = c1.id
              LEFT JOIN classes c2 ON sy.class_id = c2.id
              WHERE sy.id = ? AND sy.status = 'active'";

        $params = [$this->id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND (c1.id IN (" . $placeholders . ") OR c2.id IN (" . $placeholders . "))";
            $params = array_merge($params, $accessible_class_ids, $accessible_class_ids);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->syllabus_title = $row['syllabus_title'];
            $this->subject_id = $row['subject_id'];
            $this->class_id = $row['class_id'];
            $this->description = $row['description'];
            $this->objectives = $row['objectives'];
            $this->duration_weeks = $row['duration_weeks'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET syllabus_title=:syllabus_title, subject_id=:subject_id, class_id=:class_id,
                      description=:description, objectives=:objectives, duration_weeks=:duration_weeks
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':syllabus_title', $this->syllabus_title);
        $stmt->bindParam(':subject_id', $this->subject_id);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':objectives', $this->objectives);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
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

    public function getCount($type = null)
    {
        if ($type === 'course') {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " sy
                      LEFT JOIN classes c ON sy.class_id = c.id
                      WHERE sy.status = 'active' AND c.status = 'active' AND c.type = 'course'";
        } elseif ($type === 'class') {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " sy
                      LEFT JOIN subjects s ON sy.subject_id = s.id
                      LEFT JOIN classes c ON s.class_id = c.id
                      WHERE sy.status = 'active' AND s.status = 'active' AND c.status = 'active' AND c.type = 'class'";
        } else {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'active'";
        }

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
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " sy
                  LEFT JOIN subjects s ON sy.subject_id = s.id
                  LEFT JOIN classes c1 ON s.class_id = c1.id
                  LEFT JOIN classes c2 ON sy.class_id = c2.id
                  WHERE sy.status = 'active' 
                  AND ((s.status = 'active' AND c1.status = 'active' AND c1.id IN (" . $placeholders . "))
                       OR (c2.status = 'active' AND c2.id IN (" . $placeholders . ")))";

        $params = array_merge($class_ids, $class_ids);
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getActiveSubjects($accessible_class_ids = [])
    {
        $query = "SELECT s.id, s.subject_name, s.subject_code, c.class_name, c.class_code, c.id as class_id
                  FROM subjects s
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE s.status = 'active' AND c.status = 'active' AND c.type = 'class'";

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

    public function getSubjectsByClass($class_id, $accessible_class_ids = [])
    {
        $query = "SELECT id, subject_name, subject_code FROM subjects 
              WHERE class_id = ? AND status = 'active'";

        $params = [$class_id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND class_id IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $query .= " ORDER BY subject_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function getActiveClasses($accessible_class_ids = [], $type = null)
    {
        $query = "SELECT id, class_name, class_code, type FROM classes 
                  WHERE status = 'active'";

        if ($type) {
            $query .= " AND type = :type";
        }

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND id IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY class_name ASC";
        $stmt = $this->conn->prepare($query);

        if ($type) {
            $stmt->bindValue(':type', $type);
        }

        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }
        return $stmt;
    }

    public function getActiveCourses($accessible_class_ids = [])
    {
        return $this->getActiveClasses($accessible_class_ids, 'course');
    }
}
