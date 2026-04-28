<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'config/database.php';
require_once 'classes/ClassAccess.php';
require_once 'classes/Role.php';

// Handle AJAX requests for user class permissions
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_user_class_ids' && isset($_GET['user_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $classAccess = new ClassAccess($db);
    $user_id = (int)$_GET['user_id'];
    
    header('Content-Type: application/json');
    echo json_encode($classAccess->getUserClassIds($user_id));
    exit();
}

// Define a constant for 'All Classes' option value
define('ALL_CLASSES_OPTION_VALUE', -1);

// Check if user is logged in and has permission to manage class access
$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

// Super Admin and Organization Admin can manage class access
// School Admin can manage teacher access
if (!in_array($user_role, ['super_admin', 'organization_admin', 'school_admin'])) {
    redirect('dashboard.php');
}

$database = new Database();
$db = $database->getConnection();
$classModel = new ClassModel($db);
$classAccess = new ClassAccess($db);
$user = new User($db);
$role = new Role($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'assign_access':
                $user_id = (int)$_POST['user_id'];
                $assign_all_classes = isset($_POST['assign_all_classes']) && $_POST['assign_all_classes'] === 'on';
                $class_ids = isset($_POST['class_ids']) ? (array)$_POST['class_ids'] : [];

                // Check if current user can manage the target user
                if (!$user->canManageUser($current_user, $user_id)) {
                    $error = 'You do not have permission to manage this user.';
                    break;
                }

                // Non-super admins cannot change their own access
                if ($user_role !== 'super_admin' && (int)$user_id === (int)($current_user['id'] ?? 0)) {
                    $error = 'You cannot change your own access.';
                    break;
                }

                // Get target user's role for specific logic
                $target_user_data = $user->getById($user_id);
                $target_user_role_name = $target_user_data['role_name'] ?? '';
                // Organization Admin can only manage users they created
                if ($user_role === 'organization_admin' && (int)($target_user_data['created_by'] ?? 0) !== (int)($current_user['id'] ?? 0)) {
                    $error = 'You can only manage users you created.';
                    break;
                }

                // Super Admin granting access
                if ($user_role === 'super_admin') {
                    if ($assign_all_classes) {
                        // Grant access to all classes by updating the user table
                        if ($user->update($user_id, ['can_access_all_classes' => 1])) {
                            // Clear individual permissions if 'all classes' is granted
                            $classAccess->revokeAllAccessForUser($user_id);
                            $message = 'Access to all classes granted successfully!';
                        } else {
                            $error = 'Failed to grant all class access.';
                        }
                    } else {
                        // Assign individual classes if not assigning all
                        $current_assigned_class_ids = $classAccess->getUserClassIds($user_id);

                        // Revoke classes that are no longer selected
                        foreach ($current_assigned_class_ids as $c_id) {
                            if (!in_array($c_id, $class_ids)) {
                                $classAccess->revokeAccess($user_id, $c_id);
                            }
                        }
                        // Assign newly selected classes
                        foreach ($class_ids as $class_id) {
                            $classAccess->assignAccess($user_id, (int)$class_id, $current_user['id']);
                        }
                        // Ensure 'can_access_all_classes' is off if individual classes are managed
                        $user->update($user_id, ['can_access_all_classes' => 0]);
                        $message = 'Class access updated successfully!';
                    }
                } 
                // Organization Admin / School Admin granting access to subordinates
                elseif (in_array($user_role, ['organization_admin', 'school_admin'])) {
                    // Organization Admin can only grant access to School Admins, Teachers, Solo Students
                    // School Admin can only grant access to Teachers, Solo Students
                    
                    // Check if the target user's role is valid for the current admin's role
                    if (($user_role === 'organization_admin' && !in_array($target_user_role_name, ['School Admin', 'Teacher', 'Solo Student'])) ||
                        ($user_role === 'school_admin' && !in_array($target_user_role_name, ['Teacher']))) {
                        $error = 'You can only assign classes to subordinates.';
                        break;
                    }

                    // School Admin can only manage users they created
                    if ($user_role === 'school_admin') {
                        if ((int)($target_user_data['created_by'] ?? 0) !== (int)($current_user['id'] ?? 0)) {
                            $error = 'You can only manage users you created.';
                            break;
                        }
                    }

                    // Admins cannot grant 'all classes' access via this interface
                    if ($assign_all_classes) {
                        $error = 'You cannot grant access to all classes.';
                        break;
                    }

                    // Get classes current admin has access to
                    $admin_accessible_class_ids = array_map(function($c) { return $c['id']; }, $user->getAccessibleClasses($current_user));
                    
                    // Filter requested classes to ensure admin has permission to grant them
                    $grantable_class_ids = array_filter($class_ids, function($c_id) use ($admin_accessible_class_ids) {
                        return in_array($c_id, $admin_accessible_class_ids);
                    });

                    if (count($grantable_class_ids) !== count($class_ids)) {
                        $error = 'You can only assign classes you have access to.';
                        break;
                    }

                    $current_assigned_class_ids = $classAccess->getUserClassIds($user_id);
                    
                    // Revoke classes that are no longer selected or are outside admin's scope
                    foreach ($current_assigned_class_ids as $c_id) {
                        if (!in_array($c_id, $grantable_class_ids)) {
                            $classAccess->revokeAccess($user_id, $c_id);
                        }
                    }

                    // Assign newly selected classes
                    foreach ($grantable_class_ids as $class_id) {
                        $classAccess->assignAccess($user_id, (int)$class_id, $current_user['id']);
                    }

                    // Ensure 'can_access_all_classes' is off if individual classes are managed
                    $user->update($user_id, ['can_access_all_classes' => 0]);
                    $message = 'Class access updated successfully!';
                }

                break;
                
            case 'revoke_access':
                $user_id = (int)$_POST['user_id'];
                $class_id = (int)$_POST['class_id'];

                // Check if current user can manage the target user
                if (!$user->canManageUser($current_user, $user_id)) {
                    $error = 'You do not have permission to manage this user.';
                    break;
                }

                // Non-super admins cannot revoke their own access
                if ($user_role !== 'super_admin' && (int)$user_id === (int)($current_user['id'] ?? 0)) {
                    $error = 'You cannot revoke your own access.';
                    break;
                }
                if ($user_role === 'organization_admin') {
                    $target_user_data = $user->getById($user_id);
                    if ((int)($target_user_data['created_by'] ?? 0) !== (int)($current_user['id'] ?? 0)) {
                        $error = 'You can only manage users you created.';
                        break;
                    }
                }

                // For non-Super Admins, we only revoke specific permissions
                if (in_array($user_role, ['organization_admin', 'school_admin'])) {
                    // Check if the admin has access to the class they are revoking
                    $admin_accessible_class_ids = array_map(function($c) { return $c['id']; }, $user->getAccessibleClasses($current_user));
                    if (!in_array($class_id, $admin_accessible_class_ids)) {
                        $error = 'You can only revoke access for classes you manage.';
                        break;
                    }
                }

                if ($classAccess->revokeAccess($user_id, $class_id)) {
                    $message = 'Class access revoked successfully!';
                } else {
                    $error = 'Failed to revoke class access.';
                }
                break;

            case 'revoke_all_access':
                $user_id = (int)$_POST['user_id'];

                // Only Super Admin can revoke 'all classes' access
                if ($user_role !== 'super_admin') {
                    $error = 'You do not have permission to revoke all class access.';
                    break;
                }

                // Check if current user can manage the target user
                if (!$user->canManageUser($current_user, $user_id)) {
                    $error = 'You do not have permission to manage this user.';
                    break;
                }

                // Revoke all classes by updating the user table
                if ($user->update($user_id, ['can_access_all_classes' => 0])) {
                    // Also clear individual permissions to keep data clean
                    $classAccess->revokeAllAccessForUser($user_id);
                    $message = 'All class access revoked successfully!';
                } else {
                    $error = 'Failed to revoke all class access.';
                }
                break;
        }
    }
}

