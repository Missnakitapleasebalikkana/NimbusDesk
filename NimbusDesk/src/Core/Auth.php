<?php

namespace NimbusDesk\Core;

/**
 * Class Auth
 * Manages user sessions, authentication state, and Role-Based Access Control (RBAC).
 */
class Auth {
    /**
     * Ensure session is started safely.
     */
    public static function init(): void {
        if (!date_default_timezone_get() || date_default_timezone_get() === 'UTC' || date_default_timezone_get() === 'Europe/Berlin') {
            date_default_timezone_set('Asia/Manila');
        }
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
            } else {
                @session_start();
            }
        }
    }

    /**
     * Log in a user by saving their sanitized data into the session.
     */
    public static function login(array $user): void {
        self::init();
        // Remove password before saving into session
        unset($user['password']);
        $_SESSION['user'] = $user;
    }

    /**
     * Log out current user and clear authenticated session.
     */
    public static function logout(): void {
        self::init();
        unset($_SESSION['user']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Check if a user is currently logged in.
     */
    public static function check(): bool {
        self::init();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Get the currently logged-in user array or null.
     */
    public static function user(): ?array {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get current user's role.
     */
    public static function role(): ?string {
        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * Guard: Ensure user is logged in. Otherwise redirect to login.
     */
    public static function requireLogin(string $redirect = '/NimbusDesk/index.php'): void {
        if (!self::check()) {
            self::setFlash('danger', 'Please sign in to access this page.');
            header("Location: {$redirect}");
            exit;
        }
    }

    /**
     * Guard: Ensure user possesses one of the allowed roles.
     *
     * @param array|string $allowedRoles e.g. ['technician', 'admin'] or 'employee'
     */
    public static function requireRole($allowedRoles, string $redirect = '/NimbusDesk/index.php'): void {
        self::requireLogin($redirect);

        $allowed = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        $currentRole = self::role();

        if (!in_array($currentRole, $allowed, true)) {
            self::setFlash('danger', 'Access denied: You do not have permission to view that section.');
            
            // Redirect to their respective dashboard if logged in
            if ($currentRole === 'employee') {
                header('Location: /NimbusDesk/employee/index.php');
            } elseif ($currentRole === 'technician') {
                header('Location: /NimbusDesk/technician/index.php');
            } elseif ($currentRole === 'admin') {
                header('Location: /NimbusDesk/admin/index.php');
            } else {
                header("Location: {$redirect}");
            }
            exit;
        }
    }

    /**
     * Set a flash message for the next request.
     */
    public static function setFlash(string $key, string $message): void {
        self::init();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Retrieve and clear a flash message.
     */
    public static function getFlash(string $key): ?string {
        self::init();
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }

    /**
     * Generate or return existing CSRF token for the session.
     */
    public static function csrfToken(): string {
        self::init();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify submitted CSRF token.
     */
    public static function verifyCsrf(?string $token): bool {
        self::init();
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

