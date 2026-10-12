<?php

namespace NimbusDesk\Models;

use NimbusDesk\Core\Database;
use PDO;

/**
 * Class TicketNote
 * Handles resolution logs, comments, status transitions, and attachments for ticket audits.
 */
class TicketNote {
    /**
     * Add a note or status change entry to a ticket.
     */
    public static function add(
        int $ticketId, 
        int $userId, 
        string $note, 
        ?string $statusChange = null, 
        bool $isInternal = false,
        ?string $attachment = null
    ): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO `ticket_notes` (`ticket_id`, `user_id`, `note`, `status_change`, `is_internal`, `attachment`, `created_at`)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $ticketId,
            $userId,
            trim($note),
            $statusChange,
            $isInternal ? 1 : 0,
            $attachment
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Fetch all notes/timeline events for a given ticket.
     * When $includeInternal is false (e.g. employee portal), internal notes are filtered out.
     */
    public static function getForTicket(int $ticketId, bool $includeInternal = true): array {
        $db = Database::getConnection();
        
        $sql = "
            SELECT 
                tn.*,
                u.name as author_name,
                u.role as author_role,
                u.department as author_dept
            FROM `ticket_notes` tn
            JOIN `users` u ON tn.user_id = u.id
            WHERE tn.ticket_id = ?
        ";

        if (!$includeInternal) {
            $sql .= " AND tn.is_internal = 0";
        }

        $sql .= " ORDER BY tn.created_at ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll();
    }
}
