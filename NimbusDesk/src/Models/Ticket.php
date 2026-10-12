<?php

namespace NimbusDesk\Models;

use NimbusDesk\Core\Database;
use PDO;

/**
 * Class Ticket
 * Handles all database operations related to IT support tickets, reporting, and KPI analytics.
 */
class Ticket {
    /**
     * Generate the next unique ticket code (e.g. TKT-1008).
     */
    public static function generateTicketCode(): string {
        $db = Database::getConnection();
        $nextNumber = 1001;

        // Check highest existing numeric suffix in ticket_code
        $codeStmt = $db->query("SELECT ticket_code FROM `tickets` ORDER BY id DESC LIMIT 10");
        while ($row = $codeStmt->fetch()) {
            if (!empty($row['ticket_code']) && preg_match('/TKT-(\d+)/', $row['ticket_code'], $matches)) {
                $num = (int)$matches[1];
                if ($num >= $nextNumber) {
                    $nextNumber = $num + 1;
                }
            }
        }

        // Also safeguard against raw MAX(id)
        $idStmt = $db->query("SELECT MAX(id) as max_id FROM `tickets`");
        $idRow = $idStmt->fetch();
        if ($idRow && !empty($idRow['max_id'])) {
            $nextNumber = max($nextNumber, 1000 + (int)$idRow['max_id'] + 1);
        }

        return 'TKT-' . $nextNumber;
    }

