<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/Role.php';
require_once 'classes/Organization.php';
require_once 'classes/School.php';
require_once 'classes/ClassModel.php';
require_once 'classes/ClassAccess.php';

// Check if user is logged in
require_permission('users.view', 'dashboard.php');

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$user = new User($db);
$role = new Role($db);
$organization = new Organization($db);
$school = new School($db);
$classModel = new ClassModel($db);
$classAccess = new ClassAccess($db);

if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_user_class_ids' && isset($_GET['user_id'])) {
    if (!is_logged_in() || !can('users.edit')) {
        http_response_code(403);
        exit;
    }

    $target_user_id = (int)$_GET['user_id'];
    if (!$user->canManageUser($current_user, $target_user_id)) {
        http_response_code(403);
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode($classAccess->getUserClassIds($target_user_id));
    exit;
}

// Check permissions - only certain roles can manage users
$message = '';
$error = '';

function normalize_access_class_ids($class_ids) {
    $ids = array_map('intval', (array)$class_ids);
    $ids = array_filter($ids, function ($id) {
        return $id > 0;
    });
    return array_values(array_unique($ids));
}

function get_access_grantable_class_ids($current_user, $user, $classModel) {
    if (($current_user['role_name'] ?? '') === 'Super Admin') {
        $stmt = $classModel->read([], null, false);
        return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id'));
    }

    return array_map('intval', array_column($user->getAccessibleClasses($current_user), 'id'));
}

