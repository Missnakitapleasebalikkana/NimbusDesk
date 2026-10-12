<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Models/TicketNote.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;
use NimbusDesk\Models\TicketNote;

// Only Technicians and Admins can access
Auth::requireRole(['technician', 'admin']);
$currentUser = Auth::user();

// Handle One-Click Incident Claiming
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'claim_ticket') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Auth::setFlash('danger', 'Security validation failed.');
    } else {
        $claimId = (int)($_POST['ticket_id'] ?? 0);
        $ticketToClaim = Ticket::findById($claimId);

        if ($ticketToClaim) {
            Ticket::assignTechnician($claimId, $currentUser['id']);
            if ($ticketToClaim['status'] === 'Open') {
                Ticket::updateStatus($claimId, 'In Progress');
            }

            TicketNote::add(
                $claimId,
                $currentUser['id'],
                "Incident claimed by {$currentUser['name']} ({$currentUser['department']}). Status transitioned to In Progress.",
                'Claimed by Technician'
            );

            Auth::setFlash('success', "You have claimed incident {$ticketToClaim['ticket_code']}. It is now in your active queue!");
            header('Location: /NimbusDesk/technician/index.php');
            exit;
        } else {
            Auth::setFlash('danger', 'Ticket not found.');
        }
    }
}

// 1. My Currently Assigned Active Tickets
$assignedTickets = Ticket::getAllWithFilters([
    'assigned_to' => $currentUser['id'],
    'status'      => 'active'
]);

// 2. Unassigned Airport Queue (Open and assigned_to IS NULL)
$unassignedTickets = Ticket::getAllWithFilters([
    'unassigned_only' => true,
    'status'          => 'Open'
]);

// Personal technician stats
$db = NimbusDesk\Core\Database::getConnection();
$techStatsStmt = $db->prepare("
    SELECT 
        SUM(CASE WHEN assigned_to = ? AND status IN ('Open', 'In Progress') THEN 1 ELSE 0 END) as my_active,
        SUM(CASE WHEN assigned_to = ? AND status = 'In Progress' THEN 1 ELSE 0 END) as my_in_progress,
        SUM(CASE WHEN assigned_to = ? AND status = 'Resolved' AND DATE(resolved_at) = CURDATE() THEN 1 ELSE 0 END) as my_resolved_today,
        SUM(CASE WHEN assigned_to = ? AND status IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as my_total_resolved
    FROM `tickets`
");
$techStatsStmt->execute([$currentUser['id'], $currentUser['id'], $currentUser['id'], $currentUser['id']]);
$techStats = $techStatsStmt->fetch();

View::render('technician/dashboard', 'technician', [
    'title'             => 'Technician Workbench - NimbusDesk',
    'pageHeader'        => 'My Incident Queue & Assignments',
    'activeMenu'        => 'dashboard',
    'assignedTickets'   => $assignedTickets,
    'unassignedTickets' => $unassignedTickets,
    'techStats'         => $techStats
]);
