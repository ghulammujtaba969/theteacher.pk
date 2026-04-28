<?php
// Application configuration
define('APP_NAME', 'Syllabus Management System');
define('APP_VERSION', '1.0.0');
// define('BASE_URL', 'https://theteacher.pk/');
define('BASE_URL', 'http://127.0.0.1/theteacher.pk(backup)/2026-04-28/');

// Database configuration
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'u921830511_teacherpk');
// define('DB_USER', 'u921830511_teacherpk_user');
// define('DB_PASS', '0Hwmc!c5p;');
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'u921830511_teacherpk');
define('DB_USER', 'root');
define('DB_PASS', '');

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 90 * 1024 * 1024); // 90MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mp3', 'wav', 'avi', 'mov']);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
session_start();

// Timezone
date_default_timezone_set('UTC');

// Load .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// OAuth2 / OpenID Connect Configuration
// Google
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
// Microsoft (v2.0 endpoints)
define('MICROSOFT_CLIENT_ID', $_ENV['MICROSOFT_CLIENT_ID'] ?? '');
define('MICROSOFT_CLIENT_SECRET', $_ENV['MICROSOFT_CLIENT_SECRET'] ?? '');
// Common redirect URI used by both providers. Configure the same in provider portals.
define('OIDC_REDIRECT_URI', BASE_URL . 'oauth_callback.php');


// Zoom API Configuration
define('ZOOM_ACCOUNT_ID', $_ENV['ZOOM_ACCOUNT_ID'] ?? '');
define('ZOOM_CLIENT_ID', $_ENV['ZOOM_CLIENT_ID'] ?? '');
define('ZOOM_CLIENT_SECRET', $_ENV['ZOOM_CLIENT_SECRET'] ?? '');
define('ZOOM_API_BASE_URL', 'https://api.zoom.us/v2');

// OneDrive PPTX embed URL (public/shared or org-allowed link)
// Example format: https://onedrive.live.com/embed?resid=...&authkey=...&em=2
// define('PPTX_EMBED_URL', $_ENV['PPTX_EMBED_URL'] ?? '');
?>
