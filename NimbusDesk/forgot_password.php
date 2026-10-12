<?php
require_once __DIR__ . '/src/Core/Database.php';
require_once __DIR__ . '/src/Core/Auth.php';
require_once __DIR__ . '/src/Core/View.php';
require_once __DIR__ . '/src/Models/User.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\User;

Auth::init();

$requested = false;
$email = '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security validation failed. Please refresh the page and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid airport email address.';
        } else {
            // Log reset request in database so IT Admin receives an alert in user management
            User::requestPasswordReset($email);
            $requested = true;
        }
    }
}

View::render('auth/forgot_password', 'auth', [
    'title'     => 'Forgot Password - NimbusDesk',
    'requested' => $requested,
    'email'     => $email,
    'error'     => $error
]);
