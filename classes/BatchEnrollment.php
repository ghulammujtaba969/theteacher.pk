<?php
require_once 'config/database.php';

class BatchEnrollment {
    private $conn;
    private $table_name = "batch_enrollments";

    public $id;
    public $batch_id;
    public $user_id;
    public $enrollment_date;
    public $enrollment_status;
    public $progress_percentage;
    public $attendance_count;
    public $notes;
    public $approved_by;
    public $approved_at;
    public $completion_date;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Enroll a student in a batch
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET batch_id=:batch_id, user_id=:user_id, 
                      enrollment_status=:enrollment_status, 
                      progress_percentage=:progress_percentage,
                      attendance_count=:attendance_count, 
                      notes=:notes";

        $stmt = $this->conn->prepare($query);

        // Set defaults
        if (!isset($this->enrollment_status)) {
            $this->enrollment_status = 'pending';
        }
        if (!isset($this->progress_percentage)) {
            $this->progress_percentage = 0.00;
        }
        if (!isset($this->attendance_count)) {
            $this->attendance_count = 0;
        }

        // Bind values
        $stmt->bindParam(':batch_id', $this->batch_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':enrollment_status', $this->enrollment_status);
        $stmt->bindParam(':progress_percentage', $this->progress_percentage);
        $stmt->bindParam(':attendance_count', $this->attendance_count);
        $stmt->bindParam(':notes', $this->notes);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Get all enrollments for a batch (alias for getEnrollmentsByBatch)
     */
    public function getByBatch($batch_id, $status = null) {
        return $this->getEnrollmentsByBatch($batch_id, $status);
    }

    /**
     * Get all enrollments for a batch
     */
    public function getEnrollmentsByBatch($batch_id, $status = null) {
        $query = "SELECT be.*, 
                         u.full_name, u.email, u.username, u.phone, u.whatsapp_number,
                         u.country, u.address,
                         approver.full_name as approved_by_name
                  FROM " . $this->table_name . " be
                  LEFT JOIN users u ON be.user_id = u.id
                  LEFT JOIN users approver ON be.approved_by = approver.id
                  WHERE be.batch_id = :batch_id";

        if ($status) {
            $query .= " AND be.enrollment_status = :status";
        }

        $query .= " ORDER BY be.enrollment_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get enrollments for a specific user
     */
    public function getEnrollmentsByUser($user_id) {
        $query = "SELECT be.*, 
                         b.batch_name, b.batch_code, b.start_date, b.end_date, 
                         b.meeting_schedule, b.zoom_meeting_link,
                         c.id AS class_id, c.class_name, c.class_code, c.type as class_type,
                         c.image as class_image, c.description as class_description, c.created_at as class_created_at,
                         u.full_name as instructor_name
                  FROM " . $this->table_name . " be
                  LEFT JOIN batches b ON be.batch_id = b.id
                  LEFT JOIN classes c ON b.class_id = c.id
                  LEFT JOIN users u ON b.instructor_id = u.id
                  WHERE be.user_id = :user_id
                  ORDER BY be.enrollment_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single enrollment
     */
    public function readOne() {
        $query = "SELECT be.*, 
                         u.full_name, u.email, u.username,
                         b.batch_name, b.batch_code,
                         approver.full_name as approved_by_name
                  FROM " . $this->table_name . " be
                  LEFT JOIN users u ON be.user_id = u.id
                  LEFT JOIN batches b ON be.batch_id = b.id
                  LEFT JOIN users approver ON be.approved_by = approver.id
                  WHERE be.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->batch_id = $row['batch_id'];
            $this->user_id = $row['user_id'];
            $this->enrollment_date = $row['enrollment_date'];
            $this->enrollment_status = $row['enrollment_status'];
            $this->progress_percentage = $row['progress_percentage'];
            $this->attendance_count = $row['attendance_count'];
            $this->notes = $row['notes'];
            $this->approved_by = $row['approved_by'];
            $this->approved_at = $row['approved_at'];
            $this->completion_date = $row['completion_date'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            return $row;
        }
        return false;
    }

    /**
     * Update enrollment status
     */
    public function updateStatus($status, $approver_id = null) {
        $query = "UPDATE " . $this->table_name . "
                  SET enrollment_status=:status";

        if ($approver_id && $status === 'active') {
            $query .= ", approved_by=:approver_id, approved_at=NOW()";
        }

        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);
        
        if ($approver_id && $status === 'active') {
            $stmt->bindParam(':approver_id', $approver_id);
        }

        return $stmt->execute();
    }

    /**
     * Update enrollment
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET enrollment_status=:enrollment_status, 
                      progress_percentage=:progress_percentage,
                      attendance_count=:attendance_count, 
                      notes=:notes";

        if ($this->completion_date) {
            $query .= ", completion_date=:completion_date";
        }

        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':enrollment_status', $this->enrollment_status);
        $stmt->bindParam(':progress_percentage', $this->progress_percentage);
        $stmt->bindParam(':attendance_count', $this->attendance_count);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':id', $this->id);
        
        if ($this->completion_date) {
            $stmt->bindParam(':completion_date', $this->completion_date);
        }

        return $stmt->execute();
    }

    /**
     * Delete enrollment
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Check if user is already enrolled in batch
     */
    public function isEnrolled($user_id, $batch_id) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Get enrollment statistics for a batch
     */
    public function getBatchStatistics($batch_id) {
        $query = "SELECT 
                    COUNT(*) as total_enrollments,
                    SUM(CASE WHEN enrollment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN enrollment_status = 'active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN enrollment_status = 'suspended' THEN 1 ELSE 0 END) as suspended_count,
                    SUM(CASE WHEN enrollment_status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                    SUM(CASE WHEN enrollment_status = 'dropped' THEN 1 ELSE 0 END) as dropped_count,
                    AVG(progress_percentage) as avg_progress,
                    AVG(attendance_count) as avg_attendance
                  FROM " . $this->table_name . "
                  WHERE batch_id = :batch_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Bulk update enrollment status
     */
    public function bulkUpdateStatus($enrollment_ids, $status, $approver_id = null) {
        if (empty($enrollment_ids)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($enrollment_ids), '?'));
        
        $query = "UPDATE " . $this->table_name . "
                  SET enrollment_status = ?";

        if ($approver_id && $status === 'active') {
            $query .= ", approved_by = ?, approved_at = NOW()";
        }

        $query .= " WHERE id IN (" . $placeholders . ")";

        $stmt = $this->conn->prepare($query);
        
        $params = [$status];
        if ($approver_id && $status === 'active') {
            $params[] = $approver_id;
        }
        $params = array_merge($params, $enrollment_ids);

        return $stmt->execute($params);
    }
}
?>
