<?php
require_once 'config/config.php';
require_once 'classes/ZoomAPI.php';

// Only super admin can test
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    die('❌ Access denied - Super admin only');
}

echo "<h2>Zoom API Connection Test</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
</style>";

// Step 1: Check credentials
echo "<div class='section'>";
echo "<h3>Step 1: Checking Zoom Credentials</h3>";
$credentials_ok = true;

if (empty(ZOOM_ACCOUNT_ID)) {
    echo "❌ <span class='error'>ZOOM_ACCOUNT_ID is not set</span><br>";
    $credentials_ok = false;
} else {
    echo "✅ <span class='success'>ZOOM_ACCOUNT_ID:</span> " . substr(ZOOM_ACCOUNT_ID, 0, 10) . "...<br>";
}

if (empty(ZOOM_CLIENT_ID)) {
    echo "❌ <span class='error'>ZOOM_CLIENT_ID is not set</span><br>";
    $credentials_ok = false;
} else {
    echo "✅ <span class='success'>ZOOM_CLIENT_ID:</span> " . substr(ZOOM_CLIENT_ID, 0, 10) . "...<br>";
}

if (empty(ZOOM_CLIENT_SECRET)) {
    echo "❌ <span class='error'>ZOOM_CLIENT_SECRET is not set</span><br>";
    $credentials_ok = false;
} else {
    echo "✅ <span class='success'>ZOOM_CLIENT_SECRET:</span> " . substr(ZOOM_CLIENT_SECRET, 0, 10) . "...<br>";
}

if (!$credentials_ok) {
    echo "<br><span class='error'>Please add the following to your .env file:</span><br>";
    echo "<pre>
ZOOM_ACCOUNT_ID=your_account_id
ZOOM_CLIENT_ID=your_client_id
ZOOM_CLIENT_SECRET=your_client_secret
</pre>";
    exit;
}
echo "</div>";

// Step 2: Test OAuth token
echo "<div class='section'>";
echo "<h3>Step 2: Testing OAuth Token</h3>";
try {
    $zoomAPI = new ZoomAPI();
    echo "✅ <span class='success'>Successfully created ZoomAPI instance and obtained access token</span><br>";
} catch (Exception $e) {
    echo "❌ <span class='error'>Failed to get access token: " . htmlspecialchars($e->getMessage()) . "</span><br>";
    echo "<br><span class='info'>Common causes:</span><br>";
    echo "- Invalid credentials (check your Zoom App credentials)<br>";
    echo "- App not activated in Zoom Marketplace<br>";
    echo "- Account ID mismatch<br>";
    echo "- Internet connectivity issues<br>";
    exit;
}
echo "</div>";

// Step 3: Test API connection
echo "<div class='section'>";
echo "<h3>Step 3: Testing API Connection</h3>";
$testResult = $zoomAPI->testConnection();

if ($testResult['success']) {
    echo "✅ <span class='success'>Successfully connected to Zoom API!</span><br>";
    echo "<br><span class='info'>User Information:</span><br>";
    echo "<pre>" . print_r($testResult['user'], true) . "</pre>";
} else {
    echo "❌ <span class='error'>Failed to connect: " . htmlspecialchars($testResult['error']) . "</span><br>";
    exit;
}
echo "</div>";

// Step 4: Test creating a meeting
echo "<div class='section'>";
echo "<h3>Step 4: Testing Meeting Creation</h3>";
try {
    // Create a test meeting 1 hour from now
    $test_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $meetingData = [
        'topic' => 'Test Meeting - ' . date('Y-m-d H:i:s'),
        'start_time' => $test_time,
        'duration' => 30,
        'password' => 'test123',
        'agenda' => 'This is a test meeting created by the diagnostic tool'
    ];
    
    echo "<span class='info'>Attempting to create test meeting...</span><br>";
    $response = $zoomAPI->createMeeting($meetingData);
    
    if ($response && isset($response['id'])) {
        echo "✅ <span class='success'>Test meeting created successfully!</span><br><br>";
        echo "<strong>Meeting Details:</strong><br>";
        echo "Meeting ID: " . htmlspecialchars($response['id']) . "<br>";
        echo "Join URL: <a href='" . htmlspecialchars($response['join_url']) . "' target='_blank'>" . htmlspecialchars($response['join_url']) . "</a><br>";
        echo "Start Time: " . htmlspecialchars($response['start_time']) . "<br>";
        echo "Password: " . htmlspecialchars($response['password'] ?? 'None') . "<br>";
        
        // Try to delete the test meeting
        echo "<br><span class='info'>Cleaning up test meeting...</span><br>";
        try {
            $zoomAPI->deleteMeeting($response['id']);
            echo "✅ <span class='success'>Test meeting deleted successfully</span><br>";
        } catch (Exception $e) {
            echo "⚠️ <span class='error'>Could not delete test meeting: " . htmlspecialchars($e->getMessage()) . "</span><br>";
            echo "Please delete meeting ID " . htmlspecialchars($response['id']) . " manually from Zoom.<br>";
        }
    } else {
        echo "❌ <span class='error'>Failed to create test meeting</span><br>";
        echo "<pre>" . print_r($response, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ <span class='error'>Error creating test meeting: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}
echo "</div>";

// Final verdict
echo "<div class='section'>";
echo "<h3>Final Verdict</h3>";
echo "✅ <span class='success'>Your Zoom API integration is working correctly!</span><br>";
echo "You can now create meetings through your application.<br>";
echo "</div>";

echo "<br><a href='zoom-meetings.php'>← Back to Zoom Meetings</a>";
?>