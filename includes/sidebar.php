<?php
// Get current page name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

require_once 'config/database.php';
require_once 'classes/User.php';
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Get accessible class IDs for the current user
$accessible_classes_raw = [];
$can_access_all_classes_flag = false;

if ($current_user) {
    $accessible_classes_raw = $user->getAccessibleClasses($current_user);
    $can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;
}

?>

<div class="col-12 col-md-3 col-lg-2 px-0 sidebar-wrapper d-md-block">
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel"><?php echo APP_NAME; ?></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0 sidebar">
            <?php include 'includes/sidebar_content.php'; ?>
        </div>
    </div>
    <!-- Desktop sidebar content -->
    <div class="sidebar p-3 shadow-lg d-none d-md-block" id="desktop-sidebar-content">
        <?php include 'includes/sidebar_content.php'; ?>
    </div>
</div>
