<?php
// request_class_access.php - Create this new file
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$current_user = current_user();

// Check if user is solo student
if ($current_user['role_name'] !== 'Solo Student') {
    flash_message('Access denied. Only solo students can request class access.', 'error');
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $class_id = (int) $_POST['class_id'];
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    $user = new User($pdo);
    $classModel = new ClassModel($pdo);
    
    // Check if class exists
    $classModel->id = $class_id;
    if (!$classModel->readOne()) {
        flash_message('Class not found.', 'error');
        redirect('dashboard.php');
    }
    
    // Check if user already has access
    if ($user->hasAccessToClass($current_user['id'], $class_id)) {
        flash_message('You already have access to this class.', 'info');
        redirect('dashboard.php');
    }
    
    try {
        // Add to pending requests or directly assign (depending on your workflow)
        // For now, let's directly assign access
        require_once 'classes/ClassAccess.php';
        $classAccess = new ClassAccess($pdo);
        
        if ($classAccess->assignAccess($current_user['id'], $class_id, 1)) { // 1 = system/auto-assigned
            flash_message('Successfully enrolled in class: ' . htmlspecialchars($classModel->class_name), 'success');
        } else {
            flash_message('Failed to enroll in class. Please try again.', 'error');
        }
    } catch (Exception $e) {
        error_log("Error requesting class access: " . $e->getMessage());
        flash_message('An error occurred while processing your request.', 'error');
    }
} else {
    flash_message('Invalid request.', 'error');
}

redirect('dashboard.php');
?>