<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../classes/User.php';
require_once '../classes/ClassAccess.php';

// Check if user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo '<p class="text-danger">Unauthorized access.</p>';
    exit;
}

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$classAccess = new ClassAccess($db);

if (!can('solo_students.manage') && !can('class_access.view')) {
    http_response_code(403);
    echo '<p class="text-danger">Access denied.</p>';
    exit;
}

if (!isset($_POST['student_id'])) {
    echo '<p class="text-danger">Invalid request.</p>';
    exit;
}

$student_id = $_POST['student_id'];

// Get student's class permissions
$permissions = $classAccess->getPermissionsByUser($student_id);

if (empty($permissions)) {
    echo '<div class="text-center py-4">';
    echo '<i class="ph ph-graduation-cap text-gray-400" style="font-size: 3rem;"></i>';
    echo '<p class="text-gray-500 mt-2">This student has no classes assigned yet.</p>';
    echo '</div>';
} else {
    echo '<div class="mb-3">';
    echo '<p class="text-gray-600 mb-3">Manage class access for this student. Click the remove button to revoke access to a specific class.</p>';
    echo '</div>';
    
    echo '<div class="row">';
    foreach ($permissions as $permission) {
        echo '<div class="col-md-6 mb-3">';
        echo '<div class="card border border-gray-100">';
        echo '<div class="card-body p-3">';
        echo '<div class="flex-between align-items-start">';
        echo '<div class="flex-1">';
        echo '<h6 class="mb-1">' . htmlspecialchars($permission['class_name']) . '</h6>';
        echo '<p class="text-13 text-gray-500 mb-1">Code: ' . htmlspecialchars($permission['class_code']) . '</p>';
        echo '<p class="text-12 text-gray-400 mb-0">Granted by: ' . htmlspecialchars($permission['granted_by_username'] ?? 'System') . '</p>';
        echo '<p class="text-12 text-gray-400 mb-0">Date: ' . date('M j, Y', strtotime($permission['created_at'])) . '</p>';
        echo '</div>';
        echo '<div class="flex-column gap-2">';
        echo '<span class="text-13 py-1 px-2 bg-success-50 text-success-600 rounded-pill d-block text-center mb-2">';
        echo '<i class="ph ph-check-circle me-1"></i>Active';
        echo '</span>';
        echo '<button class="btn btn-sm btn-outline-danger remove-class-btn" ';
        echo 'data-student-id="' . $student_id . '" ';
        echo 'data-class-id="' . $permission['class_id'] . '" ';
        echo 'data-class-name="' . htmlspecialchars($permission['class_name']) . '">';
        echo '<i class="ph ph-trash me-1"></i>Remove';
        echo '</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    
    echo '<div class="alert alert-warning mt-3">';
    echo '<div class="flex-align gap-2">';
    echo '<i class="ph ph-warning text-warning-600"></i>';
    echo '<div>';
    echo '<strong>Warning:</strong> Removing class access will prevent the student from accessing lectures, subjects, and syllabi related to that class.';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
