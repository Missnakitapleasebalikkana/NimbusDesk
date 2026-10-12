<?php
/**
 * Stored Procedure Caller for Employee Role
 */

require_once __DIR__ . '/../../config/db.php';

class EmployeeProcedures {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Executes `sp_employee_create_ticket`
     */
    public function createTicket(int $userId, int $categoryId, string $urgency, string $location, string $subject, string $description, ?string $attachment = null): array {
        $stmt = $this->pdo->prepare("CALL sp_employee_create_ticket(?, ?, ?, ?, ?, ?, ?, @new_id, @ticket_code)");
        $stmt->execute([$userId, $categoryId, $urgency, $location, $subject, $description, $attachment]);
        
        // Fetch output parameters
        $out = $this->pdo->query("SELECT @new_id AS ticket_id, @ticket_code AS ticket_code")->fetch();
        return $out ?: ['ticket_id' => null, 'ticket_code' => null];
    }

    /**
     * Executes `sp_employee_get_tickets`
     */
    public function getTickets(int $userId, string $statusFilter = 'all'): array {
        $stmt = $this->pdo->prepare("CALL sp_employee_get_tickets(?, ?)");
        $stmt->execute([$userId, $statusFilter]);
        $results = $stmt->fetchAll();
        $stmt->closeCursor();
        return $results;
    }

    /**
     * Executes `sp_employee_get_ticket_details`
     */
    public function getTicketDetails(int $ticketId, int $userId): array {
        $stmt = $this->pdo->prepare("CALL sp_employee_get_ticket_details(?, ?)");
        $stmt->execute([$ticketId, $userId]);
        
        $ticket = $stmt->fetch();
        $logs = [];
        
        if ($stmt->nextRowset()) {
            $logs = $stmt->fetchAll();
        }
        $stmt->closeCursor();
        
        return [
            'ticket' => $ticket ?: null,
            'logs' => $logs
        ];
    }

    /**
     * Executes `sp_employee_get_stats`
     */
    public function getStats(int $userId): array {
        $stmt = $this->pdo->prepare("CALL sp_employee_get_stats(?)");
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();
        $stmt->closeCursor();
        return $stats ?: ['active_count' => 0, 'resolved_count' => 0, 'total_count' => 0];
    }

    /**
     * Helper to get categories for dropdown
     */
    public function getCategories(): array {
        $stmt = $this->pdo->query("SELECT id, name, description, icon FROM categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }
}
