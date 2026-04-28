<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Syllabus.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';

// Check if user is logged in
require_roles(['super_admin', 'organization_admin', 'school_admin', 'teacher', 'solo_student']);

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$syllabus = new Syllabus($db);
$user = new User($db);
$classModel = new ClassModel($db);

// Get accessible class IDs for the current user
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$syllabus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$course_filter = isset($_GET['course']) ? (int)$_GET['course'] : 0;

// Prevent non-super_admin from accessing add/edit views via GET
if (($action === 'add' || $action === 'edit') && $user_role !== 'super_admin') {
    flash_message('You do not have permission to manage syllabi.', 'error');
    redirect('course-syllabi.php');
}

// Get course details if filtering
$course_details = null;
if ($course_filter > 0) {
    $classModel->id = $course_filter;
    if ($classModel->readOne()) {
        $course_details = [
            'id' => $classModel->id,
            'name' => $classModel->class_name,
            'code' => $classModel->class_code,
            'registration_open' => $classModel->registration_open
        ];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                if ($user_role !== 'super_admin') { flash_message('You do not have permission to create syllabi.', 'error'); redirect('course-syllabi.php'); }
                $syllabus->syllabus_title = sanitize_input($_POST['syllabus_title']);
                $syllabus->class_id = (int)$_POST['course_id'];
                $syllabus->subject_id = null; // Courses don't use subjects
                $syllabus->description = sanitize_input($_POST['description']);
                $syllabus->objectives = sanitize_input($_POST['objectives']);
                $syllabus->duration_weeks = (int)$_POST['duration_weeks'];

                // Validate
                $errors = [];
                if (empty($syllabus->syllabus_title)) {
                    $errors[] = 'Syllabus title is required.';
                }
                if (empty($syllabus->class_id)) {
                    $errors[] = 'Please select a course.';
                }
                if ($syllabus->duration_weeks < 1) {
                    $errors[] = 'Duration must be at least 1 week.';
                }

                if (empty($errors)) {
                    if ($syllabus->create()) {
                        flash_message('Course syllabus created successfully!', 'success');
                        redirect('course-syllabi.php?course=' . $syllabus->class_id);
                    } else {
                        flash_message('Error creating syllabus.', 'error');
                    }
                }
                break;

            case 'update':
                if ($user_role !== 'super_admin') { flash_message('You do not have permission to update syllabi.', 'error'); redirect('course-syllabi.php'); }
                $syllabus->id = (int)$_POST['id'];
                $syllabus->syllabus_title = sanitize_input($_POST['syllabus_title']);
                $syllabus->class_id = (int)$_POST['course_id'];
                $syllabus->subject_id = null;
                $syllabus->description = sanitize_input($_POST['description']);
                $syllabus->objectives = sanitize_input($_POST['objectives']);
                $syllabus->duration_weeks = (int)$_POST['duration_weeks'];

                // Validate
                $errors = [];
                if (empty($syllabus->syllabus_title)) {
                    $errors[] = 'Syllabus title is required.';
                }
                if (empty($syllabus->class_id)) {
                    $errors[] = 'Please select a course.';
                }
                if ($syllabus->duration_weeks < 1) {
                    $errors[] = 'Duration must be at least 1 week.';
                }

                if (empty($errors)) {
                    if ($syllabus->update()) {
                        flash_message('Course syllabus updated successfully!', 'success');
                        redirect('course-syllabi.php?course=' . $syllabus->class_id);
                    } else {
                        flash_message('Error updating syllabus.', 'error');
                    }
                }
                break;

            case 'delete':
                if ($user_role !== 'super_admin') { flash_message('You do not have permission to delete syllabi.', 'error'); redirect('course-syllabi.php'); }
                $syllabus->id = (int)$_POST['id'];
                if ($syllabus->delete()) {
                    flash_message('Course syllabus deleted successfully!', 'success');
                } else {
                    flash_message('Error deleting syllabus.', 'error');
                }
                redirect('course-syllabi.php' . ($course_filter ? '?course=' . $course_filter : ''));
                break;
        }
    }
}

