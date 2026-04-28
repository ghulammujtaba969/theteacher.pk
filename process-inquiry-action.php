<?php
// process-inquiry-action.php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/ClassInquiry.php';

// Check if user is logged in and has admin privileges
if (!is_logged_in()) {
    if (isset($_POST['ajax'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    redirect('login.php');
}

$current_user = current_user();
$allowed_roles = ['Super Admin', 'Organization Admin', 'School Admin'];

if (!in_array($current_user['role_name'], $allowed_roles)) {
    if (isset($_POST['ajax'])) {
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }
    redirect('dashboard.php');
}

$database = new Database();
$db = $database->getConnection();
$classInquiry = new ClassInquiry($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $admin_notes = $_POST['admin_notes'] ?? '';

    try {
        switch ($action) {
            case 'approve':
                $inquiry_id = (int)$_POST['inquiry_id'];
                if ($classInquiry->approve($inquiry_id, $current_user['id'], $admin_notes)) {
                    $success = true;
                    $message = 'Inquiry approved successfully!';
                } else {
                    $success = false;
                    $message = 'Failed to approve inquiry.';
                }
                break;

            case 'reject':
                $inquiry_id = (int)$_POST['inquiry_id'];
                if ($classInquiry->reject($inquiry_id, $current_user['id'], $admin_notes)) {
                    $success = true;
                    $message = 'Inquiry rejected successfully!';
                } else {
                    $success = false;
                    $message = 'Failed to reject inquiry.';
                }
                break;

            case 'bulk_approve':
                $inquiry_ids_string = $_POST['inquiry_ids'] ?? '';
                $inquiry_ids = array_filter(array_map('intval', explode(',', $inquiry_ids_string)));

                if (empty($inquiry_ids)) {
                    $success = false;
                    $message = 'No inquiries selected.';
                } else {
                    if ($classInquiry->bulkApprove($inquiry_ids, $current_user['id'], $admin_notes)) {
                        $success = true;
                        $message = count($inquiry_ids) . ' inquiries approved successfully!';
                    } else {
                        $success = false;
                        $message = 'Failed to approve selected inquiries.';
                    }
                }
                break;

            default:
                $success = false;
                $message = 'Invalid action.';
                break;
        }
    } catch (Exception $e) {
        error_log('Inquiry Action Error: ' . $e->getMessage());
        $success = false;
        $message = 'An error occurred while processing the request.';
    }

    // Handle AJAX requests
    if (isset($_POST['ajax']) || isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }

    // Handle regular form submissions
    if ($success) {
        flash_message($message, 'success');
    } else {
        flash_message($message, 'error');
    }
}

redirect('class-inquiries.php');
