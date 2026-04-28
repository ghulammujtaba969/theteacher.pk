<?php
require_once 'config/database.php';

class Batch {
    private $conn;
    private $table_name = "batches";

    public $id;
    public $batch_name;
    public $batch_code;
    public $class_id;
    public $description;
    public $start_date;
    public $end_date;
    public $max_students;
    public $enrollment_status;
    public $meeting_schedule;
    public $zoom_meeting_id;
    public $zoom_meeting_link;
    public $instructor_id;
    public $status;
    public $created_by;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new batch
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET batch_name=:batch_name, batch_code=:batch_code, class_id=:class_id,
                      description=:description, start_date=:start_date, end_date=:end_date,
                      max_students=:max_students, enrollment_status=:enrollment_status,
                      meeting_schedule=:meeting_schedule, zoom_meeting_id=:zoom_meeting_id,
                      zoom_meeting_link=:zoom_meeting_link, instructor_id=:instructor_id,
                      status=:status, created_by=:created_by";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->batch_name = htmlspecialchars(strip_tags($this->batch_name));
        $this->batch_code = htmlspecialchars(strip_tags($this->batch_code));
        $this->class_id = htmlspecialchars(strip_tags($this->class_id));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(':batch_name', $this->batch_name);
        $stmt->bindParam(':batch_code', $this->batch_code);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':max_students', $this->max_students);
        $stmt->bindParam(':enrollment_status', $this->enrollment_status);
        $stmt->bindParam(':meeting_schedule', $this->meeting_schedule);
        $stmt->bindParam(':zoom_meeting_id', $this->zoom_meeting_id);
        $stmt->bindParam(':zoom_meeting_link', $this->zoom_meeting_link);
        $stmt->bindParam(':instructor_id', $this->instructor_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created_by', $this->created_by);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read all batches with access control
     */
    public function readAll($accessible_class_ids = [], $can_access_all = false) {
        $query = "SELECT b.*, 
                         c.class_name, c.class_code, c.type as class_type,
                         u.full_name as instructor_name,
                         COUNT(DISTINCT be.id) as enrolled_count,
                         COUNT(DISTINCT CASE WHEN be.enrollment_status = 'active' THEN be.id END) as active_students
                  FROM " . $this->table_name . " b
                  LEFT JOIN classes c ON b.class_id = c.id
                  LEFT JOIN users u ON b.instructor_id = u.id
                  LEFT JOIN batch_enrollments be ON b.id = be.batch_id
                  WHERE 1=1";

        // Apply access control
        if (!$can_access_all && !empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND b.class_id IN (" . $placeholders . ")";
        }

        $query .= " GROUP BY b.id ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);

        // Bind class IDs if access control applies
        if (!$can_access_all && !empty($accessible_class_ids)) {
            foreach ($accessible_class_ids as $index => $class_id) {
                $stmt->bindValue($index + 1, $class_id, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read all batches with optional filters (backward compatibility)
     */
    public function read($class_id = null, $status = null, $enrollment_status = null) {
        $query = "SELECT b.*, 
                         c.class_name, c.class_code, c.type as class_type,
                         u.full_name as instructor_name,
                         COUNT(DISTINCT be.id) as enrolled_count,
                         COUNT(DISTINCT CASE WHEN be.enrollment_status = 'active' THEN be.id END) as active_students
                  FROM " . $this->table_name . " b
                  LEFT JOIN classes c ON b.class_id = c.id
                  LEFT JOIN users u ON b.instructor_id = u.id
                  LEFT JOIN batch_enrollments be ON b.id = be.batch_id
                  WHERE 1=1";

        if ($class_id) {
            $query .= " AND b.class_id = :class_id";
        }
        if ($status) {
            $query .= " AND b.status = :status";
        }
        if ($enrollment_status) {
            $query .= " AND b.enrollment_status = :enrollment_status";
        }

        $query .= " GROUP BY b.id ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);

        if ($class_id) {
            $stmt->bindParam(':class_id', $class_id);
        }
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        if ($enrollment_status) {
            $stmt->bindParam(':enrollment_status', $enrollment_status);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Read single batch by ID
     */
    public function readOne() {
        $query = "SELECT b.*, 
                         c.class_name, c.class_code, c.type as class_type,
                         u.full_name as instructor_name, u.email as instructor_email,
                         COUNT(DISTINCT be.id) as enrolled_count,
                         COUNT(DISTINCT CASE WHEN be.enrollment_status = 'active' THEN be.id END) as active_students,
                         COUNT(DISTINCT CASE WHEN be.enrollment_status = 'pending' THEN be.id END) as pending_students
                  FROM " . $this->table_name . " b
                  LEFT JOIN classes c ON b.class_id = c.id
                  LEFT JOIN users u ON b.instructor_id = u.id
                  LEFT JOIN batch_enrollments be ON b.id = be.batch_id
                  WHERE b.id = :id
                  GROUP BY b.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->batch_name = $row['batch_name'];
            $this->batch_code = $row['batch_code'];
            $this->class_id = $row['class_id'];
            $this->description = $row['description'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->max_students = $row['max_students'];
            $this->enrollment_status = $row['enrollment_status'];
            $this->meeting_schedule = $row['meeting_schedule'];
            $this->zoom_meeting_id = $row['zoom_meeting_id'];
            $this->zoom_meeting_link = $row['zoom_meeting_link'];
            $this->instructor_id = $row['instructor_id'];
            $this->status = $row['status'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return $row;
        }
        return false;
    }

    /**
     * Update batch
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET batch_name=:batch_name, batch_code=:batch_code, 
                      description=:description, start_date=:start_date, end_date=:end_date,
                      max_students=:max_students, enrollment_status=:enrollment_status,
                      meeting_schedule=:meeting_schedule, zoom_meeting_id=:zoom_meeting_id,
                      zoom_meeting_link=:zoom_meeting_link, instructor_id=:instructor_id,
                      status=:status
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->batch_name = htmlspecialchars(strip_tags($this->batch_name));
        $this->batch_code = htmlspecialchars(strip_tags($this->batch_code));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(':batch_name', $this->batch_name);
        $stmt->bindParam(':batch_code', $this->batch_code);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':max_students', $this->max_students);
        $stmt->bindParam(':enrollment_status', $this->enrollment_status);
        $stmt->bindParam(':meeting_schedule', $this->meeting_schedule);
        $stmt->bindParam(':zoom_meeting_id', $this->zoom_meeting_id);
        $stmt->bindParam(':zoom_meeting_link', $this->zoom_meeting_link);
        $stmt->bindParam(':instructor_id', $this->instructor_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Delete batch
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Check if batch code exists
     */
    public function codeExists($exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE batch_code = :batch_code";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_code', $this->batch_code);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Get batches for a specific class/course
     */
    public function getBatchesByClass($class_id) {
        $query = "SELECT b.*, 
                         COUNT(DISTINCT be.id) as enrolled_count,
                         COUNT(DISTINCT CASE WHEN be.enrollment_status = 'active' THEN be.id END) as active_students,
                         u.full_name as instructor_name
                  FROM " . $this->table_name . " b
                  LEFT JOIN batch_enrollments be ON b.id = be.batch_id
                  LEFT JOIN users u ON b.instructor_id = u.id
                  WHERE b.class_id = :class_id
                  GROUP BY b.id
                  ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get available instructors (teachers)
     */
    public function getAvailableInstructors() {
        $query = "SELECT id, full_name, email 
                  FROM users 
                  WHERE role_id = 4 AND status = 'active'
                  ORDER BY full_name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check enrollment capacity
     */
    public function canEnroll() {
        if ($this->enrollment_status !== 'open') {
            return false;
        }

        if ($this->max_students) {
            $query = "SELECT COUNT(*) as count 
                      FROM batch_enrollments 
                      WHERE batch_id = :batch_id 
                      AND enrollment_status IN ('pending', 'active')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':batch_id', $this->id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row['count'] >= $this->max_students) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update enrollment status based on capacity
     */
    public function updateEnrollmentStatus() {
        if ($this->max_students) {
            $query = "SELECT COUNT(*) as count 
                      FROM batch_enrollments 
                      WHERE batch_id = :batch_id 
                      AND enrollment_status IN ('pending', 'active')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':batch_id', $this->id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row['count'] >= $this->max_students) {
                $update_query = "UPDATE " . $this->table_name . " 
                                SET enrollment_status = 'full' 
                                WHERE id = :batch_id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':batch_id', $this->id);
                $update_stmt->execute();
            }
        }
    }
}
?>