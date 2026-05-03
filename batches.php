<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Batch.php';
require_once 'classes/BatchEnrollment.php';
require_once 'classes/BatchRegistrationLink.php';
require_once 'classes/ClassModel.php';
require_once 'classes/User.php';
require_once 'includes/functions.php';

require_permission('batches.view', 'dashboard.php');

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$batchModel = new Batch($db);
$enrollmentModel = new BatchEnrollment($db);
$linkModel = new BatchRegistrationLink($db);
$classModel = new ClassModel($db);
$userModel = new User($db);

$accessible_classes_raw = $userModel->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$batch_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Backward-compatible redirect to new student management page
if ($action === 'manage_students' && $batch_id > 0) {
    if (!can('batches.manage_students')) permission_denied('batches.php');
    redirect('batch-students.php?batch_id=' . $batch_id);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $errors = [];

    if ($action == 'create' || $action == 'update') {
        if ($action == 'create' && !can('batches.create')) permission_denied('batches.php');
        if ($action == 'update' && !can('batches.edit')) permission_denied('batches.php');
        if (empty($_POST['batch_name'])) $errors[] = 'Batch name is required';
        if (empty($_POST['batch_code'])) $errors[] = 'Batch code is required';
        if (empty($_POST['class_id'])) $errors[] = 'Class/Course is required';

        $batchModel->batch_name = trim($_POST['batch_name']);
        $batchModel->batch_code = trim($_POST['batch_code']);
        $batchModel->class_id = (int)$_POST['class_id'];
        $batchModel->description = trim($_POST['description']);
        $batchModel->start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $batchModel->end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $batchModel->max_students = !empty($_POST['max_students']) ? (int)$_POST['max_students'] : null;
        $batchModel->enrollment_status = $_POST['enrollment_status'];
        $batchModel->meeting_schedule = trim($_POST['meeting_schedule']);
        $batchModel->zoom_meeting_id = trim($_POST['zoom_meeting_id']);
        $batchModel->zoom_meeting_link = trim($_POST['zoom_meeting_link']);
        $batchModel->instructor_id = !empty($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : null;
        $batchModel->status = $_POST['status'];

        if ($action == 'create') {
            if ($batchModel->codeExists()) {
                $errors[] = 'Batch code already exists';
            }
            if (empty($errors)) {
                $batchModel->created_by = $current_user['id'];
                if ($batchModel->create()) {
                    flash_message('Batch created successfully', 'success');
                    redirect('batches.php?action=view&id=' . $batchModel->id);
                } else {
                    $errors[] = 'Failed to create batch';
                }
            }
        } else {
            $batchModel->id = (int)$_POST['id'];
            if ($batchModel->codeExists($batchModel->id)) {
                $errors[] = 'Batch code already exists';
            }
            if (empty($errors)) {
                if ($batchModel->update()) {
                    flash_message('Batch updated successfully', 'success');
                    redirect('batches.php?action=view&id=' . $batchModel->id);
                } else {
                    $errors[] = 'Failed to update batch';
                }
            }
        }
    } elseif ($action == 'delete' && $batch_id > 0) {
        if (!can('batches.delete')) permission_denied('batches.php');
        $batchModel->id = $batch_id;
        if ($batchModel->delete()) {
            flash_message('Batch deleted successfully', 'success');
            redirect('batches.php');
        } else {
            flash_message('Failed to delete batch', 'error');
            redirect('batches.php');
        }
    } elseif ($action == 'update_enrollment_status') {
        if (!can('enrollments.manage')) permission_denied('batches.php');
        $enrollment_id = (int)$_POST['enrollment_id'];
        $new_status = $_POST['new_status'];
        $enrollmentModel->id = $enrollment_id;
        if ($enrollmentModel->updateStatus($new_status, $current_user['id'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    } elseif ($action == 'generate_link') {
        if (!can('batches.generate_links')) permission_denied('batches.php');
        $linkModel->batch_id = (int)$_POST['batch_id'];
        $linkModel->link_type = $_POST['link_type'];
        $linkModel->max_uses = !empty($_POST['max_uses']) ? (int)$_POST['max_uses'] : null;
        $linkModel->expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        $linkModel->created_by = $current_user['id'];
        
        if ($linkModel->create()) {
            flash_message('Registration link generated successfully', 'success');
            redirect('batches.php?action=manage_students&id=' . $linkModel->batch_id);
        } else {
            flash_message('Failed to generate link', 'error');
        }
    }
}

// Get data for edit action
if ($action == 'edit' && $batch_id > 0) {
    if (!can('batches.edit')) permission_denied('batches.php');
    $batchModel->id = $batch_id;
    if (!$batchModel->readOne()) {
        flash_message('Batch not found', 'error');
        redirect('batches.php');
    }
}

// Get data based on action
if ($action == 'list') {
    $batches = $batchModel->readAll($accessible_class_ids, $can_access_all_classes_flag);
} elseif ($action == 'view' && $batch_id > 0) {
    $batchModel->id = $batch_id;
    $batch = $batchModel->readOne();
    if (!$batch) {
        flash_message('Batch not found', 'error');
        redirect('batches.php');
    }
} elseif ($action == 'manage_students' && $batch_id > 0) {
    if (!can('batches.manage_students')) permission_denied('batches.php');
    $batchModel->id = $batch_id;
    $batch = $batchModel->readOne();
    if (!$batch) {
        flash_message('Batch not found', 'error');
        redirect('batches.php');
    }
    $enrollments = $enrollmentModel->getByBatch($batch_id);
    $registration_links = $linkModel->getByBatch($batch_id);
}

// Get classes for dropdowns
$classes = $classModel->readAll($accessible_class_ids, $can_access_all_classes_flag);

// Get instructors
// $instructors = $userModel->getUsersByRole('teacher');

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Management - <?php echo APP_NAME; ?></title>
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
            
            <!-- Flash Messages -->
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- List View -->
            <?php if ($action == 'list'): ?>
            
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100 d-flex align-items-center flex-wrap gap-16 justify-content-between">
                            <div>
                                <h5 class="mb-0">All Batches</h5>
                                <p class="text-sm text-secondary-light mb-0">Manage your class batches</p>
                            </div>
                            <a href="batches.php?action=add" class="btn btn-main text-sm btn-sm px-24 py-12 radius-8 d-flex align-items-center gap-2">
                                <i class="ph ph-plus me-1"></i>
                                Create New Batch
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0" id="batchesTable">
                                    <thead>
                                        <tr>
                                            <th>Batch Name</th>
                                            <th>Code</th>
                                            <th>Class/Course</th>
                                            <th>Instructor</th>
                                            <th>Start Date</th>
                                            <th>Students</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($batches)): ?>
                                            <?php foreach ($batches as $batch): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-10">
                                                        <div class="flex-grow-1">
                                                            <h6 class="text-md mb-0 fw-medium"><?php echo htmlspecialchars($batch['batch_name']); ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary text-primary-main text-sm px-20 py-9 radius-4 fw-medium">
                                                        <?php echo htmlspecialchars($batch['batch_code']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($batch['class_name']); ?></td>
                                                <td><?php echo htmlspecialchars($batch['instructor_name'] ?? 'Not Assigned'); ?></td>
                                                <td><?php echo $batch['start_date'] ? date('M d, Y', strtotime($batch['start_date'])) : 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-info text-info-main text-sm px-20 py-9 radius-4 fw-medium">
                                                        <?php echo $batch['active_students']; ?>/<?php echo $batch['max_students'] ?? '∞'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($batch['status'] == 'active'): ?>
                                                        <span class="badge text-sm fw-semibold text-success-600 bg-success px-20 py-9 radius-4 text-white">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge text-sm fw-semibold text-secondary-light bg-dark px-20 py-9 radius-4">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-10">
                                                        <a href="batches.php?action=view&id=<?php echo $batch['id']; ?>" 
                                                           class="bg-main-50 text-main-600 bg-hover-main-100 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                           title="View">
                                                            <i class="ph ph-eye"></i>
                                                        </a>
                                                        <a href="batches.php?action=edit&id=<?php echo $batch['id']; ?>" 
                                                           class="bg-success-100 text-success-main bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                           title="Edit">
                                                            <i class="ph ph-pencil"></i>
                                                        </a>
                                                        <a href="batch-students.php?batch_id=<?php echo $batch['id']; ?>" 
                                                           class="bg-info-focus text-info-main bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                           title="Manage Students">
                                                            <i class="ph ph-users"></i>
                                                        </a>
                                                        <button onclick="confirmDelete(<?php echo $batch['id']; ?>)" 
                                                                class="bg-danger-focus text-danger-main bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0"
                                                                title="Delete">
                                                            <i class="ph ph-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <i class="ph ph-folder-open text-gray-300" style="font-size: 4rem;"></i>
                                                    <p class="text-secondary-light mb-0 mt-3">No batches found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($action == 'add' || $action == 'edit'): ?>
            
            <!-- Add/Edit Form -->
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><?php echo $action == 'add' ? 'Create New Batch' : 'Edit Batch'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="batches.php">
                                <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                                <?php if ($action == 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $batchModel->id; ?>">
                                <?php endif; ?>

                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Batch Name <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" name="batch_name" 
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($batchModel->batch_name) : ''; ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Batch Code <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control radius-8" name="batch_code" 
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($batchModel->batch_code) : ''; ?>" required>
                                        <small class="text-secondary-light">Unique identifier for this batch</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Class/Course <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-select radius-8" name="class_id" required>
                                            <option value="">Select Class/Course</option>
                                            <?php foreach ($classes as $class): ?>
                                                <option value="<?php echo $class['id']; ?>" 
                                                    <?php echo ($action == 'edit' && $batchModel->class_id == $class['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Instructor
                                        </label>
                                        <select class="form-select radius-8" name="instructor_id">
                                            <option value="">Select Instructor</option>
                                            <?php foreach ($instructors as $instructor): ?>
                                                <option value="<?php echo $instructor['id']; ?>" 
                                                    <?php echo ($action == 'edit' && $batchModel->instructor_id == $instructor['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($instructor['full_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                        <textarea class="form-control radius-8" name="description" rows="3"><?php echo $action == 'edit' ? htmlspecialchars($batchModel->description) : ''; ?></textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Start Date</label>
                                        <input type="date" class="form-control radius-8" name="start_date" 
                                               value="<?php echo $action == 'edit' ? $batchModel->start_date : ''; ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">End Date</label>
                                        <input type="date" class="form-control radius-8" name="end_date" 
                                               value="<?php echo $action == 'edit' ? $batchModel->end_date : ''; ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Max Students</label>
                                        <input type="number" class="form-control radius-8" name="max_students" min="1" 
                                               value="<?php echo $action == 'edit' ? $batchModel->max_students : ''; ?>">
                                        <small class="text-secondary-light">Leave empty for unlimited</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Enrollment Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-select radius-8" name="enrollment_status" required>
                                            <option value="open" <?php echo ($action == 'edit' && $batchModel->enrollment_status == 'open') ? 'selected' : ''; ?>>Open</option>
                                            <option value="closed" <?php echo ($action == 'edit' && $batchModel->enrollment_status == 'closed') ? 'selected' : ''; ?>>Closed</option>
                                            <option value="full" <?php echo ($action == 'edit' && $batchModel->enrollment_status == 'full') ? 'selected' : ''; ?>>Full</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Meeting Schedule</label>
                                        <input type="text" class="form-control radius-8" name="meeting_schedule" 
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($batchModel->meeting_schedule) : ''; ?>" 
                                               placeholder="e.g., Mon-Wed-Fri 10:00 AM">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                            Status <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-select radius-8" name="status" required>
                                            <option value="active" <?php echo ($action == 'edit' && $batchModel->status == 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($action == 'edit' && $batchModel->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Zoom Meeting ID</label>
                                        <input type="text" class="form-control radius-8" name="zoom_meeting_id" 
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($batchModel->zoom_meeting_id) : ''; ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Zoom Meeting Link</label>
                                        <input type="url" class="form-control radius-8" name="zoom_meeting_link" 
                                               value="<?php echo $action == 'edit' ? htmlspecialchars($batchModel->zoom_meeting_link) : ''; ?>" 
                                               placeholder="https://zoom.us/j/...">
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <a href="batches.php" class="btn btn-outline-main rounded-pill px-24 py-12">
                                                <i class="ph ph-arrow-left me-2"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn btn-main rounded-pill px-24 py-12">
                                                <i class="ph ph-check me-2"></i><?php echo $action == 'add' ? 'Create Batch' : 'Update Batch'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($action == 'view' && $batch): ?>
            
            <!-- View Details -->
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100 d-flex align-items-center flex-wrap gap-16 justify-content-between">
                            <div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($batch['batch_name']); ?></h5>
                                <p class="text-sm text-secondary-light mb-0">Batch Code: <?php echo htmlspecialchars($batch['batch_code']); ?></p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="batches.php?action=edit&id=<?php echo $batch['id']; ?>" class="btn btn-outline-main rounded-pill px-20 py-11">
                                    <i class="ph ph-pencil me-2"></i>Edit Batch
                                </a>
                                <a href="batch-students.php?batch_id=<?php echo $batch['id']; ?>" class="btn btn-main rounded-pill px-20 py-11">
                                    <i class="ph ph-users me-2"></i>Manage Students
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-main-50 text-main-600 rounded-circle">
                                            <i class="ph ph-book-open text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Class/Course</span>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($batch['class_name']); ?></h6>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($batch['instructor_name']): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-success-50 text-success-600 rounded-circle">
                                            <i class="ph ph-user text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Instructor</span>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($batch['instructor_name']); ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($batch['start_date']): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-info-50 text-info-600 rounded-circle">
                                            <i class="ph ph-calendar text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Duration</span>
                                            <h6 class="mb-0">
                                                <?php echo date('M d, Y', strtotime($batch['start_date'])); ?>
                                                <?php if ($batch['end_date']): ?>
                                                    - <?php echo date('M d, Y', strtotime($batch['end_date'])); ?>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-warning-50 text-warning-600 rounded-circle">
                                            <i class="ph ph-users text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Students Enrolled</span>
                                            <h6 class="mb-0">
                                                <?php echo $batch['active_students']; ?> 
                                                <?php if ($batch['max_students']): ?>
                                                    / <?php echo $batch['max_students']; ?>
                                                <?php endif; ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($batch['meeting_schedule']): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-purple-50 text-purple-600 rounded-circle">
                                            <i class="ph ph-clock text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Schedule</span>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($batch['meeting_schedule']); ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="w-44-px h-44-px d-flex justify-content-center align-items-center bg-danger-50 text-danger-600 rounded-circle">
                                            <i class="ph ph-clipboard-text text-xl"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-secondary-light text-sm mb-1 d-block">Enrollment Status</span>
                                            <h6 class="mb-0">
                                                <span class="badge <?php 
                                                    echo $batch['enrollment_status'] == 'open' ? 'bg-success text-success-main' : 
                                                        ($batch['enrollment_status'] == 'full' ? 'bg-warning text-warning-main' : 'bg-danger text-danger-main'); 
                                                ?>">
                                                    <?php echo ucfirst($batch['enrollment_status']); ?>
                                                </span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($batch['description']): ?>
                                <div class="col-12">
                                    <div class="border-top pt-3 mt-2">
                                        <h6 class="mb-2">Description</h6>
                                        <p class="text-secondary-light mb-0"><?php echo (($batch['description'])); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($batch['zoom_meeting_link']): ?>
                                <div class="col-12">
                                    <div class="border-top pt-3">
                                        <h6 class="mb-2">Zoom Meeting</h6>
                                        <a href="<?php echo htmlspecialchars($batch['zoom_meeting_link']); ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-primary rounded-pill px-20 py-11">
                                            <i class="ph ph-video-camera me-2"></i>Join Zoom Meeting
                                        </a>
                                        <?php if ($batch['zoom_meeting_id']): ?>
                                            <span class="text-secondary-light ms-3">
                                                Meeting ID: <?php echo htmlspecialchars($batch['zoom_meeting_id']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($action == 'manage_students' && $batch): ?>
            
            <!-- Manage Students -->
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($batch['batch_name']); ?></h5>
                                    <p class="text-sm text-secondary-light mb-0">Student Management</p>
                                </div>
                                <button type="button" class="btn btn-main rounded-pill px-20 py-11" data-bs-toggle="modal" data-bs-target="#generateLinkModal">
                                    <i class="ph ph-link me-2"></i>Generate Registration Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Links -->
                <?php if (!empty($registration_links)): ?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100">
                            <h6 class="text-lg mb-0">Registration Links</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Link</th>
                                            <th>Type</th>
                                            <th>Usage</th>
                                            <th>Expires</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($registration_links as $link): ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" 
                                                       value="<?php echo BASE_URL . 'batch-registration.php?token=' . $link['link_token']; ?>" 
                                                       readonly onclick="this.select();">
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-200 text-secondary-light px-20 py-9 radius-4">
                                                    <?php echo ($link['link_type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $link['current_uses']; ?> / <?php echo $link['max_uses'] ?? '∞'; ?></td>
                                            <td><?php echo $link['expires_at'] ? date('M d, Y', strtotime($link['expires_at'])) : 'Never'; ?></td>
                                            <td>
                                                <?php if ($link['is_active']): ?>
                                                    <span class="badge text-sm fw-semibold text-success-600 bg-success px-20 py-9 radius-4 text-white">Active</span>
                                                <?php else: ?>
                                                    <span class="badge text-sm fw-semibold text-secondary-light bg-dark px-20 py-9 radius-4">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-main rounded-pill px-20 py-9" 
                                                        onclick="copyToClipboard('<?php echo BASE_URL . 'batch-registration.php?token=' . $link['link_token']; ?>')">
                                                    <i class="ph ph-copy me-1"></i>Copy
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Enrolled Students -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom border-gray-100">
                            <h6 class="text-lg mb-0">Enrolled Students</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table bordered-table mb-0" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Enrollment Date</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($enrollments)): ?>
                                            <?php foreach ($enrollments as $enrollment): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-10">
                                                        <div class="flex-grow-1">
                                                            <h6 class="text-md mb-0 fw-medium"><?php echo htmlspecialchars($enrollment['full_name']); ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($enrollment['email']); ?></td>
                                                <td><?php echo htmlspecialchars($enrollment['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                                <td>
                                                    <?php
                                                    $status_class = 'bg-neutral text-secondary-light';
                                                    if ($enrollment['enrollment_status'] == 'active') {
                                                        $status_class = 'text-success-600 bg-success text-white';
                                                    } elseif ($enrollment['enrollment_status'] == 'pending') {
                                                        $status_class = 'text-warning-600 bg-warning-100';
                                                    }
                                                    ?>
                                                    <span class="badge text-sm fw-semibold <?php echo $status_class; ?> px-20 py-9 radius-4">
                                                        <?php echo ucfirst($enrollment['enrollment_status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height: 8px;">
                                                            <div class="progress-bar bg-main-600" role="progressbar" 
                                                                 style="width: <?php echo $enrollment['progress_percentage']; ?>%">
                                                            </div>
                                                        </div>
                                                        <span class="text-sm text-secondary-light"><?php echo number_format($enrollment['progress_percentage'], 1); ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" onchange="updateStatus(<?php echo $enrollment['id']; ?>, this.value)">
                                                        <option value="">Change Status</option>
                                                        <option value="active">Active</option>
                                                        <option value="suspended">Suspended</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="dropped">Dropped</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="ph ph-users text-gray-300" style="font-size: 4rem;"></i>
                                                    <p class="text-secondary-light mb-0 mt-3">No students enrolled yet</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generate Link Modal -->
            <div class="modal fade" id="generateLinkModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Generate Registration Link</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="generate_link">
                                <input type="hidden" name="batch_id" value="<?php echo $batch_id; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Link Type</label>
                                    <select name="link_type" class="form-select radius-8" required>
                                        <option value="public">Public - Anyone can use</option>
                                        <option value="private">Private - Limited access</option>
                                        <option value="one-time">One-time use</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Max Uses (optional)</label>
                                    <input type="number" name="max_uses" class="form-control radius-8" min="1" placeholder="Leave empty for unlimited">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Expiration Date (optional)</label>
                                    <input type="datetime-local" name="expires_at" class="form-control radius-8">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-main rounded-pill px-20 py-11" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-main rounded-pill px-20 py-11">
                                    <i class="ph ph-link me-2"></i>Generate Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endif; ?>

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
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="assets/js/phosphor-icon.js"></script>
    <script src="assets/js/file-upload.js"></script>
    <script src="assets/js/plyr.js"></script>
    <script src="assets/js/full-calendar.js"></script>
    <script src="assets/js/jquery-ui.js"></script>
    <script src="assets/js/editor-quill.js"></script>
    <script src="assets/js/apexcharts.min.js"></script>
    <script src="assets/js/calendar.js"></script>
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
    $(document).ready(function() {
        $('#batchesTable, #studentsTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true
        });
    });

    function confirmDelete(batchId) {
        if (confirm('Are you sure you want to delete this batch? This will also delete all enrollments.')) {
            window.location.href = 'batches.php?action=delete&id=' + batchId;
        }
    }

    function updateStatus(enrollmentId, newStatus) {
        if (!newStatus) return;
        
        if (confirm('Are you sure you want to change the student status to "' + newStatus + '"?')) {
            $.post('batches.php', {
                action: 'update_enrollment_status',
                enrollment_id: enrollmentId,
                new_status: newStatus
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to update status. Please try again.');
                }
            }, 'json');
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-body bg-success text-white rounded">
                        <i class="ph ph-check-circle me-2"></i>Link copied to clipboard!
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }).catch(() => {
            alert('Failed to copy link. Please copy manually.');
        });
    }
    </script>
</body>

</html>
