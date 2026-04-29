<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/Organization.php';
require_once 'classes/User.php';
require_once 'classes/ClassModel.php';
require_once 'classes/Role.php';

// Check if user is logged in and has permission to manage organizations
require_roles(['super_admin']);

$database = new Database();
$db = $database->getConnection();
$current_user = current_user();
$organization = new Organization($db);
$user_obj = new User($db);
$class_obj = new ClassModel($db);
$role_obj = new Role($db);

// Get classes for permission assignment
$classes = $class_obj->readAll([], true);
// Find Organization Admin role ID
$roles = $role_obj->getAll();
$org_admin_role_id = 0;
foreach ($roles as $r) {
    if ($r['name'] === 'Organization Admin') {
        $org_admin_role_id = $r['id'];
        break;
    }
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'address' => trim($_POST['address']),
                    'phone' => trim($_POST['phone']),
                    'email' => trim($_POST['email']),
                    'status' => $_POST['status']
                ];
                
                $new_org_id = $organization->create($data);
                if ($new_org_id) {
                    // Create Admin User if requested
                    if (!empty($_POST['create_admin']) && $_POST['create_admin'] == '1') {
                        $admin_data = [
                            'username' => trim($_POST['admin_username']),
                            'email' => trim($_POST['admin_email']),
                            'full_name' => trim($_POST['admin_full_name']),
                            'password' => $_POST['admin_password'],
                            'role_id' => $org_admin_role_id,
                            'organization_id' => $new_org_id,
                            'status' => 'active'
                        ];
                        
                        $new_user_id = $user_obj->create($admin_data, $current_user['id']);
                        if ($new_user_id && !empty($_POST['class_ids'])) {
                            foreach ($_POST['class_ids'] as $class_id) {
                                $user_obj->assignClassPermission($new_user_id, $class_id, $current_user['id']);
                            }
                        }
                    }
                    flash_message('Organization created successfully!', 'success');
                } else {
                    flash_message('Failed to create organization. Please try again.', 'error');
                }
                redirect('organizations.php');
                break;
                
            case 'update':
                $org_id = $_POST['org_id'];
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'address' => trim($_POST['address']),
                    'phone' => trim($_POST['phone']),
                    'email' => trim($_POST['email']),
                    'status' => $_POST['status']
                ];
                
                if ($organization->update($org_id, $data)) {
                    // Handle Admin User
                    $admin_user_id = !empty($_POST['admin_user_id']) ? $_POST['admin_user_id'] : null;
                    
                    if ($admin_user_id) {
                        // Update existing admin
                        $admin_data = [
                            'username' => trim($_POST['admin_username']),
                            'email' => trim($_POST['admin_email']),
                            'full_name' => trim($_POST['admin_full_name']),
                            'role_id' => $org_admin_role_id,
                            'organization_id' => $org_id,
                            'status' => 'active'
                        ];
                        
                        $user_obj->update($admin_user_id, $admin_data);
                        
                        // Update password if provided
                        if (!empty($_POST['admin_password'])) {
                            $user_obj->updatePassword($admin_user_id, $_POST['admin_password']);
                        }
                        
                        // Update permissions
                        $user_obj->clearClassPermissions($admin_user_id);
                        if (!empty($_POST['class_ids'])) {
                            foreach ($_POST['class_ids'] as $class_id) {
                                $user_obj->assignClassPermission($admin_user_id, $class_id, $current_user['id']);
                            }
                        }
                    } elseif (!empty($_POST['create_admin']) && $_POST['create_admin'] == '1') {
                        // Create new admin for existing organization
                        $admin_data = [
                            'username' => trim($_POST['admin_username']),
                            'email' => trim($_POST['admin_email']),
                            'full_name' => trim($_POST['admin_full_name']),
                            'password' => $_POST['admin_password'],
                            'role_id' => $org_admin_role_id,
                            'organization_id' => $org_id,
                            'status' => 'active'
                        ];
                        
                        $new_user_id = $user_obj->create($admin_data, $current_user['id']);
                        if ($new_user_id && !empty($_POST['class_ids'])) {
                            foreach ($_POST['class_ids'] as $class_id) {
                                $user_obj->assignClassPermission($new_user_id, $class_id, $current_user['id']);
                            }
                        }
                    }
                    flash_message('Organization updated successfully!', 'success');
                } else {
                    flash_message('Failed to update organization. Please try again.', 'error');
                }
                redirect('organizations.php');
                break;
                
            case 'delete':
                $org_id = $_POST['org_id'];
                if ($organization->delete($org_id)) {
                    flash_message('Organization deleted successfully!', 'success');
                } else {
                    flash_message('Failed to delete organization. Please try again.', 'error');
                }
                redirect('organizations.php');
                break;
        }
    }
}

