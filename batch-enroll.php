<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';
require_once 'classes/ClassModel.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}
if (!can('enrollments.self_enroll') && !can('enrollments.manage')) {
    permission_denied('courses.php');
}

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$pdo = $database->getConnection();

$batchModel = new Batch($pdo);
$enrollmentModel = new BatchEnrollment($pdo);
$classModel = new ClassModel($pdo);
// For basic info validation
$userModel = new User($pdo);

// Get batch ID from URL
$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;

if ($batch_id <= 0) {
    flash_message('Invalid batch ID.', 'error');
    redirect('courses.php');
}

// Get batch details
$batchModel->id = $batch_id;
$batch = $batchModel->readOne();

if (!$batch) {
    flash_message('Batch not found.', 'error');
    redirect('courses.php');
}

// Get course/class details
$classModel->id = $batch['class_id'];
if (!$classModel->readOne()) {
    flash_message('Course not found.', 'error');
    redirect('courses.php');
}

$course = [
    'id' => $classModel->id,
    'name' => $classModel->class_name,
    'code' => $classModel->class_code,
    'type' => $classModel->type,
    'image' => $classModel->image
];

// Check if batch can accept enrollments
if (!$batchModel->canEnroll()) {
    flash_message('This batch is not accepting enrollments at this time.', 'error');
    redirect('course-detail.php?id=' . $course['id']);
}

// Check if already enrolled
if ($enrollmentModel->isEnrolled($current_user['id'], $batch_id)) {
    flash_message('You are already enrolled in this batch.', 'info');
    redirect('course-detail.php?id=' . $course['id']);
}

$errors = [];
$success = false;

// Fetch fresh user info and check required basic fields
$db_user = $userModel->getById($current_user['id']);
$required_basic = ['country','phone','gender','address'];
$missing_basic = [];
foreach ($required_basic as $f) {
    if (empty($db_user[$f])) { $missing_basic[] = $f; }
}

