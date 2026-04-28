<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'classes/Syllabus.php';
require_once 'classes/Lecture.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$classModel = new ClassModel($db);
$syllabus = new Syllabus($db);
$lecture = new Lecture($db);
$user = new User($db);
$batchModel = new Batch($db);
$enrollmentModel = new BatchEnrollment($db);

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 13;

if ($course_id <= 0) {
    flash_message('Invalid course ID.', 'error');
    redirect('courses.php');
}

// Get course details
$classModel->id = $course_id;
if (!$classModel->readOne()) {
    flash_message('Course not found.', 'error');
    redirect('courses.php');
}

// Store course data
$course = [
    'id' => $classModel->id,
    'name' => $classModel->class_name,
    'code' => $classModel->class_code,
    'type' => $classModel->type,
    'description' => $classModel->description,
    'image' => $classModel->image,
    'registration_open' => $classModel->registration_open,
    'status' => $classModel->status
];

// Only show active courses or allow admins to view all
if ($course['status'] !== 'active' && !in_array($user_role, ['super_admin', 'organization_admin', 'school_admin'])) {
    flash_message('This course is not available.', 'error');
    redirect('courses.php');
}

// Check if course type is correct
if ($course['type'] !== 'course') {
    flash_message('This is not a course. Please view it from the classes section.', 'error');
    redirect('classes.php?id=' . $course_id);
}

// Get accessible classes for the user
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes = ($current_user['can_access_all_classes'] ?? 0) == 1;

// Check if user has access to this course
$has_access = $can_access_all_classes || in_array($course_id, $accessible_class_ids);
// Determine if user is missing basic info
$basic_info_missing = empty($current_user['country']) || empty($current_user['phone']) || empty($current_user['gender']) || empty($current_user['address']);

// Allow preview of first N lectures without access
$preview_limit = 3;
$preview_shown = 0;

// Get syllabi for this course
$syllabi_stmt = $syllabus->read($can_access_all_classes ? [] : [$course_id], 'course');
$syllabi = [];
$total_lectures = 0;
while ($row = $syllabi_stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['class_id'] == $course_id) {
        // Get lectures for this syllabus
        $lectures_stmt = $lecture->readBySyllabus($row['id']);
        $syllabus_lectures = [];
        while ($lec = $lectures_stmt->fetch(PDO::FETCH_ASSOC)) {
            $syllabus_lectures[] = $lec;
            $total_lectures++;
        }
        $row['lectures'] = $syllabus_lectures;
        $row['lecture_count'] = count($syllabus_lectures);
        $syllabi[] = $row;
    }
}

// Get course statistics
$total_duration = array_sum(array_column($syllabi, 'duration_weeks'));
$total_syllabi = count($syllabi);

// Get available batches for this course
$available_batches = $batchModel->getBatchesByClass($course_id);
// Determine if there is any batch currently open for enrollment
$has_open_batch = false;
foreach ($available_batches as $b) {
    if (($b['enrollment_status'] ?? '') === 'open') {
        $has_open_batch = true;
        break;
    }
}

// Check which batches the user is already enrolled in
$user_enrollments = $enrollmentModel->getEnrollmentsByUser($current_user['id']);
$enrolled_batch_ids = array_column($user_enrollments, 'batch_id');

