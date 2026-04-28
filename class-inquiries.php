<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/ClassInquiry.php';
require_once 'classes/User.php';

// Check if user is logged in and has admin privileges
require_roles(['super_admin', 'organization_admin', 'school_admin']);

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$classInquiry = new ClassInquiry($db);
$user = new User($db);

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$inquiry_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve':
                $inquiry_id = (int)$_POST['inquiry_id'];
                $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');

                error_log("Attempting to approve inquiry ID: " . $inquiry_id . " by admin ID: " . $current_user['id']);

                $result = $classInquiry->approve($inquiry_id, $current_user['id'], $admin_notes);

                if ($result) {
                    flash_message('Inquiry approved successfully!', 'success');
                } else {
                    $simple_result = $classInquiry->approveSimple($inquiry_id, $current_user['id'], $admin_notes);

                    if ($simple_result) {
                        flash_message('Inquiry approved successfully! (Note: Manual class access may be required)', 'success');
                    } else {
                        $inquiry_details = $classInquiry->getInquiryDetails($inquiry_id);
                        if ($inquiry_details) {
                            error_log("Inquiry details: " . print_r($inquiry_details, true));
                            flash_message('Error approving inquiry. Current status: ' . $inquiry_details['status'], 'error');
                        } else {
                            flash_message('Error approving inquiry. Inquiry not found.', 'error');
                        }
                    }
                }
                redirect('class-inquiries.php');
                break;

            case 'reject':
                $inquiry_id = (int)$_POST['inquiry_id'];
                $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');
                if ($classInquiry->reject($inquiry_id, $current_user['id'], $admin_notes)) {
                    flash_message('Inquiry rejected successfully!', 'success');
                } else {
                    flash_message('Error rejecting inquiry.', 'error');
                }
                redirect('class-inquiries.php');
                break;

            case 'bulk_approve':
                $inquiry_ids_string = $_POST['inquiry_ids'] ?? '';
                $inquiry_ids = array_filter(array_map('intval', explode(',', $inquiry_ids_string)));
                $admin_notes = sanitize_input($_POST['admin_notes'] ?? '');

                if (!empty($inquiry_ids)) {
                    if ($classInquiry->bulkApprove($inquiry_ids, $current_user['id'], $admin_notes)) {
                        flash_message(count($inquiry_ids) . ' inquiries approved successfully!', 'success');
                    } else {
                        flash_message('Error approving selected inquiries.', 'error');
                    }
                }
                redirect('class-inquiries.php');
                break;
        }
    }
}

// Get filter parameters - NOW INCLUDING TIMESLOT
$filters = [
    'status' => $_GET['status'] ?? '',
    'class_id' => $_GET['class_id'] ?? '',
    'country' => $_GET['country'] ?? '',
    'time_slot' => $_GET['time_slot'] ?? ''
];

// Remove empty filters
$filters = array_filter($filters);

// Get inquiries with filters
$inquiries_query = "SELECT ci.*, u.full_name, u.username, u.email, u.phone, c.class_name, c.class_code,
                           admin.full_name as reviewed_by_name
                    FROM class_inquiries ci
                    LEFT JOIN users u ON ci.user_id = u.id
                    LEFT JOIN classes c ON ci.class_id = c.id
                    LEFT JOIN users admin ON ci.reviewed_by = admin.id
                    WHERE 1=1";

$params = [];

// Apply filters
if (!empty($filters['status'])) {
    $inquiries_query .= " AND ci.status = ?";
    $params[] = $filters['status'];
}

if (!empty($filters['class_id'])) {
    $inquiries_query .= " AND ci.class_id = ?";
    $params[] = $filters['class_id'];
}

if (!empty($filters['country'])) {
    $inquiries_query .= " AND ci.country = ?";
    $params[] = $filters['country'];
}

if (!empty($filters['time_slot'])) {
    $inquiries_query .= " AND ci.preferred_time_slot = ?";
    $params[] = $filters['time_slot'];
}

$inquiries_query .= " ORDER BY ci.created_at DESC";