// Get users based on current user's permissions for the dropdown
$users_for_dropdown = [];
if ($user_role === 'super_admin') {
    $users_for_dropdown = $user->getUsersByPermission($current_user);
} elseif ($user_role === 'organization_admin') {
    $all_possible_users = $user->getUsersByPermission($current_user);
    foreach ($all_possible_users as $u_data) {
        $u_role = $role->getById($u_data['role_id']);
        $is_allowed_role = in_array($u_role['name'], ['School Admin', 'Teacher', 'Solo Student']);
        $created_by_me = (int)($u_data['created_by'] ?? 0) === (int)($current_user['id'] ?? 0);
        if ($is_allowed_role && $created_by_me) {
            $users_for_dropdown[] = $u_data;
        }
    }
} elseif ($user_role === 'school_admin') {
    $all_possible_users = $user->getUsersByPermission($current_user);
    foreach ($all_possible_users as $u_data) {
        $u_role = $role->getById($u_data['role_id']);
        if (in_array($u_role['name'], ['Teacher', 'Solo Student'])) {
            $users_for_dropdown[] = $u_data;
        }
    }
}

// Get classes based on current user's permissions for the dropdown
$classes_for_dropdown = $user->getAccessibleClasses($current_user);

// Get current class permissions for display
$all_permissions_for_display = [];
$users_with_all_access = [];

