<?php
require_once __DIR__ . '/src/Core/Database.php';
require_once __DIR__ . '/src/Core/Auth.php';
require_once __DIR__ . '/src/Core/View.php';
require_once __DIR__ . '/src/Models/User.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\User;

Auth::init();

// If user is already logged in, redirect them to their respective dashboard
if (Auth::check()) {
    $role = Auth::role();
    if ($role === 'employee') {
        header('Location: /NimbusDesk/employee/index.php');
        exit;
    } elseif ($role === 'technician') {
        header('Location: /NimbusDesk/technician/index.php');
        exit;
    } elseif ($role === 'admin') {
        header('Location: /NimbusDesk/admin/index.php');
        exit;
    }
}

$error = null;

// Handle login POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh and try logging in again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            $user = User::authenticate($email, $password);
            if ($user) {
                Auth::login($user);
                Auth::setFlash('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');

                if ($user['role'] === 'employee') {
                    header('Location: /NimbusDesk/employee/index.php');
                } elseif ($user['role'] === 'technician') {
                    header('Location: /NimbusDesk/technician/index.php');
                } else {
                    header('Location: /NimbusDesk/admin/index.php');
                }
                exit;
            } else {
                $error = 'Invalid email, password, or your account may be deactivated by IT Operations.';
            }
        }
    }
}

// Retrieve any flash messages (e.g. from logout or route guard)
$flashSuccess = Auth::getFlash('success');
$flashDanger = $error ?: Auth::getFlash('danger');

// Render the login view
View::render('auth/login', 'auth', [
    'title'        => 'Sign In - NimbusDesk',
    'flashSuccess' => $flashSuccess,
    'flashDanger'  => $flashDanger,
    'oldEmail'     => $_POST['email'] ?? ''
]);
