<?php
/**
 * Stored Procedure Caller for Technician Role
 */

require_once __DIR__ . '/../../config/db.php';

class TechnicianProcedures {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Executes `sp_tech_get_assigned_tickets`
     */
    public function getAssignedTickets(int $techId, string $statusFilter = 'all'): array {
        $stmt = $this->pdo->prepare("CALL sp_tech_get_assigned_tickets(?, ?)");
        $stmt->execute([$techId, $statusFilter]);
        $results = $stmt->fetchAll();
        $stmt->closeCursor();
        return $results;
    }

    /**
     * Executes `sp_tech_update_ticket_status`
     */
    public function updateTicketStatus(int $ticketId, int $techId, string $newStatus, ?string $resolutionSummary = null, ?string $note = null): bool {
        $stmt = $this->pdo->prepare("CALL sp_tech_update_ticket_status(?, ?, ?, ?, ?)");
        $success = $stmt->execute([$ticketId, $techId, $newStatus, $resolutionSummary, $note]);
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Executes `sp_tech_add_resolution_note`
     */
    public function addResolutionNote(int $ticketId, int $techId, string $note): bool {
        $stmt = $this->pdo->prepare("CALL sp_tech_add_resolution_note(?, ?, ?)");
        $success = $stmt->execute([$ticketId, $techId, $note]);
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Executes `sp_tech_get_stats`
     */
    public function getStats(int $techId): array {
        $stmt = $this->pdo->prepare("CALL sp_tech_get_stats(?)");
        $stmt->execute([$techId]);
        $stats = $stmt->fetch();
        $stmt->closeCursor();
        return $stats ?: [
            'in_progress_count' => 0, 
            'unassigned_open_count' => 0, 
            'resolved_today_count' => 0, 
            'total_resolved_count' => 0
        ];
    }

    /**
     * Helper to get full ticket info & logs for technician
     */
    public function getTicketDetails(int $ticketId): array {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name AS category_name, c.icon AS category_icon, 
                   u.name AS requester_name, u.department AS requester_dept, u.email AS requester_email,
                   tech.name AS technician_name
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