// If the user is registered in any batch of this course, grant access
$enrolled_in_course = false;
foreach ($user_enrollments as $en) {
    $en_class_id = (int)($en['class_id'] ?? 0);
    $status = $en['enrollment_status'] ?? '';
    if ($en_class_id === $course_id && in_array($status, ['pending','active','completed'])) {
        $enrolled_in_course = true;
        break;
    }
}
$has_access = $has_access || $enrolled_in_course;

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['name']); ?> - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/file-upload.css">
    <link rel="stylesheet" href="assets/css/plyr.css">
    <link rel="stylesheet" href="assets/css/full-calendar.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/editor-quill.css">
    <link rel="stylesheet" href="assets/css/apexcharts.css">
    <link rel="stylesheet" href="assets/css/calendar.css">
    <link rel="stylesheet" href="assets/css/jquery-jvectormap-2.0.5.css">
    <link rel="stylesheet" href="assets/css/main.css">
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

            <!-- Breadcrumb Start -->
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="courses.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Courses</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">Course Details</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

            <?php if ($flash): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row gy-4">
                <div class="col-md-8">
                    <!-- Course Card Start -->
                    <div class="card">
                        <div class="card-body p-lg-20 p-sm-3">
                            <div class="flex-between flex-wrap gap-12 mb-20">
                                <div>
                                    <h3 class="mb-4"><?php echo htmlspecialchars($course['name']); ?></h3>
                                    <p class="text-gray-600 text-15">
                                        <i class="ph ph-hash"></i> <?php echo htmlspecialchars($course['code']); ?>
                                    </p>
                                </div>

                                <div class="flex-align flex-wrap gap-24">
                                    <?php if ($course['registration_open']): ?>
                                        <span class="py-6 px-16 bg-success-50 text-success-600 rounded-pill text-15">
                                            <i class="ph ph-check-circle"></i> Registration Open
                                        </span>
                                    <?php else: ?>
                                        <span class="py-6 px-16 bg-danger-50 text-danger-600 rounded-pill text-15">
                                            <i class="ph ph-x-circle"></i> Registration Closed
                                        </span>
                                    <?php endif; ?>
                                    <button type="button" class="bookmark-icon text-gray-200 text-26 d-flex hover-text-main-600">
                                        <i class="ph ph-bookmarks"></i>
                                    </button>
                                </div>
                            </div>

                            <?php if (!empty($course['image'])): ?>
                                <div class="rounded-16 overflow-hidden mb-24">
                                    <img src="uploads/classes/<?php echo htmlspecialchars($course['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($course['name']); ?>" 
                                         class="w-100" style="max-height: 400px; object-fit: cover;">
                                </div>
                            <?php else: ?>
                                <div class="rounded-16 overflow-hidden mb-24 bg-main-50 d-flex align-items-center justify-content-center" 
                                     style="height: 300px;">
                                    <i class="ph ph-book text-main-600" style="font-size: 80px;"></i>
                                </div>
                            <?php endif; ?>


                            <div class="mt-24">
                                <div class="mb-24 pb-24 border-bottom border-gray-100">
                                    <h5 class="mb-12 fw-bold">About this course</h5>
                                    <?php if (!empty($course['description'])): ?>
                                        <p class="text-gray-300 text-15"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                                    <?php else: ?>
                                        <p class="text-gray-300 text-15">No description available for this course.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-24 pb-24 border-bottom border-gray-100">
                                    <h5 class="mb-16 fw-bold">This Course Includes</h5>
                                    <div class="row g-20">
                                        <div class="col-md-6">
                                            <ul>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    <?php echo $total_syllabi; ?> Course Syllabi
                                                </li>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    <?php echo $total_lectures; ?> Video Lectures
                                                </li>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    <?php echo $total_duration; ?> Weeks Duration
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    <?php echo $has_access ? 'Full Access' : 'Limited Access'; ?>
                                                </li>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    Access on Mobile & Desktop
                                                </li>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-checks"></i></span>
                                                    Structured Learning Path
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <h5 class="mb-12 fw-bold">Course Statistics</h5>
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="text-center p-3 bg-main-50 rounded-8">
                                                <h4 class="text-main-600 mb-0"><?php echo $total_syllabi; ?></h4>
                                                <p class="text-gray-600 text-sm mb-0">Syllabi</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-center p-3 bg-success-50 rounded-8">
                                                <h4 class="text-success-600 mb-0"><?php echo $total_lectures; ?></h4>
                                                <p class="text-gray-600 text-sm mb-0">Lectures</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-center p-3 bg-warning-50 rounded-8">
                                                <h4 class="text-warning-600 mb-0"><?php echo $total_duration; ?></h4>
                                                <p class="text-gray-600 text-sm mb-0">Weeks</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="text-center p-3 bg-purple-50 rounded-8">
                                                <h4 class="text-purple-600 mb-0"><?php echo $has_access ? 'âœ“' : 'âœ—'; ?></h4>
                                                <p class="text-gray-600 text-sm mb-0">Access</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Course Card End -->
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <?php if (empty($syllabi)): ?>
                                <div class="text-center py-5 px-3">
                                    <i class="ph ph-book-open text-gray-300" style="font-size: 64px;"></i>
                                    <p class="text-gray-600 mt-3 mb-0">No syllabi available for this course yet.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($syllabi as $index => $syl): ?>
                                    <div class="course-item">
                                        <button type="button" class="course-item__button <?php echo $index === 0 ? 'active' : ''; ?> flex-align gap-4 w-100 p-16 border-bottom border-gray-100">
                                            <span class="d-block text-start">
                                                <span class="d-block h5 mb-0 text-line-1"><?php echo htmlspecialchars($syl['syllabus_title']); ?></span>
                                                <span class="d-block text-15 text-gray-300">
                                                    <?php echo $syl['lecture_count']; ?> lecture<?php echo $syl['lecture_count'] != 1 ? 's' : ''; ?> | <?php echo $syl['duration_weeks']; ?> week<?php echo $syl['duration_weeks'] != 1 ? 's' : ''; ?>
                                                </span>
                                            </span>
                                            <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-arrow-right"></i></span>
                                        </button>
                                        <div class="course-item-dropdown <?php echo $index === 0 ? 'active' : ''; ?> border-bottom border-gray-100">
                                            <?php if (!empty($syl['description'])): ?>
                                                <div class="px-16 pt-16 pb-8">
                                                    <p class="text-sm text-gray-600 mb-0"><?php echo htmlspecialchars($syl['description']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($syl['lectures'])): ?>
                                                <ul class="course-list p-16 pb-0">
                                                    <?php foreach ($syl['lectures'] as $lec_index => $lec): ?>
                                                        <li class="course-list__item flex-align gap-8 mb-16">
                                                            <span class="circle flex-shrink-0 text-32 d-flex text-gray-100">
                                                                <i class="ph ph-play-circle"></i>
                                                            </span>
                                                            <div class="w-100">
                                                                <a href="lectures.php?id=<?php echo $lec['id']; ?>" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                                    <?php echo ($lec_index + 1) . '. ' . htmlspecialchars($lec['lecture_title']); ?>
                                                                    <span class="text-gray-300 fw-normal d-block text-sm">
                                                                        <?php echo ucfirst($lec['lecture_type']); ?>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <div class="p-16 text-center">
                                                    <p class="text-sm text-gray-500 mb-0">No lectures available yet</p>
                                                </div>
                                            <?php endif; ?>

                                            <div class="p-16 pt-0">
                                                <a href="lectures.php?syllabus=<?php echo $syl['id']; ?>" class="btn btn-outline-main-600 btn-sm w-100">
                                                    <i class="ph ph-video-camera me-4"></i>View All Lectures
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$has_access && $course['registration_open'] && !$has_open_batch): ?>
                        <div class="card mt-24">
                            <div class="card-body">
                                <h5 class="mb-16">Ready to register?</h5>
                                <p class="text-gray-300 mb-20">Complete your basic info and submit a registration request.</p>
                                <button type="button" class="btn btn-main rounded-pill py-11 w-100" data-bs-toggle="modal" data-bs-target="#registerModal">Register Now</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Batches Section -->
            <?php if (!empty($available_batches)): ?>
            <div class="row mt-24">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100 flex-between flex-wrap gap-8">
                            <h5 class="mb-0">Available Batches</h5>
                            <span class="badge bg-main-600 text-white"><?php echo count($available_batches); ?> Batch<?php echo count($available_batches) != 1 ? 'es' : ''; ?> Available</span>
                        </div>
                        <div class="card-body">
                            <p class="text-gray-600 mb-24">Choose a batch that fits your schedule and register to start learning!</p>
                            
                            <div class="row g-20">
                                <?php foreach ($available_batches as $batch): ?>
                                    <?php 
                                    $is_enrolled = in_array($batch['id'], $enrolled_batch_ids);
                                    $is_full = $batch['enrollment_status'] === 'full';
                                    $is_closed = $batch['enrollment_status'] === 'closed';
                                    $can_enroll = $batch['enrollment_status'] === 'open' && !$is_enrolled;
                                    $spots_left = null;
                                    if ($batch['max_students']) {
                                        $spots_left = $batch['max_students'] - ($batch['active_students'] + ($batch['pending_students'] ?? 0));
                                    }
                                    ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card shadow-sm h-100 border <?php echo $is_enrolled ? 'border-success' : ($can_enroll ? 'border-main' : 'border-gray-100'); ?>">
                                            <div class="card-body">
                                                <!-- Batch Header -->
                                                <div class="flex-between mb-16">
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($batch['batch_name']); ?></h6>
                                                    <?php if ($is_enrolled): ?>
                                                        <span class="badge bg-success-600 text-white">
                                                            <i class="ph ph-check-circle"></i> Enrolled
                                                        </span>
                                                    <?php elseif ($is_full): ?>
                                                        <span class="badge bg-danger-600 text-white">
                                                            <i class="ph ph-x-circle"></i> Full
                                                        </span>
                                                    <?php elseif ($is_closed): ?>
                                                        <span class="badge bg-gray-600 text-white">
                                                            <i class="ph ph-lock"></i> Closed
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-main-600 text-white">
                                                            <i class="ph ph-calendar-check"></i> Open
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Batch Code -->
                                                <p class="text-sm text-gray-500 mb-12">
                                                    <i class="ph ph-hash"></i> <?php echo htmlspecialchars($batch['batch_code']); ?>
                                                </p>

                                                <!-- Batch Description -->
                                                <?php if (!empty($batch['description'])): ?>
                                                    <p class="text-sm text-gray-600 mb-16">
                                                        <?php echo htmlspecialchars(substr($batch['description'], 0, 100)) . (strlen($batch['description']) > 100 ? '...' : ''); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <!-- Batch Details -->
                                                <div class="mb-16">
                                                    <?php if ($batch['start_date']): ?>
                                                        <div class="flex-align gap-8 mb-8">
                                                            <i class="ph ph-calendar text-main-600"></i>
                                                            <span class="text-sm text-gray-600">
                                                                <?php echo date('M d, Y', strtotime($batch['start_date'])); ?>
                                                                <?php if ($batch['end_date']): ?>
                                                                    - <?php echo date('M d, Y', strtotime($batch['end_date'])); ?>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($batch['meeting_schedule']): ?>
                                                        <div class="flex-align gap-8 mb-8">
                                                            <i class="ph ph-clock text-main-600"></i>
                                                            <span class="text-sm text-gray-600">
                                                                <?php echo htmlspecialchars($batch['meeting_schedule']); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($batch['instructor_name']): ?>
                                                        <div class="flex-align gap-8 mb-8">
                                                            <i class="ph ph-chalkboard-teacher text-main-600"></i>
                                                            <span class="text-sm text-gray-600">
                                                                <?php echo htmlspecialchars($batch['instructor_name']); ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($batch['max_students']): ?>
                                                        <div class="flex-align gap-8">
                                                            <i class="ph ph-users text-main-600"></i>
                                                            <span class="text-sm text-gray-600">
                                                                <?php echo $batch['active_students']; ?> / <?php echo $batch['max_students']; ?> students
                                                                <?php if ($spots_left !== null && $spots_left > 0 && $spots_left <= 5): ?>
                                                                    <span class="badge bg-warning-600 text-white ms-4">Only <?php echo $spots_left; ?> spots left!</span>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Action Button -->
                                                <?php if ($is_enrolled): ?>
                                                    <button class="btn btn-success-600 w-100" disabled>
                                                        <i class="ph ph-check-circle me-4"></i>Already Enrolled
                                                    </button>
                                                <?php elseif ($can_enroll): ?>
                                                    <a href="batch-enroll.php?batch_id=<?php echo $batch['id']; ?>" class="btn btn-main w-100 btn-register-batch" data-batch-id="<?php echo $batch['id']; ?>">
                                                        <i class="ph ph-plus-circle me-4"></i>Register for this Batch
                                                    </a>
                                                <?php elseif ($is_full): ?>
                                                    <button class="btn btn-outline-danger w-100" disabled>
                                                        <i class="ph ph-x-circle me-4"></i>Batch Full
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-secondary w-100" disabled>
                                                        <i class="ph ph-lock me-4"></i>Registration Closed
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer Start -->
        <div class="dashboard-footer">
            <div class="flex-between flex-wrap gap-16">
                <p class="text-gray-300 text-13 fw-normal">&copy; Copyright <?php echo APP_NAME; ?> <?php echo date('Y'); ?>, All Right Reserved</p>
                <div class="flex-align flex-wrap gap-16">
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600">License</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600">Support</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600">Documentation</a>
                </div>
            </div>
        </div>
        <!-- Footer End -->
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Complete Your Information</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="api/save_basic_info_and_inquiry.php">
            <div class="modal-body">
                <input type="hidden" name="class_id" value="<?php echo (int)$course_id; ?>">
                <input type="hidden" name="redirect_to" id="redirect_to" value="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($current_user['full_name'] ?? ''); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($current_user['email'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-select py-9 placeholder-13 text-15" id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cabo Verde">Cabo Verde</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Eswatini">Eswatini</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Greece">Greece</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guinea-Bissau">Guinea-Bissau</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="India">India</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Ivory Coast">Ivory Coast</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Micronesia">Micronesia</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montenegro">Montenegro</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Namibia">Namibia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="North Korea">North Korea</option>
                                <option value="North Macedonia">North Macedonia</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau">Palau</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                <option value="Saint Lucia">Saint Lucia</option>
                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                <option value="Samoa">Samoa</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Serbia">Serbia</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="South Korea">South Korea</option>
                                <option value="South Sudan">South Sudan</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Timor-Leste">Timor-Leste</option>
                                <option value="Togo">Togo</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Emirates">United Arab Emirates</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="United States">United States</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City">Vatican City</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>

                            </select>

                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($current_user['phone'] ?? ($current_user['whatsapp_number'] ?? '')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" name="gender" required>
                            <?php $g = $current_user['gender'] ?? ''; ?>
                            <option value="">Select</option>
                            <option value="Male" <?php echo ($g==='Male')?'selected':''; ?>>Male</option>
                            <option value="Female" <?php echo ($g==='Female')?'selected':''; ?>>Female</option>
                            <option value="Other" <?php echo ($g==='Other')?'selected':''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Preferred Time Slot</label>
                        <input type="text" class="form-control" name="time_slot" placeholder="e.g. Evenings, 7-9 PM">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($current_user['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <p class="text-gray-600 mt-3 mb-0">Submitting will send a registration request for this course. An admin will review and enroll you into a batch.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-main">Submit Request</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/boostrap.bundle.min.js"></script>
    <script src="assets/js/phosphor-icon.js"></script>
    <script src="assets/js/file-upload.js"></script>
    <script src="assets/js/plyr.js"></script>
    <script src="assets/js/full-calendar.js"></script>
    <script src="assets/js/jquery-ui.js"></script>
    <script src="assets/js/editor-quill.js"></script>
    <script src="assets/js/apexcharts.min.js"></script>
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        const basicInfoMissing = <?php echo $basic_info_missing ? 'true' : 'false'; ?>;
        $(document).on('click', '.btn-register-batch', function(e) {
            if (basicInfoMissing) {
                e.preventDefault();
                const batchId = $(this).data('batch-id');
                $('#redirect_to').val('batch-enroll.php?batch_id=' + batchId);
                const modal = new bootstrap.Modal(document.getElementById('registerModal'));
                modal.show();
            }
        });
        // Bookmark js Start
        $('.bookmark-icon').on('click', function () {
            $(this).toggleClass('active');
            let icon = $(this).children('i');

            if ($(this).hasClass('active')) {
                icon.removeClass('ph ph-bookmarks');
                icon.addClass('ph-fill ph-bookmarks text-main-600');
            } else {
                icon.removeClass('ph-fill ph-bookmarks');
                icon.addClass('ph ph-bookmarks');
            }
        });
        // Bookmark js End
    </script>

    <style>
        .bg-purple-50 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .border-purple-600 {
            border-color: #9333ea !important;
        }
    </style>

</body>

</html>
