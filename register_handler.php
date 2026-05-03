<?php
session_start();

// include DB connection (expected to provide $pdo (PDO) or $conn (mysqli) )
require_once __DIR__ . '/database.php';

// Helper to obtain PDO (will create if not present using simple config fallback)
function getPDO() {
    global $pdo, $conn;
    if (isset($pdo) && $pdo instanceof PDO) {
        return $pdo;
    }
    if (isset($conn) && $conn instanceof mysqli) {
        // convert mysqli to PDO-like using new PDO - try to build from mysqli info (limited)
        // safest fallback: create a new PDO from config constants if present
    }
    // Fallback: try config.php constants (DB_HOST, DB_NAME, DB_USER, DB_PASS)
    if (file_exists(__DIR__.'/config.php')) {
        require_once __DIR__.'/config.php';
    }
    $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
    $port = defined('DB_PORT') ? DB_PORT : '3306';
    $db   = defined('DB_NAME') ? DB_NAME : 'u921830511_syllabusms';
    $user = defined('DB_USER') ? DB_USER : 'root';
    $pass = defined('DB_PASS') ? DB_PASS : '';
    $dsn  = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        die("DB connection error: " . $e->getMessage());
    }
}

// Validate & sanitize input
$required = ['username','email','password','full_name'];
foreach ($required as $f) {
    if (empty($_POST[$f])) {
        $_SESSION['reg_error'] = "Field {$f} is required.";
        header('Location: register.php'); // your registration page
        exit;
    }
}

$username = trim($_POST['username']);
$email    = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$fullName = trim($_POST['full_name']);
$password = $_POST['password'];
$address  = isset($_POST['address']) ? trim($_POST['address']) : null;
$gender   = isset($_POST['gender']) ? $_POST['gender'] : null;
$role_id  = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 5; // default pending role
$organization_id = isset($_POST['organization_id']) ? (int)$_POST['organization_id'] : null;
$school_id = isset($_POST['school_id']) ? (int)$_POST['school_id'] : null;

if (! $email) {
    $_SESSION['reg_error'] = "Invalid email.";
    header('Location: register.php');
    exit;
}

// Hash password for storage (we still store hashed password in pending table)
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert into pending_users
$pdo = getPDO();

$sql = "INSERT INTO pending_users
    (username, email, full_name, address, gender, password, role_id, organization_id, school_id, created_at, updated_at)
    VALUES (:username, :email, :full_name, :address, :gender, :password, :role_id, :organization_id, :school_id, NOW(), NOW())";

$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':full_name' => $fullName,
        ':address' => $address,
        ':gender'  => $gender,
        ':password'=> $hash,
        ':role_id' => $role_id,
        ':organization_id' => $organization_id,
        ':school_id' => $school_id
    ]);
    // success
    $_SESSION['reg_success'] = "Registration submitted. Awaiting admin approval.";
    header('Location: register_thanks.php'); // create a small thank-you page
    exit;
} catch (PDOException $e) {
    // log $e->getMessage() in production
    $_SESSION['reg_error'] = "Database error: " . $e->getMessage();
    header('Location: register.php');
    exit;
}
