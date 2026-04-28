<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/ClassInquiry.php';

if (!is_logged_in()) {
    flash_message('Please log in first.', 'error');
    redirect('login.php');
}

$current_user = current_user();
$role = $_SESSION['role'] ?? '';
// Allow solo_student and student to open inquiry
if (!in_array($role, ['solo_student', 'student'])) {
    flash_message('Only students can submit registration requests.', 'error');
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$required = ['class_id','country','phone','gender','address'];
$missing = [];
foreach ($required as $f) {
    if (empty($_POST[$f])) { $missing[] = $f; }
}
if (!empty($missing)) {
    flash_message('Please provide: '.implode(', ', $missing), 'error');
    redirect('course-detail.php?id='.(int)($_POST['class_id'] ?? 0));
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$inquiry = new ClassInquiry($db);

$class_id = (int)$_POST['class_id'];
$redirect_to = isset($_POST['redirect_to']) ? trim($_POST['redirect_to']) : '';
$country = trim($_POST['country']);
$phone = trim($_POST['phone']);
$gender = trim($_POST['gender']);
$address = trim($_POST['address']);
$contact_email = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : ($current_user['email'] ?? '');
$time_slot = isset($_POST['time_slot']) ? trim($_POST['time_slot']) : '';

// Update user basic info
$user->updateBasicInfo($current_user['id'], [
    'country' => $country,
    'phone' => $phone,
    'gender' => $gender,
    'address' => $address,
    'whatsapp_number' => $phone,
]);

// Avoid duplicate inquiry
if ($inquiry->inquiryExists($current_user['id'], $class_id)) {
    flash_message('You have already submitted a registration request for this course.', 'info');
    redirect('course-detail.php?id='.$class_id);
}

// Create inquiry record
$inquiry->user_id = $current_user['id'];
$inquiry->class_id = $class_id;
$inquiry->whatsapp_number = $phone;
$inquiry->country = $country;
$inquiry->address = $address;
$inquiry->contact_email = $contact_email;
$inquiry->preferred_time_slot = $time_slot;

if ($inquiry->create()) {
    flash_message('Registration request submitted! We will review and enroll you to a batch.', 'success');
} else {
    flash_message('Could not submit registration request. Please try again.', 'error');
}

// Optional redirect back to a specific action (e.g., enrollment page)
if (!empty($redirect_to)) {
    // Basic safety: allow only internal relative redirects
    if (strpos($redirect_to, 'http://') === false && strpos($redirect_to, 'https://') === false) {
        redirect(ltrim($redirect_to, '/'));
    }
}

redirect('course-detail.php?id='.$class_id);

?>
