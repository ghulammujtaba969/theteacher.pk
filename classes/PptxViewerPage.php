<?php
require_once 'config/database.php';

class PptxViewerPage {
    private $conn;
    private $table_name = "pptx_viewer_pages";

    public $id;
    public $page_title;
    public $slug;
    public $pptx_embed_url;
    public $generated_file_path;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new PPTX viewer page record
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET page_title=:page_title, slug=:slug, 
                      pptx_embed_url=:pptx_embed_url, generated_file_path=:generated_file_path";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->page_title = htmlspecialchars(strip_tags($this->page_title));
        $this->slug = htmlspecialchars(strip_tags($this->slug));
        // URL should be sanitized in the form before passing here
        $this->pptx_embed_url = htmlspecialchars(strip_tags($this->pptx_embed_url)); 
        $this->generated_file_path = htmlspecialchars(strip_tags($this->generated_file_path));

        // Bind parameters
        $stmt->bindParam(':page_title', $this->page_title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':pptx_embed_url', $this->pptx_embed_url);
        $stmt->bindParam(':generated_file_path', $this->generated_file_path);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read all PPTX viewer pages
    public function read() {
        $query = "SELECT id, page_title, slug, pptx_embed_url, generated_file_path, created_at, updated_at
                  FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a single PPTX viewer page by ID
    public function readOne() {
        $query = "SELECT id, page_title, slug, pptx_embed_url, generated_file_path, created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->page_title = $row['page_title'];
            $this->slug = $row['slug'];
            $this->pptx_embed_url = $row['pptx_embed_url'];
            $this->generated_file_path = $row['generated_file_path'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update an existing PPTX viewer page record
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET page_title=:page_title, slug=:slug, 
                      pptx_embed_url=:pptx_embed_url, generated_file_path=:generated_file_path,
                      updated_at=CURRENT_TIMESTAMP
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->page_title = htmlspecialchars(strip_tags($this->page_title));
        $this->slug = htmlspecialchars(strip_tags($this->slug));
        $this->pptx_embed_url = htmlspecialchars(strip_tags($this->pptx_embed_url));
        $this->generated_file_path = htmlspecialchars(strip_tags($this->generated_file_path));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(':page_title', $this->page_title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':pptx_embed_url', $this->pptx_embed_url);
        $stmt->bindParam(':generated_file_path', $this->generated_file_path);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a PPTX viewer page record
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all pages for dropdowns, etc.
    public function getAllPages() {
        $query = "SELECT id, page_title, slug, generated_file_path FROM " . $this->table_name . " ORDER BY page_title ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Generate a unique slug
    public function generateUniqueSlug($title, $exclude_id = 0) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $original_slug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $exclude_id)) {
            $slug = $original_slug . '-' . $counter++;
        }
        return $slug;
    }

    // Check if slug already exists (for unique constraint)
    private function slugExists($slug, $exclude_id = 0) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE slug = :slug AND id != :exclude_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':exclude_id', $exclude_id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
