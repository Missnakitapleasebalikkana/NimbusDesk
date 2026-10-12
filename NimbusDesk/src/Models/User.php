<?php

namespace NimbusDesk\Models;

use NimbusDesk\Core\Database;
use PDO;

/**
 * Class User
 * Handles User database operations, authentication, password resets, and role queries.
 */
class User {
    /**
     * Find a user by their email address.
     */
    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = ? LIMIT 1");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by their primary ID.
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT `id`, `name`, `email`, `role`, `department`, `phone`, `status`, `reset_requested_at`, `created_at` FROM `users` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Authenticate credentials against hashed passwords in MySQL.
     * Returns the user array on success or false on failure.
     */
    public static function authenticate(string $email, string $password): array|false {
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }

        // Check if account is active
        if (isset($user['status']) && $user['status'] === 'inactive') {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Get all technicians and admins available for ticket assignments.
     */
    public static function getTechnicians(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT `id`, `name`, `email`, `role`, `department` 
            FROM `users` 
            WHERE `role` IN ('technician', 'admin') AND `status` = 'active'
            ORDER BY `name` ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Create a new user record.
     */
    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO `users` (`name`, `email`, `password`, `role`, `department`, `phone`, `status`, `created_at`)
            VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())
        ");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            $hashedPassword,
            $data['role'] ?? 'employee',
            $data['department'] ?? 'Aviation Staff',
            $data['phone'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Get all users in the system.
     */
    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT `id`, `name`, `email`, `role`, `department`, `phone`, `status`, `reset_requested_at`, `created_at`
            FROM `users`
            ORDER BY 
                CASE role 
                    WHEN 'admin' THEN 1 
                    WHEN 'technician' THEN 2 
                    ELSE 3 
                END ASC, 
                `name` ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Update user details (Name, Department, Phone, Role, Status).
     */
    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE `users`
            SET `name` = ?, `email` = ?, `department` = ?, `phone` = ?, `role` = ?, `status` = ?
            WHERE `id` = ?
        ");
        return $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            trim($data['department']),
            trim($data['phone'] ?? ''),
            $data['role'],
            $data['status'] ?? 'active',
            $id
        ]);
    }

    /**
     * Toggle active/inactive status of an account.
     */
    public static function setStatus(int $id, string $status): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `status` = ? WHERE `id` = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Update a user's password and clear any pending reset requests.
     */
    public static function updatePassword(int $id, string $newPassword): bool {
        $db = Database::getConnection();
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE `users` SET `password` = ?, `reset_requested_at` = NULL WHERE `id` = ?");
        return $stmt->execute([$hashed, $id]);
    }

    /**
     * Record a password reset request from the forgot password screen.
     */
    public static function requestPasswordReset(string $email): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `reset_requested_at` = NOW() WHERE `email` = ? AND `status` = 'active'");
        return $stmt->execute([trim($email)]);
    }

    /**
     * Get all users who currently have a pending password reset request.
     */
    public static function getPendingPasswordResets(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT `id`, `name`, `email`, `role`, `department`, `reset_requested_at`
            FROM `users`
            WHERE `reset_requested_at` IS NOT NULL
            ORDER BY `reset_requested_at` DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Clear a pending password reset request without changing the password.
     */
    public static function clearPasswordReset(int $userId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `reset_requested_at` = NULL WHERE `id` = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Soft-delete or safely remove a user without destroying historical tickets.
     */
    public static function delete(int $id): bool {
        $db = Database::getConnection();

        // Check if user has associated tickets
        $checkStmt = $db->prepare("SELECT COUNT(*) as ticket_count FROM `tickets` WHERE `user_id` = ? OR `assigned_to` = ?");
        $checkStmt->execute([$id, $id]);
        $count = (int)$checkStmt->fetch()['ticket_count'];

        if ($count > 0) {
            // Soft-deactivate to maintain audit trail and prevent foreign key failure
            $stmt = $db->prepare("UPDATE `users` SET `status` = 'inactive' WHERE `id` = ?");
            return $stmt->execute([$id]);
        } else {
            // Safe to hard delete if no tickets exist
            $stmt = $db->prepare("DELETE FROM `users` WHERE `id` = ?");
            return $stmt->execute([$id]);
        }
    }
}