// First, get users who have 'can_access_all_classes' set
$query_all_access = "SELECT u.id, u.username, u.full_name, r.name as role_name, 
                       o.name as organization_name, s.name as school_name
                       FROM users u
                       JOIN roles r ON u.role_id = r.id
                       LEFT JOIN organizations o ON u.organization_id = o.id
                       LEFT JOIN schools s ON u.school_id = s.id
                       WHERE u.can_access_all_classes = 1 AND u.status = 'active'";

if ($user_role === 'organization_admin') {
    $query_all_access .= " AND u.organization_id = ?";
    $stmt_all_access = $db->prepare($query_all_access);
    $stmt_all_access->execute([$current_user['organization_id']]);
} elseif ($user_role === 'school_admin') {
    $query_all_access .= " AND u.school_id = ?";
    $stmt_all_access = $db->prepare($query_all_access);
    $stmt_all_access->execute([$current_user['school_id']]);
} else {
    $stmt_all_access = $db->prepare($query_all_access);
    $stmt_all_access->execute();
}

while ($row = $stmt_all_access->fetch(PDO::FETCH_ASSOC)) {
    $users_with_all_access[$row['id']] = $row;
}

// Then get individual class permissions, filtered by role
$permissions_query = "SELECT ucp.*, u.username, u.full_name, c.class_name, c.class_code, 
                     granter.username as granted_by_username, r.name as role_name
                     FROM user_class_permissions ucp
                     JOIN users u ON ucp.user_id = u.id
                     JOIN classes c ON ucp.class_id = c.id
                     JOIN roles r ON u.role_id = r.id
                     LEFT JOIN users granter ON ucp.granted_by = granter.id
                     WHERE u.can_access_all_classes = 0";

$params = [];

if ($user_role === 'organization_admin') {
    $permissions_query .= " AND u.organization_id = ?";
    $params[] = $current_user['organization_id'];
} elseif ($user_role === 'school_admin') {
    $permissions_query .= " AND u.school_id = ?";
    $params[] = $current_user['school_id'];
}

$permissions_query .= " ORDER BY u.username, c.class_name";
$stmt_individual_permissions = $db->prepare($permissions_query);
$stmt_individual_permissions->execute($params);

while ($row = $stmt_individual_permissions->fetch(PDO::FETCH_ASSOC)) {
    $all_permissions_for_display[] = $row;
}

// Combine users with all access and individual permissions for display
foreach ($users_with_all_access as $uid => $u_data) {
    $all_permissions_for_display[] = [
        'user_id' => $uid,
        'username' => $u_data['username'],
        'full_name' => $u_data['full_name'],
        'class_name' => 'All Classes',
        'class_code' => 'ALL',
        'granted_by_username' => 'System',
        'created_at' => '',
        'role_name' => $u_data['role_name'],
        'class_id' => ALL_CLASSES_OPTION_VALUE
    ];
}

