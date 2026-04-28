<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';
require_once 'classes/BatchRegistrationLink.php';
require_once 'includes/functions.php';

// Check if user is logged in
require_roles(['student', 'solo_student', 'teacher', 'organization_admin', 'school_admin', 'super_admin']);

$current_user = current_user();

$database = new Database();
$pdo = $database->getConnection();

$batchModel = new Batch($pdo);
$enrollmentModel = new BatchEnrollment($pdo);
$linkModel = new BatchRegistrationLink($pdo);

$token = isset($_GET['token']) ? $_GET['token'] : '';
$errors = [];
$success = false;

if (empty($token)) {
    $errors[] = 'Invalid registration link';
}

// Get link details
$link = $linkModel->getByToken($token);

if (!$link) {
    $errors[] = 'Registration link not found';
} else {
    // Validate link
    $validation = $linkModel->isValid();
    
    if (!$validation['valid']) {
        $errors[] = $validation['message'];
    }
    
    // Get batch details
    $batchModel->id = $link['batch_id'];
    $batch = $batchModel->readOne();
    
    // Check if batch can accept enrollments
    if ($batch && !$batchModel->canEnroll()) {
        $errors[] = 'This batch is not accepting enrollments at this time';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $user_id = $_SESSION['user_id'];
    
    // Check if already enrolled
    if ($enrollmentModel->isEnrolled($user_id, $link['batch_id'])) {
        $errors[] = 'You are already enrolled in this batch';
    } else {
        // Create enrollment
        $enrollmentModel->batch_id = $link['batch_id'];
        $enrollmentModel->user_id = $user_id;
        $enrollmentModel->enrollment_status = 'pending'; // Requires admin approval
        
        if ($enrollmentModel->create()) {
            // Increment link usage
            $linkModel->incrementUsage();
            
            // Update batch enrollment status
            $batchModel->updateEnrollmentStatus();
            
            $success = true;
        } else {
            $errors[] = 'Failed to process enrollment. Please try again.';
        }
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
    <title>Batch Registration - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/file-upload.css">
    <link rel="stylesheet" href="assets/css/plyr.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/full-calendar.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/editor-quill.css">
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
        

        <!-- Main Content -->
        <div class="dashboard-body">
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Batch Registration</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($flash): ?>
                                <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $flash['message']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="text-center py-5">
                                    <i class="ph ph-check-circle text-success-600" style="font-size: 4rem;"></i>
                                    <h2 class="mt-4 mb-3">Registration Submitted!</h2>
                                    <p class="text-secondary-light mb-2">Your enrollment request has been submitted successfully.</p>
                                    <p class="text-secondary-light mb-4">You will receive a confirmation once an administrator approves your enrollment.</p>
                                    <a href="dashboard.php" class="btn btn-main rounded-pill px-24 py-12">
                                        <i class="ph ph-house me-2"></i>Go to Dashboard
                                    </a>
                                </div>
                            <?php elseif (!empty($errors)): ?>
                                <div class="text-center py-5">
                                    <i class="ph ph-warning-circle text-danger-600" style="font-size: 4rem;"></i>
                                    <h2 class="mt-4 mb-3">Registration Error</h2>
                                    <div class="alert alert-danger mt-4 text-start">
                                        <?php foreach ($errors as $error): ?>
                                            <p class="mb-0"><i class="ph ph-x-circle me-2"></i><?php echo htmlspecialchars($error); ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                    <a href="dashboard.php" class="btn btn-outline-main rounded-pill px-24 py-12 mt-3">
                                        <i class="ph ph-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Batch Information Card -->
                                <div class="card bg-neutral-50 border-neutral-200 mb-4">
                                    <div class="card-body">
                                        <h4 class="mb-3"><?php echo htmlspecialchars($batch['batch_name']); ?></h4>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-hash text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Batch Code</p>
                                                        <p class="fw-semibold mb-0"><?php echo htmlspecialchars($batch['batch_code']); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-book-open text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Class/Course</p>
                                                        <p class="fw-semibold mb-0"><?php echo htmlspecialchars($batch['class_name']); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($batch['description']): ?>
                                            <div class="col-12">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-info text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Description</p>
                                                        <p class="mb-0"><?php echo htmlspecialchars($batch['description']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($batch['start_date']): ?>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-calendar text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Start Date</p>
                                                        <p class="fw-semibold mb-0"><?php echo date('F d, Y', strtotime($batch['start_date'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($batch['meeting_schedule']): ?>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-clock text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Schedule</p>
                                                        <p class="fw-semibold mb-0"><?php echo htmlspecialchars($batch['meeting_schedule']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($batch['instructor_name']): ?>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-user text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Instructor</p>
                                                        <p class="fw-semibold mb-0"><?php echo htmlspecialchars($batch['instructor_name']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($batch['max_students']): ?>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start">
                                                    <i class="ph ph-users text-main-600 text-xl me-2 mt-1"></i>
                                                    <div>
                                                        <p class="text-sm text-secondary-light mb-0">Capacity</p>
                                                        <p class="fw-semibold mb-0">
                                                            <?php echo $batch['active_students']; ?> / <?php echo $batch['max_students']; ?> students
                                                            <span class="badge bg-<?php echo $batch['active_students'] >= $batch['max_students'] ? 'danger' : 'success'; ?>-focus text-<?php echo $batch['active_students'] >= $batch['max_students'] ? 'danger' : 'success'; ?>-main text-sm ms-2">
                                                                <?php 
                                                                $remaining = $batch['max_students'] - $batch['active_students'];
                                                                echo $remaining > 0 ? $remaining . ' spots left' : 'Full';
                                                                ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Registration Form -->
                                <form method="POST" class="mt-4">
                                    <div class="alert alert-info-focus bg-info-50 text-info-main border border-info-300 mb-4">
                                        <div class="d-flex align-items-start">
                                            <i class="ph ph-info text-xl me-3 mt-1"></i>
                                            <div>
                                                <p class="mb-0">Your enrollment request will be reviewed by an administrator. You will be notified once your registration is approved.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <a href="dashboard.php" class="btn btn-outline-main rounded-pill px-24 py-12">
                                            <i class="ph ph-arrow-left me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-main rounded-pill px-24 py-12">
                                            <i class="ph ph-check me-2"></i>Register for this Batch
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
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
</body>

</html>