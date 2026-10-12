<?php
require_once __DIR__ . '/src/Core/Auth.php';

use NimbusDesk\Core\Auth;

Auth::init();
Auth::logout();
Auth::setFlash('success', 'You have been logged out successfully.');

header('Location: /NimbusDesk/index.php');
exit;
