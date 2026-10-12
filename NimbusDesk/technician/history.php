<?php
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/View.php';
require_once __DIR__ . '/../src/Models/Ticket.php';

use NimbusDesk\Core\Auth;
use NimbusDesk\Core\View;
use NimbusDesk\Models\Ticket;

Auth::requireRole(['technician', 'admin']);
$currentUser = Auth::user();

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$terminal = trim($_GET['terminal'] ?? '');

$historyTickets = Ticket::getAllWithFilters([
    'assigned_to' => $currentUser['id'],
    'status'      => 'Resolved',
    'search'      => $search,
    'category'    => $category,
    'terminal'    => $terminal
]);

View::render('technician/history', 'technician', [
    'title'          => 'My Resolved Incidents - NimbusDesk Tech',
    'pageHeader'     => 'My Completed Incidents & Resolutions',
    'activeMenu'     => 'history',
    'historyTickets' => $historyTickets,
    'search'         => $search,
    'category'       => $category,
    'terminal'       => $terminal
]);
