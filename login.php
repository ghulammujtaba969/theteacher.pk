<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create User instance with database connection
$userModel = new User($db);

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } else {
        $loggedInUser = $userModel->login($username, $password);

        if ($loggedInUser) {
            // Persist key fields
            $_SESSION['user_id'] = $loggedInUser['id'] ?? null;
            $_SESSION['username'] = $loggedInUser['username'] ?? '';
            $_SESSION['email'] = $loggedInUser['email'] ?? '';

            // Normalize role for super admin checks used elsewhere
            $roleSlug = '';
            if (!empty($loggedInUser['role'])) {
                $roleSlug = $loggedInUser['role'];
            } elseif (!empty($loggedInUser['role_name'])) {
                $roleSlug = strtolower(str_replace(' ', '_', $loggedInUser['role_name']));
            }
            $_SESSION['role'] = $roleSlug;

            // Also store full user array for pages expecting it
            $_SESSION['user'] = $loggedInUser;
            unset($_SESSION['_permissions']);

            // Redirect to dashboard
            redirect('dashboard.php');
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>Sign In - <?php echo APP_NAME; ?></title>
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

    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24">
            <img src="assets/images/thumbs/newbg.jpg" alt="">
        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="index.php" class="auth-right__logo">
                    <img width="300px" src="assets/images/logo/logo.png" alt="">
                </a>
                <h2 class="mb-8">Welcome Back! &#128075;</h2>
                <p class="text-gray-600 text-15 mb-32">Please sign in to your account and start the adventure</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
                        <i class="ph ph-warning-circle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-24">
                        <label for="username" class="form-label mb-8 h6">Email or Username</label>
                        <div class="position-relative">
                            <input type="text" class="form-control py-11 ps-40" name="username" id="username" placeholder="Type your username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                        </div>
                    </div>
                    <div class="mb-24">
                        <label for="password" class="form-label mb-8 h6">Current Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control py-11 ps-40" name="password" id="password" placeholder="Enter Current Password" required>
                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" onclick="togglePassword('password')"></span>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                        </div>
                    </div>
                    <div class="mb-32 flex-between flex-wrap gap-8">
                        <div class="form-check mb-0 flex-shrink-0">
                            <input class="form-check-input flex-shrink-0 rounded-4" type="checkbox" value="" id="remember">
                            <label class="form-check-label text-15 flex-grow-1" for="remember">Remember Me </label>
                        </div>
                        <a href="forgot-password.html" class="text-main-600 hover-text-decoration-underline text-15 fw-medium">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-main rounded-pill w-100">Sign In</button>
                    <p class="mt-32 text-gray-600 text-center">New on our platform?
                        <a href="register.php" class="text-main-600 hover-text-decoration-underline">Create an account</a>
                    </p>

                    <div class="divider my-32 position-relative text-center">
                        <span class="divider__text text-gray-600 text-13 fw-medium px-26 bg-white">or</span>
                    </div>

                    <ul class="flex-align gap-10 flex-wrap justify-content-center">
                        <li>
                            <a href="oauth_start.php?provider=google" class="w-50 h-50 flex-center rounded-6 text-white bg-google-600 hover-bg-google-700 text-xl">
                                <i class="ph ph-google-logo"></i>
                            </a>
                        </li>
                        <li>
                            <a href="oauth_start.php?provider=microsoft" class="w-50 h-50 flex-center rounded-6 text-facebook-600 bg-facebook-50 hover-bg-facebook-600 hover-text-white text-xl">
                                <i class="ph ph-microsoft-outlook-logo"></i>
                            </a>
                        </li>
                    </ul>
                    
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

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash';
            }
        }
    </script>

</body>
</html>
