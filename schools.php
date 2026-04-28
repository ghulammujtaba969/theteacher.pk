<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'classes/School.php';
require_once 'classes/Organization.php';

// Check if user is logged in and has permission to manage schools
$current_user = current_user();
$user_role = $_SESSION['role'] ?? '';

// Super Admin can manage all schools, Organization Admin can manage schools in their org
if (!in_array($user_role, ['super_admin', 'organization_admin'])) {
    redirect('dashboard.php');
}
$database = new Database();
$db = $database->getConnection();
$school = new School($db);
$organization = new Organization($db);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'organization_id' => $_POST['organization_id'],
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'address' => trim($_POST['address']),
                    'phone' => trim($_POST['phone']),
                    'email' => trim($_POST['email']),
                    'status' => $_POST['status']
                ];
                
                // Organization Admin can only create schools in their organization
                if ($user_role === 'organization_admin' && $data['organization_id'] != $current_user['organization_id']) {
                    $error = 'You can only create schools in your organization.';
                } else {
                    if ($school->create($data)) {
                        $message = 'School created successfully!';
                    } else {
                        $error = 'Failed to create school. Please try again.';
                    }
                }
                break;
                
            case 'update':
                $school_id = $_POST['school_id'];
                $data = [
                    'organization_id' => $_POST['organization_id'],
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'address' => trim($_POST['address']),
                    'phone' => trim($_POST['phone']),
                    'email' => trim($_POST['email']),
                    'status' => $_POST['status']
                ];
                
                // Organization Admin can only update schools in their organization
                if ($user_role === 'organization_admin' && $data['organization_id'] != $current_user['organization_id']) {
                    $error = 'You can only update schools in your organization.';
                } else {
                    if ($school->update($school_id, $data)) {
                        $message = 'School updated successfully!';
                    } else {
                        $error = 'Failed to update school. Please try again.';
                    }
                }
                break;
                
            case 'delete':
                $school_id = $_POST['school_id'];
                $school_data = $school->getById($school_id);
                
                // Organization Admin can only delete schools in their organization
                if ($user_role === 'organization_admin' && $school_data['organization_id'] != $current_user['organization_id']) {
                    $error = 'You can only delete schools in your organization.';
                } else {
                    if ($school->delete($school_id)) {
                        $message = 'School deleted successfully!';
                    } else {
                        $error = 'Failed to delete school. Please try again.';
                    }
                }
                break;
        }
    }
}

// Get schools based on user role
if ($user_role === 'super_admin') {
    $schools = $school->getAll();
} else {
    $schools = $school->getAll($current_user['organization_id']);
}

