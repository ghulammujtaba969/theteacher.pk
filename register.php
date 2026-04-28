<?php
// Include necessary files and initialize database connection
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Organization.php';
require_once 'classes/School.php';
require_once 'classes/Role.php'; 
require_once 'classes/PendingUser.php';
require_once 'classes/ClassModel.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = new Database();
$db = $database->getConnection();

// Test database connection
if (!$db) {
    error_log("Database connection failed in register.php");
    die("Database connection failed. Check your configuration.");
}

error_log("Database connection successful in register.php");

$user = new User($db);
$organization = new Organization($db);
$school = new School($db);
$role = new Role($db);
$classModel = new ClassModel($db);
$pendingUser = new PendingUser($db);
$available_classes = [];

try {
    $stmt_classes = $classModel->read([]); // Fetch all active classes
    $available_classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
    error_log("Available classes loaded: " . count($available_classes) . " classes found");
} catch (Exception $e) {
    error_log("Error loading classes: " . $e->getMessage());
}

$message = '';
$error = '';
$success_redirect = false;
$is_admin_creating_user = false;

// Determine if an admin is creating a user (e.g., from a dashboard link)
if (isset($_GET['admin_create']) && $_GET['admin_create'] == 'true') {
    $is_admin_creating_user = true;
    error_log("Admin creating user mode activated");
}

// Handle AJAX request for schools
if (isset($_GET['action']) && $_GET['action'] === 'get_schools_by_organization' && isset($_GET['organization_id'])) {
    $organizationId = $_GET['organization_id'];
    error_log("AJAX request for schools by organization ID: " . $organizationId);
    $schools = $school->getAll($organizationId);
    header('Content-Type: application/json');
    echo json_encode($schools);
    exit();
}

