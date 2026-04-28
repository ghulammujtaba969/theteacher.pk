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
$solo_student_has_batch = false;

if ($current_user) {
    $accessible_classes_raw = $user->getAccessibleClasses($current_user);
    $can_access_all_classes_flag = ($current_user['can_access_all_classes'] ?? 0) == 1;
    if ($user_role === 'solo_student') {
        try {
            $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM batch_enrollments WHERE user_id = :uid AND enrollment_status IN ('pending','active')");
            $stmt->bindValue(':uid', $current_user['id'] ?? ($_SESSION['user_id'] ?? 0), PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $solo_student_has_batch = (int)($row['cnt'] ?? 0) > 0;
        } catch (Exception $e) {
            $solo_student_has_batch = false;
        }
    }
}

// Get pending inquiries count for admins
$pending_inquiries_count = 0;
if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin'])) {
    try {
        $pending_query = "SELECT COUNT(*) as pending_count FROM class_inquiries WHERE status = 'pending'";
        $pending_stmt = $db->prepare($pending_query);
        $pending_stmt->execute();
        $pending_result = $pending_stmt->fetch(PDO::FETCH_ASSOC);
        $pending_inquiries_count = $pending_result['pending_count'] ?? 0;
    } catch (Exception $e) {
        // Handle error silently
        $pending_inquiries_count = 0;
    }
}

?>

