<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';

// Admin roles only
require_permission('batches.manage_students', 'batches.php');

$current_user = current_user();

$database = new Database();
$db = $database->getConnection();

$userModel = new User($db);
$batchModel = new Batch($db);
$enrollmentModel = new BatchEnrollment($db);

$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;
$batch = null;
if ($batch_id > 0) {
    $batchModel->id = $batch_id;
    $batch = $batchModel->readOne();
    if (!$batch) {
        flash_message('Batch not found.', 'error');
        redirect('batches.php');
    }
}

// Helper: find user by email or username
function find_user_by_identifier($userModel, $identifier) {
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        return $userModel->findByEmail($identifier);
    }
    return $userModel->findByUsername($identifier);
}

// Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add_student') {
            $bid = (int)($_POST['batch_id'] ?? 0);
            $identifier = trim($_POST['user_identifier'] ?? '');
            $status = $_POST['enrollment_status'] ?? 'active';
            if ($bid <= 0 || $identifier === '') {
                throw new Exception('Batch and user identifier are required.');
            }
            $user = find_user_by_identifier($userModel, $identifier);
            if (!$user) {
                throw new Exception('User not found by that email/username.');
            }
            if ($enrollmentModel->isEnrolled($user['id'], $bid)) {
                throw new Exception('User is already enrolled in this batch.');
            }
            $enrollmentModel->batch_id = $bid;
            $enrollmentModel->user_id = $user['id'];
            $enrollmentModel->enrollment_status = in_array($status, ['pending','active','suspended','completed','dropped']) ? $status : 'active';
            if ($enrollmentModel->create()) {
                flash_message('Student added to batch successfully.', 'success');
            } else {
                throw new Exception('Failed to add student to batch.');
            }
            redirect('batch-students.php?batch_id=' . $bid);
        }

        if ($action === 'remove_student') {
            $eid = (int)($_POST['enrollment_id'] ?? 0);
            $bid = (int)($_POST['batch_id'] ?? 0);
            if ($eid <= 0 || $bid <= 0) throw new Exception('Invalid enrollment.');
            $enrollmentModel->id = $eid;
            if ($enrollmentModel->delete()) {
                flash_message('Student removed from batch.', 'success');
            } else {
                throw new Exception('Failed to remove student.');
            }
            redirect('batch-students.php?batch_id=' . $bid);
        }

        if ($action === 'move_student') {
            $eid = (int)($_POST['enrollment_id'] ?? 0);
            $from_bid = (int)($_POST['from_batch_id'] ?? 0);
            $to_bid = (int)($_POST['to_batch_id'] ?? 0);
            if ($eid <= 0 || $from_bid <= 0 || $to_bid <= 0) throw new Exception('Invalid move parameters.');
            if ($from_bid === $to_bid) throw new Exception('Choose a different target batch.');
            // Update batch_id for this enrollment
            $stmt = $db->prepare("UPDATE batch_enrollments SET batch_id = :to_bid WHERE id = :id");
            if ($stmt->execute([':to_bid' => $to_bid, ':id' => $eid])) {
                flash_message('Student moved to target batch.', 'success');
            } else {
                throw new Exception('Failed to move student.');
            }
            redirect('batch-students.php?batch_id=' . $from_bid);
        }
    } catch (Exception $e) {
        flash_message($e->getMessage(), 'error');
        if (!empty($_POST['batch_id'])) {
            redirect('batch-students.php?batch_id=' . (int)$_POST['batch_id']);
        } else {
            redirect('batch-students.php');
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
    <title>Manage Batch Students - <?php echo APP_NAME; ?></title>
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
        .small-text { font-size: 12px; }
    </style>
    </head>
<body>
<?php include 'includes/sidebar_new.php'; ?>
<div class="dashboard-main-wrapper">
    <?php include 'includes/navbar_new.php'; ?>
    <div class="dashboard-body">
        <div class="row mb-20">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-between gap-8">
                        <h5 class="mb-0">Manage Batch Students</h5>
                        <?php if ($batch): ?>
                            <span class="badge bg-main-600 text-white">Batch: <?php echo htmlspecialchars($batch['batch_name']); ?> (<?php echo htmlspecialchars($batch['batch_code']); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($flash): ?>
                            <div class="alert alert-<?php echo $flash['type']==='success'?'success':'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($flash['message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Batch selector -->
                        <form method="get" class="row g-12 mb-20">
                            <div class="col-md-6">
                                <label class="form-label">Select Batch</label>
                                <div class="input-group">
                                    <input type="number" name="batch_id" class="form-control" placeholder="Enter Batch ID" value="<?php echo $batch_id ?: ''; ?>">
                                    <button class="btn btn-main">Load</button>
                                </div>
                                <div class="small-text text-gray-500 mt-1">Enter a batch ID to manage.</div>
                            </div>
                        </form>

                        <?php if ($batch): ?>
                            <div class="row g-20">
                                <div class="col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header border-bottom border-gray-100">
                                            <h6 class="mb-0">Add Student to Batch</h6>
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <input type="hidden" name="action" value="add_student">
                                                <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
                                                <div class="mb-12">
                                                    <label class="form-label">User Email or Username</label>
                                                    <input type="text" name="user_identifier" class="form-control" placeholder="e.g. alice@example.com or alice" required>
                                                </div>
                                                <div class="mb-12">
                                                    <label class="form-label">Enrollment Status</label>
                                                    <select name="enrollment_status" class="form-select">
                                                        <option value="active">Active</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="suspended">Suspended</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="dropped">Dropped</option>
                                                    </select>
                                                </div>
                                                <button class="btn btn-main w-100">Add Student</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="card h-100">
                                        <div class="card-header border-bottom border-gray-100 flex-between gap-8">
                                            <h6 class="mb-0">Enrolled Students</h6>
                                            <span class="badge bg-success-600 text-white"><?php echo (int)$batch['active_students']; ?> Active</span>
                                        </div>
                                        <div class="card-body">
                                            <?php $enrollments = $enrollmentModel->getEnrollmentsByBatch($batch_id); ?>
                                            <?php if (empty($enrollments)): ?>
                                                <p class="text-gray-600 mb-0">No students enrolled yet.</p>
                                            <?php else: ?>
                                                <div class="row g-12">
                                                    <?php foreach ($enrollments as $en): ?>
                                                        <div class="col-xxl-4 col-lg-6">
                                                            <div class="card h-100">
                                                                <div class="card-body p-16">
                                                                    <div class="flex-align gap-12 mb-12">
                                                                        <img src="<?php echo htmlspecialchars(user_avatar_url($en)); ?>" alt="" class="w-54 h-54 rounded-circle">
                                                                        <div class="flex-grow-1">
                                                                            <div class="flex-between gap-8">
                                                                                <div>
                                                                                    <h6 class="mb-2"><?php echo htmlspecialchars($en['full_name'] ?? $en['username'] ?? ''); ?></h6>
                                                                                    <div class="text-13 text-gray-400">User #<?php echo (int)$en['user_id']; ?></div>
                                                                                </div>
                                                                                <span class="py-4 px-10 rounded-pill text-13 bg-<?php echo $en['enrollment_status']==='active'?'success-50':'gray-50'; ?> text-<?php echo $en['enrollment_status']==='active'?'success-600':'gray-600'; ?>"><?php echo htmlspecialchars(ucfirst($en['enrollment_status'])); ?></span>
                                                                            </div>
                                                                            <div class="text-13 text-gray-400 mt-6"><?php echo htmlspecialchars($en['email'] ?? ''); ?></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-between gap-8 flex-wrap">
                                                                        <form method="post" class="d-flex gap-8 align-items-center">
                                                                            <input type="hidden" name="action" value="move_student">
                                                                            <input type="hidden" name="enrollment_id" value="<?php echo (int)$en['id']; ?>">
                                                                            <input type="hidden" name="from_batch_id" value="<?php echo $batch_id; ?>">
                                                                            <?php $other_batches = $batchModel->getBatchesByClass($batch['class_id']); ?>
                                                                            <select name="to_batch_id" class="form-select form-select-sm" required>
                                                                                <option value="">Move to...</option>
                                                                                <?php foreach ($other_batches as $ob): if ((int)$ob['id'] === $batch_id) continue; ?>
                                                                                    <option value="<?php echo (int)$ob['id']; ?>"><?php echo htmlspecialchars($ob['batch_name']); ?> (<?php echo htmlspecialchars($ob['batch_code']); ?>)</option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                            <button class="btn btn-outline-main btn-sm" style="width: -webkit-fill-available;">Move</button>
                                                                        </form>
                                                                        <form method="post" onsubmit="return confirm('Remove this student from the batch?');">
                                                                            <input type="hidden" name="action" value="remove_student">
                                                                            <input type="hidden" name="enrollment_id" value="<?php echo (int)$en['id']; ?>">
                                                                            <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
                                                                            <button class="btn btn-danger btn-sm">Remove</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
</body>
</html>
