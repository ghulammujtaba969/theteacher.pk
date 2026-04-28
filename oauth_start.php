<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$provider = isset($_GET['provider']) ? strtolower($_GET['provider']) : '';
if (!in_array($provider, ['google', 'microsoft'], true)) {
    redirect('login.php');
}

$state = generate_state_token();
$_SESSION['oauth_provider'] = $provider;
$_SESSION['oauth_purpose'] = $_GET['purpose'] ?? 'login'; // Store purpose (login or register)

if ($provider === 'google') {
    $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => OIDC_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'prompt' => 'select_account'
    ];
} else {
    // Microsoft Azure AD v2 common endpoint
    $auth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    $params = [
        'client_id' => MICROSOFT_CLIENT_ID,
        'redirect_uri' => OIDC_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile User.Read',
        'response_mode' => 'query',
        'state' => $state
    ];
}

$location = $auth_url . '?' . http_build_query($params);
header('Location: ' . $location);
exit;
?>


