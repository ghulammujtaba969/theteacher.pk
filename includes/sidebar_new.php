<?php
// Get current page name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/BatchEnrollment.php';
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$enrollmentModel = new BatchEnrollment($db);

// Get accessible class IDs for the current user
$accessible_classes_raw = [];
$sidebar_learning_items = [];
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

    foreach ($accessible_classes_raw as $class_item) {
        $class_id = (int)($class_item['id'] ?? 0);
        if ($class_id <= 0) {
            continue;
        }

        $sidebar_learning_items[$class_id] = [
            'id' => $class_id,
            'class_name' => $class_item['class_name'] ?? '',
            'type' => $class_item['type'] ?? 'class',
            'source' => 'access',
        ];
    }

    if ($user_role === 'solo_student') {
        try {
            $enrollments = $enrollmentModel->getEnrollmentsByUser($current_user['id'] ?? ($_SESSION['user_id'] ?? 0));
            foreach ($enrollments as $enrollment) {
                $status = $enrollment['enrollment_status'] ?? '';
                $class_id = (int)($enrollment['class_id'] ?? 0);
                if ($class_id <= 0 || !in_array($status, ['pending', 'active', 'completed'], true)) {
                    continue;
                }

                $sidebar_learning_items[$class_id] = [
                    'id' => $class_id,
                    'class_name' => $enrollment['class_name'] ?? '',
                    'type' => $enrollment['class_type'] ?? 'class',
                    'source' => 'enrollment',
                ];
            }
        } catch (Exception $e) {
            // Keep sidebar rendering even if enrollment data is unavailable.
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

                <?php if (can_any(['classes.view', 'courses.view', 'subjects.view', 'syllabi.view'])): ?>
                    <?php if (can_any(['classes.view', 'subjects.view', 'syllabi.view'])): ?>
                    <!-- Classes Menu -->
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'classes' || $current_page == 'subjects' || $current_page == 'syllabi') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">Classes</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <?php if (can('classes.view')): ?>
                            <li class="sidebar-submenu__item">
                                <a href="classes.php" class="sidebar-submenu__link <?php echo ($current_page == 'classes' && !isset($_GET['action'])) ? 'activePage' : ''; ?>"> All Classes </a>
                            </li>
                            <?php endif; ?>
                            <?php if (can('classes.create')): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="classes.php?action=add" class="sidebar-submenu__link <?php echo ($current_page == 'classes' && isset($_GET['action']) && $_GET['action'] == 'add') ? 'activePage' : ''; ?>"> Add Class </a>
                                </li>
                            <?php endif; ?>
                            <?php if (can('subjects.view')): ?>
                            <li class="sidebar-submenu__item">
                                <a href="subjects.php" class="sidebar-submenu__link <?php echo ($current_page == 'subjects') ? 'activePage' : ''; ?>"> Subjects </a>
                            </li>
                            <?php endif; ?>
                            <?php if (can('syllabi.view')): ?>
                            <li class="sidebar-submenu__item">
                                <a href="syllabi.php" class="sidebar-submenu__link <?php echo ($current_page == 'syllabi') ? 'activePage' : ''; ?>"> Syllabi </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <!-- Submenu End -->
                    </li>
                    <?php endif; ?>

                    <?php if (can_any(['courses.view', 'syllabi.view'])): ?>
                    <!-- Courses Menu -->
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'courses' || $current_page == 'course-syllabi') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-book-open"></i></span>
                            <span class="text">Courses</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <?php if (can('courses.view')): ?>
                            <li class="sidebar-submenu__item">
                                <a href="courses.php" class="sidebar-submenu__link <?php echo ($current_page == 'courses' && !isset($_GET['action'])) ? 'activePage' : ''; ?>"> All Courses </a>
                            </li>
                            <?php endif; ?>
                            <?php if (can('courses.create')): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="courses.php?action=add" class="sidebar-submenu__link <?php echo ($current_page == 'courses' && isset($_GET['action']) && $_GET['action'] == 'add') ? 'activePage' : ''; ?>"> Add Course </a>
                                </li>
                            <?php endif; ?>
                            <?php if (can('syllabi.view')): ?>
                            <li class="sidebar-submenu__item">
                                <a href="course-syllabi.php" class="sidebar-submenu__link <?php echo ($current_page == 'course-syllabi') ? 'activePage' : ''; ?>"> Course Syllabi </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <!-- Submenu End -->
                    </li>


                <?php endif; ?>
                <!-- Batch Management with Dropdown -->
                <?php if (can('batches.view')): ?>
                    <li class="sidebar-menu__item has-dropdown <?php echo ($current_page == 'batches') ? 'activePage' : ''; ?>">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-users-three"></i></span>
                            <span class="text">Batches</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li class="sidebar-submenu__item">
                                <a href="batches.php" class="sidebar-submenu__link">All Batches</a>
                            </li>
                            <?php if (can('batches.create')): ?>
                                <li class="sidebar-submenu__item">
                                    <a href="batches.php?action=add" class="sidebar-submenu__link">Create Batch</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php
                // Display assigned/enrolled classes and courses for users who don't have access to everything
                if (!$can_access_all_classes_flag && !empty($sidebar_learning_items) && $user_role !== 'super_admin'):
                ?>
                    <li class="sidebar-menu__item has-dropdown">
                        <a href="javascript:void(0)" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-chalkboard-teacher"></i></span>
                            <span class="text">My Courses / Classes</span>
                        </a>
                        <!-- Submenu start -->
                        <ul class="sidebar-submenu">
                            <?php foreach ($sidebar_learning_items as $assigned_class): ?>
                                <li class="sidebar-submenu__item">
                                    <?php 
                                        $isCourse = (isset($assigned_class['type']) && $assigned_class['type'] === 'course');
                                        $typeLabel = $isCourse ? 'Course' : 'Class';
                                        $targetPage = $user_role === 'solo_student' ? 'course-detail.php' : ($isCourse ? 'courses.php' : 'classes.php');
                                        $isActive = (in_array($current_page, ['classes','courses','course-detail'], true) && isset($_GET['id']) && $_GET['id'] == $assigned_class['id']);
                                    ?>
                                    <a href="<?php echo $targetPage; ?>?id=<?php echo $assigned_class['id']; ?>" class="sidebar-submenu__link <?php echo $isActive ? 'activePage' : ''; ?>">
                                        <span class="text-11 text-main-600 fw-semibold me-4"><?php echo $typeLabel; ?>:</span>
                                        <?php echo htmlspecialchars($assigned_class['class_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <!-- Submenu End -->
                    </li>
                <?php endif; ?>

                <?php if (can_any(['lectures.view', 'enrollments.self_enroll'])): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'my-learning') ? 'activePage' : ''; ?>">
                        <a href="my-learning.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">My Learning</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (can('lectures.view') || ($user_role === 'solo_student' && $solo_student_has_batch)): ?>

                    <li class="sidebar-menu__item <?php echo ($current_page == 'lectures') ? 'activePage' : ''; ?>">
                        <a href="lectures.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-books"></i></span>
                            <span class="text">Lectures</span>
                        </a>
                    </li>
                <?php endif; ?>

                

                <?php if (can('users.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'users') ? 'activePage' : ''; ?>">
                        <a href="users.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-users-three"></i></span>
                            <span class="text">Users</span>
                        </a>
                    </li>
                    
                    
                <?php endif; ?>

                <?php if (can('roles.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'roles-permissions') ? 'activePage' : ''; ?>">
                        <a href="roles-permissions.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-shield-check"></i></span>
                            <span class="text">Roles & Permissions</span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (can('solo_students.view')): ?>
                   <li class="sidebar-menu__item <?php echo ($current_page == 'solo-students') ? 'activePage' : ''; ?>">
                        <a href="solo-students.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-student"></i></span>
                            <span class="text">Solo Students</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (can('inquiries.view')): ?>
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

                <?php if (can('organizations.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'organizations') ? 'activePage' : ''; ?>">
                        <a href="organizations.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-buildings"></i></span>
                            <span class="text">Organizations</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (can('schools.view') || can('class_access.view')): ?>
                    <?php if (can('schools.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'schools') ? 'activePage' : ''; ?>">
                        <a href="schools.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-graduation-cap"></i></span>
                            <span class="text">Schools</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (can('class_access.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'class-access') ? 'activePage' : ''; ?>">
                        <a href="class-access.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-key"></i></span>
                            <span class="text">Class Access</span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (can('pptx.manage') || can('pending_registrations.view')): ?>
                    <?php if (can('pptx.manage')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'pptx_page_manager') ? 'activePage' : ''; ?>">
                        <a href="pptx_page_manager.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-presentation-chart"></i></span>
                            <span class="text">PPTX Page Manager</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (can('pending_registrations.view')): ?>
                    <li class="sidebar-menu__item <?php echo ($current_page == 'pending_registrations') ? 'activePage' : ''; ?>">
                        <a href="pending_registrations.php" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-user-plus"></i></span>
                            <span class="text">Pending Registrations</span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (can('zoom.view') || ($user_role === 'solo_student' && $solo_student_has_batch)): ?>
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


                <?php if (can('profile.edit')): ?>
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