// Get organizations for dropdown
if ($user_role === 'super_admin') {
    $organizations = $organization->getAll();
} else {
    $organizations = array_filter($organization->getAll(), function($org) use ($current_user) {
        return $org['id'] == $current_user['organization_id'];
    });
}

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>School Management - <?php echo APP_NAME; ?></title>
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
                    <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                    <li><span class="text-main-600 fw-normal text-15">School Management</span></li>
                </ul>
            </div>
            <!-- Breadcrumb End -->

            <?php if ($flash): ?>
                <div class="row mb-20">
                    <div class="col-12">
                        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show mb-24" role="alert">
                    <i class="ph ph-check-circle me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-24" role="alert">
                    <i class="ph ph-warning-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Schools Management Start -->
            <div class="card">
                <div class="card-header border-bottom border-gray-100 flex-between flex-wrap gap-8">
                    <h5 class="mb-0">School Management</h5>
                    <button class="btn btn-main text-sm btn-sm px-24 rounded-pill" data-bs-toggle="modal" data-bs-target="#schoolModal" onclick="openCreateModal()">
                        <i class="ph ph-plus me-2"></i>
                        Add New School
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="schoolsTable">
                            <thead>
                                <tr>
                                    <th class="h6 text-gray-300">Name</th>
                                    <th class="h6 text-gray-300">Organization</th>
                                    <th class="h6 text-gray-300">Contact</th>
                                    <th class="h6 text-gray-300">Address</th>
                                    <th class="h6 text-gray-300">Users</th>
                                    <th class="h6 text-gray-300">Status</th>
                                    <th class="h6 text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schools as $sch): ?>
                                <tr>
                                    <td>
                                        <div class="flex-align gap-8">
                                            <span class="w-32 h-32 bg-main-50 text-main-600 rounded-circle flex-center">
                                                <i class="ph ph-graduation-cap"></i>
                                            </span>
                                            <div>
                                                <span class="h6 mb-0"><?php echo htmlspecialchars($sch['name']); ?></span>
                                                <?php if ($sch['description']): ?>
                                                    <div class="text-13 text-gray-400"><?php echo htmlspecialchars(substr($sch['description'], 0, 50)) . (strlen($sch['description']) > 50 ? '...' : ''); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-15 fw-medium"><?php echo htmlspecialchars($sch['organization_name']); ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <?php if ($sch['email']): ?>
                                                <div class="text-13 text-gray-600">
                                                    <i class="ph ph-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($sch['email']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($sch['phone']): ?>
                                                <div class="text-13 text-gray-600">
                                                    <i class="ph ph-phone me-1"></i>
                                                    <?php echo htmlspecialchars($sch['phone']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!$sch['email'] && !$sch['phone']): ?>
                                                <span class="text-gray-400">N/A</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($sch['address']): ?>
                                            <span class="text-13 text-gray-600"><?php echo htmlspecialchars(substr($sch['address'], 0, 40)) . (strlen($sch['address']) > 40 ? '...' : ''); ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="w-44 h-28 bg-main-50 text-main-600 border border-main-100 text-13 px-8 py-4 rounded-pill">
                                            <?php echo $sch['user_count']; ?> users
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-13 py-2 px-8 <?php echo $sch['status'] === 'active' ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600'; ?> rounded-pill fw-medium">
                                            <?php echo ucfirst($sch['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex-align gap-8">
                                            <button type="button" class="w-32 h-32 bg-success-50 text-success-600 rounded-circle border border-success-100 flex-center text-sm hover-bg-success-600 hover-text-white hover-border-success-600" 
                                                    onclick="editSchool(<?php echo htmlspecialchars(json_encode($sch)); ?>)" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>
                                            <button type="button" class="w-32 h-32 bg-danger-50 text-danger-600 rounded-circle border border-danger-100 flex-center text-sm hover-bg-danger-600 hover-text-white hover-border-danger-600" 
                                                    onclick="deleteSchool(<?php echo $sch['id']; ?>, '<?php echo htmlspecialchars($sch['name']); ?>')" 
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
            <!-- Schools Management End -->
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

    <!-- School Modal -->
    <div class="modal fade" id="schoolModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-main-50 border-bottom border-gray-100">
                    <h5 class="modal-title" id="modalTitle">Add New School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-24">
                    <form method="POST" id="schoolForm">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="school_id" id="schoolId">
                        
                        <div class="row g-20">
                            <div class="col-md-6">
                                <label for="name" class="form-label h6 mb-8">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-11" name="name" id="name" placeholder="Enter school name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="organization_id" class="form-label h6 mb-8">Organization <span class="text-danger">*</span></label>
                                <select class="form-select py-11" name="organization_id" id="organization_id" required>
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                    <option value="<?php echo $org['id']; ?>"><?php echo htmlspecialchars($org['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-6">
                                <label for="email" class="form-label h6 mb-8">Email Address</label>
                                <input type="email" class="form-control py-11" name="email" id="email" placeholder="Enter email address">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label h6 mb-8">Phone Number</label>
                                <input type="text" class="form-control py-11" name="phone" id="phone" placeholder="Enter phone number">
                            </div>
                        </div>
                        
                        <div class="row g-20 mt-16">
                            <div class="col-md-12">
                                <label for="status" class="form-label h6 mb-8">Status <span class="text-danger">*</span></label>
                                <select class="form-select py-11" name="status" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-16">
                            <label for="description" class="form-label h6 mb-8">Description</label>
                            <textarea class="form-control py-11" name="description" id="description" rows="3" placeholder="Enter school description"></textarea>
                        </div>
                        
                        <div class="mt-16">
                            <label for="address" class="form-label h6 mb-8">Address</label>
                            <textarea class="form-control py-11" name="address" id="address" rows="2" placeholder="Enter school address"></textarea>
                        </div>
                        
                        <div class="flex-align justify-content-end gap-8 mt-24">
                            <button type="button" class="btn btn-outline-gray rounded-pill py-9" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-main rounded-pill py-9" id="submitBtn">
                                <i class="ph ph-plus me-2"></i>
                                Create School
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
            $('#schoolsTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                language: {
                    search: "",
                    searchPlaceholder: "Search schools...",
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
                    emptyTable: "No schools found"
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
                order: [[0, 'asc']],
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    // Custom pagination styling
                    $('.dataTables_paginate .paginate_button').removeClass('paginate_button').addClass('btn btn-sm btn-outline-secondary me-1');
                    $('.dataTables_paginate .current').removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('.dataTables_paginate .disabled').addClass('disabled');
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add New School';
            document.getElementById('formAction').value = 'create';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-plus me-2"></i>Create School';
            document.querySelector('#schoolForm').reset();
            document.getElementById('schoolId').value = '';
        }
        
        function editSchool(schoolData) {
            document.getElementById('modalTitle').textContent = 'Edit School';
            document.getElementById('formAction').value = 'update';
            document.getElementById('submitBtn').innerHTML = '<i class="ph ph-check me-2"></i>Update School';
            
            document.getElementById('schoolId').value = schoolData.id;
            document.getElementById('name').value = schoolData.name;
            document.getElementById('organization_id').value = schoolData.organization_id;
            document.getElementById('email').value = schoolData.email || '';
            document.getElementById('phone').value = schoolData.phone || '';
            document.getElementById('description').value = schoolData.description || '';
            document.getElementById('address').value = schoolData.address || '';
            document.getElementById('status').value = schoolData.status;
            
            new bootstrap.Modal(document.getElementById('schoolModal')).show();
        }
        
        function deleteSchool(schoolId, schoolName) {
            if (confirm(`Are you sure you want to delete "${schoolName}"? This action cannot be undone and will also affect all associated users.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="school_id" value="${schoolId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>