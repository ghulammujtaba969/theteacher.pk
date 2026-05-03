<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Lecture.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Syllabus.php';
require_once 'classes/BatchEnrollment.php';
require_once 'classes/ClassModel.php';

require_permission('lectures.view', 'dashboard.php');

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$lecture = new Lecture($db);
$user = new User($db);
$syllabusModel = new Syllabus($db);
$enrollmentModel = new BatchEnrollment($db);
$classModelForRedirect = new ClassModel($db);

$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;
$is_student_role = in_array($user_role, ['solo_student','student'], true);

// Build enrolled class IDs for the current user
$userEnrollmentsForFilter = $enrollmentModel->getEnrollmentsByUser($current_user['id']);
$enrolled_class_ids_only = [];
foreach ($userEnrollmentsForFilter as $en) {
    $status = $en['enrollment_status'] ?? '';
    if (in_array($status, ['pending','active','completed'], true)) {
        $cid = (int)($en['class_id'] ?? 0);
        if ($cid > 0) { $enrolled_class_ids_only[$cid] = $cid; }
    }
}
$enrolled_class_ids_only = array_values($enrolled_class_ids_only);

// Determine which classes to show in listings
if ($can_access_all_classes_flag) {
    $allowed_class_ids_for_listing = [];
} elseif ($is_student_role) {
    // For students, only show lectures for classes they are registered in
    $allowed_class_ids_for_listing = $enrolled_class_ids_only;
} else {
    // For admins/teachers, keep accessible classes behavior
    $allowed_class_ids_for_listing = $accessible_class_ids;
}

error_log("LECTURES_DEBUG: Current User Role: " . $user_role);
error_log("LECTURES_DEBUG: Can Access All Classes Flag: " . ($can_access_all_classes_flag ? 'true' : 'false'));
error_log("LECTURES_DEBUG: Accessible Class IDs: " . implode(',', $accessible_class_ids));

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$lecture_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$syllabus_filter = isset($_GET['syllabus']) ? (int)$_GET['syllabus'] : 0;
$class_filter = isset($_GET['class']) ? (int)$_GET['class'] : 0;

if ($class_filter > 0) {
    $allowed_ids = $can_access_all_classes_flag ? [] : array_map('intval', $allowed_class_ids_for_listing);
    if (!$can_access_all_classes_flag && !in_array($class_filter, $allowed_ids, true)) {
        flash_message('You do not have access to that class.', 'error');
        redirect('lectures.php');
    }
}

