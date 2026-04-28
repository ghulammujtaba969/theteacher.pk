<?php
// submit-class-inquiry.php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/ClassInquiry.php';
require_once 'classes/User.php';

// Check if user is logged in and is a solo student
if (!is_logged_in()) {
    flash_message('Please log in to submit an inquiry.', 'error');
    redirect('login.php');
}

$current_user = current_user();
if ($current_user['role_name'] !== 'Solo Student') {
    flash_message('Only solo students can submit class inquiries.', 'error');
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$database = new Database();
$db = $database->getConnection();
$classInquiry = new ClassInquiry($db);

// Validate required fields
$required_fields = ['class_id', 'whatsapp_number', 'country', 'address', 'contact_email', 'time_slot'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
}

// Check if inquiry already exists
$class_id = (int)$_POST['class_id'];
if ($classInquiry->inquiryExists($current_user['id'], $class_id)) {
    $errors[] = 'You have already submitted an inquiry for this class.';
}

if (!empty($errors)) {
    flash_message('Please fix the following errors: ' . implode(', ', $errors), 'error');
    redirect('dashboard.php');
}

try {
    // Set inquiry data
    $classInquiry->user_id = $current_user['id'];
    $classInquiry->class_id = $class_id;
    $classInquiry->whatsapp_number = sanitize_input($_POST['whatsapp_number']);
    $classInquiry->country = sanitize_input($_POST['country']);
    $classInquiry->address = sanitize_input($_POST['address']);
    $classInquiry->contact_email = sanitize_input($_POST['contact_email']);
    $classInquiry->preferred_time_slot = sanitize_input($_POST['time_slot']); // Single time slot

    // Create the inquiry
    if ($classInquiry->create()) {
        flash_message('Your class inquiry has been submitted successfully! You will be notified once it is reviewed.', 'success');

        // Optional: Send notification email to admins
        // sendAdminNotification($classInquiry->id);

    } else {
        flash_message('There was an error submitting your inquiry. Please try again.', 'error');
    }
} catch (Exception $e) {
    error_log('Class Inquiry Submission Error: ' . $e->getMessage());
    flash_message('There was an error submitting your inquiry. Please try again.', 'error');
}

redirect('dashboard.php');

// Optional: Function to send admin notification
function sendAdminNotification($inquiry_id)
{
    // Implementation for sending email/notification to admins
    // This would integrate with your existing notification system
}
?>