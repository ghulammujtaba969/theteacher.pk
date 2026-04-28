<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/ClassModel.php';
require_once 'classes/User.php';

// Check if user is logged in
require_roles(['super_admin', 'organization_admin', 'school_admin', 'teacher', 'solo_student']);

$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

$database = new Database();
$db = $database->getConnection();
$class = new ClassModel($db);
$user = new User($db);

// Get accessible class IDs for the current user
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $class->class_name = sanitize_input($_POST['class_name']);
                $class->class_code = sanitize_input($_POST['class_code']);
                $class->type = 'course';
                $class->registration_open = isset($_POST['registration_open']) ? 1 : 0;
                $class->description = sanitize_input($_POST['description']);

                // Validate
                $errors = [];
                if (empty($class->class_name)) {
                    $errors[] = 'Course name is required.';
                }
                if (empty($class->class_code)) {
                    $errors[] = 'Course code is required.';
                }
                if ($class->checkCodeExists($class->class_code)) {
                    $errors[] = 'Course code already exists.';
                }

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    $upload_result = $class->uploadImage($_FILES['image']);
                    if ($upload_result['success']) {
                        $class->image = $upload_result['filename'];
                    } else {
                        $errors[] = $upload_result['error'];
                    }
                } else {
                    $class->image = null;
                }

                if (empty($errors)) {
                    if ($class->create()) {
                        flash_message('Course created successfully!', 'success');
                        redirect('courses.php');
                    } else {
                        flash_message('Error creating course.', 'error');
                    }
                }
                break;

            case 'update':
                $class->id = (int)$_POST['id'];
                
                // Get existing data first
                if (!$class->readOne()) {
                    flash_message('Course not found.', 'error');
                    redirect('courses.php');
                }
                
                $old_image = $class->image;
                
                $class->class_name = sanitize_input($_POST['class_name']);
                $class->class_code = sanitize_input($_POST['class_code']);
                $class->type = 'course';
                $class->registration_open = isset($_POST['registration_open']) ? 1 : 0;
                $class->description = sanitize_input($_POST['description']);

                // Validate
                $errors = [];
                if (empty($class->class_name)) {
                    $errors[] = 'Course name is required.';
                }
                if (empty($class->class_code)) {
                    $errors[] = 'Course code is required.';
                }
                if ($class->checkCodeExists($class->class_code, $class->id)) {
                    $errors[] = 'Course code already exists.';
                }

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                    $upload_result = $class->uploadImage($_FILES['image'], $old_image);
                    if ($upload_result['success']) {
                        $class->image = $upload_result['filename'];
                    } else {
                        $errors[] = $upload_result['error'];
                    }
                } else {
                    $class->image = $old_image;
                }

                // Handle image removal
                if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1' && !empty($old_image)) {
                    $class->deleteImage($old_image);
                    $class->image = null;
                }

                if (empty($errors)) {
                    if ($class->update()) {
                        flash_message('Course updated successfully!', 'success');
                        redirect('courses.php');
                    } else {
                        flash_message('Error updating course.', 'error');
                    }
                }
                break;

            case 'delete':
                $class->id = (int)$_POST['id'];
                if ($class->delete()) {
                    flash_message('Course deleted successfully!', 'success');
                } else {
                    flash_message('Error deleting course.', 'error');
                }
                redirect('courses.php');
                break;

            case 'toggle_registration':
                $course_id = (int)$_POST['id'];
                if ($class->toggleRegistration($course_id)) {
                    flash_message('Registration status updated successfully!', 'success');
                } else {
                    flash_message('Error updating registration status.', 'error');
                }
                redirect('courses.php');
                break;
        }
    }
}