function canAccessSyllabusForLectureForm($lecture, $syllabus_id, $can_access_all_classes_flag, $accessible_class_ids)
{
    if ($syllabus_id <= 0) {
        return false;
    }

    $syllabi_stmt = $lecture->getActiveSyllabi($can_access_all_classes_flag ? [] : $accessible_class_ids);
    while ($syllabus_row = $syllabi_stmt->fetch(PDO::FETCH_ASSOC)) {
        if ((int)$syllabus_row['id'] === (int)$syllabus_id) {
            return true;
        }
    }

    return false;
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_next_order') {
    header('Content-Type: application/json');

    if (!can('lectures.create') && !can('lectures.edit')) {
        http_response_code(403);
        echo json_encode(['error' => 'Permission denied']);
        exit;
    }

    $ajax_syllabus_id = isset($_GET['syllabus_id']) ? (int)$_GET['syllabus_id'] : 0;
    if (!canAccessSyllabusForLectureForm($lecture, $ajax_syllabus_id, $can_access_all_classes_flag, $accessible_class_ids)) {
        http_response_code(403);
        echo json_encode(['error' => 'Syllabus not available']);
        exit;
    }

    echo json_encode(['next_order' => $lecture->getNextOrder($ajax_syllabus_id)]);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];

    if ($action == 'create' || $action == 'update') {
        if ($action == 'create' && !can('lectures.create')) permission_denied('lectures.php');
        if ($action == 'update' && !can('lectures.edit')) permission_denied('lectures.php');
        try {
            // Validate required fields
            if (empty($_POST['lecture_title'])) {
                $errors[] = 'Lecture title is required';
            }
            if (empty($_POST['syllabus_id'])) {
                $errors[] = 'Syllabus is required';
            }
            if (empty($_POST['lecture_type'])) {
                $errors[] = 'Lecture type is required';
            }

            // Set lecture properties
            $lecture->lecture_title = trim($_POST['lecture_title']);
            $lecture->syllabus_id = (int)$_POST['syllabus_id'];
            $lecture->lecture_type = $_POST['lecture_type'];
            $lecture->description = trim($_POST['description'] ?? '');
            $lecture->lecture_order = (int)($_POST['lecture_order'] ?? 1);
            $lecture->duration_minutes = !empty($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : null;

            // Check if this is a multiple format lecture
            $is_multiple_format = ($lecture->lecture_type === 'multiple');

            // Handle content based on type
            if ($is_multiple_format) {
                // For multiple format lectures, just set basic info
                $lecture->content_url = null;
                $lecture->text_content = null;
                $lecture->file_name = null;
                $lecture->file_size = null;
            } elseif ($lecture->lecture_type == 'text') {
                $lecture->text_content = $_POST['text_content'] ?? '';
                $lecture->content_url = null;
                $lecture->file_name = null;
                $lecture->file_size = null;
            } else {
                $lecture->text_content = null;
                
                if (!empty($_FILES['content_file']['name'])) {
                    $upload_result = $lecture->handleFileUpload($_FILES['content_file']);
                    if ($upload_result['success']) {
                        $lecture->content_url = $upload_result['path'];
                        $lecture->file_name = $upload_result['original_name'];
                        $lecture->file_size = $upload_result['size'];
                    } else {
                        $errors = array_merge($errors, $upload_result['errors']);
                    }
                } elseif (!empty($_POST['content_url'])) {
                    $lecture->content_url = trim($_POST['content_url']);
                    $lecture->file_name = null;
                    $lecture->file_size = null;
                } else {
                    if ($action == 'create') {
                        $errors[] = 'Please upload a file or provide a URL';
                    }
                }
            }

            // Handle image upload
            if ($action == 'create') {
                if (isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] === UPLOAD_ERR_OK) {
                    $lecture->image = $lecture->handleImageUpload($_FILES['lecture_image']);
                } else {
                    $lecture->image = null;
                }
            } else {
                // Update mode
                $lecture->id = (int)$_POST['id'];
                
                // Get current lecture data
                $current_lecture = new Lecture($db);
                $current_lecture->id = $lecture->id;
                $current_lecture->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids);
                
                // Handle image upload or removal
                if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                    if ($current_lecture->image && file_exists($current_lecture->image)) {
                        unlink($current_lecture->image);
                    }
                    $lecture->image = null;
                } elseif (isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] === UPLOAD_ERR_OK) {
                    $lecture->image = $lecture->handleImageUpload($_FILES['lecture_image'], $current_lecture->image);
                } else {
                    $lecture->image = null; // Don't update in database
                }
            }

            if (empty($errors)) {
                if ($action == 'create') {
                    if ($lecture->create()) {
                        // Handle multiple format file uploads
                        if ($is_multiple_format) {
                            $lecture_id = $lecture->id;
                            $display_order = 1;
                            
                            // Handle PDF upload
                            if (isset($_POST['enable_pdf']) && !empty($_FILES['pdf_file']['name'])) {
                                $upload_result = $lecture->handleFileUpload($_FILES['pdf_file']);
                                if ($upload_result['success']) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'pdf',
                                        'file_url' => $upload_result['path'],
                                        'file_name' => $upload_result['original_name'],
                                        'file_size' => $upload_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => null,
                                        'display_order' => $display_order++
                                    ]);
                                }
                            }
                            
                            // Handle PPTX upload
                            if (isset($_POST['enable_pptx']) && !empty($_POST['pptx_content'])) {
                                $pptx_content = trim($_POST['pptx_content']);
                                $lecture->createLectureFile([
                                    'lecture_id' => $lecture_id,
                                    'file_type' => 'pptx',
                                    'file_url' => $pptx_content,
                                    'file_name' => null,
                                    'file_size' => null,
                                    'text_content' => null,
                                    'duration_minutes' => null,
                                    'display_order' => $display_order++
                                ]);
                            }

                            // Handle iSpring HTML5 package upload
                            if (isset($_POST['enable_ispring']) && !empty($_FILES['ispring_file']['name'])) {
                                $ispring_result = $lecture->handleISpringUpload($_FILES['ispring_file'], $lecture_id, $lecture->syllabus_id, $lecture->lecture_order);
                                if ($ispring_result['success']) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'ispring',
                                        'file_url' => $ispring_result['index_path'],
                                        'file_name' => $ispring_result['original_name'],
                                        'file_size' => $ispring_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => null,
                                        'display_order' => $display_order++
                                    ]);
                                } else {
                                    $errors[] = 'iSpring upload failed: ' . implode(', ', $ispring_result['errors']);
                                }
                            }
                            
                            // Handle Video (URL or upload)
                            if (isset($_POST['enable_video'])) {
                                $video_duration = isset($_POST['video_duration']) ? (int)$_POST['video_duration'] : null;
                                $video_url = trim($_POST['video_url'] ?? '');
                                if (!empty($video_url)) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'video',
                                        'file_url' => $video_url,
                                        'file_name' => null,
                                        'file_size' => null,
                                        'text_content' => null,
                                        'duration_minutes' => $video_duration,
                                        'display_order' => $display_order++
                                    ]);
                                } elseif (!empty($_FILES['video_file']['name'])) {
                                    $upload_result = $lecture->handleFileUpload($_FILES['video_file']);
                                    if ($upload_result['success']) {
                                        $lecture->createLectureFile([
                                            'lecture_id' => $lecture_id,
                                            'file_type' => 'video',
                                            'file_url' => $upload_result['path'],
                                            'file_name' => $upload_result['original_name'],
                                            'file_size' => $upload_result['size'],
                                            'text_content' => null,
                                            'duration_minutes' => $video_duration,
                                            'display_order' => $display_order++
                                        ]);
                                    }
                                }
                            }
                            
                            // Handle Audio upload
                            if (isset($_POST['enable_audio']) && !empty($_FILES['audio_file']['name'])) {
                                $upload_result = $lecture->handleFileUpload($_FILES['audio_file']);
                                if ($upload_result['success']) {
                                    $audio_duration = isset($_POST['audio_duration']) ? (int)$_POST['audio_duration'] : null;
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'audio',
                                        'file_url' => $upload_result['path'],
                                        'file_name' => $upload_result['original_name'],
                                        'file_size' => $upload_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => $audio_duration,
                                        'display_order' => $display_order++
                                    ]);
                                }
                            }
                            
                            // Handle Text content
                            if (isset($_POST['enable_text']) && !empty($_POST['text_content_multiple'])) {
                                $lecture->createLectureFile([
                                    'lecture_id' => $lecture_id,
                                    'file_type' => 'text',
                                    'file_url' => null,
                                    'file_name' => null,
                                    'file_size' => null,
                                    'text_content' => $_POST['text_content_multiple'],
                                    'duration_minutes' => null,
                                    'display_order' => $display_order++
                                ]);
                            }
                        }
                        
                        if (empty($errors)) {
                            flash_message('Lecture created successfully', 'success');
                            redirect('lectures.php');
                        }
                    } else {
                        $errors[] = 'Failed to create lecture';
                    }
                } else {
                    if ($lecture->update()) {
                        // Handle multiple format file uploads for UPDATE
                        if ($is_multiple_format) {
                            $lecture_id = $lecture->id;
                            $display_order = 1;
                            
                            // Handle PDF upload
                            if (isset($_POST['enable_pdf']) && !empty($_FILES['pdf_file']['name'])) {
                                $upload_result = $lecture->handleFileUpload($_FILES['pdf_file']);
                                if ($upload_result['success']) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'pdf',
                                        'file_url' => $upload_result['path'],
                                        'file_name' => $upload_result['original_name'],
                                        'file_size' => $upload_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => null,
                                        'display_order' => $display_order++
                                    ]);
                                }
                            }
                            
                            // Handle PPTX upload
                            if (isset($_POST['enable_pptx']) && !empty($_POST['pptx_content'])) {
                                $pptx_content = trim($_POST['pptx_content']);
                                $lecture->createLectureFile([
                                    'lecture_id' => $lecture_id,
                                    'file_type' => 'pptx',
                                    'file_url' => $pptx_content,
                                    'file_name' => null,
                                    'file_size' => null,
                                    'text_content' => null,
                                    'duration_minutes' => null,
                                    'display_order' => $display_order++
                                ]);
                            }

                            // Handle iSpring HTML5 package upload
                            if (isset($_POST['enable_ispring']) && !empty($_FILES['ispring_file']['name'])) {
                                $ispring_result = $lecture->handleISpringUpload($_FILES['ispring_file'], $lecture_id, $lecture->syllabus_id, $lecture->lecture_order);
                                if ($ispring_result['success']) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'ispring',
                                        'file_url' => $ispring_result['index_path'],
                                        'file_name' => $ispring_result['original_name'],
                                        'file_size' => $ispring_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => null,
                                        'display_order' => $display_order++
                                    ]);
                                } else {
                                    $errors[] = 'iSpring upload failed: ' . implode(', ', $ispring_result['errors']);
                                }
                            }
                            
                            // Handle Video (URL or upload)
                            if (isset($_POST['enable_video'])) {
                                $video_duration = isset($_POST['video_duration']) ? (int)$_POST['video_duration'] : null;
                                $video_url = trim($_POST['video_url'] ?? '');
                                if (!empty($video_url)) {
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'video',
                                        'file_url' => $video_url,
                                        'file_name' => null,
                                        'file_size' => null,
                                        'text_content' => null,
                                        'duration_minutes' => $video_duration,
                                        'display_order' => $display_order++
                                    ]);
                                } elseif (!empty($_FILES['video_file']['name'])) {
                                    $upload_result = $lecture->handleFileUpload($_FILES['video_file']);
                                    if ($upload_result['success']) {
                                        $lecture->createLectureFile([
                                            'lecture_id' => $lecture_id,
                                            'file_type' => 'video',
                                            'file_url' => $upload_result['path'],
                                            'file_name' => $upload_result['original_name'],
                                            'file_size' => $upload_result['size'],
                                            'text_content' => null,
                                            'duration_minutes' => $video_duration,
                                            'display_order' => $display_order++
                                        ]);
                                    }
                                }
                            }
                            
                            // Handle Audio upload
                            if (isset($_POST['enable_audio']) && !empty($_FILES['audio_file']['name'])) {
                                $upload_result = $lecture->handleFileUpload($_FILES['audio_file']);
                                if ($upload_result['success']) {
                                    $audio_duration = isset($_POST['audio_duration']) ? (int)$_POST['audio_duration'] : null;
                                    $lecture->createLectureFile([
                                        'lecture_id' => $lecture_id,
                                        'file_type' => 'audio',
                                        'file_url' => $upload_result['path'],
                                        'file_name' => $upload_result['original_name'],
                                        'file_size' => $upload_result['size'],
                                        'text_content' => null,
                                        'duration_minutes' => $audio_duration,
                                        'display_order' => $display_order++
                                    ]);
                                }
                            }
                            
                            // Handle Text content
                            if (isset($_POST['enable_text']) && !empty($_POST['text_content_multiple'])) {
                                $lecture->createLectureFile([
                                    'lecture_id' => $lecture_id,
                                    'file_type' => 'text',
                                    'file_url' => null,
                                    'file_name' => null,
                                    'file_size' => null,
                                    'text_content' => $_POST['text_content_multiple'],
                                    'duration_minutes' => null,
                                    'display_order' => $display_order++
                                ]);
                            }
                        }
                        
                        if (empty($errors)) {
                            flash_message('Lecture updated successfully', 'success');
                            redirect('lectures.php?action=view&id=' . $lecture->id);
                        }
                    } else {
                        $errors[] = 'Failed to update lecture';
                    }
                }
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    } elseif ($action == 'delete') {
        if (!can('lectures.delete')) permission_denied('lectures.php');
        $lecture->id = (int)$_POST['id'];
        if ($lecture->delete()) {
            flash_message('Lecture deleted successfully', 'success');
        } else {
            flash_message('Failed to delete lecture', 'error');
        }
        redirect('lectures.php');
    }
}

