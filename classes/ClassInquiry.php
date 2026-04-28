<?php
// classes/ClassInquiry.php

class ClassInquiry
{
    private $conn;
    private $table_name = "class_inquiries";

    public $id;
    public $user_id;
    public $class_id;
    public $whatsapp_number;
    public $country;
    public $address;
    public $contact_email;
    public $preferred_time_slot; // Single time slot instead of array
    public $status;
    public $admin_notes;
    public $reviewed_by;
    public $reviewed_at;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new inquiry
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET user_id=:user_id, class_id=:class_id, whatsapp_number=:whatsapp_number,
                      country=:country, address=:address, contact_email=:contact_email,
                      preferred_time_slot=:preferred_time_slot, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->class_id = htmlspecialchars(strip_tags($this->class_id));
        $this->whatsapp_number = htmlspecialchars(strip_tags($this->whatsapp_number));
        $this->country = htmlspecialchars(strip_tags($this->country));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->contact_email = htmlspecialchars(strip_tags($this->contact_email));
        $this->preferred_time_slot = htmlspecialchars(strip_tags($this->preferred_time_slot));
        $this->status = 'pending';

        // Bind values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':whatsapp_number', $this->whatsapp_number);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':contact_email', $this->contact_email);
        $stmt->bindParam(':preferred_time_slot', $this->preferred_time_slot);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Check if inquiry already exists
    public function inquiryExists($user_id, $class_id)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $class_id]);
        return $stmt->rowCount() > 0;
    }

    // Get inquiry by user and class
    public function getByUserAndClass($user_id, $class_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? AND class_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $class_id]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->class_id = $row['class_id'];
            $this->whatsapp_number = $row['whatsapp_number'];
            $this->country = $row['country'];
            $this->address = $row['address'];
            $this->contact_email = $row['contact_email'];
            $this->preferred_time_slot = $row['preferred_time_slot'];
            $this->status = $row['status'];
            $this->admin_notes = $row['admin_notes'];
            $this->reviewed_by = $row['reviewed_by'];
            $this->reviewed_at = $row['reviewed_at'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Approve inquiry
    public function approve($inquiry_id, $admin_id, $admin_notes = '')
    {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // First, verify the inquiry exists and is pending
            $check_query = "SELECT user_id, class_id, status FROM " . $this->table_name . " WHERE id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->execute([$inquiry_id]);
            $inquiry_data = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$inquiry_data) {
                throw new Exception("Inquiry not found with ID: " . $inquiry_id);
            }

            if ($inquiry_data['status'] !== 'pending') {
                throw new Exception("Inquiry is not in pending status. Current status: " . $inquiry_data['status']);
            }

            // Update inquiry status
            $update_query = "UPDATE " . $this->table_name . " 
                        SET status = 'approved', 
                            reviewed_by = ?, 
                            admin_notes = ?, 
                            reviewed_at = NOW()
                        WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);

            if (!$update_stmt->execute([$admin_id, $admin_notes, $inquiry_id])) {
                throw new Exception("Failed to update inquiry status. SQL Error: " . implode(", ", $update_stmt->errorInfo()));
            }

            // Check if user_class_permissions table exists
            $table_check = $this->conn->query("SHOW TABLES LIKE 'user_class_permissions'");
            if ($table_check->rowCount() == 0) {
                // Create the table if it doesn't exist
                $create_table = "CREATE TABLE `user_class_permissions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `class_id` int(11) NOT NULL,
                `granted_by` int(11) NOT NULL,
                `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_user_class` (`user_id`, `class_id`),
                KEY `user_id` (`user_id`),
                KEY `class_id` (`class_id`),
                KEY `granted_by` (`granted_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

                if (!$this->conn->exec($create_table)) {
                    error_log("Warning: Could not create user_class_permissions table");
                }
            }

            // Grant class access to user
            $access_query = "INSERT INTO user_class_permissions (user_id, class_id, granted_by, granted_at) 
                        VALUES (?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        granted_by = VALUES(granted_by), 
                        granted_at = VALUES(granted_at)";
            $access_stmt = $this->conn->prepare($access_query);

            if (!$access_stmt->execute([$inquiry_data['user_id'], $inquiry_data['class_id'], $admin_id])) {
                // If this fails, still commit the inquiry approval but log the error
                error_log("Failed to grant class access but inquiry was approved. SQL Error: " . implode(", ", $access_stmt->errorInfo()));
            }

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->conn->rollBack();
            error_log("Error approving inquiry ID {$inquiry_id}: " . $e->getMessage());
            return false;
        }
    }

    // Reject inquiry
    public function reject($inquiry_id, $admin_id, $admin_notes = '')
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'rejected', reviewed_by = ?, admin_notes = ?, reviewed_at = NOW()
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$admin_id, $admin_notes, $inquiry_id]);
    }

    // Bulk approve inquiries
    public function bulkApprove($inquiry_ids, $admin_id, $admin_notes = '')
    {
        try {
            $this->conn->beginTransaction();

            $placeholders = implode(',', array_fill(0, count($inquiry_ids), '?'));

            // Update inquiry statuses
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'approved', reviewed_by = ?, admin_notes = ?, reviewed_at = NOW()
                      WHERE id IN ($placeholders)";
            $params = array_merge([$admin_id, $admin_notes], $inquiry_ids);
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            // Get inquiry details for access granting
            $inquiry_query = "SELECT user_id, class_id FROM " . $this->table_name . " WHERE id IN ($placeholders)";
            $inquiry_stmt = $this->conn->prepare($inquiry_query);
            $inquiry_stmt->execute($inquiry_ids);

            // Grant class access for each inquiry
            while ($inquiry = $inquiry_stmt->fetch(PDO::FETCH_ASSOC)) {
                $access_query = "INSERT INTO user_class_permissions (user_id, class_id, granted_by, granted_at) 
                                VALUES (?, ?, ?, NOW())
                                ON DUPLICATE KEY UPDATE granted_by = ?, granted_at = NOW()";
                $access_stmt = $this->conn->prepare($access_query);
                $access_stmt->execute([$inquiry['user_id'], $inquiry['class_id'], $admin_id, $admin_id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error bulk approving inquiries: " . $e->getMessage());
            return false;
        }
    }

    // Get user's inquiries
    public function getUserInquiries($user_id)
    {
        $query = "SELECT ci.*, c.class_name, c.class_code
                  FROM " . $this->table_name . " ci
                  LEFT JOIN classes c ON ci.class_id = c.id
                  WHERE ci.user_id = ?
                  ORDER BY ci.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt;
    }

    // Get statistics
    public function getStats()
    {
        $stats = [];

        // Total inquiries by status
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $stats['by_status'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats['by_status'][$row['status']] = $row['count'];
        }

        // By country
        $query = "SELECT country, COUNT(*) as count FROM " . $this->table_name . " GROUP BY country ORDER BY count DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $stats['by_country'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats['by_country'][$row['country']] = $row['count'];
        }

        return $stats;
    }

    public function getInquiryDetails($inquiry_id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$inquiry_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting inquiry details: " . $e->getMessage());
            return false;
        }
    }

    public function approveSimple($inquiry_id, $admin_id, $admin_notes = '')
    {
        try {
            // Just update the inquiry status without the class permission part
            $query = "UPDATE " . $this->table_name . " 
                  SET status = 'approved', 
                      reviewed_by = ?, 
                      admin_notes = ?, 
                      reviewed_at = NOW()
                  WHERE id = ? AND status = 'pending'";

            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$admin_id, $admin_notes, $inquiry_id]);

            if ($result && $stmt->rowCount() > 0) {
                return true;
            } else {
                error_log("No rows affected when approving inquiry ID: " . $inquiry_id);
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in approveSimple for inquiry ID {$inquiry_id}: " . $e->getMessage());
            return false;
        }
    }
}