$inquiries_stmt = $db->prepare($inquiries_query);
$inquiries_stmt->execute($params);
$inquiries = $inquiries_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT status, COUNT(*) as count FROM class_inquiries GROUP BY status";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats_raw = $stats_stmt->fetchAll(PDO::FETCH_ASSOC);

$stats = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'total' => 0
];

foreach ($stats_raw as $stat) {
    $stats[$stat['status']] = $stat['count'];
    $stats['total'] += $stat['count'];
}

// Get all classes for filter dropdown
$classes_query = "SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name";
$classes_stmt = $db->prepare($classes_query);
$classes_stmt->execute();
$classes = $classes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique countries for filters
$countries_query = "SELECT DISTINCT country FROM class_inquiries WHERE country IS NOT NULL ORDER BY country";
$countries_stmt = $db->prepare($countries_query);
$countries_stmt->execute();
$countries = $countries_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get unique time slots for filters
$timeslots_query = "SELECT DISTINCT preferred_time_slot FROM class_inquiries WHERE preferred_time_slot IS NOT NULL ORDER BY preferred_time_slot";
$timeslots_stmt = $db->prepare($timeslots_query);
$timeslots_stmt->execute();
$timeslots = $timeslots_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get inquiry details for view
if ($action == 'view' && $inquiry_id > 0) {
    $inquiry_query = "SELECT ci.*, u.full_name, u.username, u.email, u.phone, 
                             c.class_name, c.class_code, c.description as class_description,
                             admin.full_name as reviewed_by_name
                      FROM class_inquiries ci
                      LEFT JOIN users u ON ci.user_id = u.id
                      LEFT JOIN classes c ON ci.class_id = c.id
                      LEFT JOIN users admin ON ci.reviewed_by = admin.id
                      WHERE ci.id = ?";
    $inquiry_stmt = $db->prepare($inquiry_query);
    $inquiry_stmt->execute([$inquiry_id]);
    $inquiry_details = $inquiry_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inquiry_details) {
        flash_message('Inquiry not found.', 'error');
        redirect('class-inquiries.php');
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
    <title>Class Inquiries Management - <?php echo APP_NAME; ?></title>
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
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">
                            <?php
                            switch ($action) {
                                case 'view':
                                    echo 'View Inquiry';
                                    break;
                                default:
                                    echo 'Class Inquiries';
                                    break;
                            }
                            ?>
                        </span></li>
                </ul>
            </div>

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

            <?php if ($action == 'list'): ?>

                <!-- Statistics Cards -->
                <div class="row gy-4 mb-24">
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="flex-between gap-8 mb-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-warning-100 text-warning-600 text-2xl"><i class="ph-fill ph-clock"></i></span>
                                    <div id="pending-chart" class="remove-tooltip-title rounded-tooltip-value"></div>
                                </div>
                                <div>
                                    <h4 class="mb-2"><?php echo $stats['pending']; ?></h4>
                                    <span class="text-gray-300">Pending Inquiries</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="flex-between gap-8 mb-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-success-100 text-success text-2xl"><i class="ph-fill ph-check-circle"></i></span>
                                    <div id="approved-chart" class="remove-tooltip-title rounded-tooltip-value"></div>
                                </div>
                                <div>
                                    <h4 class="mb-2"><?php echo $stats['approved']; ?></h4>
                                    <span class="text-gray-300">Approved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="flex-between gap-8 mb-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-danger-100 text-danger-600 text-2xl"><i class="ph-fill ph-x-circle"></i></span>
                                    <div id="rejected-chart" class="remove-tooltip-title rounded-tooltip-value"></div>
                                </div>
                                <div>
                                    <h4 class="mb-2"><?php echo $stats['rejected']; ?></h4>
                                    <span class="text-gray-300">Rejected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="flex-between gap-8 mb-16">
                                    <span class="flex-shrink-0 w-48 h-48 flex-center rounded-circle bg-main-100 text-main-600 text-2xl"><i class="ph-fill ph-list"></i></span>
                                    <div id="total-chart" class="remove-tooltip-title rounded-tooltip-value"></div>
                                </div>
                                <div>
                                    <h4 class="mb-2"><?php echo $stats['total']; ?></h4>
                                    <span class="text-gray-300">Total Inquiries</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="card mb-24">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0">Filter Inquiries</h5>
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Filter inquiries by various criteria">
                            <i class="ph-fill ph-funnel"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" class="row g-20">
                            <div class="col-md-3">
                                <label class="h6 mb-8 fw-semibold font-heading">Status</label>
                                <select name="status" class="form-select py-9 placeholder-13 text-15">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo (isset($_GET['status']) && $_GET['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="h6 mb-8 fw-semibold font-heading">Class</label>
                                <select name="class_id" class="form-select py-9 placeholder-13 text-15">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo $class['id']; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['class_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="h6 mb-8 fw-semibold font-heading">Country</label>
                                <select name="country" class="form-select py-9 placeholder-13 text-15">
                                    <option value="">All Countries</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country; ?>" <?php echo (isset($_GET['country']) && $_GET['country'] == $country) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="h6 mb-8 fw-semibold font-heading">Time Slot</label>
                                <select name="time_slot" class="form-select py-9 placeholder-13 text-15">
                                    <option value="">All Time Slots</option>
                                    <?php foreach ($timeslots as $timeslot): ?>
                                        <option value="<?php echo $timeslot; ?>" <?php echo (isset($_GET['time_slot']) && $_GET['time_slot'] == $timeslot) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($timeslot); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="flex-align gap-8">
                                    <button type="submit" class="btn btn-main rounded-pill py-9 px-20">
                                        <i class="ph ph-funnel me-2"></i>Apply Filters
                                    </button>
                                    <a href="class-inquiries.php" class="btn btn-outline-main rounded-pill py-9 px-20">
                                        <i class="ph ph-x me-2"></i>Clear Filters
                                    </a>
                                    <button type="button" class="btn btn-success rounded-pill py-9 px-20 ms-auto" onclick="bulkApprove()">
                                        <i class="ph ph-checks me-2"></i>Bulk Approve Selected
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Inquiries List -->
                <div class="card">
                    <div class="card-header border-bottom border-gray-100">
                        <h5 class="mb-0">Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    if (!empty($inquiries)) {
                                        foreach ($inquiries as $inquiry) {
                                            echo "<div class='col-xxl-4 col-lg-6 col-sm-12'>";
                                            echo "<div class='card border border-gray-100 h-100'>";
                                            echo "<div class='card-body p-16'>";

                                            // Header with status
                                            echo "<div class='flex-between gap-8 mb-16'>";
                                            echo "<div class='flex-align gap-8'>";
                                            echo "<input type='checkbox' class='form-check-input inquiry-checkbox' value='" . $inquiry['id'] . "' data-status='" . $inquiry['status'] . "'>";
                                            if ($inquiry['status'] == 'pending') {
                                                echo "<span class='py-2 px-8 bg-warning-50 text-warning-600 rounded-pill text-13'>Pending</span>";
                                            } elseif ($inquiry['status'] == 'approved') {
                                                echo "<span class='py-2 px-8 bg-success-50 text-success rounded-pill text-13'>Approved</span>";
                                            } else {
                                                echo "<span class='py-2 px-8 bg-danger-50 text-danger-600 rounded-pill text-13'>Rejected</span>";
                                            }
                                            echo "</div>";
                                            echo "<small class='text-gray-400'>" . date('M j, Y', strtotime($inquiry['created_at'])) . "</small>";
                                            echo "</div>";

                                            // Student info - CHANGED TO SHOW PHONE INSTEAD OF USERNAME
                                            echo "<div class='mb-16'>";
                                            echo "<h6 class='mb-4'>" . htmlspecialchars($inquiry['full_name']) . "</h6>";
                                            echo "<p class='text-gray-400 text-13 mb-2'><i class='ph ph-phone me-1'></i>" . htmlspecialchars($inquiry['phone'] ?? 'N/A') . "</p>";
                                            echo "<p class='text-gray-400 text-13'>" . htmlspecialchars($inquiry['contact_email']) . "</p>";
                                            echo "</div>";

                                            // Class info
                                            echo "<div class='mb-16 p-12 bg-main-50 rounded-8'>";
                                            echo "<h6 class='text-main-600 mb-2'>" . htmlspecialchars($inquiry['class_name']) . "</h6>";
                                            echo "<p class='text-13 text-gray-400 mb-0'>Code: " . htmlspecialchars($inquiry['class_code']) . "</p>";
                                            echo "</div>";

                                            // Location
                                            echo "<div class='mb-16'>";
                                            echo "<div class='flex-align gap-8 mb-8'>";
                                            echo "<i class='ph ph-map-pin text-gray-400'></i>";
                                            echo "<span class='text-13 text-gray-600'>" . htmlspecialchars($inquiry['country']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";

                                            // Preferred Time Slot
                                            echo "<div class='mb-16'>";
                                            echo "<p class='text-13 fw-medium mb-8'>Preferred Time Slot:</p>";
                                            echo "<div class='flex-align gap-4 flex-wrap'>";
                                            echo "<span class='py-2 px-6 bg-gray-100 text-gray-600 rounded text-11'>" . htmlspecialchars($inquiry['preferred_time_slot']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";

                                            // Actions
                                            echo "<div class='flex-between gap-8 mt-16'>";
                                            echo "<div class='flex-align gap-8'>";
                                            echo "<a href='class-inquiries.php?action=view&id=" . $inquiry['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>View Details</a>";

                                            if ($inquiry['status'] == 'pending') {
                                                echo "<button type='button' class='btn btn-success rounded-pill py-6 px-12 text-13' onclick='quickApprove(" . $inquiry['id'] . ")'>Quick Approve</button>";
                                            }
                                            echo "</div>";
                                            echo "</div>";

                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<div class='col-12'>";
                                        echo "<div class='text-center py-5'>";
                                        echo "<div class='mb-20'>";
                                        echo "<i class='ph ph-clipboard-text text-6xl text-gray-300'></i>";
                                        echo "</div>";
                                        echo "<h5 class='text-gray-600 mb-8'>No Inquiries Found</h5>";
                                        echo "<p class='text-gray-400'>There are no class inquiries matching your current filters.</p>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($action == 'view' && isset($inquiry_details)): ?>
                <!-- Inquiry Details View (unchanged, showing phone in student info section) -->
                <div class="row gy-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body p-lg-20 p-sm-3">
                                <div class="flex-between flex-wrap gap-12 mb-20">
                                    <div>
                                        <h3 class="mb-4"><?php echo htmlspecialchars($inquiry_details['full_name']); ?></h3>
                                        <p class="text-gray-600 text-15"><i class="ph ph-phone me-2"></i><?php echo htmlspecialchars($inquiry_details['phone'] ?? 'N/A'); ?></p>
                                    </div>

                                    <div class="flex-align flex-wrap gap-24">
                                        <?php if ($inquiry_details['status'] == 'pending'): ?>
                                            <span class="py-6 px-16 bg-warning-50 text-warning-600 rounded-pill text-15">Pending Review</span>
                                        <?php elseif ($inquiry_details['status'] == 'approved'): ?>
                                            <span class="py-6 px-16 bg-success-50 text-success rounded-pill text-15">Approved</span>
                                        <?php else: ?>
                                            <span class="py-6 px-16 bg-danger-50 text-danger-600 rounded-pill text-15">Rejected</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-24">
                                    <!-- Student Information -->
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Student Information</h5>
                                        <div class="row g-20">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-user"></i></span>
                                                        <strong>Full Name:</strong> <?php echo htmlspecialchars($inquiry_details['full_name']); ?>
                                                    </li>
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-at"></i></span>
                                                        <strong>Username:</strong> <?php echo htmlspecialchars($inquiry_details['username']); ?>
                                                    </li>
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-envelope"></i></span>
                                                        <strong>Email:</strong> <?php echo htmlspecialchars($inquiry_details['email']); ?>
                                                    </li>
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-phone"></i></span>
                                                        <strong>Phone:</strong> <?php echo htmlspecialchars($inquiry_details['phone'] ?? 'N/A'); ?>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-whatsapp-logo"></i></span>
                                                        <strong>WhatsApp:</strong> <?php echo htmlspecialchars($inquiry_details['whatsapp_number']); ?>
                                                    </li>
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-envelope"></i></span>
                                                        <strong>Contact Email:</strong> <?php echo htmlspecialchars($inquiry_details['contact_email']); ?>
                                                    </li>
                                                    <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                        <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-map-pin"></i></span>
                                                        <strong>Country:</strong> <?php echo htmlspecialchars($inquiry_details['country']); ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="mt-16">
                                            <p class="text-gray-300 text-15"><strong>Address:</strong> <?php echo htmlspecialchars($inquiry_details['address']); ?></p>
                                        </div>
                                    </div>

                                    <!-- Class Information -->
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Requested Class</h5>
                                        <div class="p-16 bg-main-50 rounded-8">
                                            <h6 class="text-main-600 mb-8"><?php echo htmlspecialchars($inquiry_details['class_name']); ?></h6>
                                            <p class="text-15 mb-8"><strong>Code:</strong> <?php echo htmlspecialchars($inquiry_details['class_code']); ?></p>
                                            <?php if ($inquiry_details['class_description']): ?>
                                                <p class="text-15 text-gray-600"><?php echo htmlspecialchars($inquiry_details['class_description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Preferences -->
                                    <div class="mb-24 pb-24 border-bottom border-gray-100">
                                        <h5 class="mb-12 fw-bold">Student Preferences</h5>
                                        <div class="row g-20">
                                            <div class="col-md-12">
                                                <h6 class="mb-8">Preferred Time Slot:</h6>
                                                <div class="flex-align gap-8 flex-wrap">
                                                    <span class="py-4 px-12 bg-secondary-50 text-secondary-600 rounded-pill text-13"><?php echo htmlspecialchars($inquiry_details['preferred_time_slot']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Information -->
                                    <div class="mb-24">
                                        <h5 class="mb-12 fw-bold">Status Information</h5>
                                        <ul class="list-unstyled">
                                            <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-calendar"></i></span>
                                                <strong>Submitted:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($inquiry_details['created_at'])); ?>
                                            </li>
                                            <?php if ($inquiry_details['reviewed_at']): ?>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-calendar-check"></i></span>
                                                    <strong>Reviewed:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($inquiry_details['reviewed_at'])); ?>
                                                </li>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-user-check"></i></span>
                                                    <strong>Reviewed by:</strong> <?php echo htmlspecialchars($inquiry_details['reviewed_by_name'] ?? 'N/A'); ?>
                                                </li>
                                            <?php endif; ?>
                                            <?php if ($inquiry_details['admin_notes']): ?>
                                                <li class="flex-align gap-6 text-gray-300 text-15 mb-12">
                                                    <span class="flex-shrink-0 text-22 d-flex text-main-600"><i class="ph ph-note"></i></span>
                                                    <strong>Admin Notes:</strong> <?php echo htmlspecialchars($inquiry_details['admin_notes']); ?>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-16 fw-bold">Quick Actions</h5>
                                <div class="d-grid gap-8">
                                    <?php if ($inquiry_details['status'] == 'pending'): ?>
                                        <button type="button" class="btn btn-success rounded-pill py-9" onclick="showApproveModal(<?php echo $inquiry_details['id']; ?>)">
                                            <i class="ph ph-check me-2"></i>Approve Inquiry
                                        </button>
                                        <button type="button" class="btn btn-danger-600 rounded-pill py-9" onclick="showRejectModal(<?php echo $inquiry_details['id']; ?>)">
                                            <i class="ph ph-x me-2"></i>Reject Inquiry
                                        </button>
                                    <?php elseif ($inquiry_details['status'] == 'approved'): ?>
                                        <div class="alert alert-success">
                                            <i class="ph ph-check-circle me-2"></i>This inquiry has been approved and the student has been granted access to the class.
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-danger">
                                            <i class="ph ph-x-circle me-2"></i>This inquiry has been rejected.
                                        </div>
                                    <?php endif; ?>

                                    <a href="class-inquiries.php" class="btn btn-outline-main rounded-pill py-9">
                                        <i class="ph ph-arrow-left me-2"></i>Back to Inquiries
                                    </a>
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

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Inquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="inquiry_id" id="approveInquiryId">

                        <p>Are you sure you want to approve this class inquiry? The student will be granted access to the class.</p>

                        <div class="mb-3">
                            <label for="approveNotes" class="form-label">Admin Notes (Optional)</label>
                            <textarea class="form-control" id="approveNotes" name="admin_notes" rows="3" placeholder="Add notes for the student..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-main" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Inquiry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Inquiry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="inquiry_id" id="rejectInquiryId">

                        <p>Are you sure you want to reject this class inquiry?</p>

                        <div class="mb-3">
                            <label for="rejectNotes" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectNotes" name="admin_notes" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-main" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger-600">Reject Inquiry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Approve Modal -->
    <div class="modal fade" id="bulkApproveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Approve Inquiries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="bulk_approve">
                        <input type="hidden" name="inquiry_ids" id="bulkInquiryIds">

                        <p>Are you sure you want to approve <span id="selectedCount">0</span> selected inquiries?</p>

                        <div class="mb-3">
                            <label for="bulkNotes" class="form-label">Admin Notes (Optional)</label>
                            <textarea class="form-control" id="bulkNotes" name="admin_notes" rows="3" placeholder="Add notes for approved students..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-main" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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

    <script>
        // Show approve modal
        function showApproveModal(inquiryId) {
            document.getElementById('approveInquiryId').value = inquiryId;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }

        // Show reject modal
        function showRejectModal(inquiryId) {
            document.getElementById('rejectInquiryId').value = inquiryId;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        // Quick approve
        function quickApprove(inquiryId) {
            if (confirm('Are you sure you want to approve this inquiry?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="inquiry_id" value="${inquiryId}">
                    <input type="hidden" name="admin_notes" value="Quick approved">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Bulk approve
        function bulkApprove() {
            const selectedCheckboxes = document.querySelectorAll('.inquiry-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one inquiry to approve.');
                return;
            }

            // Filter only pending inquiries
            const pendingCheckboxes = Array.from(selectedCheckboxes).filter(cb => cb.dataset.status === 'pending');

            if (pendingCheckboxes.length === 0) {
                alert('Please select at least one pending inquiry.');
                return;
            }

            const pendingIds = pendingCheckboxes.map(cb => cb.value);

            document.getElementById('bulkInquiryIds').value = pendingIds.join(',');
            document.getElementById('selectedCount').textContent = pendingIds.length;
            new bootstrap.Modal(document.getElementById('bulkApproveModal')).show();
        }

        // Select all functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add select all checkbox if needed
            const checkboxes = document.querySelectorAll('.inquiry-checkbox');
            if (checkboxes.length > 0) {
                // Create select all checkbox
                const selectAllContainer = document.createElement('li');
                selectAllContainer.className = 'nav-item me-3';
                selectAllContainer.innerHTML = `
                    <label class="flex-align gap-8">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <span class="text-13">Select All</span>
                    </label>
                `;

                const tabsList = document.querySelector('.nav-pills');
                if (tabsList) {
                    tabsList.insertBefore(selectAllContainer, tabsList.firstChild);
                }

                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });

                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', function() {
                            const totalCheckboxes = checkboxes.length;
                            const checkedCheckboxes = document.querySelectorAll('.inquiry-checkbox:checked').length;
                            selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
                        });
                    });
                }
            }
        });

        // Refresh page
        function refreshPage() {
            window.location.reload();
        }
    </script>
</body>

</html>