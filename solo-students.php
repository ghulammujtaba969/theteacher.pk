<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'classes/ClassAccess.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';

// Check if user is logged in
require_permission('solo_students.view', 'dashboard.php');

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$user = new User($db);
$classModel = new ClassModel($db);
$classAccess = new ClassAccess($db);
$batchModel = new Batch($db);
$batchEnrollment = new BatchEnrollment($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'assign_classes':
                if (!can('solo_students.manage')) permission_denied('solo-students.php');
                if (isset($_POST['selected_students']) && isset($_POST['selected_classes'])) {
                    $selected_students = (array)$_POST['selected_students'];
                    $selected_classes = (array)$_POST['selected_classes'];
                    $selected_batches = (array)($_POST['selected_batches'] ?? []); // [class_id => batch_id]
                    $enroll_status = strtolower(trim($_POST['enroll_status'] ?? 'active'));
                    $allowed_status = ['pending','active'];
                    if (!in_array($enroll_status, $allowed_status, true)) { $enroll_status = 'active'; }
                    $success_access = 0; $total_access = 0;
                    $success_enroll = 0; $total_enroll = 0;

                    foreach ($selected_students as $student_id) {
                        $student_id = (int)$student_id;
                        foreach ($selected_classes as $class_id) {
                            $class_id = (int)$class_id;
                            // Always assign class access
                            $total_access++;
                            if ($classAccess->assignAccess($student_id, $class_id, $current_user['id'])) {
                                $success_access++;
                            }
                            // Optional: enroll to selected batch if provided
                            $batch_id = (int)($selected_batches[$class_id] ?? 0);
                            if ($batch_id > 0) {
                                $total_enroll++;
                                if (!$batchEnrollment->isEnrolled($student_id, $batch_id)) {
                                    $batchEnrollment->batch_id = $batch_id;
                                    $batchEnrollment->user_id = $student_id;
                                    $batchEnrollment->enrollment_status = $enroll_status;
                                    if ($batchEnrollment->create()) {
                                        $success_enroll++;
                                    }
                                } else {
                                    // already enrolled counts as success for user feedback
                                    $success_enroll++;
                                }
                            }
                        }
                    }

                    if ($success_access > 0) {
                        $msg = "Assigned access $success_access/$total_access";
                        if ($total_enroll > 0) { $msg .= ", batch enrollments $success_enroll/$total_enroll"; }
                        $message = $msg . ' (status: ' . ucfirst($enroll_status) . ').';
                    } else {
                        $error = 'Failed to assign classes. Please try again.';
                    }
                } else {
                    $error = 'Please select both students and classes to assign.';
                }
                break;
                
            case 'remove_class_access':
                if (!can('solo_students.manage')) permission_denied('solo-students.php');
                $student_id = $_POST['student_id'];
                $class_id = $_POST['class_id'];
                
                if ($classAccess->revokeAccess($student_id, $class_id)) {
                    $message = 'Class access removed successfully!';
                } else {
                    $error = 'Failed to remove class access. Please try again.';
                }
                break;
        }
    }
}

