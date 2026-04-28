<?php
// session_start();

require_once 'config/database.php';
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Role.php';
require_once 'classes/Organization.php';
require_once 'classes/School.php';
require_once 'classes/PendingUser.php';
require_once 'classes/ClassModel.php';

// Check if user is logged in and has permission
require_roles(['super_admin']);

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();

$user = new User($db);
$role = new Role($db);
$organization = new Organization($db);
$school = new School($db);
$pendingUser = new PendingUser($db);
$classModel = new ClassModel($db);

$message = '';
$error = '';

// Handle actions (approve/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pending_user_id = filter_input(INPUT_POST, 'pending_user_id', FILTER_SANITIZE_NUMBER_INT);
    $admin_notes = filter_input(INPUT_POST, 'admin_notes', FILTER_SANITIZE_STRING);

    if ($_POST['action'] === 'approve') {
        if ($pendingUser->approve($pending_user_id, $current_user['id'], $admin_notes)) {
            $message = 'User registration approved and moved to active users.';
        } else {
            $error = 'Failed to approve user registration.';
        }
    } elseif ($_POST['action'] === 'reject') {
        if ($pendingUser->reject($pending_user_id, $current_user['id'], $admin_notes)) {
            $message = 'User registration rejected.';
        } else {
            $error = 'Failed to reject user registration.';
        }
    }
}

$pending_registrations = $pendingUser->getAllPending();

