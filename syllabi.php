<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Syllabus.php';
require_once 'includes/functions.php';
require_once 'classes/User.php'; // Include User class
require_once 'classes/ClassModel.php'; // Include ClassModel for class filtering

// Check if user is logged in
require_roles(['super_admin', 'organization_admin', 'school_admin', 'teacher', 'solo_student']);

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$syllabus = new Syllabus($db);
$user = new User($db); // Instantiate User class
$classModel = new ClassModel($db); // Instantiate ClassModel

// Get accessible class IDs for the current user
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');

// If the user can access all classes, then do not filter by individual class IDs.
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$syllabus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;
$class_filter = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Handle AJAX request for subjects by class
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_subjects' && isset($_GET['class_id'])) {
    $class_id = (int)$_GET['class_id'];
    // Ensure the user has access to this class before fetching subjects for it
    if ($can_access_all_classes_flag || in_array($class_id, $accessible_class_ids)) {
        $subjects_stmt = $syllabus->getSubjectsByClass($class_id);
        $subjects = [];
        while ($row = $subjects_stmt->fetch()) {
            $subjects[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
    } else {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Access denied to this class.']);
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $syllabus->syllabus_title = sanitize_input($_POST['syllabus_title']);
                $syllabus->subject_id = (int)$_POST['subject_id'];
                $syllabus->description = sanitize_input($_POST['description']);
                $syllabus->objectives = sanitize_input($_POST['objectives']);
                $syllabus->duration_weeks = (int)$_POST['duration_weeks'];

                // Validate
                $errors = [];
                if (empty($syllabus->syllabus_title)) {
                    $errors[] = 'Syllabus title is required.';
                }
                if (empty($syllabus->subject_id)) {
                    $errors[] = 'Please select a subject.';
                }
                if ($syllabus->duration_weeks < 1) {
                    $errors[] = 'Duration must be at least 1 week.';
                }

                if (empty($errors)) {
                    if ($syllabus->create()) {
                        flash_message('Syllabus created successfully!', 'success');
                        redirect('syllabi.php');
                    } else {
                        flash_message('Error creating syllabus.', 'error');
                    }
                }
                break;

            case 'update':
                $syllabus->id = (int)$_POST['id'];
                $syllabus->syllabus_title = sanitize_input($_POST['syllabus_title']);
                $syllabus->subject_id = (int)$_POST['subject_id'];
                $syllabus->description = sanitize_input($_POST['description']);
                $syllabus->objectives = sanitize_input($_POST['objectives']);
                $syllabus->duration_weeks = (int)$_POST['duration_weeks'];

                // Validate
                $errors = [];
                if (empty($syllabus->syllabus_title)) {
                    $errors[] = 'Syllabus title is required.';
                }
                if (empty($syllabus->subject_id)) {
                    $errors[] = 'Please select a subject.';
                }
                if ($syllabus->duration_weeks < 1) {
                    $errors[] = 'Duration must be at least 1 week.';
                }

                if (empty($errors)) {
                    if ($syllabus->update()) {
                        flash_message('Syllabus updated successfully!', 'success');
                        redirect('syllabi.php');
                    } else {
                        flash_message('Error updating syllabus.', 'error');
                    }
                }
                break;

            case 'delete':
                $syllabus->id = (int)$_POST['id'];
                if ($syllabus->delete()) {
                    flash_message('Syllabus deleted successfully!', 'success');
                } else {
                    flash_message('Error deleting syllabus.', 'error');
                }
                redirect('syllabi.php');
                break;
        }
    }
}

