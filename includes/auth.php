<?php
/**
 * includes/auth.php
 *
 * Drop-in permission helper for theteacher.pk.
 * Include once per request (already loaded via functions.php or config).
 *
 * Usage:
 *   can('classes.create')          // bool
 *   require_permission('users.edit') // dies with 403 if denied
 *   current_user_can('zoom.join')   // alias of can()
 */

/**
 * Load & cache the permission map for the current session user.
 * Stored in $_SESSION['_permissions'] as [slug => bool].
 */
function load_permissions(bool $force = false): void {
    if (!$force && isset($_SESSION['_permissions'])) {
        return; // already loaded
    }

    $user = current_user();
    if (!$user) {
        $_SESSION['_permissions'] = [];
        return;
    }

    // Super Admin short-circuit: grant everything without a DB hit
    if (($user['role_name'] ?? '') === 'Super Admin' || ($user['role_id'] ?? 0) == 1) {
        $_SESSION['_permissions'] = ['*' => true];
        return;
    }

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../classes/Permission.php';

    $database = new Database();
    $db       = $database->getConnection();
    $permObj  = new Permission($db);

    $_SESSION['_permissions'] = $permObj->loadUserPermissions(
        (int)$user['id'],
        (int)$user['role_id']
    );
}

/**
 * Check if the current logged-in user has a permission.
 *
 * @param string $permission  Dot-notation slug, e.g. 'classes.create'
 * @return bool
 */
function can(string $permission): bool {
    if (!is_logged_in()) return false;

    load_permissions();

    $perms = $_SESSION['_permissions'] ?? [];

    // Wildcard (Super Admin)
    if (isset($perms['*']) && $perms['*'] === true) return true;

    return isset($perms[$permission]) && $perms[$permission] === true;
}

/** Alias */
function current_user_can(string $permission): bool {
    return can($permission);
}

/**
 * Require a permission or send a 403.
 * Optionally redirect instead of dying.
 */
function require_permission(string $permission, string $redirect_url = ''): void {
    if (!can($permission)) {
        if ($redirect_url) permission_denied($redirect_url);
        http_response_code(403);
        // Graceful 403 page
        $user = current_user();
        $name = htmlspecialchars($user['full_name'] ?? 'User');
        echo <<<HTML
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Access Denied</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/main.css">
</head><body>
<div class="d-flex align-items-center justify-content-center min-vh-100">
  <div class="text-center p-40">
    <div class="mb-24" style="font-size:72px;">🔒</div>
    <h2 class="mb-8">Access Denied</h2>
    <p class="text-gray-400 mb-24">Sorry {$name}, you don't have permission to perform this action.<br>
    Required permission: <code>{$permission}</code></p>
    <a href="dashboard.php" class="btn btn-main rounded-pill px-32">Go to Dashboard</a>
  </div>
</div>
</body></html>
HTML;
        exit;
    }
}

/**
 * Require at least one permission or redirect / render 403.
 */
function require_any_permission(array $permissions, string $redirect_url = ''): void {
    if (!can_any($permissions)) {
        if ($redirect_url) permission_denied($redirect_url);
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

/**
 * Standard denial flow for action/page redirects.
 */
function permission_denied(string $redirect_url = 'dashboard.php'): void {
    flash_message('You do not have permission to perform this action.', 'error');
    redirect($redirect_url);
}

/**
 * Check multiple permissions — returns true if user has ANY of them.
 */
function can_any(array $permissions): bool {
    foreach ($permissions as $p) {
        if (can($p)) return true;
    }
    return false;
}

/**
 * Check multiple permissions — returns true only if user has ALL of them.
 */
function can_all(array $permissions): bool {
    foreach ($permissions as $p) {
        if (!can($p)) return false;
    }
    return true;
}

/**
 * Flush the cached permissions so the next call to can() reloads from DB.
 * Call this after changing a user's role or direct permissions.
 */
function flush_permissions(): void {
    unset($_SESSION['_permissions']);
}

/**
 * Blade-style helper for templates:
 *   <?php if_can('classes.edit'): ?> ... <?php endif_can(); ?>
 */
function if_can(string $permission): bool {
    return can($permission);
}

/**
 * Return a human-readable list of all permissions the current user holds.
 * Useful for debugging.
 */
function my_permissions(): array {
    load_permissions();
    $perms = $_SESSION['_permissions'] ?? [];
    if (isset($perms['*'])) return ['* (all permissions — Super Admin)'];
    return array_keys(array_filter($perms));
}
