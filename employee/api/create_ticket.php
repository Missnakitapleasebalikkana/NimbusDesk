<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../stored_procs/EmployeeProcedures.php';

if (!isAuthenticated() || currentUser()['role'] !== 'employee') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$user = currentUser();
$categoryId = intval($_POST['category_id'] ?? 0);
$urgency = trim($_POST['urgency'] ?? 'Medium');
$location = trim($_POST['location'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($subject) || empty($description) || empty($categoryId) || empty($location)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required ticket details (Subject, Location, Category, Description).']);
    exit;
}

$validUrgencies = ['Low', 'Medium', 'High', 'Critical'];
if (!in_array($urgency, $validUrgencies)) {
    $urgency = 'Medium';
}

$attachment = null;
if (!empty($_FILES['attachment']['name'])) {
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['attachment']['name']);
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename)) {
        $attachment = 'uploads/' . $filename;
    }
}

try {
    $sp = new EmployeeProcedures();
    $result = $sp->createTicket($user['id'], $categoryId, $urgency, $location, $subject, $description, $attachment);
    
    echo json_encode([
        'success' => true,
        'message' => 'Ticket created successfully!',
        'ticket_id' => $result['ticket_id'],
        'ticket_code' => $result['ticket_code']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
