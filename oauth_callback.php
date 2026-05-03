<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/User.php';

// Determine provider based on hint param stored in session during start (or infer by issuer later)
$provider = isset($_GET['provider']) ? strtolower($_GET['provider']) : ($_SESSION['oauth_provider'] ?? '');
$purpose = $_SESSION['oauth_purpose'] ?? 'login'; // Get the purpose from session

// Clear OAuth session variables
unset($_SESSION['oauth_provider']);
unset($_SESSION['oauth_purpose']);

// Validate state
if (!isset($_GET['state']) || !validate_state_token($_GET['state'])) {
    flash_message('Invalid request. Please try again.', 'error');
    redirect('login.php');
}

if (!isset($_GET['code'])) {
    flash_message('Action was cancelled or failed.', 'error');
    redirect('login.php');
}

$code = $_GET['code'];

// Helper: HTTP POST for token exchange
function http_post($url, $data, $headers = []) {
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers),
            'content' => http_build_query($data),
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return [$response, $http_response_header ?? []];
}

// Exchange code for tokens and fetch userinfo
$id_token = '';
$email = '';
$email_verified = false;

if ($provider === 'google') {
    $token_url = 'https://oauth2.googleapis.com/token';
    list($body) = http_post($token_url, [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => OIDC_REDIRECT_URI,
    ]);
    $token = json_decode($body, true) ?: [];
    $id_token = $token['id_token'] ?? '';
    if (!empty($id_token)) {
        $parts = explode('.', $id_token);
        $payload = json_decode(base64_decode(strtr($parts[1] ?? '', '-_', '+/')), true) ?: [];
        $email = $payload['email'] ?? '';
        $email_verified = (bool)($payload['email_verified'] ?? false);
    }
    if (!$email_verified) {
        // fallback to userinfo
        $userinfo = @file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . ($token['access_token'] ?? '')
            ]
        ]));
        $ui = json_decode($userinfo, true) ?: [];
        $email = $email ?: ($ui['email'] ?? '');
        $email_verified = (bool)($ui['email_verified'] ?? false);
    }
} else {
    // Microsoft
    $token_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    list($body) = http_post($token_url, [
        'client_id' => MICROSOFT_CLIENT_ID,
        'client_secret' => MICROSOFT_CLIENT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => OIDC_REDIRECT_URI,
    ]);
    $token = json_decode($body, true) ?: [];
    
    // Debug: Log the token response
    error_log("Microsoft token response: " . print_r($token, true));
    
    $id_token = $token['id_token'] ?? '';
    $access_token = $token['access_token'] ?? '';
    
    // Try to get email from id_token first
    if (!empty($id_token)) {
        $parts = explode('.', $id_token);
        if (count($parts) >= 2) {
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true) ?: [];
            $email = $payload['email'] ?? $payload['upn'] ?? $payload['unique_name'] ?? '';
            $preferred = $payload['preferred_username'] ?? $payload['upn'] ?? $payload['unique_name'] ?? '';
            $email_verified = !empty($email) || !empty($preferred);
            
            // Debug: Log id_token payload
            error_log("Microsoft id_token payload: " . print_r($payload, true));
        }
    }
    
    // If no email from id_token, try Microsoft Graph API
    if (empty($email) && !empty($access_token)) {
        $graph_url = 'https://graph.microsoft.com/v1.0/me';
        $graph_response = @file_get_contents($graph_url, false, stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $access_token,
                'method' => 'GET'
            ]
        ]));
        
        if ($graph_response) {
            $graph_data = json_decode($graph_response, true) ?: [];
            $email = $graph_data['mail'] ?? $graph_data['userPrincipalName'] ?? $graph_data['email'] ?? '';
            $email_verified = !empty($email);
            
            // Debug: Log Graph response
            error_log("Microsoft Graph response: " . print_r($graph_data, true));
        } else {
            error_log("Microsoft Graph API failed. Response: " . $graph_response);
        }
    }
    
    // Fallback to preferred_username if still no email
    if (empty($email) && !empty($preferred)) {
        $email = $preferred;
        $email_verified = true;
    }
    
    // Additional fallback: if we have an access token but no email, try a different Graph endpoint
    if (empty($email) && !empty($access_token)) {
        $graph_url2 = 'https://graph.microsoft.com/v1.0/me?$select=mail,userPrincipalName,email';
        $graph_response2 = @file_get_contents($graph_url2, false, stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $access_token,
                'method' => 'GET'
            ]
        ]));
        
        if ($graph_response2) {
            $graph_data2 = json_decode($graph_response2, true) ?: [];
            $email = $graph_data2['mail'] ?? $graph_data2['userPrincipalName'] ?? $graph_data2['email'] ?? '';
            $email_verified = !empty($email);
            
            error_log("Microsoft Graph API v2 response: " . print_r($graph_data2, true));
        }
    }
}

