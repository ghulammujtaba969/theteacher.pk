<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Role.php';
require_once 'classes/Organization.php';
require_once 'classes/School.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$user = new User($db);
$role = new Role($db);
$organization = new Organization($db);
$school = new School($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $data = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'full_name' => trim($_POST['full_name'])
                ];
                
                // Handle profile image upload (optional)
                // Prefer client-side cropped image if provided
                if (!empty($_POST['profile_image_cropped']) && strpos($_POST['profile_image_cropped'], 'data:image/') === 0) {
                    $dataUrl = $_POST['profile_image_cropped'];
                    if (preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $m)) {
                        $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
                        $binary = base64_decode($base64);
                        if ($binary !== false && strlen($binary) <= 5 * 1024 * 1024) { // 5MB limit
                            $destDir = __DIR__ . '/uploads/users';
                            if (!is_dir($destDir)) { @mkdir($destDir, 0775, true); }
                            // Remove previous user_{id}.* files
                            foreach (['jpg','jpeg','png','gif','webp'] as $e) {
                                $old = $destDir . '/user_' . (int)$current_user['id'] . '.' . $e;
                                if (file_exists($old)) { @unlink($old); }
                            }
                            $destRel = 'uploads/users/user_' . (int)$current_user['id'] . '.' . $ext;
                            $destAbs = __DIR__ . '/' . $destRel;
                            if (file_put_contents($destAbs, $binary) !== false) {
                                @chmod($destAbs, 0644);
                                // Persist in DB and session
                                $user->updatePhoto($current_user['id'], $destRel);
                                $_SESSION['user']['photo'] = $destRel;
                                $_SESSION['user']['profile_image'] = $destRel;
                                $current_user['photo'] = $destRel;
                                $current_user['profile_image'] = $destRel;
                            } else {
                                $error = 'Failed to save cropped image. Please try again.';
                            }
                        } else {
                            $error = 'Invalid or too large cropped image.';
                        }
                    } else {
                        $error = 'Unsupported cropped image format.';
                    }
                } elseif (isset($_FILES['profile_image']) && is_array($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['profile_image']['tmp_name'];
                    $orig = $_FILES['profile_image']['name'];
                    $size = (int)$_FILES['profile_image']['size'];
                    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                    $allowed_img_ext = ['jpg','jpeg','png','gif','webp'];
                    $max_img_size = 5 * 1024 * 1024; // 5MB

                    $img_info = @getimagesize($tmp);
                    if ($img_info === false) {
                        $error = 'Please upload a valid image file.';
                    } elseif (!in_array($ext, $allowed_img_ext, true)) {
                        $error = 'Unsupported image type. Allowed: JPG, PNG, GIF, WEBP.';
                    } elseif ($size > $max_img_size) {
                        $error = 'Image size exceeds 5MB limit.';
                    } else {
                        $destDir = __DIR__ . '/uploads/users';
                        if (!is_dir($destDir)) { @mkdir($destDir, 0775, true); }

                        // Remove previous user_{id}.* files
                        foreach (['jpg','jpeg','png','gif','webp'] as $e) {
                            $old = $destDir . '/user_' . (int)$current_user['id'] . '.' . $e;
                            if (file_exists($old)) { @unlink($old); }
                        }

                        $destRel = 'uploads/users/user_' . (int)$current_user['id'] . '.' . $ext;
                        $destAbs = __DIR__ . '/' . $destRel;
                        if (move_uploaded_file($tmp, $destAbs)) {
                            // Make web-readable
                            @chmod($destAbs, 0644);
                            // Persist in DB and session
                            $user->updatePhoto($current_user['id'], $destRel);
                            $_SESSION['user']['photo'] = $destRel;
                            $_SESSION['user']['profile_image'] = $destRel;
                            $current_user['photo'] = $destRel;
                            $current_user['profile_image'] = $destRel;
                        } else {
                            $error = 'Failed to save uploaded image. Please try again.';
                        }
                    }
                }
                
                if ($user->update($current_user['id'], $data)) {
                    // Update session data
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['email'] = $data['email'];
                    $_SESSION['user']['username'] = $data['username'];
                    $_SESSION['user']['email'] = $data['email'];
                    $_SESSION['user']['full_name'] = $data['full_name'];
                    
                    if (empty($error)) {
                        $message = 'Profile updated successfully!';
                    }
                    // Refresh current user data
                    $current_user = $user->getById($current_user['id']);
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
                break;
                
            case 'change_password':
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                if ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } else {
                    // Verify current password
                    if ($user->login($current_user['username'], $current_password)) {
                        if ($user->updatePassword($current_user['id'], $new_password)) {
                            $message = 'Password changed successfully!';
                        } else {
                            $error = 'Failed to change password. Please try again.';
                        }
                    } else {
                        $error = 'Current password is incorrect.';
                    }
                }
                break;
        }
    }
}

