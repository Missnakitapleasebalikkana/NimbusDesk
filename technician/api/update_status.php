<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../stored_procs/TechnicianProcedures.php';

if (!isAuthenticated() || !in_array(currentUser()['role'], ['technician', 'admin'])) {
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
$newStatus = trim($_POST['status'] ?? '');
$resolutionSummary = trim($_POST['resolution_summary'] ?? '');
$note = trim($_POST['note'] ?? '');

$validStatuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
if ($ticketId <= 0 || !in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ticket ID or status.']);
    exit;
}

try {
    $sp = new TechnicianProcedures();
    $sp->updateTicketStatus($ticketId, $user['id'], $newStatus, $resolutionSummary, $note);

    echo json_encode([
        'success' => true,
        'message' => "Ticket status updated to {$newStatus} successfully."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