function sync_user_class_access($target_user_id, $current_user, $user, $classAccess, $classModel, &$error) {
    if (!can('class_access.grant')) {
        return true;
    }

    if ((int)$target_user_id === (int)($current_user['id'] ?? 0)) {
        $error = 'You cannot change your own class or course access.';
        return false;
    }

    $target_user_data = $user->getById($target_user_id);
    if (!$target_user_data || !$user->canManageUser($current_user, $target_user_id)) {
        $error = 'You do not have permission to manage access for this user.';
        return false;
    }

    $current_role = $current_user['role_name'] ?? '';
    $target_role = $target_user_data['role_name'] ?? '';
    $selected_class_ids = normalize_access_class_ids($_POST['class_ids'] ?? []);
    $assign_all = isset($_POST['assign_all_classes']) && $_POST['assign_all_classes'] === 'on';

    if ($current_role !== 'Super Admin' && $assign_all) {
        $error = 'You cannot grant access to all classes and courses.';
        return false;
    }

    if ($current_role === 'Organization Admin' && !in_array($target_role, ['School Admin', 'Teacher', 'Solo Student', 'Student'], true)) {
        $error = 'You can only assign classes and courses to subordinates.';
        return false;
    }

    if ($current_role === 'School Admin' && !in_array($target_role, ['Teacher', 'Solo Student'], true)) {
        $error = 'You can only assign classes and courses to subordinates.';
        return false;
    }

    if ($current_role === 'Super Admin' && $assign_all) {
        if ($user->update($target_user_id, ['can_access_all_classes' => 1])) {
            $classAccess->revokeAllAccessForUser($target_user_id);
            return true;
        }

        $error = 'Failed to grant access to all classes and courses.';
        return false;
    }

    $grantable_class_ids = get_access_grantable_class_ids($current_user, $user, $classModel);
    $invalid_class_ids = array_diff($selected_class_ids, $grantable_class_ids);
    if (!empty($invalid_class_ids)) {
        $error = 'You can only assign classes and courses you have access to.';
        return false;
    }

    if (!$user->update($target_user_id, ['can_access_all_classes' => 0])) {
        $error = 'Failed to update class and course access.';
        return false;
    }

    $classAccess->revokeAllAccessForUser($target_user_id);
    foreach ($selected_class_ids as $class_id) {
        if (!$classAccess->assignAccess($target_user_id, $class_id, $current_user['id'])) {
            $error = 'Failed to assign one or more classes or courses.';
            return false;
        }
    }

    return true;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                if (!can('users.create')) permission_denied('users.php');
                $data = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'full_name' => trim($_POST['full_name']),
                    'password' => $_POST['password'],
                    'role_id' => $_POST['role_id'],
                    'status' => $_POST['status'],
                    'phone' => trim($_POST['phone'] ?? ''),
                    'gender' => $_POST['gender'] ?? null,
                    'address' => trim($_POST['address'] ?? '')
                ];

                // Automatically assign organization_id and school_id based on current admin's role
                if ($current_user['role_name'] === 'Organization Admin') {
                    $data['organization_id'] = $current_user['organization_id'];
                    $data['school_id'] = !empty($_POST['school_id']) ? $_POST['school_id'] : null;
                } elseif ($current_user['role_name'] === 'School Admin') {
                    $data['organization_id'] = $current_user['organization_id'];
                    $data['school_id'] = $current_user['school_id'];
                } else {
                    $data['organization_id'] = !empty($_POST['organization_id']) ? $_POST['organization_id'] : null;
                    $data['school_id'] = !empty($_POST['school_id']) ? $_POST['school_id'] : null;
                }
                
                $created_user_id = $user->create($data, $current_user['id']);
                if ($created_user_id) {
                    if (sync_user_class_access((int)$created_user_id, $current_user, $user, $classAccess, $classModel, $error)) {
                        $message = 'User created successfully!';
                    }
                } else {
                    $error = 'Failed to create user. Please try again.';
                }
                break;
                
            case 'update':
                if (!can('users.edit')) permission_denied('users.php');
                $user_id = $_POST['user_id'];
                if ($user->canManageUser($current_user, $user_id)) {
                    $should_update_password = !empty($_POST['password']);
                    if ($should_update_password && !can('users.reset_password')) {
                        permission_denied('users.php');
                    }

                    $data = [
                        'username' => trim($_POST['username']),
                        'email' => trim($_POST['email']),
                        'full_name' => trim($_POST['full_name']),
                        'role_id' => $_POST['role_id'],
                        'organization_id' => !empty($_POST['organization_id']) ? $_POST['organization_id'] : null,
                        'school_id' => !empty($_POST['school_id']) ? $_POST['school_id'] : null,
                        'status' => $_POST['status'],
                        'phone' => trim($_POST['phone'] ?? ''),
                        'gender' => $_POST['gender'] ?? null,
                        'address' => trim($_POST['address'] ?? '')
                    ];
                    
                    if ($user->update($user_id, $data)) {
                        if ($should_update_password) {
                            $user->updatePassword($user_id, $_POST['password']);
                        }
                        if (sync_user_class_access((int)$user_id, $current_user, $user, $classAccess, $classModel, $error)) {
                            $message = 'User updated successfully!';
                        }
                    } else {
                        $error = 'Failed to update user. Please try again.';
                    }
                } else {
                    $error = 'You do not have permission to update this user.';
                }
                break;
                
            case 'delete':
                if (!can('users.delete')) permission_denied('users.php');
                $user_id = $_POST['user_id'];
                if ($user->canManageUser($current_user, $user_id)) {
                    if ($user->delete($user_id)) {
                        $message = 'User deleted successfully!';
                    } else {
                        $error = 'Failed to delete user. Please try again.';
                    }
                } else {
                    $error = 'You do not have permission to delete this user.';
                }
                break;
        }
    }
}

// Get users based on current user's permissions
$users = $user->getUsersByPermission($current_user);
// Pagination
$per_page = isset($_GET['pp']) ? max(5, min(100, (int)$_GET['pp'])) : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total = is_array($users) ? count($users) : 0;
$pages = max(1, (int)ceil($total / $per_page));
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $per_page;
$paged_users = array_slice($users, $offset, $per_page);

$roles = $role->getAll();
$organizations = $organization->getAll();
$schools = $school->getAll();

