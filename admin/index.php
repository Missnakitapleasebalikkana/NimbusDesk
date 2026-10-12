<?php
/**
 * Admin Portal Entry Point & Router
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/stored_procs/AdminProcedures.php';

// RBAC Guard: Only admin role can enter
requireRole('admin');

$user = currentUser();
$page = $_GET['page'] ?? 'dashboard';
$sp = new AdminProcedures();

switch ($page) {
    case 'manage':
        $ticketId = intval($_GET['id'] ?? 0);
        $data = $sp->getTicketDetails($ticketId);

        if (!$data['ticket']) {
            header('Location: ' . url('admin/index.php'));
            exit;
        }

        $ticket = $data['ticket'];
        $logs = $data['logs'];
        $technicians = $sp->getTechnicians();
        $pageTitle = 'Manage Ticket #' . $ticket['ticket_code'];
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/manage_ticket.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'history':
        $categoryFilter = $_GET['category'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
        $history = $sp->getIncidentHistory($categoryFilter, $search);
        $categories = $sp->getCategories();
        $pageTitle = 'Incident History Archive';
        $activePage = 'history';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/incident_history.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'reports':
        $pdo = Database::getConnection();
        // Category distribution
        $catStmt = $pdo->query("
            SELECT c.name, c.icon, COUNT(t.id) AS count
            FROM categories c
            LEFT JOIN tickets t ON c.id = t.category_id
            GROUP BY c.id, c.name, c.icon
            ORDER BY count DESC
        ");
        $categoryStats = $catStmt->fetchAll();

        // Urgency distribution
        $urgStmt = $pdo->query("
            SELECT urgency, COUNT(*) AS count
            FROM tickets
            GROUP BY urgency
            ORDER BY count DESC
        ");
        $urgencyStats = $urgStmt->fetchAll();

        $overallStats = $sp->getDashboardStats();
        $pageTitle = 'Incident Analytics & Frequency';
        $activePage = 'reports';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/reports.php';
        require __DIR__ . '/views/footer.php';
        break;

    case 'dashboard':
    default:
        $filters = [
            'status' => $_GET['status'] ?? 'all',
            'category' => $_GET['category'] ?? 'all',
            'urgency' => $_GET['urgency'] ?? 'all',
            'search' => trim($_GET['search'] ?? '')
        ];

        $tickets = $sp->getAllTickets(
            $filters['status'],
            $filters['category'],
            $filters['urgency'],
            $filters['search']
        );
        $stats = $sp->getDashboardStats();
        $technicians = $sp->getTechnicians();
        $categories = $sp->getCategories();
        $pageTitle = 'IT Incident Command';
        $activePage = 'dashboard';

        require __DIR__ . '/views/header.php';
        require __DIR__ . '/views/dashboard.php';
        require __DIR__ . '/views/footer.php';
        break;
}
