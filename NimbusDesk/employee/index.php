<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

Auth::requireRole(['employee', 'admin']);
$currentUser = Auth::user();

// Fetch live statistics for the current employee
$stats = Ticket::getStats($currentUser['id']);

// Fetch the active tickets (Open & In Progress)
$activeTickets = Ticket::getForEmployee($currentUser['id'], 'active');

View::render('employee/dashboard', 'employee', [
    'title'         => 'Dashboard - NimbusDesk',
    'pageHeader'    => 'Employee Incident Dashboard',
    'activeMenu'    => 'dashboard',
    'stats'         => $stats,
    'activeTickets' => $activeTickets
]);
