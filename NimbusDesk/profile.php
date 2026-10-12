<?php
require_once __DIR__ . '/src/Core/Database.php';
require_once __DIR__ . '/src/Core/Auth.php';
require_once __DIR__ . '/src/Core/View.php';
require_once __DIR__ . '/src/Models/User.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\User;

Auth::requireLogin();
$currentUser = Auth::user();

// Always re-fetch fresh user details from database
$freshUser = User::findById($currentUser['id']);
if ($freshUser) {
    Auth::login($freshUser);
    $currentUser = $freshUser;
}

$errors = [];
$successMessage = null;

// Determine layout based on user role
$roleLayout = 'employee';
if ($currentUser['role'] === 'admin') {
    $roleLayout = 'admin';
} elseif ($currentUser['role'] === 'technician') {
    $roleLayout = 'technician';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Auth::setFlash('danger', 'Security token expired. Please refresh the page and try again.');
        header('Location: /NimbusDesk/profile.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $department = trim($_POST['department'] ?? $currentUser['department']);

        if (empty($name) || mb_strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }

        if (empty($errors)) {
            User::update($currentUser['id'], [
                'name'       => $name,
                'email'      => $currentUser['email'],
                'department' => $department,
                'phone'      => $phone,
                'role'       => $currentUser['role'],
                'status'     => $currentUser['status'] ?? 'active'
            ]);

            $updatedUser = User::findById($currentUser['id']);
            Auth::login($updatedUser);
            $currentUser = $updatedUser;
            Auth::setFlash('success', 'Profile information updated successfully.');
            header('Location: /NimbusDesk/profile.php');
            exit;
        }
    }

    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Verify current password
        $userCheck = User::authenticate($currentUser['email'], $currentPassword);
        if (!$userCheck) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        if (empty($newPassword) || mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'New passwords do not match.';
        }

        if (empty($errors)) {
            User::updatePassword($currentUser['id'], $newPassword);
            Auth::setFlash('success', 'Your password has been changed successfully.');
            header('Location: /NimbusDesk/profile.php');
            exit;
        }
    }
}

View::render('profile/index', $roleLayout, [
    'title'       => 'My Profile - NimbusDesk',
    'pageHeader'  => 'User Account & Profile',
    'activeMenu'  => '',
    'currentUser' => $currentUser,
    'errors'      => $errors
]);