    /**
     * Create a new ticket in the database with duplicate retry safeguard.
     */
    public static function create(array $data): int {
        $db = Database::getConnection();
        $attempts = 0;
        $maxAttempts = 5;

        while ($attempts < $maxAttempts) {
            $attempts++;
            $ticketCode = self::generateTicketCode();
            if ($attempts > 1) {
                $ticketCode .= '-' . rand(10, 99);
            }

            try {
                $stmt = $db->prepare("
                    INSERT INTO `tickets` (
                        `ticket_code`, `user_id`, `terminal`, `location`, `subject`, `category`, 
                        `urgency`, `status`, `description`, `attachment`, `created_at`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Open', ?, ?, NOW())
                ");

                $stmt->execute([
                    $ticketCode,
                    $data['user_id'],
                    $data['terminal'] ?? 'Terminal 1',
                    $data['location'] ?? null,
                    $data['subject'],
                    $data['category'] ?? 'Software',
                    $data['urgency'] ?? 'Medium',
                    $data['description'],
                    $data['attachment'] ?? null
                ]);

                return (int)$db->lastInsertId();
            } catch (\PDOException $e) {
                // If duplicate key error on ticket_code, loop and retry
                if ($e->getCode() == 23000 && $attempts < $maxAttempts) {
                    continue;
                }
                throw $e;
            }
        }

        return 0;
    }

    /**
     * Find a single ticket by its primary ID, with requester and technician details.
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                t.*,
                u.name AS requester_name,
                u.email AS requester_email,
                u.department AS requester_dept,
                u.phone AS requester_phone,
                tech.name AS technician_name,
                tech.email AS technician_email
            FROM `tickets` t
            JOIN `users` u ON t.user_id = u.id
            LEFT JOIN `users` tech ON t.assigned_to = tech.id
            WHERE t.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }

    /**
     * Find a single ticket by its ticket code (e.g. TKT-1001).
     */
    public static function findByCode(string $code): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                t.*,
                u.name AS requester_name,
                u.email AS requester_email,
                tech.name AS technician_name
            FROM `tickets` t
            JOIN `users` u ON t.user_id = u.id
            LEFT JOIN `users` tech ON t.assigned_to = tech.id
            WHERE t.ticket_code = ?
            LIMIT 1
        ");
        $stmt->execute([$code]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }

    /**
     * Get tickets created by a specific employee with optional search and category filters.
     */
    public static function getForEmployee(int $userId, ?string $statusFilter = null, ?string $search = null, ?string $category = null): array {
        $db = Database::getConnection();
        $sql = "
            SELECT 
                t.*,
                tech.name AS technician_name
            FROM `tickets` t
            LEFT JOIN `users` tech ON t.assigned_to = tech.id
            WHERE t.user_id = ?
        ";
        $params = [$userId];

        if ($statusFilter !== null) {
            if ($statusFilter === 'active') {
                $sql .= " AND t.status IN ('Open', 'In Progress')";
            } elseif ($statusFilter === 'resolved') {
                $sql .= " AND t.status IN ('Resolved', 'Closed')";
            }
        }

        if (!empty($category)) {
            $sql .= " AND t.category = ?";
            $params[] = $category;
        }

        if (!empty($search)) {
            $sql .= " AND (t.ticket_code LIKE ? OR t.subject LIKE ? OR t.description LIKE ? OR t.location LIKE ?)";
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY t.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get tickets with flexible filtering for IT Admin dashboard, technician queue, or incident log.
     */
    public static function getAllWithFilters(array $filters = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT 
                t.*,
                u.name AS requester_name,
                u.department AS requester_dept,
                tech.name AS technician_name
            FROM `tickets` t
            JOIN `users` u ON t.user_id = u.id
            LEFT JOIN `users` tech ON t.assigned_to = tech.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $sql .= " AND t.status IN ('Open', 'In Progress')";
            } else {
                $sql .= " AND t.status = ?";
                $params[] = $filters['status'];
            }
        }

        if (!empty($filters['urgency'])) {
            $sql .= " AND t.urgency = ?";
            $params[] = $filters['urgency'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND t.category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['terminal'])) {
            $sql .= " AND t.terminal = ?";
            $params[] = $filters['terminal'];
        }

        if (!empty($filters['unassigned_only'])) {
            $sql .= " AND t.assigned_to IS NULL";
        }

        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = ?";
            $params[] = $filters['assigned_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.ticket_code LIKE ? OR t.subject LIKE ? OR t.description LIKE ? OR u.name LIKE ? OR t.location LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY 
            CASE t.urgency 
                WHEN 'Critical' THEN 1 
                WHEN 'High' THEN 2 
                WHEN 'Medium' THEN 3 
                ELSE 4 
            END ASC, 
            t.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Update the progress status of a ticket, carefully maintaining resolved_at timestamp integrity.
     */
    public static function updateStatus(int $ticketId, string $newStatus): bool {
        $db = Database::getConnection();
        $current = self::findById($ticketId);

        $resolvedAt = $current['resolved_at'] ?? null;

        if ($newStatus === 'Resolved') {
            // If not previously resolved, record timestamp
            if (empty($resolvedAt)) {
                $resolvedAt = date('Y-m-d H:i:s');
            }
        } elseif ($newStatus === 'Closed') {
            // If moving to closed, preserve resolved date if exists, or set now
            if (empty($resolvedAt)) {
                $resolvedAt = date('Y-m-d H:i:s');
            }
        } else {
            // Reopened (Open or In Progress): reset resolved timestamp
            $resolvedAt = null;
        }

        $stmt = $db->prepare("
            UPDATE `tickets` 
            SET `status` = ?, `resolved_at` = ?
            WHERE `id` = ?
        ");
        return $stmt->execute([$newStatus, $resolvedAt, $ticketId]);
    }

    /**
     * Assign or unassign a ticket to an IT technician.
     */
    public static function assignTechnician(int $ticketId, ?int $technicianId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `tickets` SET `assigned_to` = ? WHERE `id` = ?");
        return $stmt->execute([$technicianId ?: null, $ticketId]);
    }

    /**
     * Get aggregate statistics for dashboard KPI cards.
     */
    public static function getStats(?int $userId = null): array {
        $db = Database::getConnection();
        
        if ($userId !== null) {
            // Employee specific statistics
            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status IN ('Open', 'In Progress') THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as resolved
                FROM `tickets`
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
        } else {
            // Global IT Admin / Technician statistics
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN urgency = 'Critical' AND status NOT IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as critical,
                    SUM(CASE WHEN DATE(resolved_at) = CURDATE() THEN 1 ELSE 0 END) as resolved_today
                FROM `tickets`
            ");
        }

        $res = $stmt->fetch();
        return [
            'total'          => (int)($res['total'] ?? 0),
            'active'         => (int)($res['active'] ?? 0),
            'open'           => (int)($res['open'] ?? 0),
            'in_progress'    => (int)($res['in_progress'] ?? 0),
            'resolved'       => (int)($res['resolved'] ?? 0),
            'critical'       => (int)($res['critical'] ?? 0),
            'resolved_today' => (int)($res['resolved_today'] ?? 0),
        ];
    }

    /**
     * Analytics: Breakdown of incidents by Category.
     */
    public static function getCategoryBreakdown(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT category, COUNT(*) as count 
            FROM `tickets` 
            GROUP BY category 
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Analytics: Breakdown of incidents by Urgency level.
     */
    public static function getUrgencyBreakdown(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT urgency, COUNT(*) as count 
            FROM `tickets` 
            GROUP BY urgency 
            ORDER BY 
                CASE urgency 
                    WHEN 'Critical' THEN 1 
                    WHEN 'High' THEN 2 
                    WHEN 'Medium' THEN 3 
                    ELSE 4 
                END ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Analytics: Breakdown of incidents across Airport Terminals.
     */
    public static function getTerminalBreakdown(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT terminal, COUNT(*) as count 
            FROM `tickets` 
            GROUP BY terminal 
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Analytics: Breakdown of incidents by Status.
     */
    public static function getStatusBreakdown(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT status, COUNT(*) as count 
            FROM `tickets` 
            GROUP BY status 
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Analytics: Mean Time to Resolution (MTTR) in hours.
     */
    public static function getMeanTimeToResolution(): float {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_minutes 
            FROM `tickets` 
            WHERE resolved_at IS NOT NULL
        ");
        $res = $stmt->fetch();
        $avgMinutes = (float)($res['avg_minutes'] ?? 0);
        return round($avgMinutes / 60, 1);
    }

    /**
     * Analytics: Common Recurring Failure Points (Objective #3 / Problem 3).
     */
    public static function getRecurringFailures(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT 
                category,
                terminal,
                COUNT(*) as incident_count,
                SUM(CASE WHEN urgency IN ('Critical', 'High') THEN 1 ELSE 0 END) as high_impact_count
            FROM `tickets`
            GROUP BY category, terminal
            ORDER BY incident_count DESC
            LIMIT 5
        ");
        return $stmt->fetchAll();
    }
}
