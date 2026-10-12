<?php
/**
 * NimbusDesk Aviation IT Incident Management System
 * Global Application Configuration
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base application path for XAMPP
define('BASE_URL', '/NimbusDesk');
define('APP_NAME', 'NimbusDesk IT');
define('APP_TAGLINE', 'Aviation Technical Support & IT Incident Management');

/**
 * Helper to generate absolute URLs relative to BASE_URL
 */
function url($path = '') {
    $trimmed = ltrim($path, '/');
    return BASE_URL . ($trimmed ? '/' . $trimmed : '');
}

/**
 * Return current logged in user or null
 */
function currentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if current session has user
 */
function isAuthenticated() {
    return !empty($_SESSION['user']);
}

/**
 * Require a specific role or redirect to login / unauthorized
 */
function requireRole($allowedRoles = []) {
    if (!isAuthenticated()) {
        header('Location: ' . url('auth/login.php'));
        exit;
    }
    
    $user = currentUser();
    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    
    if (!in_array($user['role'], $roles)) {
        // Redirect to user's assigned role home
        header('Location: ' . url($user['role'] . '/index.php'));
        exit;
    }
}