// Fetch data for dropdowns if admin is creating a user
$organizations = [];
$roles_for_dropdown = [];
if ($is_admin_creating_user) {
    try {
        $organizations = $organization->getAll();
        $roles_raw = $role->getAll();
        foreach ($roles_raw as $r) {
            $roles_for_dropdown[$r['id']] = $r['name'];
        }
        error_log("Admin data loaded - Organizations: " . count($organizations) . ", Roles: " . count($roles_for_dropdown));
    } catch (Exception $e) {
        error_log("Error loading admin data: " . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted - POST request received");
    error_log("POST data: " . print_r($_POST, true));
    
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'] ?? null;
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $selected_classes = $_POST['classes'] ?? []; // Array of selected class IDs
    
    error_log("Parsed form data:");
    error_log("- Full Name: " . $full_name);
    error_log("- Username: " . $username);
    error_log("- Email: " . $email);
    error_log("- Phone: " . $phone);
    error_log("- Gender: " . $gender);
    error_log("- Selected Classes: " . print_r($selected_classes, true));
    error_log("- Is Admin Creating User: " . ($is_admin_creating_user ? 'Yes' : 'No'));

    // Validation
    if (empty($full_name) || empty($username) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password) || empty($gender)) {
        $error = 'Please fill in all required fields.';
        error_log("Validation error: Missing required fields");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
        error_log("Validation error: Invalid email format - " . $email);
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
        error_log("Validation error: Passwords do not match");
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
        error_log("Validation error: Password too short");
    } elseif ($is_admin_creating_user && empty($selected_classes)) {
        $error = 'Please select at least one class for the user.';
        error_log("Validation error: No classes selected for admin-created user");
    } else {
        error_log("Validation passed, checking for existing users");
        
        // Check if username or email already exists in active users or pending users
        $existing_user = false;
        $existing_pending = false;
        
        try {
            $existing_user = $user->findByUsername($username);
            error_log("Username check result: " . ($existing_user ? 'Found existing' : 'Not found'));
        } catch (Exception $e) {
            error_log("Error checking username: " . $e->getMessage());
        }
        
        try {
            $existing_email = $user->findByEmail($email);
            error_log("Email check result: " . ($existing_email ? 'Found existing' : 'Not found'));
        } catch (Exception $e) {
            error_log("Error checking email: " . $e->getMessage());
        }
        
        try {
            $existing_pending = $pendingUser->exists($username, $email);
            error_log("Pending user check result: " . ($existing_pending ? 'Found existing' : 'Not found'));
        } catch (Exception $e) {
            error_log("Error checking pending users: " . $e->getMessage());
        }
        
        if ($existing_user || $existing_pending) {
            if ($existing_user) {
                $error = 'Username or email already exists.';
                error_log("Error: Username or email exists in active users");
            } else {
                $error = 'Username or email already exists in pending registrations.';
                error_log("Error: Username or email exists in pending users");
            }
        } else {
            error_log("No existing users found, proceeding with user creation");
            
            $userData = [
                'full_name' => $full_name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'address' => $address,
                'password' => $password,
                'role_id' => 5, // Default to Solo Student for self-registration
                'organization_id' => null,
                'school_id' => null,
                'status' => 'active' // Direct activation instead of pending
            ];

            error_log("UserData array before user creation: " . print_r($userData, true));

            if ($is_admin_creating_user) {
                error_log("Processing admin-created user");
                
                $userData['organization_id'] = !empty($_POST['organization_id']) ? $_POST['organization_id'] : null;
                $userData['school_id'] = !empty($_POST['school_id']) ? $_POST['school_id'] : null;
                $userData['role_id'] = !empty($_POST['role_id']) ? $_POST['role_id'] : null;
                $userData['status'] = !empty($_POST['status']) ? $_POST['status'] : 'active';
                $created_by = $_SESSION['user_id'] ?? 1; // Assuming Super Admin is 1, or get from session

                error_log("Admin user data: " . print_r($userData, true));
                error_log("Created by: " . $created_by);

                try {
                    $new_user_id = $user->create($userData, $created_by);
                    error_log("User create result: " . ($new_user_id ? $new_user_id : 'false'));
                    
                    if ($new_user_id) {
                        error_log("Selected classes for new user {$new_user_id}: " . print_r($selected_classes, true));
                        if (!empty($selected_classes)) {
                            foreach ($selected_classes as $class_id) {
                                $permission_result = $user->assignClassPermission($new_user_id, $class_id, $created_by);
                                error_log("Class permission assignment for class {$class_id}: " . ($permission_result ? 'Success' : 'Failed'));
                            }
                        }
                        $message = 'User created successfully!';
                        error_log("Admin user creation successful");
                    } else {
                        error_log("User creation returned false. UserData: " . print_r($userData, true) . " Created by: " . $created_by);
                        $error = 'Failed to create user. Please try again.';
                    }
                } catch (Exception $e) {
                    error_log("Exception during admin user creation: " . $e->getMessage());
                    $error = 'Failed to create user. Please try again.';
                }
            } else {
                error_log("Processing self-registration - Direct to users table");
                
                $created_by = null; // No admin created this user

                error_log("Self-registration user data: " . print_r($userData, true));

                try {
                    $new_user_id = $user->create($userData, $created_by);
                    error_log("User create result: " . ($new_user_id ? $new_user_id : 'false'));
                    
                    if ($new_user_id) {
                        error_log("User created successfully with ID: " . $new_user_id);
                        
                        // Assign default classes if any were selected (optional for self-registration)
                        if (!empty($selected_classes)) {
                            foreach ($selected_classes as $class_id) {
                                $permission_result = $user->assignClassPermission($new_user_id, $class_id, 1); // Using admin ID 1 as default
                                error_log("Class permission assignment for class {$class_id}: " . ($permission_result ? 'Success' : 'Failed'));
                            }
                        }
                        
                        $success_redirect = true;
                        $message = 'Registration successful! You will be redirected to the login page in 5 seconds.';
                        $_POST = []; // Clear form fields on success
                    } else {
                        error_log("User creation failed. UserData: " . print_r($userData, true));
                        
                        // Try to get more specific error information
                        $errorInfo = $db->errorInfo();
                        error_log("Database error info: " . print_r($errorInfo, true));
                        
                        $error = 'Failed to complete registration. Please try again.';
                    }
                } catch (Exception $e) {
                    error_log("Exception during user creation: " . $e->getMessage());
                    error_log("Exception trace: " . $e->getTraceAsString());
                    $error = 'Failed to complete registration. Please try again.';
                }
            }
        }
    }
}

// HTML part of the registration form
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php 
    // Set dynamic page variables
    $page_title = ($is_admin_creating_user ? 'Add New User' : 'Register Yourself') . ' - ' . (defined('APP_NAME') ? APP_NAME : 'Tarjuma Tul Quran Project');
    $page_description = $is_admin_creating_user ? 
       'Create a new user account in the Learning management system.' : 
           'Join our comprehensive Tarjuma Tul Quran Project for better understanding of Quran and enhance your Translation ability.';
    $page_url = (defined('BASE_URL') ? BASE_URL : 'https://theteacher.pk/') . 'register.php';
    $og_image = (defined('BASE_URL') ? BASE_URL : 'https://theteacher.pk/') . 'assets/images/logo/og-image.png';
    ?>
    
    <!-- Title -->
    <title><?php echo $page_title; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="syllabus management, education platform, learning system, zoom integration, online classes">
    <meta name="author" content="<?php echo defined('APP_NAME') ? APP_NAME : 'LMS TheTeacher.pk'; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo $og_image; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo defined('APP_NAME') ? APP_NAME : 'LMS TheTeacher.pk'; ?>">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="<?php echo $og_image; ?>">
    <meta name="twitter:site" content="@edmate_learning">
    
    <!-- Additional Meta Tags for better sharing -->
    <meta property="fb:app_id" content="your_facebook_app_id">
    <meta name="theme-color" content="#3D7FF9">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $page_url; ?>">
    

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

    <?php if (!empty($success_redirect)): ?>
        <meta http-equiv="refresh" content="3;url=login.php">
    <?php endif; ?>
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

    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24">
            <img src="assets/images/thumbs/newbg.jpg" alt="">
        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="index.php" class="auth-right__logo">
                    <img width="300px" src="assets/images/logo/logo.png" alt="">
                </a>
                <h2 class="mb-8"><?php echo $is_admin_creating_user ? 'Add New User' : 'Sign Up'; ?></h2>
                <p class="text-gray-600 text-15 mb-32"><?php echo $is_admin_creating_user ? 'Create a new user account' : 'Please create your account and start the learning journey'; ?></p>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Debug Information (remove in production) -->
                <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
                <div class="alert alert-info mb-24">
                    <strong>Debug Info:</strong><br>
                    Database Connected: <?php echo $db ? 'Yes' : 'No'; ?><br>
                    Available Classes: <?php echo count($available_classes); ?><br>
                    Is Admin Mode: <?php echo $is_admin_creating_user ? 'Yes' : 'No'; ?><br>
                    POST Method: <?php echo $_SERVER['REQUEST_METHOD'] === 'POST' ? 'Yes' : 'No'; ?>
                </div>
                <?php endif; ?>

                <form action="#" method="POST" id="registrationForm">
                    <div class="row">
                        <div class="col-md-6 mb-24">
                            <label for="full_name" class="form-label mb-8 h6">Full Name <span class="text-danger text-bold">*</span> </label>
                            <div class="position-relative">
                                <input type="text" class="form-control py-11 ps-40" name="full_name" id="full_name" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-24">
                            <label for="username" class="form-label mb-8 h6">Username</label>
                            <div class="position-relative">
                                <input type="text" class="form-control py-11 ps-40" name="username" id="username" placeholder="Choose a username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user-circle"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-24">
                            <label for="email" class="form-label mb-8 h6">Email Address <span class="text-danger text-bold">*</span> </label>
                            <div class="position-relative">
                                <input type="email" class="form-control py-11 ps-40" name="email" id="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-envelope"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-24">
                            <label for="phone" class="form-label mb-8 h6">Phone Number <span class="text-danger text-bold">*</span> </label>
                            <div class="position-relative">
                                <input type="text" class="form-control py-11 ps-40" name="phone" id="phone" placeholder="Enter your phone number" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-phone"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-24">
                            <label for="gender" class="form-label mb-8 h6">Gender</label>
                            <select name="gender" id="gender" class="form-control py-11" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-24">
                            <label for="address" class="form-label mb-8 h6">Address <span class="text-danger text-bold">*</span> </label>
                            <div class="position-relative">
                                <textarea class="form-control py-11 ps-40" name="address" id="address" placeholder="Enter your address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-map-pin"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-24">
                            <label for="password" class="form-label mb-8 h6">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control py-11 ps-40" name="password" id="password" placeholder="Enter password" required>
                                <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" onclick="togglePassword('password')"></span>
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                            </div>
                            <span class="text-gray-900 text-15 mt-4">Must be at least 6 characters</span>
                        </div>
                        <div class="col-md-6 mb-24">
                            <label for="confirm_password" class="form-label mb-8 h6">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control py-11 ps-40" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                                <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" onclick="togglePassword('confirm_password')"></span>
                                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                            </div>
                        </div>
                    </div>

                    <?php if (!$is_admin_creating_user): // Only show for self-registration ?>
                    <div class="mb-24" style="display: none;">
                        <label class="form-label mb-8 h6">Select Classes (Optional)</label>
                        <div class="row">
                            <?php if (!empty($available_classes)): ?>
                                <?php foreach ($available_classes as $class): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="classes[]" value="<?php echo $class['id']; ?>" id="class_<?php echo $class['id']; ?>"
                                                <?php echo (isset($_POST['classes']) && in_array($class['id'], $_POST['classes'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="class_<?php echo $class['id']; ?>">
                                                <?php echo htmlspecialchars($class['class_name']); ?> (<?php echo htmlspecialchars($class['class_code']); ?>)
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No classes available at the moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($is_admin_creating_user): // Admin-specific fields ?>
                    <div class="mb-24">
                        <label for="organization_id" class="form-label mb-8 h6">Organization</label>
                        <select name="organization_id" id="organization_id" class="form-control py-11" onchange="loadSchools(this.value)">
                            <option value="">Select Organization</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['id']; ?>" <?php echo (isset($_POST['organization_id']) && $_POST['organization_id'] == $org['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($org['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-24">
                        <label for="school_id" class="form-label mb-8 h6">School</label>
                        <select name="school_id" id="school_id" class="form-control py-11">
                            <option value="">Select School</option>
                            <!-- Schools will be loaded dynamically via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-24">
                        <label for="role_id" class="form-label mb-8 h6">Role</label>
                        <select name="role_id" id="role_id" class="form-control py-11" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles_for_dropdown as $r_id => $r_name): ?>
                                <option value="<?php echo $r_id; ?>" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == $r_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-24">
                        <label for="status" class="form-label mb-8 h6">Status</label>
                        <select name="status" id="status" class="form-control py-11">
                            <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="pending" <?php echo (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <!-- Classes selection for admin-created users -->
                    <div class="mb-24" style="display: none;">
                        <label class="form-label mb-8 h6">Select Classes</label>
                        <div class="row">
                            <?php if (!empty($available_classes)): ?>
                                <?php foreach ($available_classes as $class): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="classes[]" value="<?php echo $class['id']; ?>" id="admin_class_<?php echo $class['id']; ?>"
                                                <?php echo (isset($_POST['classes']) && in_array($class['id'], $_POST['classes'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="admin_class_<?php echo $class['id']; ?>">
                                                <?php echo htmlspecialchars($class['class_name']); ?> (<?php echo htmlspecialchars($class['class_code']); ?>)
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No classes available at the moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!$is_admin_creating_user): ?>
                    <div class="mb-32 flex-between flex-wrap gap-8">
                        <div class="form-check mb-0 flex-shrink-0">
                            <input class="form-check-input flex-shrink-0 rounded-4" type="checkbox" value="" id="terms" required>
                            <label class="form-check-label text-15 flex-grow-1" for="terms">I agree to the <a href="#" class="text-main-600">Terms and Conditions</a></label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-main rounded-pill w-100" id="submitBtn">
                        <?php echo $is_admin_creating_user ? 'Add User' : 'Sign Up'; ?>
                    </button>

                    <?php if (!$is_admin_creating_user): ?>
                    <p class="mt-32 text-gray-600 text-center">Already have an account?
                        <a href="login.php" class="text-main-600 hover-text-decoration-underline"> Log In</a>
                    </p>

                    <div class="divider my-32 position-relative text-center">
                        <span class="divider__text text-gray-600 text-13 fw-medium px-26 bg-white">or</span>
                    </div>

                    <ul class="flex-align gap-10 flex-wrap justify-content-center">
                        <li>
                            <a href="oauth_start.php?provider=google&purpose=register" class="w-38 h-38 flex-center rounded-6 text-google-600 bg-google-50 hover-bg-google-600 hover-text-white text-lg">
                                <i class="ph ph-google-logo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="oauth_start.php?provider=microsoft&purpose=register" class="w-38 h-38 flex-center rounded-6 text-facebook-600 bg-facebook-50 hover-bg-facebook-600 hover-text-white text-lg">
                                <i class="ph ph-microsoft-outlook-logo"></i>
                            </a>
                        </li>
                        <!-- Removed Facebook and Twitter as they are not implemented for OAuth -->
                    </ul>
                    <?php endif; ?>
                    
                </form>
            </div>
        </div>
    </section>

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
