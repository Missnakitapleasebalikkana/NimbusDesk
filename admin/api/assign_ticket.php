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
$note = trim($_POST['note'] ?? '');

if ($ticketId <= 0 || $techId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ticket or technician selection.']);
    exit;
}

try {
    $sp = new AdminProcedures();
    $sp->assignTicket($ticketId, $user['id'], $techId, $note);

    echo json_encode([
        'success' => true,
        'message' => 'Ticket assigned successfully.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