// Get syllabus data for edit
if ($action == 'edit' && $syllabus_id > 0) {
    $syllabus->id = $syllabus_id;
    if (!$syllabus->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids)) {
        flash_message('Syllabus not found or you do not have permission to view it.', 'error');
        redirect('syllabi.php');
    }
    // Explicit authorization check for editing a syllabus
    if (!$can_access_all_classes_flag && !empty($syllabus->class_id) && !$user->hasAccessToClass($current_user['id'], $syllabus->class_id)) {
        flash_message('You do not have permission to edit this syllabus.', 'error');
        redirect('syllabi.php');
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
    <!-- Title -->
    <title>Syllabi Management - <?php echo APP_NAME; ?></title>
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
                    <li><span class="text-main-600 fw-normal text-15">
                        <?php 
                        switch($action) {
                            case 'add': echo 'Add Syllabus'; break;
                            case 'edit': echo 'Edit Syllabus'; break;
                            case 'view': echo 'View Syllabus'; break;
                            default: echo 'Syllabi'; break;
                        }
                        ?>
                    </span></li>
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

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <!-- Syllabi List -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <div class="flex-align gap-16 flex-wrap">
                                <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                      <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">
                                        All Syllabi (<?php 
                                            if ($subject_filter) {
                                                $stmt = $syllabus->readBySubject($subject_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                            } else {
                                                $stmt = $syllabus->read($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                            }
                                            echo $stmt->rowCount();
                                        ?>)
                                      </button>
                                    </li>
                                </ul>
                                
                                <!-- Subject Filter -->
                                <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-8 focus-border-main-600">
                                    <span class="text-lg"><i class="ph ph-funnel-simple"></i></span>
                                    <select class="form-control px-8 py-12 border-0 text-inherit rounded-4 text-center" id="subjectFilter" onchange="filterBySubject()">
                                        <option value="">All Subjects</option>
                                        <?php
                                        $subjects_stmt = $syllabus->getActiveSubjects($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                        while ($subject_row = $subjects_stmt->fetch()) {
                                            $selected = ($subject_filter == $subject_row['id']) ? 'selected' : '';
                                            echo "<option value='" . $subject_row['id'] . "' $selected>" . 
                                                 htmlspecialchars($subject_row['class_name']) . " - " .
                                                 htmlspecialchars($subject_row['subject_name']) . " (" . 
                                                 htmlspecialchars($subject_row['subject_code']) . ")</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?php if ($user_role === 'super_admin'): ?>
                            <a href="syllabi.php?action=add" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                <span class="d-flex text-md"><i class="ph ph-plus"></i></span> 
                                Create New Syllabus
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    if ($subject_filter) {
                                        $stmt = $syllabus->readBySubject($subject_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                    } else {
                                        $stmt = $syllabus->read($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                    }
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='lectures.php?syllabus=" . $row['id'] . "' class='bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8'>";
                                            echo "<div class='text-center'>";
                                            echo "<i class='ph ph-list-alt text-6xl text-main-600 mb-3'></i>";
                                            echo "<h6 class='text-main-600 mb-0'>" . htmlspecialchars($row['syllabus_title']) . "</h6>";
                                            echo "</div>";
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<div class='flex-align gap-8 mb-16'>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill bg-info-50 text-info-600'>" . $row['duration_weeks'] . " weeks</span>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600'>" . htmlspecialchars($row['subject_code']) . "</span>";
                                            echo "</div>";
                                            echo "<h5 class='mb-0'><a href='lectures.php?syllabus=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['syllabus_title']) . "</a></h5>";

                                            echo "<div class='flex-align gap-8 flex-wrap mt-16'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-graduation-cap text-main-600'></i>";
                                            echo "<span class='text-gray-600 text-13'>Class: " . htmlspecialchars($row['class_name']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            echo "<div class='flex-align gap-8 flex-wrap mt-8'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-bookmarks text-main-600'></i>";
                                            echo "<span class='text-gray-600 text-13'>Subject: " . htmlspecialchars($row['subject_name']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            if (!empty($row['description'])) {
                                                $description = strlen($row['description']) > 80 ? substr($row['description'], 0, 80) . '...' : $row['description'];
                                                echo "<p class='text-gray-600 text-13 mt-12 mb-16'>" . htmlspecialchars($description) . "</p>";
                                            }

                                            if (!empty($row['objectives'])) {
                                                $objectives = strlen($row['objectives']) > 60 ? substr($row['objectives'], 0, 60) . '...' : $row['objectives'];
                                                echo "<div class='bg-main-25 p-8 rounded-4 mt-12'>";
                                                echo "<small class='text-gray-600'><strong>Objectives:</strong> " . htmlspecialchars($objectives) . "</small>";
                                                echo "</div>";
                                            }

                                            echo "<div class='mt-16'>";
                                            if ($user_role === 'super_admin') {
                                                echo "<div class='d-flex flex-wrap gap-8 align-items-center mb-8 syllabus-action-buttons'>";
                                                echo "<a href='syllabi.php?action=edit&id=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-9 px-16 text-14 fw-medium syllabus-btn-edit'>";
                                                echo "<i class='ph ph-pencil me-2'></i>Edit</a>";
                                                $delete_title_json = json_encode($row['syllabus_title'], JSON_HEX_APOS | JSON_HEX_QUOT);
                                                echo "<button type='button' class='btn btn-danger rounded-pill py-9 px-16 text-14 fw-medium syllabus-btn-delete' onclick='confirmDelete(" . $row['id'] . ", " . $delete_title_json . ")'>";
                                                echo "<i class='ph ph-trash me-2'></i>Delete</button>";
                                                echo "<a href='lectures.php?syllabus=" . $row['id'] . "' class='btn btn-secondary rounded-pill py-9 px-16 text-14 fw-medium syllabus-btn-view'>";
                                                echo "<i class='ph ph-list me-2'></i>View Lectures</a>";
                                                echo "</div>";
                                                echo "<div class='d-flex justify-content-between align-items-center'>";
                                                echo "<span class='badge " . ($row['status'] === 'active' ? 'bg-success' : 'bg-danger') . " py-6 px-12 text-13'>" . ucfirst($row['status']) . "</span>";
                                                echo "</div>";
                                            } else {
                                                echo "<div class='d-flex flex-wrap gap-8 align-items-center mb-12'>";
                                                echo "<a href='lectures.php?syllabus=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-9 px-16 text-14 fw-medium'>";
                                                echo "<i class='ph ph-list me-2'></i>View Lectures</a>";
                                                echo "<span class='badge " . ($row['status'] === 'active' ? 'bg-success' : 'bg-danger') . " py-6 px-12 text-13 ms-auto'>" . ucfirst($row['status']) . "</span>";
                                                echo "</div>";
                                            }
                                            echo "</div>";
                                            
                                            echo "<div class='flex-align gap-8 mt-12'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-calendar text-gray-400'></i>";
                                            echo "<span class='text-gray-400 text-13'>Created: " . date('M d, Y', strtotime($row['created_at'])) . "</span>";
                                            echo "</div>";
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
                                        echo "<i class='ph ph-list-alt text-6xl text-gray-300'></i>";
                                        echo "</div>";
                                        if ($subject_filter) {
                                            echo "<h5 class='text-gray-600 mb-8'>No Syllabi Found for This Subject</h5>";
                                            echo "<p class='text-gray-400'>No syllabi have been created for the selected subject yet.";
                                            if ($user_role === 'super_admin') {
                                                echo " <a href='syllabi.php?action=add' class='text-main-600'>Create the first syllabus</a>";
                                            }
                                            echo "</p>";
                                            echo "<a href='syllabi.php' class='btn btn-outline-main rounded-pill py-9 mt-3'>View All Syllabi</a>";
                                        } else {
                                            echo "<h5 class='text-gray-600 mb-8'>No Syllabi Found</h5>";
                                            echo "<p class='text-gray-400'>You don't have any syllabi assigned yet.";
                                            if ($user_role === 'super_admin') {
                                                echo " <a href='syllabi.php?action=add' class='text-main-600'>Create your first syllabus</a>";
                                            }
                                            echo "</p>";
                                        }
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($action == 'add' || $action == 'edit'): 
                // Only authorized users can add/edit syllabi
                if ($user_role !== 'super_admin') {
                    flash_message('You do not have permission to manage syllabi.', 'error');
                    redirect('syllabi.php');
                }
            ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0"><?php echo $action == 'add' ? 'Create New Syllabus' : 'Edit Syllabus'; ?></h5>        
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Syllabus Details">
                            <i class="ph-fill ph-question"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $syllabus->id; ?>">
                            <?php endif; ?>
                            
                            <div class="row gy-20">
                                <div class="col-12">
                                    <div class="row g-20">
                                        <div class="col-sm-12">
                                            <label for="syllabus_title" class="h5 mb-8 fw-semibold font-heading">Syllabus Title <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="text-counter placeholder-13 form-control py-11 pe-76" maxlength="150" id="syllabus_title" name="syllabus_title" 
                                                       value="<?php echo isset($syllabus->syllabus_title) ? htmlspecialchars($syllabus->syllabus_title) : (isset($_POST['syllabus_title']) ? htmlspecialchars($_POST['syllabus_title']) : ''); ?>" 
                                                       placeholder="Enter syllabus title" required>
                                                <div class="text-gray-400 position-absolute inset-inline-end-0 top-50 translate-middle-y me-16">
                                                    <span id="current">0</span>
                                                    <span id="maximum">/ 150</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="class_select" class="h5 mb-8 fw-semibold font-heading">Class <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <select id="class_select" class="form-select py-9 placeholder-13 text-15" onchange="loadSubjects(this.value)">
                                                    <option value="" disabled selected>Select a class first</option>
                                                    <?php
                                                    $classes_stmt = $syllabus->getActiveClasses($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                                    while ($class_row = $classes_stmt->fetch()) {
                                                        $selected = '';
                                                        if ($action == 'edit' && isset($syllabus->class_id) && $syllabus->class_id == $class_row['id']) {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option value='" . $class_row['id'] . "' $selected>" . 
                                                             htmlspecialchars($class_row['class_name']) . " (" . 
                                                             htmlspecialchars($class_row['class_code']) . ")</option>";
                                                    }
                                                    ?>
                                                </select>                                            
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="subject_id" class="h5 mb-8 fw-semibold font-heading">Subject <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <select id="subject_id" name="subject_id" class="form-select py-9 placeholder-13 text-15" required>
                                                    <option value="" disabled selected>Select a subject</option>
                                                    <?php if ($action == 'edit' && isset($syllabus->class_id)): 
                                                        // Load subjects for the pre-selected class if editing
                                                        $subjects_for_edit_stmt = $syllabus->getSubjectsByClass($syllabus->class_id, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                                        while ($subject_row_edit = $subjects_for_edit_stmt->fetch()) {
                                                            $selected = ($syllabus->subject_id == $subject_row_edit['id']) ? 'selected' : '';
                                                            echo "<option value='" . $subject_row_edit['id'] . "' $selected>" . 
                                                                 htmlspecialchars($subject_row_edit['subject_name']) . " (" . 
                                                                 htmlspecialchars($subject_row_edit['subject_code']) . ")</option>";
                                                        }
                                                    endif; ?>
                                                </select>                                            
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="duration_weeks" class="h5 mb-8 fw-semibold font-heading">Duration (Weeks) <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="number" class="form-control py-11 placeholder-13" id="duration_weeks" name="duration_weeks" 
                                                       value="<?php echo isset($syllabus->duration_weeks) ? $syllabus->duration_weeks : (isset($_POST['duration_weeks']) ? $_POST['duration_weeks'] : '1'); ?>" 
                                                       min="1" max="52" placeholder="Enter duration in weeks" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="status" class="h5 mb-8 fw-semibold font-heading">Status</label>
                                            <div class="position-relative">
                                                <select id="status" name="status" class="form-select py-9 placeholder-13 text-15">
                                                    <option value="active" <?php echo (isset($syllabus->status) && $syllabus->status == 'active') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo (isset($syllabus->status) && $syllabus->status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>                                            
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="description" class="h5 mb-8 fw-semibold font-heading">Description</label>
                                            <textarea class="form-control py-11 placeholder-13" id="description" name="description" rows="4" placeholder="Enter syllabus description"><?php echo isset($syllabus->description) ? htmlspecialchars($syllabus->description) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="objectives" class="h5 mb-8 fw-semibold font-heading">Learning Objectives</label>
                                            <textarea class="form-control py-11 placeholder-13" id="objectives" name="objectives" rows="4" placeholder="List the key learning objectives for this syllabus..."><?php echo isset($syllabus->objectives) ? htmlspecialchars($syllabus->objectives) : (isset($_POST['objectives']) ? htmlspecialchars($_POST['objectives']) : ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="flex-align justify-content-end gap-8">
                                        <a href="syllabi.php" class="btn btn-outline-main rounded-pill py-9">Cancel</a>
                                        <button type="submit" class="btn btn-main rounded-pill py-9">
                                            <?php echo $action == 'add' ? 'Create Syllabus' : 'Update Syllabus'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to set the status of syllabus "<span id="syllabusTitle"></span>" to inactive?</p>
                    <p class="text-danger"><small>This will make the syllabus and its lectures unavailable to users. You can reactivate it later.</small></p>
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

    <style>
        /* Ensure ALL buttons are always visible - override any hover-only rules */
        .syllabus-action-buttons {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            max-width: 100% !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        
        .syllabus-btn-edit,
        .syllabus-btn-delete,
        .syllabus-btn-view,
        .syllabus-action-buttons .btn,
        .syllabus-action-buttons button,
        .syllabus-action-buttons a.btn {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 10 !important;
            flex-shrink: 0 !important;
            white-space: nowrap !important;
            width: auto !important;
            min-width: auto !important;
            max-width: none !important;
        }
        
        /* Override any card hover rules that might hide buttons */
        .card .syllabus-action-buttons,
        .card .syllabus-btn-edit,
        .card .syllabus-btn-delete,
        .card .syllabus-btn-view {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .card .syllabus-action-buttons .btn,
        .card .syllabus-action-buttons button,
        .card .syllabus-action-buttons a.btn {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure buttons stay visible on card hover */
        .card:hover .syllabus-action-buttons,
        .card:hover .syllabus-btn-edit,
        .card:hover .syllabus-btn-delete,
        .card:hover .syllabus-btn-view {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .card:hover .syllabus-action-buttons .btn,
        .card:hover .syllabus-action-buttons button,
        .card:hover .syllabus-action-buttons a.btn {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Prevent any overflow hiding */
        .card-body {
            overflow: visible !important;
        }
        
        .card {
            overflow: visible !important;
        }
        
        /* Ensure card body doesn't clip content */
        .card .card-body {
            overflow: visible !important;
            min-height: auto !important;
        }
        
        /* Force visibility for all button types - especially Delete and View buttons */
        button.syllabus-btn-delete,
        a.syllabus-btn-edit,
        a.syllabus-btn-view {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Specifically target Delete and View buttons to always be visible */
        .syllabus-btn-delete,
        .syllabus-btn-view {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        
        /* Override any :not(:hover) rules that might hide buttons */
        .card:not(:hover) .syllabus-btn-delete,
        .card:not(:hover) .syllabus-btn-view,
        .card:not(:hover) button.syllabus-btn-delete,
        .card:not(:hover) a.syllabus-btn-view {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure buttons are visible even without card hover */
        .syllabus-action-buttons button.syllabus-btn-delete,
        .syllabus-action-buttons a.syllabus-btn-view {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Override any nth-child or sibling selectors that might hide buttons */
        .syllabus-action-buttons .btn:nth-child(2),
        .syllabus-action-buttons .btn:nth-child(3),
        .syllabus-action-buttons button:nth-child(2),
        .syllabus-action-buttons a:nth-child(3) {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Change hover effect from outline to solid color */
        .syllabus-btn-edit:hover,
        .syllabus-btn-edit.btn-outline-main:hover {
            background-color: hsl(var(--main)) !important;
            border-color: hsl(var(--main)) !important;
            color: hsl(var(--white)) !important;
        }
        
        .syllabus-btn-delete:hover,
        .syllabus-btn-delete.btn-outline-danger:hover {
            background-color: var(--danger-600) !important;
            border-color: var(--danger-600) !important;
            color: hsl(var(--white)) !important;
        }
        
        .syllabus-btn-view:hover,
        .syllabus-btn-view.btn-outline-secondary:hover {
            background-color: var(--gray-600) !important;
            border-color: var(--gray-600) !important;
            color: hsl(var(--white)) !important;
        }
    </style>

    <script>
        function confirmDelete(id, title) {
            document.getElementById('deleteId').value = id;
            document.getElementById('syllabusTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function filterBySubject() {
            const subjectId = document.getElementById('subjectFilter').value;
            if (subjectId) {
                window.location.href = 'syllabi.php?subject=' + subjectId;
            } else {
                window.location.href = 'syllabi.php';
            }
        }

        function loadSubjects(classId) {
            const subjectSelect = document.getElementById('subject_id');
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            
            if (!classId) {
                subjectSelect.innerHTML = '<option value="">Select a subject</option>';
                return;
            }

            fetch(`syllabi.php?ajax=get_subjects&class_id=${classId}`)
                .then(response => response.json())
                .then(subjects => {
                    subjectSelect.innerHTML = '<option value="">Select a subject</option>';
                    subjects.forEach(subject => {
                        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.subject_name} (${subject.subject_code})</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                });
        }

        // Character counter for syllabus title input
        document.getElementById('syllabus_title').addEventListener('input', function() {
            const current = this.value.length;
            document.getElementById('current').textContent = current;
        });

        // Initialize character counter and subjects on page load
        document.addEventListener('DOMContentLoaded', function() {
            const syllabusTitle = document.getElementById('syllabus_title');
            if (syllabusTitle) {
                const current = syllabusTitle.value.length;
                document.getElementById('current').textContent = current;
            }

            const classSelectElement = document.getElementById('class_select');
            const subjectSelectElement = document.getElementById('subject_id');

            // Initial load for subjects if a class is pre-selected (edit mode)
            if (classSelectElement && classSelectElement.value) {
                loadSubjects(classSelectElement.value);
            }
            
            // Force visibility of Delete and View Lectures buttons
            function ensureButtonsVisible() {
                const deleteButtons = document.querySelectorAll('.syllabus-btn-delete');
                const viewButtons = document.querySelectorAll('.syllabus-btn-view');
                
                deleteButtons.forEach(function(btn) {
                    btn.style.display = 'inline-flex';
                    btn.style.visibility = 'visible';
                    btn.style.opacity = '1';
                });
                
                viewButtons.forEach(function(btn) {
                    btn.style.display = 'inline-flex';
                    btn.style.visibility = 'visible';
                    btn.style.opacity = '1';
                });
            }
            
            // Run immediately and also set up observer
            ensureButtonsVisible();
            
            // Use MutationObserver to ensure buttons stay visible
            const observer = new MutationObserver(function(mutations) {
                ensureButtonsVisible();
            });
            
            // Observe all syllabus action button containers
            document.querySelectorAll('.syllabus-action-buttons').forEach(function(container) {
                observer.observe(container, {
                    attributes: true,
                    childList: true,
                    subtree: true,
                    attributeFilter: ['style', 'class']
                });
            });
        });
    </script>
</body>
</html>