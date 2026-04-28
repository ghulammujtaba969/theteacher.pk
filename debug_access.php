<?php
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$classModel = new ClassModel($db);

echo "<h2>Debug Class Access</h2>";

// --- Check Super Admin's can_access_all_classes status ---
echo "<h3>Super Admin Status</h3>";
$superAdminUsername = 'superadmin'; // Assuming this is your Super Admin username
$superAdminData = null;

try {
    $query = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$superAdminUsername]);
    $superAdminData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($superAdminData) {
        echo "<p>Super Admin ('{$superAdminUsername}') Found:</p>";
        echo "<ul>";
        echo "<li>ID: {$superAdminData['id']}</li>";
        echo "<li>Role: {$superAdminData['role_name']}</li>";
        echo "<li>can_access_all_classes: " . ($superAdminData['can_access_all_classes'] ? 'Yes (1)' : 'No (0)') . "</li>";
        echo "</ul>";

        if (!($superAdminData['can_access_all_classes'] ?? 0)) {
            echo "<p style=\"color: red;\">**ACTION REQUIRED**: Super Admin does NOT have 'can_access_all_classes' set to 1. Please update your database:</p>";
            echo "<pre>UPDATE users SET can_access_all_classes = 1 WHERE id = {$superAdminData['id']};</pre>";
        }
    } else {
        echo "<p style=\"color: red;\">Super Admin user with username '{$superAdminUsername}' not found. Please ensure it exists.</p>";
    }
} catch (PDOException $e) {
    echo "<p style=\"color: red;\">Error fetching Super Admin data: " . $e->getMessage() . "</p>";
}

// --- List all active classes ---
echo "<h3>All Active Classes</h3>";
$allClasses = [];
try {
    $stmt = $classModel->read();
    $allClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($allClasses)) {
        echo "<p>Found " . count($allClasses) . " active classes:</p>";
        echo "<ul>";
        foreach ($allClasses as $class) {
            echo "<li>ID: {$class['id']}, Name: {$class['class_name']} ({$class['class_code']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style=\"color: red;\">No active classes found in the database. Please add some classes via the 'Classes' management page (if you are Super Admin).</p>";
    }
} catch (PDOException $e) {
    echo "<p style=\"color: red;\">Error fetching all active classes: " . $e->getMessage() . "</p>";
}

// --- Check permissions for a non-Super Admin (e.g., Organization Admin ID 2) ---
echo "<h3>Example Organization Admin Accessible Classes (User ID 2)</h3>";
// You would replace 2 with an actual ID of an Organization Admin
$orgAdminId = 2; 
$orgAdminUser = null;

try {
    $orgAdminUser = $user->getById($orgAdminId);
    if ($orgAdminUser) {
        echo "<p>Organization Admin ('{$orgAdminUser['username']}') Accessible Classes:</p>";
        $accessibleOrgAdminClasses = $user->getAccessibleClasses($orgAdminUser);
        if (!empty($accessibleOrgAdminClasses)) {
            echo "<ul>";
            foreach ($accessibleOrgAdminClasses as $class) {
                echo "<li>ID: {$class['id']}, Name: {$class['class_name']} ({$class['class_code']})</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>This Organization Admin has no classes assigned. Super Admin needs to assign classes to this user via 'Class Access'.</p>";
        }
    } else {
        echo "<p style=\"color: orange;\">Organization Admin with ID {$orgAdminId} not found. Skipping check.</p>";
    }
} catch (PDOException $e) {
    echo "<p style=\"color: red;\">Error fetching Organization Admin classes: " . $e->getMessage() . "</p>";
}

?>
