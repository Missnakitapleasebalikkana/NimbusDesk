<?php
/**
 * Stored Procedure Caller for Admin Role
 */

require_once __DIR__ . '/../../config/db.php';

class AdminProcedures {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Executes `sp_admin_get_all_tickets`
     */
    public function getAllTickets(string $statusFilter = 'all', string $categoryFilter = 'all', string $urgencyFilter = 'all', string $search = ''): array {
        $stmt = $this->pdo->prepare("CALL sp_admin_get_all_tickets(?, ?, ?, ?)");
        $stmt->execute([$statusFilter, $categoryFilter, $urgencyFilter, $search]);
        $results = $stmt->fetchAll();
        $stmt->closeCursor();
        return $results;
    }

    /**
     * Executes `sp_admin_assign_ticket`
     */
    public function assignTicket(int $ticketId, int $adminId, int $techId, ?string $note = null): bool {
        $stmt = $this->pdo->prepare("CALL sp_admin_assign_ticket(?, ?, ?, ?)");
        $success = $stmt->execute([$ticketId, $adminId, $techId, $note]);
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Executes `sp_admin_update_ticket`
     */
    public function updateTicket(int $ticketId, int $adminId, int $techId, string $status, string $urgency, ?string $resolutionNote = null): bool {
        $stmt = $this->pdo->prepare("CALL sp_admin_update_ticket(?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$ticketId, $adminId, $techId, $status, $urgency, $resolutionNote]);
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Executes `sp_admin_get_dashboard_stats`
     */
    public function getDashboardStats(): array {
        $stmt = $this->pdo->prepare("CALL sp_admin_get_dashboard_stats()");
        $stmt->execute();
        $stats = $stmt->fetch();
        $stmt->closeCursor();
        return $stats ?: [
            'critical_count' => 0, 
            'open_count' => 0, 
            'in_progress_count' => 0, 
            'resolved_today_count' => 0, 
            'total_resolved_count' => 0,
            'total_tickets_count' => 0
        ];
    }

    /**
     * Executes `sp_admin_get_incident_history`
     */
    public function getIncidentHistory(string $categoryFilter = 'all', string $search = '', int $limit = 50): array {
        $stmt = $this->pdo->prepare("CALL sp_admin_get_incident_history(?, ?, ?)");
        $stmt->execute([$categoryFilter, $search, $limit]);
        $results = $stmt->fetchAll();
        $stmt->closeCursor();
        return $results;
    }

    /**
     * Helper to retrieve list of active technicians for ticket assignment
     */
    public function getTechnicians(): array {
        $stmt = $this->pdo->query("SELECT id, name, department, email FROM users WHERE role = 'technician' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Helper to retrieve categories
     */
    public function getCategories(): array {
        $stmt = $this->pdo->query("SELECT id, name, description, icon FROM categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Helper to get full ticket info & logs for admin inspection
     */
    public function getTicketDetails(int $ticketId): array {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name AS category_name, c.icon AS category_icon, 
                   u.name AS requester_name, u.department AS requester_dept, u.email AS requester_email, u.badge_number AS requester_badge,
                   tech.name AS technician_name, tech.id AS tech_id
            FROM tickets t
            INNER JOIN categories c ON t.category_id = c.id
            INNER JOIN users u ON t.user_id = u.id
            LEFT JOIN users tech ON t.assigned_to = tech.id
            WHERE t.id = ?
        ");
        $stmt->execute([$ticketId]);
        $ticket = $stmt->fetch();

        $logStmt = $this->pdo->prepare("
            SELECT l.*, u.name AS actor_name, u.role AS actor_role
            FROM ticket_logs l
            INNER JOIN users u ON l.user_id = u.id
            WHERE l.ticket_id = ?
            ORDER BY l.created_at ASC
        ");
        $logStmt->execute([$ticketId]);
        $logs = $logStmt->fetchAll();

        return [
            'ticket' => $ticket ?: null,
            'logs' => $logs
        ];
    }
}