// Get lecture data for edit
if ($action == 'edit' && $lecture_id > 0) {
    if (!can('lectures.edit')) permission_denied('lectures.php');
    $lecture->id = $lecture_id;
    if (!$lecture->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids)) {
        flash_message('Lecture not found or you do not have permission to view it.', 'error');
        redirect('lectures.php');
    }
}

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectures - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/file-upload.css">
    <link rel="stylesheet" href="assets/css/plyr.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/full-calendar.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/editor-quill.css">
    <link rel="stylesheet" href="assets/css/apexcharts.css">
    <link rel="stylesheet" href="assets/css/calendar.css">
    <link rel="stylesheet" href="assets/css/jquery-jvectormap-2.0.5.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .remove-image-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        .lecture-card-image {
            width: 100%;
            height: 164px;
            object-fit: cover;
            border-radius: 8px;
        }
        .content-type-fields {
            display: none;
        }
        .content-type-fields.active {
            display: block;
        }
        .format-content {
            display: none;
        }
        .format-content.active {
            display: block;
        }
    </style>
</head> 
<body>
    
<div class="preloader">
    <div class="loader"></div>
</div>

<div class="side-overlay"></div>

    <?php include 'includes/sidebar_new.php'; ?>

    <div class="dashboard-main-wrapper">
        <?php include 'includes/navbar_new.php'; ?>
        
        <div class="dashboard-body">
           
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">Lectures</span></li>
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
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link <?php echo !$syllabus_filter ? 'active' : ''; ?>" id="pills-all-tab" onclick="window.location.href='<?php echo $class_filter > 0 ? 'lectures.php?class=' . $class_filter : 'lectures.php'; ?>'">All Lectures</button>
                                </li>
                                <?php
                                $syllabi_stmt = $lecture->getActiveSyllabi($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                while ($syllabus_row = $syllabi_stmt->fetch()) {
                                    if ($class_filter > 0 && (int)$syllabus_row['class_id'] !== $class_filter) {
                                        continue;
                                    }
                                    $active_class = ($syllabus_filter == $syllabus_row['id']) ? 'active' : '';
                                    $syllabus_url = 'lectures.php?syllabus=' . $syllabus_row['id'];
                                    if ($class_filter > 0) {
                                        $syllabus_url .= '&class=' . $class_filter;
                                    }
                                    echo '<li class="nav-item" role="presentation">';
                                    echo '<button class="nav-link ' . $active_class . '" onclick="window.location.href=\'' . $syllabus_url . '\'">';
                                    echo htmlspecialchars($syllabus_row['syllabus_title']);
                                    echo '</button>';
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                            <?php if (can('lectures.create')): ?>
                            <?php
                                $add_lecture_url = 'lectures.php?action=add';
                                if ($syllabus_filter > 0) {
                                    $add_lecture_url .= '&syllabus=' . $syllabus_filter;
                                } elseif ($class_filter > 0) {
                                    $add_lecture_url .= '&class=' . $class_filter;
                                }
                            ?>
                            <a href="<?php echo $add_lecture_url; ?>" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                <span class="d-flex text-md"><i class="ph ph-plus"></i></span> 
                                Add New Lecture
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-lectures" role="tabpanel">
                                <div class="row g-20">
                                    <?php
                                    if ($syllabus_filter) {
                                        $stmt = $lecture->readBySyllabus($syllabus_filter, $allowed_class_ids_for_listing);
                                    } else {
                                        $stmt = $lecture->read($allowed_class_ids_for_listing);
                                    }

                                    $lecture_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if ($class_filter > 0) {
                                        $lecture_rows = array_values(array_filter($lecture_rows, function ($row) use ($class_filter) {
                                            return (int)$row['class_id'] === $class_filter;
                                        }));
                                    }

                                    $per_page = isset($_GET['pp']) ? max(6, min(60, (int)$_GET['pp'])) : 12;
                                    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                    $total = count($lecture_rows);
                                    $pages = max(1, (int)ceil($total / $per_page));
                                    if ($page > $pages) { $page = $pages; }
                                    $offset = ($page - 1) * $per_page;
                                    $paged_lectures = array_slice($lecture_rows, $offset, $per_page);
                                    
                                    if ($total > 0) {
                                        
                                        foreach ($paged_lectures as $row) {
                                            $type_class = 'type-' . $row['lecture_type'];
                                            $type_color = '';
                                            $type_icon = '';
                                            switch ($row['lecture_type']) {
                                                case 'video': 
                                                    $type_color = 'success'; 
                                                    $type_icon = 'ph-video-camera'; 
                                                    break;
                                                case 'audio': 
                                                    $type_color = 'warning'; 
                                                    $type_icon = 'ph-speaker-high'; 
                                                    break;
                                                case 'file': 
                                                    $type_color = 'info'; 
                                                    $type_icon = 'ph-file'; 
                                                    break;
                                                case 'text': 
                                                    $type_color = 'danger'; 
                                                    $type_icon = 'ph-article'; 
                                                    break;
                                                case 'pptx_embed': 
                                                    $type_color = 'purple'; 
                                                    $type_icon = 'ph-presentation'; 
                                                    break;
                                                case 'multiple': 
                                                    $type_color = 'primary'; 
                                                    $type_icon = 'ph-stack'; 
                                                    break;
                                            }
                                    ?>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="card border border-gray-100">
                                            <div class="card-body p-8">
                                                <a href="lectures.php?action=view&id=<?php echo $row['id']; ?>" class="bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8">
                                                    <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['lecture_title']); ?>" class="lecture-card-image">
                                                    <?php else: ?>
                                                        <span class="text-6xl text-main-600"><i class="<?php echo $type_icon; ?>"></i></span>
                                                    <?php endif; ?>
                                                </a>
                                                <div class="p-8">
                                                    <span class="text-13 py-2 px-10 rounded-pill bg-<?php echo $type_color; ?>-50 text-<?php echo $type_color; ?>-600 mb-16">
                                                        <?php echo $row['lecture_type'] == 'multiple' ? 'Multiple Formats' : ucfirst($row['lecture_type']); ?>
                                                    </span>
                                                    <h5 class="mb-0"><a href="lectures.php?action=view&id=<?php echo $row['id']; ?>" class="hover-text-main-600"><?php echo htmlspecialchars($row['lecture_title']); ?></a></h5>

                                                    <?php if (!empty($row['description'])): ?>
                                                    <p class="text-gray-300 text-13 mt-8 text-line-2"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?><?php echo strlen($row['description']) > 100 ? '...' : ''; ?></p>
                                                    <?php endif; ?>

                                                    <div class="flex-align gap-8 flex-wrap mt-16">
                                                        <div class="flex-align gap-4">
                                                            <span class="text-13 text-gray-600"><strong>Class:</strong> <?php echo htmlspecialchars($row['class_name']); ?></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex-align gap-8 flex-wrap mt-8">
                                                        <div class="flex-align gap-4">
                                                            <span class="text-13 text-gray-600"><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject_name']); ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="flex-between gap-8 mt-12 pt-12 border-top border-gray-100">
                                                        <div class="flex-align gap-4">
                                                            <span class="text-sm text-main-600 d-flex"><i class="ph ph-list-numbers"></i></span>
                                                            <span class="text-13 text-gray-600">Order: <?php echo $row['lecture_order']; ?></span>
                                                        </div>
                                                        <?php if ($row['duration_minutes']): ?>
                                                        <div class="flex-align gap-4">
                                                            <span class="text-sm text-main-600 d-flex"><i class="ph ph-clock"></i></span>
                                                            <span class="text-13 text-gray-600"><?php echo $row['duration_minutes']; ?> min</span>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="flex-between gap-8 mt-16">
                                                        <a href="lectures.php?action=view&id=<?php echo $row['id']; ?>" class="btn btn-outline-main rounded-pill py-9 flex-grow-1">View Lecture</a>
                                                        <?php if (can('lectures.edit') || can('lectures.delete')): ?>
                                                        <div class="flex-align gap-4">
                                                            <?php if (can('lectures.edit')): ?>
                                                            <a href="lectures.php?action=edit&id=<?php echo $row['id']; ?>" class="w-32 h-32 flex-center bg-success-50 text-success-600 rounded-circle hover-bg-success-600 hover-text-white">
                                                                <i class="ph ph-pencil"></i>
                                                            </a>
                                                            <?php endif; ?>
                                                            <?php if (can('lectures.delete')): ?>
                                                            <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['lecture_title']); ?>')" class="w-32 h-32 flex-center bg-danger-50 text-danger-600 rounded-circle hover-bg-danger-600 hover-text-white border-0">
                                                                <i class="ph ph-trash"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                    } else {
                                        echo '<div class="col-12 text-center py-5">';
                                        echo '<div class="bg-gray-50 rounded-12 p-40">';
                                        echo '<i class="ph ph-books text-6xl text-gray-400 mb-16 d-block"></i>';
                                        echo '<h5 class="text-gray-500 mb-8">No Lectures Found</h5>';
                                        echo '<p class="text-gray-400">There are no lectures available at the moment.</p>';
                                        if (can('lectures.create')) {
                                            echo '<a href="lectures.php?action=add" class="btn btn-main rounded-pill mt-16">Add First Lecture</a>';
                                        }
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                                <?php if ($total > 0): ?>
                                <div class="card-footer flex-between flex-wrap mt-24 px-0 pb-0">
                                    <?php
                                        $start_i = $offset + 1;
                                        $end_i = min($offset + $per_page, $total);
                                        $qs = $_GET;
                                        unset($qs['page'], $qs['pp']);
                                        $base = basename($_SERVER['PHP_SELF']) . (empty($qs) ? '' : ('?' . http_build_query($qs)));
                                        $build_page_link = function ($p, $pp) use ($base) {
                                            return $base . (strpos($base, '?') !== false ? '&' : '?') . 'page=' . $p . '&pp=' . $pp;
                                        };
                                    ?>
                                    <span class="text-gray-900">Showing <?php echo $start_i; ?> to <?php echo $end_i; ?> of <?php echo $total; ?> lectures</span>
                                    <ul class="pagination flex-align flex-wrap">
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page > 1 ? $build_page_link($page - 1, $per_page) : '#'; ?>">Prev</a>
                                        </li>
                                        <?php for ($p = 1; $p <= $pages; $p++): ?>
                                            <li class="page-item <?php echo $p == $page ? 'active' : ''; ?>">
                                                <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $build_page_link($p, $per_page); ?>"><?php echo $p; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php echo $page >= $pages ? 'disabled' : ''; ?>">
                                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page < $pages ? $build_page_link($page + 1, $per_page) : '#'; ?>">Next</a>
                                        </li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($action == 'add' || $action == 'edit'): 
                if (!can($action == 'add' ? 'lectures.create' : 'lectures.edit')) {
                    flash_message('You do not have permission to manage lectures.', 'error');
                    redirect('lectures.php');
                }

                $selected_syllabus_id_for_form = isset($lecture->syllabus_id) ? (int)$lecture->syllabus_id : 0;
                if ($action == 'add' && $syllabus_filter > 0 && canAccessSyllabusForLectureForm($lecture, $syllabus_filter, $can_access_all_classes_flag, $accessible_class_ids)) {
                    $selected_syllabus_id_for_form = $syllabus_filter;
                }

                $lecture_order_value = isset($lecture->lecture_order) ? (int)$lecture->lecture_order : 1;
                if ($action == 'add' && $selected_syllabus_id_for_form > 0) {
                    $lecture_order_value = $lecture->getNextOrder($selected_syllabus_id_for_form);
                }
            ?>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-20 flex-between flex-wrap gap-8">
                            <h4 class="mb-0"><?php echo $action == 'add' ? 'Add New Lecture' : 'Edit Lecture'; ?></h4>
                            <a href="lectures.php" class="btn btn-outline-gray rounded-pill py-7 flex-align gap-4">
                                <i class="ph ph-arrow-left"></i> Back to Lectures
                            </a>
                        </div>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $lecture->id; ?>">
                            <?php endif; ?>
                            
                            <div class="row g-20">
                                <div class="col-md-8">
                                    <label for="lecture_title" class="form-label">Lecture Title *</label>
                                    <input type="text" class="form-control" id="lecture_title" name="lecture_title" 
                                           value="<?php echo isset($lecture->lecture_title) ? htmlspecialchars($lecture->lecture_title) : ''; ?>" 
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <label for="lecture_order" class="form-label">Order</label>
                                    <input type="number" class="form-control" id="lecture_order" name="lecture_order" 
                                           value="<?php echo $lecture_order_value; ?>" 
                                           min="1">
                                </div>
                            </div>
                            
                            <div class="row g-20 mt-20">
                                <div class="col-md-8">
                                    <label for="syllabus_id" class="form-label">Syllabus *</label>
                                    <select class="form-select" id="syllabus_id" name="syllabus_id" onchange="updateLectureOrder()" required>
                                        <option value="">Select a syllabus</option>
                                        <?php
                                        $syllabi_stmt = $lecture->getActiveSyllabi($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                        while ($syllabus_row = $syllabi_stmt->fetch()) {
                                            $selected = ($selected_syllabus_id_for_form === (int)$syllabus_row['id']) ? 'selected' : '';
                                            echo "<option value='" . $syllabus_row['id'] . "' ". $selected . ">" . 
                                                 htmlspecialchars($syllabus_row['class_name']) . " - " .
                                                 htmlspecialchars($syllabus_row['subject_name']) . " - " .
                                                 htmlspecialchars($syllabus_row['syllabus_title']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="duration_minutes" class="form-label">Duration (Minutes)</label>
                                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" 
                                           value="<?php echo isset($lecture->duration_minutes) ? $lecture->duration_minutes : ''; ?>" 
                                           min="1">
                                </div>
                            </div>
                            
                            <div class="mt-20">
                                <label for="lecture_type" class="form-label">Lecture Type *</label>
                                <select class="form-select" id="lecture_type" name="lecture_type" onchange="toggleContentFields()" required>
                                    <option value="">Select lecture type</option>
                                    <option value="video" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'video') ? 'selected' : ''; ?>>Video</option>
                                    <option value="audio" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'audio') ? 'selected' : ''; ?>>Audio</option>
                                    <option value="file" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'file') ? 'selected' : ''; ?>>File</option>
                                    <option value="text" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'text') ? 'selected' : ''; ?>>Text</option>
                                    <option value="pptx_embed" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'pptx_embed') ? 'selected' : ''; ?>>PPTX Embed</option>
                                    <option value="multiple" <?php echo (isset($lecture->lecture_type) && $lecture->lecture_type == 'multiple') ? 'selected' : ''; ?>>Multiple Formats</option>
                                </select>
                            </div>

                            <!-- Single Format Content Fields -->
                            <div id="single-format-fields" class="content-type-fields">
                                <!-- File Content (Video, Audio, File, PPTX) -->
                                <div id="file-content" class="content-type-fields mt-20">
                                    <div class="row g-20">
                                        <div class="col-md-6">
                                            <label for="content_file" class="form-label">Upload File</label>
                                            <input type="file" class="form-control" id="content_file" name="content_file">
                                            <div class="form-text">Upload video, audio, or document file</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="content_url" class="form-label">Or Enter URL</label>
                                            <input type="text" class="form-control" id="content_url" name="content_url" 
                                                   value="<?php echo isset($lecture->content_url) && !isset($lecture->file_name) ? htmlspecialchars($lecture->content_url) : ''; ?>">
                                            <div class="form-text">External URL for content</div>
                                        </div>
                                    </div>
                                    <?php if ($action == 'edit' && isset($lecture->file_name) && $lecture->file_name): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current file: <?php echo htmlspecialchars($lecture->file_name); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Text Content -->
                                <div id="text-content" class="content-type-fields mt-20">
                                    <label for="text_content" class="form-label">Text Content *</label>
                                    <div id="editor"><?php echo isset($lecture->text_content) ? $lecture->text_content : ''; ?></div>
                                    <input type="hidden" name="text_content" id="text_content">
                                </div>
                            </div>

                            <!-- Multiple Format Content Fields -->
                            <div id="multiple-format-fields" class="content-type-fields mt-20">
                                <div class="alert alert-info">
                                    <i class="ph ph-info me-2"></i>
                                    <strong>Multiple Format Mode:</strong> Check the formats you want to include and upload the corresponding files. Users will be able to choose which format they want to view.
                                </div>

                                <!-- PDF Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_pdf" name="enable_pdf" value="1">
                                            <label class="form-check-label fw-bold" for="enable_pdf">
                                                <i class="ph ph-file-pdf text-danger me-2"></i> Include PDF Format
                                            </label>
                                        </div>
                                        <div id="pdf_upload_section" style="display: none;">
                                            <label for="pdf_file" class="form-label">Upload PDF File</label>
                                            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                                            <small class="text-muted">Supported format: PDF</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- PPTX Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_pptx" name="enable_pptx" value="1">
                                            <label class="form-check-label fw-bold" for="enable_pptx">
                                                <i class="ph ph-presentation text-warning me-2"></i> Include PowerPoint Format
                                            </label>
                                        </div>
                                        <div id="pptx_upload_section" style="display: none;">
                                            <label for="pptx_content" class="form-label">PowerPoint URL or Embed Code</label>
                                            <textarea class="form-control" id="pptx_content" name="pptx_content" rows="4" placeholder="Enter Google Slides embed URL, PowerPoint online URL, or iframe code"></textarea>
                                            <small class="text-muted">Paste URL or full iframe embed code (e.g., from Google Slides, Office 365, SlideShare)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- iSpring Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_ispring" name="enable_ispring" value="1">
                                            <label class="form-check-label fw-bold" for="enable_ispring">
                                                <i class="ph ph-desktop text-info me-2"></i> Include iSpring HTML5 Package
                                            </label>
                                        </div>
                                        <div id="ispring_upload_section" style="display: none;">
                                            <label for="ispring_file" class="form-label">Upload iSpring ZIP Package</label>
                                            <input type="file" class="form-control" id="ispring_file" name="ispring_file" accept=".zip">
                                            <small class="text-muted">Upload the ZIP file exported from iSpring Converter. It will be extracted automatically.</small>
                                            <div class="alert alert-info mt-3 mb-0">
                                                <strong>How to prepare:</strong>
                                                <ol class="mb-0 mt-2 ps-3">
                                                    <li>Convert your PPTX using iSpring Converter</li>
                                                    <li>Publish as HTML5</li>
                                                    <li>ZIP the entire output folder</li>
                                                    <li>Upload the ZIP package here</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Video Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_video" name="enable_video" value="1">
                                            <label class="form-check-label fw-bold" for="enable_video">
                                                <i class="ph ph-video-camera text-success me-2"></i> Include Video Format
                                            </label>
                                        </div>
                                        <div id="video_upload_section" style="display: none;">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label for="video_file" class="form-label">Upload Video File</label>
                                                    <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                                    <small class="text-muted">Supported formats: MP4, AVI, MOV, etc.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="video_duration" class="form-label">Duration (minutes)</label>
                                                    <input type="number" class="form-control" id="video_duration" name="video_duration" min="1">
                                                </div>
                                                <div class="col-12">
                                                    <label for="video_url" class="form-label">Or Video URL / Embed Code</label>
                                                    <textarea class="form-control" id="video_url" name="video_url" rows="3" placeholder="Paste a direct video URL (MP4), YouTube/Vimeo URL, or an iframe embed code"></textarea>
                                                    <small class="text-muted">If provided, the URL/embed will be used instead of an uploaded file.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Audio Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_audio" name="enable_audio" value="1">
                                            <label class="form-check-label fw-bold" for="enable_audio">
                                                <i class="ph ph-speaker-high text-primary me-2"></i> Include Audio Format
                                            </label>
                                        </div>
                                        <div id="audio_upload_section" style="display: none;">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label for="audio_file" class="form-label">Upload Audio File</label>
                                                    <input type="file" class="form-control" id="audio_file" name="audio_file" accept="audio/*">
                                                    <small class="text-muted">Supported formats: MP3, WAV, OGG, etc.</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="audio_duration" class="form-label">Duration (minutes)</label>
                                                    <input type="number" class="form-control" id="audio_duration" name="audio_duration" min="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Text Format -->
                                <div class="card mb-20">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_text" name="enable_text" value="1">
                                            <label class="form-check-label fw-bold" for="enable_text">
                                                <i class="ph ph-article text-info me-2"></i> Include Text Format
                                            </label>
                                        </div>
                                        <div id="text_upload_section" style="display: none;">
                                            <label for="text_content_multiple" class="form-label">Text Content</label>
                                            <div id="editor_multiple"></div>
                                            <input type="hidden" name="text_content_multiple" id="text_content_multiple">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-20">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($lecture->description) ? htmlspecialchars($lecture->description) : ''; ?></textarea>
                            </div>

                            <div class="mt-20">
                                <label for="lecture_image" class="form-label">Lecture Image <span class="text-13 text-gray-400 fw-medium">(Optional - Max 5MB)</span></label>
                                <input type="file" class="form-control" id="lecture_image" name="lecture_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(event)">
                                <small class="text-gray-400">Supported formats: JPG, PNG, GIF, WebP</small>
                                
                                <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
                                    <img id="imagePreview" class="image-preview" src="" alt="Image Preview">
                                    <button type="button" class="remove-image-btn" onclick="removeImagePreview()" title="Remove image">&times;</button>
                                </div>

                                <?php if ($action == 'edit' && !empty($lecture->image) && file_exists($lecture->image)): ?>
                                    <div id="currentImageContainer" class="image-preview-container mt-3">
                                        <img src="<?php echo htmlspecialchars($lecture->image); ?>" class="image-preview" alt="Current Lecture Image">
                                        <button type="button" class="remove-image-btn" onclick="removeCurrentImage()" title="Remove current image">&times;</button>
                                    </div>
                                    <input type="hidden" id="remove_image" name="remove_image" value="0">
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex-align gap-8 mt-24">
                                <button type="submit" class="btn btn-main rounded-pill">
                                    <?php echo $action == 'add' ? 'Create Lecture' : 'Update Lecture'; ?>
                                </button>
                                <a href="lectures.php" class="btn btn-outline-gray rounded-pill">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ($action == 'view' && $lecture_id > 0): 
                $lecture->id = $lecture_id;
                // Lecture access was validated before output; load for display
                $lecture->readOne([]);
                // Check if this is a multiple format lecture
                $is_multiple_format = ($lecture->lecture_type === 'multiple');
                $lecture_files = [];
                if ($is_multiple_format) {
                    $lecture_files = $lecture->getLectureFiles($lecture_id);
                }
            ?>
                <div class="row gy-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body p-lg-20">
                                <div class="flex-between flex-wrap gap-12 mb-20">
                                    <div>
                                        <h3 class="mb-4"><?php echo htmlspecialchars($lecture->lecture_title); ?></h3>
                                        <p class="text-gray-600 text-15">Order: <?php echo htmlspecialchars($lecture->lecture_order); ?></p>
                                    </div>

                                    <div class="flex-align flex-wrap gap-24">
                                        <span class="py-6 px-16 bg-main-50 text-main-600 rounded-pill text-15">
                                            <?php echo $is_multiple_format ? 'Multiple Formats' : ucfirst($lecture->lecture_type); ?>
                                        </span>
                                        <?php if ($lecture->duration_minutes): ?>
                                            <span class="py-6 px-16 bg-success-50 text-success-600 rounded-pill text-15"><?php echo $lecture->duration_minutes; ?> min</span>
                                        <?php endif; ?>
                                        <a href="lectures.php" class="btn btn-outline-gray rounded-pill py-6 px-16">
                                            <i class="ph ph-arrow-left me-1"></i> Back
                                        </a>
                                    </div>
                                </div>

                                <?php if (!empty($lecture->image) && file_exists($lecture->image)): ?>
                                <div class="mb-24">
                                    <img src="<?php echo htmlspecialchars($lecture->image); ?>" alt="<?php echo htmlspecialchars($lecture->lecture_title); ?>" class="w-100 rounded-8" style="max-height: 400px; object-fit: cover;">
                                </div>
                                <?php endif; ?>

                                <?php if ($is_multiple_format && !empty($lecture_files)): ?>
                                    <!-- Multiple Format Selector -->
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Choose Format</h5>
                                        <div class="flex-align gap-8 flex-wrap">
                                            <?php 
                                            $first = true;
                                            foreach ($lecture_files as $file): 
                                                $icon_class = '';
                                                $label = '';
                                                switch($file['file_type']) {
                                                    case 'pdf':
                                                        $icon_class = 'ph-file-pdf';
                                                        $label = 'PDF';
                                                        break;
                                                    case 'pptx':
                                                        $icon_class = 'ph-presentation';
                                                        $label = 'PowerPoint';
                                                        break;
                                                    case 'ispring':
                                                        $icon_class = 'ph-desktop';
                                                        $label = 'Interactive';
                                                        break;
                                                    case 'video':
                                                        $icon_class = 'ph-video-camera';
                                                        $label = 'Video';
                                                        break;
                                                    case 'audio':
                                                        $icon_class = 'ph-speaker-high';
                                                        $label = 'Audio';
                                                        break;
                                                    case 'text':
                                                        $icon_class = 'ph-article';
                                                        $label = 'Text';
                                                        break;
                                                }
                                            ?>
                                            <button type="button" 
                                                    class="btn <?php echo $first ? 'btn-main' : 'btn-outline-main'; ?> rounded-pill py-7 px-16 format-btn" 
                                                    onclick="switchFormat(<?php echo $lecture_id; ?>, '<?php echo $file['file_type']; ?>')">
                                                <i class="<?php echo $icon_class; ?> me-1"></i> <?php echo $label; ?>
                                            </button>
                                            <?php 
                                                $first = false;
                                            endforeach; 
                                            ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($lecture->description)): ?>
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Description</h5>
                                        <p class="text-gray-300 text-15"><?php echo htmlspecialchars($lecture->description); ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Format Content Display -->
                                    <div class="lecture-content-display">
                                        <?php 
                                        $first_display = true;
                                        foreach ($lecture_files as $file): 
                                        ?>
                                            <div id="format-<?php echo $file['file_type']; ?>" class="format-content" style="display: <?php echo $first_display ? 'block' : 'none'; ?>;">
                                                <?php if ($file['file_type'] == 'pdf'): ?>
                                                    <div class="mb-20">
                                                        <iframe src="<?php echo htmlspecialchars($file['file_url']); ?>" 
                                                                width="100%" 
                                                                height="800px" 
                                                                style="border: none; border-radius: 8px;">
                                                        </iframe>
                                                        <div class="mt-3 text-center">
                                                            <a href="<?php echo htmlspecialchars($file['file_url']); ?>" 
                                                               class="btn btn-outline-main rounded-pill" 
                                                               download>
                                                                <i class="ph ph-download-simple me-2"></i>Download PDF
                                                            </a>
                                                        </div>
                                                    </div>

                                                <?php elseif ($file['file_type'] == 'pptx'): ?>
                                                    <div class="mb-20">
                                                        <?php
                                                        $pptx_content = $file['file_url'];
                                                        // Check if it's an iframe code or just a URL
                                                        if (strpos($pptx_content, '<iframe') !== false) {
                                                            // It's already an iframe, display it
                                                            echo $pptx_content;
                                                        } else {
                                                            // It's a URL, create an iframe
                                                            ?>
                                                            <iframe src="<?php echo htmlspecialchars($pptx_content); ?>" 
                                                                    width="100%" 
                                                                    height="600px" 
                                                                    style="border: 1px solid #ccc; border-radius: 8px;"
                                                                    allowfullscreen>
                                                            </iframe>
                                                            <?php
                                                        }
                                                        ?>
                                                        <div class="mt-3 text-center">
                                                            <a href="<?php echo htmlspecialchars(strip_tags($pptx_content)); ?>" 
                                                               class="btn btn-outline-main rounded-pill" 
                                                               target="_blank">
                                                                <i class="ph ph-arrow-square-out me-2"></i>Open in New Tab
                                                            </a>
                                                        </div>
                                                    </div>

                                                <?php elseif ($file['file_type'] == 'ispring'): ?>
                                                    <div class="mb-20">
                                                        <div class="ratio ratio-16x9 rounded-16 overflow-hidden">
                                                            <iframe src="<?php echo htmlspecialchars($file['file_url']); ?>"
                                                                    width="100%"
                                                                    height="100%"
                                                                    style="border: none;"
                                                                    allowfullscreen>
                                                            </iframe>
                                                        </div>
                                                        <div class="mt-3 text-center">
                                                            <a href="<?php echo htmlspecialchars($file['file_url']); ?>"
                                                               class="btn btn-outline-main rounded-pill"
                                                               target="_blank">
                                                                <i class="ph ph-arrow-square-out me-2"></i>Open in Full Screen
                                                            </a>
                                                        </div>
                                                    </div>

                                                <?php elseif ($file['file_type'] == 'video'): ?>
                                                    <?php 
                                                        $vurl = trim($file['file_url'] ?? '');
                                                        $iframe_src = '';
                                                        if (stripos($vurl, '<iframe') !== false) {
                                                            // Already iframe embed
                                                            echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20">' . $vurl . '</div>';
                                                        } else {
                                                            if (preg_match('/youtu\.be\/([\w-]+)/i', $vurl, $m) || preg_match('/youtube\.com\/(?:watch\?v=|embed\/)([\w-]+)/i', $vurl, $m)) {
                                                                $iframe_src = 'https://www.youtube.com/embed/' . $m[1];
                                                            } elseif (preg_match('/vimeo\.com\/(\d+)/i', $vurl, $m)) {
                                                                $iframe_src = 'https://player.vimeo.com/video/' . $m[1];
                                                            } elseif (preg_match('/facebook\.com\/.*\/videos\/(\d+)/i', $vurl)) {
                                                                $iframe_src = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($vurl) . '&show_text=0&width=1280';
                                                            } elseif (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/i', $vurl, $m)) {
                                                                $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                            } elseif (preg_match('/(?:open|uc)\?id=([^&]+)/i', $vurl, $m)) {
                                                                $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                            }

                                                            if ($iframe_src) {
                                                                echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20"><iframe src="' . htmlspecialchars($iframe_src) . '" allowfullscreen frameborder="0"></iframe></div>';
                                                            } else {
                                                                echo '<div class="rounded-16 overflow-hidden mb-20">\n  <video class="player w-100" controls>\n    <source src="' . htmlspecialchars($vurl) . '">\n    Your browser does not support the video tag.\n  </video>\n</div>';
                                                            }
                                                        }
                                                    ?>
                                                    <?php if ($file['duration_minutes']): ?>
                                                        <p class="text-gray-600 text-center">
                                                            <i class="ph ph-clock me-1"></i>Duration: <?php echo $file['duration_minutes']; ?> minutes
                                                        </p>
                                                    <?php endif; ?>

                                                <?php elseif ($file['file_type'] == 'audio'): ?>
                                                    <div class="mb-20">
                                                        <audio controls class="w-100">
                                                            <source src="<?php echo htmlspecialchars($file['file_url']); ?>">
                                                            Your browser does not support the audio element.
                                                        </audio>
                                                    </div>
                                                    <?php if ($file['duration_minutes']): ?>
                                                        <p class="text-gray-600 text-center">
                                                            <i class="ph ph-clock me-1"></i>Duration: <?php echo $file['duration_minutes']; ?> minutes
                                                        </p>
                                                    <?php endif; ?>

                                                <?php elseif ($file['file_type'] == 'text'): ?>
                                                    <div class="content-text-block p-4 bg-light rounded-8">
                                                        <?php echo $file['text_content']; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php 
                                            $first_display = false;
                                        endforeach; 
                                        ?>
                                    </div>

                                <?php else: ?>
                                    <!-- Single Format Display -->
                                    <?php if (!empty($lecture->description)): ?>
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Description</h5>
                                        <p class="text-gray-300 text-15"><?php echo htmlspecialchars($lecture->description); ?></p>
                                    </div>
                                    <?php endif; ?>

                                    <div class="lecture-content-display">
                                        <?php if ($lecture->lecture_type == 'text'): ?>
                                            <div class="content-text-block">
                                                <?php echo $lecture->text_content; ?>
                                            </div>
                                        <?php elseif ($lecture->lecture_type == 'video' && $lecture->content_url): ?>
                                            <?php 
                                                $vurl = trim($lecture->content_url);
                                                $iframe_src = '';
                                                if (stripos($vurl, '<iframe') !== false) {
                                                    echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20">' . $vurl . '</div>';
                                                } else {
                                                    if (preg_match('/youtu\.be\/([\w-]+)/i', $vurl, $m) || preg_match('/youtube\.com\/(?:watch\?v=|embed\/)([\w-]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://www.youtube.com/embed/' . $m[1];
                                                    } elseif (preg_match('/vimeo\.com\/(\d+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://player.vimeo.com/video/' . $m[1];
                                                    } elseif (preg_match('/facebook\.com\/.*\/videos\/(\d+)/i', $vurl)) {
                                                        $iframe_src = 'https://www.facebook.com/plugins/video.php?href=' . urlencode($vurl) . '&show_text=0&width=1280';
                                                    } elseif (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                    } elseif (preg_match('/(?:open|uc)\?id=([^&]+)/i', $vurl, $m)) {
                                                        $iframe_src = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
                                                    }
                                                    if ($iframe_src) {
                                                        echo '<div class="ratio ratio-16x9 rounded-16 overflow-hidden mb-20"><iframe src="' . htmlspecialchars($iframe_src) . '" allowfullscreen frameborder="0"></iframe></div>';
                                                    } else {
                                                        echo '<div class="rounded-16 overflow-hidden mb-20">\n  <video class="player w-100" controls>\n    <source src="' . htmlspecialchars($vurl) . '">\n  </video>\n</div>';
                                                    }
                                                }
                                            ?>
                                        <?php elseif ($lecture->lecture_type == 'audio' && $lecture->content_url): ?>
                                            <div class="mb-20">
                                                <audio controls class="w-100">
                                                    <source src="<?php echo htmlspecialchars($lecture->content_url); ?>">
                                                </audio>
                                            </div>
                                        <?php elseif ($lecture->lecture_type == 'pptx_embed' && $lecture->content_url): ?>
                                            <div class="text-center mb-20">
                                                <a href="<?php echo BASE_URL . htmlspecialchars($lecture->content_url); ?>" target="_blank" class="btn btn-main rounded-pill">
                                                    <i class="ph ph-presentation-chart me-2"></i>View Presentation
                                                </a>
                                            </div>
                                        <?php elseif ($lecture->lecture_type == 'file' && $lecture->content_url): ?>
                                            <div class="text-center mb-20">
                                                <?php
                                                $file_extension = strtolower(pathinfo($lecture->content_url, PATHINFO_EXTENSION));
                                                if ($file_extension == 'pdf'):
                                                ?>
                                                    <iframe src="<?php echo htmlspecialchars($lecture->content_url); ?>" width="100%" height="800px" style="border: none; border-radius: 8px;"></iframe>
                                                    <div class="mt-3">
                                                        <a href="<?php echo htmlspecialchars($lecture->content_url); ?>" class="btn btn-outline-main rounded-pill" download>
                                                            <i class="ph ph-download-simple me-2"></i>Download PDF
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="<?php echo htmlspecialchars($lecture->content_url); ?>" class="btn btn-main rounded-pill" download>
                                                        <i class="ph ph-download-simple me-2"></i>Download File (<?php echo htmlspecialchars($lecture->file_name); ?>)
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-16 fw-bold">Lecture Details</h5>
                                
                                <div class="mb-16">
                                    <p class="text-gray-600 mb-4 text-13"><strong>Type:</strong></p>
                                    <span class="py-4 px-12 bg-main-50 text-main-600 rounded-pill text-13">
                                        <?php echo $is_multiple_format ? 'Multiple Formats' : ucfirst($lecture->lecture_type); ?>
                                    </span>
                                </div>
                                
                                <?php if ($lecture->duration_minutes): ?>
                                <div class="mb-16 pb-16 border-bottom border-gray-100">
                                    <p class="text-gray-600 mb-4 text-13"><strong>Duration:</strong></p>
                                    <p class="text-15"><?php echo $lecture->duration_minutes; ?> minutes</p>
                                </div>
                                <?php endif; ?>

                                <div class="mb-16 pb-16 border-bottom border-gray-100">
                                    <p class="text-gray-600 mb-4 text-13"><strong>Order:</strong></p>
                                    <p class="text-15"><?php echo $lecture->lecture_order; ?></p>
                                </div>

                                <?php if ($is_multiple_format && !empty($lecture_files)): ?>
                                <div class="mb-16 pb-16 border-bottom border-gray-100">
                                    <p class="text-gray-600 mb-8 text-13"><strong>Available Formats:</strong></p>
                                    <div class="flex-column gap-8">
                                        <?php foreach ($lecture_files as $file): ?>
                                            <div class="flex-align gap-8">
                                                <i class="ph 
                                                    <?php 
                                                    echo $file['file_type'] == 'pdf' ? 'ph-file-pdf text-danger' : 
                                                         ($file['file_type'] == 'pptx' ? 'ph-presentation text-warning' : 
                                                         ($file['file_type'] == 'ispring' ? 'ph-desktop text-info' :
                                                         ($file['file_type'] == 'video' ? 'ph-video-camera text-success' : 
                                                         ($file['file_type'] == 'audio' ? 'ph-speaker-high text-primary' : 
                                                         'ph-article text-info'))));
                                                    ?>
                                                "></i>
                                                <span class="text-13"><?php echo $file['file_type'] === 'ispring' ? 'Interactive' : ucfirst($file['file_type']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (can('lectures.edit') || can('lectures.delete')): ?>
                                <div class="flex-column gap-8">
                                    <?php if (can('lectures.edit')): ?>
                                    <a href="lectures.php?action=edit&id=<?php echo $lecture->id; ?>" class="btn btn-outline-main rounded-pill w-100">
                                        <i class="ph ph-pencil me-2"></i>Edit Lecture
                                    </a>
                                    <?php endif; ?>
                                    <?php if (can('lectures.delete')): ?>
                                    <button onclick="confirmDelete(<?php echo $lecture->id; ?>, '<?php echo htmlspecialchars($lecture->lecture_title); ?>')" class="btn btn-outline-danger rounded-pill w-100">
                                        <i class="ph ph-trash me-2"></i>Delete Lecture
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the lecture "<span id="lectureTitle"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/boostrap.bundle.min.js"></script>
    <script src="assets/js/phosphor-icon.js"></script>
    <script src="assets/js/file-upload.js"></script>
    <script src="assets/js/plyr.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="assets/js/full-calendar.js"></script>
    <script src="assets/js/jquery-ui.js"></script>
    <script src="assets/js/editor-quill.js"></script>
    <script src="assets/js/apexcharts.min.js"></script>
    <script src="assets/js/calendar.js"></script>
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <script>
        let quill;
        let quillMultiple;

        function confirmDelete(id, title) {
            document.getElementById('deleteId').value = id;
            document.getElementById('lectureTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function toggleContentFields() {
            const lectureType = document.getElementById('lecture_type').value;
            const singleFormatFields = document.getElementById('single-format-fields');
            const multipleFormatFields = document.getElementById('multiple-format-fields');
            const fileContent = document.getElementById('file-content');
            const textContent = document.getElementById('text-content');

            // Hide all fields first
            singleFormatFields.style.display = 'none';
            multipleFormatFields.style.display = 'none';
            fileContent.style.display = 'none';
            textContent.style.display = 'none';

            if (lectureType === 'multiple') {
                // Show multiple format fields
                multipleFormatFields.style.display = 'block';
                // Initialize Quill for text format if not already initialized
                if (!quillMultiple && document.getElementById('editor_multiple')) {
                    initializeQuillMultiple();
                }
            } else if (lectureType === 'text') {
                // Show single format fields with text editor
                singleFormatFields.style.display = 'block';
                textContent.style.display = 'block';
                if (!quill) {
                    initializeQuill();
                }
            } else if (lectureType === 'video' || lectureType === 'audio' || lectureType === 'file' || lectureType === 'pptx_embed') {
                // Show single format fields with file upload
                singleFormatFields.style.display = 'block';
                fileContent.style.display = 'block';
            }
        }

        function initializeQuill() {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
        }

        function initializeQuillMultiple() {
            quillMultiple = new Quill('#editor_multiple', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        ['link', 'blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
        }

        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImagePreview() {
            document.getElementById('lecture_image').value = '';
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('imagePreview').src = '';
        }

        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                document.getElementById('remove_image').value = '1';
                document.getElementById('currentImageContainer').style.display = 'none';
            }
        }

        // Toggle upload sections based on checkbox
        function toggleUploadSection(checkboxId, sectionId) {
            const checkbox = document.getElementById(checkboxId);
            const section = document.getElementById(sectionId);
            
            if (checkbox && section) {
                checkbox.addEventListener('change', function() {
                    section.style.display = this.checked ? 'block' : 'none';
                });
            }
        }

        function updateLectureOrder() {
            const syllabusSelect = document.getElementById('syllabus_id');
            const orderInput = document.getElementById('lecture_order');
            const actionInput = document.querySelector('input[name="action"]');

            if (!syllabusSelect || !orderInput || !actionInput || actionInput.value !== 'create') {
                return;
            }

            const syllabusId = syllabusSelect.value;
            if (!syllabusId) {
                orderInput.value = '1';
                return;
            }

            fetch('lectures.php?ajax=get_next_order&syllabus_id=' + encodeURIComponent(syllabusId), {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Unable to load next lecture order.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.next_order) {
                        orderInput.value = data.next_order;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Format selector for view page
        function switchFormat(lectureId, format) {
            // Hide all format contents
            document.querySelectorAll('.format-content').forEach(el => {
                el.style.display = 'none';
            });
            
            // Show selected format content
            const selectedContent = document.getElementById('format-' + format);
            if (selectedContent) {
                selectedContent.style.display = 'block';
            }
            
            // Update button states
            document.querySelectorAll('.format-btn').forEach(btn => {
                btn.classList.remove('btn-main');
                btn.classList.add('btn-outline-main');
            });
            
            const activeBtn = document.querySelector(`[onclick="switchFormat(${lectureId}, '${format}')"]`);
            if (activeBtn) {
                activeBtn.classList.remove('btn-outline-main');
                activeBtn.classList.add('btn-main');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('lecture_type')) {
                toggleContentFields();
            }

            const syllabusSelect = document.getElementById('syllabus_id');
            const actionInput = document.querySelector('input[name="action"]');
            if (syllabusSelect && syllabusSelect.value && actionInput && actionInput.value === 'create') {
                updateLectureOrder();
            }
            
            // Initialize multiple format checkboxes
            toggleUploadSection('enable_pdf', 'pdf_upload_section');
            toggleUploadSection('enable_pptx', 'pptx_upload_section');
            toggleUploadSection('enable_ispring', 'ispring_upload_section');
            toggleUploadSection('enable_video', 'video_upload_section');
            toggleUploadSection('enable_audio', 'audio_upload_section');
            toggleUploadSection('enable_text', 'text_upload_section');
            
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (quill) {
                        document.getElementById('text_content').value = quill.root.innerHTML;
                    }
                    if (quillMultiple) {
                        document.getElementById('text_content_multiple').value = quillMultiple.root.innerHTML;
                    }
                });
            }

            if (typeof Plyr !== 'undefined') {
                const players = Array.from(document.querySelectorAll('.player')).map(p => new Plyr(p));
            }
        });

        $('.course-item__button').on('click', function() {
            const dropdown = $(this).next('.course-item-dropdown');
            const arrow = $(this).find('.course-item__arrow');
            
            dropdown.slideToggle();
            $(this).toggleClass('active');
            arrow.toggleClass('rotate-90');
            
            $('.course-item__button').not(this).removeClass('active').next('.course-item-dropdown').slideUp();
            $('.course-item__arrow').not(arrow).removeClass('rotate-90');
        });
    </script>

    <style>
        .course-item__arrow {
            transition: transform 0.3s ease;
        }
        
        .course-item__arrow.rotate-90 {
            transform: rotate(90deg);
        }
        
        .course-item-dropdown {
            display: none;
        }
        
        .course-item-dropdown.active {
            display: block;
        }
        
        .text-line-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .hover-bg-success-600:hover {
            background-color: #059669 !important;
        }
        
        .hover-bg-danger-600:hover {
            background-color: #dc2626 !important;
        }
        
        .hover-text-white:hover {
            color: #ffffff !important;
        }
        
        .bg-purple-50 {
            background-color: #faf5ff;
        }
        
        .text-purple-600 {
            color: #9333ea;
        }
    </style>
    
</body>
</html>