$flash = get_flash_message();

// Pagination setup
$per_page = isset($_GET['pp']) ? max(5, min(100, (int)$_GET['pp'])) : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$total = count($all_permissions_for_display);
$pages = max(1, (int)ceil($total / $per_page));
if ($page > $pages) { $page = $pages; }
$offset = ($page - 1) * $per_page;
$paged_permissions = array_slice($all_permissions_for_display, $offset, $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>Class Access Management - <?php echo APP_NAME; ?></title>
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
                        <li><span class="text-main-600 fw-normal text-15">Class Access</span></li>
                    </ul>
                </div>
                <!-- Breadcrumb End -->

                <!-- Breadcrumb Right Start -->
                <div class="flex-align gap-8 flex-wrap">
                    <button class="btn btn-main text-sm btn-sm px-24 rounded-pill py-12 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="ph ph-plus me-4"></i>
                        Grant Access 
                    </button>
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
                    <table id="accessTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="h6 text-gray-300">User</th>
                                <th class="h6 text-gray-300">Role</th>
                                <th class="h6 text-gray-300">Class</th>
                                <th class="h6 text-gray-300">Class Code</th>
                                <th class="h6 text-gray-300">Granted By</th>
                                <th class="h6 text-gray-300">Date Granted</th>
                                <th class="h6 text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($all_permissions_for_display)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-3">
                                        <i class="ph ph-users-three text-gray-400" style="font-size: 48px;"></i>
                                        <h6 class="text-gray-500 mt-3 mb-1">No permissions found</h6>
                                        <p class="text-gray-400 text-sm mb-0">Start by granting class access to users</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($paged_permissions as $perm): ?>
                            <tr>
                                <td class="fixed-width">
                                    <div class="form-check">
                                        <input class="form-check-input border-gray-200 rounded-4" type="checkbox">
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-align gap-8">
                                        <img src="<?php echo htmlspecialchars(user_avatar_url($perm)); ?>" alt="" class="w-40 h-40 rounded-circle">
                                        <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($perm['full_name'] ?? $perm['username']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-13 py-2 px-8 bg-info-50 text-info-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                        <span class="w-6 h-6 bg-info-600 rounded-circle flex-shrink-0"></span>
                                        <?php echo htmlspecialchars($perm['role_name'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($perm['class_name'] === 'All Classes'): ?>
                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">
                                            <i class="ph ph-check-circle"></i>
                                            All Classes
                                        </span>
                                    <?php else: ?>
                                        <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($perm['class_name']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($perm['class_code'] === 'ALL'): ?>
                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 d-inline-flex align-items-center gap-8 rounded-pill">ALL</span>
                                    <?php else: ?>
                                        <span class="text-13 py-2 px-8 bg-primary-50 text-primary-600 d-inline-flex align-items-center gap-8 rounded-pill"><?php echo htmlspecialchars($perm['class_code']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="h6 mb-0 fw-medium text-gray-300"><?php echo htmlspecialchars($perm['granted_by_username'] ?? 'System'); ?></span>
                                </td>
                                <td>
                                    <span class="text-gray-600">
                                        <?php echo !empty($perm['created_at']) ? date('M d, Y', strtotime($perm['created_at'])) : 'System'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="bg-danger-50 text-danger-600 py-2 px-14 rounded-pill hover-bg-danger-600 hover-text-white text-sm" 
                                            onclick="revokeAccess(<?php echo $perm['user_id']; ?>, <?php echo $perm['class_id']; ?>, '<?php echo htmlspecialchars($perm['username']); ?>', '<?php echo htmlspecialchars($perm['class_name']); ?>')">
                                        <i class="ph ph-x me-4"></i>Revoke
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer flex-between flex-wrap">
                    <?php 
                        $start_i = $total ? ($offset + 1) : 0; 
                        $end_i = min($offset + $per_page, $total);
                        $qs = $_GET; unset($qs['page']); unset($qs['pp']);
                        $base = basename($_SERVER['PHP_SELF']) . (empty($qs) ? '' : ('?' . http_build_query($qs)));
                        function page_link($base,$p,$pp){ return $base . (strpos($base,'?')!==false? '&':'?') . 'page=' . $p . '&pp=' . $pp; }
                    ?>
                    <div class="flex-between flex-wrap gap-12 w-100">
                        <span class="text-gray-900">Showing <?php echo $start_i; ?> to <?php echo $end_i; ?> of <?php echo $total; ?> entries</span>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item <?php echo $page<=1?'disabled':''; ?>">
                                    <a class="page-link" href="<?php echo $page>1? page_link($base,$page-1,$per_page):'#'; ?>">Prev</a>
                                </li>
                                <?php for ($p=1; $p<=$pages; $p++): ?>
                                    <li class="page-item <?php echo $p==$page?'active':''; ?>">
                                        <a class="page-link" href="<?php echo page_link($base,$p,$per_page); ?>"><?php echo $p; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page>=$pages?'disabled':''; ?>">
                                    <a class="page-link" href="<?php echo $page<$pages? page_link($base,$page+1,$per_page):'#'; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <ul class="pagination flex-align flex-wrap">
                        <li class="page-item active">
                            <a class="page-link h-44 w-44 flex-center text-15 rounded-8 fw-medium" href="#">1</a>
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

    <!-- Assign Access Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Grant Class Access</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="assign_access">
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User</label>
                            <select class="form-select" name="user_id" id="user_id" required onchange="updateClassAssignmentUI()">
                                <option value="">Choose a user</option>
                                <?php foreach ($users_for_dropdown as $u): ?>
                                <option 
                                    value="<?php echo $u['id']; ?>"
                                    data-role-name="<?php echo htmlspecialchars($u['role_name']); ?>"
                                    data-can-access-all-classes="<?php echo $u['can_access_all_classes']; ?>">
                                    <?php echo htmlspecialchars($u['full_name'] ?? $u['username']); ?> 
                                    (@<?php echo htmlspecialchars($u['username']); ?>) - 
                                    <?php echo htmlspecialchars($u['role_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php if ($user_role === 'super_admin'): ?>
                        <div class="mb-3" id="assign-all-classes-container" style="display: none;">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="assign_all_classes" name="assign_all_classes" onchange="toggleClassSelect()">
                                <label class="form-check-label" for="assign_all_classes">
                                    <strong>Grant Access to ALL Classes</strong>
                                    <small class="d-block text-muted">User will have access to current and future classes</small>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="class_ids" class="form-label">Select Classes</label>
                            <select class="form-select" name="class_ids[]" id="class_ids" multiple required size="6">
                                <?php foreach ($classes_for_dropdown as $c): ?>
                                <option value="<?php echo $c['id']; ?>">
                                    <?php echo htmlspecialchars($c['class_name']); ?> 
                                    (<?php echo htmlspecialchars($c['class_code']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) to select multiple classes</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-main">Save Access</button>
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

    <script>
        const currentUserRole = '<?php echo $user_role; ?>';
        const allClasses = <?php echo json_encode($classes_for_dropdown); ?>;
        
        // Store initial permissions for the user being edited (if any) to pre-select checkboxes
        let initialUserPermissions = {};

        function updateClassAssignmentUI() {
            const userIdSelect = document.getElementById('user_id');
            const selectedUserOption = userIdSelect.options[userIdSelect.selectedIndex];
            const targetUserRoleName = selectedUserOption ? selectedUserOption.dataset.roleName : '';
            const targetUserCanAccessAllClasses = selectedUserOption ? selectedUserOption.dataset.canAccessAllClasses === '1' : false;
            
            const assignAllClassesContainer = document.getElementById('assign-all-classes-container');
            const assignAllClassesCheckbox = document.getElementById('assign_all_classes');
            const classSelect = document.getElementById('class_ids');

            // Reset UI elements
            assignAllClassesContainer.style.display = 'none';
            assignAllClassesCheckbox.checked = false;
            classSelect.disabled = false;
            clearSelect(classSelect);

            // Clear existing options before populating
            while (classSelect.options.length > 0) {
                classSelect.remove(0);
            }

            // Populate class select with all available classes from PHP
            allClasses.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = `${c.class_name} (${c.class_code})`;
                classSelect.appendChild(option);
            });

            // Show 'Assign All Classes' checkbox only for Super Admin
            if (currentUserRole === 'super_admin') {
                assignAllClassesContainer.style.display = 'block';
                // Pre-fill checkbox if target user already has all access
                assignAllClassesCheckbox.checked = targetUserCanAccessAllClasses;
            }

            // If target user has 'all classes' access, disable individual class selection
            if (targetUserCanAccessAllClasses) {
                classSelect.disabled = true;
            }

            toggleClassSelect(); // Adjust class select state based on checkbox

            // Load current individual permissions for the selected user
            if (selectedUserOption) {
                const selectedUserId = selectedUserOption.value;
                // Only fetch individual permissions if not 'can_access_all_classes'
                if (!targetUserCanAccessAllClasses) {
                    fetch(`class-access.php?ajax=get_user_class_ids&user_id=${selectedUserId}`)
                        .then(response => response.json())
                        .then(classIds => {
                            initialUserPermissions[selectedUserId] = classIds;
                            preselectClasses(selectedUserId);
                        })
                        .catch(error => console.error('Error fetching user class permissions:', error));
                } else {
                    // If user has 'all classes' access, ensure no individual classes are selected
                    initialUserPermissions[selectedUserId] = [];
                    preselectClasses(selectedUserId);
                }
            }
        }

        function toggleClassSelect() {
            const assignAllClassesCheckbox = document.getElementById('assign_all_classes');
            const classSelect = document.getElementById('class_ids');

            if (assignAllClassesCheckbox.checked && currentUserRole === 'super_admin') {
                classSelect.disabled = true;
                clearSelect(classSelect);
            } else {
                classSelect.disabled = false;
                // Restore initial selections if available and not assigning all
                const selectedUserOption = document.getElementById('user_id').options[document.getElementById('user_id').selectedIndex];
                if (selectedUserOption) {
                    preselectClasses(selectedUserOption.value);
                }
            }
        }

        function clearSelect(selectElement) {
            Array.from(selectElement.options).forEach(option => {
                option.selected = false;
            });
        }

        function preselectClasses(userId) {
            const classSelect = document.getElementById('class_ids');
            const userPermissions = initialUserPermissions[userId] || [];

            clearSelect(classSelect);
            Array.from(classSelect.options).forEach(option => {
                if (userPermissions.includes(parseInt(option.value))) {
                    option.selected = true;
                }
            });
        }

        window.revokeAccess = function(userId, classId, username, className) {
            if (confirm(`Are you sure you want to revoke access for "${username}" to class "${className}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                let actionValue = 'revoke_access';
                let classIdInput = `<input type="hidden" name="class_id" value="${classId}">`;

                if (classId === <?php echo ALL_CLASSES_OPTION_VALUE; ?>) {
                    actionValue = 'revoke_all_access';
                    classIdInput = ''; 
                }

                form.innerHTML = `
                    <input type="hidden" name="action" value="${actionValue}">
                    <input type="hidden" name="user_id" value="${userId}">
                    ${classIdInput}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        };

        // ========================== Export Js Start ==============================
        document.getElementById('exportOptions').addEventListener('change', function() {
            const format = this.value;
            const table = document.getElementById('accessTable');
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
            a.download = 'class-access.csv';
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
            a.download = 'class-access.json';
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
        new DataTable('#accessTable', {
            searching: false,
            lengthChange: false,
            info: false,   // Bottom Left Text => Showing 1 to 10 of 12 entries
            paging: false, // Pagination False
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] } // Disables sorting on the first and last column
            ]
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateClassAssignmentUI(); // Initialize UI state
        });
    </script>
    
</body>
</html>
