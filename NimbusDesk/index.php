<?php
require_once __DIR__ . '/src/Core/View.php';
use NimbusDesk\Core\View;

// Simulate a basic router for demo purposes.
// If the user submits the login form (GET request with role):
if (isset($_GET['role'])) {
    $role = $_GET['role'];
    if ($role === 'employee') {
        header('Location: /NimbusDesk/employee/index.php');
        exit;
    } elseif ($role === 'admin') {
        header('Location: /NimbusDesk/admin/index.php');
        exit;
    }
}

// Render the login view inside the auth layout
View::render('auth/login', 'auth', [
    'title' => 'Login - NimbusDesk'
]);
