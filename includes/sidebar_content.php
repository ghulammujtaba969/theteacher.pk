<?php
// This file contains the reusable content for the sidebar, to be included in includes/sidebar.php
// It expects $current_page, $current_user, $user_role, and APP_NAME to be defined in the parent scope.
?>

<div class="text-center mb-4 py-2 border-bottom border-secondary-subtle">
    <i class="fas fa-graduation-cap fa-3x mb-2 text-white"></i>
    <h5 class="text-white fw-bold mb-0"><?php echo APP_NAME; ?></h5>
    <small class="text-info"><?php echo htmlspecialchars($current_user['role_name'] ?? 'Guest'); ?> Panel</small>
</div>

<nav class="nav flex-column mt-4">
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'dashboard') ? 'active-link' : ''; ?>" href="dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
    </a>
    <?php if (can('classes.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'classes') ? 'active-link' : ''; ?>" href="classes.php">
        <i class="fas fa-school me-2"></i>My Classes
    </a>
    <?php endif; ?>

    <?php 
    // Display individually assigned classes for users who don't have access to all classes
    if (!$can_access_all_classes_flag && !empty($accessible_classes_raw) && $user_role !== 'super_admin'): 
    ?>
    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
        <span>My Assigned Classes</span>
    </h6>
    <?php foreach ($accessible_classes_raw as $assigned_class): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'classes' && isset($_GET['id']) && $_GET['id'] == $assigned_class['id']) ? 'active-link' : ''; ?>" href="classes.php?id=<?php echo $assigned_class['id']; ?>">
        <i class="fas fa-chalkboard-teacher me-2"></i><?php echo htmlspecialchars($assigned_class['class_name']); ?>
    </a>
    <?php endforeach; ?>
    <hr class="my-3 border-light opacity-50">
    <?php endif; ?>

    <?php if (can_any(['subjects.view', 'syllabi.view', 'lectures.view'])): ?>
    <?php if (can('subjects.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'subjects') ? 'active-link' : ''; ?>" href="subjects.php">
        <i class="fas fa-book me-2"></i>Subjects
    </a>
    <?php endif; ?>
    <?php if (can('syllabi.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'syllabi') ? 'active-link' : ''; ?>" href="syllabi.php">
        <i class="fas fa-list-alt me-2"></i>Syllabi
    </a>
    <?php endif; ?>
    <?php if (can('lectures.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'lectures') ? 'active-link' : ''; ?>" href="lectures.php">
        <i class="fas fa-play-circle me-2"></i>Lectures
    </a>
    <?php endif; ?>
    <?php endif; ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'profile') ? 'active-link' : ''; ?>" href="profile.php">
        <i class="fas fa-user me-2"></i>My Profile
    </a>
    <?php if (can('users.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'users') ? 'active-link' : ''; ?>" href="users.php">
        <i class="fas fa-users me-2"></i>Users
    </a>
    <?php endif; ?>
    <?php if (can('organizations.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'organizations') ? 'active-link' : ''; ?>" href="organizations.php">
        <i class="fas fa-building me-2"></i>Organizations
    </a>
    <?php endif; ?>
    <?php if (can('schools.view') || can('class_access.view')): ?>
    <?php if (can('schools.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'schools') ? 'active-link' : ''; ?>" href="schools.php">
        <i class="fas fa-school me-2"></i>Schools
    </a>
    <?php endif; ?>
    <?php if (can('class_access.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'class-access') ? 'active-link' : ''; ?>" href="class-access.php">
        <i class="fas fa-key me-2"></i>Class Access
    </a>
    <?php endif; ?>
    <?php endif; ?>
    <?php if (can('pptx.manage')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'pptx_page_manager') ? 'active-link' : ''; ?>" href="pptx_page_manager.php">
        <i class="fas fa-file-powerpoint me-2"></i>PPTX Page Manager
    </a>
    <?php endif; ?>
    <?php if (can('pending_registrations.view')): ?>
    <a class="nav-link text-white d-flex align-items-center rounded py-2 mb-2 <?php echo ($current_page == 'pending_registrations') ? 'active-link' : ''; ?>" href="pending_registrations.php">
        <i class="fas fa-file-powerpoint me-2"></i>Pending Registrations
    </a>
    <?php endif; ?>
    <hr class="my-3 border-light opacity-50">
    <a class="nav-link text-white d-flex align-items-center rounded py-2 <?php echo ($current_page == 'logout') ? 'active-link' : ''; ?>" href="logout.php">
        <i class="fas fa-sign-out-alt me-2"></i>Logout
    </a>
</nav>