// Allow updating basic info before enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_basic') {
    $country = trim($_POST['country'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $gender  = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $missing = [];
    if ($country === '') $missing[] = 'country';
    if ($phone === '')   $missing[] = 'phone';
    if ($gender === '')  $missing[] = 'gender';
    if ($address === '') $missing[] = 'address';

    if (!empty($missing)) {
        $errors[] = 'Please provide: '.implode(', ', $missing);
    } else {
        if ($userModel->updateBasicInfo($current_user['id'], [
            'country' => $country,
            'phone' => $phone,
            'gender' => $gender,
            'address' => $address,
            'whatsapp_number' => $phone,
        ])) {
            flash_message('Information updated successfully. You can now complete enrollment.', 'success');
            redirect('batch-enroll.php?batch_id='.(int)$batch_id);
        } else {
            $errors[] = 'Failed to update information. Please try again.';
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'update_basic')) {
    // Create enrollment
    $enrollmentModel->batch_id = $batch_id;
    $enrollmentModel->user_id = $current_user['id'];
    $enrollmentModel->enrollment_status = 'pending'; // Requires admin approval
    $enrollmentModel->notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : '';
    
    if ($enrollmentModel->create()) {
        // Update batch enrollment status if needed
        $batchModel->updateEnrollmentStatus();
        
        flash_message('Your enrollment request has been submitted successfully! You will be notified once approved.', 'success');
        redirect('course-detail.php?id=' . $course['id']);
    } else {
        $errors[] = 'Failed to process enrollment. Please try again.';
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
    <title>Batch Enrollment - <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/file-upload.css">
    <link rel="stylesheet" href="assets/css/plyr.css">
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
        
        <div class="dashboard-body">
            
            <!-- Breadcrumb Start -->
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="courses.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Courses</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><a href="course-detail.php?id=<?php echo $course['id']; ?>" class="text-gray-200 fw-normal text-15 hover-text-main-600">Course Details</a></li>
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">Batch Enrollment</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

            <?php if ($flash): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'info' ? 'info' : 'success'); ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><i class="ph ph-x-circle me-2"></i><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row gy-4">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-main-600">
                            <h4 class="mb-0 text-white">
                                <i class="ph ph-graduation-cap me-8"></i>Enroll in Batch
                            </h4>
                        </div>
                        <div class="card-body">

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $e): ?>
                                            <li><?php echo htmlspecialchars($e); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($missing_basic)): ?>
                                <div class="alert alert-warning d-flex align-items-center mb-16" role="alert">
                                    <i class="ph ph-warning-circle me-12" style="font-size: 24px;"></i>
                                    <div>
                                        <strong>Complete Your Information</strong>
                                        <p class="mb-0">Please provide your Country, Phone, Gender, and Address before enrolling.</p>
                                    </div>
                                </div>

                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_basic">
                                    <div class="row g-3 mb-24">
                                        <div class="col-md-6">
                                            <label class="form-label">Country <span class="text-danger">*</span></label>
                                            <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($db_user['country'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone / WhatsApp <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($db_user['phone'] ?? ($db_user['whatsapp_number'] ?? '')); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select name="gender" class="form-select" required>
                                                <?php $g = $db_user['gender'] ?? ''; ?>
                                                <option value="">Select</option>
                                                <option value="Male" <?php echo ($g==='Male')?'selected':''; ?>>Male</option>
                                                <option value="Female" <?php echo ($g==='Female')?'selected':''; ?>>Female</option>
                                                <option value="Other" <?php echo ($g==='Other')?'selected':''; ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($db_user['address'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-main">Save & Continue</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                            
                            <!-- Course Information -->
                            <div class="bg-main-50 p-16 rounded-8 mb-24">
                                <div class="flex-align gap-16">
                                    <?php if ($course['image']): ?>
                                        <img src="uploads/classes/<?php echo htmlspecialchars($course['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($course['name']); ?>" 
                                             class="w-80 h-80 object-fit-cover rounded-8">
                                    <?php else: ?>
                                        <div class="w-80 h-80 bg-main-100 rounded-8 flex-center">
                                            <i class="ph ph-book-open text-main-600" style="font-size: 2rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h5 class="mb-4"><?php echo htmlspecialchars($course['name']); ?></h5>
                                        <p class="text-sm text-gray-600 mb-0">
                                            <i class="ph ph-hash"></i> <?php echo htmlspecialchars($course['code']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Batch Information -->
                            <div class="border border-gray-100 p-20 rounded-8 mb-24">
                                <h6 class="mb-16 pb-16 border-bottom border-gray-100">Batch Details</h6>
                                
                                <div class="row g-16">
                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-flag text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Batch Name</span>
                                                <span class="text-gray-900 fw-medium"><?php echo htmlspecialchars($batch['batch_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-identification-badge text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Batch Code</span>
                                                <span class="text-gray-900 fw-medium"><?php echo htmlspecialchars($batch['batch_code']); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($batch['description']): ?>
                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-info text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Description</span>
                                                <span class="text-gray-900"><?php echo htmlspecialchars($batch['description']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($batch['start_date']): ?>
                                    <div class="col-md-6">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-calendar-check text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Start Date</span>
                                                <span class="text-gray-900"><?php echo date('F d, Y', strtotime($batch['start_date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($batch['end_date']): ?>
                                    <div class="col-md-6">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-calendar-x text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">End Date</span>
                                                <span class="text-gray-900"><?php echo date('F d, Y', strtotime($batch['end_date'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($batch['meeting_schedule']): ?>
                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-clock text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Meeting Schedule</span>
                                                <span class="text-gray-900"><?php echo htmlspecialchars($batch['meeting_schedule']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($batch['instructor_name']): ?>
                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-chalkboard-teacher text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Instructor</span>
                                                <span class="text-gray-900"><?php echo htmlspecialchars($batch['instructor_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($batch['max_students']): ?>
                                    <div class="col-12">
                                        <div class="flex-align gap-12">
                                            <i class="ph ph-users text-main-600 text-xl"></i>
                                            <div>
                                                <span class="text-sm text-gray-500 d-block">Enrollment</span>
                                                <span class="text-gray-900">
                                                    <?php echo $batch['active_students']; ?> / <?php echo $batch['max_students']; ?> students enrolled
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Enrollment Form -->
                            <?php if (empty($missing_basic)): ?>
                            <form method="POST" action="">
                                <div class="mb-24">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="4" 
                                              placeholder="Add any questions or special requests here..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                                    <small class="text-gray-500">Let us know if you have any specific requirements or questions about the batch.</small>
                                </div>

                                <div class="alert alert-info mb-24">
                                    <div class="flex-align gap-12">
                                        <i class="ph ph-info text-xl"></i>
                                        <div>
                                            <strong>Important:</strong> Your enrollment request will be reviewed by an administrator. 
                                            You'll receive a notification once your request is approved.
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-align gap-12">
                                    <button type="submit" class="btn btn-main rounded-pill px-32 py-11">
                                        <i class="ph ph-check-circle me-8"></i>Submit Enrollment Request
                                    </button>
                                    <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-main rounded-pill px-32 py-11">
                                        <i class="ph ph-arrow-left me-8"></i>Back to Course
                                    </a>
                                </div>
                            </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
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

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/boostrap.bundle.min.js"></script>
    <script src="assets/js/phosphor-icon.js"></script>
    <script src="assets/js/file-upload.js"></script>
    <script src="assets/js/plyr.js"></script>
    <script src="assets/js/full-calendar.js"></script>
    <script src="assets/js/jquery-ui.js"></script>
    <script src="assets/js/editor-quill.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
