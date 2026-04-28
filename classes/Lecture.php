<?php
require_once 'config/database.php';

class Lecture
{
    private $conn;
    private $table_name = "lectures";
    private $files_table = "lecture_files";

    public $id;
    public $lecture_title;
    public $syllabus_id;
    public $lecture_type;
    public $content_url;
    public $text_content;
    public $file_name;
    public $file_size;
    public $duration_minutes;
    public $lecture_order;
    public $description;
    public $image;
    public $created_at;
    public $updated_at;
    public $status;
    public $class_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET lecture_title=:lecture_title, syllabus_id=:syllabus_id, 
                      lecture_type=:lecture_type, content_url=:content_url, 
                      text_content=:text_content, file_name=:file_name, 
                      file_size=:file_size, duration_minutes=:duration_minutes,
                      lecture_order=:lecture_order, description=:description,
                      image=:image, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lecture_title', $this->lecture_title);
        $stmt->bindParam(':syllabus_id', $this->syllabus_id);
        $stmt->bindParam(':lecture_type', $this->lecture_type);
        $stmt->bindParam(':content_url', $this->content_url);
        $stmt->bindParam(':text_content', $this->text_content);
        $stmt->bindParam(':file_name', $this->file_name);
        $stmt->bindParam(':file_size', $this->file_size);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':lecture_order', $this->lecture_order);
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

    // NEW METHOD: Create lecture file entry
    public function createLectureFile($file_data)
    {
        $query = "INSERT INTO " . $this->files_table . "
                  SET lecture_id=:lecture_id, file_type=:file_type, 
                      file_url=:file_url, file_name=:file_name, 
                      file_size=:file_size, text_content=:text_content,
                      duration_minutes=:duration_minutes, display_order=:display_order";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lecture_id', $file_data['lecture_id']);
        $stmt->bindParam(':file_type', $file_data['file_type']);
        $stmt->bindParam(':file_url', $file_data['file_url']);
        $stmt->bindParam(':file_name', $file_data['file_name']);
        $stmt->bindParam(':file_size', $file_data['file_size']);
        $stmt->bindParam(':text_content', $file_data['text_content']);
        $stmt->bindParam(':duration_minutes', $file_data['duration_minutes']);
        $stmt->bindParam(':display_order', $file_data['display_order']);

        return $stmt->execute();
    }

    // NEW METHOD: Get all files for a lecture
    public function getLectureFiles($lecture_id)
    {
        $query = "SELECT * FROM " . $this->files_table . " 
                  WHERE lecture_id = :lecture_id AND status = 'active' 
                  ORDER BY display_order ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lecture_id', $lecture_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW METHOD: Get files by type for a lecture
    public function getLectureFilesByType($lecture_id, $file_type)
    {
        $query = "SELECT * FROM " . $this->files_table . " 
                  WHERE lecture_id = :lecture_id AND file_type = :file_type 
                  AND status = 'active' 
                  ORDER BY display_order ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lecture_id', $lecture_id);
        $stmt->bindParam(':file_type', $file_type);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW METHOD: Delete lecture file
    public function deleteLectureFile($file_id)
    {
        $query = "UPDATE " . $this->files_table . " 
                  SET status = 'inactive' 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $file_id);

        return $stmt->execute();
    }

    // NEW METHOD: Check if lecture has multiple formats
    public function hasMultipleFormats($lecture_id)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->files_table . " 
                  WHERE lecture_id = :lecture_id AND status = 'active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lecture_id', $lecture_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 1;
    }

    public function read($accessible_class_ids = [])
    {
        $query = "SELECT l.*, sy.syllabus_title, sy.subject_id, 
              COALESCE(s.subject_name, 'Course') as subject_name,
              COALESCE(s.subject_code, '') as subject_code,
              COALESCE(c1.class_name, c2.class_name) as class_name,
              COALESCE(c1.class_code, c2.class_code) as class_code,
              COALESCE(c1.id, c2.id) as class_id
              FROM " . $this->table_name . " l
              LEFT JOIN syllabi sy ON l.syllabus_id = sy.id
              LEFT JOIN subjects s ON sy.subject_id = s.id AND s.status = 'active'
              LEFT JOIN classes c1 ON s.class_id = c1.id AND c1.status = 'active'
              LEFT JOIN classes c2 ON sy.class_id = c2.id AND c2.status = 'active'
              WHERE l.status = 'active' AND sy.status = 'active'
              AND (
                  (sy.subject_id IS NOT NULL AND s.id IS NOT NULL AND c1.id IS NOT NULL)
                  OR 
                  (sy.class_id IS NOT NULL AND c2.id IS NOT NULL)
              )";

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND COALESCE(c1.id, c2.id) IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY COALESCE(c1.class_name, c2.class_name) ASC, 
                COALESCE(s.subject_name, 'Course') ASC, 
                sy.syllabus_title ASC, l.lecture_order ASC";

        $stmt = $this->conn->prepare($query);
        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }

        return $stmt;
    }

    public function readBySyllabus($syllabus_id, $accessible_class_ids = [])
    {
        $query = "SELECT l.*, sy.syllabus_title, sy.subject_id,
              COALESCE(s.subject_name, 'Course') as subject_name,
              COALESCE(s.subject_code, '') as subject_code,
              COALESCE(c1.class_name, c2.class_name) as class_name,
              COALESCE(c1.class_code, c2.class_code) as class_code,
              COALESCE(c1.id, c2.id) as class_id
              FROM " . $this->table_name . " l
              LEFT JOIN syllabi sy ON l.syllabus_id = sy.id
              LEFT JOIN subjects s ON sy.subject_id = s.id AND s.status = 'active'
              LEFT JOIN classes c1 ON s.class_id = c1.id AND c1.status = 'active'
              LEFT JOIN classes c2 ON sy.class_id = c2.id AND c2.status = 'active'
              WHERE l.syllabus_id = ? AND l.status = 'active' AND sy.status = 'active'
              AND (
                  (sy.subject_id IS NOT NULL AND s.id IS NOT NULL AND c1.id IS NOT NULL)
                  OR 
                  (sy.class_id IS NOT NULL AND c2.id IS NOT NULL)
              )";

        $params = [$syllabus_id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND COALESCE(c1.id, c2.id) IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $query .= " ORDER BY l.lecture_order ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    public function readOne($accessible_class_ids = [])
    {
        $query = "SELECT l.*, sy.syllabus_title,
              COALESCE(s.subject_name, 'Course') as subject_name,
              COALESCE(s.subject_code, '') as subject_code,
              COALESCE(c1.class_name, c2.class_name) as class_name,
              COALESCE(c1.class_code, c2.class_code) as class_code,
              COALESCE(c1.id, c2.id) as class_id,
              COALESCE(s.id, 0) as subject_id
              FROM " . $this->table_name . " l
              LEFT JOIN syllabi sy ON l.syllabus_id = sy.id
              LEFT JOIN subjects s ON sy.subject_id = s.id AND s.status = 'active'
              LEFT JOIN classes c1 ON s.class_id = c1.id AND c1.status = 'active'
              LEFT JOIN classes c2 ON sy.class_id = c2.id AND c2.status = 'active'
              WHERE l.id = ? AND l.status = 'active' AND sy.status = 'active'
              AND (
                  (sy.subject_id IS NOT NULL AND s.id IS NOT NULL AND c1.id IS NOT NULL)
                  OR 
                  (sy.class_id IS NOT NULL AND c2.id IS NOT NULL)
              )";

        $params = [$this->id];

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND COALESCE(c1.id, c2.id) IN (" . $placeholders . ")";
            $params = array_merge($params, $accessible_class_ids);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->lecture_title = $row['lecture_title'];
            $this->syllabus_id = $row['syllabus_id'];
            $this->lecture_type = $row['lecture_type'];
            $this->content_url = $row['content_url'];
            $this->text_content = $row['text_content'];
            $this->file_name = $row['file_name'];
            $this->file_size = $row['file_size'];
            $this->duration_minutes = $row['duration_minutes'];
            $this->lecture_order = $row['lecture_order'];
            $this->description = $row['description'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->class_id = $row['class_id'];
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET lecture_title=:lecture_title, syllabus_id=:syllabus_id, 
                      lecture_type=:lecture_type, content_url=:content_url, 
                      text_content=:text_content, file_name=:file_name, 
                      file_size=:file_size, duration_minutes=:duration_minutes,
                      lecture_order=:lecture_order, description=:description";

        if ($this->image !== null) {
            $query .= ", image=:image";
        }

        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lecture_title', $this->lecture_title);
        $stmt->bindParam(':syllabus_id', $this->syllabus_id);
        $stmt->bindParam(':lecture_type', $this->lecture_type);
        $stmt->bindParam(':content_url', $this->content_url);
        $stmt->bindParam(':text_content', $this->text_content);
        $stmt->bindParam(':file_name', $this->file_name);
        $stmt->bindParam(':file_size', $this->file_size);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':lecture_order', $this->lecture_order);
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

    public function getCount($accessible_class_ids = [])
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " l
                  LEFT JOIN syllabi sy ON l.syllabus_id = sy.id
                  LEFT JOIN subjects s ON sy.subject_id = s.id
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE l.status = 'active' AND sy.status = 'active' AND s.status = 'active' AND c.status = 'active'";

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND c.id IN (" . $placeholders . ")";
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getCountByClassIds($class_ids)
    {
        if (empty($class_ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " l
                  LEFT JOIN syllabi sy ON l.syllabus_id = sy.id
                  LEFT JOIN subjects s ON sy.subject_id = s.id
                  LEFT JOIN classes c ON s.class_id = c.id
                  WHERE l.status = 'active' AND sy.status = 'active' AND s.status = 'active' AND c.status = 'active' AND c.id IN (" . $placeholders . ")";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($class_ids);
        $row = $stmt->fetch();
        return $row['total'];
    }

    public function getActiveSyllabi($accessible_class_ids = [])
    {
        $query = "SELECT 
                    sy.id, 
                    sy.syllabus_title,
                    COALESCE(s.subject_name, 'Course') as subject_name,
                    COALESCE(s.subject_code, '') as subject_code,
                    COALESCE(c1.class_name, c2.class_name) as class_name,
                    COALESCE(c1.class_code, c2.class_code) as class_code,
                    COALESCE(c1.id, c2.id) as class_id
                  FROM syllabi sy
                  LEFT JOIN subjects s ON sy.subject_id = s.id AND s.status = 'active'
                  LEFT JOIN classes c1 ON s.class_id = c1.id AND c1.status = 'active'
                  LEFT JOIN classes c2 ON sy.class_id = c2.id AND c2.status = 'active'
                  WHERE sy.status = 'active' 
                  AND (
                      (sy.subject_id IS NOT NULL AND s.id IS NOT NULL AND c1.id IS NOT NULL)
                      OR 
                      (sy.class_id IS NOT NULL AND c2.id IS NOT NULL)
                  )";

        if (!empty($accessible_class_ids)) {
            $placeholders = implode(',', array_fill(0, count($accessible_class_ids), '?'));
            $query .= " AND COALESCE(c1.id, c2.id) IN (" . $placeholders . ")";
        }

        $query .= " ORDER BY class_name ASC, subject_name ASC, sy.syllabus_title ASC";

        $stmt = $this->conn->prepare($query);
        if (!empty($accessible_class_ids)) {
            $stmt->execute($accessible_class_ids);
        } else {
            $stmt->execute();
        }
        return $stmt;
    }

    public function getNextOrder($syllabus_id)
    {
        $query = "SELECT MAX(lecture_order) as max_order FROM " . $this->table_name . " WHERE syllabus_id = :syllabus_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':syllabus_id', $syllabus_id);
        $stmt->execute();
        $row = $stmt->fetch();
        return ($row['max_order'] ?? 0) + 1;
    }

    public function handleFileUpload($file_input)
    {
        $errors = validate_file_upload($file_input);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $target_directory = "uploads/";
        $file_name = generate_unique_filename($file_input['name']);
        $target_file = $target_directory . $file_name;

        if (move_uploaded_file($file_input['tmp_name'], $target_file)) {
            return ['success' => true, 'path' => $target_file, 'original_name' => $file_input['name'], 'size' => $file_input['size']];
        } else {
            return ['success' => false, 'errors' => ['Failed to upload file.']];
        }
    }

    public function handleImageUpload($file, $old_image = null)
    {
        $upload_dir = 'uploads/lectures/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return $old_image;
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
        }

        if ($file['size'] > $max_size) {
            throw new Exception('File size exceeds 5MB limit.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'lecture_' . uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            if ($old_image && file_exists($old_image)) {
                unlink($old_image);
            }
            return $filepath;
        } else {
            throw new Exception('Failed to upload image.');
        }
    }

    public function deletePptxViewerFile($lecture_id)
    {
        $query = "SELECT content_url, file_name, lecture_type FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $lecture_id);
        $stmt->execute();
        $lecture_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lecture_info && $lecture_info['lecture_type'] === 'pptx_viewer' && !empty($lecture_info['file_name'])) {
            require_once 'config/config.php';
            $file_path = UPLOAD_DIR . $lecture_info['file_name'];

            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    return true;
                } else {
                    error_log("Failed to delete PPTX viewer file: " . $file_path);
                    return false;
                }
            }
        } elseif ($lecture_info && $lecture_info['lecture_type'] === 'pptx_viewer' && empty($lecture_info['file_name'])) {
            error_log("PPTX viewer lecture (ID: " . $lecture_id . ") has no associated file_name.");
        } elseif ($lecture_info && $lecture_info['lecture_type'] !== 'pptx_viewer') {
            return true;
        }
        return false;
    }
}