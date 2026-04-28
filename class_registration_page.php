<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'classes/ClassAccess.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';
$user = new User($db);
$classModel = new ClassModel($db);
$classAccess = new ClassAccess($db);

// Only solo students can access this page
if ($user_role !== 'solo_student') {
    redirect('dashboard.php');
}

// Check if student already has classes assigned
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
if (!empty($accessible_classes_raw)) {
    // Student already has classes, redirect to dashboard
    flash_message('You already have classes assigned. View them in your dashboard.', 'info');
    redirect('dashboard.php');
}

$message = '';
$error = '';

// Handle class registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register_classes') {
        if (isset($_POST['selected_classes']) && !empty($_POST['selected_classes'])) {
            $selected_classes = $_POST['selected_classes'];
            $success_count = 0;
            
            foreach ($selected_classes as $class_id) {
                if ($classAccess->assignAccess($current_user['id'], $class_id, $current_user['id'])) {
                    $success_count++;
                }
            }
            
            if ($success_count > 0) {
                flash_message("Successfully registered for $success_count classes! You can now access your content.", 'success');
                redirect('dashboard.php');
            } else {
                $error = 'Failed to register for classes. Please try again.';
            }
        } else {
            $error = 'Please select at least one class to register.';
        }
    }
}

// Get all available classes
$stmt = $classModel->read([]);
$all_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get classes with additional info (subjects count, etc.)
$classes_with_info = [];
foreach ($all_classes as $class) {
    // Get subjects count for this class
    $query = "SELECT COUNT(*) as subject_count FROM subjects WHERE class_id = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$class['id']]);
    $subject_data = $stmt->fetch();
    
    // Get syllabi count for this class
    $query = "SELECT COUNT(sy.id) as syllabi_count 
              FROM syllabi sy 
              JOIN subjects s ON sy.subject_id = s.id 
              WHERE s.class_id = ? AND sy.status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$class['id']]);
    $syllabi_data = $stmt->fetch();
    
    // Get lectures count for this class
    $query = "SELECT COUNT(l.id) as lecture_count 
              FROM lectures l 
              JOIN syllabi sy ON l.syllabus_id = sy.id
              JOIN subjects s ON sy.subject_id = s.id 
              WHERE s.class_id = ? AND l.status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$class['id']]);
    $lecture_data = $stmt->fetch();
    
    $class['subject_count'] = $subject_data['subject_count'] ?? 0;
    $class['syllabi_count'] = $syllabi_data['syllabi_count'] ?? 0;
    $class['lecture_count'] = $lecture_data['lecture_count'] ?? 0;
    
    $classes_with_info[] = $class;
}

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>Class Registration - <?php echo APP_NAME; ?></title>
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
            
            <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
                <!-- Breadcrumb Start -->
                <div class="breadcrumb mb-24">
                    <ul class="flex-align gap-4">
                        <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                        <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                        <li><span class="text-main-600 fw-normal text-15">Class Registration</span></li>
                    </ul>
                </div>
                <!-- Breadcrumb End -->
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show mb-24" role="alert">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Welcome Message -->
            <div class="card mb-24 bg-gradient-to-r from-main-600 to-purple-600 text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-3">Welcome to Class Registration!</h3>
                            <p class="text-white-200 mb-3">Choose from <?php echo count($classes_with_info); ?> available classes and start your learning journey. You can select multiple classes that interest you.</p>
                            <div class="flex-align gap-3">
                                <span class="text-13 py-2 px-3 bg-white-10 rounded-pill">
                                    <i class="ph ph-graduation-cap me-1"></i><?php echo count($classes_with_info); ?> Classes Available
                                </span>
                                <span class="text-13 py-2 px-3 bg-white-10 rounded-pill">
                                    <i class="ph ph-check-circle me-1"></i>Free Registration
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="ph ph-student text-white" style="font-size: 5rem; opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <form method="POST" id="registrationForm">
                <input type="hidden" name="action" value="register_classes">
                
                <div class="card">
                    <div class="card-header">
                        <div class="flex-between align-items-center">
                            <h5 class="mb-0">Select Classes to Register</h5>
                            <div class="flex-align gap-3">
                                <button type="button" class="btn btn-outline-main btn-sm" id="selectAllBtn">
                                    <i class="ph ph-check-square me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllBtn">
                                    <i class="ph ph-x-square me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($classes_with_info)): ?>
                            <div class="text-center py-5">
                                <i class="ph ph-graduation-cap text-gray-400" style="font-size: 4rem;"></i>
                                <h5 class="text-gray-500 mt-3">No Classes Available</h5>
                                <p class="text-gray-400">There are currently no classes available for registration. Please check back later.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($classes_with_info as $class): ?>
                                <div class="col-lg-6 col-xl-4">
                                    <div class="class-card border rounded-12 overflow-hidden h-100 hover-shadow-lg transition-2" data-class-id="<?php echo $class['id']; ?>">
                                        <div class="class-card-header bg-main-50 p-4">
                                            <div class="flex-between align-items-start mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input class-checkbox" type="checkbox" 
                                                           name="selected_classes[]" value="<?php echo $class['id']; ?>" 
                                                           id="class_<?php echo $class['id']; ?>">
                                                    <label class="form-check-label fw-semibold text-main-600" for="class_<?php echo $class['id']; ?>">
                                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                                    </label>
                                                </div>
                                                <span class="text-12 text-gray-500 bg-white px-2 py-1 rounded-pill">
                                                    <?php echo htmlspecialchars($class['class_code']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4">
                                            <p class="text-gray-600 text-14 mb-3 line-clamp-3">
                                                <?php echo htmlspecialchars($class['description'] ?: 'No description available'); ?>
                                            </p>
                                            
                                            <div class="row g-2 mb-3">
                                                <div class="col-4">
                                                    <div class="text-center p-2 bg-gray-50 rounded-8">
                                                        <div class="text-main-600 text-18 mb-1">
                                                            <i class="ph ph-book-open"></i>
                                                        </div>
                                                        <div class="text-12 text-gray-500">Subjects</div>
                                                        <div class="text-15 fw-medium"><?php echo $class['subject_count']; ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-center p-2 bg-gray-50 rounded-8">
                                                        <div class="text-main-600 text-18 mb-1">
                                                            <i class="ph ph-list"></i>
                                                        </div>
                                                        <div class="text-12 text-gray-500">Syllabi</div>
                                                        <div class="text-15 fw-medium"><?php echo $class['syllabi_count']; ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-center p-2 bg-gray-50 rounded-8">
                                                        <div class="text-main-600 text-18 mb-1">
                                                            <i class="ph ph-play-circle"></i>
                                                        </div>
                                                        <div class="text-12 text-gray-500">Lectures</div>
                                                        <div class="text-15 fw-medium"><?php echo $class['lecture_count']; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center">
                                                <small class="text-gray-500">
                                                    Created on <?php echo date('M j, Y', strtotime($class['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($classes_with_info)): ?>
                    <div class="card-footer bg-gray-50">
                        <div class="flex-between align-items-center">
                            <div>
                                <span class="text-gray-600">Selected Classes: </span>
                                <span class="fw-semibold text-main-600" id="selectedCount">0</span>
                            </div>
                            <div class="flex-align gap-3">
                                <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="ph ph-arrow-left me-2"></i>Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-main rounded-pill px-4" id="registerBtn" disabled>
                                    <i class="ph ph-graduation-cap me-2"></i>Register for Selected Classes
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
            
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
        $(document).ready(function() {
            // Update selected count and button state
            function updateSelectionState() {
                const selectedCount = $('.class-checkbox:checked').length;
                $('#selectedCount').text(selectedCount);
                $('#registerBtn').prop('disabled', selectedCount === 0);
                
                // Update select all button text
                const totalClasses = $('.class-checkbox').length;
                if (selectedCount === totalClasses && totalClasses > 0) {
                    $('#selectAllBtn').html('<i class="ph ph-check-square me-1"></i>All Selected');
                } else {
                    $('#selectAllBtn').html('<i class="ph ph-check-square me-1"></i>Select All');
                }
            }

            // Handle individual checkbox changes
            $('.class-checkbox').on('change', function() {
                const classCard = $(this).closest('.class-card');
                if ($(this).is(':checked')) {
                    classCard.addClass('border-main-600 bg-main-25');
                } else {
                    classCard.removeClass('border-main-600 bg-main-25');
                }
                updateSelectionState();
            });

            // Handle select all button
            $('#selectAllBtn').on('click', function() {
                const allChecked = $('.class-checkbox:checked').length === $('.class-checkbox').length;
                $('.class-checkbox').prop('checked', !allChecked).trigger('change');
            });

            // Handle clear all button
            $('#clearAllBtn').on('click', function() {
                $('.class-checkbox').prop('checked', false).trigger('change');
            });

            // Handle class card clicks (but not on checkbox)
            $('.class-card').on('click', function(e) {
                if (!$(e.target).is('.class-checkbox') && !$(e.target).is('.form-check-label')) {
                    const checkbox = $(this).find('.class-checkbox');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });

            // Form validation before submit
            $('#registrationForm').on('submit', function(e) {
                const selectedCount = $('.class-checkbox:checked').length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one class to register.');
                    return false;
                }
                
                // Show confirmation
                const classNames = $('.class-checkbox:checked').map(function() {
                    return $(this).next('label').text().trim();
                }).get().join(', ');
                
                return confirm(`Are you sure you want to register for ${selectedCount} class(es):\n\n${classNames}`);
            });

            // Initialize state
            updateSelectionState();
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

    <style>
        .class-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .class-card.border-main-600 {
            border-color: #3D7FF9 !important;
            background-color: rgba(61, 127, 249, 0.05);
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .bg-main-25 {
            background-color: rgba(61, 127, 249, 0.05);
        }
        
        .hover-shadow-lg:hover {
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .transition-2 {
            transition: all 0.2s ease;
        }
        
        .text-white-200 {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .bg-white-10 {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>

</body>
</html>