<?php
/**
 * Technician Portal Entry Point & Router
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/stored_procs/TechnicianProcedures.php';

// RBAC Guard: Only technician and admin role can enter
requireRole(['technician', 'admin']);

$user = currentUser();
$page = $_GET['page'] ?? 'dashboard';
$sp = new TechnicianProcedures();

switch ($page) {
    case 'manage':
        $ticketId = intval($_GET['id'] ?? 0);
        $data = $sp->getTicketDetails($ticketId);

        if (!$data['ticket']) {
            header('Location: ' . url('technician/index.php'));
            exit;
        }

        $ticket = $data['ticket'];
        $logs = $data['logs'];
        $pageTitle = 'Manage Ticket #' . $ticket['ticket_code'];
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/manage_ticket.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'history':
        $tickets = $sp->getAssignedTickets($user['id'], 'Resolved');
        $pageTitle = 'Resolved History';
        $activePage = 'history';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/history.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'dashboard':
    default:
        $statusFilter = $_GET['status'] ?? 'all';
        $tickets = $sp->getAssignedTickets($user['id'], $statusFilter);
        $stats = $sp->getStats($user['id']);
        $pageTitle = 'Technician Queue';
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/dashboard.php';
        require __DIR__ . '/views/footer.php';
        break;
}
