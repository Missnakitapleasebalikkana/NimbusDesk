<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../stored_procs/AdminProcedures.php';

if (!isAuthenticated() || currentUser()['role'] !== 'admin') {
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
$ticketId = intval($_POST['ticket_id'] ?? 0);
$techId = intval($_POST['technician_id'] ?? 0);
$status = trim($_POST['status'] ?? 'Open');
$urgency = trim($_POST['urgency'] ?? 'Medium');
$resolutionNote = trim($_POST['resolution_note'] ?? '');

$validStatuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
$validUrgencies = ['Low', 'Medium', 'High', 'Critical'];

if ($ticketId <= 0 || !in_array($status, $validStatuses) || !in_array($urgency, $validUrgencies)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

try {
    $sp = new AdminProcedures();
    $sp->updateTicket($ticketId, $user['id'], $techId, $status, $urgency, $resolutionNote);

    echo json_encode([
        'success' => true,
        'message' => 'Ticket updated successfully.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