// Get solo students (role_id = 5, organization_id = null, school_id = null)
function getSoloStudents($db, $current_user) {
    // Base query: Solo Student role (role_id = 5), active, not all-access
    $query = "SELECT u.*, 
              GROUP_CONCAT(CONCAT(c.class_name, ' (', c.class_code, ')') SEPARATOR ', ') as assigned_classes,
              COUNT(ucp.class_id) as class_count
              FROM users u 
              LEFT JOIN user_class_permissions ucp ON u.id = ucp.user_id
              LEFT JOIN classes c ON ucp.class_id = c.id AND c.status = 'active'
              WHERE u.role_id = 5 
              AND u.status = 'active'
              AND u.can_access_all_classes = 0";

    $params = [];
    $roleName = $current_user['role_name'] ?? '';

    if ($roleName === 'Organization Admin') {
        // Org Admin sees: students they created OR whose school belongs to their organization
        $query .= " AND (u.created_by = :uid OR u.school_id IN (SELECT id FROM schools WHERE organization_id = :org_id))";
        $params[':uid'] = (int)($current_user['id'] ?? 0);
        $params[':org_id'] = (int)($current_user['organization_id'] ?? 0);
    } elseif ($roleName === 'School Admin') {
        // School Admin sees: students in their school
        $query .= " AND u.school_id = :school_id";
        $params[':school_id'] = (int)($current_user['school_id'] ?? 0);
    } else {
        // Super Admin — no extra filter
    }

    $query .= " GROUP BY u.id ORDER BY u.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$solo_students = getSoloStudents($db, $current_user);
// Pagination for solo students
$per_page = isset($_GET['pp']) ? max(5, min(100, (int)$_GET['pp'])) : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total = is_array($solo_students) ? count($solo_students) : 0;
$pages = max(1, (int)ceil($total / $per_page));
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $per_page;
$paged_solo_students = array_slice($solo_students, $offset, $per_page);

// Get all active classes for assignment
$stmt = $classModel->read([]);
$all_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Preload batches per class for the assign UI
$batches_by_class = [];
foreach ($all_classes as $cls) {
    $batches_by_class[$cls['id']] = $batchModel->getBatchesByClass($cls['id']);
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
    <title>Solo Students Management - <?php echo APP_NAME; ?></title>
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
                        <li><span class="text-main-600 fw-normal text-15">Solo Students</span></li>
                    </ul>
                </div>
                <!-- Breadcrumb End -->

                <!-- Breadcrumb Right Start -->
                <div class="flex-align gap-8 flex-wrap">
                    <button class="btn btn-main text-sm btn-sm px-24 rounded-pill py-12 d-flex align-items-center gap-2" id="assignClassesBtn" style="display: none;" data-bs-toggle="modal" data-bs-target="#assignClassModal">
                        <i class="ph ph-graduation-cap me-4"></i>
                        Assign Classes 
                    </button>
                    <div class="position-relative text-gray-500 flex-align gap-4 text-13">
                        <span class="text-inherit">Filter by: </span>
                        <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                            <span class="text-lg"><i class="ph ph-funnel-simple"></i></span>
                            <select class="form-control ps-8 pe-20 py-16 border-0 text-inherit rounded-4 text-center" id="classFilter">
                                <option value="">All Students</option>
                                <option value="with_classes">With Classes</option>
                                <option value="without_classes">Without Classes</option>
                            </select>
                        </div>
                    </div>
                    <div class="position-relative text-gray-500 flex-align gap-4 text-13">
                        <span class="text-inherit">Sort by: </span>
                        <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                            <span class="text-lg"><i class="ph ph-sort-ascending"></i></span>
                            <select class="form-control ps-8 pe-20 py-16 border-0 text-inherit rounded-4 text-center">
                                <option value="1" selected>Latest</option>
                                <option value="1">Name</option>
                                <option value="1">Email</option>
                                <option value="1">Classes Count</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                        <span class="text-lg"><i class="ph ph-layout"></i></span>
                        <select class="form-control ps-8 pe-20 py-16 border-0 text-inherit rounded-4 text-center" id="exportOptions">
                            <option value="" selected disabled>Export</option>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                </div>
                <!-- Breadcrumb Right End -->
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

            <div class="card overflow-hidden">
                <div class="card-body p-0 overflow-x-auto">
                    <table id="soloStudentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="h6 text-gray-300">Student</th>
                                <th class="h6 text-gray-300">Email</th>
                                <th class="h6 text-gray-300">Whatsapp/Phone</th>
                                <th class="h6 text-gray-300">Assigned Classes</th>
                                <th class="h6 text-gray-300">Classes Count</th>
                                <th class="h6 text-gray-300">Joined Date</th>
                                <th class="h6 text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paged_solo_students as $student): ?>
                            <tr data-class-count="<?php echo $student['class_count']; ?>">
                                <td class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4 student-checkbox" type="checkbox" value="<?php echo $student['id']; ?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-align gap-8">
                                        
                                        <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($student['full_name'] ?? $student['username']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($student['email']); ?></span>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($student['phone'] ?? "N/A"); ?></span>
                                </td>
                                <td>
                                    <div class="assigned-classes-cell" style="max-width: 300px;">
                                        <?php if ($student['assigned_classes']): ?>
                                            <span class="text-13 text-success-600"><?php echo htmlspecialchars($student['assigned_classes']); ?></span>
                                        <?php else: ?>
                                            <span class="text-13 text-gray-400 fst-italic">No classes assigned</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($student['class_count'] > 0): ?>
                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                            <?php echo $student['class_count']; ?> classes
                                        </span>
                                    <?php else: ?>
                                        <span class="text-13 py-2 px-8 bg-warning-50 text-warning-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <span class="w-6 h-6 bg-warning-600 rounded-circle flex-shrink-0"></span>
                                            0 classes
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo date('M j, Y', strtotime($student['created_at'])); ?></span>
                                </td>
                                <td>
                                    <div class="flex-align gap-8">
                                        <button class="bg-main-50 text-main-600 py-2 px-14 rounded-pill hover-bg-main-600 hover-text-white text-sm" onclick="viewStudentClasses(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['full_name'] ?? $student['username']); ?>')">
                                            <i class="ph ph-eye me-4"></i>View Classes
                                        </button>
                                        <?php if ($student['class_count'] > 0): ?>
                                        <button class="bg-warning-50 text-warning-600 py-2 px-14 rounded-pill hover-bg-warning-600 hover-text-white text-sm" onclick="manageStudentClasses(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['full_name'] ?? $student['username']); ?>')">
                                            <i class="ph ph-gear me-4"></i>Manage
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer flex-between flex-wrap">
                    <?php 
                        $start_i = $total ? ($offset + 1) : 0; 
                        $end_i = min($offset + $per_page, $total);
                        $qs = $_GET; unset($qs['page']); unset($qs['pp']);
                        $base = basename($_SERVER['PHP_SELF']) . (empty($qs) ? '' : ('?' . http_build_query($qs)));
                        $build = function($p,$pp) use($base){ return $base . (strpos($base,'?')!==false? '&':'?') . 'page=' . $p . '&pp=' . $pp; };
                    ?>
                    <span class="text-gray-900">Showing <?php echo $start_i; ?> to <?php echo $end_i; ?> of <?php echo $total; ?> solo students</span>
                    <ul class="pagination flex-align flex-wrap">
                        <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page>1? $build($page-1,$per_page):'#'; ?>">Prev</a>
                        </li>
                        <?php for ($p=1; $p<=$pages; $p++): ?>
                            <li class="page-item <?php echo $p==$page?'active':''; ?>">
                                <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $build($p,$per_page); ?>"><?php echo $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page>=$pages?'disabled':''; ?>">
                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page<$pages? $build($page+1,$per_page):'#'; ?>">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
            
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

    <!-- Assign Classes Modal -->
    <div class="modal fade" id="assignClassModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-elegant rounded-12 border-0 box-shadow-custom">
                <form method="POST" id="assignClassForm">
                    <div class="modal-header border-0 pb-0 px-16 pt-16">
                        <div class="flex-align gap-10">
                            <span class="w-40 h-40 rounded-circle flex-center bg-main-50 text-main-600 text-xl"><i class="ph ph-graduation-cap"></i></span>
                            <div>
                                <h5 class="modal-title mb-2">Assign Classes to Students</h5>
                                <p class="text-13 text-gray-500 mb-0">Grant class access and optionally enroll in a batch.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-12 px-16" style="max-height: 65vh; overflow-y: auto;">
                        <input type="hidden" name="action" value="assign_classes">
                        
                        <div class="mb-16">
                            <label class="form-label fw-semibold">Selected Students</label>
                            <div id="selectedStudentsList" class="rounded-12 p-12 bg-main-50 border border-main-100"></div>
                        </div>
                        
                        <div class="mb-16">
                            <label class="form-label fw-semibold">Enrollment Status for Selected Batches</label>
                            <select name="enroll_status" class="form-select w-auto d-inline-block">
                                <option value="active" selected>Active</option>
                                <option value="pending">Pending</option>
                            </select>
                            <span class="text-13 text-gray-500 ms-8">Applies only when a batch is selected.</span>
                        </div>
                        
                        <div class="mb-8">
                            <label class="form-label fw-semibold">Select Classes to Assign</label>
                            <div class="row g-12">
                                <?php foreach ($all_classes as $class): ?>
                                <div class="col-md-6">
                                    <div class="border border-gray-100 rounded-12 p-12 bg-white">
                                        <div class="form-check">
                                            <input class="form-check-input class-check" type="checkbox" name="selected_classes[]" value="<?php echo $class['id']; ?>" id="class_<?php echo $class['id']; ?>" data-class-id="<?php echo $class['id']; ?>">
                                            <label class="form-check-label" for="class_<?php echo $class['id']; ?>">
                                                <?php echo htmlspecialchars($class['class_name']); ?> (<?php echo htmlspecialchars($class['class_code']); ?>)
                                            </label>
                                        </div>
                                        <?php $batches = $batches_by_class[$class['id']] ?? []; if (!empty($batches)): ?>
                                            <div class="mt-10 ms-4">
                                                <label class="text-13 text-gray-500 d-block mb-6">Optional: Select Batch</label>
                                                <select name="selected_batches[<?php echo $class['id']; ?>]" id="batch_select_<?php echo $class['id']; ?>" class="form-select form-select-sm rounded-pill" disabled>
                                                    <option value="">No batch (grant access only)</option>
                                                    <?php foreach ($batches as $b): ?>
                                                        <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['batch_name']); ?> (<?php echo htmlspecialchars($b['batch_code']); ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-16 pb-16 flex-between flex-wrap gap-12">
                        <div class="text-13 text-gray-500">Tip: You can pick batches per class where available.</div>
                        <div class="flex-align gap-8">
                            <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-main rounded-pill py-9">Assign Classes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Classes Management Modal -->
    <div class="modal fade" id="studentClassesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-elegant rounded-12 border-0 box-shadow-custom">
                <div class="modal-header border-0 pb-0 px-16 pt-16">
                    <div class="flex-align gap-10">
                        <span class="w-40 h-40 rounded-circle flex-center bg-purple-50 text-purple-600 text-xl"><i class="ph ph-users-three"></i></span>
                        <h5 class="modal-title mb-0" id="studentClassesModalTitle">Student Classes</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-12 px-16" id="studentClassesContent" style="max-height: 65vh; overflow-y: auto;">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer border-0 pt-0 px-16 pb-16">
                    <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9" data-bs-dismiss="modal">Close</button>
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
        // Enable/disable batch selects based on class checkbox
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.class-check').forEach(function(cb){
                cb.addEventListener('change', function(){
                    var cid = this.getAttribute('data-class-id');
                    var sel = document.getElementById('batch_select_' + cid);
                    if (!sel) return;
                    sel.disabled = !this.checked;
                    if (!this.checked) sel.value = '';
                });
            });
        });
    </script>
    <style>
        .modal-elegant .modal-title { font-weight: 600; }
        .modal-elegant .box-shadow-custom { box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
    </style>

    <script>
        let dataTable;
        
        $(document).ready(function() {
            // Initialize DataTable
            dataTable = new DataTable('#soloStudentsTable', {
                searching: true,
                lengthChange: false,
                info: false,
                paging: false,
                "columnDefs": [
                    { "orderable": false, "targets": [0, 7] } // Disables sorting on the 1st & last column
                ]
            });

            // Handle checkbox selection
            $('.student-checkbox, #selectAll').on('change', updateAssignButton);
            
            // Handle class filter
            $('#classFilter').on('change', function() {
                const filterValue = $(this).val();
                filterTable(filterValue);
            });
        });
        
        // Function to update the assign button visibility
        function updateAssignButton() {
            const selectedStudents = $('.student-checkbox:checked').length;
            if (selectedStudents > 0) {
                $('#assignClassesBtn').show();
            } else {
                $('#assignClassesBtn').hide();
            }
        }
        
        // Function to filter table based on class assignment
        function filterTable(filter) {
            dataTable.rows().every(function() {
                const row = this.node();
                const classCount = parseInt($(row).data('class-count'));
                let show = true;
                
                if (filter === 'with_classes') {
                    show = classCount > 0;
                } else if (filter === 'without_classes') {
                    show = classCount === 0;
                }
                
                if (show) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
            
            // Update showing count
            const visibleRows = $('#soloStudentsTable tbody tr:visible').length;
            $('#showing-count').text(visibleRows);
        }
        
        // Handle select all checkbox
        $('#selectAll').on('change', function () {
            const isChecked = $(this).prop('checked');
            $('#soloStudentsTable tbody tr:visible .student-checkbox').prop('checked', isChecked);
            updateAssignButton();
        });
        
        // Handle individual checkbox changes
        $(document).on('change', '.student-checkbox', function() {
            const totalVisible = $('#soloStudentsTable tbody tr:visible .student-checkbox').length;
            const checkedVisible = $('#soloStudentsTable tbody tr:visible .student-checkbox:checked').length;
            $('#selectAll').prop('checked', totalVisible === checkedVisible && totalVisible > 0);
            updateAssignButton();
        });
        
        // Handle assign classes modal opening
        $('#assignClassModal').on('show.bs.modal', function() {
            const selectedStudents = $('.student-checkbox:checked');
            let studentsList = '<ul class="list-unstyled">';
            
            // Add hidden inputs for selected students
            $('#assignClassForm').find('input[name="selected_students[]"]').remove();
            
            selectedStudents.each(function() {
                const studentId = $(this).val();
                const studentRow = $(this).closest('tr');
                const studentName = studentRow.find('td:eq(1)').text().trim();
                
                studentsList += `<li class="text-primary"><i class="ph ph-user-circle me-2"></i>${studentName}</li>`;
                
                // Add hidden input
                $('#assignClassForm').append(`<input type="hidden" name="selected_students[]" value="${studentId}">`);
            });
            
            studentsList += '</ul>';
            $('#selectedStudentsList').html(studentsList);
        });
        
        // Function to view student classes
        function viewStudentClasses(studentId, studentName) {
            $('#studentClassesModalTitle').text(`Classes for ${studentName}`);
            
            // Make AJAX call to get student's classes
            $.post('ajax/get_student_classes.php', {
                student_id: studentId
            }, function(response) {
                $('#studentClassesContent').html(response);
                $('#studentClassesModal').modal('show');
            }).fail(function() {
                $('#studentClassesContent').html('<p class="text-danger">Error loading student classes.</p>');
                $('#studentClassesModal').modal('show');
            });
        }
        
        // Function to manage student classes
        function manageStudentClasses(studentId, studentName) {
            $('#studentClassesModalTitle').text(`Manage Classes for ${studentName}`);
            
            // Make AJAX call to get student's classes with management options
            $.post('ajax/manage_student_classes.php', {
                student_id: studentId
            }, function(response) {
                $('#studentClassesContent').html(response);
                $('#studentClassesModal').modal('show');
            }).fail(function() {
                $('#studentClassesContent').html('<p class="text-danger">Error loading student classes.</p>');
                $('#studentClassesModal').modal('show');
            });
        }
        
        // Handle remove class access
        $(document).on('click', '.remove-class-btn', function() {
            const studentId = $(this).data('student-id');
            const classId = $(this).data('class-id');
            const className = $(this).data('class-name');
            
            if (confirm(`Are you sure you want to remove access to "${className}"?`)) {
                $.post('', {
                    action: 'remove_class_access',
                    student_id: studentId,
                    class_id: classId
                }, function(response) {
                    location.reload();
                });
            }
        });

        // ========================== Export Js Start ==============================
        document.getElementById('exportOptions').addEventListener('change', function() {
            const format = this.value;
            const table = document.getElementById('soloStudentsTable');
            let data = [];
            const headers = [];
            
            // Get the table headers (skip checkbox column)
            table.querySelectorAll('thead th').forEach((th, index) => {
                if (index > 0) { // Skip checkbox column
                    headers.push(th.innerText.trim());
                }
            });

            // Get the visible table rows
            table.querySelectorAll('tbody tr:not([style*="display: none"])').forEach(tr => {
                const row = {};
                tr.querySelectorAll('td').forEach((td, index) => {
                    if (index > 0 && index < headers.length + 1) { // Skip checkbox and actions columns
                        row[headers[index - 1]] = td.innerText.trim();
                    }
                });
                data.push(row);
            });

            if (format === 'csv') {
                downloadCSV(data);
            } else if (format === 'json') {
                downloadJSON(data);
            }
        });

        function downloadCSV(data) {
            const headers = Object.keys(data[0]);
            const csv = [headers.join(','), ...data.map(row => headers.map(header => row[header]).join(','))].join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'solo_students.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function downloadJSON(data) {
            const json = JSON.stringify(data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'solo_students.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        // ========================== Export Js End ==============================

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

</body>
</html>