// Get course data for edit
if ($action == 'edit' && $course_id > 0) {
    $class->id = $course_id;
    if (!$class->readOne()) {
        flash_message('Course not found.', 'error');
        redirect('courses.php');
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
    <title>Courses Management - <?php echo APP_NAME; ?></title>
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
                                case 'add':
                                    echo 'Add Course';
                                    break;
                                case 'edit':
                                    echo 'Edit Course';
                                    break;
                                case 'view':
                                    echo 'View Course';
                                    break;
                                default:
                                    echo 'My Courses';
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
                <!-- Courses List -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">All Courses (<?php
                                                                                                                                                                                                                                $stmt = $class->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course', $user_role === 'super_admin');
                                                                                                                                                                                                                                echo $stmt->rowCount();
                                                                                                                                                                                                                                ?>)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-open-tab" data-bs-toggle="pill" data-bs-target="#pills-open" type="button" role="tab" aria-controls="pills-open" aria-selected="false">Registration Open</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pills-closed-tab" data-bs-toggle="pill" data-bs-target="#pills-closed" type="button" role="tab" aria-controls="pills-closed" aria-selected="false">Registration Closed</button>
                                </li>
                            </ul>
                            <?php if ($user_role === 'super_admin'): ?>
                                <a href="courses.php?action=add" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                    <span class="d-flex text-md"><i class="ph ph-plus"></i></span>
                                    Create New Course
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <!-- All Courses Tab -->
                            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    $stmt = $class->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course', $user_role === 'super_admin');
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            $reg_status = $row['registration_open'] ? 'Open' : 'Closed';
                                            $reg_badge = $row['registration_open'] ? 'bg-success' : 'bg-warning';
                                            $image_url = !empty($row['image']) ? 'uploads/classes/' . $row['image'] : null;

                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='course-syllabi.php?course=" . $row['id'] . "' class='bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative'>";
                                            
                                            if ($image_url && file_exists($image_url)) {
                                                echo "<img src='" . $image_url . "' alt='" . htmlspecialchars($row['class_name']) . "' class='w-100 h-100 object-fit-cover rounded-8'>";
                                            } else {
                                                echo "<div class='text-center'>";
                                                echo "<i class='ph ph-book-open text-6xl text-purple-600 mb-3'></i>";
                                                echo "<h6 class='text-purple-600 mb-0'>" . htmlspecialchars($row['class_name']) . "</h6>";
                                                echo "</div>";
                                            }
                                            
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<div class='flex-between gap-8 mb-16'>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill bg-purple-50 text-purple-600'>" . htmlspecialchars($row['class_code']) . "</span>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill $reg_badge text-white'>$reg_status</span>";
                                            echo "</div>";
                                            echo "<h5 class='mb-0'><a href='course-syllabi.php?course=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['class_name']) . "</a></h5>";

                                            echo "<div class='flex-align gap-8 flex-wrap mt-16'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-calendar text-main-600'></i>";
                                            echo "<span class='text-gray-600 text-13'>Created: " . date('M d, Y', strtotime($row['created_at'])) . "</span>";
                                            echo "</div>";
                                            echo "</div>";

                                            if (!empty($row['description'])) {
                                                $description = strlen($row['description']) > 80 ? substr($row['description'], 0, 80) . '...' : $row['description'];
                                                echo "<p class='text-gray-600 text-13 mt-12 mb-16'>" . htmlspecialchars($description) . "</p>";
                                            }

                                            echo "<div class='flex-between gap-8 mt-16'>";
                                            echo "<div class='flex-align gap-8'>";
                                            if ($user_role === 'super_admin') {
                                                echo "<a href='courses.php?action=edit&id=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>Edit</a>";
                                                echo "<button type='button' class='btn btn-outline-danger rounded-pill py-6 px-12 text-13' onclick='confirmDelete(" . $row['id'] . ", \"" . htmlspecialchars($row['class_name']) . "\")'>Delete</button>";
                                            } else {
                                                echo "<a href='course-syllabi.php?course=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>View Syllabi</a>";
                                            }
                                            echo "</div>";
                                            if ($user_role === 'super_admin') {
                                                echo "<button type='button' class='btn btn-sm " . ($row['registration_open'] ? 'btn-warning' : 'btn-success') . " rounded-pill' onclick='toggleRegistration(" . $row['id'] . ")'>";
                                                echo "<i class='ph ph-lock" . ($row['registration_open'] ? '-open' : '') . "'></i>";
                                                echo "</button>";
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
                                        echo "<i class='ph ph-book-open text-6xl text-gray-300'></i>";
                                        echo "</div>";
                                        echo "<h5 class='text-gray-600 mb-8'>No Courses Found</h5>";
                                        echo "<p class='text-gray-400'>You don't have any courses assigned yet.";
                                        if ($user_role === 'super_admin') {
                                            echo " <a href='courses.php?action=add' class='text-main-600'>Create your first course</a>";
                                        }
                                        echo "</p>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Registration Open Tab -->
                            <div class="tab-pane fade" id="pills-open" role="tabpanel" aria-labelledby="pills-open-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    $stmt = $class->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course', $user_role === 'super_admin');
                                    $has_open = false;
                                    while ($row = $stmt->fetch()) {
                                        if ($row['registration_open']) {
                                            $has_open = true;
                                            $image_url = !empty($row['image']) ? 'uploads/classes/' . $row['image'] : null;
                                            
                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='course-syllabi.php?course=" . $row['id'] . "' class='bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative'>";
                                            
                                            if ($image_url && file_exists($image_url)) {
                                                echo "<img src='" . $image_url . "' alt='" . htmlspecialchars($row['class_name']) . "' class='w-100 h-100 object-fit-cover rounded-8'>";
                                            } else {
                                                echo "<div class='text-center'>";
                                                echo "<i class='ph ph-book-open text-6xl text-purple-600 mb-3'></i>";
                                                echo "<h6 class='text-purple-600 mb-0'>" . htmlspecialchars($row['class_name']) . "</h6>";
                                                echo "</div>";
                                            }
                                            
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<h5 class='mb-0'><a href='course-syllabi.php?course=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['class_name']) . "</a></h5>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                    if (!$has_open) {
                                        echo "<div class='col-12 text-center py-5'>";
                                        echo "<p class='text-gray-400'>No courses with open registration.</p>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Registration Closed Tab -->
                            <div class="tab-pane fade" id="pills-closed" role="tabpanel" aria-labelledby="pills-closed-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    $stmt = $class->read($can_access_all_classes_flag ? [] : $accessible_class_ids, 'course', $user_role === 'super_admin');
                                    $has_closed = false;
                                    while ($row = $stmt->fetch()) {
                                        if (!$row['registration_open']) {
                                            $has_closed = true;
                                            $image_url = !empty($row['image']) ? 'uploads/classes/' . $row['image'] : null;
                                            
                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='course-syllabi.php?course=" . $row['id'] . "' class='bg-purple-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8 position-relative'>";
                                            
                                            if ($image_url && file_exists($image_url)) {
                                                echo "<img src='" . $image_url . "' alt='" . htmlspecialchars($row['class_name']) . "' class='w-100 h-100 object-fit-cover rounded-8'>";
                                            } else {
                                                echo "<div class='text-center'>";
                                                echo "<i class='ph ph-book-open text-6xl text-purple-600 mb-3'></i>";
                                                echo "<h6 class='text-purple-600 mb-0'>" . htmlspecialchars($row['class_name']) . "</h6>";
                                                echo "</div>";
                                            }
                                            
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<h5 class='mb-0'><a href='course-syllabi.php?course=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['class_name']) . "</a></h5>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                    if (!$has_closed) {
                                        echo "<div class='col-12 text-center py-5'>";
                                        echo "<p class='text-gray-400'>No courses with closed registration.</p>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($action == 'add' || $action == 'edit'):
                if (!in_array($user_role, ['super_admin', 'organization_admin', 'school_admin'])) {
                    flash_message('You do not have permission to manage courses.', 'error');
                    redirect('courses.php');
                }
            ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0"><?php echo $action == 'add' ? 'Create New Course' : 'Edit Course'; ?></h5>
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Course Details">
                            <i class="ph-fill ph-question"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $class->id; ?>">
                            <?php endif; ?>

                            <div class="row gy-20">
                                <div class="col-12">
                                    <div class="row g-20">
                                        <div class="col-sm-12">
                                            <label for="class_name" class="h5 mb-8 fw-semibold font-heading">Course Name <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="text-counter placeholder-13 form-control py-11 pe-76" maxlength="100" id="class_name" name="class_name"
                                                    value="<?php echo isset($class->class_name) ? htmlspecialchars($class->class_name) : ''; ?>"
                                                    placeholder="Enter course name" required>
                                                <div class="text-gray-400 position-absolute inset-inline-end-0 top-50 translate-middle-y me-16">
                                                    <span id="current">0</span>
                                                    <span id="maximum">/ 100</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="class_code" class="h5 mb-8 fw-semibold font-heading">Course Code <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control py-11 placeholder-13" id="class_code" name="class_code"
                                                    value="<?php echo isset($class->class_code) ? htmlspecialchars($class->class_code) : ''; ?>"
                                                    placeholder="Enter unique course code" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="registration_open" class="h5 mb-8 fw-semibold font-heading">Registration Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="registration_open" name="registration_open"
                                                    <?php echo (isset($class->registration_open) && $class->registration_open == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="registration_open">
                                                    Open for Registration
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="description" class="h5 mb-8 fw-semibold font-heading">Description</label>
                                            <textarea class="form-control py-11 placeholder-13" id="description" name="description" rows="4" placeholder="Enter course description"><?php echo isset($class->description) ? htmlspecialchars($class->description) : ''; ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="h5 mb-8 fw-semibold font-heading">Course Image</label>
                                            
                                            <?php if ($action == 'edit' && !empty($class->image)): ?>
                                                <div class="mb-16">
                                                    <p class="text-13 text-gray-600 mb-8">Current Image:</p>
                                                    <div class="position-relative d-inline-block">
                                                        <img src="uploads/classes/<?php echo $class->image; ?>" alt="Course Image" class="rounded-8" style="max-width: 200px; max-height: 200px;">
                                                        <div class="form-check mt-8">
                                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                            <label class="form-check-label text-13" for="remove_image">
                                                                Remove current image
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="upload-image-wrapper d-flex align-items-center gap-3">
                                                <div class="uploaded-img position-relative h-120 w-120 border border-gray-100 input-form-light radius-8 overflow-hidden bg-purple-25" id="imagePreview" style="display: none;">
                                                    <button type="button" class="uploaded-img__remove position-absolute top-0 end-0 z-1 text-2xl line-height-1 me-8 mt-8 d-flex" id="removeImage">
                                                        <i class="ph ph-x"></i>
                                                    </button>
                                                    <img id="previewImg" class="w-100 h-100 object-fit-cover" src="" alt="Preview">
                                                </div>

                                                <label class="upload-file h-120 w-120 border border-main-600 input-form-light radius-8 overflow-hidden border-dashed bg-purple-25 d-flex align-items-center flex-column justify-content-center gap-1 cursor-pointer" for="upload-file">
                                                    <i class="ph ph-upload-simple text-purple-600 text-2xl"></i>
                                                    <span class="fw-semibold text-13 text-gray-600">Upload</span>
                                                </label>
                                                <input type="file" id="upload-file" name="image" accept="image/*" hidden>
                                            </div>
                                            <p class="text-13 text-gray-600 mt-8">Supported formats: JPG, PNG, GIF, WEBP. Max size: 5MB</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="flex-align justify-content-end gap-8">
                                        <a href="courses.php" class="btn btn-outline-main rounded-pill py-9">Cancel</a>
                                        <button type="submit" class="btn btn-main rounded-pill py-9">
                                            <?php echo $action == 'add' ? 'Create Course' : 'Update Course'; ?>
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
                    <p>Are you sure you want to delete course "<span id="courseName"></span>"?</p>
                    <p class="text-danger"><small>This will make the course and its syllabi unavailable.</small></p>
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

    <!-- Registration Toggle Modal -->
    <div class="modal fade" id="toggleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Toggle Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the registration status for this course?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="action" value="toggle_registration">
                        <input type="hidden" name="id" id="toggleId">
                        <button type="submit" class="btn btn-primary">Confirm</button>
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
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('courseName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function toggleRegistration(id) {
            document.getElementById('toggleId').value = id;
            new bootstrap.Modal(document.getElementById('toggleModal')).show();
        }

        // Character counter
        document.getElementById('class_name')?.addEventListener('input', function() {
            document.getElementById('current').textContent = this.value.length;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const classNameInput = document.getElementById('class_name');
            if (classNameInput) {
                document.getElementById('current').textContent = classNameInput.value.length;
            }
        });

        // Image upload preview
        const uploadInput = document.getElementById('upload-file');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const removeImageBtn = document.getElementById('removeImage');
        const removeCheckbox = document.getElementById('remove_image');

        if (uploadInput) {
            uploadInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                        if (removeCheckbox) {
                            removeCheckbox.checked = false;
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                uploadInput.value = '';
                imagePreview.style.display = 'none';
                previewImg.src = '';
            });
        }

        if (removeCheckbox) {
            removeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    uploadInput.value = '';
                    imagePreview.style.display = 'none';
                    previewImg.src = '';
                }
            });
        }
    </script>

    <style>
        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .bg-purple-50 {
            background-color: #faf5ff;
        }

        .bg-purple-25 {
            background-color: #fefcff;
        }
    </style>

</body>

</html>