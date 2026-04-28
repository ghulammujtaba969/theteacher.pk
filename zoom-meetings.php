<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/ZoomMeeting.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';

// Check if user is logged in
require_roles(['super_admin', 'organization_admin', 'school_admin', 'teacher', 'solo_student']);

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$zoomMeeting = new ZoomMeeting($db);
$user = new User($db);

// Get accessible class IDs for the current user
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$meeting_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


// Handle form submissions (only super admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_role === 'super_admin') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $zoomMeeting->meeting_title = sanitize_input($_POST['meeting_title']);
                $zoomMeeting->meeting_description = sanitize_input($_POST['meeting_description']);
                $zoomMeeting->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
                $zoomMeeting->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
                $zoomMeeting->syllabus_id = !empty($_POST['syllabus_id']) ? (int)$_POST['syllabus_id'] : null;
                $zoomMeeting->lecture_id = !empty($_POST['lecture_id']) ? (int)$_POST['lecture_id'] : null;
                $zoomMeeting->passcode = sanitize_input($_POST['passcode']);
                $zoomMeeting->scheduled_date = $_POST['scheduled_date'];
                $zoomMeeting->duration_minutes = (int)$_POST['duration_minutes'];
                $zoomMeeting->host_email = sanitize_input($_POST['host_email']);
                $zoomMeeting->max_participants = (int)$_POST['max_participants'];
                $zoomMeeting->created_by = $current_user['id'];

                // Recurring meeting settings
                $zoomMeeting->is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
                if ($zoomMeeting->is_recurring) {
                    $zoomMeeting->recurrence_type = $_POST['recurrence_type'] ?? 'daily';
                    $zoomMeeting->recurrence_interval = (int)($_POST['recurrence_interval'] ?? 1);
                    
                    // Weekly recurrence days
                    if ($zoomMeeting->recurrence_type === 'weekly' && isset($_POST['recurrence_days'])) {
                        $zoomMeeting->recurrence_days = implode(',', $_POST['recurrence_days']);
                    }
                    
                    // End date or end times
                    if (isset($_POST['recurrence_end_type']) && $_POST['recurrence_end_type'] === 'date') {
                        $zoomMeeting->recurrence_end_date = $_POST['recurrence_end_date'] ?? null;
                        $zoomMeeting->recurrence_end_times = null;
                    } elseif (isset($_POST['recurrence_end_type']) && $_POST['recurrence_end_type'] === 'times') {
                        $zoomMeeting->recurrence_end_times = (int)($_POST['recurrence_end_times'] ?? 10);
                        $zoomMeeting->recurrence_end_date = null;
                    }
                }

                // Advanced settings
                $zoomMeeting->waiting_room = isset($_POST['waiting_room']) ? 1 : 0;
                $zoomMeeting->join_before_host = isset($_POST['join_before_host']) ? 1 : 0;
                $zoomMeeting->mute_upon_entry = isset($_POST['mute_upon_entry']) ? 1 : 0;
                $zoomMeeting->host_video = isset($_POST['host_video']) ? 1 : 0;
                $zoomMeeting->participant_video = isset($_POST['participant_video']) ? 1 : 0;
                $zoomMeeting->auto_recording = $_POST['auto_recording'] ?? 'none';
                $zoomMeeting->approval_type = (int)$_POST['approval_type'];
                $zoomMeeting->audio_type = $_POST['audio_type'] ?? 'both';
                $zoomMeeting->allow_multiple_devices = isset($_POST['allow_multiple_devices']) ? 1 : 0;
                $zoomMeeting->screen_sharing = $_POST['screen_sharing'] ?? 'all';
                $zoomMeeting->enable_chat = isset($_POST['enable_chat']) ? 1 : 0;
                $zoomMeeting->enable_private_chat = isset($_POST['enable_private_chat']) ? 1 : 0;
                $zoomMeeting->enable_raise_hand = isset($_POST['enable_raise_hand']) ? 1 : 0;
                $zoomMeeting->enable_reactions = isset($_POST['enable_reactions']) ? 1 : 0;
                $zoomMeeting->enable_breakout_rooms = isset($_POST['enable_breakout_rooms']) ? 1 : 0;

                // Validate
                $errors = [];
                if (empty($zoomMeeting->meeting_title)) {
                    $errors[] = 'Meeting title is required.';
                }
                if (empty($zoomMeeting->scheduled_date)) {
                    $errors[] = 'Scheduled date and time is required.';
                }
                
                // Validate datetime is in the future
                $scheduled_time = strtotime($zoomMeeting->scheduled_date);
                if ($scheduled_time <= time()) {
                    $errors[] = 'Scheduled date must be in the future.';
                }
                
                // Validate recurring meeting settings
                if ($zoomMeeting->is_recurring) {
                    if ($zoomMeeting->recurrence_type === 'weekly' && empty($zoomMeeting->recurrence_days)) {
                        $errors[] = 'Please select at least one day for weekly recurrence.';
                    }
                    if (empty($zoomMeeting->recurrence_end_date) && empty($zoomMeeting->recurrence_end_times)) {
                        $errors[] = 'Please specify when the recurring meeting should end.';
                    }
                }

                if (empty($errors)) {
                    if ($zoomMeeting->create()) {
                        flash_message('Meeting scheduled successfully in Zoom!', 'success');
                        redirect('zoom-meetings.php');
                    } else {
                        // Get detailed error from ZoomMeeting
                        $error_detail = $zoomMeeting->getLastError();
                        if ($error_detail) {
                            flash_message('Error: ' . $error_detail, 'error');
                        } else {
                            flash_message('Error scheduling meeting. Please check error logs for details.', 'error');
                        }
                    }
                }
                break;

            case 'update':
                $zoomMeeting->id = (int)$_POST['id'];
                
                // Load existing meeting data first
                if (!$zoomMeeting->readOne()) {
                    flash_message('Meeting not found.', 'error');
                    redirect('zoom-meetings.php');
                    exit;
                }
                
                $zoomMeeting->meeting_title = sanitize_input($_POST['meeting_title']);
                $zoomMeeting->meeting_description = sanitize_input($_POST['meeting_description']);
                $zoomMeeting->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
                $zoomMeeting->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
                $zoomMeeting->syllabus_id = !empty($_POST['syllabus_id']) ? (int)$_POST['syllabus_id'] : null;
                $zoomMeeting->lecture_id = !empty($_POST['lecture_id']) ? (int)$_POST['lecture_id'] : null;
                $zoomMeeting->passcode = sanitize_input($_POST['passcode']);
                $zoomMeeting->scheduled_date = $_POST['scheduled_date'];
                $zoomMeeting->duration_minutes = (int)$_POST['duration_minutes'];
                $zoomMeeting->host_email = sanitize_input($_POST['host_email']);
                $zoomMeeting->max_participants = (int)$_POST['max_participants'];

                // Recurring meeting settings
                $zoomMeeting->is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
                if ($zoomMeeting->is_recurring) {
                    $zoomMeeting->recurrence_type = $_POST['recurrence_type'] ?? 'daily';
                    $zoomMeeting->recurrence_interval = (int)($_POST['recurrence_interval'] ?? 1);
                    
                    // Weekly recurrence days
                    if ($zoomMeeting->recurrence_type === 'weekly' && isset($_POST['recurrence_days'])) {
                        $zoomMeeting->recurrence_days = implode(',', $_POST['recurrence_days']);
                    }
                    
                    // End date or end times
                    if (isset($_POST['recurrence_end_type']) && $_POST['recurrence_end_type'] === 'date') {
                        $zoomMeeting->recurrence_end_date = $_POST['recurrence_end_date'] ?? null;
                        $zoomMeeting->recurrence_end_times = null;
                    } elseif (isset($_POST['recurrence_end_type']) && $_POST['recurrence_end_type'] === 'times') {
                        $zoomMeeting->recurrence_end_times = (int)($_POST['recurrence_end_times'] ?? 10);
                        $zoomMeeting->recurrence_end_date = null;
                    }
                }

                // Advanced settings
                $zoomMeeting->waiting_room = isset($_POST['waiting_room']) ? 1 : 0;
                $zoomMeeting->join_before_host = isset($_POST['join_before_host']) ? 1 : 0;
                $zoomMeeting->mute_upon_entry = isset($_POST['mute_upon_entry']) ? 1 : 0;
                $zoomMeeting->host_video = isset($_POST['host_video']) ? 1 : 0;
                $zoomMeeting->participant_video = isset($_POST['participant_video']) ? 1 : 0;
                $zoomMeeting->auto_recording = $_POST['auto_recording'] ?? 'none';
                $zoomMeeting->approval_type = (int)$_POST['approval_type'];
                $zoomMeeting->audio_type = $_POST['audio_type'] ?? 'both';
                $zoomMeeting->allow_multiple_devices = isset($_POST['allow_multiple_devices']) ? 1 : 0;
                $zoomMeeting->screen_sharing = $_POST['screen_sharing'] ?? 'all';
                $zoomMeeting->enable_chat = isset($_POST['enable_chat']) ? 1 : 0;
                $zoomMeeting->enable_private_chat = isset($_POST['enable_private_chat']) ? 1 : 0;
                $zoomMeeting->enable_raise_hand = isset($_POST['enable_raise_hand']) ? 1 : 0;
                $zoomMeeting->enable_reactions = isset($_POST['enable_reactions']) ? 1 : 0;
                $zoomMeeting->enable_breakout_rooms = isset($_POST['enable_breakout_rooms']) ? 1 : 0;

                // Validate
                $errors = [];
                if (empty($zoomMeeting->meeting_title)) {
                    $errors[] = 'Meeting title is required.';
                }
                if (empty($zoomMeeting->scheduled_date)) {
                    $errors[] = 'Scheduled date and time is required.';
                }
                
                // Validate recurring meeting settings
                if ($zoomMeeting->is_recurring) {
                    if ($zoomMeeting->recurrence_type === 'weekly' && empty($zoomMeeting->recurrence_days)) {
                        $errors[] = 'Please select at least one day for weekly recurrence.';
                    }
                    if (empty($zoomMeeting->recurrence_end_date) && empty($zoomMeeting->recurrence_end_times)) {
                        $errors[] = 'Please specify when the recurring meeting should end.';
                    }
                }

                if (empty($errors)) {
                    if ($zoomMeeting->update()) {
                        $update_error = $zoomMeeting->getLastError();
                        if ($update_error) {
                            flash_message('Meeting updated in database, but Zoom update failed: ' . $update_error, 'warning');
                        } else {
                            flash_message('Meeting updated successfully!', 'success');
                        }
                        redirect('zoom-meetings.php');
                    } else {
                        flash_message('Error updating meeting.', 'error');
                    }
                }
                break;

            case 'delete':
                $zoomMeeting->id = (int)$_POST['id'];
                
                // Load meeting data
                if (!$zoomMeeting->readOne()) {
                    flash_message('Meeting not found.', 'error');
                    redirect('zoom-meetings.php');
                    exit;
                }
                
                if ($zoomMeeting->delete()) {
                    $delete_error = $zoomMeeting->getLastError();
                    if ($delete_error) {
                        flash_message('Meeting cancelled in database, but Zoom deletion failed: ' . $delete_error, 'warning');
                    } else {
                        flash_message('Meeting cancelled successfully!', 'success');
                    }
                } else {
                    flash_message('Error cancelling meeting.', 'error');
                }
                redirect('zoom-meetings.php');
                break;
        }
    }
}
// Get meeting data for edit
if ($action == 'edit' && $meeting_id > 0) {
    $zoomMeeting->id = $meeting_id;
    if (!$zoomMeeting->readOne()) {
        flash_message('Meeting not found.', 'error');
        redirect('zoom-meetings.php');
    }
}

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoom Meetings - <?php echo APP_NAME; ?></title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- file upload -->
    <link rel="stylesheet" href="assets/css/file-upload.css">
    <!-- file upload -->
    <link rel="stylesheet" href="assets/css/plyr.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <!-- full calendar -->
    <link rel="stylesheet" href="assets/css/full-calendar.css">
    <!-- jquery Ui -->
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <!-- editor quill Ui -->
    <link rel="stylesheet" href="assets/css/editor-quill.css">
    <!-- apex charts Css -->
    <link rel="stylesheet" href="assets/css/apexcharts.css">
    <!-- calendar Css -->
    <link rel="stylesheet" href="assets/css/calendar.css">
    <!-- jvector map Css -->
    <link rel="stylesheet" href="assets/css/jquery-jvectormap-2.0.5.css">
    <!-- Main css -->
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
    /* Add hover effect for copy buttons */
    .btn-outline-main:hover {
        transform: scale(1.05);
        transition: transform 0.2s;
    }
    
    /* Toast animation */
    #copyToast {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Copy button styling */
    .btn-link:hover {
        text-decoration: none;
    }
    
    /* Settings section styling */
    .settings-section {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f9fafb;
    }
    
    .settings-section h6 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .form-check {
        padding: 8px 0;
    }
    
    .form-check-label {
        margin-left: 8px;
        font-weight: 500;
    }
    
    .setting-description {
        font-size: 12px;
        color: #6b7280;
        margin-left: 28px;
        margin-top: -5px;
    }
    
    .nav-tabs .nav-link {
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
        background: transparent;
    }
    
    .badge-setting {
        font-size: 11px;
        padding: 2px 8px;
        margin-left: 5px;
    }
    
    /* Recurring meeting styles */
    .recurring-options {
        display: none;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
    }
    
    .recurring-options.active {
        display: block;
    }
    
    .day-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .day-checkbox {
        display: none;
    }
    
    .day-label {
        display: inline-block;
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .day-checkbox:checked + .day-label {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    
    .day-label:hover {
        border-color: #4f46e5;
    }
</style>
</head>
<body>

<!--==================== Preloader Start ====================-->
  <div class="preloader">
    <div class="loader"></div>
  </div>
<!--==================== Preloader End ====================-->

<!--==================== Sidebar Overlay End ====================-->
<div class="side-overlay"></div>
<!--==================== Sidebar Overlay End ====================-->

    <!-- ============================ Sidebar Start ============================ -->
    <?php include 'includes/sidebar_new.php'; ?>
    <!-- ============================ Sidebar End  ============================ -->

    <div class="dashboard-main-wrapper">
        <!-- ============================ Top Navbar Start ============================ -->
        <?php include 'includes/navbar_new.php'; ?>
        <!-- ============================ Top Navbar End ============================ -->
        
        <div class="dashboard-body">
            
            <!-- Breadcrumb -->
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">
                        <?php 
                        switch($action) {
                            case 'add': echo 'Schedule Meeting'; break;
                            case 'edit': echo 'Edit Meeting'; break;
                            default: echo 'Zoom Meetings'; break;
                        }
                        ?>
                    </span></li>
                </ul>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <!-- Meetings List -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <ul class="nav nav-pills common-tab gap-20">
                                <li class="nav-item">
                                    <button class="nav-link active">All Meetings (<?php 
                                        $stmt = $zoomMeeting->read($can_access_all_classes_flag ? [] : $accessible_class_ids, $user_role);
                                        echo $stmt->rowCount();
                                    ?>)</button>
                                </li>
                            </ul>
                            
                            <?php if ($user_role === 'super_admin'): ?>
                            <a href="zoom-meetings.php?action=add" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                <span class="d-flex text-md"><i class="ph ph-plus"></i></span> 
                                Schedule New Meeting
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row g-20">
                            <?php
                            $stmt = $zoomMeeting->read($can_access_all_classes_flag ? [] : $accessible_class_ids, $user_role);
                            
                            if ($stmt->rowCount() > 0) {
                                while ($row = $stmt->fetch()) {
                                    $meeting_datetime = new DateTime($row['scheduled_date']);
                                    $now = new DateTime();
                                    $is_upcoming = $meeting_datetime > $now;
                                    $status_class = $is_upcoming ? 'success' : 'secondary';
                                    $status_text = $is_upcoming ? 'Upcoming' : 'Past';
                            ?>
                            <div class="col-xxl-4 col-lg-6">
                                <div class="card border border-gray-100">
                                    <div class="card-body p-16">
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="w-40 h-40 bg-info-600 text-white rounded-circle flex-center">
                                                <i class="ph ph-video-camera"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($row['meeting_title']); ?></h6>
                                                <span class="text-13 text-gray-400"><?php echo $row['duration_minutes']; ?> minutes</span>
                                            </div>
                                            <div class="d-flex flex-column gap-4 align-items-end">
                                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                <?php if ($row['is_recurring']): ?>
                                                    <span class="badge bg-purple-100 text-purple-600">
                                                        <i class="ph ph-repeat"></i> Recurring
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($row['meeting_description'])): ?>
                                        <p class="text-13 text-gray-600 mb-16"><?php echo htmlspecialchars(substr($row['meeting_description'], 0, 100)); ?><?php echo strlen($row['meeting_description']) > 100 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="mb-16">
                                            <div class="flex-align gap-4 mb-8">
                                                <i class="ph ph-calendar text-main-600"></i>
                                                <span class="text-13 text-gray-600"><?php echo $meeting_datetime->format('M d, Y'); ?></span>
                                            </div>
                                            <div class="flex-align gap-4">
                                                <i class="ph ph-clock text-main-600"></i>
                                                <span class="text-13 text-gray-600"><?php echo $meeting_datetime->format('h:i A'); ?></span>
                                            </div>
                                            
                                            <?php if ($row['is_recurring']): ?>
                                            <div class="flex-align gap-4 mt-8">
                                                <i class="ph ph-repeat text-purple-600"></i>
                                                <span class="text-13 text-gray-600">
                                                    Repeats <?php echo ucfirst($row['recurrence_type']); ?>
                                                    <?php if ($row['recurrence_type'] === 'weekly' && !empty($row['recurrence_days'])): ?>
                                                        on <?php 
                                                        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                        $selected_days = explode(',', $row['recurrence_days']);
                                                        $day_names = array_map(function($d) use ($days) { return $days[$d - 1]; }, $selected_days);
                                                        echo implode(', ', $day_names);
                                                        ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Meeting Settings Badges -->
                                        <div class="mb-16">
                                            <?php if ($row['waiting_room']): ?>
                                                <span class="badge bg-info-100 text-info-600 text-11 mb-4 me-4">
                                                    <i class="ph ph-door-open"></i> Waiting Room
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($row['mute_upon_entry']): ?>
                                                <span class="badge bg-warning-100 text-warning-600 text-11 mb-4 me-4">
                                                    <i class="ph ph-microphone-slash"></i> Auto Mute
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($row['auto_recording'] !== 'none'): ?>
                                                <span class="badge bg-danger-100 text-danger-600 text-11 mb-4 me-4">
                                                    <i class="ph ph-record"></i> Recording
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($row['class_name']): ?>
                                        <div class="flex-align gap-4 mb-16">
                                            <i class="ph ph-graduation-cap text-main-600"></i>
                                            <span class="text-13 text-gray-600">Class: <?php echo htmlspecialchars($row['class_name']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($row['subject_name']): ?>
                                        <div class="flex-align gap-4 mb-16">
                                            <i class="ph ph-bookmarks text-main-600"></i>
                                            <span class="text-13 text-gray-600">Subject: <?php echo htmlspecialchars($row['subject_name']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="flex-between gap-8">
                                            <?php if (!empty($row['meeting_url'])): ?>
                                            <div class="flex-grow-1 d-flex gap-8">
                                                <a href="<?php echo htmlspecialchars($row['meeting_url']); ?>" target="_blank" class="btn btn-main rounded-pill py-6 px-16 flex-grow-1">
                                                    <i class="ph ph-video-camera me-2"></i>Join Meeting
                                                </a>
                                                <button onclick="copyMeetingLink('<?php echo htmlspecialchars($row['meeting_url'], ENT_QUOTES); ?>', this)" 
                                                        class="btn btn-outline-main rounded-pill py-6 px-16" 
                                                        title="Copy meeting link"
                                                        data-bs-toggle="tooltip">
                                                    <i class="ph ph-copy"></i>
                                                </button>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-13 text-gray-500 flex-grow-1">No meeting link available</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($user_role === 'super_admin'): ?>
                                            <div class="flex-align gap-4">
                                                <a href="zoom-meetings.php?action=edit&id=<?php echo $row['id']; ?>" class="w-32 h-32 flex-center bg-success-50 text-success-600 rounded-circle hover-bg-success-600 hover-text-white">
                                                    <i class="ph ph-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['meeting_title'], ENT_QUOTES); ?>')" class="w-32 h-32 flex-center bg-danger-50 text-danger-600 rounded-circle hover-bg-danger-600 hover-text-white border-0">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (!empty($row['passcode'])): ?>
                                        <div class="mt-12 pt-12 border-top border-gray-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-gray-500">Passcode: <span class="fw-medium"><?php echo htmlspecialchars($row['passcode']); ?></span></small>
                                                <button onclick="copyToClipboard('<?php echo htmlspecialchars($row['passcode'], ENT_QUOTES); ?>', 'Passcode')" 
                                                        class="btn btn-sm btn-link text-main-600 p-0" 
                                                        title="Copy passcode">
                                                    <i class="ph ph-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($row['zoom_meeting_id'])): ?>
                                        <div class="mt-8">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-gray-500">Meeting ID: <span class="fw-medium"><?php echo htmlspecialchars($row['zoom_meeting_id']); ?></span></small>
                                                <button onclick="copyToClipboard('<?php echo htmlspecialchars($row['zoom_meeting_id'], ENT_QUOTES); ?>', 'Meeting ID')" 
                                                        class="btn btn-sm btn-link text-main-600 p-0" 
                                                        title="Copy meeting ID">
                                                    <i class="ph ph-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo '<div class="col-12 text-center py-5">';
                                echo '<div class="bg-gray-50 rounded-12 p-40">';
                                echo '<i class="ph ph-video-camera text-6xl text-gray-400 mb-16 d-block"></i>';
                                echo '<h5 class="text-gray-500 mb-8">No Meetings Scheduled</h5>';
                                echo '<p class="text-gray-400">There are no Zoom meetings scheduled at the moment.</p>';
                                if ($user_role === 'super_admin') {
                                    echo '<a href="zoom-meetings.php?action=add" class="btn btn-main rounded-pill mt-16">Schedule First Meeting</a>';
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($action == 'add' || $action == 'edit'): 
                // Only super admin can access create/edit
                if ($user_role !== 'super_admin') {
                    flash_message('You do not have permission to manage meetings.', 'error');
                    redirect('zoom-meetings.php');
                }
            ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0"><?php echo $action == 'add' ? 'Schedule New Meeting' : 'Edit Meeting'; ?></h5>        
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Configure detailed meeting settings">
                            <i class="ph-fill ph-question"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $zoomMeeting->id; ?>">
                            <?php endif; ?>
                            
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs mb-24" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab">
                                        <i class="ph ph-info me-2"></i>Basic Information
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#recurring-settings" type="button" role="tab">
                                        <i class="ph ph-repeat me-2"></i>Recurring Schedule
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#security-settings" type="button" role="tab">
                                        <i class="ph ph-lock me-2"></i>Security & Access
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#participant-settings" type="button" role="tab">
                                        <i class="ph ph-users me-2"></i>Participant Controls
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#advanced-settings" type="button" role="tab">
                                        <i class="ph ph-gear me-2"></i>Advanced Settings
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Basic Information Tab -->
                                <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                    <div class="row g-20">
                                        <div class="col-12">
                                            <label for="meeting_title" class="h5 mb-8 fw-semibold font-heading">Meeting Title <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <input type="text" class="form-control py-11 placeholder-13" id="meeting_title" name="meeting_title" 
                                                   value="<?php echo isset($zoomMeeting->meeting_title) ? htmlspecialchars($zoomMeeting->meeting_title) : ''; ?>" 
                                                   placeholder="Enter meeting title" required>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="scheduled_date" class="h5 mb-8 fw-semibold font-heading">Scheduled Date & Time <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <input type="datetime-local" class="form-control py-11" id="scheduled_date" name="scheduled_date" 
                                                   value="<?php echo isset($zoomMeeting->scheduled_date) ? date('Y-m-d\TH:i', strtotime($zoomMeeting->scheduled_date)) : ''; ?>" 
                                                   required>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="duration_minutes" class="h5 mb-8 fw-semibold font-heading">Duration (Minutes)</label>
                                            <input type="number" class="form-control py-11 placeholder-13" id="duration_minutes" name="duration_minutes" 
                                                   value="<?php echo isset($zoomMeeting->duration_minutes) ? $zoomMeeting->duration_minutes : '60'; ?>" 
                                                   min="15" max="480" placeholder="60">
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="host_email" class="h5 mb-8 fw-semibold font-heading">Host Email</label>
                                            <input type="email" class="form-control py-11 placeholder-13" id="host_email" name="host_email" 
                                                   value="<?php echo isset($zoomMeeting->host_email) ? htmlspecialchars($zoomMeeting->host_email) : $current_user['email']; ?>" 
                                                   placeholder="host@example.com">
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="max_participants" class="h5 mb-8 fw-semibold font-heading">Max Participants</label>
                                            <input type="number" class="form-control py-11 placeholder-13" id="max_participants" name="max_participants" 
                                                   value="<?php echo isset($zoomMeeting->max_participants) ? $zoomMeeting->max_participants : '100'; ?>" 
                                                   min="1" max="1000" placeholder="100">
                                        </div>
                                        
                                        <!-- Association Options -->
                                        <div class="col-sm-6">
                                            <label for="class_id" class="h5 mb-8 fw-semibold font-heading">Associate with Class (Optional)</label>
                                            <select id="class_id" name="class_id" class="form-select py-9 placeholder-13 text-15">
                                                <option value="">Select a class</option>
                                                <?php
                                                require_once 'classes/ClassModel.php';
                                                $classModel = new ClassModel($db);
                                                $classes_stmt = $classModel->read([]);
                                                while ($class_row = $classes_stmt->fetch()) {
                                                    $selected = (isset($zoomMeeting->class_id) && $zoomMeeting->class_id == $class_row['id']) ? 'selected' : '';
                                                    echo "<option value='" . $class_row['id'] . "' $selected>" . 
                                                         htmlspecialchars($class_row['class_name']) . " (" . 
                                                         htmlspecialchars($class_row['class_code']) . ")</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="subject_id" class="h5 mb-8 fw-semibold font-heading">Associate with Subject (Optional)</label>
                                            <select id="subject_id" name="subject_id" class="form-select py-9 placeholder-13 text-15">
                                                <option value="">Select a subject</option>
                                                <?php
                                                require_once 'classes/Subject.php';
                                                $subjectModel = new Subject($db);
                                                $subjects_stmt = $subjectModel->read([]);
                                                while ($subject_row = $subjects_stmt->fetch()) {
                                                    $selected = (isset($zoomMeeting->subject_id) && $zoomMeeting->subject_id == $subject_row['id']) ? 'selected' : '';
                                                    echo "<option value='" . $subject_row['id'] . "' $selected>" . 
                                                         htmlspecialchars($subject_row['class_name']) . " - " .
                                                         htmlspecialchars($subject_row['subject_name']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="meeting_description" class="h5 mb-8 fw-semibold font-heading">Description</label>
                                            <textarea class="form-control py-11 placeholder-13" id="meeting_description" name="meeting_description" rows="4" placeholder="Enter meeting description"><?php echo isset($zoomMeeting->meeting_description) ? htmlspecialchars($zoomMeeting->meeting_description) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Recurring Schedule Tab -->
                                <div class="tab-pane fade" id="recurring-settings" role="tabpanel">
                                    <div class="settings-section">
                                        <h6><i class="ph ph-repeat me-2"></i>Recurring Meeting Configuration</h6>
                                        
                                        <div class="form-check mb-20">
                                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1" 
                                                   <?php echo (isset($zoomMeeting->is_recurring) && $zoomMeeting->is_recurring) ? 'checked' : ''; ?>
                                                   onchange="toggleRecurringOptions()">
                                            <label class="form-check-label" for="is_recurring">
                                                <strong>Make this a recurring meeting</strong>
                                            </label>
                                            <div class="setting-description">Generate multiple meeting instances with the same settings</div>
                                        </div>
                                        
                                        <div id="recurring-options" class="recurring-options <?php echo (isset($zoomMeeting->is_recurring) && $zoomMeeting->is_recurring) ? 'active' : ''; ?>">
                                            <div class="row g-20">
                                                <!-- Recurrence Pattern -->
                                                <div class="col-12">
                                                    <label for="recurrence_type" class="h5 mb-8 fw-semibold font-heading">Recurrence Pattern</label>
                                                    <select id="recurrence_type" name="recurrence_type" class="form-select py-9" onchange="toggleWeeklyDays()">
                                                        <option value="daily" <?php echo (isset($zoomMeeting->recurrence_type) && $zoomMeeting->recurrence_type == 'daily') ? 'selected' : ''; ?>>Daily</option>
                                                        <option value="weekly" <?php echo (isset($zoomMeeting->recurrence_type) && $zoomMeeting->recurrence_type == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                                                        <option value="monthly" <?php echo (isset($zoomMeeting->recurrence_type) && $zoomMeeting->recurrence_type == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                                                    </select>
                                                </div>
                                                
                                                <!-- Repeat Interval -->
                                                <div class="col-sm-6">
                                                    <label for="recurrence_interval" class="h5 mb-8 fw-semibold font-heading">Repeat Every</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control py-11" id="recurrence_interval" name="recurrence_interval" 
                                                               value="<?php echo isset($zoomMeeting->recurrence_interval) ? $zoomMeeting->recurrence_interval : '1'; ?>" 
                                                               min="1" max="90">
                                                        <span class="input-group-text" id="interval-label">day(s)</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Weekly Days Selection -->
                                                <div class="col-12" id="weekly-days-container" style="display: <?php echo (isset($zoomMeeting->recurrence_type) && $zoomMeeting->recurrence_type == 'weekly') ? 'block' : 'none'; ?>;">
                                                    <label class="h5 mb-8 fw-semibold font-heading">Repeat On (Days of Week)</label>
                                                    <div class="day-selector">
                                                        <?php
                                                        $days = ['Sunday' => 1, 'Monday' => 2, 'Tuesday' => 3, 'Wednesday' => 4, 'Thursday' => 5, 'Friday' => 6, 'Saturday' => 7];
                                                        $selected_days = isset($zoomMeeting->recurrence_days) ? explode(',', $zoomMeeting->recurrence_days) : [];
                                                        
                                                        foreach ($days as $day_name => $day_value) {
                                                            $checked = in_array($day_value, $selected_days) ? 'checked' : '';
                                                            $short_name = substr($day_name, 0, 3);
                                                            echo '<div>';
                                                            echo '<input type="checkbox" class="day-checkbox" id="day_' . $day_value . '" name="recurrence_days[]" value="' . $day_value . '" ' . $checked . '>';
                                                            echo '<label class="day-label" for="day_' . $day_value . '">' . $short_name . '</label>';
                                                            echo '</div>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <small class="text-muted">Select the days when the meeting should repeat</small>
                                                </div>
                                                
                                                <!-- End Options -->
                                                <div class="col-12">
                                                    <label class="h5 mb-12 fw-semibold font-heading">End Recurrence</label>
                                                    
                                                    <div class="form-check mb-12">
                                                        <input class="form-check-input" type="radio" name="recurrence_end_type" id="end_by_date" value="date" 
                                                               <?php echo (isset($zoomMeeting->recurrence_end_date) && !empty($zoomMeeting->recurrence_end_date)) ? 'checked' : ''; ?>
                                                               onchange="toggleEndOptions('date')">
                                                        <label class="form-check-label" for="end_by_date">
                                                            End by specific date
                                                        </label>
                                                    </div>
                                                    
                                                    <div id="end-date-container" class="mb-16" style="display: <?php echo (isset($zoomMeeting->recurrence_end_date) && !empty($zoomMeeting->recurrence_end_date)) ? 'block' : 'none'; ?>;">
                                                        <input type="date" class="form-control py-11" id="recurrence_end_date" name="recurrence_end_date" 
                                                               value="<?php echo isset($zoomMeeting->recurrence_end_date) ? date('Y-m-d', strtotime($zoomMeeting->recurrence_end_date)) : ''; ?>">
                                                    </div>
                                                    
                                                    <div class="form-check mb-12">
                                                        <input class="form-check-input" type="radio" name="recurrence_end_type" id="end_after_times" value="times" 
                                                               <?php echo (isset($zoomMeeting->recurrence_end_times) && !empty($zoomMeeting->recurrence_end_times)) ? 'checked' : 'checked'; ?>
                                                               onchange="toggleEndOptions('times')">
                                                        <label class="form-check-label" for="end_after_times">
                                                            End after number of occurrences
                                                        </label>
                                                    </div>
                                                    
                                                    <div id="end-times-container" style="display: <?php echo (isset($zoomMeeting->recurrence_end_times) && !empty($zoomMeeting->recurrence_end_times)) || (!isset($zoomMeeting->recurrence_end_date)) ? 'block' : 'none'; ?>;">
                                                        <div class="input-group" style="max-width: 200px;">
                                                            <input type="number" class="form-control py-11" id="recurrence_end_times" name="recurrence_end_times" 
                                                                   value="<?php echo isset($zoomMeeting->recurrence_end_times) ? $zoomMeeting->recurrence_end_times : '10'; ?>" 
                                                                   min="1" max="365">
                                                            <span class="input-group-text">occurrences</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Recurring Meeting Info -->
                                                <div class="col-12">
                                                    <div class="alert alert-info mb-0">
                                                        <i class="ph ph-info me-2"></i>
                                                        <strong>Note:</strong> Zoom will create a single recurring meeting with multiple instances. All instances share the same meeting ID and settings.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Security & Access Tab -->
                                <div class="tab-pane fade" id="security-settings" role="tabpanel">
                                    <div class="settings-section">
                                        <h6><i class="ph ph-shield-check me-2"></i>Security Options</h6>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="waiting_room" name="waiting_room" value="1" 
                                                   <?php echo (isset($zoomMeeting->waiting_room) && $zoomMeeting->waiting_room) ? 'checked' : 'checked'; ?>>
                                            <label class="form-check-label" for="waiting_room">
                                                Enable Waiting Room <span class="badge badge-setting bg-primary-100 text-primary-600">Recommended</span>
                                            </label>
                                            <div class="setting-description">Participants will wait for host approval before joining the meeting</div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="join_before_host" name="join_before_host" value="1" 
                                                   <?php echo (isset($zoomMeeting->join_before_host) && $zoomMeeting->join_before_host) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="join_before_host">
                                                Allow Participants to Join Before Host
                                            </label>
                                            <div class="setting-description">Participants can enter the meeting before the host arrives</div>
                                        </div>
                                        
                                        <div class="row g-20 mt-3">
                                            <div class="col-sm-6">
                                                <label for="passcode" class="h5 mb-8 fw-semibold font-heading">Meeting Passcode</label>
                                                <input type="text" class="form-control py-11 placeholder-13" id="passcode" name="passcode" 
                                                       value="<?php echo isset($zoomMeeting->passcode) ? htmlspecialchars($zoomMeeting->passcode) : ''; ?>" 
                                                       placeholder="Enter passcode (optional)">
                                                <small class="text-muted">Leave empty to auto-generate a secure passcode</small>
                                            </div>
                                            
                                            <div class="col-sm-6">
                                                <label for="approval_type" class="h5 mb-8 fw-semibold font-heading">Registration Requirement</label>
                                                <select id="approval_type" name="approval_type" class="form-select py-9">
                                                    <option value="2" <?php echo (isset($zoomMeeting->approval_type) && $zoomMeeting->approval_type == 2) ? 'selected' : 'selected'; ?>>No Registration Required</option>
                                                    <option value="0" <?php echo (isset($zoomMeeting->approval_type) && $zoomMeeting->approval_type == 0) ? 'selected' : ''; ?>>Automatic Approval</option>
                                                    <option value="1" <?php echo (isset($zoomMeeting->approval_type) && $zoomMeeting->approval_type == 1) ? 'selected' : ''; ?>>Manual Approval</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Participant Controls Tab -->
                                <div class="tab-pane fade" id="participant-settings" role="tabpanel">
                                    <div class="settings-section">
                                        <h6><i class="ph ph-user-circle me-2"></i>Audio & Video Settings</h6>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="host_video" name="host_video" value="1" 
                                                   <?php echo (isset($zoomMeeting->host_video) && $zoomMeeting->host_video) ? 'checked' : 'checked'; ?>>
                                            <label class="form-check-label" for="host_video">
                                                Host Video On by Default
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="participant_video" name="participant_video" value="1" 
                                                   <?php echo (isset($zoomMeeting->participant_video) && $zoomMeeting->participant_video) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="participant_video">
                                                Participant Video On by Default
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="mute_upon_entry" name="mute_upon_entry" value="1" 
                                                   <?php echo (isset($zoomMeeting->mute_upon_entry) && $zoomMeeting->mute_upon_entry) ? 'checked' : 'checked'; ?>>
                                            <label class="form-check-label" for="mute_upon_entry">
                                                Mute Participants Upon Entry <span class="badge badge-setting bg-success-100 text-success-600">Recommended</span>
                                            </label>
                                            <div class="setting-description">Participants join with their microphone muted</div>
                                        </div>
                                        
                                        <div class="row g-20 mt-3">
                                            <div class="col-12">
                                                <label for="audio_type" class="h5 mb-8 fw-semibold font-heading">Audio Options</label>
                                                <select id="audio_type" name="audio_type" class="form-select py-9">
                                                    <option value="both" <?php echo (!isset($zoomMeeting->audio_type) || $zoomMeeting->audio_type == 'both') ? 'selected' : ''; ?>>Computer Audio & Telephone</option>
                                                    <option value="voip" <?php echo (isset($zoomMeeting->audio_type) && $zoomMeeting->audio_type == 'voip') ? 'selected' : ''; ?>>Computer Audio Only</option>
                                                    <option value="telephony" <?php echo (isset($zoomMeeting->audio_type) && $zoomMeeting->audio_type == 'telephony') ? 'selected' : ''; ?>>Telephone Only</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="settings-section">
                                        <h6><i class="ph ph-chats me-2"></i>Interaction Controls</h6>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_chat" name="enable_chat" value="1" 
                                                   <?php echo (!isset($zoomMeeting->enable_chat) || $zoomMeeting->enable_chat) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_chat">
                                                Enable Meeting Chat
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_private_chat" name="enable_private_chat" value="1" 
                                                   <?php echo (!isset($zoomMeeting->enable_private_chat) || $zoomMeeting->enable_private_chat) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_private_chat">
                                                Allow Private Chat Between Participants
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_raise_hand" name="enable_raise_hand" value="1" 
                                                   <?php echo (!isset($zoomMeeting->enable_raise_hand) || $zoomMeeting->enable_raise_hand) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_raise_hand">
                                                Enable Raise Hand Feature
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_reactions" name="enable_reactions" value="1" 
                                                   <?php echo (!isset($zoomMeeting->enable_reactions) || $zoomMeeting->enable_reactions) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_reactions">
                                                Enable Meeting Reactions (👍, 👏, ❤️, etc.)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="settings-section">
                                        <h6><i class="ph ph-presentation me-2"></i>Sharing & Collaboration</h6>
                                        
                                        <div class="row g-20">
                                            <div class="col-12">
                                                <label for="screen_sharing" class="h5 mb-8 fw-semibold font-heading">Who Can Share Screen?</label>
                                                <select id="screen_sharing" name="screen_sharing" class="form-select py-9">
                                                    <option value="all" <?php echo (!isset($zoomMeeting->screen_sharing) || $zoomMeeting->screen_sharing == 'all') ? 'selected' : ''; ?>>All Participants</option>
                                                    <option value="host" <?php echo (isset($zoomMeeting->screen_sharing) && $zoomMeeting->screen_sharing == 'host') ? 'selected' : ''; ?>>Host Only</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" id="enable_breakout_rooms" name="enable_breakout_rooms" value="1" 
                                                   <?php echo (isset($zoomMeeting->enable_breakout_rooms) && $zoomMeeting->enable_breakout_rooms) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_breakout_rooms">
                                                Enable Breakout Rooms
                                            </label>
                                            <div class="setting-description">Allow host to split participants into separate sessions</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Advanced Settings Tab -->
                                <div class="tab-pane fade" id="advanced-settings" role="tabpanel">
                                    <div class="settings-section">
                                        <h6><i class="ph ph-record me-2"></i>Recording Options</h6>
                                        
                                        <div class="row g-20">
                                            <div class="col-12">
                                                <label for="auto_recording" class="h5 mb-8 fw-semibold font-heading">Automatic Recording</label>
                                                <select id="auto_recording" name="auto_recording" class="form-select py-9">
                                                    <option value="none" <?php echo (!isset($zoomMeeting->auto_recording) || $zoomMeeting->auto_recording == 'none') ? 'selected' : ''; ?>>No Recording</option>
                                                    <option value="local" <?php echo (isset($zoomMeeting->auto_recording) && $zoomMeeting->auto_recording == 'local') ? 'selected' : ''; ?>>Local Recording (to computer)</option>
                                                    <option value="cloud" <?php echo (isset($zoomMeeting->auto_recording) && $zoomMeeting->auto_recording == 'cloud') ? 'selected' : ''; ?>>Cloud Recording</option>
                                                </select>
                                                <small class="text-muted">Cloud recording requires a paid Zoom account</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="settings-section">
                                        <h6><i class="ph ph-devices me-2"></i>Device & Connection</h6>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="allow_multiple_devices" name="allow_multiple_devices" value="1" 
                                                   <?php echo (isset($zoomMeeting->allow_multiple_devices) && $zoomMeeting->allow_multiple_devices) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="allow_multiple_devices">
                                                Allow Participants to Join from Multiple Devices
                                            </label>
                                            <div class="setting-description">Users can join from both computer and mobile simultaneously</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row mt-24">
                                <div class="col-12">
                                    <div class="flex-align justify-content-end gap-8">
                                        <a href="zoom-meetings.php" class="btn btn-outline-main rounded-pill py-9">Cancel</a>
                                        <button type="submit" class="btn btn-main rounded-pill py-9">
                                            <i class="ph ph-floppy-disk me-2"></i>
                                            <?php echo $action == 'add' ? 'Schedule Meeting' : 'Update Meeting'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        
        <!-- Footer Start -->
        <div class="dashboard-footer">
            <div class="flex-between flex-wrap gap-16">
                <p class="text-gray-300 text-13 fw-normal"> &copy; Copyright <?php echo APP_NAME; ?> <?php echo date('Y'); ?>, All Right Reserved</p>
                <div class="flex-align flex-wrap gap-16">
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">License</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Support</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Documentation</a>
                </div>
            </div>
        </div>
        <!-- Footer End -->
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel the meeting "<span id="meetingTitle"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Cancel Meeting</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery js -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="assets/js/boostrap.bundle.min.js"></script>
    <!-- Phosphor Js -->
    <script src="assets/js/phosphor-icon.js"></script>
    <!-- file upload -->
    <script src="assets/js/file-upload.js"></script>
    <!-- file upload -->
    <script src="assets/js/plyr.js"></script>
    <!-- dataTables -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <!-- full calendar -->
    <script src="assets/js/full-calendar.js"></script>
    <!-- jQuery UI -->
    <script src="assets/js/jquery-ui.js"></script>
    <!-- jQuery UI -->
    <script src="assets/js/editor-quill.js"></script>
    <!-- apex charts -->
    <script src="assets/js/apexcharts.min.js"></script>
    <!-- Calendar Js -->
    <script src="assets/js/calendar.js"></script>
    <!-- jvectormap Js -->
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <!-- jvectormap world Js -->
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    
    <!-- main js -->
    <script src="assets/js/main.js"></script>

    <script>
        function confirmDelete(id, title) {
            document.getElementById('deleteId').value = id;
            document.getElementById('meetingTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Copy meeting link to clipboard with visual feedback
        function copyMeetingLink(url, button) {
            copyToClipboard(url, 'Meeting link', button);
        }

        // Generic copy to clipboard function
        function copyToClipboard(text, label = 'Text', button = null) {
            // Use modern Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopySuccess(button, label);
                }).catch(function(err) {
                    // Fallback for older browsers
                    fallbackCopyToClipboard(text, button, label);
                });
            } else {
                // Fallback for older browsers or non-HTTPS
                fallbackCopyToClipboard(text, button, label);
            }
        }

        // Fallback copy method for older browsers
        function fallbackCopyToClipboard(text, button, label) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccess(button, label);
                } else {
                    showCopyError(button);
                }
            } catch (err) {
                showCopyError(button);
            }
            
            document.body.removeChild(textArea);
        }

        // Show success feedback
        function showCopySuccess(button, label) {
            if (button) {
                const originalHTML = button.innerHTML;
                const originalClass = button.className;
                
                // Change button appearance
                button.innerHTML = '<i class="ph ph-check"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-main', 'btn-link');
                
                // Show toast notification
                showToast(label + ' copied to clipboard!', 'success');
                
                // Reset button after 2 seconds
                setTimeout(function() {
                    button.innerHTML = originalHTML;
                    button.className = originalClass;
                }, 2000);
            } else {
                showToast(label + ' copied to clipboard!', 'success');
            }
        }

        // Show error feedback
        function showCopyError(button) {
            showToast('Failed to copy. Please try again.', 'error');
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            // Remove any existing toast
            const existingToast = document.getElementById('copyToast');
            if (existingToast) {
                existingToast.remove();
            }
            
            // Create toast element
            const toast = document.createElement('div');
            toast.id = 'copyToast';
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            const icon = type === 'success' ? 'ph-check-circle' : 'ph-x-circle';
            
            toast.innerHTML = `
                <div class="toast show align-items-center text-white ${bgClass} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="ph ${icon} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 3 seconds
            setTimeout(function() {
                if (toast.parentNode) {
                    toast.style.transition = 'opacity 0.3s';
                    toast.style.opacity = '0';
                    setTimeout(function() {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 3000);
        }
        
        // Toggle recurring options visibility
        function toggleRecurringOptions() {
            const checkbox = document.getElementById('is_recurring');
            const options = document.getElementById('recurring-options');
            
            if (checkbox.checked) {
                options.classList.add('active');
            } else {
                options.classList.remove('active');
            }
        }
        
        // Toggle weekly days selector
        function toggleWeeklyDays() {
            const recurrenceType = document.getElementById('recurrence_type').value;
            const weeklyDaysContainer = document.getElementById('weekly-days-container');
            const intervalLabel = document.getElementById('interval-label');
            
            if (recurrenceType === 'weekly') {
                weeklyDaysContainer.style.display = 'block';
                intervalLabel.textContent = 'week(s)';
            } else {
                weeklyDaysContainer.style.display = 'none';
                if (recurrenceType === 'daily') {
                    intervalLabel.textContent = 'day(s)';
                } else if (recurrenceType === 'monthly') {
                    intervalLabel.textContent = 'month(s)';
                }
            }
        }
        
        // Toggle end options visibility
        function toggleEndOptions(type) {
            const dateContainer = document.getElementById('end-date-container');
            const timesContainer = document.getElementById('end-times-container');
            
            if (type === 'date') {
                dateContainer.style.display = 'block';
                timesContainer.style.display = 'none';
            } else {
                dateContainer.style.display = 'none';
                timesContainer.style.display = 'block';
            }
        }

        // Initialize tooltips on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips if available
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            // Update interval label on page load
            toggleWeeklyDays();
        });
    </script>

</body>
</html>