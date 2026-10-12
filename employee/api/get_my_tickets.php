<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../stored_procs/EmployeeProcedures.php';

if (!isAuthenticated() || currentUser()['role'] !== 'employee') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = currentUser();
$status = $_GET['status'] ?? 'all';

try {
    $sp = new EmployeeProcedures();
    $tickets = $sp->getTickets($user['id'], $status);
    $stats = $sp->getStats($user['id']);

    echo json_encode([
        'success' => true,
        'tickets' => $tickets,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
