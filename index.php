<?php
/**
 * NimbusDesk Main Entrypoint & Router
 */

require_once __DIR__ . '/config/config.php';

// If user is logged in, redirect them to their specific role dashboard
if (isAuthenticated()) {
    $role = currentUser()['role'];
    header('Location: ' . url($role . '/index.php'));
    exit;
}

// Otherwise, present login page
header('Location: ' . url('auth/login.php'));
exit;
