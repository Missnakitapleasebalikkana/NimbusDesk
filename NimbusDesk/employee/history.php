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

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$resolvedTickets = Ticket::getForEmployee(
    $currentUser['id'],
    'resolved',
    $search ?: null,
    $category ?: null
);

View::render('employee/history', 'employee', [
    'title'           => 'Incident History - NimbusDesk',
    'pageHeader'      => 'My Resolved Incidents',
    'activeMenu'      => 'history',
    'resolvedTickets' => $resolvedTickets,
    'search'          => $search,
    'selectedCategory'=> $category
]);
