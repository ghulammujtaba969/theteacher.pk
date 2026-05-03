<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'classes/Role.php';
require_once 'classes/Permission.php';

require_permission('roles.view', 'dashboard.php');

$current_user = current_user();

$database = new Database();
$db        = $database->getConnection();
$roleObj   = new Role($db);
$permObj   = new Permission($db);

// ── AJAX: get user direct permissions ──────────────────────
if (isset($_GET['ajax']) && $_GET['ajax'] === 'user_permissions' && isset($_GET['user_id'])) {
    header('Content-Type: application/json');
    $rows = $permObj->getUserPermissions((int)$_GET['user_id']);
    // Return as { permission_name: granted }
    $map = [];
    foreach ($rows as $r) $map[$r['name']] = (bool)$r['granted'];
    echo json_encode($map);
    exit;
}

// ── POST ACTIONS ──────────────────────────────────────────
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $action_permissions = [
        'create_role' => 'roles.create',
        'update_role' => 'roles.edit',
        'delete_role' => 'roles.delete',
        'sync_role_permissions' => 'permissions.manage',
        'set_user_permission' => 'permissions.manage',
        'remove_user_permission' => 'permissions.manage',
        'clear_user_permissions' => 'permissions.manage',
    ];
    if (isset($action_permissions[$action]) && !can($action_permissions[$action])) {
        permission_denied('roles-permissions.php');
    }

    // Create role
    if ($action === 'create_role' && can('roles.create')) {
        $name = trim($_POST['role_name'] ?? '');
        $desc = trim($_POST['role_description'] ?? '');
        if (!$name) { $errors[] = 'Role name is required.'; }
        else {
            $new_id = $roleObj->create(['name' => $name, 'description' => $desc]);
            if ($new_id) {
                // Clone permissions from a base role if requested
                if (!empty($_POST['clone_from'])) {
                    $roleObj->clonePermissions((int)$_POST['clone_from'], $new_id);
                }
                flush_permissions();
                flash_message("Role \"$name\" created.", 'success');
            } else {
                $errors[] = 'Failed to create role.';
            }
        }
    }

    // Update role metadata
    if ($action === 'update_role' && can('roles.edit')) {
        $role_id = (int)($_POST['role_id'] ?? 0);
        $roleObj->update($role_id, [
            'name'        => trim($_POST['role_name'] ?? ''),
            'description' => trim($_POST['role_description'] ?? ''),
        ]);
        flush_permissions();
        flash_message('Role updated.', 'success');
    }

    // Delete role
    if ($action === 'delete_role' && can('roles.delete')) {
        $role_id = (int)($_POST['role_id'] ?? 0);
        if ($roleObj->delete($role_id)) {
            flush_permissions();
            flash_message('Role deleted.', 'success');
        } else {
            $errors[] = 'Cannot delete a system role.';
        }
    }

    // Sync role permissions
    if ($action === 'sync_role_permissions' && can('permissions.manage')) {
        $role_id    = (int)($_POST['role_id'] ?? 0);
        $perm_ids   = array_map('intval', $_POST['permission_ids'] ?? []);
        if ($roleObj->syncPermissions($role_id, $perm_ids)) {
            flush_permissions();
            flash_message('Permissions saved for role.', 'success');
        } else {
            $errors[] = 'Failed to save permissions.';
        }
    }

    // Set direct user permission override
    if ($action === 'set_user_permission' && can('permissions.manage')) {
        $user_id    = (int)($_POST['user_id']       ?? 0);
        $perm_id    = (int)($_POST['permission_id'] ?? 0);
        $granted    = (int)($_POST['granted']        ?? 1);
        $permObj->setUserPermission($user_id, $perm_id, $granted, (int)$current_user['id']);
        flush_permissions();
        flash_message('User permission override saved.', 'success');
    }

    // Remove user permission override
    if ($action === 'remove_user_permission' && can('permissions.manage')) {
        $user_id = (int)($_POST['user_id']       ?? 0);
        $perm_id = (int)($_POST['permission_id'] ?? 0);
        $permObj->removeUserPermission($user_id, $perm_id);
        flush_permissions();
        flash_message('User override removed.', 'success');
    }

    // Clear ALL user overrides
    if ($action === 'clear_user_permissions' && can('permissions.manage')) {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $permObj->clearUserPermissions($user_id);
        flush_permissions();
        flash_message('All user overrides cleared.', 'success');
    }

    if (!empty($errors)) {
        foreach ($errors as $e) flash_message($e, 'error');
    }
    redirect('roles-permissions.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
}

