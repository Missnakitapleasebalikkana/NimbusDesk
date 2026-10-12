<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/User.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\User;

// Only System Administrators can access this page
Auth::requireRole('admin');
$currentUser = Auth::user();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Auth::setFlash('danger', 'Security validation failed. Please refresh and try again.');
        header('Location: /NimbusDesk/admin/users.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    // Create a new user account
    if ($action === 'create_user') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'employee');
        $department = trim($_POST['department'] ?? 'Aviation Staff');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || mb_strlen($name) < 2) {
            $errors[] = 'Full name must be at least 2 characters.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid work email address.';
        } elseif (User::findByEmail($email)) {
            $errors[] = 'An account with that email already exists in the system.';
        }

        if (empty($password) || mb_strlen($password) < 6) {
            $errors[] = 'Initial password must be at least 6 characters.';
        }

        if (empty($errors)) {
            User::create([
                'name'       => $name,
                'email'      => $email,
                'password'   => $password,
                'role'       => $role,
                'department' => $department,
                'phone'      => $phone
            ]);

            Auth::setFlash('success', "Account for {$name} ({$role}) created successfully.");
            header('Location: /NimbusDesk/admin/users.php');
            exit;
        }
    }

    // Reset password for an account
    if ($action === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';

        if ($userId > 0 && !empty($newPassword) && mb_strlen($newPassword) >= 6) {
            User::updatePassword($userId, $newPassword);
            Auth::setFlash('success', 'User password has been reset successfully.');
            header('Location: /NimbusDesk/admin/users.php');
            exit;
        } else {
            $errors[] = 'Password must be at least 6 characters.';
        }
    }

    // Clear password reset request without changing password
    if ($action === 'clear_reset') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0) {
            User::clearPasswordReset($userId);
            Auth::setFlash('success', 'Password reset request cleared.');
            header('Location: /NimbusDesk/admin/users.php');
            exit;
        }
    }

    // Toggle active / inactive status
    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? 'active');

        if ($userId === (int)$currentUser['id']) {
            $errors[] = 'You cannot deactivate your own active administrator account.';
        } elseif ($userId > 0) {
            User::setStatus($userId, $newStatus);
            Auth::setFlash('success', "Account status updated to {$newStatus}.");
            header('Location: /NimbusDesk/admin/users.php');
            exit;
        }
    }

    // Delete a user (safely soft-deactivates if tickets exist, preserving historical audits)
    if ($action === 'delete_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId === (int)$currentUser['id']) {
            $errors[] = 'You cannot delete your own active administrator account.';
        } elseif ($userId > 0) {
            User::delete($userId);
            Auth::setFlash('success', 'User account was deactivated / removed from the system while preserving audit logs.');
            header('Location: /NimbusDesk/admin/users.php');
            exit;
        }
    }
}

$pendingResets = User::getPendingPasswordResets();
$users = User::getAll();

View::render('admin/users/index', 'admin', [
    'title'          => 'Manage User Accounts - NimbusDesk Admin',
    'pageHeader'     => 'User Account & RBAC Management',
    'activeMenu'     => 'users',
    'currentUser'    => $currentUser,
    'users'          => $users,
    'pendingResets'  => $pendingResets,
    'errors'         => $errors
]);
