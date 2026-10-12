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
$note = trim($_POST['note'] ?? '');

if ($ticketId <= 0 || empty($note)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid ticket ID and note content.']);
    exit;
}

try {
    $sp = new TechnicianProcedures();
    $sp->addResolutionNote($ticketId, $user['id'], $note);

    echo json_encode([
        'success' => true,
        'message' => 'Troubleshooting update added to ticket history.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