// ── DATA ──────────────────────────────────────────────────
$roles          = $roleObj->getAll();
$all_perms      = $permObj->getAllGrouped();
$all_perms_flat = $permObj->getAll();
$stats          = $permObj->getStats();
$active_tab     = $_GET['tab'] ?? 'roles';

// For user overrides tab — get all non-super-admin users
$users_stmt = $db->query(
    "SELECT u.id, u.full_name, u.username, u.email, r.name as role_name, r.id as role_id
     FROM users u JOIN roles r ON u.role_id = r.id
     WHERE u.status = 'active'
     ORDER BY r.name, u.full_name"
);
$users_for_override = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions — <?php echo APP_NAME; ?></title>
    <link rel="shortcut icon" href="assets/images/logo/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        /* ── permission matrix ── */
        .perm-matrix { width: 100%; border-collapse: collapse; }
        .perm-matrix th { background: var(--main-50); color: var(--main-700, #1a4fa0);
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; padding: 10px 14px; white-space: nowrap; }
        .perm-matrix td { padding: 8px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .perm-matrix tr:hover td { background: #fafbff; }
        .module-header td { background: #f3f6ff; font-weight: 700; font-size: 12px;
            color: #3D7FF9; text-transform: uppercase; letter-spacing: .8px; padding: 6px 14px; }

        /* toggle switch */
        .perm-toggle { width: 40px; height: 20px; appearance: none; -webkit-appearance: none;
            background: #d1d5db; border-radius: 999px; cursor: pointer;
            position: relative; transition: background .2s; }
        .perm-toggle:checked { background: #3D7FF9; }
        .perm-toggle::before { content: ''; position: absolute; top: 2px; left: 2px;
            width: 16px; height: 16px; background: #fff; border-radius: 50%;
            transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
        .perm-toggle:checked::before { transform: translateX(20px); }

        .role-badge { display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .role-card { cursor: pointer; transition: border-color .15s, box-shadow .15s; }
        .role-card.active { border-color: #3D7FF9 !important; box-shadow: 0 0 0 3px rgba(61,127,249,.12); }
        .role-card:hover { border-color: #a0b8f8; }

        .sticky-header { position: sticky; top: 0; z-index: 10; background: #fff; }

        /* select-all row */
        .select-all-row td { background: #fffbeb; font-weight: 600; font-size: 12px; }

        .user-badge { font-size: 11px; padding: 2px 8px; border-radius: 999px;
            font-weight: 600; background: #e0e7ff; color: #3730a3; }

        .override-grant  { color: #16a34a; font-weight: 600; font-size: 11px; }
        .override-deny   { color: #dc2626; font-weight: 600; font-size: 11px; }
    </style>
</head>
<body>
<div class="preloader"><div class="loader"></div></div>
<div class="side-overlay"></div>
<?php include 'includes/sidebar_new.php'; ?>

<div class="dashboard-main-wrapper">
    <?php include 'includes/navbar_new.php'; ?>
    <div class="dashboard-body">

        <!-- Breadcrumb -->
        <div class="breadcrumb mb-24">
            <ul class="flex-align gap-4">
                <li><a href="dashboard.php" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                <li><span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span></li>
                <li><span class="text-main-600 fw-normal text-15">Roles & Permissions</span></li>
            </ul>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']==='error'?'danger':'success'; ?> alert-dismissible fade show mb-20">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats row -->
        <div class="row g-20 mb-24">
            <?php
            $stat_items = [
                ['label'=>'Total Permissions', 'value'=>$stats['total'],  'color'=>'main',    'icon'=>'ph-key'],
                ['label'=>'Roles Configured',  'value'=>count($roles),    'color'=>'success', 'icon'=>'ph-users-three'],
                ['label'=>'User Overrides',     'value'=>$stats['direct'], 'color'=>'warning', 'icon'=>'ph-user-gear'],
                ['label'=>'Permission Groups',  'value'=>count($all_perms),'color'=>'info',    'icon'=>'ph-stack'],
            ];
            foreach ($stat_items as $s): ?>
            <div class="col-xxl-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="flex-between mb-16">
                            <span class="w-48 h-48 flex-center rounded-circle bg-<?php echo $s['color']; ?>-50 text-<?php echo $s['color']; ?>-600 text-2xl">
                                <i class="ph <?php echo $s['icon']; ?>"></i>
                            </span>
                        </div>
                        <h3 class="mb-2"><?php echo $s['value']; ?></h3>
                        <span class="text-gray-400"><?php echo $s['label']; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabs -->
        <div class="card">
            <div class="card-header border-bottom border-gray-100">
                <ul class="nav nav-pills common-tab gap-20" id="rp-tab">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab==='roles'?'active':''; ?>" href="?tab=roles">
                            <i class="ph ph-users-three me-2"></i>Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab==='matrix'?'active':''; ?>" href="?tab=matrix">
                            <i class="ph ph-table me-2"></i>Role Permissions Matrix
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab==='user_overrides'?'active':''; ?>" href="?tab=user_overrides">
                            <i class="ph ph-user-gear me-2"></i>User Overrides
                        </a>
                    </li>
                    <?php if (can('permissions.view') || can('permissions.manage')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active_tab==='all_perms'?'active':''; ?>" href="?tab=all_perms">
                            <i class="ph ph-list-checks me-2"></i>All Permissions
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card-body p-0">

                <!-- ═══════════════════════════════════════════════════════
                     TAB 1 — ROLES
                ═══════════════════════════════════════════════════════ -->
                <?php if ($active_tab === 'roles'): ?>
                <div class="p-24">
                    <div class="row g-20">

                        <!-- Role list (left) -->
                        <div class="col-lg-4">
                            <div class="flex-between mb-16">
                                <h6 class="mb-0">All Roles</h6>
                                <?php if (can('roles.create')): ?>
                                <button class="btn btn-main btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                    <i class="ph ph-plus me-1"></i>New Role
                                </button>
                                <?php endif; ?>
                            </div>

                            <?php foreach ($roles as $role): ?>
                            <?php $colors = [1=>'danger',2=>'warning',3=>'info',4=>'success',5=>'purple']; $c = $colors[$role['id']] ?? 'main'; ?>
                            <div class="card border border-gray-100 mb-12 role-card <?php echo $_GET['role_id']??'' == $role['id'] ? 'active' : ''; ?>"
                                 onclick="window.location='?tab=roles&role_id=<?php echo $role['id']; ?>'">
                                <div class="card-body p-16">
                                    <div class="flex-between mb-8">
                                        <div class="flex-align gap-8">
                                            <span class="w-36 h-36 flex-center rounded-circle bg-<?php echo $c; ?>-50 text-<?php echo $c; ?>-600 text-xl">
                                                <i class="ph ph-shield-check"></i>
                                            </span>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($role['name']); ?></h6>
                                        </div>
                                        <div class="flex-align gap-4">
                                            <span class="role-badge bg-<?php echo $c; ?>-50 text-<?php echo $c; ?>-600">
                                                <?php echo $role['permission_count']; ?> perms
                                            </span>
                                        </div>
                                    </div>
                                    <?php if ($role['description']): ?>
                                    <p class="text-13 text-gray-400 mb-0"><?php echo htmlspecialchars($role['description']); ?></p>
                                    <?php endif; ?>
                                    <div class="flex-between mt-8">
                                        <span class="text-12 text-gray-400"><i class="ph ph-users me-1"></i><?php echo $role['user_count']; ?> users</span>
                                        <?php if (can('roles.edit')): ?>
                                        <button class="btn btn-outline-main btn-sm rounded-pill py-4 px-12 text-12"
                                                onclick="event.stopPropagation(); openEditRole(<?php echo htmlspecialchars(json_encode($role)); ?>)">
                                            <i class="ph ph-pencil"></i> Edit
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Role permission editor (right) -->
                        <div class="col-lg-8">
                            <?php
                            $selected_role_id = (int)($_GET['role_id'] ?? ($roles[0]['id'] ?? 0));
                            $selected_role    = null;
                            foreach ($roles as $r) { if ($r['id'] == $selected_role_id) { $selected_role = $r; break; } }
                            $role_perm_ids    = $selected_role ? $roleObj->getPermissionIds($selected_role_id) : [];
                            ?>
                            <?php if ($selected_role): ?>
                            <div class="flex-between mb-16">
                                <div>
                                    <h5 class="mb-2"><?php echo htmlspecialchars($selected_role['name']); ?></h5>
                                    <span class="text-13 text-gray-400">
                                        <?php echo $selected_role['permission_count']; ?> permissions assigned &bull;
                                        <?php echo $selected_role['user_count']; ?> users
                                    </span>
                                </div>
                                <?php if (can('permissions.manage')): ?>
                                <button class="btn btn-main rounded-pill" onclick="saveRolePerms(<?php echo $selected_role_id; ?>)">
                                    <i class="ph ph-floppy-disk me-2"></i>Save Permissions
                                </button>
                                <?php endif; ?>
                            </div>

                            <form id="rolePermForm_<?php echo $selected_role_id; ?>" method="POST">
                                <input type="hidden" name="action"  value="sync_role_permissions">
                                <input type="hidden" name="role_id" value="<?php echo $selected_role_id; ?>">
                                <input type="hidden" name="tab"     value="roles">

                                <?php if ($selected_role_id == 1): ?>
                                <div class="alert alert-info d-flex align-items-center gap-8">
                                    <i class="ph ph-info text-xl"></i>
                                    <span>Super Admin has all permissions automatically. The matrix below is for reference only.</span>
                                </div>
                                <?php endif; ?>

                                <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                                <table class="perm-matrix">
                                    <thead class="sticky-header">
                                        <tr>
                                            <th style="min-width:280px">Permission</th>
                                            <th>Description</th>
                                            <th style="width:80px; text-align:center">Access</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($all_perms as $module => $module_perms): ?>
                                        <tr class="module-header">
                                            <td colspan="3">
                                                <i class="ph ph-folder me-2"></i>
                                                <?php echo ucwords(str_replace('_', ' ', $module)); ?>
                                                <span class="ms-8 text-gray-400 fw-normal">(<?php echo count($module_perms); ?>)</span>
                                            </td>
                                        </tr>
                                        <tr class="select-all-row">
                                            <td colspan="2" class="text-12 text-gray-500">
                                                Select all in this group:
                                            </td>
                                            <td style="text-align:center">
                                                <input type="checkbox"
                                                       class="perm-toggle module-select-all"
                                                       data-module="<?php echo $module; ?>"
                                                       <?php echo ($selected_role_id==1)?'checked disabled':''; ?>
                                                       title="Toggle all <?php echo $module; ?>">
                                            </td>
                                        </tr>
                                        <?php foreach ($module_perms as $perm): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-medium"><?php echo htmlspecialchars($perm['display_name']); ?></span>
                                                <br><code class="text-11 text-gray-400"><?php echo htmlspecialchars($perm['name']); ?></code>
                                            </td>
                                            <td class="text-13 text-gray-400"><?php echo htmlspecialchars($perm['description'] ?? ''); ?></td>
                                            <td style="text-align:center">
                                                <input type="checkbox"
                                                       name="permission_ids[]"
                                                       value="<?php echo $perm['id']; ?>"
                                                       class="perm-toggle perm-cb"
                                                       data-module="<?php echo $module; ?>"
                                                       <?php echo in_array($perm['id'], $role_perm_ids) || $selected_role_id==1 ? 'checked' : ''; ?>
                                                       <?php echo $selected_role_id==1 ? 'disabled' : ''; ?>>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            </form>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="ph ph-arrow-left text-gray-300" style="font-size:3rem;"></i>
                                <p class="text-gray-400 mt-12">Select a role on the left to manage its permissions.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


                <!-- ═══════════════════════════════════════════════════════
                     TAB 2 — MATRIX (roles × permissions overview)
                ═══════════════════════════════════════════════════════ -->
                <?php elseif ($active_tab === 'matrix'): ?>
                <div class="p-0" style="overflow-x:auto;">
                    <?php
                    // Precompute: role_id => [perm_id => true]
                    $matrix = [];
                    foreach ($roles as $r) {
                        $ids = $roleObj->getPermissionIds($r['id']);
                        $matrix[$r['id']] = array_fill_keys($ids, true);
                    }
                    ?>
                    <table class="perm-matrix" style="font-size:12px;">
                        <thead class="sticky-header">
                            <tr>
                                <th style="min-width:220px; position:sticky; left:0; background:#f3f6ff; z-index:11;">Permission</th>
                                <?php foreach ($roles as $r): ?>
                                <th style="text-align:center; min-width:130px;">
                                    <?php echo htmlspecialchars($r['name']); ?>
                                    <br><span class="text-gray-400 fw-normal"><?php echo $r['permission_count']; ?> perms</span>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($all_perms as $module => $module_perms): ?>
                            <tr class="module-header">
                                <td colspan="<?php echo count($roles)+1; ?>">
                                    <i class="ph ph-folder me-2"></i><?php echo ucwords(str_replace('_',' ',$module)); ?>
                                </td>
                            </tr>
                            <?php foreach ($module_perms as $perm): ?>
                            <tr>
                                <td style="position:sticky; left:0; background:#fff; z-index:5; border-right:1px solid #eee;">
                                    <?php echo htmlspecialchars($perm['display_name']); ?>
                                    <br><code style="font-size:10px;color:#9ca3af;"><?php echo htmlspecialchars($perm['name']); ?></code>
                                </td>
                                <?php foreach ($roles as $r): ?>
                                <td style="text-align:center;">
                                    <?php if ($r['id']==1 || isset($matrix[$r['id']][$perm['id']])): ?>
                                    <span style="color:#16a34a; font-size:18px;">✓</span>
                                    <?php else: ?>
                                    <span style="color:#e5e7eb; font-size:18px;">−</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <!-- ═══════════════════════════════════════════════════════
                     TAB 3 — USER OVERRIDES
                ═══════════════════════════════════════════════════════ -->
                <?php elseif ($active_tab === 'user_overrides'): ?>
                <div class="p-24">
                    <div class="row g-24">
                        <!-- User selector -->
                        <div class="col-lg-4">
                            <h6 class="mb-16">Select User</h6>
                            <input type="text" class="form-control mb-12" id="userSearch"
                                   placeholder="Search users…" oninput="filterUsers()">
                            <div style="max-height:520px;overflow-y:auto;" id="userList">
                                <?php foreach ($users_for_override as $u): ?>
                                <div class="card border border-gray-100 mb-8 role-card user-item"
                                     data-name="<?php echo strtolower(htmlspecialchars($u['full_name'].' '.$u['username'])); ?>"
                                     onclick="loadUserOverrides(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars(addslashes($u['full_name'])); ?>', <?php echo $u['role_id']; ?>)"
                                     id="ucard_<?php echo $u['id']; ?>">
                                    <div class="card-body p-12">
                                        <div class="flex-between">
                                            <div>
                                                <p class="mb-0 fw-medium text-14"><?php echo htmlspecialchars($u['full_name']); ?></p>
                                                <p class="mb-0 text-12 text-gray-400">@<?php echo htmlspecialchars($u['username']); ?></p>
                                            </div>
                                            <span class="user-badge"><?php echo htmlspecialchars($u['role_name']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Override editor -->
                        <div class="col-lg-8">
                            <div id="overridePanel">
                                <div class="text-center py-5 text-gray-400">
                                    <i class="ph ph-user-gear" style="font-size:3rem;"></i>
                                    <p class="mt-12">Select a user to manage their permission overrides.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- ═══════════════════════════════════════════════════════
                     TAB 4 — ALL PERMISSIONS
                ═══════════════════════════════════════════════════════ -->
                <?php elseif ($active_tab === 'all_perms'): ?>
                <div class="p-24">
                    <div class="flex-between mb-20">
                        <h6 class="mb-0">All System Permissions (<?php echo $stats['total']; ?>)</h6>
                        <span class="text-13 text-gray-400">Read-only reference</span>
                    </div>
                    <div class="table-responsive">
                    <table class="perm-matrix">
                        <thead>
                            <tr>
                                <th>Slug</th>
                                <th>Display Name</th>
                                <th>Module</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($all_perms as $module => $module_perms): ?>
                            <tr class="module-header">
                                <td colspan="4"><i class="ph ph-folder me-2"></i><?php echo ucwords(str_replace('_',' ',$module)); ?></td>
                            </tr>
                            <?php foreach ($module_perms as $p): ?>
                            <tr>
                                <td><code class="text-12"><?php echo htmlspecialchars($p['name']); ?></code></td>
                                <td><?php echo htmlspecialchars($p['display_name']); ?></td>
                                <td><span class="py-2 px-8 bg-main-50 text-main-600 rounded-pill text-11"><?php echo htmlspecialchars($p['module']); ?></span></td>
                                <td class="text-gray-400 text-13"><?php echo htmlspecialchars($p['description'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /dashboard-body -->

    <div class="dashboard-footer">
        <p class="text-gray-300 text-13 fw-normal">&copy; <?php echo APP_NAME . ' ' . date('Y'); ?></p>
    </div>
</div><!-- /dashboard-main-wrapper -->

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create_role">
                <input type="hidden" name="tab" value="roles">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-16">
                        <label class="form-label fw-medium">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" class="form-control" required placeholder="e.g. Content Manager">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="role_description" class="form-control" rows="2" placeholder="What does this role do?"></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-medium">Clone permissions from</label>
                        <select name="clone_from" class="form-select">
                            <option value="">— Start blank —</option>
                            <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Optionally inherit permissions from an existing role.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gray rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-main rounded-pill">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="tab" value="roles">
                <input type="hidden" name="role_id" id="editRoleId">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-16">
                        <label class="form-label fw-medium">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" id="editRoleName" class="form-control" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="role_description" id="editRoleDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div id="editRoleSystemNotice" class="alert alert-warning d-none">
                        <i class="ph ph-warning me-2"></i>This is a system role. Renaming is allowed but it cannot be deleted.
                    </div>
                </div>
                <div class="modal-footer flex-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger rounded-pill" id="deleteRoleBtn"
                                onclick="deleteRole()">Delete Role</button>
                    </div>
                    <div class="flex-align gap-8">
                        <button type="button" class="btn btn-outline-gray rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-main rounded-pill">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Role form (hidden) -->
<form method="POST" id="deleteRoleForm">
    <input type="hidden" name="action" value="delete_role">
    <input type="hidden" name="tab" value="roles">
    <input type="hidden" name="role_id" id="deleteRoleId">
</form>

<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/boostrap.bundle.min.js"></script>
<script src="assets/js/phosphor-icon.js"></script>
<script src="assets/js/main.js"></script>
<script>
// ── Role editing ───────────────────────────────────────────
const SYSTEM_ROLES = ['Super Admin','Organization Admin','School Admin','Teacher','Solo Student'];

function openEditRole(role) {
    document.getElementById('editRoleId').value   = role.id;
    document.getElementById('editRoleName').value = role.name;
    document.getElementById('editRoleDesc').value  = role.description || '';
    const isSystem = SYSTEM_ROLES.includes(role.name);
    document.getElementById('editRoleSystemNotice').classList.toggle('d-none', !isSystem);
    document.getElementById('deleteRoleBtn').disabled = isSystem;
    document.getElementById('deleteRoleId').value = role.id;
    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
}

function deleteRole() {
    if (confirm('Delete this role? Users assigned to it will need a new role.')) {
        document.getElementById('deleteRoleForm').submit();
    }
}

// ── Role permission save ────────────────────────────────────
function saveRolePerms(roleId) {
    if (confirm('Save permission changes for this role?')) {
        document.getElementById('rolePermForm_' + roleId).submit();
    }
}

// ── Module select-all toggles ───────────────────────────────
document.querySelectorAll('.module-select-all').forEach(function(el) {
    el.addEventListener('change', function() {
        const mod = this.dataset.module;
        document.querySelectorAll('.perm-cb[data-module="' + mod + '"]').forEach(function(cb) {
            cb.checked = el.checked;
        });
    });
});

// Keep module-select-all in sync with individual checkboxes
document.querySelectorAll('.perm-cb').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const mod = this.dataset.module;
        const all = document.querySelectorAll('.perm-cb[data-module="' + mod + '"]');
        const allChecked = Array.from(all).every(function(c) { return c.checked; });
        const sa = document.querySelector('.module-select-all[data-module="' + mod + '"]');
        if (sa) sa.checked = allChecked;
    });
});

// ── User overrides ──────────────────────────────────────────
let _currentUserId   = null;
let _currentRoleId   = null;
let _currentUserName = '';
const ALL_PERMS      = <?php echo json_encode($all_perms_flat); ?>;

function filterUsers() {
    const q = document.getElementById('userSearch').value.toLowerCase();
    document.querySelectorAll('.user-item').forEach(function(el) {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

function loadUserOverrides(userId, userName, roleId) {
    _currentUserId   = userId;
    _currentRoleId   = roleId;
    _currentUserName = userName;

    // Highlight selected user
    document.querySelectorAll('.user-item').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById('ucard_' + userId)?.classList.add('active');

    fetch('roles-permissions.php?ajax=user_permissions&user_id=' + userId)
        .then(function(r) { return r.json(); })
        .then(function(overrides) { renderOverridePanel(userId, userName, roleId, overrides); });
}

function renderOverridePanel(userId, userName, roleId, overrides) {
    // Get role permission names from preloaded data
    const rolePerms = {};
    <?php foreach ($roles as $r):
        $rnames = $roleObj->getPermissionNames($r['id']);
    ?>
    if (roleId === <?php echo $r['id']; ?>) {
        <?php foreach ($rnames as $rn): ?>
        rolePerms['<?php echo $rn; ?>'] = true;
        <?php endforeach; ?>
    }
    <?php endforeach; ?>

    let html = `
    <div class="flex-between mb-16">
        <div>
            <h5 class="mb-2">${userName}</h5>
            <span class="text-13 text-gray-400">Direct permission overrides (add or deny on top of role)</span>
        </div>
        <form method="POST" onsubmit="return confirm('Clear all overrides?');">
            <input type="hidden" name="action" value="clear_user_permissions">
            <input type="hidden" name="user_id" value="${userId}">
            <input type="hidden" name="tab" value="user_overrides">
            <button class="btn btn-outline-danger btn-sm rounded-pill">
                <i class="ph ph-trash me-1"></i>Clear All Overrides
            </button>
        </form>
    </div>
    <div class="table-responsive" style="max-height:520px; overflow-y:auto;">
    <table class="perm-matrix">
        <thead class="sticky-header">
            <tr>
                <th style="min-width:200px">Permission</th>
                <th style="width:100px; text-align:center">Role Default</th>
                <th style="width:80px; text-align:center">Grant</th>
                <th style="width:80px; text-align:center">Deny</th>
                <th style="width:80px; text-align:center">Clear</th>
            </tr>
        </thead><tbody>`;

    // Group perms by module
    const grouped = {};
    ALL_PERMS.forEach(function(p) {
        if (!grouped[p.module]) grouped[p.module] = [];
        grouped[p.module].push(p);
    });

    for (const [module, perms] of Object.entries(grouped)) {
        html += `<tr class="module-header"><td colspan="5"><i class="ph ph-folder me-2"></i>${module.replace(/_/g,' ')}</td></tr>`;
        perms.forEach(function(p) {
            const hasRole  = rolePerms[p.name] || false;
            const override = overrides[p.name];
            const isGrant  = override === true;
            const isDeny   = override === false;

            html += `<tr>
                <td>
                    <span class="fw-medium text-13">${p.display_name}</span><br>
                    <code class="text-11 text-gray-400">${p.name}</code>
                </td>
                <td style="text-align:center">
                    ${hasRole ? '<span style="color:#16a34a;font-size:16px;">✓</span>' : '<span style="color:#d1d5db;font-size:16px;">−</span>'}
                </td>
                <td style="text-align:center">
                    <button type="button" class="btn btn-sm ${isGrant?'btn-success':'btn-outline-success'} rounded-pill py-2 px-10"
                            onclick="setOverride(${userId}, ${p.id}, 1, '${p.name}')">
                        ${isGrant ? '✓ Granted' : 'Grant'}
                    </button>
                </td>
                <td style="text-align:center">
                    <button type="button" class="btn btn-sm ${isDeny?'btn-danger':'btn-outline-danger'} rounded-pill py-2 px-10"
                            onclick="setOverride(${userId}, ${p.id}, 0, '${p.name}')">
                        ${isDeny ? '✗ Denied' : 'Deny'}
                    </button>
                </td>
                <td style="text-align:center">
                    ${override !== undefined
                        ? `<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-2 px-10"
                               onclick="removeOverride(${userId}, ${p.id})">Clear</button>`
                        : '<span class="text-gray-300 text-12">—</span>'}
                </td>
            </tr>`;
        });
    }

    html += '</tbody></table></div>';
    document.getElementById('overridePanel').innerHTML = html;
}

function setOverride(userId, permId, granted, permName) {
    const fd = new FormData();
    fd.append('action', 'set_user_permission');
    fd.append('user_id', userId);
    fd.append('permission_id', permId);
    fd.append('granted', granted);
    fetch('roles-permissions.php', { method: 'POST', body: fd })
        .then(function() {
            // Reload overrides for this user
            fetch('roles-permissions.php?ajax=user_permissions&user_id=' + userId)
                .then(function(r) { return r.json(); })
                .then(function(ov) { renderOverridePanel(userId, _currentUserName, _currentRoleId, ov); });
        });
}

function removeOverride(userId, permId) {
    const fd = new FormData();
    fd.append('action', 'remove_user_permission');
    fd.append('user_id', userId);
    fd.append('permission_id', permId);
    fetch('roles-permissions.php', { method: 'POST', body: fd })
        .then(function() {
            fetch('roles-permissions.php?ajax=user_permissions&user_id=' + userId)
                .then(function(r) { return r.json(); })
                .then(function(ov) { renderOverridePanel(userId, _currentUserName, _currentRoleId, ov); });
        });
}
</script>
</body>
</html>
