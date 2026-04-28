<?php
class Database {
    // private $host = 'localhost';
    // private $db_name = 'syllabus_management';
    // private $username = 'root';
    // private $password = '';
    // private $conn;
    private $host = 'localhost';
    private $db_name = 'u921830511_teacherpk';
    private $username = 'u921830511_teacherpk_user';
    private $password = '0Hwmc!c5p;';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