// Function to get class name by ID
function getClassName($classModel, $class_id) {
    $classModel->id = $class_id;
    if ($classModel->readOne([])) {
        return $classModel->class_name . ' (' . $classModel->class_code . ')';
    }
    return 'Class ID: ' . $class_id;
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
    <title>Pending Registrations - <?php echo APP_NAME; ?></title>
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
            <!-- Breadcrumb Start -->
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">Pending Registrations</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

            <?php if ($flash): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
                    <i class="ph ph-check-circle me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
                    <i class="ph ph-warning-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Header Card Start -->
            <div class="card mb-24">
                <div class="card-body">
                    <div class="flex-between flex-wrap gap-8">
                        <div>
                            <h4 class="mb-8">Pending User Registrations</h4>
                            <p class="text-gray-600 mb-0">Review and approve user registration requests</p>
                        </div>
                        <div class="flex-align gap-16">
                            <div class="text-center">
                                <span class="w-44 h-44 bg-warning-50 text-warning-600 rounded-circle flex-center text-2xl">
                                    <i class="ph ph-clock"></i>
                                </span>
                                <div class="mt-8">
                                    <span class="h5 mb-0 text-warning-600"><?php echo count($pending_registrations); ?></span>
                                    <div class="text-13 text-gray-600">Pending</div>
                                </div>
                            </div>
                            <a href="register.php?admin_create=true" class="btn btn-main text-sm btn-sm px-24 rounded-pill">
                                <i class="ph ph-plus me-2"></i>
                                Add New User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header Card End -->

            <?php if (empty($pending_registrations)): ?>
                <!-- Empty State Start -->
                <div class="card">
                    <div class="card-body text-center py-80">
                        <div class="mb-24">
                            <span class="w-80 h-80 bg-main-50 text-main-600 rounded-circle flex-center text-4xl mx-auto">
                                <i class="ph ph-inbox"></i>
                            </span>
                        </div>
                        <h4 class="text-gray-500 mb-8">No Pending Registrations</h4>
                        <p class="text-gray-400 mb-24">There are no pending user registrations at the moment.</p>
                        <a href="register.php?admin_create=true" class="btn btn-main rounded-pill">
                            <i class="ph ph-plus me-2"></i>Add New User
                        </a>
                    </div>
                </div>
                <!-- Empty State End -->
            <?php else: ?>
                <!-- Registration Cards Start -->
                <div class="row gy-24">
                    <?php foreach ($pending_registrations as $reg): ?>
                        <div class="col-xxl-4 col-lg-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <!-- Header -->
                                    <div class="flex-between flex-wrap gap-8 mb-20">
                                        <div class="flex-align gap-12">
                                            <span class="w-44 h-44 bg-main-50 text-main-600 rounded-circle flex-center text-xl">
                                                <i class="ph ph-user"></i>
                                            </span>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($reg['full_name']); ?></h6>
                                                <span class="text-13 text-gray-400"><?php echo htmlspecialchars($reg['username']); ?></span>
                                            </div>
                                        </div>
                                        <span class="text-13 py-2 px-8 bg-warning-50 text-warning-600 rounded-pill fw-medium">
                                            <i class="ph ph-clock me-1"></i>Pending
                                        </span>
                                    </div>
                                    
                                    <!-- User Details -->
                                    <div class="mb-20">
                                        <div class="row g-12">
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-envelope text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo htmlspecialchars($reg['email']); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($reg['phone']): ?>
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-phone text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo htmlspecialchars($reg['phone']); ?></span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-user-tag text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo htmlspecialchars($reg['role_name'] ?? 'Solo Student'); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($reg['organization_name']): ?>
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-buildings text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo htmlspecialchars($reg['organization_name']); ?></span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($reg['school_name']): ?>
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-graduation-cap text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo htmlspecialchars($reg['school_name']); ?></span>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-12">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-calendar text-gray-400 text-15"></i>
                                                    <span class="text-13 text-gray-600"><?php echo date('M d, Y H:i', strtotime($reg['submission_timestamp'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Selected Classes -->
                                    <?php 
                                    $selected_classes = json_decode($reg['selected_classes'], true);
                                    if (!empty($selected_classes)): 
                                    ?>
                                        <div class="mb-20">
                                            <h6 class="text-15 mb-8">Selected Classes:</h6>
                                            <div class="flex-align gap-4 flex-wrap">
                                                <?php foreach ($selected_classes as $class_id): ?>
                                                    <span class="text-13 py-2 px-8 bg-info-50 text-info-600 rounded-pill">
                                                        <?php echo getClassName($classModel, $class_id); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Additional Info Collapsible -->
                                    <div class="mb-20">
                                        <button class="btn btn-outline-main btn-sm w-100 rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#details<?php echo $reg['id']; ?>" aria-expanded="false">
                                            <i class="ph ph-info me-2"></i>View More Details
                                        </button>
                                        <div class="collapse mt-16" id="details<?php echo $reg['id']; ?>">
                                            <div class="border border-gray-100 rounded-8 p-16 bg-main-25">
                                                <div class="row g-12">
                                                    <?php if ($reg['gender']): ?>
                                                    <div class="col-6">
                                                        <span class="text-13 text-gray-500">Gender:</span>
                                                        <div class="text-13 fw-medium"><?php echo htmlspecialchars($reg['gender']); ?></div>
                                                    </div>
                                                    <?php endif; ?>
                                                    <?php if ($reg['address']): ?>
                                                    <div class="col-12">
                                                        <span class="text-13 text-gray-500">Address:</span>
                                                        <div class="text-13 fw-medium"><?php echo htmlspecialchars($reg['address']); ?></div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex-align gap-8">
                                        <button type="button" class="btn btn-success rounded-pill py-9 flex-grow-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#actionModal"
                                                onclick="setActionModal('approve', <?php echo $reg['id']; ?>, '<?php echo htmlspecialchars($reg['full_name']); ?>')">
                                            <i class="ph ph-check me-2"></i>Approve
                                        </button>
                                        <button type="button" class="btn btn-danger rounded-pill py-9 flex-grow-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#actionModal"
                                                onclick="setActionModal('reject', <?php echo $reg['id']; ?>, '<?php echo htmlspecialchars($reg['full_name']); ?>')">
                                            <i class="ph ph-x me-2"></i>Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Registration Cards End -->
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

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-main-50 border-bottom border-gray-100">
                    <h5 class="modal-title" id="modalTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <form method="POST" id="actionForm">
                        <input type="hidden" name="pending_user_id" id="modalUserId">
                        <input type="hidden" name="action" id="modalAction">
                        
                        <div class="alert alert-info border-0 mb-20" id="modalMessage">
                            <!-- Message will be set by JavaScript -->
                        </div>
                        
                        <div class="mb-20">
                            <label for="admin_notes" class="form-label h6 mb-8">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control py-11" rows="4" 
                                      placeholder="Add any notes about this decision..."></textarea>
                        </div>
                        
                        <div class="flex-align justify-content-end gap-8">
                            <button type="button" class="btn btn-outline-gray rounded-pill py-9" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn rounded-pill py-9" id="modalSubmitBtn">Confirm</button>
                        </div>
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
        function setActionModal(action, userId, userName) {
            document.getElementById('modalUserId').value = userId;
            document.getElementById('modalAction').value = action;
            document.getElementById('admin_notes').value = '';
            
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const submitBtn = document.getElementById('modalSubmitBtn');
            
            if (action === 'approve') {
                modalTitle.textContent = 'Approve Registration';
                modalMessage.innerHTML = `<i class="ph ph-check-circle me-2 text-success-600"></i>Are you sure you want to <strong>approve</strong> the registration for <strong>${userName}</strong>? This will create an active user account.`;
                modalMessage.className = 'alert alert-success border-0 mb-20';
                submitBtn.innerHTML = '<i class="ph ph-check me-2"></i>Approve User';
                submitBtn.className = 'btn btn-success rounded-pill py-9';
            } else {
                modalTitle.textContent = 'Reject Registration';
                modalMessage.innerHTML = `<i class="ph ph-x-circle me-2 text-warning-600"></i>Are you sure you want to <strong>reject</strong> the registration for <strong>${userName}</strong>? This action cannot be undone.`;
                modalMessage.className = 'alert alert-warning border-0 mb-20';
                submitBtn.innerHTML = '<i class="ph ph-x me-2"></i>Reject User';
                submitBtn.className = 'btn btn-danger rounded-pill py-9';
            }
        }
    </script>
</body>
</html>