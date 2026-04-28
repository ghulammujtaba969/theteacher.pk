<?php
// get-inquiry-details.php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/ClassInquiry.php';

header('Content-Type: application/json');

// Check if user is logged in and has admin privileges
if (!is_logged_in()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$current_user = current_user();
$allowed_roles = ['Super Admin', 'Organization Admin', 'School Admin'];

if (!in_array($current_user['role_name'], $allowed_roles)) {
    echo json_encode(['error' => 'Insufficient permissions']);
    exit;
}

$inquiry_id = (int)($_GET['id'] ?? 0);
if ($inquiry_id <= 0) {
    echo json_encode(['error' => 'Invalid inquiry ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Get inquiry details with user and class information
$query = "SELECT ci.*, u.full_name, u.username, u.email, u.phone, 
                 c.class_name, c.class_code, c.description as class_description,
                 admin.full_name as reviewed_by_name
          FROM class_inquiries ci
          LEFT JOIN users u ON ci.user_id = u.id
          LEFT JOIN classes c ON ci.class_id = c.id
          LEFT JOIN users admin ON ci.reviewed_by = admin.id
          WHERE ci.id = ?";

$stmt = $db->prepare($query);
$stmt->execute([$inquiry_id]);
$inquiry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inquiry) {
    echo json_encode(['error' => 'Inquiry not found']);
    exit;
}

// Decode JSON fields
$timeSlots = json_decode($inquiry['preferred_time_slots'], true) ?? [];
// $days = json_decode($inquiry['preferred_days'], true) ?? [];

// Generate HTML for modal content
$html = '
<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold text-primary mb-3">Student Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Name:</strong></td>
                <td>' . htmlspecialchars($inquiry['full_name']) . '</td>
            </tr>
            <tr>
                <td><strong>Username:</strong></td>
                <td>' . htmlspecialchars($inquiry['username']) . '</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>' . htmlspecialchars($inquiry['email']) . '</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>' . htmlspecialchars($inquiry['phone'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>WhatsApp:</strong></td>
                <td>' . htmlspecialchars($inquiry['whatsapp_number']) . '</td>
            </tr>
            <tr>
                <td><strong>Contact Email:</strong></td>
                <td>' . htmlspecialchars($inquiry['contact_email']) . '</td>
            </tr>
            <tr>
                <td><strong>Country:</strong></td>
                <td>' . htmlspecialchars($inquiry['country']) . '</td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td>' . htmlspecialchars($inquiry['address']) . '</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold text-primary mb-3">Class Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Class Name:</strong></td>
                <td>' . htmlspecialchars($inquiry['class_name']) . '</td>
            </tr>
            <tr>
                <td><strong>Class Code:</strong></td>
                <td>' . htmlspecialchars($inquiry['class_code']) . '</td>
            </tr>
            <tr>
                <td><strong>Description:</strong></td>
                <td>' . htmlspecialchars($inquiry['class_description'] ?? 'N/A') . '</td>
            </tr>
        </table>
        
        <h6 class="fw-bold text-primary mb-3">Preferences</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Preferred Time Slots:</strong></td>
                <td>';

foreach ($timeSlots as $slot) {
    $html .= '<span class="badge bg-secondary me-1">' . htmlspecialchars($slot) . '</span>';
}

$html .= '</td>
            </tr>
            
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold text-primary mb-3">Status Information</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Current Status:</strong></td>
                <td>';

if ($inquiry['status'] == 'pending') {
    $html .= '<span class="badge bg-warning">Pending Review</span>';
} elseif ($inquiry['status'] == 'approved') {
    $html .= '<span class="badge bg-success">Approved</span>';
} else {
    $html .= '<span class="badge bg-danger">Rejected</span>';
}

$html .= '</td>
            </tr>
            <tr>
                <td><strong>Submitted On:</strong></td>
                <td>' . date('F j, Y \a\t g:i A', strtotime($inquiry['created_at'])) . '</td>
            </tr>';

if ($inquiry['reviewed_at']) {
    $html .= '<tr>
                <td><strong>Reviewed On:</strong></td>
                <td>' . date('F j, Y \a\t g:i A', strtotime($inquiry['reviewed_at'])) . '</td>
            </tr>
            <tr>
                <td><strong>Reviewed By:</strong></td>
                <td>' . htmlspecialchars($inquiry['reviewed_by_name'] ?? 'N/A') . '</td>
            </tr>';
}

if ($inquiry['admin_notes']) {
    $html .= '<tr>
                <td><strong>Admin Notes:</strong></td>
                <td>' . htmlspecialchars($inquiry['admin_notes']) . '</td>
            </tr>';
}

$html .= '</table>
    </div>
</div>';

// Generate action buttons based on status
$actions = '';
if ($inquiry['status'] == 'pending') {
    $actions = '
        <button type="button" class="btn btn-success" onclick="approveInquiry(' . $inquiry['id'] . ')">
            <i class="ri-check-line"></i> Approve
        </button>
        <button type="button" class="btn btn-danger" onclick="rejectInquiry(' . $inquiry['id'] . ')">
            <i class="ri-close-line"></i> Reject
        </button>';
} elseif ($inquiry['status'] == 'approved') {
    $actions = '<span class="text-success"><i class="ri-check-circle-line"></i> Already Approved</span>';
} else {
    $actions = '<span class="text-danger"><i class="ri-close-circle-line"></i> Already Rejected</span>';
}

echo json_encode([
    'html' => $html,
    'actions' => $actions,
    'inquiry' => $inquiry
]);