// Get syllabus data for edit
if ($action == 'edit' && $syllabus_id > 0) {
    $syllabus->id = $syllabus_id;
    if (!$syllabus->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids)) {
        flash_message('Syllabus not found or you do not have permission to view it.', 'error');
        redirect('course-syllabi.php');
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
    <title>Course Syllabi - <?php echo APP_NAME; ?></title>
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
                    <li><a href="courses.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Courses</a></li>
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">
                            <?php
                            if ($course_details) {
                                echo htmlspecialchars($course_details['name']) . ' - ';
                            }
                            switch ($action) {
                                case 'add':
                                    echo 'Add Syllabus';
                                    break;
                                case 'edit':
                                    echo 'Edit Syllabus';
                                    break;
                                default:
                                    echo 'Syllabi';
                                    break;
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
                <!-- Course Info Card (if filtering by course) -->
                <?php if ($course_details): ?>
                    <div class="card mb-24">
                        <div class="card-body">
                            <div class="flex-between flex-wrap gap-16">
                                <div>
                                    <h4 class="mb-2"><?php echo htmlspecialchars($course_details['name']); ?></h4>
                                    <p class="text-gray-600 mb-0">Course Code: <?php echo htmlspecialchars($course_details['code']); ?></p>
                                </div>
                                <div class="flex-align gap-8">
                                    <span class="py-2 px-12 rounded-pill <?php echo $course_details['registration_open'] ? 'bg-success' : 'bg-warning'; ?> text-white">
                                        Registration: <?php echo $course_details['registration_open'] ? 'Open' : 'Closed'; ?>
                                    </span>
                                    <a href="courses.php" class="btn btn-outline-main rounded-pill py-6 px-16">
                                        <i class="ph ph-arrow-left me-2"></i>Back to Courses
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Syllabi List -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <div class="flex-align gap-16 flex-wrap">
                                <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">
                                            All Syllabi (<?php
                                                            if ($course_filter) {
                                                                $stmt = $syllabus->readByCourse($course_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                                            } else {
                                                                $stmt = $syllabus->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course');
                                                            }
                                                            echo $stmt->rowCount();
                                                            ?>)
                                        </button>
                                    </li>
                                </ul>

                                <?php if (!$course_filter): ?>
                                    <!-- Course Filter -->
                                    <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-8 focus-border-main-600">
                                        <span class="text-lg"><i class="ph ph-funnel-simple"></i></span>
                                        <select class="form-control px-8 py-12 border-0 text-inherit rounded-4 text-center" id="courseFilter" onchange="filterByCourse()">
                                            <option value="">All Courses</option>
                                            <?php
                                            $courses_stmt = $syllabus->getActiveCourses($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                            while ($course_row = $courses_stmt->fetch()) {
                                                $selected = ($course_filter == $course_row['id']) ? 'selected' : '';
                                                echo "<option value='" . $course_row['id'] . "' $selected>" .
                                                    htmlspecialchars($course_row['class_name']) . " (" .
                                                    htmlspecialchars($course_row['class_code']) . ")</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($user_role === 'super_admin'): ?>
                                <?php if ($user_role === 'super_admin'): ?>
                                    <a href="course-syllabi.php?action=add<?php echo $course_filter ? '&course=' . $course_filter : ''; ?>" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                        <span class="d-flex text-md"><i class="ph ph-plus"></i></span>
                                        Add New Syllabus
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    if ($course_filter) {
                                        $stmt = $syllabus->readByCourse($course_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                    } else {
                                        $stmt = $syllabus->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course');
                                    }

                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='lectures.php?syllabus=" . $row['id'] . "' class='bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8'>";
                                            echo "<div class='text-center'>";
                                            echo "<i class='ph ph-notebook text-6xl text-purple-600 mb-3'></i>";
                                            echo "<h6 class='text-purple-600 mb-0'>" . htmlspecialchars($row['syllabus_title']) . "</h6>";
                                            echo "</div>";
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<div class='flex-align gap-8 mb-16'>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill bg-info-50 text-info-600'>" . $row['duration_weeks'] . " weeks</span>";
                                            echo "</div>";
                                            echo "<h5 class='mb-0'><a href='lectures.php?syllabus=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['syllabus_title']) . "</a></h5>";

                                            echo "<div class='flex-align gap-8 flex-wrap mt-16'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-book-open text-main-600'></i>";
                                            echo "<span class='text-gray-600 text-13'>Course: " . htmlspecialchars($row['class_name']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";

                                            if (!empty($row['description'])) {
                                                $description = strlen($row['description']) > 80 ? substr($row['description'], 0, 80) . '...' : $row['description'];
                                                echo "<p class='text-gray-600 text-13 mt-12 mb-16'>" . htmlspecialchars($description) . "</p>";
                                            }

                                            if (!empty($row['objectives'])) {
                                                $objectives = strlen($row['objectives']) > 60 ? substr($row['objectives'], 0, 60) . '...' : $row['objectives'];
                                                echo "<div class='bg-purple-25 p-8 rounded-4 mt-12'>";
                                                echo "<small class='text-gray-600'><strong>Objectives:</strong> " . htmlspecialchars($objectives) . "</small>";
                                                echo "</div>";
                                            }

                                            echo "<div class='flex-between gap-8 mt-16'>";
                                            echo "<div class='flex-align gap-8'>";
                                            if ($user_role === 'super_admin') {
                                                echo "<a href='course-syllabi.php?action=edit&id=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>Edit</a>";
                                                echo "<button type='button' class='btn btn-outline-danger rounded-pill py-6 px-12 text-13' onclick='confirmDelete(" . $row['id'] . ", \"" . htmlspecialchars($row['syllabus_title']) . "\")'>Delete</button>";
                                            } else {
                                                echo "<a href='lectures.php?syllabus=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>View Lectures</a>";
                                            }
                                            echo "</div>";
                                            echo "<span class='badge " . ($row['status'] === 'active' ? 'bg-success' : 'bg-danger') . "'>" . ucfirst($row['status']) . "</span>";
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
                                        echo "<i class='ph ph-notebook text-6xl text-gray-300'></i>";
                                        echo "</div>";
                                        if ($course_filter) {
                                            echo "<h5 class='text-gray-600 mb-8'>No Syllabi Found for This Course</h5>";
                                            echo "<p class='text-gray-400'>No syllabi have been created for this course yet.";
                                            if ($user_role === 'super_admin') {
                                            if ($user_role === 'super_admin') { echo " <a href='course-syllabi.php?action=add&course=" . $course_filter . "' class='text-main-600'>Create the first syllabus</a>"; }
                                            }
                                            echo "</p>";
                                        } else {
                                            echo "<h5 class='text-gray-600 mb-8'>No Course Syllabi Found</h5>";
                                            echo "<p class='text-gray-400'>You don't have any course syllabi assigned yet.";
                                            if ($user_role === 'super_admin') {
                                                if ($user_role === 'super_admin') { echo " <a href='course-syllabi.php?action=add' class='text-main-600'>Create your first syllabus</a>"; }
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
                if ($user_role !== 'super_admin') {
                    flash_message('You do not have permission to manage syllabi.', 'error');
                    redirect('course-syllabi.php');
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
                                                    value="<?php echo isset($syllabus->syllabus_title) ? htmlspecialchars($syllabus->syllabus_title) : ''; ?>"
                                                    placeholder="Enter syllabus title" required>
                                                <div class="text-gray-400 position-absolute inset-inline-end-0 top-50 translate-middle-y me-16">
                                                    <span id="current">0</span>
                                                    <span id="maximum">/ 150</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <label for="course_id" class="h5 mb-8 fw-semibold font-heading">Course <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <select id="course_id" name="course_id" class="form-select py-9 placeholder-13 text-15" required>
                                                    <option value="">Select a course</option>
                                                    <?php
                                                    $courses_stmt = $syllabus->getActiveCourses($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                                    while ($course_row = $courses_stmt->fetch()) {
                                                        $selected = '';
                                                        if ($action == 'edit' && isset($syllabus->class_id) && $syllabus->class_id == $course_row['id']) {
                                                            $selected = 'selected';
                                                        } elseif ($action == 'add' && $course_filter == $course_row['id']) {
                                                            $selected = 'selected';
                                                        }
                                                        echo "<option value='" . $course_row['id'] . "' $selected>" .
                                                            htmlspecialchars($course_row['class_name']) . " (" .
                                                            htmlspecialchars($course_row['class_code']) . ")</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="duration_weeks" class="h5 mb-8 fw-semibold font-heading">Duration (Weeks) <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="number" class="form-control py-11 placeholder-13" id="duration_weeks" name="duration_weeks"
                                                    value="<?php echo isset($syllabus->duration_weeks) ? $syllabus->duration_weeks : '1'; ?>"
                                                    min="1" max="52" placeholder="Enter duration" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="description" class="h5 mb-8 fw-semibold font-heading">Description</label>
                                            <textarea class="form-control py-11 placeholder-13" id="description" name="description" rows="4" placeholder="Enter syllabus description"><?php echo isset($syllabus->description) ? htmlspecialchars($syllabus->description) : ''; ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="objectives" class="h5 mb-8 fw-semibold font-heading">Learning Objectives</label>
                                            <textarea class="form-control py-11 placeholder-13" id="objectives" name="objectives" rows="4" placeholder="List the key learning objectives..."><?php echo isset($syllabus->objectives) ? htmlspecialchars($syllabus->objectives) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="flex-align justify-content-end gap-8">
                                        <a href="course-syllabi.php<?php echo $course_filter ? '?course=' . $course_filter : ''; ?>" class="btn btn-outline-main rounded-pill py-9">Cancel</a>
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
                    <p>Are you sure you want to delete syllabus "<span id="syllabusTitle"></span>"?</p>
                    <p class="text-danger"><small>This will make the syllabus and its lectures unavailable.</small></p>
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
        function confirmDelete(id, title) {
            document.getElementById('deleteId').value = id;
            document.getElementById('syllabusTitle').textContent = title;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function filterByCourse() {
            const courseId = document.getElementById('courseFilter').value;
            if (courseId) {
                window.location.href = 'course-syllabi.php?course=' + courseId;
            } else {
                window.location.href = 'course-syllabi.php';
            }
        }

        // Character counter
        document.getElementById('syllabus_title').addEventListener('input', function() {
            document.getElementById('current').textContent = this.value.length;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const syllabusTitle = document.getElementById('syllabus_title');
            if (syllabusTitle) {
                document.getElementById('current').textContent = syllabusTitle.value.length;
            }
        });
    </script>

    <style>
        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .bg-purple-25 {
            background-color: #fefcff;
        }
    </style>

</body>

</html>
