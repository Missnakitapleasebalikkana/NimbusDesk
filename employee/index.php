<?php
/**
 * Employee Portal Entry Point & Router
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/stored_procs/EmployeeProcedures.php';

// RBAC Guard: Only employee role can enter
requireRole('employee');

$user = currentUser();
$page = $_GET['page'] ?? 'dashboard';
$sp = new EmployeeProcedures();

switch ($page) {
    case 'create':
        $pageTitle = 'Submit Technical Issue';
        $activePage = 'create';
        $categories = $sp->getCategories();
        
        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/create_ticket.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'detail':
        $ticketId = intval($_GET['id'] ?? 0);
        $data = $sp->getTicketDetails($ticketId, $user['id']);
        
        if (!$data['ticket']) {
            header('Location: ' . url('employee/index.php'));
            exit;
        }

        $ticket = $data['ticket'];
        $logs = $data['logs'];
        $pageTitle = 'Ticket #' . $ticket['ticket_code'];
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/ticket_detail.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'history':
        $pageTitle = 'Incident History Log';
        $activePage = 'history';
        $tickets = $sp->getTickets($user['id'], 'all');

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/history.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'dashboard':
    default:
        $statusFilter = $_GET['status'] ?? 'all';
        $tickets = $sp->getTickets($user['id'], $statusFilter);
        $stats = $sp->getStats($user['id']);
        $pageTitle = 'Employee Dashboard';
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/dashboard.php';
        require __DIR__ . '/views/footer.php';
        break;
}
