<?php
// Common utility functions

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Backward-compatible alias used in some pages
function isLoggedIn() {
    return is_logged_in();
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_super_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

function is_organization_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'organization_admin';
}

function is_school_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'school_admin';
}

function require_roles($allowed_roles = []) {
    if (!is_logged_in()) {
        redirect('login.php');
    }
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $allowed_roles, true)) {
        redirect('login.php');
    }
}

function require_super_admin() {
    if (!is_logged_in() || !is_super_admin()) {
        redirect('login.php');
    }
}

function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function generate_unique_filename($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

function validate_file_upload($file) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error occurred.';
        return $errors;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size exceeds maximum allowed size.';
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_FILE_TYPES)) {
        $errors[] = 'File type not allowed.';
    }
    
    return $errors;
}

function flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Returns relative URL to a user's avatar image with fallbacks by gender
function user_avatar_url($user) {
    $id = (int)($user['id'] ?? ($user['user_id'] ?? 0));
    $gender = strtolower(trim($user['gender'] ?? ''));
    $exts = ['jpg','jpeg','png','gif','webp'];
    $bases = ['uploads/users', 'uploads/user'];

    // 0) DB-stored photo path takes precedence if valid
    if (!empty($user['photo']) && file_exists($user['photo'])) {
        return $user['photo'];
    }

    // 1) Specific uploaded avatar: uploads/users/user_{id}.ext or uploads/user/user_{id}.ext
    if ($id > 0) {
        foreach ($bases as $base) {
            foreach ($exts as $ext) {
                $candidate = $base . '/user_' . $id . '.' . $ext;
                if (file_exists($candidate)) {
                    return $candidate;
                }
            }
        }
    }

    // 2) Session-provided path (set after upload)
    if (!empty($_SESSION['user']['profile_image']) && file_exists($_SESSION['user']['profile_image'])) {
        return $_SESSION['user']['profile_image'];
    }

    // 3) Gender-based default in uploads/users or uploads/user
    $gender_file_map = [
        'female' => ['female.png','female.jpg','woman.png','woman.jpg'],
        'male'   => ['male.png','male.jpg','man.png','man.jpg'],
    ];
    $keys = isset($gender_file_map[$gender]) ? $gender_file_map[$gender] : $gender_file_map['male'];
    foreach ($bases as $base) {
        foreach ($keys as $file) {
            $candidate = $base . '/' . $file;
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
    }

    // 4) Final fallback
    return 'assets/images/thumbs/user-img.png';
}

// OAuth helpers
function generate_state_token() {
    $token = bin2hex(random_bytes(16));
    $_SESSION['oauth2_state'] = $token;
    return $token;
}

function validate_state_token($state) {
    if (!isset($_SESSION['oauth2_state'])) return false;
    $valid = hash_equals($_SESSION['oauth2_state'], $state);
    unset($_SESSION['oauth2_state']);
    return $valid;
}

function get_base_url() {
    return rtrim(BASE_URL, '/').'/';
}

function generate_random_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
    $password = '';
    $char_length = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $char_length - 1)];
    }
    return $password;
}
?>