// Filter roles and organizations based on current user's permissions
if ($current_user['role_name'] === 'Organization Admin') {
    $roles = array_filter($roles, function($r) {
        // Org Admin may add School Admins, Teachers, and Students
        return in_array($r['name'], ['School Admin', 'Teacher', 'Solo Student', 'Student']);
    });
    $organizations = array_filter($organizations, function($o) use ($current_user) {
        return $o['id'] == $current_user['organization_id'];
    });
    $schools = $school->getAll($current_user['organization_id']);
} elseif ($current_user['role_name'] === 'School Admin') {
    $roles = array_filter($roles, function($r) {
        return $r['name'] === 'Teacher';
    });
    $organizations = array_filter($organizations, function($o) use ($current_user) {
        return $o['id'] == $current_user['organization_id'];
    });
    $schools = array_filter($schools, function($s) use ($current_user) {
        return $s['id'] == $current_user['school_id'];
    });
}

$current_user_role_key = strtolower(str_replace(' ', '_', $current_user['role_name'] ?? ''));
if (($current_user['role_name'] ?? '') === 'Super Admin') {
    $classes_for_access = $classModel->read([], null, false)->fetchAll(PDO::FETCH_ASSOC);
} else {
    $classes_for_access = $user->getAccessibleClasses($current_user);
}
$can_manage_user_access = can('class_access.grant');

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>User Management - <?php echo APP_NAME; ?></title>
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

    <!-- ============================ Sidebar Start ============================ -->
    <?php include 'includes/sidebar_new.php'; ?>
    <!-- ============================ Sidebar End  ============================ -->

    <div class="dashboard-main-wrapper">
        <!-- ============================ Top Navbar Start ============================ -->
        <?php include 'includes/navbar_new.php'; ?>
        <!-- ============================ Top Navbar End ============================ -->
        
        <div class="dashboard-body">
            
            <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
                <!-- Breadcrumb Start -->
                <div class="breadcrumb mb-24">
                    <ul class="flex-align gap-4">
                        <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                        <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                        <li><span class="text-main-600 fw-normal text-15">Users</span></li>
                    </ul>
                </div>
                <!-- Breadcrumb End -->

                <!-- Breadcrumb Right Start -->
                <div class="flex-align gap-8 flex-wrap">
                    <?php if (can('users.create')): ?>
                    <button class="btn btn-main text-sm btn-sm px-24 rounded-pill py-12 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
                        <i class="ph ph-plus me-4"></i>
                        Add User 
                    </button>
                    <?php endif; ?>
                    <div class="position-relative text-gray-500 flex-align gap-4 text-13">
                        <span class="text-inherit">Sort by: </span>
                        <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                            <span class="text-lg"><i class="ph ph-funnel-simple"></i></span>
                            <select class="form-control ps-8 pe-20 py-16 border-0 text-inherit rounded-4 text-center">
                                <option value="1" selected>Latest</option>
                                <option value="1">Name</option>
                                <option value="1">Role</option>
                                <option value="1">Status</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                        <span class="text-lg"><i class="ph ph-layout"></i></span>
                        <select class="form-control ps-8 pe-20 py-16 border-0 text-inherit rounded-4 text-center" id="exportOptions">
                            <option value="" selected disabled>Export</option>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                </div>
                <!-- Breadcrumb Right End -->
            </div>

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
                <div class="card-body p-0 overflow-x-auto">
                    <table id="userTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="h6 text-gray-300">Users</th>
                                <th class="h6 text-gray-300">Email</th>
                                <th class="h6 text-gray-300">Role</th>
                                <th class="h6 text-gray-300">Organization</th>
                                <th class="h6 text-gray-300">School</th>
                                <th class="h6 text-gray-300">Status</th>
                                <th class="h6 text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paged_users as $u): ?>
                            <tr>
                                <td class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-align gap-8">
                                        <img src="<?php echo htmlspecialchars(user_avatar_url($u)); ?>" alt="" class="w-40 h-40 rounded-circle">
                                        <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($u['full_name'] ?? $u['username']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($u['email']); ?></span>
                                </td>
                                <td>
                                    <span class="text-13 py-2 px-8 bg-info-50 text-info-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                        <span class="w-6 h-6 bg-info-600 rounded-circle flex-shrink-0"></span>
                                        <?php echo htmlspecialchars($u['role_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($u['organization_name'] ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($u['school_name'] ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <span class="w-6 h-6 bg-success-600 rounded-circle flex-shrink-0"></span>
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="text-13 py-2 px-8 bg-danger-50 text-danger-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <span class="w-6 h-6 bg-danger-600 rounded-circle flex-shrink-0"></span>
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex-align gap-8">
                                        <?php if (can('users.edit') && $user->canManageUser($current_user, $u['id'])): ?>
                                        <button class="bg-main-50 text-main-600 py-2 px-14 rounded-pill hover-bg-main-600 hover-text-white text-sm" onclick="editUser(<?php echo htmlspecialchars(json_encode($u)); ?>)">
                                            <i class="ph ph-pencil-simple me-4"></i>Edit
                                        </button>
                                        <?php endif; ?>
                                        <?php if (can('users.delete') && $user->canManageUser($current_user, $u['id'])): ?>
                                        <button class="bg-danger-50 text-danger-600 py-2 px-14 rounded-pill hover-bg-danger-600 hover-text-white text-sm" onclick="deleteUser(<?php echo $u['id']; ?>, <?php echo htmlspecialchars(json_encode($u['username'])); ?>)">
                                            <i class="ph ph-trash me-4"></i>Delete
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer flex-between flex-wrap">
                    <?php 
                        $start_i = $total ? ($offset + 1) : 0; 
                        $end_i = min($offset + $per_page, $total);
                        $qs = $_GET; unset($qs['page']); unset($qs['pp']);
                        $base = basename($_SERVER['PHP_SELF']) . (empty($qs) ? '' : ('?' . http_build_query($qs)));
                        $build = function($p,$pp) use($base){ return $base . (strpos($base,'?')!==false? '&':'?') . 'page=' . $p . '&pp=' . $pp; };
                    ?>
                    <span class="text-gray-900">Showing <?php echo $start_i; ?> to <?php echo $end_i; ?> of <?php echo $total; ?> entries</span>
                    <ul class="pagination flex-align flex-wrap">
                        <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page>1? $build($page-1,$per_page):'#'; ?>">Prev</a>
                        </li>
                        <?php for ($p=1; $p<=$pages; $p++): ?>
                            <li class="page-item <?php echo $p==$page?'active':''; ?>">
                                <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $build($p,$per_page); ?>"><?php echo $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page>=$pages?'disabled':''; ?>">
                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="<?php echo $page<$pages? $build($page+1,$per_page):'#'; ?>">Next</a>
                        </li>
                    </ul>
                </div>
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

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <form method="POST" id="userForm">
                    <div class="modal-header bg-main-50 border-bottom border-gray-100">
                        <h5 class="modal-title" id="modalTitle">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-24">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="user_id" id="userId">
                        
                        <div class="row g-20">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label h6 mb-8">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-11" name="full_name" id="full_name" placeholder="Enter full name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label h6 mb-8">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-11" name="username" id="username" placeholder="Enter username" required>
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-6">
                                <label for="email" class="form-label h6 mb-8">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control py-11" name="email" id="email" placeholder="Enter email address" required>
                            </div>
                            <div class="col-md-6" id="passwordField">
                                <label for="password" class="form-label h6 mb-8">Password <span id="passwordRequiredMark" class="text-danger">*</span></label>
                                <input type="password" class="form-control py-11" name="password" id="password" placeholder="Enter password" required>
                                <small class="text-gray-400" id="passwordHelp" style="display: none;">Leave blank to keep current password</small>
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-4">
                                <label for="role_id" class="form-label h6 mb-8">Role <span class="text-danger">*</span></label>
                                <select class="form-select py-11" name="role_id" id="role_id" required onchange="handleRoleChange()">
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $r): ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="organization_id" class="form-label h6 mb-8">Organization</label>
                                <select class="form-select py-11" name="organization_id" id="organization_id" onchange="loadSchools()">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                    <option value="<?php echo $org['id']; ?>"><?php echo htmlspecialchars($org['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="school_id" class="form-label h6 mb-8">School</label>
                                <select class="form-select py-11" name="school_id" id="school_id">
                                    <option value="">Select School</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-6">
                                <label for="phone" class="form-label h6 mb-8">Phone Number</label>
                                <input type="text" class="form-control py-11" name="phone" id="phone" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label h6 mb-8">Gender</label>
                                <select class="form-select py-11" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label h6 mb-8">Status <span class="text-danger">*</span></label>
                                <select class="form-select py-11" name="status" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-16">
                            <label for="address" class="form-label h6 mb-8">Address</label>
                            <textarea class="form-control py-11" name="address" id="address" rows="2" placeholder="Enter address"></textarea>
                        </div>

                        <?php if ($can_manage_user_access): ?>
                        <div class="mt-24 border-top border-gray-100 pt-20" id="accessSection">
                            <div class="flex-between flex-wrap gap-8 mb-12">
                                <div>
                                    <h6 class="mb-4">Class & Course Access</h6>
                                    <p class="text-gray-500 text-sm mb-0">Grant this user access immediately after saving.</p>
                                </div>
                                <?php if (($current_user['role_name'] ?? '') === 'Super Admin'): ?>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="assign_all_classes" name="assign_all_classes" onchange="toggleClassSelect()">
                                    <label class="form-check-label text-sm fw-medium" for="assign_all_classes">All classes & courses</label>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div id="class-access-picker" class="class-access-picker">
                                <input type="text" class="form-control class-access-search mb-12" id="class_access_search" placeholder="Search classes, courses, or codes..." oninput="filterClassOptions()">
                                <div id="class-access-grid" class="class-access-grid">
                                    <?php foreach ($classes_for_access as $c): ?>
                                    <?php $access_type = strtolower($c['type'] ?? 'class') === 'course' ? 'Course' : 'Class'; ?>
                                    <label class="class-access-option" data-class-filter="<?php echo htmlspecialchars(strtolower(($c['class_name'] ?? '') . ' ' . ($c['class_code'] ?? '') . ' ' . $access_type)); ?>">
                                        <input class="form-check-input class-access-checkbox" type="checkbox" name="class_ids[]" value="<?php echo (int)$c['id']; ?>" onchange="syncClassOptionState(this)">
                                        <span>
                                            <span class="class-access-option-title"><?php echo htmlspecialchars($c['class_name']); ?></span>
                                            <span class="d-flex align-items-center gap-8 flex-wrap mt-6">
                                                <span class="class-access-option-code"><?php echo htmlspecialchars($c['class_code']); ?></span>
                                                <span class="class-access-option-type"><?php echo $access_type; ?></span>
                                            </span>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <div id="class-access-empty" class="class-access-empty">No classes or courses match your search.</div>
                            </div>
                            <div class="form-text">Selected access applies to both classes and courses because courses use the same access table.</div>
                        </div>
                        <?php endif; ?>

                        <div class="flex-align justify-content-end gap-8 mt-24">
                            <button type="button" class="btn btn-outline-gray rounded-pill py-9" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-main rounded-pill py-9" id="submitBtn">
                                <i class="ph ph-plus me-2"></i>
                                Create User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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

    <style>
        .class-access-picker {
            border: 1px solid #d9e1ec;
            border-radius: 8px;
            background: #f8fafc;
            padding: 16px;
        }

        .class-access-picker.is-disabled {
            opacity: 0.65;
            pointer-events: none;
        }

        .class-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 12px;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .class-access-option {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            border: 1px solid #d9e1ec;
            border-radius: 8px;
            background: #fff;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .class-access-option:hover,
        .class-access-option.is-selected {
            border-color: #4f7cff;
            box-shadow: 0 8px 18px rgba(79, 124, 255, 0.08);
        }

        .class-access-option.is-selected {
            background: #eef4ff;
        }

        .class-access-option.is-hidden {
            display: none;
        }

        .class-access-option input {
            margin-top: 3px;
            flex-shrink: 0;
        }

        .class-access-option-title {
            display: block;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.35;
        }

        .class-access-option-code,
        .class-access-option-type {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .class-access-option-code {
            background: #eff6ff;
            color: #2563eb;
        }

        .class-access-option-type {
            background: #ecfdf5;
            color: #059669;
        }

        .class-access-empty {
            display: none;
            border: 1px dashed #d9e1ec;
            border-radius: 8px;
            padding: 18px;
            text-align: center;
            color: #6b7280;
            background: #fff;
        }
    </style>

    <script>
        const schools = <?php echo json_encode($schools); ?>;
        const canResetPassword = <?php echo can('users.reset_password') ? 'true' : 'false'; ?>;
        const currentUserRole = '<?php echo htmlspecialchars($current_user_role_key, ENT_QUOTES); ?>';
        const canManageUserAccess = <?php echo $can_manage_user_access ? 'true' : 'false'; ?>;
        let initialUserPermissions = {};
        
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add User';
            document.getElementById('formAction').value = 'create';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-plus me-2"></i>Create User';
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('password').required = true;
            document.getElementById('password').disabled = false;
            document.getElementById('passwordRequiredMark').style.display = 'inline';
            document.getElementById('passwordHelp').style.display = 'none';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('organization_id').disabled = false;
            document.getElementById('school_id').disabled = false;
            resetAccessPicker();
            loadSchools();
            new bootstrap.Modal(document.getElementById('userModal')).show();
        }
        
        function editUser(userData) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-check me-2"></i>Update User';
            document.getElementById('passwordField').style.display = canResetPassword ? 'block' : 'none';
            document.getElementById('password').required = false;
            document.getElementById('password').disabled = !canResetPassword;
            document.getElementById('password').value = '';
            document.getElementById('passwordRequiredMark').style.display = 'none';
            document.getElementById('passwordHelp').style.display = 'block';
            
            document.getElementById('userId').value = userData.id;
            document.getElementById('full_name').value = userData.full_name || '';
            document.getElementById('username').value = userData.username;
            document.getElementById('email').value = userData.email;
            document.getElementById('role_id').value = userData.role_id;
            document.getElementById('organization_id').value = userData.organization_id || '';
            document.getElementById('status').value = userData.status;
            document.getElementById('phone').value = userData.phone || '';
            document.getElementById('gender').value = userData.gender || '';
            document.getElementById('address').value = userData.address || '';
            resetAccessPicker();
            loadUserAccess(userData);
            
            loadSchools();
            setTimeout(() => {
                document.getElementById('school_id').value = userData.school_id || '';
                handleRoleChange(false);
            }, 100);
            
            new bootstrap.Modal(document.getElementById('userModal')).show();
        }
        
        function deleteUser(userId, username) {
            if (confirm(`Are you sure you want to delete user "${username}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function loadSchools() {
            const organizationId = document.getElementById('organization_id').value;
            const schoolSelect = document.getElementById('school_id');
            
            schoolSelect.innerHTML = '<option value="">Select School</option>';
            
            if (organizationId) {
                schools.filter(school => school.organization_id == organizationId)
                      .forEach(school => {
                          const option = document.createElement('option');
                          option.value = school.id;
                          option.textContent = school.name;
                          schoolSelect.appendChild(option);
                      });
            }
        }
        
        function handleRoleChange(clearScope = true) {
            const roleSelect = document.getElementById('role_id');
            const selectedRole = roleSelect.options[roleSelect.selectedIndex].text;
            const orgField = document.getElementById('organization_id');
            const schoolField = document.getElementById('school_id');
            
            if (clearScope) {
                orgField.value = '';
                schoolField.value = '';
            }
            
            if (selectedRole === 'Solo Student') {
                orgField.disabled = true;
                schoolField.disabled = true;
            } else {
                orgField.disabled = false;
                schoolField.disabled = false;
            }
        }

        function getClassCheckboxes() {
            return Array.from(document.querySelectorAll('.class-access-checkbox'));
        }

        function syncClassOptionState(checkbox) {
            const optionCard = checkbox.closest('.class-access-option');
            if (optionCard) {
                optionCard.classList.toggle('is-selected', checkbox.checked);
            }
        }

        function clearClassSelections() {
            getClassCheckboxes().forEach(checkbox => {
                checkbox.checked = false;
                syncClassOptionState(checkbox);
            });
        }

        function setClassSelectionDisabled(disabled) {
            const classPicker = document.getElementById('class-access-picker');
            const searchInput = document.getElementById('class_access_search');

            getClassCheckboxes().forEach(checkbox => {
                checkbox.disabled = disabled;
            });

            if (classPicker) {
                classPicker.classList.toggle('is-disabled', disabled);
            }

            if (searchInput) {
                searchInput.disabled = disabled;
            }
        }

        function filterClassOptions() {
            const searchInput = document.getElementById('class_access_search');
            if (!searchInput) return;

            const query = (searchInput.value || '').trim().toLowerCase();
            const optionCards = Array.from(document.querySelectorAll('.class-access-option'));
            let visibleCount = 0;

            optionCards.forEach(card => {
                const haystack = card.getAttribute('data-class-filter') || '';
                const isMatch = query === '' || haystack.includes(query);
                card.classList.toggle('is-hidden', !isMatch);
                if (isMatch) visibleCount += 1;
            });

            const emptyState = document.getElementById('class-access-empty');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        function resetClassSearch() {
            const searchInput = document.getElementById('class_access_search');
            if (searchInput) {
                searchInput.value = '';
            }
            filterClassOptions();
        }

        function preselectClasses(userId) {
            const userPermissions = (initialUserPermissions[userId] || []).map(id => parseInt(id));

            clearClassSelections();
            getClassCheckboxes().forEach(checkbox => {
                if (userPermissions.includes(parseInt(checkbox.value))) {
                    checkbox.checked = true;
                    syncClassOptionState(checkbox);
                }
            });
        }

        function toggleClassSelect() {
            const assignAllClassesCheckbox = document.getElementById('assign_all_classes');
            const allowAll = assignAllClassesCheckbox && assignAllClassesCheckbox.checked && currentUserRole === 'super_admin';

            if (allowAll) {
                clearClassSelections();
            }

            setClassSelectionDisabled(allowAll);
        }

        function resetAccessPicker() {
            if (!canManageUserAccess) return;

            const assignAllClassesCheckbox = document.getElementById('assign_all_classes');
            if (assignAllClassesCheckbox) {
                assignAllClassesCheckbox.checked = false;
            }

            initialUserPermissions = {};
            setClassSelectionDisabled(false);
            clearClassSelections();
            resetClassSearch();
        }

        function loadUserAccess(userData) {
            if (!canManageUserAccess || !userData || !userData.id) return;

            const assignAllClassesCheckbox = document.getElementById('assign_all_classes');
            const canAccessAll = parseInt(userData.can_access_all_classes || 0) === 1;

            if (assignAllClassesCheckbox) {
                assignAllClassesCheckbox.checked = canAccessAll;
            }

            if (canAccessAll) {
                setClassSelectionDisabled(true);
                return;
            }

            fetch(`users.php?ajax=get_user_class_ids&user_id=${encodeURIComponent(userData.id)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Access lookup failed');
                    }
                    return response.json();
                })
                .then(classIds => {
                    initialUserPermissions[userData.id] = classIds;
                    preselectClasses(userData.id);
                })
                .catch(error => console.error('Error fetching user class and course access:', error));
        }

        // ========================== Export Js Start ==============================
        document.getElementById('exportOptions').addEventListener('change', function() {
            const format = this.value;
            const table = document.getElementById('userTable');
            let data = [];
            const headers = [];
            
            // Get the table headers
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText.trim());
            });

            // Get the table rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = {};
                tr.querySelectorAll('td').forEach((td, index) => {
                    row[headers[index]] = td.innerText.trim();
                });
                data.push(row);
            });

            if (format === 'csv') {
                downloadCSV(data);
            } else if (format === 'json') {
                downloadJSON(data);
            }
        });

        function downloadCSV(data) {
            const csv = data.map(row => Object.values(row).join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'users.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function downloadJSON(data) {
            const json = JSON.stringify(data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'users.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        // ========================== Export Js End ==============================
    
        // Table Header Checkbox checked all js Start
        $('#selectAll').on('change', function () {
            $('.form-check .form-check-input').prop('checked', $(this).prop('checked')); 
        }); 
    
        // Data Tables
        new DataTable('#userTable', {
            searching: false,
            lengthChange: false,
            info: false,
            paging: false,
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] } // Disables sorting on the 1st & 8th column (index 0, 7)
            ]
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>

</body>
</html>
