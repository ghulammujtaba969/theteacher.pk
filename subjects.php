<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Subject.php';
require_once 'includes/functions.php';

// Include User class and get accessible class IDs for the current user
require_once 'classes/User.php';
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$current_user = current_user();
$accessible_classes_raw = $user->getAccessibleClasses($current_user);
$accessible_class_ids = array_column($accessible_classes_raw, 'id');
$can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;

// Check if user is logged in and is super admin
require_roles(['super_admin', 'organization_admin', 'school_admin', 'teacher', 'solo_student']);

$user_role = $_SESSION['role'] ?? '';
$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$class_filter = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Function to handle image upload
function handleImageUpload($file, $old_image = null) {
    $upload_dir = 'uploads/subjects/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return $old_image;
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('File size exceeds 5MB limit.');
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'subject_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Delete old image if exists
        if ($old_image && file_exists($old_image)) {
            unlink($old_image);
        }
        return $filepath;
    } else {
        throw new Exception('Failed to upload image.');
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                try {
                    $subject->subject_name = sanitize_input($_POST['subject_name']);
                    $subject->subject_code = sanitize_input($_POST['subject_code']);
                    $subject->class_id = (int)$_POST['class_id'];
                    $subject->description = sanitize_input($_POST['description']);

                    // Validate
                    $errors = [];
                    if (empty($subject->subject_name)) {
                        $errors[] = 'Subject name is required.';
                    }
                    if (empty($subject->subject_code)) {
                        $errors[] = 'Subject code is required.';
                    }
                    if (empty($subject->class_id)) {
                        $errors[] = 'Please select a class.';
                    }
                    if ($subject->checkCodeExists($subject->subject_code, $subject->class_id)) {
                        $errors[] = 'Subject code already exists for this class.';
                    }

                    // Handle image upload
                    if (isset($_FILES['subject_image']) && $_FILES['subject_image']['error'] === UPLOAD_ERR_OK) {
                        $subject->image = handleImageUpload($_FILES['subject_image']);
                    } else {
                        $subject->image = null;
                    }

                    if (empty($errors)) {
                        if ($subject->create()) {
                            flash_message('Subject created successfully!', 'success');
                            redirect('subjects.php');
                        } else {
                            flash_message('Error creating subject.', 'error');
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                break;

            case 'update':
                try {
                    $subject->id = (int)$_POST['id'];
                    
                    // Get current subject data
                    $current_subject = new Subject($db);
                    $current_subject->id = $subject->id;
                    $current_subject->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids);
                    
                    $subject->subject_name = sanitize_input($_POST['subject_name']);
                    $subject->subject_code = sanitize_input($_POST['subject_code']);
                    $subject->class_id = (int)$_POST['class_id'];
                    $subject->description = sanitize_input($_POST['description']);

                    // Validate
                    $errors = [];
                    if (empty($subject->subject_name)) {
                        $errors[] = 'Subject name is required.';
                    }
                    if (empty($subject->subject_code)) {
                        $errors[] = 'Subject code is required.';
                    }
                    if (empty($subject->class_id)) {
                        $errors[] = 'Please select a class.';
                    }
                    if ($subject->checkCodeExists($subject->subject_code, $subject->class_id, $subject->id)) {
                        $errors[] = 'Subject code already exists for this class.';
                    }

                    // Handle image upload or removal
                    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                        // Remove existing image
                        if ($current_subject->image && file_exists($current_subject->image)) {
                            unlink($current_subject->image);
                        }
                        $subject->image = null;
                    } elseif (isset($_FILES['subject_image']) && $_FILES['subject_image']['error'] === UPLOAD_ERR_OK) {
                        // Upload new image
                        $subject->image = handleImageUpload($_FILES['subject_image'], $current_subject->image);
                    } else {
                        // Keep existing image
                        $subject->image = null; // Don't update in database
                    }

                    if (empty($errors)) {
                        if ($subject->update()) {
                            flash_message('Subject updated successfully!', 'success');
                            redirect('subjects.php');
                        } else {
                            flash_message('Error updating subject.', 'error');
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                break;

            case 'delete':
                $subject->id = (int)$_POST['id'];
                if ($subject->delete()) {
                    flash_message('Subject deleted successfully!', 'success');
                } else {
                    flash_message('Error deleting subject.', 'error');
                }
                redirect('subjects.php');
                break;
        }
    }
}

// Get subject data for edit
if ($action == 'edit' && $subject_id > 0) {
    $subject->id = $subject_id;
    if (!$subject->readOne($can_access_all_classes_flag ? [] : $accessible_class_ids)) {
        flash_message('Subject not found or you do not have permission to view it.', 'error');
        redirect('subjects.php');
    }
    // Explicit authorization check for editing a subject
    if (!$can_access_all_classes_flag && !empty($subject->class_id) && !$user->hasAccessToClass($current_user['id'], $subject->class_id)) {
        flash_message('You do not have permission to edit subjects in this class.', 'error');
        redirect('subjects.php');
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
    <title>Subjects Management - <?php echo APP_NAME; ?></title>
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
    <style>
        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .remove-image-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        .subject-card-image {
            width: 100%;
            height: 164px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
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
            
            <div class="breadcrumb mb-24">
                <ul class="flex-align gap-4">
                    <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">
                        <?php 
                        switch($action) {
                            case 'add': echo 'Add Subject'; break;
                            case 'edit': echo 'Edit Subject'; break;
                            case 'view': echo 'View Subject'; break;
                            default: echo 'Subjects'; break;
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
                <!-- Subjects List -->
                <div class="card">
                    <div class="card-body">
                        <div class="mb-24 flex-between gap-16 flex-wrap-reverse">
                            <div class="flex-align gap-16 flex-wrap">
                                <ul class="nav nav-pills common-tab gap-20" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                      <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">
                                        All Subjects (<?php 
                                            if ($class_filter) {
                                                $stmt = $subject->readByClass($class_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                            } else {
                                                $stmt = $subject->read($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                            }
                                            echo $stmt->rowCount();
                                        ?>)
                                      </button>
                                    </li>
                                </ul>
                                
                                <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-8 focus-border-main-600">
                                    <span class="text-lg"><i class="ph ph-funnel-simple"></i></span>
                                    <select class="form-control px-8 py-12 border-0 text-inherit rounded-4 text-center" id="classFilter" onchange="filterByClass()">
                                        <option value="">All Classes</option>
                                        <?php
                                        $classes_stmt = $subject->getActiveClasses($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                        while ($class_row = $classes_stmt->fetch()) {
                                            $selected = ($class_filter == $class_row['id']) ? 'selected' : '';
                                            echo "<option value='" . $class_row['id'] . "' $selected>" . 
                                                 htmlspecialchars($class_row['class_name']) . " (" . 
                                                 htmlspecialchars($class_row['class_code']) . ")</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?php if ($user_role === 'super_admin'): ?>
                            <a href="subjects.php?action=add" class="btn btn-main rounded-pill py-7 flex-align gap-4 fw-normal">
                                <span class="d-flex text-md"><i class="ph ph-plus"></i></span> 
                                Create New Subject
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab" tabindex="0">
                                <div class="row g-20">
                                    <?php
                                    if ($class_filter) {
                                        $stmt = $subject->readByClass($class_filter, $can_access_all_classes_flag ? [] : $accessible_class_ids);
                                    } else {
                                        $stmt = $subject->read($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                    }
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            echo "<div class='col-xxl-3 col-lg-4 col-sm-6'>";
                                            echo "<div class='card border border-gray-100'>";
                                            echo "<div class='card-body p-8'>";
                                            echo "<a href='syllabi.php?subject=" . $row['id'] . "' class='bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8'>";
                                            
                                            // Display subject image or default icon
                                            if (!empty($row['image']) && file_exists($row['image'])) {
                                                echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['subject_name']) . "' class='subject-card-image'>";
                                            } else {
                                                echo "<div class='text-center'>";
                                                echo "<i class='ph ph-bookmarks text-6xl text-main-600 mb-3'></i>";
                                                echo "<h6 class='text-main-600 mb-0'>" . htmlspecialchars($row['subject_name']) . "</h6>";
                                                echo "</div>";
                                            }
                                            
                                            echo "</a>";
                                            echo "<div class='p-8'>";
                                            echo "<span class='text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600 mb-16'>" . htmlspecialchars($row['subject_code']) . "</span>";
                                            echo "<h5 class='mb-0'><a href='syllabi.php?subject=" . $row['id'] . "' class='hover-text-main-600'>" . htmlspecialchars($row['subject_name']) . "</a></h5>";

                                            echo "<div class='flex-align gap-8 flex-wrap mt-16'>";
                                            echo "<div class='flex-align gap-4'>";
                                            echo "<i class='ph ph-graduation-cap text-main-600'></i>";
                                            echo "<span class='text-gray-600 text-13'>Class: " . htmlspecialchars($row['class_name']) . "</span>";
                                            echo "</div>";
                                            echo "</div>";
                                            
                                            echo "<div class='flex-align gap-8 flex-wrap mt-8'>";
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
                                                echo "<a href='subjects.php?action=edit&id=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>Edit</a>";
                                                echo "<button type='button' class='btn btn-danger rounded-pill py-6 px-12 text-13' onclick='confirmDelete(" . $row['id'] . ", \"" . htmlspecialchars($row['subject_name']) . "\")'>Delete</button>";
                                            } else {
                                                echo "<a href='syllabi.php?subject=" . $row['id'] . "' class='btn btn-outline-main rounded-pill py-6 px-12 text-13'>View Syllabi</a>";
                                            }
                                            echo "</div>";
                                            echo "<span class='badge " . ($row['status'] === 'active' ? 'bg-success' : 'bg-danger') . "'>" . ucfirst($row['status']) . "</span>";
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
                                        echo "<i class='ph ph-bookmarks text-6xl text-gray-300'></i>";
                                        echo "</div>";
                                        if ($class_filter) {
                                            echo "<h5 class='text-gray-600 mb-8'>No Subjects Found in This Class</h5>";
                                            echo "<p class='text-gray-400'>No subjects have been created for the selected class yet.";
                                            if ($user_role === 'super_admin') {
                                                echo " <a href='subjects.php?action=add' class='text-main-600'>Create the first subject</a>";
                                            }
                                            echo "</p>";
                                            echo "<a href='subjects.php' class='btn btn-outline-main rounded-pill py-9 mt-3'>View All Subjects</a>";
                                        } else {
                                            echo "<h5 class='text-gray-600 mb-8'>No Subjects Found</h5>";
                                            echo "<p class='text-gray-400'>You don't have any subjects assigned yet.";
                                            if ($user_role === 'super_admin') {
                                                echo " <a href='subjects.php?action=add' class='text-main-600'>Create your first subject</a>";
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
                    flash_message('You do not have permission to manage subjects.', 'error');
                    redirect('subjects.php');
                }
            ?>
                <!-- Add/Edit Form -->
                <div class="card">
                    <div class="card-header border-bottom border-gray-100 flex-align gap-8">
                        <h5 class="mb-0"><?php echo $action == 'add' ? 'Create New Subject' : 'Edit Subject'; ?></h5>        
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Subject Details">
                            <i class="ph-fill ph-question"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $action == 'add' ? 'create' : 'update'; ?>">
                            <?php if ($action == 'edit'): ?>
                                <input type="hidden" name="id" value="<?php echo $subject->id; ?>">
                            <?php endif; ?>
                            
                            <div class="row gy-20">
                                <div class="col-12">
                                    <div class="row g-20">
                                        <div class="col-sm-12">
                                            <label for="subject_name" class="h5 mb-8 fw-semibold font-heading">Subject Name <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="text-counter placeholder-13 form-control py-11 pe-76" maxlength="100" id="subject_name" name="subject_name" 
                                                       value="<?php echo isset($subject->subject_name) ? htmlspecialchars($subject->subject_name) : (isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : ''); ?>" 
                                                       placeholder="Enter subject name" required>
                                                <div class="text-gray-400 position-absolute inset-inline-end-0 top-50 translate-middle-y me-16">
                                                    <span id="current">0</span>
                                                    <span id="maximum">/ 100</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="subject_code" class="h5 mb-8 fw-semibold font-heading">Subject Code <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control py-11 placeholder-13" id="subject_code" name="subject_code" 
                                                       value="<?php echo isset($subject->subject_code) ? htmlspecialchars($subject->subject_code) : (isset($_POST['subject_code']) ? htmlspecialchars($_POST['subject_code']) : ''); ?>" 
                                                       placeholder="Enter unique subject code (e.g., MATH01)" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="class_id" class="h5 mb-8 fw-semibold font-heading">Class <span class="text-13 text-gray-400 fw-medium">(Required)</span></label>
                                            <div class="position-relative">
                                                <select id="class_id" name="class_id" class="form-select py-9 placeholder-13 text-15" required>
                                                    <option value="" disabled selected>Select a class</option>
                                                    <?php
                                                    $classes_stmt = $subject->getActiveClasses($can_access_all_classes_flag ? [] : $accessible_class_ids);
                                                    while ($class_row = $classes_stmt->fetch()) {
                                                        $selected = '';
                                                        if (isset($subject->class_id) && $subject->class_id == $class_row['id']) {
                                                            $selected = 'selected';
                                                        } elseif (isset($_POST['class_id']) && $_POST['class_id'] == $class_row['id']) {
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
                                        
                                        <div class="col-12">
                                            <label for="description" class="h5 mb-8 fw-semibold font-heading">Description</label>
                                            <textarea class="form-control py-11 placeholder-13" id="description" name="description" rows="4" placeholder="Enter subject description"><?php echo isset($subject->description) ? htmlspecialchars($subject->description) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label for="subject_image" class="h5 mb-8 fw-semibold font-heading">Subject Image <span class="text-13 text-gray-400 fw-medium">(Optional - Max 5MB)</span></label>
                                            <input type="file" class="form-control py-11" id="subject_image" name="subject_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(event)">
                                            <small class="text-gray-400">Supported formats: JPG, PNG, GIF, WebP</small>
                                            
                                            <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
                                                <img id="imagePreview" class="image-preview" src="" alt="Image Preview">
                                                <button type="button" class="remove-image-btn" onclick="removeImagePreview()" title="Remove image">&times;</button>
                                            </div>

                                            <?php if ($action == 'edit' && !empty($subject->image) && file_exists($subject->image)): ?>
                                                <div id="currentImageContainer" class="image-preview-container mt-3">
                                                    <img src="<?php echo htmlspecialchars($subject->image); ?>" class="image-preview" alt="Current Subject Image">
                                                    <button type="button" class="remove-image-btn" onclick="removeCurrentImage()" title="Remove current image">&times;</button>
                                                </div>
                                                <input type="hidden" id="remove_image" name="remove_image" value="0">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="flex-align justify-content-end gap-8">
                                        <a href="subjects.php" class="btn btn-outline-main rounded-pill py-9">Cancel</a>
                                        <button type="submit" class="btn btn-main rounded-pill py-9">
                                            <?php echo $action == 'add' ? 'Create Subject' : 'Update Subject'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        
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
                    <p>Are you sure you want to set the status of subject "<span id="subjectName"></span>" to inactive?</p>
                    <p class="text-danger"><small>This will make the subject, its syllabi, and lectures unavailable to users. You can reactivate it later.</small></p>
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
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('subjectName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function filterByClass() {
            const classId = document.getElementById('classFilter').value;
            if (classId) {
                window.location.href = 'subjects.php?class=' + classId;
            } else {
                window.location.href = 'subjects.php';
            }
        }

        // Image preview function
        function previewImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        }

        // Remove image preview
        function removeImagePreview() {
            document.getElementById('subject_image').value = '';
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('imagePreview').src = '';
        }

        // Remove current image (for edit mode)
        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                document.getElementById('remove_image').value = '1';
                document.getElementById('currentImageContainer').style.display = 'none';
            }
        }

        // Character counter for subject name input
        document.getElementById('subject_name')?.addEventListener('input', function() {
            const current = this.value.length;
            document.getElementById('current').textContent = current;
        });

        // Initialize character counter on page load
        document.addEventListener('DOMContentLoaded', function() {
            const subjectNameInput = document.getElementById('subject_name');
            if (subjectNameInput) {
                const current = subjectNameInput.value.length;
                document.getElementById('current').textContent = current;
            }
        });
    </script>
</body>
</html>