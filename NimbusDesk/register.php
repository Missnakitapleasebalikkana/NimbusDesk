<?php
require_once __DIR__ . '/src/Core/Auth.php';

use NimbusDesk\Core\Auth;

Auth::init();

// Airport exclusive security policy: Self-registration is restricted.
// Accounts must be created by IT Systems Administrators.
Auth::setFlash('danger', 'Airport Security Policy: Self-registration is disabled. Please contact your IT Systems Administrator to request user account credentials.');

header('Location: /NimbusDesk/index.php');
exit;