// Debug: Log final email status
error_log("Final email check - Email: '$email', Verified: " . ($email_verified ? 'true' : 'false'));

if (empty($email) || !$email_verified) {
    // More detailed error message for debugging
    $debug_info = "Email: '$email', Verified: " . ($email_verified ? 'true' : 'false');
    error_log("OAuth email extraction failed: $debug_info");
    
    // User-friendly message for Microsoft first-time login issue
    if ($provider === 'microsoft') {
        flash_message('Microsoft login requires additional permissions. Please try logging in again - this is normal for first-time users.', 'error');
    } else {
        flash_message('Unable to obtain a verified email from the provider. Please try again.', 'error');
    }
    redirect('login.php');
}

// Map to existing users table by email and set full session
$database = new Database();
$db = $database->getConnection();
$userModel = new User($db); // Pass DB connection to User constructor
$existing = $userModel->findByEmail($email);

if (!$existing) {
    if ($purpose === 'register') {
        // New registration: create a user
        $newUserData = [
            'username' => explode('@', $email)[0], // Use part of email as username
            'email' => $email,
            'password' => password_hash(generate_random_password(), PASSWORD_DEFAULT), // Generate a random password, user can change later
            'full_name' => $payload['name'] ?? explode('@', $email)[0], // Try to get name from OAuth payload
            'role_id' => 5, // Default to Solo Student
            'status' => 'active' // Automatically active for OAuth registrations
        ];
        // For OAuth registrations, 'created_by' is typically null or a system ID
        $new_user_id = $userModel->create($newUserData, null, true); // true indicates password is already hashed

        if ($new_user_id) {
            $existing = $userModel->getById($new_user_id); // Fetch the newly created user
            flash_message('Registration successful! You have been logged in.', 'success');
        } else {
            error_log("OAuth registration failed for email: " . $email);
            flash_message('Registration failed. Please try again.', 'error');
            redirect('register.php');
        }
    } else {
        flash_message('Access denied. No active user found with your email. Please register or contact support.', 'error');
        redirect('login.php');
    }
}

if (!$existing) {
    // This condition should ideally not be met if user was just created
    flash_message('Could not retrieve user data after OAuth. Please try again.', 'error');
    redirect('login.php');
}

// Persist key fields as in password login
$_SESSION['user_id'] = $existing['id'] ?? null;
$_SESSION['username'] = $existing['username'] ?? '';
$_SESSION['email'] = $existing['email'] ?? '';

// Normalize role slug
$roleSlug = '';
if (!empty($existing['role'])) {
    $roleSlug = $existing['role'];
} elseif (!empty($existing['role_name'])) {
    $roleSlug = strtolower(str_replace(' ', '_', $existing['role_name']));
}
$_SESSION['role'] = $roleSlug;
unset($_SESSION['_permissions']);

$_SESSION['user'] = $existing;

// Redirect into the normal app so existing class/lecture permissions apply
redirect('dashboard.php');
?>