<aside class="sidebar">
    <!-- sidebar close btn -->
    <button type="button" class="sidebar-close-btn text-gray-500 hover-text-white hover-bg-main-600 text-md w-24 h-24 border border-gray-100 hover-border-main-600 d-xl-none d-flex flex-center rounded-circle position-absolute"><i class="ph ph-x"></i></button>
    <!-- sidebar close btn -->

    <a href="dashboard.php" class="sidebar__logo text-center p-20 position-sticky inset-block-start-0 bg-white w-100 z-1 pb-10">
        <img src="assets/images/logo/logo.png" alt="<?php echo APP_NAME; ?>">
    </a>

    <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
        <div class="p-20 pt-10">
            <ul class="sidebar-menu">
                <li class="sidebar-menu__item <?php echo ($current_page == 'dashboard') ? 'activePage' : ''; ?>">
                    <a href="dashboard.php" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-squares-four"></i></span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <?php if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin', 'teacher'])): ?>
                    <!-- Classes Menu -->
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'classes' || $current_page == 'subjects' || $current_page == 'syllabi') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">Classes</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <li class="sidebar-submenu__item">
                                <a href="classes.php" class="sidebar-submenu__link <?php echo ($current_page == 'classes' && !isset($_GET['action'])) ? 'activePage' : ''; ?>"> All Classes </a>
                            </li>
                            <?php if (in_array($user_role, ['super_admin'])): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="classes.php?action=add" class="sidebar-submenu__link <?php echo ($current_page == 'classes' && isset($_GET['action']) && $_GET['action'] == 'add') ? 'activePage' : ''; ?>"> Add Class </a>
                                </li>
                            <?php endif; ?>
                            <li class="sidebar-submenu__item">
                                <a href="subjects.php" class="sidebar-submenu__link <?php echo ($current_page == 'subjects') ? 'activePage' : ''; ?>"> Subjects </a>
                            </li>
                            <li class="sidebar-submenu__item">
                                <a href="syllabi.php" class="sidebar-submenu__link <?php echo ($current_page == 'syllabi') ? 'activePage' : ''; ?>"> Syllabi </a>
                            </li>
                        </ul>
                        <!-- Submenu End -->
                    </li>

                    <!-- Courses Menu -->
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'courses' || $current_page == 'course-syllabi') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-book-open"></i></span>
                            <span class="text">Courses</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <li class="sidebar-submenu__item">
                                <a href="courses.php" class="sidebar-submenu__link <?php echo ($current_page == 'courses' && !isset($_GET['action'])) ? 'activePage' : ''; ?>"> All Courses </a>
                            </li>
                            <?php if (in_array($user_role, ['super_admin'])): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="courses.php?action=add" class="sidebar-submenu__link <?php echo ($current_page == 'courses' && isset($_GET['action']) && $_GET['action'] == 'add') ? 'activePage' : ''; ?>"> Add Course </a>
                                </li>
                            <?php endif; ?>
                            <li class="sidebar-submenu__item">
                                <a href="course-syllabi.php" class="sidebar-submenu__link <?php echo ($current_page == 'course-syllabi') ? 'activePage' : ''; ?>"> Course Syllabi </a>
                            </li>
                        </ul>
                        <!-- Submenu End -->
                    </li>


                <?php endif; ?>
                <!-- Batch Management with Dropdown -->
                <?php if (in_array($user_role, ['super_admin'])): ?>
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'batches') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-users-three"></i></span>
                            <span class="text">Batches</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-submenu__item">
                                <a href="batches.php" class="sidebar-submenu__link">All Batches</a>
                            </li>
                            <?php if (in_array($user_role, ['super_admin'])): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="batches.php?action=add" class="sidebar-submenu__link">Create Batch</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php
                // Display individually assigned classes for users who don't have access to all classes
                if (!$can_access_all_classes_flag && !empty($accessible_classes_raw) && $user_role !== 'super_admin'):
                ?>
                    <li class="sidebar-menu__item has-dropdown">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-chalkboard-teacher"></i></span>
                            <span class="text">My Assigned Classes</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <?php foreach ($accessible_classes_raw as $assigned_class): ?>
                                <li class="sidebar-submenu__item">
                                    <?php 
                                        $isCourse = (isset($assigned_class['type']) && $assigned_class['type'] === 'course');
                                        $targetPage = $isCourse ? 'courses.php' : 'classes.php';
                                        $isActive = (in_array($current_page, ['classes','courses']) && isset($_GET['id']) && $_GET['id'] == $assigned_class['id']);
                                    ?>
                                    <a href="<?php echo $targetPage; ?>?id=<?php echo $assigned_class['id']; ?>" class="sidebar-submenu__link <?php echo $isActive ? 'activePage' : ''; ?>">
                                        <?php echo htmlspecialchars($assigned_class['class_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <!-- Submenu End -->
                    </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['solo_student', 'student'])): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'my-learning') ? 'activePage' : ''; ?>">
                        <a href="my-learning.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">My Learning</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin', 'teacher']) || ($user_role === 'solo_student' && $solo_student_has_batch)): ?>

                    <li class="sidebar-menu__item <?php echo ($current_page == 'lectures') ? 'activePage' : ''; ?>">
                        <a href="lectures.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-books"></i></span>
                            <span class="text">Lectures</span>
                        </a>
                    </li>
                <?php endif; ?>

                

                <?php if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin'])): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'users') ? 'activePage' : ''; ?>">
                        <a href="users.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-users-three"></i></span>
                            <span class="text">Users</span>
                        </a>
                    </li>
                    
                    
                <?php endif; ?>

                <?php if ($user_role === 'super_admin'): ?>
                   <li class="sidebar-menu__item <?php echo ($current_page == 'solo-students') ? 'activePage' : ''; ?>">
                        <a href="solo-students.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-student"></i></span>
                            <span class="text">Solo Students</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user_role === 'super_admin'): ?>
                    <!-- NEW: Class Inquiries Menu Item -->
                    <li class="sidebar-menu__item <?php echo ($current_page == 'class-inquiries') ? 'activePage' : ''; ?>">
                        <a href="class-inquiries.php" class="sidebar-menu__link">
                            <span class="icon position-relative">
                                <i class="ph ph-clipboard-text"></i>

                            </span>
                            <span class="text">Class Inquiries</span>
                            <?php if ($pending_inquiries_count > 0): ?>
                                <span class="badge bg-warning text-dark ms-auto rounded-pill" style="font-size: 10px; padding: 2px 6px;">
                                    <?php echo $pending_inquiries_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($user_role === 'super_admin'): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'organizations') ? 'activePage' : ''; ?>">
                        <a href="organizations.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-buildings"></i></span>
                            <span class="text">Organizations</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($user_role, ['super_admin', 'organization_admin'])): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'schools') ? 'activePage' : ''; ?>">
                        <a href="schools.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">Schools</span>
                        </a>
                    </li>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'class-access') ? 'activePage' : ''; ?>">
                        <a href="class-access.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-key"></i></span>
                            <span class="text">Class Access</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($user_role === 'super_admin'): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'pptx_page_manager') ? 'activePage' : ''; ?>">
                        <a href="pptx_page_manager.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-presentation-chart"></i></span>
                            <span class="text">PPTX Page Manager</span>
                        </a>
                    </li>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'pending_registrations') ? 'activePage' : ''; ?>">
                        <a href="pending_registrations.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-user-plus"></i></span>
                            <span class="text">Pending Registrations</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin', 'teacher']) || ($user_role === 'solo_student' && $solo_student_has_batch)): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'zoom-meetings') ? 'activePage' : ''; ?>">
                        <a href="zoom-meetings.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-video-camera"></i></span>
                            <span class="text">Zoom Meetings</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="sidebar-menu__item">
                    <span class="text-gray-300 text-sm px-20 pt-20 fw-semibold border-top border-gray-100 d-block text-uppercase">Settings</span>
                </li>
                <li class="sidebar-menu__item <?php echo ($current_page == 'profile') ? 'activePage' : ''; ?>">
                    <a href="profile.php" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-user"></i></span>
                        <span class="text">My Profile</span>
                    </a>
                </li>


                <?php if (in_array($user_role, ['super_admin', 'organization_admin', 'school_admin', '', ''])): ?>
                    <li class="sidebar-menu__item">
                        <a href="profile.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-gear"></i></span>
                            <span class="text">Account Settings</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="sidebar-menu__item">
                    <a href="logout.php" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-sign-out"></i></span>
                        <span class="text">Logout</span>
                    </a>
                </li>

            </ul>
        </div>



    </div>

</aside>