// Get all organizations
$organizations = $organization->getAll();
$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>Organizations - <?php echo APP_NAME; ?></title>
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
    
    <!-- Custom DataTable fixes -->
    <style>
        /* DataTable pagination fixes */
        .dataTables_wrapper .dataTables_paginate {
            float: none !important;
            text-align: center;
            margin-top: 1rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .pagination {
            margin: 0;
            display: inline-flex;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block !important;
            padding: 8px 12px;
            margin: 0 2px;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            color: #6c757d !important;
            background: white;
            cursor: pointer;
            min-width: 40px;
            text-align: center;
            line-height: 1;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            border-color: #adb5bd;
            color: #495057 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #6366f1 !important;
            color: white !important;
            border-color: #6366f1;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #6c757d !important;
            cursor: not-allowed;
            opacity: 0.5;
            background: #f8f9fa !important;
        }
        
        /* Fix search and length controls */
        .dataTables_wrapper .dataTables_filter {
            float: none;
            text-align: right;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 8px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 6px 12px;
        }
        
        .dataTables_wrapper .dataTables_length {
            float: none;
        }
        
        .dataTables_wrapper .dataTables_length select {
            margin: 0 8px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 4px 8px;
        }
        
        /* Info styling */
        .dataTables_wrapper .dataTables_info {
            padding-top: 8px;
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Remove DataTable default margins */
        .dataTables_wrapper {
            width: 100%;
        }
        
        .dataTables_wrapper .row {
            margin: 0 !important;
        }
        
        .dataTables_wrapper .col-sm-12,
        .dataTables_wrapper .col-md-6 {
            padding: 0 !important;
        }
    </style>
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
                    <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                    <li><span class="text-main-600 fw-normal text-15">Organizations</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show mb-24" role="alert">
                    <i class="ph ph-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'warning-circle'; ?> me-2"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Organizations Management Start -->
            <div class="card">
                <div class="card-header border-bottom border-gray-100 flex-between flex-wrap gap-8">
                    <h5 class="mb-0">Organization Management</h5>
                    <button class="btn btn-main text-sm btn-sm px-24 rounded-pill" data-bs-toggle="modal" data-bs-target="#orgModal" onclick="openCreateModal()">
                        <i class="ph ph-plus me-2"></i>
                        Add Organization
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="organizationsTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="h6 text-gray-300">#</th>
                                    <th class="h6 text-gray-300">Organization</th>
                                    <th class="h6 text-gray-300">Contact Info</th>
                                    <th class="h6 text-gray-300">Address</th>
                                    <th class="h6 text-gray-300">Schools</th>
                                    <th class="h6 text-gray-300">Users</th>
                                    <th class="h6 text-gray-300">Status</th>
                                    <th class="h6 text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($organizations as $index => $org): ?>
                                <tr>
                                    <td>
                                        <span class="h6 mb-0 fw-medium text-gray-300"><?php echo $index + 1; ?></span>
                                    </td>
                                    <td>
                                        <div class="flex-align gap-8">
                                            <span class="w-32 h-32 bg-main-50 text-main-600 rounded-circle flex-center">
                                                <i class="ph ph-buildings"></i>
                                            </span>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($org['name']); ?></h6>
                                                <?php if (!empty($org['description'])): ?>
                                                    <div class="text-13 text-gray-400"><?php echo htmlspecialchars(substr($org['description'], 0, 50)) . (strlen($org['description']) > 50 ? '...' : ''); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php if (!empty($org['email'])): ?>
                                                <div class="text-13 text-gray-600 mb-1">
                                                    <i class="ph ph-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($org['email']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($org['phone'])): ?>
                                                <div class="text-13 text-gray-600">
                                                    <i class="ph ph-phone me-1"></i>
                                                    <?php echo htmlspecialchars($org['phone']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!$org['email'] && !$org['phone']): ?>
                                                <span class="text-gray-400">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($org['address'])): ?>
                                            <span class="text-13 text-gray-600"><?php echo htmlspecialchars(substr($org['address'], 0, 40)) . (strlen($org['address']) > 40 ? '...' : ''); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-13 py-2 px-8 bg-main-50 text-main-600 border border-main-100 rounded-pill fw-medium">
                                            <?php echo $org['school_count']; ?> schools
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-13 py-2 px-8 bg-success-50 text-success-600 border border-success-100 rounded-pill fw-medium">
                                            <?php echo $org['user_count']; ?> users
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-13 py-2 px-8 <?php echo $org['status'] === 'active' ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600'; ?> rounded-pill fw-medium">
                                            <?php echo ucfirst($org['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex-align gap-8">
                                            <button type="button" class="w-32 h-32 bg-success-50 text-success-600 rounded-circle border border-success-100 flex-center text-sm hover-bg-success-600 hover-text-white hover-border-success-600" 
                                                    onclick="editOrg(<?php echo htmlspecialchars(json_encode($org)); ?>)" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>
                                            <button type="button" class="w-32 h-32 bg-danger-50 text-danger-600 rounded-circle border border-danger-100 flex-center text-sm hover-bg-danger-600 hover-text-white hover-border-danger-600" 
                                                    onclick="deleteOrg(<?php echo $org['id']; ?>, '<?php echo htmlspecialchars($org['name']); ?>')" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete">
                                                <i class="ph ph-trash-simple"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Organizations Management End -->
        </div>

        <!-- Footer Start -->
        <div class="dashboard-footer">
            <div class="flex-between flex-wrap gap-16">
                <p class="text-gray-300 text-13 fw-normal">&copy; Copyright <?php echo APP_NAME; ?> <?php echo date('Y'); ?>, All Right Reserved</p>
                <div class="flex-align flex-wrap gap-16">
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">License</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Support</a>
                    <a href="#" class="text-gray-300 text-13 fw-normal hover-text-main-600 hover-text-decoration-underline">Documentation</a>
                </div>
            </div>
        </div>
        <!-- Footer End -->
    </div>

    <!-- Organization Modal -->
    <div class="modal fade" id="orgModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-main-50 border-bottom border-gray-100">
                    <h5 class="modal-title" id="modalTitle">Add Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <form method="POST" id="orgForm">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="org_id" id="orgId">
                        
                        <div class="row g-20">
                            <div class="col-md-6">
                                <label for="name" class="form-label h6 mb-8">Organization Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-11" name="name" id="name" placeholder="Enter organization name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label h6 mb-8">Email Address</label>
                                <input type="email" class="form-control py-11" name="email" id="email" placeholder="Enter email address">
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-6">
                                <label for="phone" class="form-label h6 mb-8">Phone Number</label>
                                <input type="text" class="form-control py-11" name="phone" id="phone" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label h6 mb-8">Status <span class="text-danger">*</span></label>
                                <select class="form-select py-11" name="status" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-16">
                            <label for="description" class="form-label h6 mb-8">Description</label>
                            <textarea class="form-control py-11" name="description" id="description" rows="3" placeholder="Enter organization description"></textarea>
                        </div>
                        
                        <div class="mt-16">
                            <label for="address" class="form-label h6 mb-8">Address</label>
                            <textarea class="form-control py-11" name="address" id="address" rows="2" placeholder="Enter organization address"></textarea>
                        </div>

                        <!-- Organization Admin Section -->
                        <div class="mt-24 pt-24 border-top border-gray-100">
                            <div class="flex-between mb-16">
                                <h6 class="mb-0">Organization Admin</h6>
                                <div class="form-check form-switch" id="createAdminSwitchContainer">
                                    <input class="form-check-input" type="checkbox" name="create_admin" id="createAdmin" value="1">
                                    <label class="form-check-label" for="createAdmin">Manage Admin User</label>
                                </div>
                            </div>
                            
                            <input type="hidden" name="admin_user_id" id="adminUserId">
                            
                            <div id="adminFields" style="display: none;">
                                <div class="row g-20">
                                    <div class="col-md-6">
                                        <label for="admin_full_name" class="form-label h6 mb-8">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control py-11" name="admin_full_name" id="adminFullName" placeholder="Enter full name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="admin_username" class="form-label h6 mb-8">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control py-11" name="admin_username" id="adminUsername" placeholder="Enter username">
                                    </div>
                                </div>
                                
                                <div class="row g-20 mt-16">
                                    <div class="col-md-6">
                                        <label for="admin_email" class="form-label h6 mb-8">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control py-11" name="admin_email" id="adminEmail" placeholder="Enter email address">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="admin_password" class="form-label h6 mb-8">Password <span class="admin-pass-label"></span></label>
                                        <input type="password" class="form-control py-11" name="admin_password" id="adminPassword" placeholder="Enter password">
                                        <small class="text-gray-400 edit-pass-note" style="display: none;">Leave blank to keep current password</small>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <label class="form-label h6 mb-8">Class/Course Access</label>
                                    <div class="row g-10">
                                        <?php if (empty($classes)): ?>
                                            <div class="col-12 text-gray-400 text-13">No active classes available.</div>
                                        <?php else: ?>
                                            <?php foreach ($classes as $cls): ?>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input class-permission-checkbox" type="checkbox" name="class_ids[]" 
                                                               value="<?php echo $cls['id']; ?>" id="class_<?php echo $cls['id']; ?>">
                                                        <label class="form-check-label text-13" for="class_<?php echo $cls['id']; ?>">
                                                            <?php echo htmlspecialchars($cls['class_name']); ?>
                                                            <span class="badge bg-<?php echo $cls['type'] === 'class' ? 'info' : 'warning'; ?>-50 text-<?php echo $cls['type'] === 'class' ? 'info' : 'warning'; ?>-600 text-10">
                                                                <?php echo ucfirst($cls['type']); ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex-align justify-content-end gap-8 mt-24">
                            <button type="button" class="btn btn-outline-gray rounded-pill py-9" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-main rounded-pill py-9" id="submitBtn">
                                <i class="ph ph-plus me-2"></i>
                                Create Organization
                            </button>
                        </div>
                    </form>
                </div>
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
        // Initialize DataTable
        $(document).ready(function() {
            $('#organizationsTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                language: {
                    search: "",
                    searchPlaceholder: "Search organizations...",
                    lengthMenu: "_MENU_",
                    info: "_START_-_END_ of _TOTAL_",
                    infoEmpty: "0-0 of 0",
                    infoFiltered: "",
                    paginate: {
                        first: "«",
                        last: "»", 
                        next: "›",
                        previous: "‹"
                    },
                    emptyTable: "No organizations found"
                },
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>' +
                     '<"table-responsive"t>' +
                     '<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                columnDefs: [
                    { 
                        targets: -1,
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'asc']],
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Toggle admin fields
            $('#createAdmin').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#adminFields').slideDown();
                    $('#adminFullName, #adminUsername, #adminEmail').prop('required', true);
                    if ($('#formAction').val() === 'create') {
                        $('#adminPassword').prop('required', true);
                    }
                } else {
                    $('#adminFields').slideUp();
                    $('#adminFullName, #adminUsername, #adminEmail, #adminPassword').prop('required', false);
                }
            });
        });

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Organization';
            document.getElementById('formAction').value = 'create';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-plus me-2"></i>Create Organization';
            document.querySelector('#orgForm').reset();
            document.getElementById('orgId').value = '';
            
            // Reset Admin Section
            document.getElementById('adminUserId').value = '';
            document.getElementById('createAdmin').checked = false;
            document.getElementById('adminFields').style.display = 'none';
            document.querySelector('.admin-pass-label').innerHTML = '<span class="text-danger">*</span>';
            document.querySelector('.edit-pass-note').style.display = 'none';
            
            // Uncheck all classes
            document.querySelectorAll('.class-permission-checkbox').forEach(cb => cb.checked = false);
        }
        
        function editOrg(orgData) {
            document.getElementById('modalTitle').textContent = 'Edit Organization';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-check me-2"></i>Update Organization';
            
            document.getElementById('orgId').value = orgData.id;
            document.getElementById('name').value = orgData.name;
            document.getElementById('email').value = orgData.email || '';
            document.getElementById('phone').value = orgData.phone || '';
            document.getElementById('description').value = orgData.description || '';
            document.getElementById('address').value = orgData.address || '';
            document.getElementById('status').value = orgData.status;
            
            // Reset Admin Section
            document.getElementById('adminUserId').value = '';
            document.getElementById('createAdmin').checked = false;
            document.getElementById('adminFields').style.display = 'none';
            document.querySelectorAll('.class-permission-checkbox').forEach(cb => cb.checked = false);
            
            // Fill Admin details if exists
            if (orgData.admin_user_id) {
                document.getElementById('adminUserId').value = orgData.admin_user_id;
                document.getElementById('adminFullName').value = orgData.admin_full_name || '';
                document.getElementById('adminUsername').value = orgData.admin_username || '';
                document.getElementById('adminEmail').value = orgData.admin_email || '';
                document.getElementById('adminPassword').value = ''; // Don't show password
                
                document.getElementById('createAdmin').checked = true;
                document.getElementById('adminFields').style.display = 'block';
                
                document.querySelector('.admin-pass-label').innerHTML = '';
                document.querySelector('.edit-pass-note').style.display = 'block';
                $('#adminPassword').prop('required', false);
                
                // Set class permissions
                if (orgData.admin_class_ids) {
                    const classIds = orgData.admin_class_ids.split(',');
                    classIds.forEach(id => {
                        const cb = document.getElementById('class_' + id);
                        if (cb) cb.checked = true;
                    });
                }
            } else {
                document.querySelector('.admin-pass-label').innerHTML = '<span class="text-danger">*</span>';
                document.querySelector('.edit-pass-note').style.display = 'none';
            }
            
            new bootstrap.Modal(document.getElementById('orgModal')).show();
        }
        
        function deleteOrg(orgId, orgName) {
            if (confirm(`Are you sure you want to delete organization "${orgName}"? This will also delete all associated schools and users.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="org_id" value="${orgId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