// Get user's role, organization, and school details
$user_role = $role->getById($current_user['role_id']);
$user_organization = null;
$user_school = null;

if ($current_user['organization_id']) {
    $user_organization = $organization->getById($current_user['organization_id']);
}
if ($current_user['school_id']) {
    $user_school = $school->getById($current_user['school_id']);
}

// Get flash message if available
$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>My Profile - <?php echo APP_NAME; ?></title>
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
    <!-- Cropper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"/>
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
                    <li><span class="text-main-600 fw-normal text-15">Profile Settings</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

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
                <div class="card-body p-0">
                     

                    <div class="setting-profile px-24 pt-40">
                        <div class="flex-between">
                            <div class="d-flex align-items-end flex-wrap mb-32 gap-24">
                                <?php $avatar_url = user_avatar_url($current_user ?? []); ?>
                                <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="" class="w-120 h-120 rounded-circle border border-white">
                                <div>
                                    <h4 class="mb-8"><?php echo htmlspecialchars($current_user['full_name'] ?? $current_user['username']); ?></h4>
                                    <div class="setting-profile__infos flex-align flex-wrap gap-16">
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-user-circle"></i></span>
                                            <span class="text-gray-600 d-flex text-15"><?php echo htmlspecialchars($user_role['name'] ?? 'N/A'); ?></span>
                                        </div>
                                        <?php if ($user_organization): ?>
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-buildings"></i></span>
                                            <span class="text-gray-600 d-flex text-15"><?php echo htmlspecialchars($user_organization['name']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="flex-align gap-6">
                                            <span class="text-gray-600 d-flex text-lg"><i class="ph ph-calendar-dots"></i></span>
                                            <span class="text-gray-600 d-flex text-15">Joined <?php echo date('F Y', strtotime($current_user['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="nav common-tab style-two nav-pills mb-0" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="true">My Details</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="pills-password-tab" data-bs-toggle="pill" data-bs-target="#pills-password" type="button" role="tab" aria-controls="pills-password" aria-selected="false">Password</button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <!-- My Details Tab start -->
                <div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
                    <div class="card mt-24">
                        <div class="card-header border-bottom">
                            <h4 class="mb-4">My Details</h4>
                            <p class="text-gray-600 text-15">Please fill full details about yourself</p>
                        </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="update_profile">
                                        <input type="hidden" name="profile_image_cropped" id="profile_image_cropped" value="">
                                <div class="row gy-4">
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="fname" class="form-label mb-8 h6">Full Name</label>
                                        <input type="text" class="form-control py-11" name="full_name" id="fname" 
                                               value="<?php echo htmlspecialchars($current_user['full_name'] ?? ''); ?>" 
                                               placeholder="Enter Full Name" required>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="username" class="form-label mb-8 h6">Username</label>
                                        <input type="text" class="form-control py-11" name="username" id="username" 
                                               value="<?php echo htmlspecialchars($current_user['username']); ?>" 
                                               placeholder="Enter Username" required>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="email" class="form-label mb-8 h6">Email</label>
                                        <input type="email" class="form-control py-11" name="email" id="email" 
                                               value="<?php echo htmlspecialchars($current_user['email']); ?>" 
                                               placeholder="Enter Email" required>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="phone" class="form-label mb-8 h6">Phone Number</label>
                                        <input type="text" class="form-control py-11" id="phone" 
                                               value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>" 
                                               placeholder="Enter Phone Number">
                                    </div>
                                    <div class="col-12">
                                        <label for="imageUpload" class="form-label mb-8 h6">Your Photo</label>
                                        <div class="flex-align gap-22">
                                        <div class="avatar-upload flex-shrink-0">
                                                <input type='file' id="imageUpload" name="profile_image" accept=".png, .jpg, .jpeg, .gif, .webp">
                                                <div class="avatar-preview">
                                                    <?php $photoUrl = user_avatar_url($current_user ?? []); ?>
                                                    <div id="profileImagePreview" style="background-image: url('<?php echo htmlspecialchars($photoUrl); ?>');">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="avatar-upload-box text-center position-relative flex-grow-1 py-24 px-4 rounded-16 border border-main-300 border-dashed bg-main-50 hover-bg-main-100 hover-border-main-400 transition-2 cursor-pointer">
                                                <label for="imageUpload" class="position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 rounded-16 cursor-pointer z-1"></label>
                                                <span class="text-32 icon text-main-600 d-inline-flex"><i class="ph ph-upload"></i></span>
                                                <span class="text-13 d-block text-gray-400 text my-8">Click to upload or drag and drop</span>
                                                <span class="text-13 d-block text-main-600">SVG, PNG, JPEG OR GIF (max 1080px1200px)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="role" class="form-label mb-8 h6">Role</label>
                                        <input type="text" class="form-control py-11" id="role" 
                                               value="<?php echo htmlspecialchars($user_role['name'] ?? 'N/A'); ?>" 
                                               placeholder="Role" readonly>
                                    </div>
                                    <div class="col-sm-6 col-xs-6">
                                        <label for="address" class="form-label mb-8 h6">Address</label>
                                        <input type="text" class="form-control py-11" id="address" 
                                               value="<?php echo htmlspecialchars($current_user['address'] ?? ''); ?>" 
                                               placeholder="Enter Address">
                                    </div>
                                    <div class="col-12">
                                        <div class="flex-align justify-content-end gap-8">
                                            <button type="reset" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9">Cancel</button>
                                            <button type="submit" class="btn btn-main rounded-pill py-9">Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- My Details Tab End -->
                
                <!-- Profile Tab Start -->
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    <div class="row gy-4">
                        <div class="col-lg-6">
                            <div class="card mt-24">
                                <div class="card-body">
                                    <h6 class="mb-12">Account Information</h6>
                                    <div class="rounded-8 border border-gray-100 p-16 mb-16">
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl"> 
                                                <i class="ph ph-user"></i>
                                            </span>
                                            <div class="flex-align gap-8 flex-wrap text-gray-600">
                                                <strong>Full Name:</strong>
                                                <span><?php echo htmlspecialchars($current_user['full_name'] ?? $current_user['username']); ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl"> 
                                                <i class="ph ph-envelope-simple"></i>
                                            </span>
                                            <div class="flex-align gap-8 flex-wrap text-gray-600">
                                                <strong>Email:</strong>
                                                <span><?php echo htmlspecialchars($current_user['email']); ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl"> 
                                                <i class="ph ph-identification-badge"></i>
                                            </span>
                                            <div class="flex-align gap-8 flex-wrap text-gray-600">
                                                <strong>Role:</strong>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($user_role['name'] ?? 'N/A'); ?></span>
                                            </div>
                                        </div>
                                        <?php if ($user_organization): ?>
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl"> 
                                                <i class="ph ph-buildings"></i>
                                            </span>
                                            <div class="flex-align gap-8 flex-wrap text-gray-600">
                                                <strong>Organization:</strong>
                                                <span><?php echo htmlspecialchars($user_organization['name']); ?></span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($user_school): ?>
                                        <div class="flex-align gap-8 mb-16">
                                            <span class="flex-center w-36 h-36 text-main-600 bg-main-100 rounded-circle text-xl"> 
                                                <i class="ph ph-graduation-cap"></i>
                                            </span>
                                            <div class="flex-align gap-8 flex-wrap text-gray-600">
                                                <strong>School:</strong>
                                                <span><?php echo htmlspecialchars($user_school['name']); ?></span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card mt-24">
                                <div class="card-body">
                                    <h6 class="mb-12">Account Status</h6>
                                    <div class="mb-3">
                                        <strong>Status:</strong><br>
                                        <span class="badge <?php echo $current_user['status'] === 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($current_user['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Member Since:</strong><br>
                                        <span class="text-muted"><?php echo date('M d, Y', strtotime($current_user['created_at'])); ?></span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Last Updated:</strong><br>
                                        <span class="text-muted"><?php echo date('M d, Y H:i', strtotime($current_user['updated_at'])); ?></span>
                                    </div>

                                    <h6 class="mb-12 mt-24">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
                                            <i class="ph ph-squares-four me-2"></i>Dashboard
                                        </a>
                                        
                                        <?php if (in_array($_SESSION['role'], ['super_admin', 'organization_admin', 'school_admin'])): ?>
                                        <a href="users.php" class="btn btn-outline-info btn-sm">
                                            <i class="ph ph-users-three me-2"></i>Manage Users
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="classes.php" class="btn btn-outline-success btn-sm">
                                            <i class="ph ph-graduation-cap me-2"></i>My Classes
                                        </a>
                                        
                                        <a href="lectures.php" class="btn btn-outline-warning btn-sm">
                                            <i class="ph ph-books me-2"></i>My Lectures
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Profile Tab End -->

                <!-- Password Tab Start -->
                <div class="tab-pane fade" id="pills-password" role="tabpanel" aria-labelledby="pills-password-tab" tabindex="0">
                    <div class="card mt-24">
                        <div class="card-header border-bottom">
                            <h4 class="mb-4">Password Settings</h4>
                            <p class="text-gray-600 text-15">Update your password to keep your account secure</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        <div class="row gy-4">
                                            <div class="col-12">
                                                <label for="current-password" class="form-label mb-8 h6">Current Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control py-11" name="current_password" id="current-password" placeholder="Enter Current Password" required>
                                                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#current-password"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="new-password" class="form-label mb-8 h6">New Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control py-11" name="new_password" id="new-password" placeholder="Enter New Password" required>
                                                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#new-password"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="confirm-password" class="form-label mb-8 h6">Confirm Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control py-11" name="confirm_password" id="confirm-password" placeholder="Enter Confirm Password" required>
                                                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#confirm-password"></span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label mb-8 h6">Password Requirements:</label>
                                                <ul class="list-inside">
                                                    <li class="text-gray-600 mb-4">At least one lowercase character</li>
                                                    <li class="text-gray-600 mb-4">Minimum 6 characters long - the more, the better</li>
                                                    <li class="text-gray-600 mb-4">At least one number or symbol is recommended</li>
                                                </ul>
                                            </div>
                                            <div class="col-12">
                                                <div class="flex-align justify-content-end gap-8">
                                                    <button type="reset" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9">Cancel</button>
                                                    <button type="submit" class="btn btn-main rounded-pill py-9">Save Changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Password Tab End -->

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
    <!-- full calendar removed on profile to avoid DOM dependency errors -->
    <!-- jQuery UI -->
    <script src="assets/js/jquery-ui.js"></script>
    <!-- jQuery UI -->
    <script src="assets/js/editor-quill.js"></script>
    <!-- apex charts -->
    <script src="assets/js/apexcharts.min.js"></script>
    <!-- Calendar Js removed on profile to avoid null element errors -->
    <!-- jvectormap Js -->
    <script src="assets/js/jquery-jvectormap-2.0.5.min.js"></script>
    <!-- jvectormap world Js -->
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    
    <!-- main js -->
    <script src="assets/js/main.js"></script>
    <!-- Cropper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <script>
        // ============================= Avatar Upload js ============================= 
        // Initialize cropper flow for #imageUpload after DOM is ready
        document.addEventListener('DOMContentLoaded', function(){
            var cropper = null;
            var cropperModalEl = document.getElementById('cropperModal');
            var cropperImage = document.getElementById('cropperImage');
            var fileInput = document.getElementById('imageUpload');
            var hiddenInput = document.getElementById('profile_image_cropped');
            var preview = document.getElementById('profileImagePreview');
            var cropBtn = document.getElementById('cropAndSaveBtn');

            if (!cropperModalEl || !fileInput || !cropperImage || !hiddenInput || !cropBtn) {
                return; // Missing elements; do nothing
            }

            var bsModal = new bootstrap.Modal(cropperModalEl);

            function initCropper(url){
                cropperImage.src = url;
                if (cropper) { cropper.destroy(); cropper = null; }
                setTimeout(function(){
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        background: false,
                        responsive: true
                    });
                }, 50);
                bsModal.show();
            }

            fileInput.addEventListener('change', function(e){
                var file = e.target.files && e.target.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(ev){ initCropper(ev.target.result); };
                reader.readAsDataURL(file);
            });

            cropBtn.addEventListener('click', function(){
                if (!cropper) return;
                var canvas = cropper.getCroppedCanvas({ width: 512, height: 512, imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });
                if (!canvas) return;
                var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                hiddenInput.value = dataUrl;
                if (preview) {
                    preview.style.backgroundImage = 'url(' + dataUrl + ')';
                    preview.style.display = 'block';
                }
                if (fileInput) fileInput.value = '';
                bsModal.hide();
                if (cropper) { cropper.destroy(); cropper = null; }
            });
        });

        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const target = $(this).attr('id').replace('#', '');
            const input = $(`#${target}`);
            const icon = $(this);
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ph-eye-slash').addClass('ph-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ph-eye').addClass('ph-eye-slash');
            }
        });

        // Form validation
        $('form').on('submit', function(e) {
            const action = $(this).find('input[name="action"]').val();
            
            if (action === 'change_password') {
                const newPassword = $('#new-password').val();
                const confirmPassword = $('#confirm-password').val();
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New passwords do not match!');
                    return false;
                }
                
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    return false;
                }
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true" style="min-height:auto;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crop Your Photo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="w-100">
          <img id="cropperImage" src="" alt="Cropper" style="max-width: 100%; display: block;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-gray rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="cropAndSaveBtn" class="btn btn-main rounded-pill">Crop & Save</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>

